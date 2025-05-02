<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    /**
     * عرض قائمة فئات المدونة
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = BlogCategory::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.blog.categories.index', compact('categories'));
    }

    /**
     * عرض نموذج إنشاء فئة جديدة
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.blog.categories.create');
    }

    /**
     * تخزين فئة جديدة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories',
            'slug' => 'required|string|max:255|unique:blog_categories',
            'description' => 'nullable|string',
        ]);

        BlogCategory::create($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'تم إنشاء الفئة بنجاح');
    }

    /**
     * عرض فئة محددة
     *
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\View\View
     */
    public function show(BlogCategory $category)
    {
        return view('admin.blog.categories.show', compact('category'));
    }

    /**
     * عرض نموذج تعديل فئة
     *
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\View\View
     */
    public function edit(BlogCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    /**
     * تحديث فئة محددة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $category->id,
            'slug' => 'required|string|max:255|unique:blog_categories,slug,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * حذف فئة محددة من قاعدة البيانات
     *
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BlogCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'تم حذف الفئة بنجاح');
    }
}
