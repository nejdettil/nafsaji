<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse as Middleware;
use Illuminate\Support\Facades\Log;

class AddQueuedCookiesToResponse extends Middleware
{
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
            $response = $next($request);
            
            // التحقق من أن الاستجابة ليست فارغة قبل إضافة ملفات تعريف الارتباط
            if ($response !== null) {
                foreach ($this->cookies->getQueuedCookies() as $cookie) {
                    try {
                        $response->headers->setCookie($cookie);
                    } catch (\Throwable $e) {
                        Log::error('خطأ في إضافة ملف تعريف ارتباط: ' . $e->getMessage());
                    }
                }
            } else {
                Log::error('الاستجابة فارغة في AddQueuedCookiesToResponse');
            }
            
            return $response;
        } catch (\Throwable $e) {
            Log::error('خطأ في AddQueuedCookiesToResponse: ' . $e->getMessage());
            return $next($request);
        }
    }
}
