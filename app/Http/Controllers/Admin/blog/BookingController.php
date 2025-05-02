<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Specialist;
use App\Models\Service;
use App\Models\Session;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * عرض قائمة الحجوزات في لوحة الإدارة
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'specialist.user', 'service', 'payment']);

        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // تصفية حسب التاريخ
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

        // البحث بواسطة رقم الحجز
        if ($request->has('booking_number') && !empty($request->booking_number)) {
            $query->where('booking_number', 'like', '%' . $request->booking_number . '%');
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

        return view('admin.bookings.index', compact(
            'bookings', 
            'users', 
            'specialists', 
            'services', 
            'statuses'
        ));
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
            'payment',
            'session',
            'coupon'
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
     * عرض نموذج تعديل حالة الحجز
     */
    public function edit($id)
    {
        $booking = Booking::with([
            'user', 
            'specialist.user', 
            'service', 
            'payment'
        ])->findOrFail($id);

        $statuses = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر',
            'rescheduled' => 'معاد جدولته',
            'payment_failed' => 'فشل الدفع'
        ];

        return view('admin.bookings.edit', compact('booking', 'statuses'));
    }

    /**
     * تحديث حالة الحجز
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show,rescheduled,payment_failed',
            'notes' => 'nullable|string',
        ], [
            'status.required' => 'يرجى تحديد حالة الحجز',
            'status.in' => 'حالة الحجز غير صالحة'
        ]);

        try {
            $booking = Booking::findOrFail($id);
            
            // حفظ الحالة القديمة للإشعارات
            $oldStatus = $booking->status;
            
            $booking->status = $request->status;
            
            if ($request->has('notes')) {
                $booking->admin_notes = $request->notes;
            }
            
            $booking->save();

            // تحديث حالة الجلسة إذا كانت موجودة
            if ($booking->session) {
                $session = $booking->session;
                
                if ($request->status === 'completed' && $oldStatus !== 'completed') {
                    $session->status = 'completed';
                    $session->save();
                } elseif ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
                    $session->status = 'cancelled';
                    $session->save();
                } elseif ($request->status === 'no_show' && $oldStatus !== 'no_show') {
                    $session->status = 'no_show';
                    $session->save();
                }
            }

            // إرسال إشعارات
            $this->sendBookingStatusUpdateNotifications($booking, $oldStatus);

            // تسجيل النشاط
            activity()
                ->performedOn($booking)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $booking->status,
                    'notes' => $booking->admin_notes
                ])
                ->log('تم تحديث حالة الحجز');

            return redirect()->route('admin.bookings.index')
                ->with('success', 'تم تحديث حالة الحجز بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث حالة الحجز: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث حالة الحجز: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * إرسال إشعارات تحديث حالة الحجز
     */
    private function sendBookingStatusUpdateNotifications($booking, $oldStatus)
    {
        try {
            $user = $booking->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'تم تحديث حالة الحجز',
                'content' => 'تم تحديث حالة حجزك رقم ' . $booking->booking_number . ' إلى ' . $this->getStatusInArabic($booking->status),
                'type' => 'booking',
                'is_read' => false,
                'link' => route('user.bookings.show', $booking->id),
            ]);
            
            // إشعار للمختص
            \App\Models\Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'تم تحديث حالة الحجز',
                'content' => 'تم تحديث حالة الحجز رقم ' . $booking->booking_number . ' إلى ' . $this->getStatusInArabic($booking->status),
                'type' => 'booking',
                'is_read' => false,
                'link' => route('specialist.bookings.show', $booking->id),
            ]);
            
            // إرسال بريد إلكتروني للمستخدم
            // \Mail::to($user->email)->send(new \App\Mail\BookingStatusUpdated($booking));
            
            // إرسال بريد إلكتروني للمختص
            // \Mail::to($specialist->user->email)->send(new \App\Mail\BookingStatusUpdated($booking));
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات تحديث حالة الحجز: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على حالة الحجز باللغة العربية
     */
    private function getStatusInArabic($status)
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر',
            'rescheduled' => 'معاد جدولته',
            'payment_failed' => 'فشل الدفع'
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * إعادة جدولة الحجز
     */
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'booking_date' => 'required|date|after:now',
            'booking_time' => 'required',
            'notes' => 'nullable|string',
        ], [
            'booking_date.required' => 'يرجى تحديد تاريخ الحجز',
            'booking_date.date' => 'تاريخ الحجز غير صالح',
            'booking_date.after' => 'يجب أن يكون تاريخ الحجز بعد الوقت الحالي',
            'booking_time.required' => 'يرجى تحديد وقت الحجز',
        ]);

        try {
            $booking = Booking::findOrFail($id);
            
            // حفظ التاريخ القديم للإشعارات
            $oldDate = $booking->booking_date;
            
            // تحديث تاريخ ووقت الحجز
            $newDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
            $booking->booking_date = $newDateTime;
            $booking->status = 'rescheduled';
            
            if ($request->has('notes')) {
                $booking->admin_notes = ($booking->admin_notes ? $booking->admin_notes . "\n" : '') . 
                    'تمت إعادة الجدولة: ' . $request->notes;
            }
            
            $booking->save();

            // تحديث الجلسة إذا كانت موجودة
            if ($booking->session) {
                $session = $booking->session;
                $session->session_date = $newDateTime;
                $session->status = 'rescheduled';
                $session->save();
            }

            // إرسال إشعارات
            $this->sendBookingRescheduledNotifications($booking, $oldDate);

            // تسجيل النشاط
            activity()
                ->performedOn($booking)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_date' => $oldDate,
                    'new_date' => $booking->booking_date,
                    'notes' => $request->notes
                ])
                ->log('تمت إعادة جدولة الحجز');

            return redirect()->route('admin.bookings.show', $booking->id)
                ->with('success', 'تمت إعادة جدولة الحجز بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إعادة جدولة الحجز: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إعادة جدولة الحجز: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * إرسال إشعارات إعادة جدولة الحجز
     */
    private function sendBookingRescheduledNotifications($booking, $oldDate)
    {
        try {
            $user = $booking->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'تمت إعادة جدولة الحجز',
                'content' => 'تمت إعادة جدولة حجزك رقم ' . $booking->booking_number . ' من ' . 
                    $oldDate->format('d/m/Y H:i') . ' إلى ' . $booking->booking_date->format('d/m/Y H:i'),
                'type' => 'booking',
                'is_read' => false,
                'link' => route('user.bookings.show', $booking->id),
            ]);
            
            // إشعار للمختص
            \App\Models\Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'تمت إعادة جدولة الحجز',
                'content' => 'تمت إعادة جدولة الحجز رقم ' . $booking->booking_number . ' من ' . 
                    $oldDate->format('d/m/Y H:i') . ' إلى ' . $booking->booking_date->format('d/m/Y H:i'),
                'type' => 'booking',
                'is_read' => false,
                'link' => route('specialist.bookings.show', $booking->id),
            ]);
            
            // إرسال بريد إلكتروني للمستخدم
            // \Mail::to($user->email)->send(new \App\Mail\BookingRescheduled($booking, $oldDate));
            
            // إرسال بريد إلكتروني للمختص
            // \Mail::to($specialist->user->email)->send(new \App\Mail\BookingRescheduled($booking, $oldDate));
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إعادة جدولة الحجز: ' . $e->getMessage());
        }
    }

    /**
     * إلغاء الحجز
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
            'refund_amount' => 'nullable|numeric|min:0',
        ], [
            'cancellation_reason.required' => 'يرجى تحديد سبب الإلغاء',
        ]);

        try {
            $booking = Booking::findOrFail($id);
            
            // التحقق من أن الحجز ليس ملغياً بالفعل
            if ($booking->status === 'cancelled') {
                return redirect()->back()->with('info', 'الحجز ملغي بالفعل');
            }
            
            // حفظ الحالة القديمة للإشعارات
            $oldStatus = $booking->status;
            
            $booking->status = 'cancelled';
            $booking->cancellation_reason = $request->cancellation_reason;
            $booking->cancelled_at = now();
            $booking->cancelled_by = auth()->id();
            
            if ($request->has('notes')) {
                $booking->admin_notes = ($booking->admin_notes ? $booking->admin_notes . "\n" : '') . 
                    'سبب الإلغاء: ' . $request->cancellation_reason;
            }
            
            $booking->save();

            // تحديث الجلسة إذا كانت موجودة
            if ($booking->session) {
                $session = $booking->session;
                $session->status = 'cancelled';
                $session->save();
            }

            // معالجة استرداد المبلغ إذا كان مطلوباً
            if ($request->has('refund_amount') && $request->refund_amount > 0 && $booking->payment && $booking->payment->status === 'completed') {
                $payment = $booking->payment;
                
                // إنشاء معاملة استرداد
                $refund = Payment::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'amount' => -1 * $request->refund_amount, // قيمة سالبة للإشارة إلى الاسترداد
                    'payment_method' => $payment->payment_method,
                    'status' => 'completed',
                    'transaction_id' => 'RF-' . substr($payment->transaction_id, 3), // استخدام نفس رقم المعاملة مع بادئة RF
                    'notes' => 'استرداد مبلغ للحجز الملغي: ' . $request->cancellation_reason,
                ]);
                
                // تحديث حالة الدفع الأصلي
                $payment->update(['status' => 'refunded']);
            }

            // إرسال إشعارات
            $this->sendBookingCancelledNotifications($booking);

            // تسجيل النشاط
            activity()
                ->performedOn($booking)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'cancellation_reason' => $request->cancellation_reason,
                    'refund_amount' => $request->refund_amount ?? 0
                ])
                ->log('تم إلغاء الحجز');

            return redirect()->route('admin.bookings.index')
                ->with('success', 'تم إلغاء الحجز بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إلغاء الحجز: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إلغاء الحجز: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * إرسال إشعارات إلغاء الحجز
     */
    private function sendBookingCancelledNotifications($booking)
    {
        try {
            $user = $booking->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'تم إلغاء الحجز',
                'content' => 'تم إلغاء حجزك رقم ' . $booking->booking_number . ' بسبب: ' . $booking->cancellation_reason,
                'type' => 'booking',
                'is_read' => false,
                'link' => route('user.bookings.show', $booking->id),
            ]);
            
            // إشعار للمختص
            \App\Models\Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'تم إلغاء الحجز',
                'content' => 'تم إلغاء الحجز رقم ' . $booking->booking_number . ' بسبب: ' . $booking->cancellation_reason,
                'type' => 'booking',
                'is_read' => false,
                'link' => route('specialist.bookings.show', $booking->id),
            ]);
            
            // إرسال بريد إلكتروني للمستخدم
            // \Mail::to($user->email)->send(new \App\Mail\BookingCancelled($booking));
            
            // إرسال بريد إلكتروني للمختص
            // \Mail::to($specialist->user->email)->send(new \App\Mail\BookingCancelled($booking));
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إلغاء الحجز: ' . $e->getMessage());
        }
    }

    /**
     * عرض تقارير الحجوزات
     */
    public function reports(Request $request)
    {
        // تحديد الفترة الزمنية
        $period = $request->input('period', 'month');
        $startDate = null;
        $endDate = Carbon::now();
        
        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'quarter':
                $startDate = Carbon::now()->subMonths(3);
                break;
            case 'year':
                $startDate = Carbon::now()->subYear();
                break;
            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
                break;
        }
        
        // إجمالي الحجوزات
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // الحجوزات حسب الحالة
        $bookingsByStatus = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        // الحجوزات حسب الخدمة
        $bookingsByService = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('service_id', DB::raw('count(*) as count'))
            ->groupBy('service_id')
            ->with('service:id,name')
            ->get();
        
        // الحجوزات حسب المختص
        $bookingsBySpecialist = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('specialist_id', DB::raw('count(*) as count'))
            ->groupBy('specialist_id')
            ->with('specialist.user:id,name')
            ->get();
        
        // الحجوزات حسب اليوم/الأسبوع/الشهر
        $groupBy = 'day';
        $format = '%Y-%m-%d';
        
        if ($period == 'quarter' || $period == 'year') {
            $groupBy = 'week';
            $format = '%Y-%u'; // Year and week number
        }
        
        if ($period == 'year' && $startDate->diffInDays($endDate) > 180) {
            $groupBy = 'month';
            $format = '%Y-%m';
        }
        
        $bookingsOverTime = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw("DATE_FORMAT(created_at, '$format') as period"), DB::raw('count(*) as count'))
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        // أكثر المستخدمين حجزاً
        $topUsers = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->with('user:id,name')
            ->get();
        
        // أكثر المختصين حجزاً
        $topSpecialists = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('specialist_id', DB::raw('count(*) as count'))
            ->groupBy('specialist_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->with('specialist.user:id,name')
            ->get();
        
        // معدل إكمال الحجوزات
        $completedBookings = Booking::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $completionRate = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;
        
        // معدل إلغاء الحجوزات
        $cancelledBookings = Booking::where('status', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;
        
        return view('admin.bookings.reports', compact(
            'period',
            'startDate',
            'endDate',
            'totalBookings',
            'bookingsByStatus',
            'bookingsByService',
            'bookingsBySpecialist',
            'bookingsOverTime',
            'topUsers',
            'topSpecialists',
            'completionRate',
            'cancellationRate',
            'groupBy'
        ));
    }

    /**
     * تصدير بيانات الحجوزات
     */
    public function export(Request $request)
    {
        $query = Booking::with(['user', 'specialist.user', 'service', 'payment']);

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
