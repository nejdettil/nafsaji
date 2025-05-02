<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_timezone' => config('app.timezone'),
            'app_locale' => config('app.locale'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];
        
        return view('admin.settings.index', compact('settings'));
    }
    
    /**
     * تحديث الإعدادات العامة
     */
    public function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_timezone' => 'required|string',
            'app_locale' => 'required|string|in:ar,en',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // تحديث ملف .env
        $this->updateEnvironmentFile([
            'APP_NAME' => $request->app_name,
            'APP_URL' => $request->app_url,
            'APP_TIMEZONE' => $request->app_timezone,
            'APP_LOCALE' => $request->app_locale,
        ]);

        // إعادة تحميل الإعدادات
        Artisan::call('config:clear');
        
        return redirect()->route('admin.settings')
            ->with('success', 'تم تحديث الإعدادات العامة بنجاح');
    }
    
    /**
     * تحديث إعدادات البريد الإلكتروني
     */
    public function updateMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_driver' => 'required|string|in:smtp,sendmail,mailgun,ses,postmark,log,array',
            'mail_host' => 'required_if:mail_driver,smtp|string',
            'mail_port' => 'required_if:mail_driver,smtp|numeric',
            'mail_username' => 'required_if:mail_driver,smtp|string',
            'mail_password' => 'required_if:mail_driver,smtp|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // تحديث ملف .env
        $this->updateEnvironmentFile([
            'MAIL_MAILER' => $request->mail_driver,
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_USERNAME' => $request->mail_username,
            'MAIL_PASSWORD' => $request->mail_password,
            'MAIL_ENCRYPTION' => $request->mail_encryption,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => $request->mail_from_name,
        ]);

        // إعادة تحميل الإعدادات
        Artisan::call('config:clear');
        
        return redirect()->route('admin.settings')
            ->with('success', 'تم تحديث إعدادات البريد الإلكتروني بنجاح');
    }
    
    /**
     * تحديث إعدادات الدفع
     */
    public function updatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_gateway' => 'required|string|in:stripe,paypal,tap,myfatoorah',
            'payment_mode' => 'required|string|in:live,sandbox',
            'payment_api_key' => 'required|string',
            'payment_secret_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // تحديث ملف .env
        $this->updateEnvironmentFile([
            'PAYMENT_GATEWAY' => $request->payment_gateway,
            'PAYMENT_MODE' => $request->payment_mode,
            'PAYMENT_API_KEY' => $request->payment_api_key,
            'PAYMENT_SECRET_KEY' => $request->payment_secret_key,
        ]);

        // إعادة تحميل الإعدادات
        Artisan::call('config:clear');
        
        return redirect()->route('admin.settings')
            ->with('success', 'تم تحديث إعدادات الدفع بنجاح');
    }
    
    /**
     * تحديث إعدادات وسائل التواصل الاجتماعي
     */
    public function updateSocial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facebook_app_id' => 'nullable|string',
            'facebook_app_secret' => 'nullable|string',
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // تحديث ملف .env
        $this->updateEnvironmentFile([
            'FACEBOOK_APP_ID' => $request->facebook_app_id,
            'FACEBOOK_APP_SECRET' => $request->facebook_app_secret,
            'GOOGLE_CLIENT_ID' => $request->google_client_id,
            'GOOGLE_CLIENT_SECRET' => $request->google_client_secret,
        ]);

        // إعادة تحميل الإعدادات
        Artisan::call('config:clear');
        
        return redirect()->route('admin.settings')
            ->with('success', 'تم تحديث إعدادات وسائل التواصل الاجتماعي بنجاح');
    }
    
    /**
     * تحديث ملف .env
     */
    private function updateEnvironmentFile($data)
    {
        $path = base_path('.env');
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            foreach ($data as $key => $value) {
                // تحديث القيمة إذا كانت موجودة
                if (strpos($content, "{$key}=") !== false) {
                    $content = preg_replace("/{$key}=(.*)/", "{$key}={$value}", $content);
                } else {
                    // إضافة القيمة إذا لم تكن موجودة
                    $content .= "\n{$key}={$value}";
                }
            }
            
            file_put_contents($path, $content);
        }
    }
}
