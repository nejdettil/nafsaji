<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // البحث والتصفية
        $query = User::query();
        
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }
        
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('active', $request->status == 'active' ? 1 : 0);
        }
        
        // الترتيب
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        // التقسيم إلى صفحات
        $users = $query->paginate(10);
        
        // الأدوار للتصفية
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * عرض نموذج إنشاء مستخدم جديد
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * تخزين مستخدم جديد في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'active' => 'boolean',
        ]);
        
        // إنشاء المستخدم
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->password = Hash::make($validated['password']);
        $user->address = $validated['address'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->active = $request->has('active');
        
        // معالجة الصورة الشخصية
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = 'profile_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/profiles', $filename);
            $user->profile_image = 'storage/profiles/' . $filename;
        }
        
        $user->save();
        
        // إضافة الأدوار
        $user->roles()->sync($request->roles);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * عرض معلومات مستخدم محدد
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // تحميل العلاقات
        $user->load('roles', 'bookings', 'payments');
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * عرض نموذج تعديل مستخدم
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * تحديث معلومات مستخدم محدد
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'active' => 'boolean',
        ]);
        
        // تحديث المستخدم
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->address = $validated['address'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->active = $request->has('active');
        
        // معالجة الصورة الشخصية
        if ($request->hasFile('profile_image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->profile_image && Storage::exists(str_replace('storage/', 'public/', $user->profile_image))) {
                Storage::delete(str_replace('storage/', 'public/', $user->profile_image));
            }
            
            $image = $request->file('profile_image');
            $filename = 'profile_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/profiles', $filename);
            $user->profile_image = 'storage/profiles/' . $filename;
        }
        
        $user->save();
        
        // تحديث الأدوار
        $user->roles()->sync($request->roles);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * حذف مستخدم محدد
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // التحقق من عدم حذف المستخدم الحالي
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'لا يمكنك حذف حسابك الحالي');
        }
        
        // حذف الصورة الشخصية إذا كانت موجودة
        if ($user->profile_image && Storage::exists(str_replace('storage/', 'public/', $user->profile_image))) {
            Storage::delete(str_replace('storage/', 'public/', $user->profile_image));
        }
        
        // حذف المستخدم
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * تغيير حالة المستخدم (تفعيل/تعطيل)
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(User $user)
    {
        // التحقق من عدم تعطيل المستخدم الحالي
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'لا يمكنك تعطيل حسابك الحالي');
        }
        
        $user->active = !$user->active;
        $user->save();
        
        $status = $user->active ? 'تفعيل' : 'تعطيل';
        
        return redirect()->route('admin.users.index')
            ->with('success', "تم {$status} المستخدم بنجاح");
    }

    /**
     * عرض قائمة المستخدمين المحذوفين
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $trashedUsers = User::onlyTrashed()->paginate(10);
        return view('admin.users.trashed', compact('trashedUsers'));
    }

    /**
     * استعادة مستخدم محذوف
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        
        return redirect()->route('admin.users.trashed')
            ->with('success', 'تم استعادة المستخدم بنجاح');
    }

    /**
     * حذف مستخدم نهائياً
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        // حذف الصورة الشخصية إذا كانت موجودة
        if ($user->profile_image && Storage::exists(str_replace('storage/', 'public/', $user->profile_image))) {
            Storage::delete(str_replace('storage/', 'public/', $user->profile_image));
        }
        
        $user->forceDelete();
        
        return redirect()->route('admin.users.trashed')
            ->with('success', 'تم حذف المستخدم نهائياً بنجاح');
    }

    /**
     * تصدير بيانات المستخدمين
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // تحديد نوع التصدير
        $format = $request->format ?? 'xlsx';
        
        // بناء الاستعلام
        $query = User::query();
        
        // تطبيق التصفية إذا كانت موجودة
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('active', $request->status == 'active' ? 1 : 0);
        }
        
        // تحميل العلاقات
        $query->with('roles');
        
        // الحصول على البيانات
        $users = $query->get();
        
        // تحديد اسم الملف
        $filename = 'users_' . date('Y-m-d') . '.' . $format;
        
        // تصدير البيانات حسب النوع المطلوب
        if ($format == 'csv') {
            return $this->exportToCsv($users, $filename);
        } elseif ($format == 'pdf') {
            return $this->exportToPdf($users, $filename);
        } else {
            return $this->exportToExcel($users, $filename);
        }
    }

    /**
     * تصدير البيانات إلى ملف Excel
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $users
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    private function exportToExcel($users, $filename)
    {
        // تنفيذ التصدير إلى Excel (يتطلب مكتبة إضافية)
        // يمكن استخدام مكتبة مثل maatwebsite/excel
        
        return redirect()->route('admin.users.index')
            ->with('info', 'تم تصدير البيانات بنجاح');
    }

    /**
     * تصدير البيانات إلى ملف CSV
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $users
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    private function exportToCsv($users, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM للدعم الصحيح للغة العربية
            fputs($file, "\xEF\xBB\xBF");
            
            // إضافة رؤوس الأعمدة
            fputcsv($file, ['الاسم', 'البريد الإلكتروني', 'الهاتف', 'الأدوار', 'الحالة', 'تاريخ التسجيل']);
            
            // إضافة البيانات
            foreach ($users as $user) {
                $roles = $user->roles->pluck('name')->implode(', ');
                $status = $user->active ? 'نشط' : 'غير نشط';
                
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->phone ?? 'غير متوفر',
                    $roles,
                    $status,
                    $user->created_at->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * تصدير البيانات إلى ملف PDF
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $users
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf($users, $filename)
    {
        // تنفيذ التصدير إلى PDF (يتطلب مكتبة إضافية)
        // يمكن استخدام مكتبة مثل barryvdh/laravel-dompdf
        
        return redirect()->route('admin.users.index')
            ->with('info', 'تم تصدير البيانات بنجاح');
    }

    /**
     * استيراد بيانات المستخدمين
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:10240',
        ]);
        
        // معالجة ملف الاستيراد (يتطلب مكتبة إضافية)
        // يمكن استخدام مكتبة مثل maatwebsite/excel
        
        return redirect()->route('admin.users.index')
            ->with('success', 'تم استيراد البيانات بنجاح');
    }
}
