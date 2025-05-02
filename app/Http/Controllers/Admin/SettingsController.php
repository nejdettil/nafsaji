<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * تحديث الإعدادات
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'enable_registration' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        // Clear cache
        Cache::forget('settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }
    public function updateGeneral(Request $request)
    {
        // مثال بسيط لحفظ إعدادات عامة
        $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
        ]);

        // تحديث الإعدادات في جدول settings
        Setting::updateOrCreate(['key' => 'site_name'], ['value' => $request->site_name]);
        Setting::updateOrCreate(['key' => 'contact_email'], ['value' => $request->contact_email]);

        return redirect()->back()->with('success', 'تم تحديث الإعدادات العامة بنجاح');
    }

}
