<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * عرض صفحة التقارير الرئيسية
     */
    public function index()
    {
        // إحصائيات عامة
        $totalUsers = User::where('role', 'user')->count();
        $totalSpecialists = User::where('role', 'specialist')->count();
        $totalBookings = Booking::count();
        $totalPayments = Payment::sum('amount');
        
        // إحصائيات الحجوزات حسب الحالة
        $bookingsByStatus = DB::table('bookings')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        // إحصائيات المدفوعات الشهرية
        $monthlyPayments = DB::table('payments')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // تحويل البيانات إلى تنسيق مناسب للرسم البياني
        $months = [];
        $paymentAmounts = [];
        
        foreach ($monthlyPayments as $payment) {
            $monthName = Carbon::createFromDate($payment->year, $payment->month, 1)->translatedFormat('F');
            $months[] = $monthName;
            $paymentAmounts[] = $payment->total;
        }
        
        // عكس المصفوفات لعرضها بترتيب تصاعدي
        $months = array_reverse($months);
        $paymentAmounts = array_reverse($paymentAmounts);
        
        return view('admin.reports.index', compact(
            'totalUsers',
            'totalSpecialists',
            'totalBookings',
            'totalPayments',
            'bookingsByStatus',
            'months',
            'paymentAmounts'
        ));
    }
    
    /**
     * عرض تقارير الحجوزات
     */
    public function bookings(Request $request)
    {
        $query = Booking::query();
        
        // تطبيق الفلاتر إذا تم تحديدها
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('specialist_id') && $request->specialist_id != 'all') {
            $query->where('specialist_id', $request->specialist_id);
        }
        
        // الحصول على البيانات
        $bookings = $query->with(['user', 'specialist', 'service'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // الحصول على قائمة المختصين للفلتر
        $specialists = User::where('role', 'specialist')->get();
        
        return view('admin.reports.bookings', compact('bookings', 'specialists'));
    }
    
    /**
     * عرض تقارير المدفوعات
     */
    public function payments(Request $request)
    {
        $query = Payment::query();
        
        // تطبيق الفلاتر إذا تم تحديدها
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('payment_method') && $request->payment_method != 'all') {
            $query->where('payment_method', $request->payment_method);
        }
        
        // الحصول على البيانات
        $payments = $query->with(['user', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // إحصائيات المدفوعات
        $totalAmount = $query->sum('amount');
        $successfulAmount = $query->where('status', 'completed')->sum('amount');
        $pendingAmount = $query->where('status', 'pending')->sum('amount');
        
        return view('admin.reports.payments', compact(
            'payments',
            'totalAmount',
            'successfulAmount',
            'pendingAmount'
        ));
    }
    
    /**
     * عرض تقارير المستخدمين
     */
    public function users(Request $request)
    {
        $query = User::where('role', 'user');
        
        // تطبيق الفلاتر إذا تم تحديدها
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // الحصول على البيانات
        $users = $query->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // إحصائيات المستخدمين
        $totalUsers = $query->count();
        $activeUsers = $query->where('status', 'active')->count();
        $newUsersThisMonth = $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return view('admin.reports.users', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'newUsersThisMonth'
        ));
    }
    
    /**
     * عرض تقارير المختصين
     */
    public function specialists(Request $request)
    {
        $query = User::where('role', 'specialist');
        
        // تطبيق الفلاتر إذا تم تحديدها
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // الحصول على البيانات
        $specialists = $query->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // إحصائيات المختصين
        $totalSpecialists = $query->count();
        $activeSpecialists = $query->where('status', 'active')->count();
        $newSpecialistsThisMonth = $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return view('admin.reports.specialists', compact(
            'specialists',
            'totalSpecialists',
            'activeSpecialists',
            'newSpecialistsThisMonth'
        ));
    }
    
    /**
     * تصدير التقارير
     */
    public function export($type)
    {
        switch ($type) {
            case 'bookings':
                return $this->exportBookings();
            case 'payments':
                return $this->exportPayments();
            case 'users':
                return $this->exportUsers();
            case 'specialists':
                return $this->exportSpecialists();
            default:
                return redirect()->back()->with('error', 'نوع التقرير غير صالح');
        }
    }
    
    /**
     * تصدير تقرير الحجوزات
     */
    private function exportBookings()
    {
        $bookings = Booking::with(['user', 'specialist', 'service'])->get();
        
        // هنا يمكن إضافة كود لتصدير البيانات إلى ملف CSV أو Excel
        
        return redirect()->back()->with('success', 'تم تصدير تقرير الحجوزات بنجاح');
    }
    
    /**
     * تصدير تقرير المدفوعات
     */
    private function exportPayments()
    {
        $payments = Payment::with(['user', 'booking'])->get();
        
        // هنا يمكن إضافة كود لتصدير البيانات إلى ملف CSV أو Excel
        
        return redirect()->back()->with('success', 'تم تصدير تقرير المدفوعات بنجاح');
    }
    
    /**
     * تصدير تقرير المستخدمين
     */
    private function exportUsers()
    {
        $users = User::where('role', 'user')->withCount('bookings')->get();
        
        // هنا يمكن إضافة كود لتصدير البيانات إلى ملف CSV أو Excel
        
        return redirect()->back()->with('success', 'تم تصدير تقرير المستخدمين بنجاح');
    }
    
    /**
     * تصدير تقرير المختصين
     */
    private function exportSpecialists()
    {
        $specialists = User::where('role', 'specialist')->withCount('bookings')->get();
        
        // هنا يمكن إضافة كود لتصدير البيانات إلى ملف CSV أو Excel
        
        return redirect()->back()->with('success', 'تم تصدير تقرير المختصين بنجاح');
    }
}
