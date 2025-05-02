// مسارات الحجز
Route::middleware(['auth'])->group(function () {
    // مسارات عملية الحجز
    Route::get('/booking/step1', [App\Http\Controllers\BookingController::class, 'step1'])->name('booking.step1');
    Route::get('/booking/step2', [App\Http\Controllers\BookingController::class, 'step2'])->name('booking.step2');
    Route::get('/booking/step3', [App\Http\Controllers\BookingController::class, 'step3'])->name('booking.step3');
    Route::post('/booking/select-specialist', [App\Http\Controllers\BookingController::class, 'selectSpecialist'])->name('booking.selectSpecialist');
    Route::post('/booking/select-service', [App\Http\Controllers\BookingController::class, 'selectService'])->name('booking.selectService');
    Route::post('/booking/select-package', [App\Http\Controllers\BookingController::class, 'selectPackage'])->name('booking.selectPackage');
    Route::post('/booking/select-date-time', [App\Http\Controllers\BookingController::class, 'selectDateTime'])->name('booking.selectDateTime');
    Route::post('/booking/confirm', [App\Http\Controllers\BookingController::class, 'confirm'])->name('booking.confirm');
    Route::get('/booking/confirmation', [App\Http\Controllers\BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::get('/booking/process/{step}/{specialist_id?}/{service_id?}', [App\Http\Controllers\BookingController::class, 'process'])->name('booking.process');
});

// مسارات الصفحات العامة
Route::get('/terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');

// مسارات إعدادات الإدارة
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update/general', [App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('settings.update.general');
    Route::post('/settings/update/mail', [App\Http\Controllers\Admin\SettingsController::class, 'updateMail'])->name('settings.update.mail');
    Route::post('/settings/update/payment', [App\Http\Controllers\Admin\SettingsController::class, 'updatePayment'])->name('settings.update.payment');
    Route::post('/settings/update/social', [App\Http\Controllers\Admin\SettingsController::class, 'updateSocial'])->name('settings.update.social');
});
