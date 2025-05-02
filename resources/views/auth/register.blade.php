@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - نفسجي للتمكين النفسي')

@section('content')
<div class="register-page">
    <!-- قسم الترويسة -->
    <section class="auth-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1 class="main-title">إنشاء حساب جديد</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item active" aria-current="page">إنشاء حساب</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم نموذج إنشاء الحساب -->
    <section class="register-form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="auth-image">
                        <img src="{{ asset('assets/images/register-illustration.svg') }}" alt="إنشاء حساب" class="img-fluid">
                        <div class="auth-features">
                            <h3>لماذا تنضم إلى نفسجي؟</h3>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> الوصول إلى أفضل المختصين في المجال النفسي</li>
                                <li><i class="fas fa-check-circle"></i> جلسات استشارية مخصصة لاحتياجاتك</li>
                                <li><i class="fas fa-check-circle"></i> متابعة مستمرة لتقدمك النفسي</li>
                                <li><i class="fas fa-check-circle"></i> محتوى تثقيفي وتوعوي حصري</li>
                                <li><i class="fas fa-check-circle"></i> مجتمع داعم لمساندتك في رحلتك</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7 col-md-10">
                    <div class="auth-card">
                        <div class="auth-header">
                            <div class="logo">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="نفسجي للتمكين النفسي" class="img-fluid">
                            </div>
                            <h2>انضم إلينا اليوم</h2>
                            <p>أنشئ حسابك للوصول إلى خدمات نفسجي للتمكين النفسي</p>
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
                            
                            <form method="POST" action="{{ route('register') }}" class="auth-form">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">الاسم الأول</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus placeholder="الاسم الأول">
                                            </div>
                                            @error('first_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">الاسم الأخير</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" placeholder="الاسم الأخير">
                                            </div>
                                            @error('last_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="أدخل بريدك الإلكتروني">
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" placeholder="أدخل رقم هاتفك">
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">كلمة المرور</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="أدخل كلمة المرور">
                                                <button type="button" class="btn btn-outline-secondary toggle-password" tabindex="-1">
                                                    <i class="far fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل وتتضمن حروفاً وأرقاماً ورموزاً.
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password-confirm">تأكيد كلمة المرور</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="أعد إدخال كلمة المرور">
                                                <button type="button" class="btn btn-outline-secondary toggle-confirm-password" tabindex="-1">
                                                    <i class="far fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="user_type">نوع الحساب</label>
                                    <div class="account-type-selector">
                                        <div class="form-check form-check-inline account-type-option">
                                            <input class="form-check-input" type="radio" name="user_type" id="user_type_client" value="client" {{ old('user_type') == 'client' ? 'checked' : '' }} checked>
                                            <label class="form-check-label" for="user_type_client">
                                                <div class="account-type-icon">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="account-type-details">
                                                    <span class="account-type-name">مستخدم</span>
                                                    <span class="account-type-desc">أبحث عن استشارات نفسية</span>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div class="form-check form-check-inline account-type-option">
                                            <input class="form-check-input" type="radio" name="user_type" id="user_type_specialist" value="specialist" {{ old('user_type') == 'specialist' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="user_type_specialist">
                                                <div class="account-type-icon">
                                                    <i class="fas fa-user-md"></i>
                                                </div>
                                                <div class="account-type-details">
                                                    <span class="account-type-name">مختص</span>
                                                    <span class="account-type-desc">أقدم خدمات استشارية</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group specialist-fields" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <span>سيتم مراجعة طلبك كمختص من قبل فريقنا، وسيتم التواصل معك لاستكمال البيانات المطلوبة.</span>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="specialization">التخصص</label>
                                        <select class="form-select @error('specialization') is-invalid @enderror" id="specialization" name="specialization">
                                            <option value="">اختر تخصصك</option>
                                            <option value="clinical_psychology" {{ old('specialization') == 'clinical_psychology' ? 'selected' : '' }}>علم النفس السريري</option>
                                            <option value="counseling_psychology" {{ old('specialization') == 'counseling_psychology' ? 'selected' : '' }}>علم النفس الإرشادي</option>
                                            <option value="psychiatry" {{ old('specialization') == 'psychiatry' ? 'selected' : '' }}>الطب النفسي</option>
                                            <option value="family_therapy" {{ old('specialization') == 'family_therapy' ? 'selected' : '' }}>العلاج الأسري</option>
                                            <option value="child_psychology" {{ old('specialization') == 'child_psychology' ? 'selected' : '' }}>علم نفس الطفل</option>
                                            <option value="other" {{ old('specialization') == 'other' ? 'selected' : '' }}>أخرى</option>
                                        </select>
                                        @error('specialization')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="experience_years">سنوات الخبرة</label>
                                        <select class="form-select @error('experience_years') is-invalid @enderror" id="experience_years" name="experience_years">
                                            <option value="">اختر سنوات الخبرة</option>
                                            <option value="0-2" {{ old('experience_years') == '0-2' ? 'selected' : '' }}>أقل من 2 سنوات</option>
                                            <option value="3-5" {{ old('experience_years') == '3-5' ? 'selected' : '' }}>3-5 سنوات</option>
                                            <option value="6-10" {{ old('experience_years') == '6-10' ? 'selected' : '' }}>6-10 سنوات</option>
                                            <option value="10+" {{ old('experience_years') == '10+' ? 'selected' : '' }}>أكثر من 10 سنوات</option>
                                        </select>
                                        @error('experience_years')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="terms">
                                            أوافق على <a href="{{ route('terms') }}" target="_blank">شروط الاستخدام</a> و <a href="{{ route('privacy') }}" target="_blank">سياسة الخصوصية</a>
                                        </label>
                                        @error('terms')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-user-plus"></i> إنشاء حساب
                                    </button>
                                </div>
                            </form>
                            
                            <div class="social-login">
                                <div class="divider">
                                    <span>أو</span>
                                </div>
                                
                                <div class="social-buttons">
                                    <a href="{{ route('login.google') }}" class="btn btn-google">
                                        <i class="fab fa-google"></i> التسجيل بواسطة Google
                                    </a>
                                    <a href="{{ route('login.facebook') }}" class="btn btn-facebook">
                                        <i class="fab fa-facebook-f"></i> التسجيل بواسطة Facebook
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="auth-footer">
                            <p>لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
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
    .register-page section {
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
    
    /* قسم نموذج إنشاء الحساب */
    .register-form-section {
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
    
    .auth-form .toggle-password, .auth-form .toggle-confirm-password {
        border-right: none;
        border-color: #ced4da;
        background-color: #fff;
    }
    
    .auth-form .toggle-password:hover, .auth-form .toggle-confirm-password:hover {
        background-color: #f8f9fa;
    }
    
    .auth-form .form-text {
        font-size: 12px;
    }
    
    .auth-form .form-check-label {
        color: #555;
    }
    
    .auth-form .form-check-label a {
        color: #6a1b9a;
        text-decoration: none;
    }
    
    .auth-form .form-check-label a:hover {
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
    
    /* نوع الحساب */
    .account-type-selector {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    
    .account-type-option {
        flex: 1;
        margin: 0 5px;
    }
    
    .account-type-option .form-check-input {
        display: none;
    }
    
    .account-type-option .form-check-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px;
        border: 2px solid #eee;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .account-type-option .form-check-input:checked + .form-check-label {
        border-color: #6a1b9a;
        background-color: #f0e6f5;
    }
    
    .account-type-icon {
        font-size: 24px;
        color: #6a1b9a;
        margin-bottom: 10px;
    }
    
    .account-type-details {
        text-align: center;
    }
    
    .account-type-name {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .account-type-desc {
        display: block;
        font-size: 12px;
        color: #666;
    }
    
    /* حقول المختص */
    .specialist-fields {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .specialist-fields .alert {
        margin-bottom: 15px;
    }
    
    .specialist-fields .alert i {
        margin-left: 5px;
    }
    
    /* التسجيل بواسطة وسائل التواصل الاجتماعي */
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
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .auth-card {
            max-width: 700px;
            margin: 0 auto 30px;
        }
    }
    
    @media (max-width: 767px) {
        .register-page section {
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
        
        .account-type-selector {
            flex-direction: column;
        }
        
        .account-type-option {
            margin: 5px 0;
        }
    }
</style>
@endsection

@section('scripts')
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
        
        // تبديل عرض تأكيد كلمة المرور
        $('.toggle-confirm-password').on('click', function() {
            const confirmPasswordField = $('#password-confirm');
            const icon = $(this).find('i');
            
            if (confirmPasswordField.attr('type') === 'password') {
                confirmPasswordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                confirmPasswordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        
        // إظهار/إخفاء حقول المختص
        $('input[name="user_type"]').on('change', function() {
            if ($(this).val() === 'specialist') {
                $('.specialist-fields').slideDown();
                $('#specialization, #experience_years').prop('required', true);
            } else {
                $('.specialist-fields').slideUp();
                $('#specialization, #experience_years').prop('required', false);
            }
        });
        
        // تهيئة حقول المختص عند تحميل الصفحة
        if ($('#user_type_specialist').is(':checked')) {
            $('.specialist-fields').show();
            $('#specialization, #experience_years').prop('required', true);
        }
        
        // التحقق من تطابق كلمات المرور
        $('#password, #password-confirm').on('keyup', function() {
            if ($('#password').val() !== $('#password-confirm').val()) {
                $('#password-confirm').addClass('is-invalid');
            } else {
                $('#password-confirm').removeClass('is-invalid');
            }
        });
        
        // التحقق من قوة كلمة المرور
        $('#password').on('keyup', function() {
            const password = $(this).val();
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;
            
            if (strength < 3) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // تحقق من صحة النموذج قبل الإرسال
        $('.auth-form').on('submit', function(e) {
            let isValid = true;
            
            // التحقق من تطابق كلمات المرور
            if ($('#password').val() !== $('#password-confirm').val()) {
                $('#password-confirm').addClass('is-invalid');
                isValid = false;
            }
            
            // التحقق من قوة كلمة المرور
            const password = $('#password').val();
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;
            
            if (strength < 3) {
                $('#password').addClass('is-invalid');
                isValid = false;
            }
            
            // التحقق من حقول المختص إذا تم اختيار نوع الحساب كمختص
            if ($('#user_type_specialist').is(':checked')) {
                if (!$('#specialization').val()) {
                    $('#specialization').addClass('is-invalid');
                    isValid = false;
                }
                
                if (!$('#experience_years').val()) {
                    $('#experience_years').addClass('is-invalid');
                    isValid = false;
                }
            }
            
            return isValid;
        });
        
        // إزالة فئة الخطأ عند الكتابة
        $('.form-control').on('input', function() {
            $(this).removeClass('is-invalid');
        });
        
        // إزالة فئة الخطأ عند تغيير القائمة المنسدلة
        $('.form-select').on('change', function() {
            $(this).removeClass('is-invalid');
        });
    });
</script>
@endsection
