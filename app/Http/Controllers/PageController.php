<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * عرض صفحة الشروط والأحكام
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * عرض صفحة سياسة الخصوصية
     */
    public function privacy()
    {
        return view('pages.privacy');
    }
}
