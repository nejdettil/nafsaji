<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class CookieErrorHandler
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
        try {
            $response = $next($request);
            
            // إذا كانت الاستجابة فارغة، نقوم بإنشاء استجابة جديدة
            if ($response === null) {
                Log::warning('تم اكتشاف استجابة فارغة في CookieErrorHandler، إنشاء استجابة جديدة');
                $response = response('', 200);
            }
            
            return $response;
        } catch (\Throwable $e) {
            Log::error('خطأ في معالجة الطلب: ' . $e->getMessage());
            
            // إنشاء استجابة جديدة في حالة حدوث خطأ
            return response('', 200);
        }
    }
}
