<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class FixCookieResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // التأكد من أن الاستجابة ليست فارغة
        if ($response instanceof Response) {
            return $response;
        }

        // إذا كانت الاستجابة فارغة، إنشاء استجابة جديدة
        $newResponse = new \Illuminate\Http\Response();
        
        // نقل محتوى الاستجابة الأصلية إلى الاستجابة الجديدة إذا كان ذلك ممكناً
        if ($response !== null) {
            $newResponse->setContent($response);
        }
        
        // إضافة ملفات تعريف الارتباط المضافة إلى الطابور
        foreach (Cookie::getQueuedCookies() as $cookie) {
            $newResponse->headers->setCookie($cookie);
        }
        
        return $newResponse;
    }
}
