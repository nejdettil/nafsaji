// مسار dashboard الرئيسي
Route::get('/dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('specialist')) {
            return redirect()->route('specialist.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
    return redirect()->route('login');
})->name('dashboard');
