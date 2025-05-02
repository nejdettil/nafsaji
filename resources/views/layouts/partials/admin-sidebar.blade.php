<!-- قائمة المدير الجانبية -->
<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>لوحة التحكم</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>إدارة المستخدمين</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.specialists.index') }}" class="nav-link {{ request()->routeIs('admin.specialists*') ? 'active' : '' }}">
        <i class="fas fa-user-md"></i>
        <span>إدارة المختصين</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
        <i class="fas fa-hand-holding-medical"></i>
        <span>إدارة الخدمات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.services.categories.index') }}" class="nav-link {{ request()->routeIs('admin.services.categories*') ? 'active' : '' }}">
        <i class="fas fa-th-list"></i>
        <span>إدارة الفئات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages*') ? 'active' : '' }}">
        <i class="fas fa-box"></i>
        <span>إدارة الباقات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i>
        <span>إدارة الحجوزات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i>
        <span>إدارة المدفوعات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
        <i class="fas fa-chart-bar"></i>
        <span>التقارير والإحصائيات</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i>
        <span>إعدادات الموقع</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
        <i class="fas fa-user-tag"></i>
        <span>الأدوار والصلاحيات</span>
    </a>
</li>
