<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    /**
     * الاشتراك في النشرة البريدية
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);

        // هنا يمكن إضافة منطق حفظ البريد الإلكتروني في قاعدة البيانات
        // على سبيل المثال:
        // NewsletterSubscriber::create(['email' => $request->email]);

        return redirect()->back()->with('success', 'تم الاشتراك في النشرة البريدية بنجاح');
    }
}
