<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Specialist;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
        // الخدمات المميزة
        $featuredServices = Service::where('is_featured', true)
            ->where('is_active', true)
            ->take(6)
            ->get();

        // المختصين المميزين
        $featuredSpecialists = Specialist::with(['services', 'categories'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('rating', 'desc')
            ->take(4)
            ->get();

        // جميع المختصين النشطين (للقسم الإضافي في الصفحة الرئيسية)
        $specialists = Specialist::with(['services', 'categories'])
            ->where('is_active', true)
            ->orderBy('rating', 'desc')
            ->take(8)
            ->get();

        // الباقات المميزة
        $featuredPackages = Service::where('is_package', true)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->take(3)
            ->get();

        // أحدث المقالات
        $latestPosts = BlogPost::with('category', 'author')
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // الأسئلة الشائعة
        $faqs = Faq::where('is_featured', true)
            ->orderBy('order')
            ->take(5)
            ->get();

        // الإحصائيات
        $stats = [
            'specialists' => Specialist::where('is_active', true)->count(),
            'specialists_count' => Specialist::where('is_active', true)->count(),
            'sessions' => 5000, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
            'sessions_count' => 5000, // إضافة مفتاح sessions_count
            'clients' => 3000, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
            'users_count' => User::count(), // عدد المستخدمين
            'satisfaction' => 98, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
        ];

        return view('home', compact(
            'featuredServices',
            'featuredSpecialists',
            'specialists',
            'featuredPackages',
            'latestPosts',
            'faqs',
            'stats'
        ));
    }

    /**
     * عرض صفحة من نحن
     */
    public function about()
    {
        // الإحصائيات
        $stats = [
            'specialists' => Specialist::where('is_active', true)->count(),
            'specialists_count' => Specialist::where('is_active', true)->count(),
            'sessions' => 5000, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
            'sessions_count' => 5000, // إضافة مفتاح sessions_count
            'clients' => 3000, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
            'users_count' => User::count(), // عدد المستخدمين
            'satisfaction' => 98, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
        ];

        // شركاء النجاح
        $partners = Partner::where('is_active', true)
            ->orderBy('order')
            ->get();

        // الشهادات والآراء
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('order')
            ->get();

        // معلومات الاتصال
        $contact_info = [
            'address' => 'الرياض، المملكة العربية السعودية',
            'phone' => '+966 12 345 6789',
            'email' => 'info@nafsaji.com',
            'working_hours' => 'الأحد - الخميس: 9:00 صباحاً - 5:00 مساءً'
        ];

        return view('about', compact('stats', 'partners', 'testimonials', 'contact_info'));
    }

    /**
     * عرض لوحة التحكم للمستخدم
     */
    public function dashboard()
    {
        $user = Auth::user();

        // بيانات لوحة التحكم حسب نوع المستخدم
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('specialist')) {
            // بيانات المختص
            $specialist = $user->specialist;
            
            // تعديل استخدام upcoming() إلى استعلام مباشر
            $upcomingSessions = $specialist->sessions()
                ->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->take(5)
                ->get();
                
            $recentBookings = $specialist->bookings()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('specialist.dashboard', compact('specialist', 'upcomingSessions', 'recentBookings'));
        } else {
            // بيانات المستخدم العادي
            
            // تعديل استخدام upcoming() إلى استعلام مباشر
            $upcomingSessions = $user->sessions()
                ->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->take(5)
                ->get();
                
            // تعديل استخدام recent() إلى استعلام مباشر
            $recentBookings = $user->bookings()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // تصحيح مسار العرض من dashboard.user إلى user.dashboard
            return view('user.dashboard', compact('user', 'upcomingSessions', 'recentBookings'));
        }
    }

    /**
     * عرض لوحة التحكم للمدير
     */
    public function adminDashboard()
    {
        // التحقق من صلاحيات المستخدم
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('dashboard');
        }

        // إحصائيات عامة
        $stats = [
            'users' => User::count(),
            'specialists' => Specialist::count(),
            'specialists_count' => Specialist::count(), // إضافة مفتاح specialists_count
            'services' => Service::where('is_package', false)->count(),
            'packages' => Service::where('is_package', true)->count(),
            'sessions' => 5000, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
            'sessions_count' => 5000, // إضافة مفتاح sessions_count
            'revenue' => 150000, // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات
        ];

        // المستخدمين الجدد
        $newUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        // أحدث الحجوزات
        $recentBookings = []; // يمكن استبدالها بقيمة حقيقية من قاعدة البيانات

        return view('admin.dashboard', compact('stats', 'newUsers', 'recentBookings'));
    }

    /**
     * عرض الملف الشخصي للمستخدم
     */
    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    /**
     * تحديث الملف الشخصي للمستخدم
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // تحديث البيانات الأساسية
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        // تحديث كلمة المرور إذا تم توفيرها
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        // تحديث الصورة الشخصية إذا تم توفيرها
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا وجدت
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = 'storage/' . $avatarPath;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض حجوزات المستخدم
     */
    public function bookings()
    {
        $user = Auth::user();
        $bookings = $user->bookings()->with(['service', 'specialist'])->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * عرض تفاصيل حجز معين
     */
    public function bookingShow($id)
    {
        $user = Auth::user();
        $booking = $user->bookings()->with(['service', 'specialist', 'sessions'])->findOrFail($id);

        return view('bookings.show', compact('booking'));
    }

    /**
     * إنشاء حجز جديد
     */
    public function bookingStore(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'specialist_id' => 'required|exists:specialists,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        // إنشاء الحجز
        $booking = Auth::user()->bookings()->create([
            'service_id' => $validated['service_id'],
            'specialist_id' => $validated['specialist_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.show', $booking->id)->with('success', 'تم إنشاء الحجز بنجاح');
    }

    /**
     * إلغاء حجز
     */
    public function bookingDestroy($id)
    {
        $user = Auth::user();
        $booking = $user->bookings()->findOrFail($id);

        // التحقق من إمكانية إلغاء الحجز
        if ($booking->status === 'completed') {
            return redirect()->route('bookings')->with('error', 'لا يمكن إلغاء حجز مكتمل');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return redirect()->route('bookings')->with('success', 'تم إلغاء الحجز بنجاح');
    }
}
