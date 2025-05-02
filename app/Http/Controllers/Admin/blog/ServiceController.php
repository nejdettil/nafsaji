<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Specialist;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات في لوحة الإدارة
     */
    public function index(Request $request)
    {
        $query = Service::with(['category', 'specialists', 'packages']);

        // تصفية حسب الحالة
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active == 1);
        }

        // تصفية حسب الفئة
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // البحث بواسطة الاسم
        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // تصفية حسب نطاق السعر
        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // تصفية حسب المدة
        if ($request->has('min_duration') && !empty($request->min_duration)) {
            $query->where('duration', '>=', $request->min_duration);
        }
        if ($request->has('max_duration') && !empty($request->max_duration)) {
            $query->where('duration', '<=', $request->max_duration);
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'name');
        $orderDirection = $request->input('order_direction', 'asc');
        $query->orderBy($orderBy, $orderDirection);

        $services = $query->paginate($request->input('per_page', 15));
        
        // الحصول على قوائم للفلاتر
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.services.index', compact('services', 'categories'));
    }

    /**
     * عرض نموذج إنشاء خدمة جديدة
     */
    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $specialists = Specialist::with('user')->where('is_active', true)->get();
        
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
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
            'specialists' => 'nullable|array',
            'specialists.*' => 'exists:specialists,id',
        ], [
            'name.required' => 'يرجى إدخال اسم الخدمة',
            'name.unique' => 'اسم الخدمة موجود بالفعل',
            'category_id.required' => 'يرجى اختيار فئة الخدمة',
            'category_id.exists' => 'فئة الخدمة غير موجودة',
            'description.required' => 'يرجى إدخال وصف الخدمة',
            'price.required' => 'يرجى إدخال سعر الخدمة',
            'price.numeric' => 'سعر الخدمة يجب أن يكون رقم',
            'price.min' => 'سعر الخدمة يجب أن يكون أكبر من أو يساوي صفر',
            'duration.required' => 'يرجى إدخال مدة الخدمة',
            'duration.integer' => 'مدة الخدمة يجب أن تكون رقم صحيح',
            'duration.min' => 'مدة الخدمة يجب أن تكون أكبر من أو تساوي 1',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة',
            'image.mimes' => 'صيغة الصورة غير مدعومة، الصيغ المدعومة هي: jpeg, png, jpg, gif, svg',
            'image.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            'specialists.*.exists' => 'أحد المتخصصين المختارين غير موجود',
        ]);

        try {
            DB::beginTransaction();

            // التعامل مع الصورة
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('services', 'public');
            }

            // إنشاء الخدمة
            $service = Service::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'price' => $request->price,
                'duration' => $request->duration,
                'image' => $imagePath,
                'is_active' => $request->has('is_active'),
            ]);

            // ربط الخدمة بالمتخصصين
            if ($request->has('specialists') && is_array($request->specialists)) {
                $service->specialists()->attach($request->specialists);
            }

            DB::commit();

            // تسجيل النشاط
            activity()
                ->performedOn($service)
                ->causedBy(auth()->user())
                ->log('تم إنشاء خدمة جديدة');

            return redirect()->route('admin.services.index')
                ->with('success', 'تم إنشاء الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إنشاء الخدمة: ' . $e->getMessage());
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
        $service = Service::with(['category', 'specialists.user', 'packages', 'bookings'])
            ->findOrFail($id);

        // عدد الحجوزات لهذه الخدمة
        $bookingsCount = $service->bookings->count();
        
        // عدد المتخصصين الذين يقدمون هذه الخدمة
        $specialistsCount = $service->specialists->count();
        
        // عدد الباقات التي تتضمن هذه الخدمة
        $packagesCount = $service->packages->count();

        return view('admin.services.show', compact(
            'service',
            'bookingsCount',
            'specialistsCount',
            'packagesCount'
        ));
    }

    /**
     * عرض نموذج تعديل خدمة
     */
    public function edit($id)
    {
        $service = Service::with('specialists')->findOrFail($id);
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $specialists = Specialist::with('user')->where('is_active', true)->get();
        $selectedSpecialists = $service->specialists->pluck('id')->toArray();

        return view('admin.services.edit', compact('service', 'categories', 'specialists', 'selectedSpecialists'));
    }

    /**
     * تحديث خدمة محددة
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $id,
            'category_id' => 'required|exists:service_categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'boolean',
            'specialists' => 'nullable|array',
            'specialists.*' => 'exists:specialists,id',
        ], [
            'name.required' => 'يرجى إدخال اسم الخدمة',
            'name.unique' => 'اسم الخدمة موجود بالفعل',
            'category_id.required' => 'يرجى اختيار فئة الخدمة',
            'category_id.exists' => 'فئة الخدمة غير موجودة',
            'description.required' => 'يرجى إدخال وصف الخدمة',
            'price.required' => 'يرجى إدخال سعر الخدمة',
            'price.numeric' => 'سعر الخدمة يجب أن يكون رقم',
            'price.min' => 'سعر الخدمة يجب أن يكون أكبر من أو يساوي صفر',
            'duration.required' => 'يرجى إدخال مدة الخدمة',
            'duration.integer' => 'مدة الخدمة يجب أن تكون رقم صحيح',
            'duration.min' => 'مدة الخدمة يجب أن تكون أكبر من أو تساوي 1',
            'image.image' => 'يجب أن تكون الصورة من نوع صورة',
            'image.mimes' => 'صيغة الصورة غير مدعومة، الصيغ المدعومة هي: jpeg, png, jpg, gif, svg',
            'image.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            'specialists.*.exists' => 'أحد المتخصصين المختارين غير موجود',
        ]);

        try {
            DB::beginTransaction();

            // التعامل مع الصورة
            if ($request->hasFile('image')) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }
                $service->image = $request->file('image')->store('services', 'public');
            }

            // تحديث الخدمة
            $service->name = $request->name;
            $service->category_id = $request->category_id;
            $service->description = $request->description;
            $service->price = $request->price;
            $service->duration = $request->duration;
            $service->is_active = $request->has('is_active');
            $service->save();

            // تحديث المتخصصين
            if ($request->has('specialists')) {
                $service->specialists()->sync($request->specialists);
            } else {
                $service->specialists()->detach();
            }

            DB::commit();

            // تسجيل النشاط
            activity()
                ->performedOn($service)
                ->causedBy(auth()->user())
                ->log('تم تحديث خدمة');

            return redirect()->route('admin.services.index')
                ->with('success', 'تم تحديث الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث الخدمة: ' . $e->getMessage());
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

            // حذف الصورة إذا كانت موجودة
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            // حذف العلاقات مع المتخصصين والباقات
            $service->specialists()->detach();
            $service->packages()->detach();

            // حذف الخدمة
            $service->delete();

            // تسجيل النشاط
            activity()
                ->performedOn($service)
                ->causedBy(auth()->user())
                ->log('تم حذف خدمة');

            return redirect()->route('admin.services.index')
                ->with('success', 'تم حذف الخدمة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حذف الخدمة: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * تغيير حالة الخدمة (نشط/غير نشط)
     */
    public function toggleStatus($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->is_active = !$service->is_active;
            $service->save();

            // تسجيل النشاط
            activity()
                ->performedOn($service)
                ->causedBy(auth()->user())
                ->log('تم تغيير حالة الخدمة');

            return redirect()->back()
                ->with('success', 'تم تغيير حالة الخدمة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تغيير حالة الخدمة: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * عرض المتخصصين الذين يقدمون خدمة محددة
     */
    public function specialists($id)
    {
        $service = Service::findOrFail($id);
        $specialists = $service->specialists()
            ->with('user')
            ->paginate(15);

        return view('admin.services.specialists', compact('service', 'specialists'));
    }

    /**
     * عرض الباقات التي تتضمن خدمة محددة
     */
    public function packages($id)
    {
        $service = Service::findOrFail($id);
        $packages = $service->packages()
            ->paginate(15);

        return view('admin.services.packages', compact('service', 'packages'));
    }

    /**
     * عرض الحجوزات لخدمة محددة
     */
    public function bookings($id)
    {
        $service = Service::findOrFail($id);
        $bookings = $service->bookings()
            ->with(['user', 'specialist.user'])
            ->orderBy('booking_date', 'desc')
            ->paginate(15);

        return view('admin.services.bookings', compact('service', 'bookings'));
    }

    /**
     * تصدير بيانات الخدمات
     */
    public function export(Request $request)
    {
        $query = Service::with(['category', 'specialists', 'packages']);

        // تطبيق نفس الفلاتر المستخدمة في الصفحة الرئيسية
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active == 1);
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('min_price') && !empty($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && !empty($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        $services = $query->get();

        // تحديد نوع التصدير
        $exportType = $request->input('export_type', 'csv');

        switch ($exportType) {
            case 'excel':
                return (new \App\Exports\ServicesExport($services))->download('services.xlsx');
            case 'pdf':
                return (new \App\Exports\ServicesExport($services))->download('services.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            case 'csv':
            default:
                return (new \App\Exports\ServicesExport($services))->download('services.csv');
        }
    }
}
