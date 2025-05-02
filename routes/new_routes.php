// إضافة المسارات للصفحات الجديدة

// مسارات المستخدم العام
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/booking-details/{id}', [UserDashboardController::class, 'showBookingDetails'])->name('booking.details');
    Route::get('/payment-details/{id}', [UserDashboardController::class, 'showPaymentDetails'])->name('payment.details');
    Route::get('/notifications', [UserDashboardController::class, 'showNotifications'])->name('notifications');
});

// مسارات المختص
Route::middleware(['auth', 'role:specialist'])->prefix('specialist')->name('specialist.')->group(function () {
    Route::get('/schedule', [DashboardController::class, 'showSchedule'])->name('schedule');
    Route::post('/schedule/store', [DashboardController::class, 'storeSchedule'])->name('schedule.store');
    Route::get('/reports', [DashboardController::class, 'showReports'])->name('reports');
    
    // مسارات إدارة أوقات الإتاحة
    Route::post('/availability/store', [DashboardController::class, 'storeAvailability'])->name('availability.store');
});

// مسارات الإدارة العامة
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // مسارات إدارة المحتوى
    Route::get('/content-management', [AdminController::class, 'showContentManagement'])->name('content.management');
    Route::post('/content/pages/store', [AdminController::class, 'storeContentPage'])->name('content.pages.store');
    Route::get('/content/pages/edit/{id}', [AdminController::class, 'editContentPage'])->name('content.pages.edit');
    Route::get('/content/pages/show/{id}', [AdminController::class, 'showContentPage'])->name('content.pages.show');
    
    Route::post('/content/blog/store', [AdminController::class, 'storeBlogPost'])->name('content.blog.store');
    Route::get('/content/blog/edit/{id}', [AdminController::class, 'editBlogPost'])->name('content.blog.edit');
    Route::get('/content/blog/show/{id}', [AdminController::class, 'showBlogPost'])->name('content.blog.show');
    
    Route::post('/content/faq/store', [AdminController::class, 'storeFaq'])->name('content.faq.store');
    Route::post('/content/testimonials/store', [AdminController::class, 'storeTestimonial'])->name('content.testimonials.store');
    Route::post('/content/banners/store', [AdminController::class, 'storeBanner'])->name('content.banners.store');
    
    // مسارات النسخ الاحتياطي
    Route::get('/backups', [AdminController::class, 'showBackups'])->name('backups');
    Route::get('/backups/database/download/{id}', [AdminController::class, 'downloadDatabaseBackup'])->name('backups.database.download');
    Route::get('/backups/database/restore/{id}', [AdminController::class, 'restoreDatabaseBackup'])->name('backups.database.restore');
    Route::get('/backups/files/download/{id}', [AdminController::class, 'downloadFilesBackup'])->name('backups.files.download');
    Route::get('/backups/files/restore/{id}', [AdminController::class, 'restoreFilesBackup'])->name('backups.files.restore');
    
    Route::post('/backups/schedule/database', [AdminController::class, 'scheduleDatabaseBackup'])->name('backups.schedule.database');
    Route::post('/backups/schedule/files', [AdminController::class, 'scheduleFilesBackup'])->name('backups.schedule.files');
    Route::post('/backups/settings/update', [AdminController::class, 'updateBackupSettings'])->name('backups.settings.update');
});
