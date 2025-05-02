<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Specialist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    /**
     * عرض قائمة فئات الخدمات
     */
    public function index(Request $request)
    {
        $query = ServiceCategory::with(['parent', 'children', 'services']);

        // تصفية حسب الحالة
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active == 1);
        }

        // تصفية حسب الفئة الأب
        if ($request->has('parent_id') && !empty($request->parent_id)) {
            $query->where('parent_id', $request->parent_id);
        } else {
            // إذا لم يتم تحديد فئة أب، اعرض الفئات الرئيسية والفرعية
            $query->orderBy('parent_id', 'asc');
        }

        // البحث بواسطة الاسم
        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'order');
        $orderDirection = $request->input('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);

        $categories = $query->paginate($request->input('per_page', 15));
        
        // الحصول على قوائم للفلاتر
        $parentCategories = ServiceCategory::whereNull('parent_id')->orWhere('parent_id', 0)->get();

        return view('admin.services.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * عرض نموذج إنشاء فئة خدمة جديدة
     */
    public function create()
    {
        $parentCategories = ServiceCategory::whereNull('parent_id')->orWhere('parent_id', 0)->get();
        return view('admin.services.categories.create', compact('parentCategories'));
    }

    /**
     * حفظ فئة خدمة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:service_categories,id',
            'order' => 'nullable|integer',
        ], [
            'name.required' => 'يرجى إدخال اسم الفئة',
            'name.unique' => 'اسم الفئة موجود بالفعل',
            'icon.image' => 'يجب أن يكون الأيقونة صورة',
            'icon.mimes' => 'صيغة الصورة غير مدعومة، الصيغ المدعومة هي: jpeg, png, jpg, gif, svg',
            'icon.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            'parent_id.exists' => 'الفئة الأب غير موجودة',
            'order.integer' => 'الترتيب يجب أن يكون رقم صحيح',
        ]);

        try {
            DB::beginTransaction();

            // إنشاء slug من الاسم
            $slug = Str::slug($request->name, '-');
            
            // التعامل مع الأيقونة
            $iconPath = null;
            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('categories', 'public');
            }

            // إنشاء الفئة
            $category = ServiceCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'icon' => $iconPath,
                'is_active' => $request->has('is_active'),
                'parent_id' => $request->parent_id,
                'order' => $request->order ?? 0,
            ]);

            DB::commit();

            // تسجيل النشاط
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->log('تم إنشاء فئة خدمة جديدة');

            return redirect()->route('admin.services.categories.index')
                ->with('success', 'تم إنشاء فئة الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إنشاء فئة الخدمة: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء فئة الخدمة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل فئة خدمة محددة
     */
    public function show($id)
    {
        $category = ServiceCategory::with(['parent', 'children', 'services', 'specialists.user'])
            ->findOrFail($id);

        // عدد الخدمات في هذه الفئة
        $servicesCount = $category->services->count();
        
        // عدد المتخصصين في هذه الفئة
        $specialistsCount = $category->specialists->count();
        
        // عدد الفئات الفرعية
        $childrenCount = $category->children->count();

        return view('admin.services.categories.show', compact(
            'category',
            'servicesCount',
            'specialistsCount',
            'childrenCount'
        ));
    }

    /**
     * عرض نموذج تعديل فئة خدمة
     */
    public function edit($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $parentCategories = ServiceCategory::where('id', '!=', $id)
            ->whereNull('parent_id')
            ->orWhere('parent_id', 0)
            ->get();

        return view('admin.services.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * تحديث فئة خدمة محددة
     */
    public function update(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:service_categories,id',
            'order' => 'nullable|integer',
        ], [
            'name.required' => 'يرجى إدخال اسم الفئة',
            'name.unique' => 'اسم الفئة موجود بالفعل',
            'icon.image' => 'يجب أن يكون الأيقونة صورة',
            'icon.mimes' => 'صيغة الصورة غير مدعومة، الصيغ المدعومة هي: jpeg, png, jpg, gif, svg',
            'icon.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            'parent_id.exists' => 'الفئة الأب غير موجودة',
            'order.integer' => 'الترتيب يجب أن يكون رقم صحيح',
        ]);

        try {
            DB::beginTransaction();

            // التحقق من أن الفئة الأب ليست هي نفسها أو أحد أبنائها
            if ($request->parent_id) {
                if ($request->parent_id == $id) {
                    return redirect()->back()
                        ->with('error', 'لا يمكن جعل الفئة أب لنفسها')
                        ->withInput();
                }

                // التحقق من أن الفئة الأب ليست أحد الأبناء
                $childIds = $this->getAllChildrenIds($category);
                if (in_array($request->parent_id, $childIds)) {
                    return redirect()->back()
                        ->with('error', 'لا يمكن جعل الفئة الفرعية أب للفئة الرئيسية')
                        ->withInput();
                }
            }

            // إنشاء slug من الاسم إذا تم تغيير الاسم
            if ($category->name != $request->name) {
                $category->slug = Str::slug($request->name, '-');
            }

            // التعامل مع الأيقونة
            if ($request->hasFile('icon')) {
                // حذف الأيقونة القديمة إذا كانت موجودة
                if ($category->icon) {
                    Storage::disk('public')->delete($category->icon);
                }
                $category->icon = $request->file('icon')->store('categories', 'public');
            }

            // تحديث الفئة
            $category->name = $request->name;
            $category->description = $request->description;
            $category->is_active = $request->has('is_active');
            $category->parent_id = $request->parent_id;
            $category->order = $request->order ?? 0;
            $category->save();

            DB::commit();

            // تسجيل النشاط
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->log('تم تحديث فئة خدمة');

            return redirect()->route('admin.services.categories.index')
                ->with('success', 'تم تحديث فئة الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث فئة الخدمة: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث فئة الخدمة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف فئة خدمة محددة
     */
    public function destroy($id)
    {
        try {
            $category = ServiceCategory::findOrFail($id);

            // التحقق من عدم وجود خدمات مرتبطة بالفئة
            if ($category->services->count() > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على خدمات مرتبطة بها');
            }

            // التحقق من عدم وجود فئات فرعية
            if ($category->children->count() > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على فئات فرعية');
            }

            // حذف الأيقونة إذا كانت موجودة
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            // حذف العلاقات مع المتخصصين
            $category->specialists()->detach();

            // حذف الفئة
            $category->delete();

            // تسجيل النشاط
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->log('تم حذف فئة خدمة');

            return redirect()->route('admin.services.categories.index')
                ->with('success', 'تم حذف فئة الخدمة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حذف فئة الخدمة: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف فئة الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * تغيير حالة فئة الخدمة (نشط/غير نشط)
     */
    public function toggleStatus($id)
    {
        try {
            $category = ServiceCategory::findOrFail($id);
            $category->is_active = !$category->is_active;
            $category->save();

            // تسجيل النشاط
            activity()
                ->performedOn($category)
                ->causedBy(auth()->user())
                ->log('تم تغيير حالة فئة الخدمة');

            return redirect()->back()
                ->with('success', 'تم تغيير حالة فئة الخدمة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تغيير حالة فئة الخدمة: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة فئة الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على جميع معرفات الفئات الفرعية
     */
    private function getAllChildrenIds(ServiceCategory $category)
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildrenIds($child));
        }
        return $ids;
    }

    /**
     * عرض الخدمات في فئة محددة
     */
    public function services($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $services = Service::where('category_id', $id)
            ->orderBy('name')
            ->paginate(15);

        return view('admin.services.categories.services', compact('category', 'services'));
    }

    /**
     * عرض المتخصصين في فئة محددة
     */
    public function specialists($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $specialists = $category->specialists()
            ->with('user')
            ->paginate(15);

        return view('admin.services.categories.specialists', compact('category', 'specialists'));
    }

    /**
     * تصدير بيانات فئات الخدمات
     */
    public function export(Request $request)
    {
        $query = ServiceCategory::with(['parent', 'children', 'services']);

        // تطبيق نفس الفلاتر المستخدمة في الصفحة الرئيسية
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active == 1);
        }

        if ($request->has('parent_id') && !empty($request->parent_id)) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $categories = $query->get();

        // تحديد نوع التصدير
        $exportType = $request->input('export_type', 'csv');

        switch ($exportType) {
            case 'excel':
                return (new \App\Exports\ServiceCategoriesExport($categories))->download('service_categories.xlsx');
            case 'pdf':
                return (new \App\Exports\ServiceCategoriesExport($categories))->download('service_categories.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            case 'csv':
            default:
                return (new \App\Exports\ServiceCategoriesExport($categories))->download('service_categories.csv');
        }
    }
}
