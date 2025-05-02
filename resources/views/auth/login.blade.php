@extends('layouts.app')

@section('title', 'تسجيل الدخول - نفسجي للتمكين النفسي')

@section('content')
    <div class="login-page">
        <!-- قسم الترويسة -->
        <section class="auth-header">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header">
                            <h1 class="main-title">تسجيل الدخول</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">تسجيل الدخول</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم نموذج تسجيل الدخول -->
        <section class="login-form-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-8">
                        <div class="auth-card">
                            <div class="auth-header">
                                <div class="logo">
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="نفسجي للتمكين النفسي" class="img-fluid">
                                </div>
                                <h2>مرحباً بعودتك</h2>
                                <p>قم بتسجيل الدخول للوصول إلى حسابك</p>
                            </div>

                            <div class="auth-body">
                                @if(session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div id="login-error-alert" class="alert alert-danger" style="display: none;"></div>

                                <form method="POST" action="{{ route('login') }}" class="auth-form" id="login-form">
                                    @csrf

                                    <div class="form-group">
                                        <label for="email">البريد الإلكتروني</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="أدخل بريدك الإلكتروني">
                                        </div>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="password">كلمة المرور</label>
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="forgot-password">نسيت كلمة المرور؟</a>
                                            @endif
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="أدخل كلمة المرور">
                                            <button type="button" class="btn btn-outline-secondary toggle-password" tabindex="-1">
                                                <i class="far fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                تذكرني
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block" id="login-button">
                                            <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                                        </button>
                                    </div>
                                </form>

                                <div class="social-login">
                                    <div class="divider">
                                        <span>أو</span>
                                    </div>

                                </div>
                            </div>

                            <div class="auth-footer">
                                <p>ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 d-none d-lg-block">
                        <div class="auth-image">
                            <img src="{{ asset('assets/images/login-illustration.svg') }}" alt="تسجيل الدخول" class="img-fluid">
                            <div class="auth-features">
                                <h3>مميزات حسابك في نفسجي</h3>
                                <ul>
                                    <li><i class="fas fa-check-circle"></i> حجز جلسات مع المختصين بسهولة</li>
                                    <li><i class="fas fa-check-circle"></i> متابعة تقدمك النفسي</li>
                                    <li><i class="fas fa-check-circle"></i> الوصول إلى محتوى حصري</li>
                                    <li><i class="fas fa-check-circle"></i> المشاركة في المجتمع الداعم</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('styles')
    <style>
        /* أنماط عامة للصفحة */
        .login-page section {
            padding: 40px 0;
        }

        /* قسم الترويسة */
        .auth-header {
            background-color: #f8f9fa;
            padding: 40px 0;
            text-align: center;
        }

        .main-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .breadcrumb {
            justify-content: center;
            background-color: transparent;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: #6a1b9a;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #666;
        }

        /* قسم نموذج تسجيل الدخول */
        .login-form-section {
            padding: 60px 0;
        }

        .auth-card {
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .auth-header {
            padding: 30px;
            text-align: center;
        }

        .auth-header .logo {
            margin-bottom: 20px;
        }

        .auth-header .logo img {
            max-height: 60px;
        }

        .auth-header h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }

        .auth-header p {
            color: #666;
            margin-bottom: 0;
        }

        .auth-body {
            padding: 0 30px 30px;
        }

        .auth-form .form-group {
            margin-bottom: 20px;
        }

        .auth-form label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .auth-form .input-group-text {
            background-color: #f8f9fa;
            border-left: none;
            color: #6a1b9a;
        }

        .auth-form .form-control {
            height: 48px;
            border-right: none;
        }

        .auth-form .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .auth-form .toggle-password {
            border-right: none;
            border-color: #ced4da;
            background-color: #fff;
        }

        .auth-form .toggle-password:hover {
            background-color: #f8f9fa;
        }

        .auth-form .form-check-label {
            color: #555;
        }

        .forgot-password {
            font-size: 14px;
            color: #6a1b9a;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .auth-form .btn-primary {
            background-color: #6a1b9a;
            border-color: #6a1b9a;
            height: 48px;
            font-weight: 600;
            width: 100%;
        }

        .auth-form .btn-primary:hover {
            background-color: #5c1786;
            border-color: #5c1786;
        }

        .social-login {
            margin-top: 30px;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #eee;
            z-index: 1;
        }

        .divider span {
            background-color: #fff;
            padding: 0 15px;
            position: relative;
            z-index: 2;
            color: #666;
        }

        .social-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-google, .btn-facebook {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 48px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-google {
            background-color: #fff;
            border: 1px solid #ddd;
            color: #333;
        }

        .btn-google:hover {
            background-color: #f8f9fa;
            color: #333;
        }

        .btn-facebook {
            background-color: #3b5998;
            border: 1px solid #3b5998;
            color: #fff;
        }

        .btn-facebook:hover {
            background-color: #344e86;
            border-color: #344e86;
            color: #fff;
        }

        .btn-google i, .btn-facebook i {
            margin-left: 10px;
            font-size: 18px;
        }

        .auth-footer {
            padding: 20px 30px;
            text-align: center;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
        }

        .auth-footer p {
            margin-bottom: 0;
            color: #555;
        }

        .auth-footer a {
            color: #6a1b9a;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        /* قسم الصورة التوضيحية */
        .auth-image {
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-image img {
            max-width: 100%;
            margin-bottom: 30px;
        }

        .auth-features {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }

        .auth-features h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .auth-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .auth-features li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .auth-features li i {
            color: #6a1b9a;
            margin-left: 10px;
        }

        /* مؤشر التحميل */
        .login-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .login-loading p {
            margin-top: 10px;
            font-weight: bold;
        }

        /* تصميم متجاوب */
        @media (max-width: 991px) {
            .auth-card {
                max-width: 500px;
                margin: 0 auto 30px;
            }
        }

        @media (max-width: 767px) {
            .login-page section {
                padding: 30px 0;
            }

            .auth-header {
                padding: 20px;
            }

            .auth-body {
                padding: 0 20px 20px;
            }

            .auth-footer {
                padding: 15px 20px;
            }

            .main-title {
                font-size: 28px;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $(document).ready(function() {
            // تبديل عرض كلمة المرور
            $('.toggle-password').on('click', function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // معالجة تقديم نموذج تسجيل الدخول
            $('#login-form').on('submit', function(e) {
                // منع السلوك الافتراضي للنموذج
                e.preventDefault();

                // التحقق من صحة النموذج
                if (!validateForm(this)) {
                    return false;
                }

                // الحصول على بيانات النموذج
                const formData = new FormData(this);
                const url = this.getAttribute('action');

                // إظهار مؤشر التحميل
                showLoading();

                // إرسال الطلب باستخدام Axios
                axios.post(url, formData)
                    .then(response => {
                        // معالجة الاستجابة الناجحة
                        if (response.data && response.data.redirect) {
                            // إعادة التوجيه إلى الصفحة المطلوبة
                            window.location.href = response.data.redirect;
                        } else if (response.request && response.request.responseURL) {
                            // إذا كانت الاستجابة تحتوي على URL إعادة توجيه (في حالة الاستجابة المباشرة)
                            window.location.href = response.request.responseURL;
                        } else {
                            // إعادة تحميل الصفحة كملاذ أخير
                            // التحقق من نوع المستخدم وتوجيهه إلى لوحة التحكم المناسبة
                            window.location.href = "{{ auth()->check() ? (auth()->user()->hasRole('admin') ? route('admin.dashboard') : (auth()->user()->hasRole('specialist') ? route('specialist.dashboard') : route('user.dashboard'))) : route('login') }}";
                        }
                    })
                    .catch(error => {
                        // معالجة الخطأ
                        hideLoading();

                        if (error.response) {
                            // الحصول على رسائل الخطأ من الاستجابة
                            const errors = error.response.data.errors || {};

                            // عرض رسائل الخطأ
                            Object.keys(errors).forEach(field => {
                                const input = document.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');

                                    // إضافة رسالة الخطأ
                                    let feedbackElement = input.nextElementSibling;
                                    if (!feedbackElement || !feedbackElement.classList.contains('invalid-feedback')) {
                                        feedbackElement = document.createElement('div');
                                        feedbackElement.className = 'invalid-feedback';
                                        input.parentNode.insertBefore(feedbackElement, input.nextSibling);
                                    }
                                    feedbackElement.textContent = errors[field][0];
                                }
                            });

                            // عرض رسالة خطأ عامة إذا لم تكن هناك أخطاء محددة
                            if (Object.keys(errors).length === 0) {
                                showErrorMessage(error.response.data.message || 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.');
                            }
                        } else {
                            // عرض رسالة خطأ عامة
                            showErrorMessage('حدث خطأ أثناء الاتصال بالخادم. يرجى التحقق من اتصالك بالإنترنت والمحاولة مرة أخرى.');
                        }
                    });
            });

            // إضافة مستمعي أحداث لإزالة رسائل الخطأ عند الكتابة
            $('.form-control').on('input', function() {
                $(this).removeClass('is-invalid');
                const feedbackElement = $(this).next('.invalid-feedback');
                if (feedbackElement.length) {
                    feedbackElement.text('');
                }
                $('#login-error-alert').hide();
            });
        });

        /**
         * التحقق من صحة النموذج
         * @param {HTMLFormElement} form - عنصر النموذج
         * @returns {boolean} - ما إذا كان النموذج صحيحاً
         */
        function validateForm(form) {
            let isValid = true;

            // التحقق من حقل البريد الإلكتروني
            const emailField = form.querySelector('input[name="email"]');
            if (emailField && !validateEmail(emailField.value)) {
                emailField.classList.add('is-invalid');

                // إضافة رسالة خطأ
                let feedbackElement = emailField.nextElementSibling;
                if (!feedbackElement || !feedbackElement.classList.contains('invalid-feedback')) {
                    feedbackElement = document.createElement('div');
                    feedbackElement.className = 'invalid-feedback';
                    emailField.parentNode.insertBefore(feedbackElement, emailField.nextSibling);
                }
                feedbackElement.textContent = 'يرجى إدخال عنوان بريد إلكتروني صحيح.';

                isValid = false;
            }

            // التحقق من حقل كلمة المرور
            const passwordField = form.querySelector('input[name="password"]');
            if (passwordField && passwordField.value.trim() === '') {
                passwordField.classList.add('is-invalid');

                // إضافة رسالة خطأ
                let feedbackElement = passwordField.nextElementSibling;
                if (!feedbackElement || !feedbackElement.classList.contains('invalid-feedback')) {
                    feedbackElement = document.createElement('div');
                    feedbackElement.className = 'invalid-feedback';
                    passwordField.parentNode.insertBefore(feedbackElement, passwordField.nextSibling);
                }
                feedbackElement.textContent = 'يرجى إدخال كلمة المرور.';

                isValid = false;
            }

            return isValid;
        }

        /**
         * التحقق من صحة البريد الإلكتروني
         * @param {string} email - البريد الإلكتروني
         * @returns {boolean} - ما إذا كان البريد الإلكتروني صحيحاً
         */
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        /**
         * إظهار مؤشر التحميل
         */
        function showLoading() {
            // التحقق من وجود مؤشر تحميل
            let loadingElement = document.querySelector('.login-loading');

            // إنشاء مؤشر تحميل إذا لم يكن موجوداً
            if (!loadingElement) {
                loadingElement = document.createElement('div');
                loadingElement.className = 'login-loading';
                loadingElement.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p>جاري تسجيل الدخول...</p>
            `;

                // إضافة مؤشر التحميل إلى الصفحة
                document.body.appendChild(loadingElement);
            } else {
                // إظهار مؤشر التحميل إذا كان موجوداً
                loadingElement.style.display = 'flex';
            }

            // تعطيل زر تسجيل الدخول
            const submitButton = document.querySelector('#login-button');
            if (submitButton) {
                submitButton.disabled = true;
            }
        }

        /**
         * إخفاء مؤشر التحميل
         */
        function hideLoading() {
            // إخفاء مؤشر التحميل
            const loadingElement = document.querySelector('.login-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }

            // إعادة تفعيل زر تسجيل الدخول
            const submitButton = document.querySelector('#login-button');
            if (submitButton) {
                submitButton.disabled = false;
            }
        }

        /**
         * عرض رسالة خطأ
         * @param {string} message - رسالة الخطأ
         */
        function showErrorMessage(message) {
            const alertElement = document.querySelector('#login-error-alert');
            if (alertElement) {
                alertElement.textContent = message;
                alertElement.style.display = 'block';
                alertElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    </script>
@endsection
