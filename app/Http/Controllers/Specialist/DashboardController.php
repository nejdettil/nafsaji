<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Review;

class DashboardController extends Controller
{
    /**
     * عرض لوحة التحكم الرئيسية للمختص
     */
    public function index()
    {
        $specialist = Auth::user();
        
        // إحصائيات عامة
        $totalBookings = Booking::where('specialist_id', $specialist->id)->count();
        $pendingBookings = Booking::where('specialist_id', $specialist->id)
            ->where('status', 'pending')
            ->count();
        $completedBookings = Booking::where('specialist_id', $specialist->id)
            ->where('status', 'completed')
            ->count();
        
        // الحجوزات القادمة
        $upcomingBookings = Booking::where('specialist_id', $specialist->id)
            ->where('status', 'confirmed')
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date', 'asc')
            ->limit(5)
            ->get();
        
        // أكثر الخدمات حجزاً
        $topBookedServices = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->select('services.name', DB::raw('count(*) as booking_count'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('booking_count', 'desc')
            ->limit(5)
            ->get();

        $activeClients = Booking::where('specialist_id', $specialist->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->distinct('user_id')
            ->count('user_id');

        $averageRating = DB::table('reviews')
            ->where('specialist_id', $specialist->id)
            ->avg('rating');

        $monthlyIncome = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->whereMonth('payments.created_at', now()->month)
            ->whereYear('payments.created_at', now()->year)
            ->sum('payments.amount');

        $sessions = \App\Models\Session::where('specialist_id', $specialist->id)
            ->whereDate('session_date', '>=', now())
            ->orderBy('session_date', 'asc')
            ->get();

        $reviews = Review::where('specialist_id', $specialist->id)
            ->latest()
            ->take(5)
            ->get();

        $totalSessions = \App\Models\Session::where('specialist_id', $specialist->id)->count();
        // Fix: Calculate completed sessions dynamically
        $completedSessions = \App\Models\Session::where('specialist_id', $specialist->id)
            ->where('status', 'completed') // Assuming 'completed' status exists
            ->count();
        $canceledSessions = \App\Models\Session::where('specialist_id', $specialist->id)
            ->where('status', 'canceled')
            ->count();
        $completionRate = $totalSessions > 0 ? ($completedSessions / $totalSessions) * 100 : 0;
        $cancellationRate = $totalSessions > 0 ? ($canceledSessions / $totalSessions) * 100 : 0;
        
        $totalIncome = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->sum('payments.amount');

        $monthsWithIncome = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->selectRaw('YEAR(payments.created_at) as year, MONTH(payments.created_at) as month, SUM(payments.amount) as total')
            ->groupBy('year', 'month')
            ->get();

        $averageMonthlyIncome = $monthsWithIncome->count() > 0
            ? $monthsWithIncome->avg('total')
            : 0;

        // دخل هذا الشهر
        $currentMonthIncome = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->whereMonth('payments.created_at', now()->month)
            ->whereYear('payments.created_at', now()->year)
            ->sum('payments.amount');

        // دخل الشهر الماضي
        $lastMonthIncome = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->whereMonth('payments.created_at', now()->subMonth()->month)
            ->whereYear('payments.created_at', now()->subMonth()->year)
            ->sum('payments.amount');

        // نسبة النمو
        $incomeGrowth = $lastMonthIncome > 0
            ? (($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100
            : ($currentMonthIncome > 0 ? 100 : 0); // Handle division by zero if last month income is 0

        $totalClients = \App\Models\Booking::where('specialist_id', $specialist->id)
            ->distinct('user_id')
            ->count('user_id');

        $newClients = \App\Models\Booking::where('specialist_id', $specialist->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->distinct('user_id')
            ->count('user_id');

        $returningClients = \App\Models\Booking::select('user_id')
            ->where('specialist_id', $specialist->id)
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $returnRate = $totalClients > 0 ? ($returningClients / $totalClients) * 100 : 0;

        return view('specialist.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'completedBookings',
            'upcomingBookings',
            'activeClients',
            'topBookedServices',
            'monthlyIncome',
            'sessions',
            'reviews',
            'totalSessions',
            'completedSessions',
            'completionRate',
            'cancellationRate',
            'averageRating',
            'totalIncome',
            'incomeGrowth',
            'totalClients',
            'newClients',
            'returnRate',
            'averageMonthlyIncome'
        ));
    }

    /**
     * عرض قائمة الحجوزات للمختص
     */
    public function bookings()
    {
        $specialist = Auth::user();
        $bookings = Booking::where('specialist_id', $specialist->id)
            ->with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('specialist.bookings.index', compact('bookings'));
    }

    /**
     * عرض تفاصيل حجز محدد
     */
    public function showBooking($id)
    {
        $specialist = Auth::user();
        $booking = Booking::where('specialist_id', $specialist->id)
            ->where('id', $id)
            ->with(['user', 'service', 'payments'])
            ->firstOrFail();
        
        return view('specialist.bookings.show', compact('booking'));
    }

    /**
     * تحديث حالة الحجز
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $specialist = Auth::user();
        $booking = Booking::where('specialist_id', $specialist->id)
            ->where('id', $id)
            ->firstOrFail();
        
        $validated = $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $booking->update([
            'status' => $validated['status'],
            'specialist_notes' => $validated['notes'] ?? $booking->specialist_notes,
        ]);
        
        // TODO: Add notification logic for user about status change

        return redirect()->route('specialist.bookings.show', $booking->id)
            ->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    /**
     * عرض قائمة الجلسات للمختص
     */
    public function sessions()
    {
        $specialist = Auth::user();
        $sessions = Session::where('specialist_id', $specialist->id)
            ->with(['user', 'service', 'booking']) // Eager load relationships
            ->orderBy('session_date', 'desc')
            ->paginate(10);
        
        return view('specialist.sessions.index', compact('sessions'));
    }

    /**
     * عرض نموذج إنشاء جلسة جديدة
     */
    public function createSession()
    {
        $specialist = Auth::user();
        
        // الحجوزات المؤكدة التي لم يتم إنشاء جلسات لها بعد
        $availableBookings = Booking::where('specialist_id', $specialist->id)
            ->where('status', 'confirmed')
            ->whereDoesntHave('sessions')
            ->with(['user', 'service'])
            ->get();
        
        // الخدمات التي يقدمها المختص
        $services = Service::whereHas('specialists', function($query) use ($specialist) {
            $query->where('specialist_id', $specialist->id);
        })->get();
        
        return view('specialist.sessions.create', compact('availableBookings', 'services'));
    }

    /**
     * حفظ جلسة جديدة
     */
    public function storeSession(Request $request)
    {
        $specialist = Auth::user();
        
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'session_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_type' => 'required|in:in-person,video,voice',
            'notes' => 'nullable|string',
        ]);
        
        // التحقق من أن الحجز ينتمي للمختص
        $booking = Booking::where('id', $validated['booking_id'])
            ->where('specialist_id', $specialist->id)
            ->firstOrFail();
        
        // إنشاء الجلسة
        $session = new Session();
        $session->booking_id = $booking->id;
        $session->specialist_id = $specialist->id;
        $session->user_id = $booking->user_id;
        $session->service_id = $booking->service_id;
        $session->session_date = $validated['session_date'];
        $session->start_time = $validated['start_time'];
        $session->end_time = $validated['end_time'];
        $session->session_type = $validated['session_type'];
        $session->notes = $validated['notes'] ?? null;
        $session->status = 'scheduled'; // Default status
        $session->save();

        // TODO: Add notification logic for user about new session
        
        return redirect()->route('specialist.sessions.show', $session->id)
            ->with('success', 'تم إنشاء الجلسة بنجاح');
    }

    /**
     * عرض تفاصيل جلسة محددة
     */
    public function showSession($id)
    {
        $specialist = Auth::user();
        $session = Session::where('specialist_id', $specialist->id)
            ->where('id', $id)
            ->with(['user', 'service', 'booking']) // Eager load relationships
            ->firstOrFail();
        
        return view('specialist.sessions.show', compact('session'));
    }

    /**
     * تحديث جلسة موجودة (الحالة، الملاحظات، الخ)
     */
    public function updateSession(Request $request, $id)
    {
        $specialist = Auth::user();
        $session = Session::where('specialist_id', $specialist->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,canceled,no-show', // Added no-show
            'notes' => 'nullable|string',
            // Add validation for file uploads if implementing
        ]);

        $session->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $session->notes,
        ]);

        // TODO: Handle file uploads if needed
        // TODO: Add notification logic for user about session update

        return redirect()->route('specialist.sessions.show', $session->id)
            ->with('success', 'تم تحديث تفاصيل الجلسة بنجاح');
    }

    /**
     * عرض الملف الشخصي للمختص
     */
    public function profile()
    {
        $specialist = Auth::user();
        $specialistServices = Service::whereHas('specialists', function($query) use ($specialist) {
            $query->where('specialist_id', $specialist->id);
        })->get();
        
        return view('specialist.profile', compact('specialist', 'specialistServices'));
    }

    /**
     * تحديث الملف الشخصي للمختص
     */
    public function updateProfile(Request $request)
    {
        $specialist = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $specialist->id,
            'phone' => 'nullable|string|max:20', // Made phone nullable as per typical profile updates
            'bio' => 'nullable|string',
            'specialization' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Add other fields like address, working hours, etc. if needed
        ]);
        
        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($specialist->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($specialist->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($specialist->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }
        
        $specialist->update($validated);
        
        return redirect()->route('specialist.profile')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض الجدول الزمني للمختص
     */
    public function schedule()
    {
        $specialist = Auth::user();
        
        // الحجوزات المؤكدة المستقبلية
        $upcomingBookings = Booking::where('specialist_id', $specialist->id)
            ->where('status', 'confirmed')
            ->where('appointment_date', '>=', now())
            ->with(['user', 'service'])
            ->orderBy('appointment_date', 'asc')
            ->get();
        
        // الجلسات المجدولة
        $scheduledSessions = Session::where('specialist_id', $specialist->id)
            ->where('session_date', '>=', now())
            ->where('status', 'scheduled') // Only show scheduled sessions
            ->with(['user', 'service', 'booking'])
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        
        // TODO: Add logic for specialist's availability (needs availability model/table)

        return view('specialist.schedule', compact('upcomingBookings', 'scheduledSessions'));
    }

    /**
     * عرض تقارير المختص
     */
    public function reports()
    {
        $specialist = Auth::user();
        
        // إحصائيات الحجوزات
        $totalBookings = Booking::where('specialist_id', $specialist->id)->count();
        $bookingsByStatus = Booking::where('specialist_id', $specialist->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status'); // Simplified pluck
        
        // إحصائيات الحجوزات الشهرية (آخر 6 أشهر)
        $monthlyBookings = Booking::where('specialist_id', $specialist->id)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->pluck('total', 'month'); // Use pluck for easier chart data
        
        // أكثر الخدمات حجزاً (بالعدد)
        $topBookedServicesCount = Booking::where('bookings.specialist_id', $specialist->id)
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(*) as count'))
            ->groupBy('services.id', 'services.name') // Group by ID as well
            ->orderBy('count', 'desc')
            ->limit(5)
            ->pluck('count', 'name');

        // إحصائيات الجلسات
        $totalSessions = Session::where('specialist_id', $specialist->id)->count();
        $sessionsByStatus = Session::where('specialist_id', $specialist->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // متوسط التقييم
        $averageRating = Review::where('specialist_id', $specialist->id)->avg('rating');

        // إحصائيات العملاء
        $totalClients = Booking::where('specialist_id', $specialist->id)->distinct('user_id')->count('user_id');
        $newClientsLast30Days = Booking::where('specialist_id', $specialist->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count('user_id');

        return view('specialist.reports.index', compact(
            'totalBookings',
            'bookingsByStatus',
            'monthlyBookings',
            'topBookedServicesCount',
            'totalSessions',
            'sessionsByStatus',
            'averageRating',
            'totalClients',
            'newClientsLast30Days'
        ));
    }

    /**
     * عرض التقارير المالية للمختص
     */
    public function financialReports()
    {
        $specialist = Auth::user();

        // إجمالي الدخل
        $totalIncome = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->sum('payments.amount');

        // الدخل الشهري (آخر 6 أشهر)
        $monthlyIncome = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->select(DB::raw('DATE_FORMAT(payments.created_at, "%Y-%m") as month'), DB::raw('SUM(payments.amount) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->pluck('total', 'month');

        // أكثر الخدمات تحقيقاً للدخل
        $topServicesByRevenue = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->select('services.name', DB::raw('SUM(payments.amount) as total_revenue'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->pluck('total_revenue', 'name');

        // قائمة المدفوعات الأخيرة
        $recentPayments = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.specialist_id', $specialist->id)
            ->select('payments.*', 'users.name as user_name', 'services.name as service_name')
            ->orderBy('payments.created_at', 'desc')
            ->paginate(15); // Paginate payments list

        return view('specialist.reports.financial', compact(
            'totalIncome',
            'monthlyIncome',
            'topServicesByRevenue',
            'recentPayments'
        ));
    }

    /**
     * عرض قائمة العملاء للمختص
     */
    public function clients()
    {
        $specialist = Auth::user();

        // جلب معرفات المستخدمين الذين حجزوا مع هذا المختص
        $clientIds = Booking::where('specialist_id', $specialist->id)
            ->distinct('user_id')
            ->pluck('user_id');

        // جلب بيانات المستخدمين (العملاء)
        $clients = User::whereIn('id', $clientIds)
            ->withCount(['bookings' => function ($query) use ($specialist) {
                $query->where('specialist_id', $specialist->id);
            }])
            ->with(['bookings' => function ($query) use ($specialist) {
                $query->where('specialist_id', $specialist->id)->latest()->first(); // Get latest booking
            }])
            ->paginate(15);

        return view('specialist.clients.index', compact('clients'));
    }

    /**
     * عرض تفاصيل عميل محدد وسجل جلساته
     */
    public function showClient($clientId)
    {
        $specialist = Auth::user();

        // التأكد من أن هذا العميل قد حجز مع المختص الحالي
        $hasBooking = Booking::where('specialist_id', $specialist->id)
            ->where('user_id', $clientId)
            ->exists();

        if (!$hasBooking) {
            abort(403, 'لا يمكنك الوصول لبيانات هذا العميل.');
        }

        $client = User::findOrFail($clientId);

        // جلب حجوزات العميل مع هذا المختص
        $bookings = Booking::where('specialist_id', $specialist->id)
            ->where('user_id', $clientId)
            ->with(['service', 'payments', 'sessions'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        // جلب جلسات العميل مع هذا المختص
        $sessions = Session::where('specialist_id', $specialist->id)
            ->where('user_id', $clientId)
            ->with(['service', 'booking'])
            ->orderBy('session_date', 'desc')
            ->get();

        return view('specialist.clients.show', compact('client', 'bookings', 'sessions'));
    }

    // Add methods for availability management if needed
    // public function availability() { ... }
    // public function updateAvailability(Request $request) { ... }
}

