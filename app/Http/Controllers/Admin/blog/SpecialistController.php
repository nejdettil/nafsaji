<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class SpecialistController extends Controller
{
    /**
     * عرض قائمة المتخصصين في لوحة الإدارة
     */
    public function index(Request $request)
    {
        $query = Specialist::with(['user', 'services', 'categories']);

        // تصفية حسب الحالة
        if ($request->has('is_verified') && $request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified == 1);
        }

        // تصفية حسب الظهور المميز
        if ($request->has('is_featured') && $request->is_featured !== null) {
            $query->where('is_featured', $request->is_featured == 1);
        }

        // البحث بواسطة الاسم
        if ($request->has('name') && !empty($request->name)) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        // البحث بواسطة البريد الإلكتروني
        if ($request->has('email') && !empty($request->email)) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->email . '%');
            });
        }

        // تصفية حسب التخصص
        if ($request->has('specialty') && !empty($request->specialty)) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        // تصفية حسب سنوات الخبرة
        if ($request->has('min_experience') && !empty($request->min_experience)) {
            $query->where('experience_years', '>=', $request->min_experience);
        }
        if ($request->has('max_experience') && !empty($request->max_experience)) {
            $query->where('experience_years', '<=', $request->max_experience);
        }

        // تصفية حسب التقييم
        if ($request->has('min_rating') && !empty($request->min_rating)) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // تصفية حسب الفئة
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('service_categories.id', $request->category_id);
            });
        }

        // تصفية حسب الخدمة
        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->service_id);
            });
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'rating');
        $orderDirection = $request->input('order_direction', 'desc');
        
        if ($orderBy === 'name') {
            $query->join('users', 'specialists.user_id', '=', 'users.id')
                  ->orderBy('users.name', $orderDirection)
                  ->select('specialists.*');
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $specialists = $query->paginate($request->input('per_page', 15));
        
        // الحصول على قوائم للفلاتر
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('admin.specialists.index', compact('specialists', 'categories', 'services'));
    }

    /**
     * عرض نموذج إنشاء متخصص جديد
     */
    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.specialists.create', compact('categories', 'services'));
    }

    /**
     * حفظ متخصص جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'specialty' => 'required|string|max:255',
            'bio' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:service_categories,id',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
        ], [
            'name.required' => 'يرجى إدخال اسم المتخصص',
            'email.required' => 'يرجى إدخال البريد الإلكتروني',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'يرجى إدخال كلمة المرور',
            'password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
            'phone.required' => 'يرجى إدخال رقم الهاتف',
            'specialty.required' => 'يرجى إدخال التخصص',
            'bio.required' => 'يرجى إدخال نبذة عن المتخصص',
            'experience_years.required' => 'يرجى إدخال سنوات الخبرة',
            'experience_years.integer' => 'سنوات الخبرة يجب أن تكون رقم صحيح',
            'experience_years.min' => 'سنوات الخبرة يجب أن تكون أكبر من أو تساوي 0',
            'education.required' => 'يرجى إدخال المؤهل العلمي',
            'hourly_rate.required' => 'يرجى إدخال السعر بالساعة',
            'hourly_rate.numeric' => 'السعر بالساعة يجب أن يكون رقم',
            'hourly_rate.min' => 'السعر بالساعة يجب أن يكون أكبر من أو يساوي 0',
            'profile_image.image' => 'يجب أن تكون الصورة من نوع صورة',
            'profile_image.mimes' => 'صيغة الصورة غير مدعومة، الصيغ المدعومة هي: jpeg, png, jpg, gif, svg',
            'profile_image.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            'categories.required' => 'يرجى اختيار فئة واحدة على الأقل',
            'categories.*.exists' => 'إحدى الفئات المختارة غير موجودة',
            'services.required' => 'يرجى اختيار خدمة واحدة على الأقل',
            'services.*.exists' => 'إحدى الخدمات المختارة غير موجودة',
        ]);

        try {
            DB::beginTransaction();

            // التعامل مع الصورة
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('specialists', 'public');
            }

            // إنشاء المستخدم
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'profile_image' => $profileImagePath,
            ]);

            // تعيين دور المتخصص للمستخدم
            $user->assignRole('specialist');

            // إنشاء المتخصص
            $specialist = Specialist::create([
                'user_id' => $user->id,
                'specialty' => $request->specialty,
                'bio' => $request->bio,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'certifications' => $request->certifications,
                'hourly_rate' => $request->hourly_rate,
                'is_verified' => $request->has('is_verified'),
                'is_featured' => $request->has('is_featured'),
                'availability' => [], // سيتم تعيينه لاحقاً
                'rating' => 0, // سيتم حسابه لاحقاً
            ]);

            // ربط المتخصص بالفئات
            if ($request->has('categories') && is_array($request->categories)) {
                $specialist->categories()->attach($request->categories);
            }

            // ربط المتخصص بالخدمات
            if ($request->has('services') && is_array($request->services)) {
                $specialist->services()->attach($request->services);
            }

            DB::commit();

            // تسجيل النشاط
            activity()
                ->performedOn($specialist)
                ->causedBy(auth()->user())
                ->log('تم إنشاء متخصص جديد');

            return redirect()->route('admin.specialists.index')
                ->with('success', 'تم إنشاء المتخصص بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إنشاء المتخصص: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء المتخصص: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل متخصص محدد
     */
    public function show($id)
    {
        $specialist = Specialist::with([
            'user', 
            'services', 
            'categories', 
            'bookings.service', 
            'bookings.user',
            'reviews.user'
        ])->findOrFail($id);

        // إحصائيات المتخصص
        $totalBookings = $specialist->bookings->count();
        $completedBookings = $specialist->bookings->where('status', 'completed')->count();
        $pendingBookings = $specialist->bookings->where('status', 'pending')->count();
        $cancelledBookings = $specialist->bookings->where('status', 'cancelled')->count();
        
        $totalReviews = $specialist->reviews->count();
        $averageRating = $specialist->reviews->avg('rating') ?? 0;
        
        // الحجوزات القادمة
        $upcomingBookings = $specialist->bookings()
            ->where('booking_date', '>=', now())
            ->orderBy('booking_date', 'asc')
            ->take(5)
            ->get();
        
        // آخر التقييمات
        $latestReviews = $specialist->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.specialists.show', compact(
            'specialist',
            'totalBookings',
            'completedBookings',
            'pendingBookings',
            'cancelledBookings',
            'totalReviews',
            'averageRating',
            'upcomingBookings',
            'latestReviews'
        ));
    }

    /**
     * عرض نموذج تعديل متخصص
     */
    public function edit($id)
    {
        $specialist = Specialist::with(['user', 'services', 'categories'])->findOrFail($id);
        $categories = ServiceCategory::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        
        $selectedCategories = $specialist->categories->pluck('id')->toArray();
        $selectedServices = $specialist->services->pluck('id')->toArray();

        return view('admin.specialists.edit', compact(
            'specialist', 
            'categories', 
            'services', 
            'selectedCategories', 
            'selectedServices'
        ));
    }

    /**
     * تحديث متخصص محدد
     */
    public function update(Request $request, $id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $specialist->user_id,
            'phone' => 'required|string|max:20',
            'specialty' => 'required|string|max:255',
            'bio' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:service_categories,id',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
        ], [
            'name.required' => 'يرجى إدخال اسم المتخصص',
            'email.required' => 'يرجى إدخال البريد الإلكتروني',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.required' => 'يرجى إدخال رقم الهاتف',
            'specialty.required' => 'يرجى إدخال التخصص',
            'bio.required' => 'يرجى إدخال نبذة عن المتخصص',
            'experience_years.required' => 'يرجى إدخال سنوات الخبرة',
            'experience_years.integer' => 'سنوات الخبرة يجب أن تكون رقم صحيح',
            'experience_years.min' => 'سنوات الخبرة يجب أن تكون أكبر من أو تساوي 0',
            'education.required' => 'يرجى إدخال المؤهل العلمي',
            'hourly_rate.required' => 'يرجى إدخال السعر بالساعة',
            'hourly_rate.numeric' => 'السعر بالساعة يجب أن يكون رقم',
            'hourly_rate.min' => 'السعر بالساعة يجب أن يكون أكبر من أو يساوي 0',
            'profile_image.image' => 'يجب أن تكون الصورة من نوع صورة',
            'profile_image.mimes' => 'صيغة الصورة غير مدعومة، الصيغ المدعومة هي: jpeg, png, jpg, gif, svg',
            'profile_image.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            'categories.required' => 'يرجى اختيار فئة واحدة على الأقل',
            'categories.*.exists' => 'إحدى الفئات المختارة غير موجودة',
            'services.required' => 'يرجى اختيار خدمة واحدة على الأقل',
            'services.*.exists' => 'إحدى الخدمات المختارة غير موجودة',
        ]);

        try {
            DB::beginTransaction();

            // تحديث المستخدم
            $user = $specialist->user;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            
            // تحديث كلمة المرور إذا تم توفيرها
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'string|min:8|confirmed',
                ], [
                    'password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل',
                    'password.confirmed' => 'كلمة المرور غير متطابقة',
                ]);
                
                $user->password = Hash::make($request->password);
            }
            
            // التعامل مع الصورة
            if ($request->hasFile('profile_image')) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $user->profile_image = $request->file('profile_image')->store('specialists', 'public');
            }
            
            $user->save();

            // تحديث المتخصص
            $specialist->specialty = $request->specialty;
            $specialist->bio = $request->bio;
            $specialist->experience_years = $request->experience_years;
            $specialist->education = $request->education;
            $specialist->certifications = $request->certifications;
            $specialist->hourly_rate = $request->hourly_rate;
            $specialist->is_verified = $request->has('is_verified');
            $specialist->is_featured = $request->has('is_featured');
            $specialist->save();

            // تحديث الفئات
            if ($request->has('categories')) {
                $specialist->categories()->sync($request->categories);
            } else {
                $specialist->categories()->detach();
            }

            // تحديث الخدمات
            if ($request->has('services')) {
                $specialist->services()->sync($request->services);
            } else {
                $specialist->services()->detach();
            }

            DB::commit();

            // تسجيل النشاط
            activity()
                ->performedOn($specialist)
                ->causedBy(auth()->user())
                ->log('تم تحديث متخصص');

            return redirect()->route('admin.specialists.index')
                ->with('success', 'تم تحديث المتخصص بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث المتخصص: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المتخصص: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف متخصص محدد
     */
    public function destroy($id)
    {
        try {
            $specialist = Specialist::with('user')->findOrFail($id);

            // التحقق من عدم وجود حجوزات مرتبطة بالمتخصص
            if ($specialist->bookings()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف المتخصص لأنه مرتبط بحجوزات');
            }

            // حذف الصورة إذا كانت موجودة
            if ($specialist->user->profile_image) {
                Storage::disk('public')->delete($specialist->user->profile_image);
            }

            // حذف العلاقات مع الفئات والخدمات والباقات
            $specialist->categories()->detach();
            $specialist->services()->detach();
            $specialist->packages()->detach();

            // حذف التقييمات
            $specialist->reviews()->delete();

            // حذف المتخصص
            $specialist->delete();

            // حذف المستخدم المرتبط (اختياري)
            // $specialist->user->delete();

            // تسجيل النشاط
            activity()
                ->performedOn($specialist)
                ->causedBy(auth()->user())
                ->log('تم حذف متخصص');

            return redirect()->route('admin.specialists.index')
                ->with('success', 'تم حذف المتخصص بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حذف المتخصص: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المتخصص: ' . $e->getMessage());
        }
    }

    /**
     * تغيير حالة التحقق من المتخصص
     */
    public function toggleVerification($id)
    {
        try {
            $specialist = Specialist::findOrFail($id);
            $specialist->is_verified = !$specialist->is_verified;
            $specialist->save();

            // تسجيل النشاط
            activity()
                ->performedOn($specialist)
                ->causedBy(auth()->user())
                ->log('تم تغيير حالة التحقق من المتخصص');

            return redirect()->back()
                ->with('success', 'تم تغيير حالة التحقق من المتخصص بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تغيير حالة التحقق من المتخصص: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة التحقق من المتخصص: ' . $e->getMessage());
        }
    }

    /**
     * تغيير حالة الظهور المميز للمتخصص
     */
    public function toggleFeatured($id)
    {
        try {
            $specialist = Specialist::findOrFail($id);
            $specialist->is_featured = !$specialist->is_featured;
            $specialist->save();

            // تسجيل النشاط
            activity()
                ->performedOn($specialist)
                ->causedBy(auth()->user())
                ->log('تم تغيير حالة الظهور المميز للمتخصص');

            return redirect()->back()
                ->with('success', 'تم تغيير حالة الظهور المميز للمتخصص بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تغيير حالة الظهور المميز للمتخصص: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة الظهور المميز للمتخصص: ' . $e->getMessage());
        }
    }

    /**
     * عرض حجوزات متخصص محدد
     */
    public function bookings($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $bookings = $specialist->bookings()
            ->with(['user', 'service', 'payment'])
            ->orderBy('booking_date', 'desc')
            ->paginate(15);

        return view('admin.specialists.bookings', compact('specialist', 'bookings'));
    }

    /**
     * عرض تقييمات متخصص محدد
     */
    public function reviews($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $reviews = $specialist->reviews()
            ->with(['user', 'booking.service'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.specialists.reviews', compact('specialist', 'reviews'));
    }

    /**
     * عرض خدمات متخصص محدد
     */
    public function services($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $services = $specialist->services()
            ->with('category')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.specialists.services', compact('specialist', 'services'));
    }

    /**
     * عرض جدول توفر متخصص محدد
     */
    public function availability($id)
    {
        $specialist = Specialist::with('user')->findOrFail($id);
        $availability = $specialist->availability ?? [];

        return view('admin.specialists.availability', compact('specialist', 'availability'));
    }

    /**
     * تحديث جدول توفر متخصص محدد
     */
    public function updateAvailability(Request $request, $id)
    {
        $request->validate([
            'availability' => 'required|array',
            'availability.*.day' => 'required|integer|between:0,6',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i|after:availability.*.start_time',
        ], [
            'availability.required' => 'يرجى تحديد جدول التوفر',
            'availability.*.day.required' => 'يرجى تحديد اليوم',
            'availability.*.day.integer' => 'اليوم يجب أن يكون رقم صحيح',
            'availability.*.day.between' => 'اليوم يجب أن يكون بين 0 و 6',
            'availability.*.start_time.required' => 'يرجى تحديد وقت البداية',
            'availability.*.start_time.date_format' => 'صيغة وقت البداية غير صحيحة',
            'availability.*.end_time.required' => 'يرجى تحديد وقت النهاية',
            'availability.*.end_time.date_format' => 'صيغة وقت النهاية غير صحيحة',
            'availability.*.end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية',
        ]);

        try {
            $specialist = Specialist::findOrFail($id);
            $specialist->availability = $request->availability;
            $specialist->save();

            // تسجيل النشاط
            activity()
                ->performedOn($specialist)
                ->causedBy(auth()->user())
                ->log('تم تحديث جدول توفر المتخصص');

            return redirect()->route('admin.specialists.show', $specialist->id)
                ->with('success', 'تم تحديث جدول التوفر بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث جدول توفر المتخصص: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث جدول التوفر: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * تصدير بيانات المتخصصين
     */
    public function export(Request $request)
    {
        $query = Specialist::with(['user', 'services', 'categories']);

        // تطبيق نفس الفلاتر المستخدمة في الصفحة الرئيسية
        if ($request->has('is_verified') && $request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified == 1);
        }

        if ($request->has('is_featured') && $request->is_featured !== null) {
            $query->where('is_featured', $request->is_featured == 1);
        }

        if ($request->has('name') && !empty($request->name)) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->has('specialty') && !empty($request->specialty)) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        $specialists = $query->get();

        // تحديد نوع التصدير
        $exportType = $request->input('export_type', 'csv');

        switch ($exportType) {
            case 'excel':
                return (new \App\Exports\SpecialistsExport($specialists))->download('specialists.xlsx');
            case 'pdf':
                return (new \App\Exports\SpecialistsExport($specialists))->download('specialists.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            case 'csv':
            default:
                return (new \App\Exports\SpecialistsExport($specialists))->download('specialists.csv');
        }
    }
}
