<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * عرض لوحة التحكم الرئيسية للمدير
     */
    public function index()
    {
        // إحصائيات عامة
        $usersCount = User::where('role', 'user')->count();
        $specialistsCount = User::where('role', 'specialist')->count();
        $totalServices = Service::count();
        $bookingsCount = Booking::count();
        $paymentsTotal = Payment::sum('amount');
        
        // أكثر الخدمات حجزاً
        $topBookedServices = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(*) as booking_count'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('booking_count', 'desc')
            ->limit(5)
            ->get();
        
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
            ->limit(6)
            ->get();
            
        // أحدث الحجوزات
        $latestBookings = Booking::with(['user', 'specialist'])->latest()->limit(5)->get();
        
        // أحدث المستخدمين
        $latestUsers = User::where('role', 'user')->latest()->limit(5)->get();
        
        return view('admin.dashboard', compact(
            'usersCount',
            'specialistsCount',
            'totalServices',
            'bookingsCount',
            'paymentsTotal',
            'topBookedServices',
            'bookingsByStatus',
            'monthlyPayments',
            'latestBookings',
            'latestUsers'
        ));
    }
}
