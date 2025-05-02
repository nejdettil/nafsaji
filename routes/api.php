<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SpecialistController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| هنا يمكنك تسجيل مسارات API لتطبيقك. يتم تحميل هذه المسارات
| بواسطة RouteServiceProvider وسيتم تعيينها جميعًا إلى مجموعة "api".
|
*/

// مسارات المصادقة
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

// المسارات المحمية بالمصادقة
Route::middleware('auth:sanctum')->group(function () {
    // معلومات المستخدم
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // الخدمات
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::get('/service-categories', [ServiceController::class, 'categories']);
    Route::get('/service-categories/{category}', [ServiceController::class, 'categoryServices']);
    Route::get('/packages', [ServiceController::class, 'packages']);
    Route::get('/packages/{package}', [ServiceController::class, 'showPackage']);
    
    // المختصين
    Route::get('/specialists', [SpecialistController::class, 'index']);
    Route::get('/specialists/{specialist}', [SpecialistController::class, 'show']);
    Route::get('/specialists/by-service/{service}', [SpecialistController::class, 'getByService']);
    Route::get('/specialists/{specialist}/available-slots', [SpecialistController::class, 'getAvailableSlots']);
    Route::get('/specialists/{specialist}/reviews', [SpecialistController::class, 'reviews']);
    Route::post('/specialists/{specialist}/reviews', [SpecialistController::class, 'storeReview']);
    
    // الحجوزات
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    
    // الجلسات
    Route::get('/sessions', [SessionController::class, 'index']);
    Route::get('/sessions/{session}', [SessionController::class, 'show']);
    Route::post('/sessions/{session}/join', [SessionController::class, 'join']);
    Route::post('/sessions/{session}/leave', [SessionController::class, 'leave']);
    Route::post('/sessions/{session}/complete', [SessionController::class, 'complete']);
    Route::post('/sessions/{session}/notes', [SessionController::class, 'storeNotes']);
    
    // المدفوعات
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::get('/payment-methods', [PaymentController::class, 'methods']);
    Route::post('/payment-methods', [PaymentController::class, 'storeMethod']);
    Route::delete('/payment-methods/{method}', [PaymentController::class, 'destroyMethod']);
    
    // مسارات خاصة بالمختصين
    Route::middleware('role:specialist')->prefix('specialist')->group(function () {
        Route::get('/dashboard', [SpecialistController::class, 'dashboard']);
        Route::get('/sessions', [SessionController::class, 'specialistSessions']);
        Route::get('/bookings', [BookingController::class, 'specialistBookings']);
        Route::get('/payments', [PaymentController::class, 'specialistPayments']);
        Route::get('/reviews', [SpecialistController::class, 'specialistReviews']);
        Route::get('/schedule', [SpecialistController::class, 'schedule']);
        Route::post('/schedule', [SpecialistController::class, 'updateSchedule']);
        Route::post('/sessions/{session}/start', [SessionController::class, 'start']);
        Route::post('/sessions/{session}/end', [SessionController::class, 'end']);
    });
    
    // مسارات خاصة بالمدير
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // إدارة المستخدمين
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        
        // إدارة المختصين
        Route::get('/specialists', [SpecialistController::class, 'adminIndex']);
        Route::post('/specialists', [SpecialistController::class, 'adminStore']);
        Route::get('/specialists/{specialist}', [SpecialistController::class, 'adminShow']);
        Route::put('/specialists/{specialist}', [SpecialistController::class, 'adminUpdate']);
        Route::delete('/specialists/{specialist}', [SpecialistController::class, 'adminDestroy']);
        Route::put('/specialists/{specialist}/approve', [SpecialistController::class, 'approve']);
        Route::put('/specialists/{specialist}/reject', [SpecialistController::class, 'reject']);
        
        // إدارة الخدمات
        Route::get('/services', [ServiceController::class, 'adminIndex']);
        Route::post('/services', [ServiceController::class, 'adminStore']);
        Route::get('/services/{service}', [ServiceController::class, 'adminShow']);
        Route::put('/services/{service}', [ServiceController::class, 'adminUpdate']);
        Route::delete('/services/{service}', [ServiceController::class, 'adminDestroy']);
        
        // إدارة فئات الخدمات
        Route::get('/service-categories', [ServiceController::class, 'adminCategories']);
        Route::post('/service-categories', [ServiceController::class, 'adminStoreCategory']);
        Route::put('/service-categories/{category}', [ServiceController::class, 'adminUpdateCategory']);
        Route::delete('/service-categories/{category}', [ServiceController::class, 'adminDestroyCategory']);
        
        // إدارة الباقات
        Route::get('/packages', [ServiceController::class, 'adminPackages']);
        Route::post('/packages', [ServiceController::class, 'adminStorePackage']);
        Route::put('/packages/{package}', [ServiceController::class, 'adminUpdatePackage']);
        Route::delete('/packages/{package}', [ServiceController::class, 'adminDestroyPackage']);
        
        // إدارة الحجوزات
        Route::get('/bookings', [BookingController::class, 'adminIndex']);
        Route::post('/bookings', [BookingController::class, 'adminStore']);
        Route::get('/bookings/{booking}', [BookingController::class, 'adminShow']);
        Route::put('/bookings/{booking}', [BookingController::class, 'adminUpdate']);
        Route::delete('/bookings/{booking}', [BookingController::class, 'adminDestroy']);
        Route::put('/bookings/{booking}/approve', [BookingController::class, 'approve']);
        Route::put('/bookings/{booking}/reject', [BookingController::class, 'reject']);
        
        // إدارة الجلسات
        Route::get('/sessions', [SessionController::class, 'adminIndex']);
        Route::get('/sessions/{session}', [SessionController::class, 'adminShow']);
        Route::put('/sessions/{session}', [SessionController::class, 'adminUpdate']);
        Route::delete('/sessions/{session}', [SessionController::class, 'adminDestroy']);
        
        // إدارة المدفوعات
        Route::get('/payments', [PaymentController::class, 'adminIndex']);
        Route::get('/payments/{payment}', [PaymentController::class, 'adminShow']);
        Route::put('/payments/{payment}', [PaymentController::class, 'adminUpdate']);
        Route::delete('/payments/{payment}', [PaymentController::class, 'adminDestroy']);
        Route::put('/payments/{payment}/approve', [PaymentController::class, 'approve']);
        Route::put('/payments/{payment}/reject', [PaymentController::class, 'reject']);
        
        // إحصائيات
        Route::get('/stats/users', [UserController::class, 'stats']);
        Route::get('/stats/bookings', [BookingController::class, 'stats']);
        Route::get('/stats/payments', [PaymentController::class, 'stats']);
        Route::get('/stats/sessions', [SessionController::class, 'stats']);
    });
});

// المسارات العامة
Route::get('/services/public', [ServiceController::class, 'publicIndex']);
Route::get('/specialists/public', [SpecialistController::class, 'publicIndex']);
Route::get('/service-categories/public', [ServiceController::class, 'publicCategories']);
