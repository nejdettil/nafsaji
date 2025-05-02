<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Specialist;
use App\Models\Service;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * عرض قائمة الحجوزات في لوحة الإدارة
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'specialist.user', 'service', 'session']);

        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // تصفية حسب تاريخ الحجز
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // البحث عن حجوزات مستخدم معين
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        // البحث عن حجوزات مختص معين
        if ($request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->where('specialist_id', $request->specialist_id);
        }

        // البحث عن حجوزات خدمة معينة
        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->where('service_id', $request->service_id);
        }

        // البحث بواسطة حالة الدفع
        if ($request->has('is_paid') && $request->is_paid !== null) {
            $query->where('is_paid', $request->is_paid == '1');
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'booking_date');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $bookings = $query->paginate($request->input('per_page', 15));

        // الحصول على قوائم للفلاتر
        $users = User::select('id', 'name')->get();
        $specialists = Specialist::with('user:id,name')->get();
        $services = Service::select('id', 'name')->get();
        $statuses = Booking::distinct()->pluck('status');

        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        // تجهيز الأحداث للكاليندر
        $calendarEvents = Booking::where('status', 'confirmed')
            ->get()
            ->map(function ($booking) {
                return [
                    'title' => $booking->service->name ?? 'جلسة',
                    'start' => $booking->booking_date,
                    'url' => route('admin.bookings.show', $booking->id),
                ];
            });

        return view('admin.bookings.index', compact(
            'bookings',
            'users',
            'specialists',
            'services',
            'statuses',
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'calendarEvents'
        ));
    }


    /**
     * عرض نموذج إنشاء حجز جديد
     */
    public function create()
    {
        $users = User::where('status', 'active')->get();
        $specialists = Specialist::with('user')->where('status', 'active')->get();
        $services = Service::where('status', 'active')->get();
        
        return view('admin.bookings.create', compact('users', 'specialists', 'services'));
    }

    /**
     * حفظ حجز جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialist_id' => 'required|exists:specialists,id',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'duration' => 'required|integer|min:15',
            'notes' => 'nullable|string',
        ], [
            'user_id.required' => 'يرجى اختيار المستخدم',
            'specialist_id.required' => 'يرجى اختيار المختص',
            'service_id.required' => 'يرجى اختيار الخدمة',
            'booking_date.required' => 'يرجى تحديد تاريخ الحجز',
            'booking_date.after_or_equal' => 'يجب أن يكون تاريخ الحجز اليوم أو بعده',
            'booking_time.required' => 'يرجى تحديد وقت الحجز',
            'duration.required' => 'يرجى تحديد مدة الجلسة',
            'duration.min' => 'يجب أن تكون مدة الجلسة 15 دقيقة على الأقل',
        ]);

        try {
            DB::beginTransaction();
            
            // التحقق من توفر المختص في الوقت المحدد
            $bookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
            $endDateTime = (clone $bookingDateTime)->addMinutes($request->duration);
            
            $isAvailable = $this->checkSpecialistAvailability(
                $request->specialist_id,
                $bookingDateTime,
                $endDateTime
            );
            
            if (!$isAvailable) {
                return redirect()->back()
                    ->with('error', 'المختص غير متاح في هذا الوقت')
                    ->withInput();
            }
            
            // إنشاء الحجز
            $booking = new Booking();
            $booking->user_id = $request->user_id;
            $booking->specialist_id = $request->specialist_id;
            $booking->service_id = $request->service_id;
            $booking->booking_date = $request->booking_date;
            $booking->booking_time = $request->booking_time;
            $booking->duration = $request->duration;
            $booking->notes = $request->notes;
            $booking->status = 'pending';
            $booking->is_paid = false;
            $booking->save();
            
            // إنشاء جلسة مرتبطة بالحجز
            $session = new Session();
            $session->booking_id = $booking->id;
            $session->start_time = $bookingDateTime;
            $session->end_time = $endDateTime;
            $session->status = 'scheduled';
            $session->save();
            
            DB::commit();
            
            // إرسال إشعارات
            $this->sendBookingNotifications($booking);
            
            return redirect()->route('admin.bookings.index')
                ->with('success', 'تم إنشاء الحجز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الحجز: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * التحقق من توفر المختص في الوقت المحدد
     */
    private function checkSpecialistAvailability($specialistId, $startDateTime, $endDateTime)
    {
        // التحقق من جدول توفر المختص
        $specialist = Specialist::findOrFail($specialistId);
        $dayOfWeek = strtolower($startDateTime->format('l'));
        $availabilityField = 'available_' . $dayOfWeek;
        
        if (!$specialist->$availabilityField) {
            return false;
        }
        
        // التحقق من عدم وجود حجوزات أخرى في نفس الوقت
        $conflictingBookings = Booking::where('specialist_id', $specialistId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where('booking_date', $startDateTime->format('Y-m-d'))
                      ->whereRaw("TIME(CONCAT(booking_time, ':00')) < ?", [$endDateTime->format('H:i:s')])
                      ->whereRaw("ADDTIME(TIME(CONCAT(booking_time, ':00')), SEC_TO_TIME(duration * 60)) > ?", [$startDateTime->format('H:i:s')]);
                });
            })
            ->count();
        
        return $conflictingBookings === 0;
    }

    /**
     * عرض تفاصيل حجز محدد
     */
    public function show($id)
    {
        $booking = Booking::with([
            'user', 
            'specialist.user', 
            'service', 
            'session',
            'payment'
        ])->findOrFail($id);

        // سجل النشاط للحجز
        $activityLog = DB::table('activity_log')
            ->where('subject_type', 'App\Models\Booking')
            ->where('subject_id', $booking->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bookings.show', compact('booking', 'activityLog'));
    }

    /**
     * عرض نموذج تعديل حجز
     */
    public function edit($id)
    {
        $booking = Booking::with(['user', 'specialist', 'service', 'session'])->findOrFail($id);
        $users = User::where('status', 'active')->get();
        $specialists = Specialist::with('user')->where('status', 'active')->get();
        $services = Service::where('status', 'active')->get();
        
        $statuses = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر'
        ];
        
        return view('admin.bookings.edit', compact('booking', 'users', 'specialists', 'services', 'statuses'));
    }

    /**
     * تحديث حجز محدد
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'duration' => 'required|integer|min:15',
            'notes' => 'nullable|string',
        ], [
            'status.required' => 'يرجى تحديد حالة الحجز',
            'booking_date.required' => 'يرجى تحديد تاريخ الحجز',
            'booking_time.required' => 'يرجى تحديد وقت الحجز',
            'duration.required' => 'يرجى تحديد مدة الجلسة',
            'duration.min' => 'يجب أن تكون مدة الجلسة 15 دقيقة على الأقل',
        ]);

        try {
            DB::beginTransaction();
            
            $booking = Booking::findOrFail($id);
            $oldStatus = $booking->status;
            
            // التحقق من توفر المختص في حالة تغيير الموعد
            if ($request->booking_date != $booking->booking_date || $request->booking_time != $booking->booking_time || $request->duration != $booking->duration) {
                $bookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
                $endDateTime = (clone $bookingDateTime)->addMinutes($request->duration);
                
                $isAvailable = $this->checkSpecialistAvailabilityExcludingCurrentBooking(
                    $booking->specialist_id,
                    $bookingDateTime,
                    $endDateTime,
                    $id
                );
                
                if (!$isAvailable) {
                    return redirect()->back()
                        ->with('error', 'المختص غير متاح في هذا الوقت')
                        ->withInput();
                }
            }
            
            // تحديث الحجز
            $booking->booking_date = $request->booking_date;
            $booking->booking_time = $request->booking_time;
            $booking->duration = $request->duration;
            $booking->notes = $request->notes;
            $booking->status = $request->status;
            $booking->save();
            
            // تحديث الجلسة المرتبطة
            $bookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
            $endDateTime = (clone $bookingDateTime)->addMinutes($request->duration);
            
            $session = Session::where('booking_id', $booking->id)->first();
            if ($session) {
                $session->start_time = $bookingDateTime;
                $session->end_time = $endDateTime;
                
                // تحديث حالة الجلسة بناءً على حالة الحجز
                if ($request->status == 'completed') {
                    $session->status = 'completed';
                } elseif ($request->status == 'cancelled') {
                    $session->status = 'cancelled';
                } elseif ($request->status == 'no_show') {
                    $session->status = 'no_show';
                } else {
                    $session->status = 'scheduled';
                }
                
                $session->save();
            }
            
            DB::commit();
            
            // إرسال إشعارات إذا تغيرت الحالة
            if ($oldStatus != $request->status) {
                $this->sendBookingStatusChangeNotifications($booking, $oldStatus);
            }
            
            return redirect()->route('admin.bookings.index')
                ->with('success', 'تم تحديث الحجز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الحجز: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * التحقق من توفر المختص في الوقت المحدد مع استثناء الحجز الحالي
     */
    private function checkSpecialistAvailabilityExcludingCurrentBooking($specialistId, $startDateTime, $endDateTime, $currentBookingId)
    {
        // التحقق من جدول توفر المختص
        $specialist = Specialist::findOrFail($specialistId);
        $dayOfWeek = strtolower($startDateTime->format('l'));
        $availabilityField = 'available_' . $dayOfWeek;
        
        if (!$specialist->$availabilityField) {
            return false;
        }
        
        // التحقق من عدم وجود حجوزات أخرى في نفس الوقت
        $conflictingBookings = Booking::where('specialist_id', $specialistId)
            ->where('id', '!=', $currentBookingId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where('booking_date', $startDateTime->format('Y-m-d'))
                      ->whereRaw("TIME(CONCAT(booking_time, ':00')) < ?", [$endDateTime->format('H:i:s')])
                      ->whereRaw("ADDTIME(TIME(CONCAT(booking_time, ':00')), SEC_TO_TIME(duration * 60)) > ?", [$startDateTime->format('H:i:s')]);
                });
            })
            ->count();
        
        return $conflictingBookings === 0;
    }

    /**
     * حذف حجز محدد
     */
    public function destroy($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            // التحقق من عدم وجود مدفوعات مرتبطة
            if ($booking->payment && $booking->payment->status == 'completed') {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الحجز لأنه مرتبط بمدفوعات مكتملة');
            }
            
            // حذف الجلسة المرتبطة
            Session::where('booking_id', $booking->id)->delete();
            
            // حذف الحجز
            $booking->delete();
            
            return redirect()->route('admin.bookings.index')
                ->with('success', 'تم حذف الحجز بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الحجز: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات الحجز
     */
    private function sendBookingNotifications($booking)
    {
        try {
            $user = $booking->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            $user->notify(new \App\Notifications\BookingCreated($booking));
            
            // إشعار للمختص
            $specialist->user->notify(new \App\Notifications\SpecialistBookingCreated($booking));
            
            // إشعار للإدارة
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminBookingCreated($booking));
            }
        } catch (\Exception $e) {
            \Log::error('خطأ في إرسال إشعارات الحجز: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات تغيير حالة الحجز
     */
    private function sendBookingStatusChangeNotifications($booking, $oldStatus)
    {
        try {
            $user = $booking->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            $user->notify(new \App\Notifications\BookingStatusChanged($booking, $oldStatus));
            
            // إشعار للمختص
            $specialist->user->notify(new \App\Notifications\SpecialistBookingStatusChanged($booking, $oldStatus));
            
            // إشعار للإدارة
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminBookingStatusChanged($booking, $oldStatus));
            }
        } catch (\Exception $e) {
            \Log::error('خطأ في إرسال إشعارات تغيير حالة الحجز: ' . $e->getMessage());
        }
    }

    /**
     * إعادة جدولة الحجز
     */
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'duration' => 'required|integer|min:15',
        ], [
            'booking_date.required' => 'يرجى تحديد تاريخ الحجز',
            'booking_date.after_or_equal' => 'يجب أن يكون تاريخ الحجز اليوم أو بعده',
            'booking_time.required' => 'يرجى تحديد وقت الحجز',
            'duration.required' => 'يرجى تحديد مدة الجلسة',
            'duration.min' => 'يجب أن تكون مدة الجلسة 15 دقيقة على الأقل',
        ]);

        try {
            DB::beginTransaction();
            
            $booking = Booking::findOrFail($id);
            $oldDate = $booking->booking_date;
            $oldTime = $booking->booking_time;
            
            // التحقق من توفر المختص في الوقت الجديد
            $bookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
            $endDateTime = (clone $bookingDateTime)->addMinutes($request->duration);
            
            $isAvailable = $this->checkSpecialistAvailabilityExcludingCurrentBooking(
                $booking->specialist_id,
                $bookingDateTime,
                $endDateTime,
                $id
            );
            
            if (!$isAvailable) {
                return redirect()->back()
                    ->with('error', 'المختص غير متاح في هذا الوقت')
                    ->withInput();
            }
            
            // تحديث الحجز
            $booking->booking_date = $request->booking_date;
            $booking->booking_time = $request->booking_time;
            $booking->duration = $request->duration;
            $booking->status = 'confirmed'; // تأكيد الحجز بعد إعادة الجدولة
            $booking->save();
            
            // تحديث الجلسة المرتبطة
            $session = Session::where('booking_id', $booking->id)->first();
            if ($session) {
                $session->start_time = $bookingDateTime;
                $session->end_time = $endDateTime;
                $session->status = 'scheduled';
                $session->save();
            }
            
            DB::commit();
            
            // إرسال إشعارات إعادة الجدولة
            $this->sendRescheduleNotifications($booking, $oldDate, $oldTime);
            
            return redirect()->route('admin.bookings.show', $booking->id)
                ->with('success', 'تم إعادة جدولة الحجز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إعادة جدولة الحجز: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * إرسال إشعارات إعادة جدولة الحجز
     */
    private function sendRescheduleNotifications($booking, $oldDate, $oldTime)
    {
        try {
            $user = $booking->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            $user->notify(new \App\Notifications\BookingRescheduled($booking, $oldDate, $oldTime));
            
            // إشعار للمختص
            $specialist->user->notify(new \App\Notifications\SpecialistBookingRescheduled($booking, $oldDate, $oldTime));
            
            // إشعار للإدارة
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminBookingRescheduled($booking, $oldDate, $oldTime));
            }
        } catch (\Exception $e) {
            \Log::error('خطأ في إرسال إشعارات إعادة جدولة الحجز: ' . $e->getMessage());
        }
    }
    public function markAsCompleted($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'completed';
        $booking->save();

        return redirect()->back()->with('success', 'تم إكمال الجلسة بنجاح.');
    }

    /**
     * تصدير بيانات الحجوزات
     */
    public function export(Request $request)
    {
        $query = Booking::with(['user', 'specialist.user', 'service', 'session']);

        // تطبيق نفس الفلاتر المستخدمة في الصفحة الرئيسية
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->where('specialist_id', $request->specialist_id);
        }

        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->has('is_paid') && $request->is_paid !== null) {
            $query->where('is_paid', $request->is_paid == '1');
        }

        $bookings = $query->get();

        // تحديد نوع التصدير
        $exportType = $request->input('export_type', 'csv');

        switch ($exportType) {
            case 'excel':
                return (new \App\Exports\BookingsExport($bookings))->download('bookings.xlsx');
            case 'pdf':
                return (new \App\Exports\BookingsExport($bookings))->download('bookings.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            case 'csv':
            default:
                return (new \App\Exports\BookingsExport($bookings))->download('bookings.csv');
        }
    }
}
