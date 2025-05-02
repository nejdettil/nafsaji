<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpecialistController extends Controller
{
    /**
     * عرض قائمة المختصين
     */
    public function index(Request $request)
    {
        $query = Specialist::with('user', 'services');

        // فلترة الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // فلترة التخصص
        if ($request->has('specialization') && !empty($request->specialization)) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }

        // فلترة الخدمة
        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->service_id);
            });
        }

        // فلترة حسب الاسم أو البريد أو الهاتف
        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // الترتيب
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        if ($orderBy === 'name') {
            $query->join('users', 'specialists.user_id', '=', 'users.id')
                ->orderBy('users.name', $orderDirection)
                ->select('specialists.*');
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $specialists = $query->paginate($request->input('per_page', 15));

        // البيانات المرافقة للواجهة
        $services = \App\Models\Service::where('status', 'active')->get();
        $statuses = ['active' => 'نشط', 'inactive' => 'غير نشط', 'pending' => 'قيد المراجعة', 'rejected' => 'مرفوض'];
        $specializations = Specialist::distinct()->pluck('specialization');

        // 🔥 المختصين الأعلى تقييماً
        $topRatedSpecialists = Specialist::with('user')
            ->orderByDesc('rating') // تأكد أن عمود rating موجود
            ->take(5)
            ->get();

        // 🔥 المختصين الأكثر نشاطاً (حسب عدد الجلسات)
        $mostActiveSpecialists = Specialist::withCount('sessions')
            ->orderByDesc('sessions_count')
            ->take(5)
            ->get();
// 🔵 تجميع عدد المختصين حسب التخصص
        $specialtiesCount = Specialist::select('specialization', DB::raw('count(*) as total'))
            ->groupBy('specialization')
            ->get();

        $specialtiesChartData = [
            'labels' => $specialtiesCount->pluck('specialization'),
            'data' => $specialtiesCount->pluck('total'),
        ];

        return view('admin.specialists.index', compact(
            'specialists',
            'services',
            'statuses',
            'specializations',
            'topRatedSpecialists',
            'mostActiveSpecialists',
            'specialtiesChartData'
        ));
    }
    public function updateStatus(Request $request)
    {
        $specialist = Specialist::findOrFail($request->input('id'));
        $specialist->is_active = $request->input('is_active');
        $specialist->save();

        return response()->json(['message' => 'تم تحديث حالة المختص بنجاح']);
    }
    public function getSpecialist(Request $request)
    {
        $specialist = Specialist::with('user')->find($request->specialist_id);

        if (!$specialist) {
            return response()->json(['error' => 'المختص غير موجود'], 404);
        }

        return response()->json($specialist);
    }

    /**
     * عرض نموذج إنشاء مختص جديد
     */
    public function create()
    {
        $users = User::whereDoesntHave('specialist')->get();
        $services = Service::where('status', 'active')->get();
        return view('admin.specialists.create', compact('users', 'services'));
    }

    /**
     * حفظ مختص جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:specialists,user_id',
            'specialization' => 'required|string|max:255',
            'bio' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'available_sunday' => 'boolean',
            'available_monday' => 'boolean',
            'available_tuesday' => 'boolean',
            'available_wednesday' => 'boolean',
            'available_thursday' => 'boolean',
            'available_friday' => 'boolean',
            'available_saturday' => 'boolean',
            'work_start_time' => 'required',
            'work_end_time' => 'required|after:work_start_time',
            'status' => 'required|in:active,inactive,pending,rejected',
        ], [
            'user_id.required' => 'يرجى اختيار المستخدم',
            'user_id.unique' => 'هذا المستخدم مرتبط بمختص بالفعل',
            'specialization.required' => 'يرجى إدخال التخصص',
            'bio.required' => 'يرجى إدخال نبذة عن المختص',
            'experience_years.required' => 'يرجى إدخال سنوات الخبرة',
            'experience_years.integer' => 'يجب أن تكون سنوات الخبرة رقمًا صحيحًا',
            'education.required' => 'يرجى إدخال المؤهل العلمي',
            'hourly_rate.required' => 'يرجى إدخال السعر بالساعة',
            'hourly_rate.numeric' => 'يجب أن يكون السعر رقمًا',
            'profile_image.image' => 'يجب أن تكون الصورة صورة',
            'services.required' => 'يرجى اختيار خدمة واحدة على الأقل',
            'work_start_time.required' => 'يرجى تحديد وقت بدء العمل',
            'work_end_time.required' => 'يرجى تحديد وقت انتهاء العمل',
            'work_end_time.after' => 'يجب أن يكون وقت انتهاء العمل بعد وقت البدء',
            'status.required' => 'يرجى اختيار حالة المختص',
        ]);

        try {
            DB::beginTransaction();

            $specialist = new Specialist();
            $specialist->user_id = $request->user_id;
            $specialist->specialization = $request->specialization;
            $specialist->bio = $request->bio;
            $specialist->experience_years = $request->experience_years;
            $specialist->education = $request->education;
            $specialist->certifications = $request->certifications;
            $specialist->hourly_rate = $request->hourly_rate;
            $specialist->available_sunday = $request->has('available_sunday');
            $specialist->available_monday = $request->has('available_monday');
            $specialist->available_tuesday = $request->has('available_tuesday');
            $specialist->available_wednesday = $request->has('available_wednesday');
            $specialist->available_thursday = $request->has('available_thursday');
            $specialist->available_friday = $request->has('available_friday');
            $specialist->available_saturday = $request->has('available_saturday');
            $specialist->work_start_time = $request->work_start_time;
            $specialist->work_end_time = $request->work_end_time;
            $specialist->status = $request->status;

            // معالجة الصورة
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('specialists', 'public');
                $specialist->profile_image = $imagePath;
            }

            $specialist->save();

            // ربط الخدمات بالمختص
            if ($request->has('services') && is_array($request->services)) {
                $specialist->services()->sync($request->services);
            }

            // تعيين دور المختص للمستخدم
            $user = User::find($request->user_id);
            $user->assignRole('specialist');

            DB::commit();

            return redirect()->route('admin.specialists.index')
                ->with('success', 'تم إنشاء المختص بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء المختص: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل مختص محدد
     */
    public function show($id)
    {
        $specialist = Specialist::with(['user', 'services', 'bookings.service', 'bookings.user'])->findOrFail($id);

        // حساب التقييم المتوسط
        $averageRating = $specialist->bookings()
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;

        // حساب عدد الحجوزات المكتملة
        $completedBookingsCount = $specialist->bookings()
            ->where('status', 'completed')
            ->count();

        // حساب إجمالي الإيرادات
        $totalRevenue = $specialist->bookings()
            ->where('status', 'completed')
            ->where('is_paid', true)
            ->sum('total_amount');

        // الحصول على الحجوزات القادمة
        $upcomingBookings = $specialist->bookings()
            ->where('booking_date', '>=', Carbon::today())
            ->where('status', 'confirmed')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->take(5)
            ->get();

        // الحصول على آخر التقييمات
        $latestReviews = $specialist->bookings()
            ->whereNotNull('rating')
            ->whereNotNull('review')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.specialists.show', compact(
            'specialist',
            'averageRating',
            'completedBookingsCount',
            'totalRevenue',
            'upcomingBookings',
            'latestReviews'
        ));
    }

    /**
     * عرض نموذج تعديل مختص
     */
    public function edit($id)
    {
        $specialist = Specialist::with('services')->findOrFail($id);
        $services = Service::where('status', 'active')->get();
        $selectedServices = $specialist->services->pluck('id')->toArray();

        return view('admin.specialists.edit', compact('specialist', 'services', 'selectedServices'));
    }

    /**
     * تحديث مختص محدد
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'specialization' => 'required|string|max:255',
            'bio' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'available_sunday' => 'boolean',
            'available_monday' => 'boolean',
            'available_tuesday' => 'boolean',
            'available_wednesday' => 'boolean',
            'available_thursday' => 'boolean',
            'available_friday' => 'boolean',
            'available_saturday' => 'boolean',
            'work_start_time' => 'required',
            'work_end_time' => 'required|after:work_start_time',
            'status' => 'required|in:active,inactive,pending,rejected',
        ], [
            'specialization.required' => 'يرجى إدخال التخصص',
            'bio.required' => 'يرجى إدخال نبذة عن المختص',
            'experience_years.required' => 'يرجى إدخال سنوات الخبرة',
            'experience_years.integer' => 'يجب أن تكون سنوات الخبرة رقمًا صحيحًا',
            'education.required' => 'يرجى إدخال المؤهل العلمي',
            'hourly_rate.required' => 'يرجى إدخال السعر بالساعة',
            'hourly_rate.numeric' => 'يجب أن يكون السعر رقمًا',
            'profile_image.image' => 'يجب أن تكون الصورة صورة',
            'services.required' => 'يرجى اختيار خدمة واحدة على الأقل',
            'work_start_time.required' => 'يرجى تحديد وقت بدء العمل',
            'work_end_time.required' => 'يرجى تحديد وقت انتهاء العمل',
            'work_end_time.after' => 'يجب أن يكون وقت انتهاء العمل بعد وقت البدء',
            'status.required' => 'يرجى اختيار حالة المختص',
        ]);

        try {
            DB::beginTransaction();

            $specialist = Specialist::findOrFail($id);
            $specialist->specialization = $request->specialization;
            $specialist->bio = $request->bio;
            $specialist->experience_years = $request->experience_years;
            $specialist->education = $request->education;
            $specialist->certifications = $request->certifications;
            $specialist->hourly_rate = $request->hourly_rate;
            $specialist->available_sunday = $request->has('available_sunday');
            $specialist->available_monday = $request->has('available_monday');
            $specialist->available_tuesday = $request->has('available_tuesday');
            $specialist->available_wednesday = $request->has('available_wednesday');
            $specialist->available_thursday = $request->has('available_thursday');
            $specialist->available_friday = $request->has('available_friday');
            $specialist->available_saturday = $request->has('available_saturday');
            $specialist->work_start_time = $request->work_start_time;
            $specialist->work_end_time = $request->work_end_time;
            $specialist->status = $request->status;

            // معالجة الصورة
            if ($request->hasFile('profile_image')) {
                // حذف الصورة القديمة
                if ($specialist->profile_image) {
                    Storage::disk('public')->delete($specialist->profile_image);
                }

                $imagePath = $request->file('profile_image')->store('specialists', 'public');
                $specialist->profile_image = $imagePath;
            }

            $specialist->save();

            // ربط الخدمات بالمختص
            if ($request->has('services')) {
                $specialist->services()->sync($request->services);
            } else {
                $specialist->services()->detach();
            }

            DB::commit();

            return redirect()->route('admin.specialists.index')
                ->with('success', 'تم تحديث المختص بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المختص: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف مختص محدد
     */
    public function destroy($id)
    {
        try {
            $specialist = Specialist::findOrFail($id);

            // التحقق من عدم وجود حجوزات مرتبطة بالمختص
            if ($specialist->bookings()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف المختص لأنه مرتبط بحجوزات');
            }

            // حذف الصورة المرتبطة
            if ($specialist->profile_image) {
                Storage::disk('public')->delete($specialist->profile_image);
            }

            // فصل الخدمات
            $specialist->services()->detach();

            // إزالة دور المختص من المستخدم
            $user = $specialist->user;
            $user->removeRole('specialist');

            $specialist->delete();

            return redirect()->route('admin.specialists.index')
                ->with('success', 'تم حذف المختص بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المختص: ' . $e->getMessage());
        }
    }

    /**
     * تغيير حالة المختص
     */
    public function changeStatus($id)
    {
        try {
            $specialist = Specialist::findOrFail($id);

            // تبديل الحالة بين نشط وغير نشط
            if ($specialist->status === 'active') {
                $specialist->status = 'inactive';
            } else {
                $specialist->status = 'active';
            }

            $specialist->save();

            return redirect()->back()
                ->with('success', 'تم تغيير حالة المختص بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة المختص: ' . $e->getMessage());
        }
    }

    /**
     * عرض جدول توفر المختص
     */
    public function availability($id)
    {
        $specialist = Specialist::findOrFail($id);

        // الحصول على الحجوزات المستقبلية للمختص
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30);

        $bookings = $specialist->bookings()
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        // تنظيم الحجوزات حسب التاريخ والوقت
        $bookingsByDate = [];
        foreach ($bookings as $booking) {
            $date = $booking->booking_date;
            if (!isset($bookingsByDate[$date])) {
                $bookingsByDate[$date] = [];
            }
            $bookingsByDate[$date][] = $booking;
        }

        return view('admin.specialists.availability', compact('specialist', 'bookingsByDate', 'startDate', 'endDate'));
    }

    /**
     * تحديث جدول توفر المختص
     */
    public function updateAvailability(Request $request, $id)
    {
        $request->validate([
            'available_sunday' => 'boolean',
            'available_monday' => 'boolean',
            'available_tuesday' => 'boolean',
            'available_wednesday' => 'boolean',
            'available_thursday' => 'boolean',
            'available_friday' => 'boolean',
            'available_saturday' => 'boolean',
            'work_start_time' => 'required',
            'work_end_time' => 'required|after:work_start_time',
            'break_start_time' => 'nullable',
            'break_end_time' => 'nullable|after:break_start_time',
        ], [
            'work_start_time.required' => 'يرجى تحديد وقت بدء العمل',
            'work_end_time.required' => 'يرجى تحديد وقت انتهاء العمل',
            'work_end_time.after' => 'يجب أن يكون وقت انتهاء العمل بعد وقت البدء',
            'break_end_time.after' => 'يجب أن يكون وقت انتهاء الاستراحة بعد وقت البدء',
        ]);

        try {
            $specialist = Specialist::findOrFail($id);
            $specialist->available_sunday = $request->has('available_sunday');
            $specialist->available_monday = $request->has('available_monday');
            $specialist->available_tuesday = $request->has('available_tuesday');
            $specialist->available_wednesday = $request->has('available_wednesday');
            $specialist->available_thursday = $request->has('available_thursday');
            $specialist->available_friday = $request->has('available_friday');
            $specialist->available_saturday = $request->has('available_saturday');
            $specialist->work_start_time = $request->work_start_time;
            $specialist->work_end_time = $request->work_end_time;
            $specialist->break_start_time = $request->break_start_time;
            $specialist->break_end_time = $request->break_end_time;
            $specialist->save();

            return redirect()->route('admin.specialists.availability', $specialist->id)
                ->with('success', 'تم تحديث جدول التوفر بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث جدول التوفر: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('import_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $header = $data[0];
        unset($data[0]);

        foreach ($data as $row) {
            $row = array_combine($header, $row);

            // مثال على إضافة مختص جديد (حسب أسماء الأعمدة في الملف)
            $user = \App\Models\User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'password' => \Hash::make($row['password']),
                'is_active' => true,
            ]);

            $user->assignRole('specialist');

            \App\Models\Specialist::create([
                'user_id' => $user->id,
                'specialization' => $row['specialization'] ?? 'غير محدد',
            ]);
        }

        return redirect()->back()->with('success', 'تم استيراد المختصين بنجاح');
    }
    public function downloadTemplate()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=specialists_template.csv',
        ];

        $content = "name,email,phone,specialization,is_verified,is_available\n";

        return response($content, 200, $headers);
    }
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = explode(',', $request->input('ids'));

        $specialists = \App\Models\Specialist::whereIn('id', $ids)->get();

        switch ($action) {
            case 'activate':
                foreach ($specialists as $s) {
                    $s->is_verified = true;
                    $s->save();
                }
                $msg = 'تم تفعيل المختصين بنجاح';
                break;

            case 'deactivate':
                foreach ($specialists as $s) {
                    $s->is_verified = false;
                    $s->save();
                }
                $msg = 'تم تعطيل المختصين بنجاح';
                break;

            case 'delete':
                foreach ($specialists as $s) {
                    $s->delete();
                }
                $msg = 'تم حذف المختصين بنجاح';
                break;

            default:
                return redirect()->back()->with('error', 'إجراء غير معروف');
        }

        return redirect()->route('admin.specialists.index')->with('success', $msg);
    }





}
