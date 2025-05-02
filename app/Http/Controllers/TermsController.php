<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TermsController extends Controller
{
    /**
     * عرض صفحة الشروط والأحكام
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('terms');
    }
}
