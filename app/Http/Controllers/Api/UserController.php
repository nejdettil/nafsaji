<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // تصفية حسب الدور
        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // تصفية حسب الحالة
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // البحث بالاسم أو البريد الإلكتروني
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $users = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    /**
     * إنشاء مستخدم جديد
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'profile_image' => 'nullable|string',
            'is_active' => 'boolean',
            'role' => 'required|string|in:admin,user,specialist',
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
            'profile_image' => $request->profile_image,
            'is_active' => $request->is_active ?? true,
        ]);

        // إضافة الدور المطلوب
        $role = Role::where('name', $request->role)->first();
        if (!$role) {
            $role = Role::create(['name' => $request->role]);
        }
        $user->assignRole($role);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء المستخدم بنجاح',
            'data' => $user->load('roles')
        ], 201);
    }

    /**
     * عرض مستخدم محدد
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // إذا كان المستخدم مختص، قم بتحميل بيانات المختص
        if ($user->hasRole('specialist')) {
            $user->load('specialist');
        }

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    /**
     * تحديث مستخدم محدد
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'string|max:20',
            'profile_image' => 'nullable|string',
            'is_active' => 'boolean',
            'role' => 'string|in:admin,user,specialist',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث بيانات المستخدم
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('profile_image')) {
            $user->profile_image = $request->profile_image;
        }
        if ($request->has('is_active')) {
            $user->is_active = $request->is_active;
        }

        $user->save();

        // تحديث الدور إذا تم تغييره
        if ($request->has('role')) {
            // إزالة جميع الأدوار الحالية
            $user->syncRoles([]);
            
            // إضافة الدور الجديد
            $role = Role::where('name', $request->role)->first();
            if (!$role) {
                $role = Role::create(['name' => $request->role]);
            }
            $user->assignRole($role);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث المستخدم بنجاح',
            'data' => $user->load('roles')
        ]);
    }

    /**
     * تغيير حالة النشاط للمستخدم
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        // إذا كان المستخدم مختص، قم بتحديث حالة المختص أيضاً
        if ($user->hasRole('specialist') && $user->specialist) {
            $user->specialist->is_active = $user->is_active;
            $user->specialist->save();
        }

        return response()->json([
            'status' => true,
            'message' => $user->is_active ? 'تم تفعيل المستخدم بنجاح' : 'تم تعطيل المستخدم بنجاح',
            'data' => $user
        ]);
    }

    /**
     * حذف مستخدم محدد
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // حذف المستخدم سيؤدي تلقائياً لحذف جميع البيانات المرتبطة به بسبب العلاقات المعرفة
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المستخدم بنجاح'
        ]);
    }

    /**
     * تحديث الملف الشخصي للمستخدم الحالي
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'string|max:20',
            'profile_image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث بيانات المستخدم
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('profile_image')) {
            $user->profile_image = $request->profile_image;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'data' => $user
        ]);
    }
}
