<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminPackageController extends Controller
{
    /**
     * عرض قائمة الباقات
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = Package::with(['services', 'specialists'])->latest()->paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    /**
     * عرض نموذج إنشاء باقة جديدة
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::all();
        $specialists = Specialist::all();
        $categories = ServiceCategory::all();
        return view('admin.packages.create', compact('services', 'specialists', 'categories'));
    }

    /**
     * تخزين باقة جديدة
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'service_id' => 'required|exists:services,id',
            'specialists' => 'nullable|array',
            'specialists.*' => 'exists:specialists,id',
            'sessions_count' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('packages', 'public');
            $validated['image'] = $imagePath;
        }

        // إنشاء رمز فريد للباقة
        $validated['code'] = 'PKG-' . strtoupper(Str::random(8));
        
        // تعيين القيم الافتراضية
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        // إنشاء الباقة
        $package = Package::create($validated);

        // ربط المختصين بالباقة إذا تم تحديدهم
        if ($request->has('specialists')) {
            $package->specialists()->sync($request->specialists);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم إنشاء الباقة بنجاح');
    }

    /**
     * عرض تفاصيل باقة محددة
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function show(Package $package)
    {
        $package->load(['service', 'specialists']);
        return view('admin.packages.show', compact('package'));
    }

    /**
     * عرض نموذج تعديل باقة محددة
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Package $package)
    {
        $services = Service::all();
        $specialists = Specialist::all();
        $categories = ServiceCategory::all();
        $selectedSpecialists = $package->specialists->pluck('id')->toArray();
        
        return view('admin.packages.edit', compact('package', 'services', 'specialists', 'categories', 'selectedSpecialists'));
    }

    /**
     * تحديث باقة محددة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'service_id' => 'required|exists:services,id',
            'specialists' => 'nullable|array',
            'specialists.*' => 'exists:specialists,id',
            'sessions_count' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            
            $imagePath = $request->file('image')->store('packages', 'public');
            $validated['image'] = $imagePath;
        }

        // تعيين القيم الافتراضية
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        // تحديث الباقة
        $package->update($validated);

        // ربط المختصين بالباقة إذا تم تحديدهم
        if ($request->has('specialists')) {
            $package->specialists()->sync($request->specialists);
        } else {
            $package->specialists()->detach();
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم تحديث الباقة بنجاح');
    }

    /**
     * حذف باقة محددة
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
        // حذف الصورة إذا كانت موجودة
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }

        // فصل العلاقات قبل الحذف
        $package->specialists()->detach();
        
        // حذف الباقة
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم حذف الباقة بنجاح');
    }
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'لم يتم تحديد أي عناصر.'], 422);
        }

        if ($action === 'delete') {
            Package::whereIn('id', $ids)->delete();
            return response()->json(['message' => 'تم حذف الباقات بنجاح.']);
        }

        if (in_array($action, ['active', 'inactive'])) {
            Package::whereIn('id', $ids)->update(['status' => $action]);
            return response()->json(['message' => 'تم تحديث الحالة بنجاح.']);
        }

        return response()->json(['message' => 'إجراء غير معروف.'], 400);
    }

}
