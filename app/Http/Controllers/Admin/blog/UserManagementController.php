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
        
        return response()->json([
            'status' => true,
            'users' => $users
        ]);
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

    /**
     * تصدير بيانات المستخدمين
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // بناء الاستعلام مع تطبيق الفلاتر
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
        
        // تحميل العلاقات
        $users = $query->with('roles')->get();
        
        // تحديد نوع التصدير
        $exportType = $request->input('export_type', 'csv');
        
        // تحضير البيانات للتصدير
        $data = [];
        $headers = ['الرقم', 'الاسم', 'البريد الإلكتروني', 'رقم الهاتف', 'الدور', 'الحالة', 'تاريخ الإنشاء'];
        
        foreach ($users as $index => $user) {
            $roles = $user->roles->pluck('name')->implode(', ');
            $status = $user->is_active ? 'نشط' : 'غير نشط';
            
            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $roles,
                'status' => $status,
                'created_at' => $user->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        // تصدير البيانات حسب النوع المطلوب
        switch ($exportType) {
            case 'excel':
                return $this->exportExcel($data, $headers);
            case 'pdf':
                return $this->exportPdf($data, $headers);
            case 'csv':
            default:
                return $this->exportCsv($data, $headers);
        }
    }
    
    /**
     * تصدير البيانات بتنسيق CSV
     * 
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    private function exportCsv($data, $headers)
    {
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');
        
        // إضافة BOM للدعم الصحيح للغة العربية
        fputs($handle, "\xEF\xBB\xBF");
        
        // إضافة الترويسة
        fputcsv($handle, $headers);
        
        // إضافة البيانات
        foreach ($data as $row) {
            fputcsv($handle, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['roles'],
                $row['status'],
                $row['created_at']
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * تصدير البيانات بتنسيق Excel
     * 
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    private function exportExcel($data, $headers)
    {
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // إنشاء مصفوفة تحتوي على البيانات مع الترويسة
        $exportData = [];
        $exportData[] = $headers;
        
        foreach ($data as $row) {
            $exportData[] = [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['roles'],
                $row['status'],
                $row['created_at']
            ];
        }
        
        // استخدام مكتبة Laravel Excel للتصدير
        return (new \Maatwebsite\Excel\Excel)->download(
            new \App\Exports\UsersExport($exportData),
            $filename
        );
    }
    
    /**
     * تصدير البيانات بتنسيق PDF
     * 
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    private function exportPdf($data, $headers)
    {
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // تحضير البيانات للعرض في قالب PDF
        $viewData = [
            'headers' => $headers,
            'data' => $data,
            'title' => 'قائمة المستخدمين',
            'date' => date('Y-m-d H:i:s')
        ];
        
        // إنشاء PDF باستخدام مكتبة DOMPDF
        $pdf = \PDF::loadView('admin.users.export_pdf', $viewData);
        
        return $pdf->download($filename);
    }
}
