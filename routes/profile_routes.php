<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// مسارات الملف الشخصي
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::get('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.update-notifications');
});
