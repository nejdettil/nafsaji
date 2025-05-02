<!-- قائمة المستخدم العام الجانبية -->
<li class="nav-item">
    <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>لوحة التحكم</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('user.bookings') }}" class="nav-link {{ request()->routeIs('user.bookings*') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i>
        <span>حجوزاتي</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('user.sessions') }}" class="nav-link {{ request()->routeIs('user.sessions*') ? 'active' : '' }}">
        <i class="fas fa-video"></i>
        <span>جلساتي</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('user.favorites') }}" class="nav-link {{ request()->routeIs('user.favorites*') ? 'active' : '' }}">
        <i class="fas fa-heart"></i>
        <span>المفضلة</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('user.payments') }}" class="nav-link {{ request()->routeIs('user.payments*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i>
        <span>المدفوعات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('user.reviews') }}" class="nav-link {{ request()->routeIs('user.reviews*') ? 'active' : '' }}">
        <i class="fas fa-star"></i>
        <span>تقييماتي</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
        <i class="fas fa-user-circle"></i>
        <span>الملف الشخصي</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('user.notifications') }}" class="nav-link {{ request()->routeIs('user.notifications*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span>الإشعارات</span>
    </a>
</li>
