<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * عرض قائمة الأدوار
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        // تجميع الصلاحيات حسب المجموعة الأولى من الاسم (مثل users.create → users)
        $permissions_by_group = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'عام';
        });

        return view('admin.roles.index', compact('roles', 'permissions', 'permissions_by_group'));
    }

    /**
     * عرض نموذج إنشاء دور جديد
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * حفظ دور جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ], [
            'name.required' => 'يرجى إدخال اسم الدور',
            'name.unique' => 'اسم الدور موجود بالفعل',
            'permissions.required' => 'يرجى اختيار صلاحية واحدة على الأقل',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
            $role->syncPermissions($request->permissions);

            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with('success', 'تم إنشاء الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الدور: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل دور محدد
     */
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $users = User::role($role->name)->paginate(10);

        return view('admin.roles.show', compact('role', 'users'));
    }

    /**
     * عرض نموذج تعديل دور
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * تحديث دور محدد
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ], [
            'name.required' => 'يرجى إدخال اسم الدور',
            'name.unique' => 'اسم الدور موجود بالفعل',
            'permissions.required' => 'يرجى اختيار صلاحية واحدة على الأقل',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions);

            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with('success', 'تم تحديث الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الدور: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف دور محدد
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            // التحقق من عدم حذف الأدوار الأساسية
            if (in_array($role->name, ['admin', 'super-admin'])) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الأدوار الأساسية في النظام');
            }

            // التحقق من عدم وجود مستخدمين مرتبطين بهذا الدور
            $usersCount = User::role($role->name)->count();
            if ($usersCount > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الدور لأنه مرتبط بـ ' . $usersCount . ' مستخدم');
            }

            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', 'تم حذف الدور بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الدور: ' . $e->getMessage());
        }
    }
}
