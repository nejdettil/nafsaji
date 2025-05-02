<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Foundation\Application;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // تسجيل تعيين نموذج role بأحرف صغيرة إلى نموذج Role بحرف كبير
        $this->app->bind('role', function (Application $app) {
            return $app->make(\App\Models\Role::class);
        });
    }
}
