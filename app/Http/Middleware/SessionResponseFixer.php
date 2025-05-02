<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class SessionResponseFixer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // تنفيذ الطلب والحصول على الاستجابة
        $response = $next($request);
        
        // التحقق من أن الاستجابة ليست فارغة
        if ($response === null) {
            Log::warning('تم اكتشاف استجابة فارغة في SessionResponseFixer، إنشاء استجابة جديدة');
            $response = response('', 200);
        }
        
        // التأكد من أن الجلسة تم حفظها بشكل صحيح
        try {
            if (Session::isStarted()) {
                Session::save();
            }
        } catch (\Throwable $e) {
            Log::error('خطأ في حفظ الجلسة: ' . $e->getMessage());
        }
        
        // إضافة ملفات تعريف الارتباط المضافة إلى الصف إلى الاستجابة
        try {
            foreach (Cookie::getQueuedCookies() as $cookie) {
                if (method_exists($response, 'withCookie')) {
                    $response = $response->withCookie($cookie);
                } elseif (method_exists($response, 'headers') && method_exists($response->headers, 'setCookie')) {
                    $response->headers->setCookie($cookie);
                }
            }
        } catch (\Throwable $e) {
            Log::error('خطأ في إضافة ملفات تعريف الارتباط: ' . $e->getMessage());
        }
        
        return $response;
    }
}
