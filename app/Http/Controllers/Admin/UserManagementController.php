<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Response;

class UserManagementController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     */
    public function index(Request $request)
    {
        $query = User::query();

        // البحث حسب الاسم أو البريد الإلكتروني
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // التصفية حسب الدور
        if ($request->has('role')) {
            $role = $request->role;
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // التصفية حسب الحالة
        if ($request->has('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        $users = $query->with('roles')->paginate(10);
        $roles = \Spatie\Permission\Models\Role::all();

        // بيانات رسوم بيانية مؤقتة (تقدر تغيرها لاحقًا)
        $usersChartData = [
            'labels' => ['يناير', 'فبراير', 'مارس'],
            'data' => [10, 15, 8],
        ];


        $userDistributionData = [
            'labels' => ['مدراء', 'مختصين', 'مستخدمين'],
            'data' => [3, 5, 7],
        ];

        return view('admin.users.index', compact('users', 'roles', 'usersChartData', 'userDistributionData'));
    }


    /**
     * إنشاء مستخدم جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'role' => 'required|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء المستخدم بنجاح',
            'user' => $user
        ], 201);
    }

    /**
     * عرض بيانات مستخدم محدد
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // إذا كان المستخدم مختص، نقوم بإرفاق بيانات المختص
        if ($user->hasRole('specialist')) {
            $user->load('specialist');
        }

        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }

    /**
     * تحديث بيانات مستخدم
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'sometimes|string|max:20',
            'is_active' => 'sometimes|boolean',
            'role' => 'sometimes|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث البيانات
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('is_active')) {
            $user->is_active = $request->is_active;
        }

        $user->save();

        // تحديث الدور إذا تم تقديمه
        if ($request->has('role')) {
            // إزالة جميع الأدوار الحالية
            $user->syncRoles([]);
            // إضافة الدور الجديد
            $user->assignRole($request->role);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث بيانات المستخدم بنجاح',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * حذف مستخدم
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // التحقق من عدم حذف المدير الرئيسي
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن حذف المدير الرئيسي الوحيد في النظام'
            ], 403);
        }

        // حذف بيانات المختص إذا كان المستخدم مختص
        if ($user->hasRole('specialist')) {
            $specialist = Specialist::where('user_id', $id)->first();
            if ($specialist) {
                $specialist->delete();
            }
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المستخدم بنجاح'
        ]);
    }

    /**
     * تغيير كلمة مرور مستخدم
     */
    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }

    /**
     * عرض قائمة المختصين
     */
    public function specialists(Request $request)
    {
        $query = Specialist::query();

        // البحث حسب الاسم أو التخصص
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('specialization', 'like', "%{$search}%");
        }

        // التصفية حسب حالة التحقق
        if ($request->has('verified')) {
            $verified = $request->verified === 'true';
            $query->where('is_verified', $verified);
        }

        // التصفية حسب الإتاحة
        if ($request->has('available')) {
            $available = $request->available === 'true';
            $query->where('is_available', $available);
        }

        $specialists = $query->with('user')->paginate(10);

        return response()->json([
            'status' => true,
            'specialists' => $specialists
        ]);
    }

    /**
     * تحديث حالة التحقق للمختص
     */
    public function verifySpecialist(Request $request, $id)
    {
        $specialist = Specialist::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_verified' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $specialist->is_verified = $request->is_verified;
        $specialist->save();

        return response()->json([
            'status' => true,
            'message' => $request->is_verified ? 'تم قبول المختص بنجاح' : 'تم رفض المختص',
            'specialist' => $specialist
        ]);
    }

    /**
     * عرض قائمة الأدوار
     */
    public function roles()
    {
        $roles = Role::with('permissions')->get();

        return response()->json([
            'status' => true,
            'roles' => $roles
        ]);
    }

    /**
     * عرض قائمة الصلاحيات
     */
    public function permissions()
    {
        $permissions = Permission::all();

        return response()->json([
            'status' => true,
            'permissions' => $permissions
        ]);
    }

    /**
     * إنشاء دور جديد
     */
    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الدور بنجاح',
            'role' => $role->load('permissions')
        ], 201);
    }

    /**
     * تحديث دور
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) {
            $role->name = $request->name;
            $role->save();
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الدور بنجاح',
            'role' => $role->load('permissions')
        ]);
    }

    /**
     * حذف دور
     */
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);

        // التحقق من عدم حذف الأدوار الأساسية
        if (in_array($role->name, ['admin', 'specialist', 'user'])) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن حذف الأدوار الأساسية في النظام'
            ], 403);
        }

        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الدور بنجاح'
        ]);
    }
    public function downloadTemplate()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=users_template.csv',
        ];

        // محتوى الملف (مثال على رؤوس الأعمدة فقط)
        $content = "name,email,phone,password,role\n";

        return Response::make($content, 200, $headers);
    }
    /**
     * تحديث حالة المستخدم (تفعيل/تعطيل)
     */
    public function updateStatus(Request $request)
    {
        $userId = $request->input('id');
        $isActive = $request->input('is_active');

        $user = User::findOrFail($userId);
        $user->is_active = $isActive;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المستخدم بنجاح'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = explode(',', $request->input('ids'));

        $users = User::whereIn('id', $ids)->get();

        switch ($action) {
            case 'activate':
                foreach ($users as $user) {
                    $user->is_active = true;
                    $user->save();
                }
                $message = 'تم تفعيل المستخدمين المحددين بنجاح';
                break;

            case 'deactivate':
                foreach ($users as $user) {
                    $user->is_active = false;
                    $user->save();
                }
                $message = 'تم تعطيل المستخدمين المحددين بنجاح';
                break;

            case 'delete':
                foreach ($users as $user) {
                    $user->delete();
                }
                $message = 'تم حذف المستخدمين المحددين بنجاح';
                break;

            default:
                return redirect()->back()->with('error', 'إجراء غير معروف');
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }
    public function chartData(Request $request)
    {
        $period = $request->get('period', 'monthly');

        // بيانات افتراضية للتجريب
        $labels = ['يناير', 'فبراير', 'مارس'];
        $data = [12, 18, 9];

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'عدد المستخدمين',
                    'backgroundColor' => '#4e73df',
                    'data' => $data,
                ],
            ],
        ]);
    }
    /**
     * جلب بيانات المستخدم
     */
    public function getUser(Request $request)
    {
        $userId = $request->input('id');
        $user = User::with('roles')->findOrFail($userId);

        return response()->json([
            'user' => $user
        ]);
    }


}
