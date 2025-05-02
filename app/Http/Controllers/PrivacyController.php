<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PrivacyController extends Controller
{
    /**
     * عرض صفحة سياسة الخصوصية
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('privacy');
    }
}
