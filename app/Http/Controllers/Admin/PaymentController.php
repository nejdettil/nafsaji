<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\User;
use App\Models\Specialist;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;

class PaymentController extends Controller
{
    /**
     * عرض قائمة المدفوعات في لوحة الإدارة
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package']);

        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // تصفية حسب طريقة الدفع
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }

        // تصفية حسب التاريخ
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // البحث عن مدفوعات مستخدم معين
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        // البحث عن مدفوعات مختص معين
        if ($request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('specialist_id', $request->specialist_id);
            });
        }

        // البحث عن مدفوعات خدمة معينة
        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        // البحث عن مدفوعات باقة معينة
        if ($request->has('package_id') && !empty($request->package_id)) {
            $query->where('package_id', $request->package_id);
        }

        // البحث بواسطة رقم المعاملة
        if ($request->has('transaction_id') && !empty($request->transaction_id)) {
            $query->where('transaction_id', 'like', '%' . $request->transaction_id . '%');
        }

        // البحث بواسطة المبلغ
        if ($request->has('amount') && !empty($request->amount)) {
            $query->where('amount', $request->amount);
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $payments = $query->paginate($request->input('per_page', 15));

        // الحصول على قوائم للفلاتر
        $users = User::select('id', 'name')->get();
        $specialists = Specialist::with('user:id,name')->get();
        $services = Service::select('id', 'name')->get();
        $paymentMethods = Payment::distinct()->pluck('payment_method');
        $statuses = Payment::distinct()->pluck('status');

        // ✅ تحضير الإحصائيات المطلوبة
        $totalPayments = Payment::sum('amount');
        $completedPayments = Payment::where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        $failedPayments = Payment::where('status', 'failed')->sum('amount');
// ⚡ تجهيز بيانات الرسم البياني
// ⚡ تجهيز بيانات الرسم البياني

// تواريخ المدفوعات (تاريخ الإنشاء)
        $paymentsChartLabels = Payment::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('date');

// مجموع المدفوعات المكتملة حسب اليوم
        $completedPaymentsData = Payment::where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total');

// مجموع المدفوعات قيد المعالجة حسب اليوم
        $pendingPaymentsData = Payment::where('status', 'pending')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total');

// مجموع المدفوعات الفاشلة حسب اليوم
        $failedPaymentsData = Payment::where('status', 'failed')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total');
// 🔥 تجهيز بيانات طرق الدفع
        $paymentMethodsData = Payment::select('payment_method', DB::raw('count(*) as total'))
            ->groupBy('payment_method')
            ->pluck('total');

// 🔥 تجهيز بيانات حالات المدفوعات
        $paymentStatusData = Payment::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total');

        return view('admin.payments.index', compact(
            'payments',
            'users',
            'specialists',
            'services',
            'paymentMethods',
            'statuses',
            'totalPayments',
            'completedPayments',
            'pendingPayments',
            'failedPayments',
            'paymentsChartLabels',
            'completedPaymentsData',
            'pendingPaymentsData',
            'failedPaymentsData',
            'paymentMethodsData',
            'paymentStatusData'   // ✅ أضفه هنا
        ));






    }






    /**
     * عرض تفاصيل دفعة محددة
     */
    public function show($id)
    {
        $payment = Payment::with([
            'user', 
            'booking.specialist.user', 
            'booking.service', 
            'package',
            'booking.session'
        ])->findOrFail($id);

        // سجل النشاط للدفعة
        $activityLog = DB::table('activity_log')
            ->where('subject_type', 'App\Models\Payment')
            ->where('subject_id', $payment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payments.show', compact('payment', 'activityLog'));
    }

    /**
     * عرض نموذج تعديل حالة الدفع
     */
    public function edit($id)
    {
        $payment = Payment::with([
            'user', 
            'booking.specialist.user', 
            'booking.service', 
            'package'
        ])->findOrFail($id);

        $statuses = [
            'pending' => 'قيد الانتظار',
            'pending_verification' => 'في انتظار التحقق',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'refunded' => 'مسترجع'
        ];

        return view('admin.payments.edit', compact('payment', 'statuses'));
    }

    /**
     * تحديث حالة الدفع
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,pending_verification,completed,failed,refunded',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'status.required' => 'يرجى تحديد حالة الدفع',
            'status.in' => 'حالة الدفع غير صالحة'
        ]);

        try {
            $payment = Payment::findOrFail($id);
            
            // حفظ الحالة القديمة للإشعارات
            $oldStatus = $payment->status;
            
            $payment->status = $request->status;
            
            if ($request->has('transaction_id') && !empty($request->transaction_id)) {
                $payment->transaction_id = $request->transaction_id;
            }
            
            if ($request->has('notes')) {
                $payment->notes = $request->notes;
            }
            
            $payment->save();

            // إذا كان الدفع مرتبط بحجز، قم بتحديث حالة الحجز
            if ($payment->booking_id) {
                $booking = $payment->booking;
                
                if ($request->status === 'completed' && $oldStatus !== 'completed') {
                    $booking->is_paid = true;
                    $booking->status = 'confirmed';
                    $booking->save();
                    
                    // إرسال إشعارات
                    $this->sendPaymentCompletedNotifications($payment);
                } elseif ($request->status === 'refunded' && $oldStatus !== 'refunded') {
                    $booking->is_paid = false;
                    $booking->status = 'cancelled';
                    $booking->save();
                    
                    // إرسال إشعارات
                    $this->sendPaymentRefundedNotifications($payment);
                } elseif ($request->status !== 'completed' && $oldStatus === 'completed') {
                    $booking->is_paid = false;
                    $booking->status = 'pending';
                    $booking->save();
                }
            }

            // تسجيل النشاط
            activity()
                ->performedOn($payment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'notes' => $payment->notes
                ])
                ->log('تم تحديث حالة الدفع');

            return redirect()->route('admin.payments.index')
                ->with('success', 'تم تحديث حالة الدفع بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث حالة الدفع: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث حالة الدفع: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * إرسال إشعارات اكتمال الدفع
     */
    private function sendPaymentCompletedNotifications($payment)
    {
        try {
            $booking = $payment->booking;
            $user = $payment->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            $user->notify(new \App\Notifications\PaymentCompleted($payment));
            
            // إشعار للمختص
            $specialist->user->notify(new \App\Notifications\SpecialistBookingPaid($booking));
            
            // إشعار للإدارة
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminPaymentCompleted($payment));
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات اكتمال الدفع: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات استرجاع الدفع
     */
    private function sendPaymentRefundedNotifications($payment)
    {
        try {
            $booking = $payment->booking;
            $user = $payment->user;
            $specialist = $booking->specialist;
            
            // إشعار للمستخدم
            $user->notify(new \App\Notifications\PaymentRefunded($payment));
            
            // إشعار للمختص
            $specialist->user->notify(new \App\Notifications\SpecialistBookingRefunded($booking));
            
            // إشعار للإدارة
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminPaymentRefunded($payment));
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات استرجاع الدفع: ' . $e->getMessage());
        }
    }

    /**
     * عرض تقارير المدفوعات
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
        
        // إجمالي المدفوعات
        $totalPayments = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // إجمالي الإيرادات
        $totalRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        // المدفوعات حسب الطريقة
        $paymentsByMethod = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->get();
        
        // المدفوعات حسب الحالة
        $paymentsByStatus = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('status')
            ->get();
        
        // المدفوعات حسب اليوم/الأسبوع/الشهر
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
        
        $paymentsOverTime = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw("DATE_FORMAT(created_at, '$format') as period"), DB::raw('sum(amount) as total'))
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        // أعلى المستخدمين إنفاقاً
        $topUsers = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', DB::raw('sum(amount) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->with('user:id,name')
            ->get();
        
        // أعلى المختصين دخلاً
        $topSpecialists = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('booking_id')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->select('bookings.specialist_id', DB::raw('sum(payments.amount) as total'))
            ->groupBy('bookings.specialist_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // الحصول على بيانات المختصين
        $specialistIds = $topSpecialists->pluck('specialist_id')->toArray();
        $specialists = Specialist::whereIn('id', $specialistIds)->with('user:id,name')->get()->keyBy('id');
        
        // إضافة بيانات المختصين إلى النتائج
        $topSpecialists->map(function ($item) use ($specialists) {
            $item->specialist = $specialists[$item->specialist_id] ?? null;
            return $item;
        });
        
        return view('admin.payments.reports', compact(
            'period',
            'startDate',
            'endDate',
            'totalPayments',
            'totalRevenue',
            'paymentsByMethod',
            'paymentsByStatus',
            'paymentsOverTime',
            'topUsers',
            'topSpecialists',
            'groupBy'
        ));
    }

    /**
     * تصدير بيانات المدفوعات
     */
    public function export(Request $request)
    {
        $query = Payment::query();

        // تطبيق الفلاتر
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('specialist_id')) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('specialist_id', $request->specialist_id);
            });
        }

        if ($request->filled('service_id')) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', 'like', '%' . $request->transaction_id . '%');
        }

        if ($request->filled('amount')) {
            $query->where('amount', $request->amount);
        }

        // جلب كل النتائج
        $payments = $query->get();

        // تحديد نوع التصدير المطلوب
        $exportType = $request->input('export_type', 'xlsx');

        $fileName = 'payments_' . now()->format('Y-m-d_H-i-s');

        switch ($exportType) {
            case 'csv':
                return Excel::download(new PaymentsExport($payments), "$fileName.csv", \Maatwebsite\Excel\Excel::CSV);
            case 'pdf':
                return Excel::download(new PaymentsExport($payments), "$fileName.pdf", \Maatwebsite\Excel\Excel::DOMPDF);
            case 'xlsx':
            default:
                return Excel::download(new PaymentsExport($payments), "$fileName.xlsx");
        }
    }

}
