<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Specialist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات
     */
    public function index(Request $request)
    {
        $query = Service::with('category');

        // تصفية حسب الفئة
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // البحث بالاسم
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $services = $query->paginate($request->input('per_page', 15));
        $categories = ServiceCategory::paginate(10); // العدد حسب الحاجة
        $statuses = ['active' => 'نشط', 'inactive' => 'غير نشط'];
        $packages = \App\Models\Package::paginate(10); // ← هذه اللي كانت موجودة قبلك

        return view('admin.services.index', compact(
            'services',
            'categories',
            'statuses',
            'packages'
        ));
    }

    public function packagesShow($id)
    {
        $package = \App\Models\Package::findOrFail($id);
        return view('admin.services.packages.show', compact('package'));
    }

    /**
     * عرض نموذج إنشاء خدمة جديدة
     */
    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)->get();
        $specialists = Specialist::with('user')->where('status', 'active')->get();
        return view('admin.services.create', compact('categories', 'specialists'));
    }

    /**
     * حفظ خدمة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services',
            'category_id' => 'required|exists:service_categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration' => 'required|integer|min:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
            'specialists' => 'nullable|array',
            'specialists.*' => 'exists:specialists,id',
            'is_featured' => 'boolean',
            'is_online' => 'boolean',
            'is_home_service' => 'boolean',
        ], [
            'name.required' => 'يرجى إدخال اسم الخدمة',
            'name.unique' => 'اسم الخدمة موجود بالفعل',
            'category_id.required' => 'يرجى اختيار فئة الخدمة',
            'description.required' => 'يرجى إدخال وصف الخدمة',
            'price.required' => 'يرجى إدخال سعر الخدمة',
            'price.numeric' => 'يجب أن يكون السعر رقمًا',
            'price.min' => 'يجب أن يكون السعر أكبر من أو يساوي صفر',
            'duration.required' => 'يرجى إدخال مدة الخدمة',
            'duration.integer' => 'يجب أن تكون المدة رقمًا صحيحًا',
            'duration.min' => 'يجب أن تكون المدة 15 دقيقة على الأقل',
            'image.image' => 'يجب أن تكون الصورة صورة',
            'image.mimes' => 'صيغة الصورة غير مدعومة',
            'image.max' => 'حجم الصورة كبير جدًا',
            'status.required' => 'يرجى اختيار حالة الخدمة',
            'specialists.*.exists' => 'أحد المختصين المحددين غير موجود',
        ]);

        try {
            DB::beginTransaction();

            $service = new Service();
            $service->name = $request->name;
            $service->slug = Str::slug($request->name);
            $service->category_id = $request->category_id;
            $service->description = $request->description;
            $service->short_description = $request->short_description;
            $service->price = $request->price;
            $service->discount_price = $request->discount_price;
            $service->duration = $request->duration;
            $service->status = $request->status;
            $service->is_featured = $request->has('is_featured');
            $service->is_online = $request->has('is_online');
            $service->is_home_service = $request->has('is_home_service');

            // معالجة الصورة
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('services', 'public');
                $service->image = $imagePath;
            }

            $service->save();

            // ربط المختصين بالخدمة
            if ($request->has('specialists') && is_array($request->specialists)) {
                $service->specialists()->sync($request->specialists);
            }

            DB::commit();

            return redirect()->route('admin.services.index')
                ->with('success', 'تم إنشاء الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الخدمة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل خدمة محددة
     */
    public function show($id)
    {
        $service = Service::with(['category', 'specialists.user', 'bookings'])->findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    /**
     * عرض نموذج تعديل خدمة
     */
    public function edit($id)
    {
        $service = Service::with('specialists')->findOrFail($id);
        $categories = ServiceCategory::where('is_active', true)->get();
        $specialists = Specialist::with('user')->where('status', 'active')->get();
        $selectedSpecialists = $service->specialists->pluck('id')->toArray();

        return view('admin.services.edit', compact('service', 'categories', 'specialists', 'selectedSpecialists'));
    }

    /**
     * تحديث خدمة محددة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $id,
            'category_id' => 'required|exists:service_categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'duration' => 'required|integer|min:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
            'specialists' => 'nullable|array',
            'specialists.*' => 'exists:specialists,id',
            'is_featured' => 'boolean',
            'is_online' => 'boolean',
            'is_home_service' => 'boolean',
        ], [
            'name.required' => 'يرجى إدخال اسم الخدمة',
            'name.unique' => 'اسم الخدمة موجود بالفعل',
            'category_id.required' => 'يرجى اختيار فئة الخدمة',
            'description.required' => 'يرجى إدخال وصف الخدمة',
            'price.required' => 'يرجى إدخال سعر الخدمة',
            'price.numeric' => 'يجب أن يكون السعر رقمًا',
            'price.min' => 'يجب أن يكون السعر أكبر من أو يساوي صفر',
            'duration.required' => 'يرجى إدخال مدة الخدمة',
            'duration.integer' => 'يجب أن تكون المدة رقمًا صحيحًا',
            'duration.min' => 'يجب أن تكون المدة 15 دقيقة على الأقل',
            'image.image' => 'يجب أن تكون الصورة صورة',
            'image.mimes' => 'صيغة الصورة غير مدعومة',
            'image.max' => 'حجم الصورة كبير جدًا',
            'status.required' => 'يرجى اختيار حالة الخدمة',
            'specialists.*.exists' => 'أحد المختصين المحددين غير موجود',
        ]);

        try {
            DB::beginTransaction();

            $service = Service::findOrFail($id);
            $service->name = $request->name;
            $service->slug = Str::slug($request->name);
            $service->category_id = $request->category_id;
            $service->description = $request->description;
            $service->short_description = $request->short_description;
            $service->price = $request->price;
            $service->discount_price = $request->discount_price;
            $service->duration = $request->duration;
            $service->status = $request->status;
            $service->is_featured = $request->has('is_featured');
            $service->is_online = $request->has('is_online');
            $service->is_home_service = $request->has('is_home_service');

            // معالجة الصورة
            if ($request->hasFile('image')) {
                // حذف الصورة القديمة
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }

                $imagePath = $request->file('image')->store('services', 'public');
                $service->image = $imagePath;
            }

            $service->save();

            // ربط المختصين بالخدمة
            if ($request->has('specialists')) {
                $service->specialists()->sync($request->specialists);
            } else {
                $service->specialists()->detach();
            }

            DB::commit();

            return redirect()->route('admin.services.index')
                ->with('success', 'تم تحديث الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الخدمة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف خدمة محددة
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);

            // التحقق من عدم وجود حجوزات مرتبطة بالخدمة
            if ($service->bookings()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الخدمة لأنها مرتبطة بحجوزات');
            }

            // حذف الصورة المرتبطة
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            // فصل المختصين
            $service->specialists()->detach();

            $service->delete();

            return redirect()->route('admin.services.index')
                ->with('success', 'تم حذف الخدمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * تغيير حالة الخدمة
     */
    public function changeStatus($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->status = $service->status === 'active' ? 'inactive' : 'active';
            $service->save();

            return redirect()->back()
                ->with('success', 'تم تغيير حالة الخدمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * تغيير خاصية العرض المميز للخدمة
     */
    public function toggleFeatured($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->is_featured = !$service->is_featured;
            $service->save();

            return redirect()->back()
                ->with('success', 'تم تغيير خاصية العرض المميز للخدمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير خاصية العرض المميز للخدمة: ' . $e->getMessage());
        }
    }
}
