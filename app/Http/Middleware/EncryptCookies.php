<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
    
    /**
     * تجاوز طريقة handle لمعالجة مشكلة الكائن الفارغ
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (\Throwable $e) {
            // إذا حدث خطأ، نسجله ونستمر بدون تشفير ملفات تعريف الارتباط
            \Illuminate\Support\Facades\Log::error('خطأ في تشفير ملفات تعريف الارتباط: ' . $e->getMessage());
            return $next($request);
        }
    }
}
