<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Specialist;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * عرض صفحة التقارير الرئيسية
     */
    public function reportsIndex()
    {
        // إحصائيات المستخدمين
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        
        // إحصائيات المختصين
        $totalSpecialists = Specialist::count();
        $activeSpecialists = Specialist::where('status', 'active')->count();
        
        // إحصائيات الحجوزات
        $totalBookings = Booking::count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        
        // إحصائيات الإيرادات
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $revenueThisMonth = Payment::where('status', 'completed')
                                  ->where('created_at', '>=', now()->startOfMonth())
                                  ->sum('amount');
        
        // إحصائيات حسب الشهر للسنة الحالية
        $monthlyRevenue = Payment::where('status', 'completed')
                                ->whereYear('created_at', now()->year)
                                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
                                ->groupBy('month')
                                ->get();
        
        $monthlyBookings = Booking::whereYear('created_at', now()->year)
                                 ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                                 ->groupBy('month')
                                 ->get();
        
        return view('admin.reports.index', compact(
            'totalUsers',
            'newUsersThisMonth',
            'totalSpecialists',
            'activeSpecialists',
            'totalBookings',
            'completedBookings',
            'pendingBookings',
            'cancelledBookings',
            'totalRevenue',
            'revenueThisMonth',
            'monthlyRevenue',
            'monthlyBookings'
        ));
    }
    
    /**
     * عرض لوحة التحكم الرئيسية للإدارة
     */
    public function index()
    {
        // إحصائيات المستخدمين
        $totalUsers = User::count();
        $totalSpecialists = Specialist::count();
        
        // حساب نسبة التغيير في المستخدمين
        $lastMonthUsers = User::where('created_at', '<', now()->subMonth())->count();
        $userChange = $lastMonthUsers > 0 ? (($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 100;
        
        // حساب نسبة التغيير في المختصين
        $lastMonthSpecialists = Specialist::where('created_at', '<', now()->subMonth())->count();
        $specialistChange = $lastMonthSpecialists > 0 ? (($totalSpecialists - $lastMonthSpecialists) / $lastMonthSpecialists) * 100 : 100;
        
        // إحصائيات الحجوزات
        $totalBookings = Booking::count();
        $lastMonthBookings = Booking::where('created_at', '<', now()->subMonth())->count();
        $bookingChange = $lastMonthBookings > 0 ? (($totalBookings - $lastMonthBookings) / $lastMonthBookings) * 100 : 100;
        
        // إحصائيات الإيرادات
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $lastMonthRevenue = Payment::where('status', 'completed')
                                  ->where('created_at', '<', now()->subMonth())
                                  ->sum('amount');
        $revenueChange = $lastMonthRevenue > 0 ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 100;
        
        // أحدث الحجوزات
        $latestBookings = Booking::with(['user', 'specialist.user', 'service'])
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
        
        // أحدث المدفوعات
        $latestPayments = Payment::with('user')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
        
        // أحدث الإشعارات
        $notifications = auth()->user()->notifications()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalSpecialists',
            'userChange',
            'specialistChange',
            'totalBookings',
            'bookingChange',
            'totalRevenue',
            'revenueChange',
            'latestBookings',
            'latestPayments',
            'notifications'
        ));
    }
}
