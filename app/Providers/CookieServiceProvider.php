<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\CookieJar;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;

class CookieServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('cookie', function ($app) {
            $config = $app->make('config')->get('session');

            return (new CookieJar)->setDefaultPathAndDomain(
                $config['path'], $config['domain'], $config['secure'], $config['same_site'] ?? null
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // تسجيل middleware إصلاح استجابة ملفات تعريف الارتباط
        $this->app['router']->aliasMiddleware('fix.cookie', \App\Http\Middleware\FixCookieResponse::class);
        
        // إضافة middleware إلى مجموعة web
        $this->app['router']->pushMiddlewareToGroup('web', \App\Http\Middleware\FixCookieResponse::class);
    }
}
