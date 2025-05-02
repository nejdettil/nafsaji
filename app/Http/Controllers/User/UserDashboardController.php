<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Session; // Added Session model
use App\Models\Review; // Added Review model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Added Storage facade

class UserDashboardController extends Controller
{
    /**
     * Display the user's main dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // General stats
        $totalBookings = Booking::where(\'user_id\', $user->id)->count();
        $completedBookings = Booking::where(\'user_id\', $user->id)
            ->where(\'status\', \'completed\')
            ->count();
        $upcomingSessions = Session::where(\'user_id\', $user->id)
            ->where(\'status\', \'scheduled\')
            ->where(\'session_date\', \'>=\', now())
            ->orderBy(\'session_date\', \'asc\')
            ->orderBy(\'start_time\', \'asc\')
            ->take(5)
            ->with(\'specialist\', \'service\') // Eager load relationships
            ->get();

        $recentPayments = Payment::where(\'user_id\', $user->id)
            ->orderBy(\'created_at\', \'desc\')
            ->take(5)
            ->with(\'booking.service\') // Eager load for payment details
            ->get();

        // Fetch unread notifications count
        $unreadNotificationsCount = $user->unreadNotifications()->count();

        return view(\'user.dashboard\', compact(
            \'user\',
            \'totalBookings\',
            \'completedBookings\',
            \'upcomingSessions\',
            \'recentPayments\',
            \'unreadNotificationsCount\'
        ));
    }

    /**
     * Display the user's profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        return view(\'user.profile\', compact(\'user\'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function editProfile()
    {
        $user = Auth::user();
        // Assuming profile-edit view exists or needs creation/update
        return view(\'user.profile-edit\', compact(\'user\'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            \'name\' => \'required|string|max:255\',
            \'email\' => \'required|string|email|max:255|unique:users,email,\' . $user->id,
            \'phone\' => \'nullable|string|max:20\', // Made phone nullable
            \'avatar\' => \'nullable|image|mimes:jpeg,png,jpg,gif|max:2048\',
            // Add other profile fields as needed (e.g., address, dob)
        ]);

        if ($request->hasFile(\'avatar\')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk(\'public\')->exists($user->avatar)) {
                Storage::disk(\'public\')->delete($user->avatar);
            }
            $avatarPath = $request->file(\'avatar\')->store(\'avatars\', \'public\');
            $validated[\'avatar\'] = $avatarPath;
        }

        $user->update($validated);

        return redirect()->route(\'user.profile\')
            ->with(\'success\', \'تم تحديث الملف الشخصي بنجاح.\');
    }

    /**
     * Show the form for changing the user's password.
     */
    public function showChangePasswordForm()
    {
        // Assuming change-password view exists or needs creation/update
        return view(\'user.change-password\');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            \'current_password\' => \'required\',
            \'password\' => \'required|string|min:8|confirmed\',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([\'current_password\' => \'كلمة المرور الحالية غير صحيحة.\']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route(\'user.settings\') // Redirect to settings page after password change
            ->with(\'success\', \'تم تغيير كلمة المرور بنجاح.\');
    }

    /**
     * Display the user's settings page.
     */
    public function settings()
    {
        $user = Auth::user();
        // Ensure settings view is updated to handle new structure
        return view(\'user.settings\', compact(\'user\'));
    }

    /**
     * Update the user's settings.
     */
    public function updateSettings(Request $request)
    {
        // Simplified settings update, adjust based on actual settings fields
        $validated = $request->validate([
            \'notification_email\' => \'boolean\',
            \'notification_push\' => \'boolean\', // Assuming push notifications setting
            \'language\' => \'nullable|string|in:ar,en\',
            \'timezone\' => \'nullable|string\',
            // Add other settings validation
        ]);

        $user = Auth::user();

        // Using JSON column for settings is flexible
        $currentSettings = $user->settings ?? [];
        $newSettings = [
            \'notifications\' => [
                \'email\' => $validated[\'notification_email\'] ?? $currentSettings[\'notifications\'][\'email\'] ?? true,
                \'push\' => $validated[\'notification_push\'] ?? $currentSettings[\'notifications\'][\'push\'] ?? true,
            ],
            \'language\' => $validated[\'language\'] ?? $currentSettings[\'language\'] ?? config(\'app.locale\'),
            \'timezone\' => $validated[\'timezone\'] ?? $currentSettings[\'timezone\'] ?? config(\'app.timezone\'),
        ];

        $user->settings = $newSettings;
        $user->save();

        // Apply language change immediately if needed
        if (isset($validated[\'language\'])) {
            app()->setLocale($validated[\'language\']);
            session()->put(\'locale\', $validated[\'language\']);
        }

        return redirect()->route(\'user.settings\')
            ->with(\'success\', \'تم تحديث الإعدادات بنجاح.\');
    }

    /**
     * Display the user's notifications.
     */
    public function notifications()
    {
        $user = Auth::user();
        // Mark all as read when visiting the notifications page
        $user->unreadNotifications->markAsRead();

        $notifications = $user->notifications()->latest()->paginate(15);

        return view(\'user.notifications\', compact(\'notifications\'));
    }

    /**
     * Mark a specific notification as read (via AJAX usually).
     */
    public function markNotificationAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json([\'success\' => true]);
    }

    /**
     * Mark all notifications as read (via AJAX usually).
     */
    public function markAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json([\'success\' => true]);
    }

    /**
     * Delete a specific notification.
     */
    public function deleteNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json([\'success\' => true]);
        }
        return back()->with(\'success\', \'تم حذف الإشعار بنجاح.\');
    }

    /**
     * Delete all notifications.
     */
    public function deleteAllNotifications()
    {
        Auth::user()->notifications()->delete();
        if (request()->ajax()) {
            return response()->json([\'success\' => true]);
        }
        return redirect()->route(\'user.notifications\')->with(\'success\', \'تم حذف جميع الإشعارات بنجاح.\');
    }

    /**
     * Display the user's bookings list.
     */
    public function bookings()
    {
        $user = Auth::user();
        $bookings = Booking::where(\'user_id\', $user->id)
            ->with(\'specialist\', \'service\', \'sessions\') // Eager load sessions
            ->orderBy(\'created_at\', \'desc\')
            ->paginate(10);

        return view(\'user.bookings\', compact(\'bookings\'));
    }

    /**
     * Display details of a specific booking.
     */
    public function showBooking($bookingId)
    {
        $booking = Booking::where(\'user_id\', Auth::id())
            ->with(\'specialist\', \'service\', \'payments\', \'sessions\') // Eager load related data
            ->findOrFail($bookingId);

        // Assuming booking-details view exists or needs creation/update
        return view(\'user.booking-details\', compact(\'booking\'));
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking($bookingId)
    {
        $booking = Booking::where(\'user_id\', Auth::id())
                         ->whereIn(\'status\', [\'pending\', \'confirmed\']) // Only cancel pending or confirmed
                         ->findOrFail($bookingId);

        // Add logic here for cancellation policy (e.g., time limit, refunds)
        // For now, just mark as cancelled
        $booking->status = \'cancelled\';
        $booking->save();

        // TODO: Notify specialist about cancellation

        return redirect()->route(\'user.bookings\')
            ->with(\'success\', \'تم إلغاء الحجز بنجاح.\');
    }

    /**
     * Display the user's sessions list (upcoming and past).
     */
    public function sessions()
    {
        $user = Auth::user();
        $upcomingSessions = Session::where(\'user_id\', $user->id)
            ->where(\'status\', \'scheduled\')
            ->where(\'session_date\', \'>=\', now()->toDateString())
            ->with(\'specialist\', \'service\')
            ->orderBy(\'session_date\', \'asc\')
            ->orderBy(\'start_time\', \'asc\')
            ->get();

        $pastSessions = Session::where(\'user_id\', $user->id)
            ->where(function($query) {
                $query->where(\'status\', \'completed\')
                      ->orWhere(\'status\', \'canceled\')
                      ->orWhere(\'status\', \'no-show\')
                      ->orWhere(function($q) {
                          $q->where(\'status\', \'scheduled\')
                            ->where(\'session_date\', \'<\', now()->toDateString());
                      });
            })
            ->with(\'specialist\', \'service\', \'review\') // Eager load review
            ->orderBy(\'session_date\', \'desc\')
            ->orderBy(\'start_time\', \'desc\')
            ->paginate(10); // Paginate past sessions

        return view(\'user.sessions\', compact(\'upcomingSessions\', \'pastSessions\'));
    }

    /**
     * Display details of a specific session, including notes/files.
     */
    public function showSession($sessionId)
    {
        $session = Session::where(\'user_id\', Auth::id())
            ->with(\'specialist\', \'service\', \'booking\', \'review\') // Eager load related data
            // TODO: Add relationship for session files/notes if implemented
            // ->with(\'sessionFiles\', \'sessionNotes\')
            ->findOrFail($sessionId);

        // Assuming session-details view exists or needs creation/update
        return view(\'user.session-details\', compact(\'session\'));
    }

    /**
     * Display the user's payment history.
     */
    public function payments()
    {
        $user = Auth::user();
        $payments = Payment::where(\'user_id\', $user->id)
            ->with(\'booking.service\', \'booking.specialist\') // Eager load related data
            ->orderBy(\'created_at\', \'desc\')
            ->paginate(10);

        return view(\'user.payments\', compact(\'payments\'));
    }

    /**
     * Display details of a specific payment.
     */
    public function showPayment($paymentId)
    {
        $payment = Payment::where(\'user_id\', Auth::id())
            ->with(\'booking.service\', \'booking.specialist\') // Eager load related data
            ->findOrFail($paymentId);

        // Assuming payment-details view exists or needs creation/update
        return view(\'user.payment-details\', compact(\'payment\'));
    }

    /**
     * Display payment methods management page.
     * Placeholder: Actual implementation depends heavily on the payment gateway.
     */
    public function paymentMethods()
    {
        $user = Auth::user();
        // Fetch saved payment methods from your payment provider (e.g., Stripe customer object)
        $paymentMethods = []; // Placeholder
        $defaultPaymentMethod = null; // Placeholder

        // This view needs to be created
        return view(\'user.payment-methods\', compact(\'user\', \'paymentMethods\', \'defaultPaymentMethod\'));
    }

    /**
     * Add a new payment method.
     * Placeholder: Needs integration with payment gateway (e.g., Stripe Setup Intents).
     */
    public function addPaymentMethod(Request $request)
    {
        $user = Auth::user();
        // Logic to create Setup Intent, pass client secret to frontend
        // Frontend uses Stripe.js to confirm Setup Intent
        // Webhook handles successful setup and saves method details
        return redirect()->route(\'user.paymentMethods\')->with(\'info\', \'وظيفة إضافة طريقة الدفع قيد الإنشاء.\');
    }

    /**
     * Delete a saved payment method.
     * Placeholder: Needs integration with payment gateway.
     */
    public function deletePaymentMethod(Request $request, $paymentMethodId)
    {
        $user = Auth::user();
        // Logic to detach payment method using payment provider API
        return redirect()->route(\'user.paymentMethods\')->with(\'info\', \'وظيفة حذف طريقة الدفع قيد الإنشاء.\');
    }

    /**
     * Set a default payment method.
     * Placeholder: Needs integration with payment gateway.
     */
    public function setDefaultPaymentMethod(Request $request, $paymentMethodId)
    {
        $user = Auth::user();
        // Logic to update customer default payment method using payment provider API
        return redirect()->route(\'user.paymentMethods\')->with(\'info\', \'وظيفة تعيين طريقة الدفع الافتراضية قيد الإنشاء.\');
    }

    /**
     * Show reviews submitted by the user.
     */
    public function reviews()
    {
        $user = Auth::user();
        $reviews = Review::where(\'user_id\', $user->id)
            ->with(\'specialist\', \'session.service\') // Eager load related data
            ->latest()
            ->paginate(10);

        return view(\'user.reviews\', compact(\'reviews\'));
    }

    /**
     * Show form to submit a review for a completed session.
     */
    public function createReview(Session $session)
    {
        // Ensure the session belongs to the user and is completed
        if ($session->user_id !== Auth::id() || $session->status !== \'completed\') {
            abort(403);
        }
        // Ensure review doesn\'t already exist
        if ($session->review()->exists()) {
            return redirect()->route(\'user.sessions.show\', $session->id)->with(\'info\', \'لقد قمت بتقييم هذه الجلسة بالفعل.\');
        }

        return view(\'user.review-create\', compact(\'session\'));
    }

    /**
     * Store a new review.
     */
    public function storeReview(Request $request, Session $session)
    {
        // Ensure the session belongs to the user and is completed
        if ($session->user_id !== Auth::id() || $session->status !== \'completed\') {
            abort(403);
        }
        // Ensure review doesn\'t already exist
        if ($session->review()->exists()) {
            return redirect()->route(\'user.sessions.show\', $session->id)->with(\'info\', \'لقد قمت بتقييم هذه الجلسة بالفعل.\');
        }

        $validated = $request->validate([
            \'rating\' => \'required|integer|min:1|max:5\',
            \'comment\' => \'nullable|string|max:1000\',
        ]);

        $review = new Review();
        $review->user_id = Auth::id();
        $review->specialist_id = $session->specialist_id;
        $review->session_id = $session->id;
        $review->booking_id = $session->booking_id; // Link review to booking as well
        $review->rating = $validated[\'rating\'];
        $review->comment = $validated[\'comment\'];
        $review->save();

        return redirect()->route(\'user.sessions.show\', $session->id)
            ->with(\'success\', \'شكراً لك! تم إرسال تقييمك بنجاح.\');
    }

}

