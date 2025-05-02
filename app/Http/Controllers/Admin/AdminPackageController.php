<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Service;
use App\Models\Specialist;
use Illuminate\Support\Facades\Storage;

class AdminPackageController extends Controller
{
    /**
     * عرض قائمة الباقات
     */
    public function index()
    {
        $packages = Package::with(['services', 'specialists'])->latest()->paginate(10);

        return view('admin.packages.index', compact('packages'));
    }

    /**
     * عرض نموذج إنشاء باقة جديدة
     */
    public function create()
    {
        $services = Service::where('is_active', true)->get();
        $specialists = Specialist::where('is_active', true)->get();
        
        return view('admin.packages.create', compact('services', 'specialists'));
    }

    /**
     * حفظ باقة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'sessions_count' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'specialist_ids' => 'required|array',
            'specialist_ids.*' => 'exists:specialists,id',
        ]);

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('packages', 'public');
            $validated['image'] = $imagePath;
        }

        // إنشاء الباقة
        $package = Package::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'sessions_count' => $validated['sessions_count'],
            'is_active' => $request->has('is_active'),
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'image' => $validated['image'] ?? null,
        ]);

        // ربط الخدمات بالباقة
        $package->service()->attach($validated['service_ids']);
        
        // ربط المختصين بالباقة
        $package->specialists()->attach($validated['specialist_ids']);

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم إنشاء الباقة بنجاح');
    }

    /**
     * عرض باقة محددة
     */
    public function show(Package $package)
    {
        $package->load(['service', 'specialists', 'bookings']);
        
        return view('admin.packages.show', compact('package'));
    }

    /**
     * عرض نموذج تعديل باقة
     */
    public function edit(Package $package)
    {
        $services = Service::where('is_active', true)->get();
        $specialists = Specialist::where('is_active', true)->get();

        $selectedServices = $package->services()->pluck('services.id')->toArray();



        return view('admin.packages.edit', compact('package', 'services', 'specialists', 'selectedServices', 'selectedSpecialists'));
    }

    /**
     * تحديث باقة محددة
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'sessions_count' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'specialist_ids' => 'required|array',
            'specialist_ids.*' => 'exists:specialists,id',
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

        // تحديث الباقة
        $package->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'sessions_count' => $validated['sessions_count'],
            'is_active' => $request->has('is_active'),
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'image' => $validated['image'] ?? $package->image,
        ]);

        // تحديث الخدمات المرتبطة بالباقة
        $package->service()->sync($validated['service_ids']);
        
        // تحديث المختصين المرتبطين بالباقة
        $package->specialists()->sync($validated['specialist_ids']);

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم تحديث الباقة بنجاح');
    }

    /**
     * حذف باقة محددة
     */
    public function destroy(Package $package)
    {
        // التحقق من عدم وجود حجوزات مرتبطة بالباقة
        if ($package->bookings()->count() > 0) {
            return redirect()->route('admin.packages.index')
                ->with('error', 'لا يمكن حذف الباقة لأنها مرتبطة بحجوزات');
        }

        // حذف الصورة إذا كانت موجودة
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }

        // حذف العلاقات
        $package->service()->detach();
        $package->specialists()->detach();
        
        // حذف الباقة
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم حذف الباقة بنجاح');
    }
}
