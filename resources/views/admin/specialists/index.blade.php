@extends('layouts.dashboard')

@section('title', 'إدارة المختصين - نفسجي للتمكين النفسي')

@section('content')
<div class="specialists-management-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">إدارة المختصين</h1>
                    <p class="dashboard-subtitle">إدارة المختصين النفسيين والمعالجين</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecialistModal">
                            <i class="fas fa-user-plus"></i> إضافة مختص جديد
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importSpecialistsModal">
                            <i class="fas fa-file-import"></i> استيراد مختصين
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export"></i> تصدير
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.specialists.export', ['format' => 'excel']) }}">Excel</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.specialists.export', ['format' => 'csv']) }}">CSV</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.specialists.export', ['format' => 'pdf']) }}">PDF</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-user-md"></i> قائمة المختصين
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="search-box">
                                    <form action="{{ route('admin.specialists.index') }}" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="بحث..." name="search" value="{{ request('search') }}">
                                            <button class="btn btn-outline-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="dropdown d-inline-block ms-2">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter"></i> تصفية
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item {{ request('status') == 'all' || !request('status') ? 'active' : '' }}" href="{{ route('admin.specialists.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}">جميع المختصين</a></li>
                                        <li><a class="dropdown-item {{ request('status') == 'active' ? 'active' : '' }}" href="{{ route('admin.specialists.index', array_merge(request()->except('status', 'page'), ['status' => 'active'])) }}">المختصين النشطين</a></li>
                                        <li><a class="dropdown-item {{ request('status') == 'inactive' ? 'active' : '' }}" href="{{ route('admin.specialists.index', array_merge(request()->except('status', 'page'), ['status' => 'inactive'])) }}">المختصين غير النشطين</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item {{ request('category') == 'psychologist' ? 'active' : '' }}" href="{{ route('admin.specialists.index', array_merge(request()->except('category', 'page'), ['category' => 'psychologist'])) }}">أخصائي نفسي</a></li>
                                        <li><a class="dropdown-item {{ request('category') == 'therapist' ? 'active' : '' }}" href="{{ route('admin.specialists.index', array_merge(request()->except('category', 'page'), ['category' => 'therapist'])) }}">معالج نفسي</a></li>
                                        <li><a class="dropdown-item {{ request('category') == 'counselor' ? 'active' : '' }}" href="{{ route('admin.specialists.index', array_merge(request()->except('category', 'page'), ['category' => 'counselor'])) }}">مرشد نفسي</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($specialists) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check">
                                                        <input class="form-check-input select-all" type="checkbox" id="selectAll">
                                                        <label class="form-check-label" for="selectAll"></label>
                                                    </div>
                                                </th>
                                                <th>المختص</th>
                                                <th>التخصص</th>
                                                <th>التقييم</th>
                                                <th>الجلسات</th>
                                                <th>الحالة</th>
                                                <th>تاريخ الانضمام</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($specialists as $specialist)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input select-item" type="checkbox" id="specialist{{ $specialist->id }}" value="{{ $specialist->id }}">
                                                            <label class="form-check-label" for="specialist{{ $specialist->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="specialist-info">
                                                            <img src="{{ $specialist->profile_image ? asset('storage/' . $specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->full_name }}" class="specialist-avatar">
                                                            <div class="specialist-details">
                                                                <h6 class="specialist-name">{{ $specialist->full_name }}</h6>
                                                                <p class="specialist-email">{{ $specialist->email }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="specialty-badge">{{ $specialist->specialty }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="rating">
                                                            <span class="rating-value">{{ number_format($specialist->rating, 1) }}</span>
                                                            <div class="rating-stars">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= $specialist->rating)
                                                                        <i class="fas fa-star"></i>
                                                                    @elseif($i - 0.5 <= $specialist->rating)
                                                                        <i class="fas fa-star-half-alt"></i>
                                                                    @else
                                                                        <i class="far fa-star"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            <span class="reviews-count">({{ $specialist->reviews_count }})</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sessions-info">
                                                            <span class="sessions-count">{{ $specialist->sessions_count }}</span>
                                                            <span class="sessions-completion">{{ number_format($specialist->sessions_completion_rate, 0) }}%</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-switch" type="checkbox" id="statusSwitch{{ $specialist->id }}" {{ $specialist->status == 'active' ? 'checked' : '' }} data-id="{{ $specialist->id }}">
                                                            <label class="form-check-label" for="statusSwitch{{ $specialist->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $specialist->created_at->format('Y-m-d') }}</td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="{{ route('admin.specialists.show', $specialist->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-primary edit-specialist" data-id="{{ $specialist->id }}" data-bs-toggle="tooltip" title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger delete-specialist" data-id="{{ $specialist->id }}" data-bs-toggle="tooltip" title="حذف">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="bulk-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                                إجراءات جماعية
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="activate">تفعيل المحدد</a></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="deactivate">تعطيل المحدد</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="feature">تمييز المحدد</a></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="unfeature">إلغاء تمييز المحدد</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="delete">حذف المحدد</a></li>
                                            </ul>
                                        </div>
                                        <span class="selected-count ms-2">0 محدد</span>
                                    </div>
                                    
                                    <div class="pagination-container">
                                        {{ $specialists->appends(request()->except('page'))->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <h5>لا يوجد مختصين</h5>
                                    <p>لم يتم العثور على أي مختصين مطابقين لمعايير البحث.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecialistModal">
                                        <i class="fas fa-user-plus"></i> إضافة مختص جديد
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- إحصائيات المختصين -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-chart-pie"></i> توزيع التخصصات
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <canvas id="specialtiesChart" height="260"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-star"></i> أعلى المختصين تقييماً
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="top-specialists">
                                @foreach($topRatedSpecialists as $specialist)
                                    <div class="top-specialist-item">
                                        <div class="specialist-info">
                                            <img src="{{ $specialist->profile_image ? asset('storage/' . $specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->full_name }}" class="specialist-avatar">
                                            <div class="specialist-details">
                                                <h6 class="specialist-name">{{ $specialist->full_name }}</h6>
                                                <p class="specialist-specialty">{{ $specialist->specialty }}</p>
                                            </div>
                                        </div>
                                        <div class="rating">
                                            <span class="rating-value">{{ number_format($specialist->rating, 1) }}</span>
                                            <div class="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $specialist->rating)
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i - 0.5 <= $specialist->rating)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-calendar-check"></i> أكثر المختصين نشاطاً
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="top-specialists">
                                @foreach($mostActiveSpecialists as $specialist)
                                    <div class="top-specialist-item">
                                        <div class="specialist-info">
                                            <img src="{{ $specialist->profile_image ? asset('storage/' . $specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->full_name }}" class="specialist-avatar">
                                            <div class="specialist-details">
                                                <h6 class="specialist-name">{{ $specialist->full_name }}</h6>
                                                <p class="specialist-specialty">{{ $specialist->specialty }}</p>
                                            </div>
                                        </div>
                                        <div class="sessions-count-badge">
                                            <i class="fas fa-calendar-check"></i> {{ $specialist->sessions_count }} جلسة
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة مختص جديد -->
<div class="modal fade" id="addSpecialistModal" tabindex="-1" aria-labelledby="addSpecialistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSpecialistModalLabel">إضافة مختص جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.specialists.store') }}" method="POST" id="addSpecialistForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name">الاسم الأول</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name">الاسم الأخير</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone">رقم الهاتف</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password">كلمة المرور</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password_confirmation">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="specialty">التخصص</label>
                                <select class="form-select" id="specialty" name="specialty" required>
                                    <option value="">اختر التخصص</option>
                                    <option value="أخصائي نفسي">أخصائي نفسي</option>
                                    <option value="معالج نفسي">معالج نفسي</option>
                                    <option value="مرشد نفسي">مرشد نفسي</option>
                                    <option value="طبيب نفسي">طبيب نفسي</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="status">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" selected>نشط</option>
                                    <option value="inactive">غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="years_experience">سنوات الخبرة</label>
                                <input type="number" class="form-control" id="years_experience" name="years_experience" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="hourly_rate">السعر بالساعة (ريال)</label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="bio">نبذة تعريفية</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="profile_image">الصورة الشخصية</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                    </div>
                    <div class="form-group mb-3">
                        <label for="certificates">الشهادات والمؤهلات</label>
                        <textarea class="form-control" id="certificates" name="certificates" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label" for="is_featured">
                                مختص مميز
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_welcome_email" name="send_welcome_email" checked>
                            <label class="form-check-label" for="send_welcome_email">
                                إرسال بريد إلكتروني ترحيبي
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المختص</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تعديل مختص -->
<div class="modal fade" id="editSpecialistModal" tabindex="-1" aria-labelledby="editSpecialistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSpecialistModalLabel">تعديل المختص</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.specialists.update', 0) }}" method="POST" id="editSpecialistForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_specialist_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_first_name">الاسم الأول</label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_last_name">الاسم الأخير</label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_email">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_phone">رقم الهاتف</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_password">كلمة المرور (اتركها فارغة للاحتفاظ بالحالية)</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_password_confirmation">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_specialty">التخصص</label>
                                <select class="form-select" id="edit_specialty" name="specialty" required>
                                    <option value="">اختر التخصص</option>
                                    <option value="أخصائي نفسي">أخصائي نفسي</option>
                                    <option value="معالج نفسي">معالج نفسي</option>
                                    <option value="مرشد نفسي">مرشد نفسي</option>
                                    <option value="طبيب نفسي">طبيب نفسي</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_status">الحالة</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="active">نشط</option>
                                    <option value="inactive">غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_years_experience">سنوات الخبرة</label>
                                <input type="number" class="form-control" id="edit_years_experience" name="years_experience" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_hourly_rate">السعر بالساعة (ريال)</label>
                                <input type="number" class="form-control" id="edit_hourly_rate" name="hourly_rate" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_bio">نبذة تعريفية</label>
                        <textarea class="form-control" id="edit_bio" name="bio" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_profile_image">الصورة الشخصية</label>
                        <input type="file" class="form-control" id="edit_profile_image" name="profile_image" accept="image/*">
                        <div class="current-image mt-2" id="current_image_container">
                            <p>الصورة الحالية:</p>
                            <img id="current_image" src="" alt="الصورة الحالية" style="max-width: 100px; max-height: 100px;">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_certificates">الشهادات والمؤهلات</label>
                        <textarea class="form-control" id="edit_certificates" name="certificates" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_featured" name="is_featured">
                            <label class="form-check-label" for="edit_is_featured">
                                مختص مميز
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal حذف مختص -->
<div class="modal fade" id="deleteSpecialistModal" tabindex="-1" aria-labelledby="deleteSpecialistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSpecialistModalLabel">حذف المختص</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.specialists.destroy', 0) }}" method="POST" id="deleteSpecialistForm">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete_specialist_id" name="id">
                <div class="modal-body">
                    <p>هل أنت متأكد من رغبتك في حذف هذا المختص؟</p>
                    <p class="text-danger">تحذير: هذا الإجراء لا يمكن التراجع عنه وسيؤدي إلى حذف جميع الجلسات والتقييمات المرتبطة بهذا المختص.</p>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="delete_confirm" name="delete_confirm" required>
                            <label class="form-check-label" for="delete_confirm">
                                نعم، أنا متأكد من رغبتي في حذف هذا المختص
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal استيراد مختصين -->
<div class="modal fade" id="importSpecialistsModal" tabindex="-1" aria-labelledby="importSpecialistsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSpecialistsModalLabel">استيراد مختصين</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.specialists.import') }}" method="POST" enctype="multipart/form-data" id="importSpecialistsForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="import_file">ملف الاستيراد</label>
                        <input type="file" class="form-control" id="import_file" name="import_file" accept=".csv, .xlsx" required>
                        <small class="form-text text-muted">يجب أن يكون الملف بتنسيق CSV أو Excel.</small>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_header_row" name="has_header_row" checked>
                            <label class="form-check-label" for="has_header_row">
                                الملف يحتوي على صف عناوين
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <a href="{{ route('admin.specialists.download-template') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> تنزيل قالب الاستيراد
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">استيراد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal الإجراءات الجماعية -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">تأكيد الإجراء الجماعي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.specialists.bulk-action') }}" method="POST" id="bulkActionForm">
                @csrf
                <input type="hidden" id="bulk_action_type" name="action">
                <input type="hidden" id="bulk_action_ids" name="ids">
                <div class="modal-body">
                    <p id="bulk_action_message"></p>
                    <div class="form-group mb-3" id="bulk_action_confirm_container">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bulk_action_confirm" name="confirm" required>
                            <label class="form-check-label" for="bulk_action_confirm" id="bulk_action_confirm_label">
                                نعم، أنا متأكد من رغبتي في تنفيذ هذا الإجراء
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="bulk_action_submit">تأكيد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .specialists-management-page {
        background-color: #f8f9fa;
    }
    
    .dashboard-header {
        background-color: #fff;
        padding: 30px 0;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .dashboard-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }
    
    .dashboard-subtitle {
        color: #666;
        margin-bottom: 0;
    }
    
    .dashboard-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .dashboard-content {
        margin-bottom: 30px;
    }
    
    /* بطاقات المحتوى */
    .dashboard-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .dashboard-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .dashboard-card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 0;
        color: #333;
        display: flex;
        align-items: center;
    }
    
    .dashboard-card-title i {
        margin-left: 10px;
        color: #6a1b9a;
    }
    
    .dashboard-card-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dashboard-card-body {
        padding: 20px;
    }
    
    /* جدول المختصين */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        font-weight: 600;
        color: #333;
        border-top: none;
    }
    
    .specialist-info {
        display: flex;
        align-items: center;
    }
    
    .specialist-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        object-fit: cover;
    }
    
    .specialist-details {
        display: flex;
        flex-direction: column;
    }
    
    .specialist-name {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 0;
        color: #333;
    }
    
    .specialist-email {
        font-size: 12px;
        margin-bottom: 0;
        color: #666;
    }
    
    .specialty-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .rating {
        display: flex;
        align-items: center;
    }
    
    .rating-value {
        font-weight: 600;
        margin-left: 5px;
    }
    
    .rating-stars {
        color: #ffc107;
        font-size: 14px;
    }
    
    .reviews-count {
        font-size: 12px;
        color: #666;
        margin-right: 5px;
    }
    
    .sessions-info {
        display: flex;
        flex-direction: column;
    }
    
    .sessions-count {
        font-weight: 600;
    }
    
    .sessions-completion {
        font-size: 12px;
        color: #388e3c;
    }
    
    .form-switch {
        padding-right: 2.5em;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    /* مربع البحث */
    .search-box {
        max-width: 300px;
    }
    
    /* الإجراءات الجماعية */
    .bulk-actions {
        display: flex;
        align-items: center;
    }
    
    .selected-count {
        font-size: 14px;
        color: #666;
    }
    
    /* الترقيم الصفحي */
    .pagination-container {
        display: flex;
        justify-content: center;
    }
    
    .pagination {
        --bs-pagination-color: #6a1b9a;
        --bs-pagination-hover-color: #6a1b9a;
        --bs-pagination-focus-color: #6a1b9a;
        --bs-pagination-active-bg: #6a1b9a;
        --bs-pagination-active-border-color: #6a1b9a;
    }
    
    /* حالة فارغة */
    .empty-state {
        text-align: center;
        padding: 30px 20px;
    }
    
    .empty-state-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #f0e6f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #6a1b9a;
        margin: 0 auto 15px;
    }
    
    .empty-state h5 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .empty-state p {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
    }
    
    /* أعلى المختصين */
    .top-specialists {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .top-specialist-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .top-specialist-item:last-child {
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .sessions-count-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .dashboard-actions {
            margin-top: 15px;
            justify-content: flex-start;
        }
    }
    
    @media (max-width: 767px) {
        .dashboard-header {
            padding: 20px 0;
        }
        
        .dashboard-title {
            font-size: 20px;
        }
        
        .dashboard-actions {
            flex-wrap: wrap;
        }
        
        .table-responsive {
            border: none;
        }
    }
    
    @media (max-width: 575px) {
        .specialist-info {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .specialist-avatar {
            margin-bottom: 10px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // تهيئة الرسم البياني للتخصصات
        var specialtiesChartEl = document.getElementById('specialtiesChart');
        if (specialtiesChartEl) {
            var specialtiesChart = new Chart(specialtiesChartEl, {
                type: 'doughnut',
                data: {
                    labels: @json($specialtiesChartData['labels']),
                    datasets: [{
                        data: @json($specialtiesChartData['data']),
                        backgroundColor: [
                            '#6a1b9a',
                            '#8e24aa',
                            '#ab47bc',
                            '#ce93d8'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = Math.round((value / total) * 100);
                                    return label + ': ' + percentage + '%';
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
        
        // تحديد/إلغاء تحديد جميع العناصر
        $('#selectAll').on('change', function() {
            $('.select-item').prop('checked', $(this).is(':checked'));
            updateBulkActions();
        });
        
        // تحديث حالة زر الإجراءات الجماعية عند تغيير التحديد
        $('.select-item').on('change', function() {
            updateBulkActions();
        });
        
        // تحديث حالة الإجراءات الجماعية
        function updateBulkActions() {
            var selectedCount = $('.select-item:checked').length;
            $('.selected-count').text(selectedCount + ' محدد');
            
            if (selectedCount > 0) {
                $('#bulkActionsDropdown').prop('disabled', false);
            } else {
                $('#bulkActionsDropdown').prop('disabled', true);
            }
        }
        
        // تغيير حالة المختص
        $('.status-switch').on('change', function() {
            var specialistId = $(this).data('id');
            var isActive = $(this).is(':checked');
            
            $.ajax({
                url: "{{ route('admin.specialists.update-status') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    specialist_id: specialistId,
                    status: isActive ? 'active' : 'inactive'
                },
                success: function(response) {
                    if (response.success) {
                        // يمكن إضافة إشعار نجاح هنا
                    } else {
                        // إعادة الحالة السابقة في حالة الفشل
                        $(this).prop('checked', !isActive);
                    }
                }.bind(this),
                error: function() {
                    // إعادة الحالة السابقة في حالة الخطأ
                    $(this).prop('checked', !isActive);
                }.bind(this)
            });
        });
        
        // تعديل المختص
        $('.edit-specialist').on('click', function() {
            var specialistId = $(this).data('id');
            
            // جلب بيانات المختص
            $.ajax({
                url: "{{ route('admin.specialists.get') }}",
                type: 'GET',
                data: {
                    specialist_id: specialistId
                },
                success: function(response) {
                    if (response.success) {
                        var specialist = response.specialist;
                        
                        $('#edit_specialist_id').val(specialist.id);
                        $('#edit_first_name').val(specialist.first_name);
                        $('#edit_last_name').val(specialist.last_name);
                        $('#edit_email').val(specialist.email);
                        $('#edit_phone').val(specialist.phone);
                        $('#edit_specialty').val(specialist.specialty);
                        $('#edit_years_experience').val(specialist.years_experience);
                        $('#edit_hourly_rate').val(specialist.hourly_rate);
                        $('#edit_bio').val(specialist.bio);
                        $('#edit_certificates').val(specialist.certificates);
                        $('#edit_status').val(specialist.status);
                        $('#edit_is_featured').prop('checked', specialist.is_featured);
                        
                        if (specialist.profile_image) {
                            $('#current_image_container').show();
                            $('#current_image').attr('src', "{{ asset('storage') }}/" + specialist.profile_image);
                        } else {
                            $('#current_image_container').hide();
                        }
                        
                        var updateUrl = "{{ route('admin.specialists.update', ':id') }}".replace(':id', specialist.id);
                        $('#editSpecialistForm').attr('action', updateUrl);
                        
                        $('#editSpecialistModal').modal('show');
                    }
                }
            });
        });
        
        // حذف المختص
        $('.delete-specialist').on('click', function() {
            var specialistId = $(this).data('id');
            
            $('#delete_specialist_id').val(specialistId);
            
            var deleteUrl = "{{ route('admin.specialists.destroy', ':id') }}".replace(':id', specialistId);
            $('#deleteSpecialistForm').attr('action', deleteUrl);
            
            $('#deleteSpecialistModal').modal('show');
        });
        
        // الإجراءات الجماعية
        $('.bulk-action').on('click', function(e) {
            e.preventDefault();
            
            var action = $(this).data('action');
            var selectedIds = [];
            
            $('.select-item:checked').each(function() {
                selectedIds.push($(this).val());
            });
            
            $('#bulk_action_type').val(action);
            $('#bulk_action_ids').val(selectedIds.join(','));
            
            // تعيين رسالة التأكيد
            if (action === 'activate') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في تفعيل المختصين المحددين؟');
                $('#bulk_action_submit').removeClass('btn-danger').addClass('btn-primary').text('تأكيد التفعيل');
                $('#bulk_action_confirm_container').hide();
            } else if (action === 'deactivate') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في تعطيل المختصين المحددين؟');
                $('#bulk_action_submit').removeClass('btn-danger').addClass('btn-primary').text('تأكيد التعطيل');
                $('#bulk_action_confirm_container').hide();
            } else if (action === 'feature') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في تمييز المختصين المحددين؟');
                $('#bulk_action_submit').removeClass('btn-danger').addClass('btn-primary').text('تأكيد التمييز');
                $('#bulk_action_confirm_container').hide();
            } else if (action === 'unfeature') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في إلغاء تمييز المختصين المحددين؟');
                $('#bulk_action_submit').removeClass('btn-danger').addClass('btn-primary').text('تأكيد إلغاء التمييز');
                $('#bulk_action_confirm_container').hide();
            } else if (action === 'delete') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في حذف المختصين المحددين؟');
                $('#bulk_action_message').append('<p class="text-danger">تحذير: هذا الإجراء لا يمكن التراجع عنه وسيؤدي إلى حذف جميع الجلسات والتقييمات المرتبطة بهؤلاء المختصين.</p>');
                $('#bulk_action_submit').removeClass('btn-primary').addClass('btn-danger').text('تأكيد الحذف');
                $('#bulk_action_confirm_label').text('نعم، أنا متأكد من رغبتي في حذف المختصين المحددين');
                $('#bulk_action_confirm_container').show();
            }
            
            $('#bulkActionModal').modal('show');
        });
        
        // التحقق من صحة نموذج إضافة مختص
        $('#addSpecialistForm').on('submit', function(e) {
            var password = $('#password').val();
            var passwordConfirmation = $('#password_confirmation').val();
            
            if (password !== passwordConfirmation) {
                e.preventDefault();
                alert('كلمة المرور وتأكيدها غير متطابقين');
            }
        });
        
        // التحقق من صحة نموذج تعديل مختص
        $('#editSpecialistForm').on('submit', function(e) {
            var password = $('#edit_password').val();
            var passwordConfirmation = $('#edit_password_confirmation').val();
            
            if (password && password !== passwordConfirmation) {
                e.preventDefault();
                alert('كلمة المرور وتأكيدها غير متطابقين');
            }
        });
    });
</script>
@endsection
