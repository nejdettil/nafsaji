@extends('layouts.app')

@section('title', 'تأكيد البريد الإلكتروني - نفسجي للتمكين النفسي')

@section('content')
<div class="verify-email-page">
    <!-- قسم الترويسة -->
    <section class="auth-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1 class="main-title">تأكيد البريد الإلكتروني</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item active" aria-current="page">تأكيد البريد الإلكتروني</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم تأكيد البريد الإلكتروني -->
    <section class="verify-email-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-card">
                        <div class="auth-header">
                            <div class="logo">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="نفسجي للتمكين النفسي" class="img-fluid">
                            </div>
                            <h2>تأكيد البريد الإلكتروني</h2>
                            <p>شكراً لتسجيلك في نفسجي للتمكين النفسي</p>
                        </div>
                        
                        <div class="auth-body">
                            <div class="verify-email-illustration">
                                <img src="{{ asset('assets/images/email-verification.svg') }}" alt="تأكيد البريد الإلكتروني" class="img-fluid">
                            </div>
                            
                            @if(session('status') == 'verification-link-sent')
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <span>تم إرسال رابط تأكيد جديد إلى عنوان بريدك الإلكتروني.</span>
                                </div>
                            @endif
                            
                            <div class="verify-email-message">
                                <p>قبل البدء، هل يمكنك التحقق من بريدك الإلكتروني بالنقر على الرابط الذي أرسلناه إليك؟ إذا لم تستلم البريد الإلكتروني، فسنرسل لك رابطاً آخر.</p>
                            </div>
                            
                            <form method="POST" action="{{ route('verification.send') }}" class="auth-form">
                                @csrf
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-paper-plane"></i> إعادة إرسال رابط التأكيد
                                    </button>
                                </div>
                            </form>
                            
                            <div class="auth-info">
                                <div class="auth-info-item">
                                    <div class="auth-info-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="auth-info-content">
                                        <h4>تحقق من بريدك الإلكتروني</h4>
                                        <p>تأكد من التحقق من مجلد البريد الوارد والبريد غير المرغوب فيه للعثور على رسالة التأكيد.</p>
                                    </div>
                                </div>
                                
                                <div class="auth-info-item">
                                    <div class="auth-info-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="auth-info-content">
                                        <h4>لماذا التأكيد ضروري؟</h4>
                                        <p>تأكيد بريدك الإلكتروني يساعدنا في حماية حسابك ويتيح لك الوصول إلى جميع ميزات المنصة.</p>
                                    </div>
                                </div>
                                
                                <div class="auth-info-item">
                                    <div class="auth-info-icon">
                                        <i class="fas fa-question-circle"></i>
                                    </div>
                                    <div class="auth-info-content">
                                        <h4>تواجه مشكلة؟</h4>
                                        <p>إذا كنت تواجه أي مشكلة في تأكيد بريدك الإلكتروني، يرجى <a href="{{ route('contact') }}">التواصل مع فريق الدعم</a>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="auth-footer">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-link">تسجيل الخروج</button>
                            </form>
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
    .verify-email-page section {
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
    
    /* قسم تأكيد البريد الإلكتروني */
    .verify-email-section {
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
    
    .verify-email-illustration {
        text-align: center;
        margin: 20px 0;
    }
    
    .verify-email-illustration img {
        max-width: 200px;
    }
    
    .alert {
        display: flex;
        align-items: center;
    }
    
    .alert i {
        margin-left: 10px;
        font-size: 18px;
    }
    
    .verify-email-message {
        text-align: center;
        margin: 20px 0;
    }
    
    .verify-email-message p {
        color: #555;
    }
    
    .auth-form .form-group {
        margin-bottom: 20px;
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
    
    .auth-info {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
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
    
    .auth-info-content a {
        color: #6a1b9a;
        text-decoration: none;
        font-weight: 600;
    }
    
    .auth-info-content a:hover {
        text-decoration: underline;
    }
    
    .auth-footer {
        padding: 20px 30px;
        text-align: center;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }
    
    .btn-link {
        background: none;
        border: none;
        color: #6a1b9a;
        font-weight: 600;
        text-decoration: none;
        padding: 0;
        cursor: pointer;
    }
    
    .btn-link:hover {
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
        .verify-email-page section {
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
