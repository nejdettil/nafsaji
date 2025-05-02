<!-- قائمة المختص الجانبية -->
<li class="nav-item">
    <a href="{{ route('specialist.dashboard') }}" class="nav-link {{ request()->routeIs('specialist.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>لوحة التحكم</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.appointments') }}" class="nav-link {{ request()->routeIs('specialist.appointments*') ? 'active' : '' }}">
        <i class="fas fa-calendar-alt"></i>
        <span>المواعيد</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.sessions') }}" class="nav-link {{ request()->routeIs('specialist.sessions*') ? 'active' : '' }}">
        <i class="fas fa-video"></i>
        <span>الجلسات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.clients') }}" class="nav-link {{ request()->routeIs('specialist.clients*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>العملاء</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.services') }}" class="nav-link {{ request()->routeIs('specialist.services*') ? 'active' : '' }}">
        <i class="fas fa-hand-holding-medical"></i>
        <span>خدماتي</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.availability') }}" class="nav-link {{ request()->routeIs('specialist.availability*') ? 'active' : '' }}">
        <i class="fas fa-clock"></i>
        <span>أوقات العمل</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.payments') }}" class="nav-link {{ request()->routeIs('specialist.payments*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i>
        <span>المدفوعات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.reviews') }}" class="nav-link {{ request()->routeIs('specialist.reviews*') ? 'active' : '' }}">
        <i class="fas fa-star"></i>
        <span>التقييمات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.reports') }}" class="nav-link {{ request()->routeIs('specialist.reports*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span>التقارير</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('specialist.profile') }}" class="nav-link {{ request()->routeIs('specialist.profile*') ? 'active' : '' }}">
        <i class="fas fa-user-circle"></i>
        <span>الملف الشخصي</span>
    </a>
</li>
