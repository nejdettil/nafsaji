<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * عرض صفحة الاتصال
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('contact');
    }

    /**
     * حفظ بيانات نموذج الاتصال
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // هنا يمكن إضافة منطق حفظ بيانات الاتصال في قاعدة البيانات
        // على سبيل المثال:
        // Contact::create($request->all());

        return redirect()->back()->with('success', 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً');
    }

    /**
     * إرسال رسالة الاتصال
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        // هذه الدالة يمكن استخدامها لإرسال البريد الإلكتروني
        // على سبيل المثال:
        // Mail::to('info@nafsaji.com')->send(new ContactFormMail($request->all()));

        return $this->store($request);
    }
}
