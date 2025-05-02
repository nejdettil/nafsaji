<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

class ServiceCategoryController extends Controller
{
    /**
     * عرض قائمة فئات الخدمات
     */
    public function index()
    {
        $categories = ServiceCategory::paginate(10); // مع 10
        
        // إضافة عدد الخدمات لكل فئة
        $categories->each(function ($category) {
            $category->services_count = $category->services()->count();
        });
        
        return view('admin.services.categories.index', compact('categories'));
    }

    /**
     * عرض نموذج إنشاء فئة جديدة
     */
    public function create()
    {
        return view('admin.services.categories.create');
    }

    /**
     * حفظ فئة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ServiceCategory::create($validated);

        return redirect()->route('admin.services.categories.index')
            ->with('success', 'تم إنشاء الفئة بنجاح');
    }

    /**
     * عرض فئة محددة
     */
    public function show(ServiceCategory $category)
    {
        // الحصول على الخدمات المرتبطة بالفئة
        $services = $category->services()->paginate(10);
        
        return view('admin.services.categories.show', compact('category', 'services'));
    }

    /**
     * عرض نموذج تعديل فئة
     */
    public function edit(ServiceCategory $category)
    {
        return view('admin.services.categories.edit', compact('category'));
    }

    /**
     * تحديث فئة محددة
     */
    public function update(Request $request, ServiceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.services.categories.index')
            ->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * حذف فئة محددة
     */
    public function destroy(ServiceCategory $category)
    {
        // التحقق من عدم وجود خدمات مرتبطة بالفئة
        if ($category->services()->count() > 0) {
            return redirect()->route('admin.services.categories.index')
                ->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على خدمات مرتبطة بها');
        }

        $category->delete();

        return redirect()->route('admin.services.categories.index')
            ->with('success', 'تم حذف الفئة بنجاح');
    }
}
