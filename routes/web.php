<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminPackageController;
use App\Http\Controllers\Admin\UserManagementController as AdminUserManagementController;
use App\Http\Controllers\Admin\SpecialistController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Specialist\DashboardController as SpecialistDashboardController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\BookingController as UserBookingController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\NotificationController; // General notification controller
use App\Http\Controllers\Specialist\NotificationController as SpecialistNotificationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// مسار dashboard العام - يقوم بتوجيه المستخدم حسب دوره
Route::middleware(["auth"])->group(function () {
    Route::get("/dashboard", function () {
        if (Auth::user()->hasRole("admin")) {
            return redirect()->route("admin.dashboard");
        } elseif (Auth::user()->hasRole("specialist")) {
            return redirect()->route("specialist.dashboard");
        } else {
            return redirect()->route("user.dashboard");
        }
    })->name("dashboard");
});

// الصفحة الرئيسية
Route::get("/", [HomeController::class, "index"])->name("home");

// صفحة من نحن
Route::get("/about", [HomeController::class, "about"])->name("about");

// صفحة الشروط والأحكام
Route::get("/terms", [TermsController::class, "index"])->name("terms");

// صفحة سياسة الخصوصية
Route::get("/privacy", [PrivacyController::class, "index"])->name("privacy");
Route::get("/privacy-policy", [PrivacyController::class, "index"])->name("privacy-policy");

// صفحة الاتصال
Route::get("/contact", [ContactController::class, "index"])->name("contact");
Route::post("/contact", [ContactController::class, "store"])->name("contact.store");
Route::post("/contact/send", [ContactController::class, "send"])->name("contact.send");
Route::post("/contact/submit", [ContactController::class, "store"])->name("contact.submit");

// الأسئلة الشائعة
Route::get("/faq", [FaqController::class, "index"])->name("faq");

// النشرة البريدية
Route::post("/newsletter/subscribe", [NewsletterController::class, "subscribe"])->name("newsletter.subscribe");

// الباقات - مسارات مباشرة للواجهة الأمامية
Route::get("/packages", [ServiceController::class, "packages"])->name("packages.index");
Route::get("/packages/{package}", [ServiceController::class, "packageShow"])->name("packages.show");
Route::get("/packages/{package}/book", [ServiceController::class, "packageBook"])->name("packages.book");

// الخدمات - مسارات مباشرة للواجهة الأمامية
Route::get("/services", [ServiceController::class, "index"])->name("services");
Route::get("/services/index", [ServiceController::class, "index"])->name("services.index");
Route::get("/services/{service}", [ServiceController::class, "show"])->name("services.show");
Route::get("/services/{service}/book", [ServiceController::class, "book"])->name("services.book");
Route::get("/services/packages", [ServiceController::class, "servicesPackages"])->name("services.packages");

// المختصين - مسارات مباشرة للواجهة الأمامية
Route::get("/specialists", [ServiceController::class, "specialists"])->name("specialists");
Route::get("/specialists/index", [ServiceController::class, "specialists"])->name("specialists.index");
Route::get("/specialists/{specialist}", [ServiceController::class, "specialistShow"])->name("specialists.show");
Route::get("/specialists/{specialist}/book", [ServiceController::class, "specialistBook"])->name("specialists.book");

// مسارات المصادقة
Route::get("/login", [AuthController::class, "showLoginForm"])->name("login");
Route::post("/login", [AuthController::class, "login"]);
Route::get("/register", [AuthController::class, "showRegistrationForm"])->name("register");
Route::post("/register", [AuthController::class, "register"]);
Route::post("/logout", [AuthController::class, "logout"])->name("logout");

// إضافة دعم لطريقة GET لمسار تسجيل الخروج
Route::get("/logout", [AuthController::class, "logout"])->middleware("auth")->name("logout.get");

// مسارات المصادقة الاجتماعية
Route::get("/login/google", [AuthController::class, "redirectToGoogle"])->name("login.google");
Route::get("/login/google/callback", [AuthController::class, "handleGoogleCallback"]);
Route::get("/login/facebook", [AuthController::class, "redirectToFacebook"])->name("login.facebook");
Route::get("/login/facebook/callback", [AuthController::class, "handleFacebookCallback"]);

// مسارات الملف الشخصي العامة (للمستخدمين المصادق عليهم)
Route::middleware(["auth"])->group(function () {
    Route::get("/profile/edit", [ProfileController::class, "edit"])->name("profile.edit"); // General profile edit form
    Route::put("/profile", [ProfileController::class, "update"])->name("profile.update"); // General profile update
    Route::post("/profile/password", [ProfileController::class, "updatePassword"])->name("profile.update-password");
    Route::post("/profile/photo", [ProfileController::class, "updatePhoto"])->name("profile.update-photo");
    Route::get("/profile/delete-photo", [ProfileController::class, "deletePhoto"])->name("profile.delete-photo");
    Route::post("/profile/notifications", [ProfileController::class, "updateNotifications"])->name("profile.update-notifications");
});

// مسارات الحجز العامة
Route::get("/booking/create", [UserBookingController::class, "create"])->name("booking.create");
Route::post("/booking", [UserBookingController::class, "store"])->name("booking.store"); // General booking store
// Route::post("/bookings/{booking}/complete", [BookingController::class, "markAsCompleted"])->name("bookings.complete"); // Moved to admin/specialist

// مسارات محمية للمستخدم العادي (Merged)
Route::prefix("user")->name("user.")->middleware(["auth", "role:user"])->group(function () {
    // لوحة التحكم
    Route::get("/dashboard", [UserDashboardController::class, "index"])->name("dashboard");

    // الملف الشخصي (User specific)
    Route::get("/profile", [UserDashboardController::class, "profile"])->name("profile");
    Route::get("/profile/edit", [UserDashboardController::class, "editProfile"])->name("profile.edit"); // Specific edit form for user dashboard
    Route::put("/profile", [UserDashboardController::class, "updateProfile"])->name("profile.update"); // Specific update action for user dashboard
    Route::get("/profile/change-password", [UserDashboardController::class, "showChangePasswordForm"])->name("profile.change-password");
    Route::post("/profile/change-password", [UserDashboardController::class, "changePassword"])->name("profile.update-password");

    // الإعدادات
    Route::get("/settings", [UserDashboardController::class, "settings"])->name("settings");
    Route::post("/settings", [UserDashboardController::class, "updateSettings"])->name("settings.update");

    // الإشعارات
    Route::get("/notifications", [UserDashboardController::class, "notifications"])->name("notifications");
    Route::post("/notifications/{id}/read", [UserDashboardController::class, "markNotificationAsRead"])->name("notification.read");
    Route::post("/notifications/read-all", [UserDashboardController::class, "markAllNotificationsAsRead"])->name("notifications.read-all");
    Route::delete("/notifications/{id}", [UserDashboardController::class, "deleteNotification"])->name("notification.delete");
    Route::delete("/notifications", [UserDashboardController::class, "deleteAllNotifications"])->name("notifications.delete-all");
    // Route::get("/notifications/count", [NotificationController::class, "count"])->name("notifications.count"); // Original conflicting route removed
    // Route::get("/admin/notifications/count/{user}", function ($userId) { ... })->name("notifications.count"); // Original conflicting/misplaced route removed

    // الحجوزات
    Route::get("/bookings", [UserDashboardController::class, "bookings"])->name("bookings");
    Route::get("/bookings/{booking}", [UserDashboardController::class, "showBooking"])->name("bookings.show");
    // Route::post("/bookings", [UserDashboardController::class, "storeBooking"])->name("bookings.store"); // Use general booking.store route
    Route::delete("/bookings/{booking}", [UserDashboardController::class, "destroyBooking"])->name("bookings.destroy"); // User cancel booking
    // Route::post("/bookings", [BookingController::class, "store"])->name("bookings.store"); // Original duplicate removed

    // المدفوعات
    Route::get("/payments", [UserDashboardController::class, "payments"])->name("payments");
    Route::get("/payments/{payment}", [UserDashboardController::class, "showPayment"])->name("payments.show");
    // طرق الدفع (New)
    Route::get("/payment-methods", [UserDashboardController::class, "paymentMethods"])->name("payment-methods.index");
    Route::post("/payment-methods", [UserDashboardController::class, "storePaymentMethod"])->name("payment-methods.store");
    Route::delete("/payment-methods/{id}", [UserDashboardController::class, "destroyPaymentMethod"])->name("payment-methods.destroy");

    // الجلسات
    Route::get("/sessions", [UserDashboardController::class, "sessions"])->name("sessions");
    Route::get("/sessions/{session}", [UserDashboardController::class, "showSession"])->name("sessions.show");

    // المفضلة
    Route::get("/favorites", [UserDashboardController::class, "favorites"])->name("favorites");
    Route::post("/favorites/{id}", [UserDashboardController::class, "addToFavorites"])->name("favorites.add");
    Route::delete("/favorites/{id}", [UserDashboardController::class, "removeFromFavorites"])->name("favorites.remove");

    // التقييمات
    Route::get("/reviews", [UserDashboardController::class, "reviews"])->name("reviews"); // List user's reviews
    Route::get("/reviews/create/{session}", [UserDashboardController::class, "createReview"])->name("reviews.create"); // Show review form for a session (New)
    Route::post("/reviews/{session}", [UserDashboardController::class, "storeReview"])->name("reviews.store"); // Store review for a session (New - specific signature)
    // Route::post("/reviews", [UserDashboardController::class, "storeReview"])->name("user.reviews.store"); // Original general store review removed/replaced
    Route::get("/reviews/{review}/edit", [UserDashboardController::class, "editReview"])->name("reviews.edit"); // Edit user's review (New)
    Route::put("/reviews/{review}", [UserDashboardController::class, "updateReview"])->name("reviews.update");
    Route::delete("/reviews/{review}", [UserDashboardController::class, "destroyReview"])->name("reviews.destroy");
});

// مسارات لوحة المختص (Merged)
Route::prefix("specialist")->name("specialist.")->middleware(["auth", "role:specialist"])->group(function () {
    // لوحة التحكم
    Route::get("/", [SpecialistDashboardController::class, "index"])->name("dashboard");
    Route::get("/dashboard", [SpecialistDashboardController::class, "index"])->name("dashboard.index");
    // Route::get("/analytics", [SpecialistDashboardController::class, "analytics"])->name("analytics"); // Covered by reports

    // الملف الشخصي
    Route::get("/profile", [SpecialistDashboardController::class, "profile"])->name("profile");
    Route::get("/profile/edit", [SpecialistDashboardController::class, "editProfile"])->name("profile.edit"); // Original route kept
    Route::put("/profile", [SpecialistDashboardController::class, "updateProfile"])->name("profile.update");

    // الحجوزات
    Route::get("/bookings", [SpecialistDashboardController::class, "bookings"])->name("bookings.index"); // Renamed for consistency
    Route::get("/bookings/{booking}", [SpecialistDashboardController::class, "showBooking"])->name("bookings.show");
    Route::put("/bookings/{booking}/status", [SpecialistDashboardController::class, "updateBookingStatus"])->name("bookings.update-status");

    // الجلسات
    Route::get("/sessions", [SpecialistDashboardController::class, "sessions"])->name("sessions.index");
    Route::get("/sessions/create", [SpecialistDashboardController::class, "createSession"])->name("sessions.create");
    Route::post("/sessions", [SpecialistDashboardController::class, "storeSession"])->name("sessions.store");
    Route::get("/sessions/{session}", [SpecialistDashboardController::class, "showSession"])->name("sessions.show");
    Route::put("/sessions/{session}", [SpecialistDashboardController::class, "updateSession"])->name("sessions.update"); // Added update session route

    // الجدول الزمني والتوفر
    Route::get("/schedule", [SpecialistDashboardController::class, "schedule"])->name("schedule");
    Route::get("/availability", [SpecialistDashboardController::class, "availability"])->name("availability");
    Route::post("/availability", [SpecialistDashboardController::class, "storeAvailability"])->name("availability.store");

    // التقارير
    Route::get("/reports", [SpecialistDashboardController::class, "reports"])->name("reports.index"); // General reports
    Route::get("/reports/financial", [SpecialistDashboardController::class, "financialReports"])->name("reports.financial"); // Financial reports (New)

    // العملاء
    Route::get("/clients", [SpecialistDashboardController::class, "clients"])->name("clients.index"); // Renamed for consistency
    Route::get("/clients/{client}", [SpecialistDashboardController::class, "showClient"])->name("clients.show");

    // الخدمات (If managed by specialist)
    Route::get("/services", [SpecialistDashboardController::class, "services"])->name("services");
    Route::get("/services/create", [SpecialistDashboardController::class, "createService"])->name("services.create");
    Route::post("/services", [SpecialistDashboardController::class, "storeService"])->name("services.store");
    Route::get("/services/{service}/edit", [SpecialistDashboardController::class, "editService"])->name("services.edit");
    Route::put("/services/{service}", [SpecialistDashboardController::class, "updateService"])->name("services.update");

    // التقييمات
    Route::get("/reviews", [SpecialistDashboardController::class, "reviews"])->name("reviews"); // List reviews received
    Route::post("/reviews/{review}/reply", [SpecialistDashboardController::class, "replyToReview"])->name("reviews.reply"); // Reply to a review

    // المدفوعات (Read-only view for specialist)
    Route::get("/payments", [SpecialistDashboardController::class, "payments"])->name("payments"); // List payments related to specialist
    Route::get("/payments/{payment}", [SpecialistDashboardController::class, "showPayment"])->name("payments.show"); // Show payment details

    // الإشعارات
    Route::get("/notifications", [SpecialistNotificationController::class, "index"])->name("notifications.index");
    Route::post("/notifications/{id}/read", [SpecialistNotificationController::class, "markAsRead"])->name("notifications.read");
    Route::post("/notifications/read-all", [SpecialistNotificationController::class, "markAllAsRead"])->name("notifications.read-all");

});
// مسارات لوحة الإدارة
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // إدارة فئات الخدمات
    Route::resource('services/categories', ServiceCategoryController::class)->names([
        'index' => 'services.categories.index',
        'create' => 'services.categories.create',
        'store' => 'services.categories.store',
        'show' => 'services.categories.show',
        'edit' => 'services.categories.edit',
        'update' => 'services.categories.update',
        'destroy' => 'services.categories.destroy',
    ]);
    Route::post('/packages/update-status', [AdminPackageController::class, 'updateStatus'])->name('packages.update-status');
    Route::post('/packages/bulk-action', [AdminPackageController::class, 'bulkAction'])->name('packages.bulk-action');

    Route::get('/notifications/count', [App\Http\Controllers\Admin\NotificationController::class, 'count'])->name('admin.notifications.count');
    // لوحة التحكم الإدارية
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');

    Route::get('/users/download-template', [AdminUserManagementController::class, 'downloadTemplate'])->name('users.download-template');
    Route::post('/users/bulk-action', [AdminUserManagementController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/chart-data', [AdminUserManagementController::class, 'getChartData'])->name('users-chart-data');
    Route::post('/users/status', [AdminUserManagementController::class, 'updateStatus'])->name('users.update-status');
    Route::get('/users/create', [AdminUserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/get', [AdminUserManagementController::class, 'get'])->name('users.get');


    // الإشعارات
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // التقارير
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/bookings', [ReportsController::class, 'bookings'])->name('reports.bookings');
    Route::get('/reports/payments', [ReportsController::class, 'payments'])->name('reports.payments');
    Route::get('/reports/users', [ReportsController::class, 'users'])->name('reports.users');
    Route::get('/reports/specialists', [ReportsController::class, 'specialists'])->name('reports.specialists');
    Route::get('/reports/export/{type}', [ReportsController::class, 'export'])->name('reports.export');

    // إعدادات النظام
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.update.general');
    Route::post('/settings/mail', [SettingsController::class, 'updateMail'])->name('settings.update.mail');
    Route::post('/settings/payment', [SettingsController::class, 'updatePayment'])->name('settings.update.payment');
    Route::post('/settings/social', [SettingsController::class, 'updateSocial'])->name('settings.update.social');

    // إدارة المستخدمين
    Route::get('/users', [AdminUserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdminUserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/export', [AdminUserManagementController::class, 'export'])->name('users.export');
    Route::post('/users/import', [AdminUserManagementController::class, 'import'])->name('users.import');

    // إدارة المختصين
    Route::get('/specialists', [SpecialistController::class, 'index'])->name('specialists.index');
    Route::get('/specialists/create', [SpecialistController::class, 'create'])->name('specialists.create');
    Route::post('/specialists', [SpecialistController::class, 'store'])->name('specialists.store');
    Route::get('/specialists/{specialist}', [SpecialistController::class, 'show'])->name('specialists.show');
    Route::get('/specialists/{specialist}/edit', [SpecialistController::class, 'edit'])->name('specialists.edit');
    Route::put('/specialists/{specialist}', [SpecialistController::class, 'update'])->name('specialists.update');
    Route::delete('/specialists/{specialist}', [SpecialistController::class, 'destroy'])->name('specialists.destroy');
    Route::post('/specialists/status', [SpecialistController::class, 'updateStatus'])->name('specialists.update-status');
    Route::get('/specialists/get/specialist', [SpecialistController::class, 'getSpecialist'])->name('specialists.get');
    Route::get('/specialists/export', [SpecialistController::class, 'export'])->name('specialists.export');
    Route::post('/specialists/import', [SpecialistController::class, 'import'])->name('admin.specialists.import');
    Route::get('/specialists/download-template', [SpecialistController::class, 'downloadTemplate'])->name('specialists.download-template');
    Route::post('/specialists/bulk-action', [SpecialistController::class, 'bulkAction'])->name('specialists.bulk-action');
    Route::post('/specialists/import', [SpecialistController::class, 'import'])->name('specialists.import');

    // إدارة الخدمات
    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [AdminServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');


    // إدارة الحجوزات
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'markAsCompleted'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // إدارة الجلسات
    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');
    Route::get('/sessions/{session}/edit', [SessionController::class, 'edit'])->name('sessions.edit');
    Route::put('/sessions/{session}', [SessionController::class, 'update'])->name('sessions.update');
    Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');

    // إدارة المدفوعات
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // إدارة المدونة - الفئات
    Route::get('/blog/categories', [BlogCategoryController::class, 'index'])->name('blog.categories.index');
    Route::get('/blog/categories/create', [BlogCategoryController::class, 'create'])->name('blog.categories.create');
    Route::post('/blog/categories', [BlogCategoryController::class, 'store'])->name('blog.categories.store');
    Route::get('/blog/categories/{category}', [BlogCategoryController::class, 'show'])->name('blog.categories.show');
    Route::get('/blog/categories/{category}/edit', [BlogCategoryController::class, 'edit'])->name('blog.categories.edit');
    Route::put('/blog/categories/{category}', [BlogCategoryController::class, 'update'])->name('blog.categories.update');
    Route::delete('/blog/categories/{category}', [BlogCategoryController::class, 'destroy'])->name('blog.categories.destroy');

    // إدارة المدونة - المقالات
    Route::get('/blog/posts', [BlogPostController::class, 'index'])->name('blog.posts.index');
    Route::get('/blog/posts/create', [BlogPostController::class, 'create'])->name('blog.posts.create');
    Route::post('/blog/posts', [BlogPostController::class, 'store'])->name('blog.posts.store');
    Route::get('/blog/posts/{post}', [BlogPostController::class, 'show'])->name('blog.posts.show');
    Route::get('/blog/posts/{post}/edit', [BlogPostController::class, 'edit'])->name('blog.posts.edit');
    Route::put('/blog/posts/{post}', [BlogPostController::class, 'update'])->name('blog.posts.update');
    Route::delete('/blog/posts/{post}', [BlogPostController::class, 'destroy'])->name('blog.posts.destroy');

    // إدارة المدونة - التعليقات
    Route::get('/blog/comments', [BlogCommentController::class, 'index'])->name('blog.comments.index');
    Route::get('/blog/comments/{comment}', [BlogCommentController::class, 'show'])->name('blog.comments.show');
    Route::put('/blog/comments/{comment}', [BlogCommentController::class, 'update'])->name('blog.comments.update');
    Route::delete('/blog/comments/{comment}', [BlogCommentController::class, 'destroy'])->name('blog.comments.destroy');
    Route::put('/blog/comments/{comment}/approve', [BlogCommentController::class, 'approve'])->name('blog.comments.approve');
    Route::put('/blog/comments/{comment}/reject', [BlogCommentController::class, 'reject'])->name('blog.comments.reject');

    // إدارة الأسئلة الشائعة
    Route::get('/faqs', [AdminFaqController::class, 'index'])->name('faqs.index');
    Route::get('/faqs/create', [AdminFaqController::class, 'create'])->name('faqs.create');
    Route::post('/faqs', [AdminFaqController::class, 'store'])->name('faqs.store');
    Route::get('/faqs/{faq}', [AdminFaqController::class, 'show'])->name('faqs.show');
    Route::get('/faqs/{faq}/edit', [AdminFaqController::class, 'edit'])->name('faqs.edit');
    Route::put('/faqs/{faq}', [AdminFaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [AdminFaqController::class, 'destroy'])->name('faqs.destroy');

    // إدارة الباقات
    Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}', [AdminPackageController::class, 'show'])->name('packages.show');
    Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');


});
// Fallback route for 404
Route::fallback(function () {
    return response()->view("errors.404", [], 404);
});


