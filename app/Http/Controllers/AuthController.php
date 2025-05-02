<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة طلب تسجيل الدخول
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // استخدام واجهة Cookie بدلاً من محاولة استخدام كائن Response مباشرة
            if ($request->filled('remember')) {
                Cookie::queue('remember_web', true, 43200); // 30 يوم
            }

            // التحقق مما إذا كان الطلب يتوقع استجابة JSON
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse([
                    'success' => true,
                    'redirect' => route('dashboard')
                ]);
            }

            // إعادة التوجيه المباشر للطلبات العادية
            return redirect()->intended('dashboard');
        }

        // التحقق مما إذا كان الطلب يتوقع استجابة JSON
        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'email' => ['بيانات الاعتماد المقدمة غير متطابقة مع سجلاتنا.']
                ]
            ], 422);
        }

        return back()->withErrors([
            'email' => 'بيانات الاعتماد المقدمة غير متطابقة مع سجلاتنا.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * عرض صفحة التسجيل
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * معالجة طلب التسجيل
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        // التحقق مما إذا كان الطلب يتوقع استجابة JSON
        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        // إعادة التوجيه المباشر للطلبات العادية
        return redirect('dashboard');
    }

    /**
     * تسجيل الخروج
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // إزالة ملف تعريف الارتباط الخاص بتذكر المستخدم
        Cookie::queue(Cookie::forget('remember_web'));

        // التحقق مما إذا كان الطلب يتوقع استجابة JSON
        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'redirect' => route('home')
            ]);
        }

        // إعادة التوجيه المباشر للطلبات العادية
        return redirect('/');
    }

    /**
     * عرض صفحة نسيت كلمة المرور
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * عرض صفحة إعادة تعيين كلمة المرور
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showResetPasswordForm(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * توجيه المستخدم إلى صفحة تسجيل الدخول بواسطة Google
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * معالجة استجابة Google بعد تسجيل الدخول
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // إنشاء مستخدم جديد
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // تحديث معلومات Google للمستخدم الحالي
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($user);

            // التحقق مما إذا كان الطلب يتوقع استجابة JSON
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse([
                    'success' => true,
                    'redirect' => route('dashboard')
                ]);
            }

            // إعادة التوجيه المباشر للطلبات العادية
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            // التحقق مما إذا كان الطلب يتوقع استجابة JSON
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تسجيل الدخول بواسطة Google. يرجى المحاولة مرة أخرى.'
                ], 500);
            }

            return redirect('login')->with('error', 'حدث خطأ أثناء تسجيل الدخول بواسطة Google. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * توجيه المستخدم إلى صفحة تسجيل الدخول بواسطة Facebook
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * معالجة استجابة Facebook بعد تسجيل الدخول
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $user = User::where('email', $facebookUser->getEmail())->first();

            if (!$user) {
                // إنشاء مستخدم جديد
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                ]);
            } else {
                // تحديث معلومات Facebook للمستخدم الحالي
                $user->update([
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                ]);
            }

            Auth::login($user);

            // التحقق مما إذا كان الطلب يتوقع استجابة JSON
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse([
                    'success' => true,
                    'redirect' => route('dashboard')
                ]);
            }

            // إعادة التوجيه المباشر للطلبات العادية
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            // التحقق مما إذا كان الطلب يتوقع استجابة JSON
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تسجيل الدخول بواسطة Facebook. يرجى المحاولة مرة أخرى.'
                ], 500);
            }

            return redirect('login')->with('error', 'حدث خطأ أثناء تسجيل الدخول بواسطة Facebook. يرجى المحاولة مرة أخرى.');
        }
    }
}
