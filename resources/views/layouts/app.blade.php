<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- إضافة وسم meta لرمز CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نفسجي - للتمكين النفسي')</title>

    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ✅ jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Google Fonts - Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @yield('styles')
</head>
<body>
<!-- Header -->
<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="نفسجي" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services*') ? 'active' : '' }}" href="{{ route('services.index') }}">الخدمات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('specialists*') ? 'active' : '' }}" href="{{ route('specialists.index') }}">المختصين</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('packages*') ? 'active' : '' }}" href="{{ route('packages.index') }}">الباقات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">من نحن</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">اتصل بنا</a>
                    </li>
                </ul>

                <div class="d-flex">
                    @auth
                        <div class="dropdown">
                            <a class="btn btn-outline-primary dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                @role('admin')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                                @endrole

                                @role('specialist')
                                <li><a class="dropdown-item" href="{{ route('specialist.dashboard') }}">لوحة التحكم</a></li>
                                @endrole

                                @role('user')
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">لوحة التحكم</a></li>
                                @endrole

                                @role('admin')
                                <a class="dropdown-item" href="{{ route('admin.users.edit', Auth::id()) }}">
                                    <i class="fas fa-user-cog"></i> تعديل الحساب
                                </a>
                                @elserole('specialist')
                                <a class="dropdown-item" href="{{ route('specialist.profile') }}">
                                    <i class="fas fa-user-cog"></i> تعديل الحساب
                                </a>
                                @elserole('user')
                                <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                    <i class="fas fa-user-cog"></i> تعديل الحساب
                                </a>
                                @endrole
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <!-- استخدام مسار تسجيل الخروج الجديد الذي يدعم طريقة GET -->
                                    <a class="dropdown-item" href="{{ route('logout.get') }}">تسجيل الخروج</a>

                                    <!-- الاحتفاظ بنموذج POST كاحتياط -->
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">إنشاء حساب</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- Main Content -->
<main class="main-content">
    @yield('content')
</main>

<!-- Footer -->
<footer class="footer bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <img src="{{ asset('assets/images/logo-white.png') }}" alt="نفسجي" height="60" class="mb-3">
                <p>نفسجي هي منصة متخصصة في تقديم خدمات الدعم والاستشارات النفسية عن بعد، تهدف إلى تمكين الأفراد نفسياً وتحسين جودة حياتهم.</p>
                <div class="social-links mt-3">
                    <a href="https://www.facebook.com/people/Nafsaji/100089054826728/" target="_blank" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/nafsajii" target="_blank" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-md-0">
                <h5 class="text-uppercase mb-4">روابط سريعة</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white text-decoration-none">الرئيسية</a></li>
                    <li class="mb-2"><a href="{{ route('services.index') }}" class="text-white text-decoration-none">الخدمات</a></li>
                    <li class="mb-2"><a href="{{ route('specialists.index') }}" class="text-white text-decoration-none">المختصين</a></li>
                    <li class="mb-2"><a href="{{ route('packages.index') }}" class="text-white text-decoration-none">الباقات</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-white text-decoration-none">من نحن</a></li>
                    <li><a href="{{ route('contact') }}" class="text-white text-decoration-none">اتصل بنا</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">الخدمات</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">الاستشارات النفسية</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">العلاج النفسي</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">الإرشاد الأسري</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">الإرشاد الزواجي</a></li>
                    <li><a href="#" class="text-white text-decoration-none">تنمية المهارات الشخصية</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-6">
                <h5 class="text-uppercase mb-4">اتصل بنا</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> سوريا، دمشق</li>
                    <li class="mb-2"><i class="fas fa-phone-alt me-2"></i> +963 XXXXXXXX</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@nafsaji.com</li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Copyright -->
<div class="copyright bg-dark text-white py-3 border-top border-secondary">
    <div class="container text-center">
        <p class="mb-0">&copy; {{ date('Y') }} نفسجي. جميع الحقوق محفوظة.</p>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- تسجيل Service Worker -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('تم تسجيل Service Worker بنجاح:', registration.scope);
                })
                .catch(function(error) {
                    console.error('فشل تسجيل Service Worker:', error);
                });
        });
    }
</script>

<!-- الحل الشامل لمشاكل JavaScript -->
<script src="{{ asset('assets/js/main.js') }}" defer></script>

<!-- إضافة ملف mobile.js لدعم تجربة المستخدم على الأجهزة المحمولة -->
<script src="{{ asset('assets/js/mobile.js') }}" defer></script>

<!-- معالجة أي روابط تسجيل خروج إضافية -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التعامل مع أي روابط تسجيل خروج إضافية في الصفحة
        const additionalLogoutLinks = document.querySelectorAll('a[href$="/logout"]:not([href$="logout.get"])');

        additionalLogoutLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('logout.get') }}";
            });
        });

        // إصلاح مشكلة الرابط في شريط العنوان
        if (window.location.pathname.endsWith('/logout') && !window.location.pathname.endsWith('logout.get')) {
            // إعادة توجيه المستخدم إلى مسار تسجيل الخروج الصحيح
            window.location.href = "{{ route('logout.get') }}";
        }
    });
</script>

@yield('scripts')
</body>
</html>
