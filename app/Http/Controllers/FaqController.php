<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * عرض صفحة الأسئلة الشائعة
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // يمكن استرجاع الأسئلة الشائعة من قاعدة البيانات هنا
        // $faqs = Faq::all();
        
        return view('faq');
    }
}
