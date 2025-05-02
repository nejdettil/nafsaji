@extends('layouts.app')

@section('title', 'استعادة كلمة المرور - نفسجي للتمكين النفسي')

@section('content')
<div class="forgot-password-page">
    <!-- قسم الترويسة -->
    <section class="auth-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1 class="main-title">استعادة كلمة المرور</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('login') }}">تسجيل الدخول</a></li>
                                <li class="breadcrumb-item active" aria-current="page">استعادة كلمة المرور</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم نموذج استعادة كلمة المرور -->
    <section class="forgot-password-form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-card">
                        <div class="auth-header">
                            <div class="logo">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="نفسجي للتمكين النفسي" class="img-fluid">
                            </div>
                            <h2>استعادة كلمة المرور</h2>
                            <p>أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور</p>
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
                            
                            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
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
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-paper-plane"></i> إرسال رابط إعادة التعيين
                                    </button>
                                </div>
                            </form>
                            
                            <div class="forgot-password-illustration">
                                <img src="{{ asset('assets/images/forgot-password-illustration.svg') }}" alt="استعادة كلمة المرور" class="img-fluid">
                            </div>
                            
                            <div class="auth-info">
                                <div class="auth-info-item">
                                    <div class="auth-info-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="auth-info-content">
                                        <h4>تحقق من بريدك الإلكتروني</h4>
                                        <p>سنرسل لك رابطاً لإعادة تعيين كلمة المرور. تأكد من التحقق من مجلد البريد الوارد والبريد غير المرغوب فيه.</p>
                                    </div>
                                </div>
                                
                                <div class="auth-info-item">
                                    <div class="auth-info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="auth-info-content">
                                        <h4>صلاحية الرابط</h4>
                                        <p>رابط إعادة تعيين كلمة المرور صالح لمدة 60 دقيقة فقط. إذا انتهت صلاحية الرابط، يمكنك طلب رابط جديد.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="auth-footer">
                            <p>تذكرت كلمة المرور؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
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
    .forgot-password-page section {
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
    
    /* قسم نموذج استعادة كلمة المرور */
    .forgot-password-form-section {
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
    
    .forgot-password-illustration {
        text-align: center;
        margin: 30px 0;
    }
    
    .forgot-password-illustration img {
        max-width: 250px;
    }
    
    .auth-info {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
    }
    
    .auth-info-item {
        display: flex;
        margin-bottom: 15px;
    }
    
    .auth-info-item:last-child {
        margin-bottom: 0;
    }
    
    .auth-info-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        background-color: #e0d0ea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6a1b9a;
        margin-left: 15px;
    }
    
    .auth-info-content h4 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .auth-info-content p {
        font-size: 14px;
        color: #666;
        margin-bottom: 0;
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
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .auth-card {
            max-width: 500px;
            margin: 0 auto 30px;
        }
    }
    
    @media (max-width: 767px) {
        .forgot-password-page section {
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
        
        .auth-info-item {
            flex-direction: column;
        }
        
        .auth-info-icon {
            margin-bottom: 10px;
            margin-left: 0;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحقق من صحة النموذج قبل الإرسال
        $('.auth-form').on('submit', function(e) {
            const email = $('#email').val();
            let isValid = true;
            
            // التحقق من البريد الإلكتروني
            if (!email || !email.includes('@')) {
                $('#email').addClass('is-invalid');
                isValid = false;
            } else {
                $('#email').removeClass('is-invalid');
            }
            
            return isValid;
        });
        
        // إزالة فئة الخطأ عند الكتابة
        $('.form-control').on('input', function() {
            $(this).removeClass('is-invalid');
        });
    });
</script>
@endsection
