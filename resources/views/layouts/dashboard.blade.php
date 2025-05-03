<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم - نفسجي')</title>

    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Tajawal (متوافق مع ملف admin.css) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">

    @yield('styles')
</head>
<body class="admin-panel">
<div class="dashboard-container d-flex">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('home') }}" class="d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="نفسجي" height="40">
                </div>
                <div class="sidebar-brand-text mx-2">نفسجي</div>
            </a>
            <button class="btn btn-link d-md-none sidebar-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <hr class="sidebar-divider my-2">

        <div class="text-center mb-3">
            <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ Auth::user()->name }}" class="img-profile rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
            <div class="mt-2">
                <h6 class="text-white font-weight-bold mb-0">{{ Auth::user()->name }}</h6>
                <span class="text-white-50 small">
                    @role('admin') مدير النظام
                    @elserole('specialist') مختص
                    @else مستخدم
                    @endrole
                </span>
            </div>
        </div>

        <hr class="sidebar-divider">

        <nav class="sidebar-nav">
            <ul class="nav-list">
                @role('admin')
                @include('layouts.partials.admin-sidebar')
                @elserole('specialist')
                @include('layouts.partials.specialist-sidebar')
                @else
                    @include('layouts.partials.user-sidebar')
                    @endrole
            </ul>
        </nav>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-light btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content d-flex flex-column flex-grow-1">
        <!-- Header -->
        <header class="admin-topbar sticky-top">
            <nav class="navbar navbar-expand navbar-light bg-white">
                <div class="container-fluid">
                    <button class="btn btn-link d-md-none rounded-circle me-3 sidebar-toggle" id="sidebarToggleTop">
                        <i class="fas fa-bars"></i>
                    </button>

                    <h4 class="page-title mb-0 d-none d-md-block">@yield('page-title', 'لوحة التحكم')</h4>

                    <ul class="navbar-nav ms-auto">
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                 aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header bg-primary text-white">
                                    الإشعارات
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="me-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-calendar-check text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">{{ date('d/m/Y') }}</div>
                                        <span>حجز جديد #123</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="me-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-money-bill text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">{{ date('d/m/Y') }}</div>
                                        تم تأكيد الدفع لحجز #456
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="me-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-star text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">{{ date('d/m/Y') }}</div>
                                        تقييم جديد من العميل أحمد
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">عرض جميع الإشعارات</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <img class="img-profile rounded-circle"
                                     src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/images/default-avatar.png') }}"
                                     alt="{{ Auth::user()->name }}" style="width: 32px; height: 32px; object-fit: cover;">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                 aria-labelledby="userDropdown">
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
                                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                    الملف الشخصي
                                </a>
                                <a class="dropdown-item" href="{{ route('home') }}">
                                    <i class="fas fa-home fa-sm fa-fw me-2 text-gray-400"></i>
                                    الصفحة الرئيسية
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Content -->
        <div class="admin-content">
            @if(session('success'))
                <div class="admin-alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="admin-alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white mt-auto">
            <div class="container">
                <div class="copyright text-center">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0">&copy; {{ date('Y') }} نفسجي. جميع الحقوق محفوظة.</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0">تم التطوير بواسطة <a href="#" class="text-primary">فريق نفسجي</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS -->
<script src="{{ asset('assets/js/admin.js') }}"></script>
<script>
    const notificationsUrl = "{{ route('admin.notifications.count') }}
        ";
</script>

@yield('scripts')
</body>
</html>
