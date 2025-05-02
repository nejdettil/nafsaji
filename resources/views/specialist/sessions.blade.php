@extends('layouts.dashboard')

@section('title', 'إدارة الجلسات - نفسجي للتمكين النفسي')

@section('content')
<div class="sessions-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">إدارة الجلسات</h1>
                    <p class="dashboard-subtitle">إدارة جميع الجلسات الخاصة بك</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <a href="{{ route('specialist.sessions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> إضافة جلسة جديدة
                        </a>
                        <a href="{{ route('specialist.calendar') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt"></i> عرض التقويم
                        </a>
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
                                <i class="fas fa-filter"></i> تصفية الجلسات
                            </h5>
                            <div class="dashboard-card-actions">
                                <button type="button" class="btn btn-sm btn-link" id="resetFilters">إعادة تعيين</button>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            <form action="{{ route('specialist.sessions.index') }}" method="GET" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="status">الحالة</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="">جميع الحالات</option>
                                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="service">الخدمة</label>
                                            <select class="form-select" id="service" name="service_id">
                                                <option value="">جميع الخدمات</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="date_from">من تاريخ</label>
                                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="date_to">إلى تاريخ</label>
                                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="client">العميل</label>
                                            <select class="form-select" id="client" name="user_id">
                                                <option value="">جميع العملاء</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ request('user_id') == $client->id ? 'selected' : '' }}>{{ $client->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="payment_status">حالة الدفع</label>
                                            <select class="form-select" id="payment_status" name="payment_status">
                                                <option value="">جميع حالات الدفع</option>
                                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                                                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>مدفوعة جزئياً</option>
                                                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>مستردة</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="sort_by">ترتيب حسب</label>
                                            <select class="form-select" id="sort_by" name="sort_by">
                                                <option value="date_desc" {{ request('sort_by') == 'date_desc' ? 'selected' : '' }}>التاريخ (الأحدث أولاً)</option>
                                                <option value="date_asc" {{ request('sort_by') == 'date_asc' ? 'selected' : '' }}>التاريخ (الأقدم أولاً)</option>
                                                <option value="client_name" {{ request('sort_by') == 'client_name' ? 'selected' : '' }}>اسم العميل</option>
                                                <option value="service_name" {{ request('sort_by') == 'service_name' ? 'selected' : '' }}>اسم الخدمة</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3 d-flex align-items-end h-100">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search"></i> تطبيق الفلتر
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-calendar-check"></i> قائمة الجلسات
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="exportCSV">
                                        <i class="fas fa-file-csv"></i> تصدير CSV
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="exportPDF">
                                        <i class="fas fa-file-pdf"></i> تصدير PDF
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="printSessions">
                                        <i class="fas fa-print"></i> طباعة
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($sessions) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>العميل</th>
                                                <th>الخدمة</th>
                                                <th>التاريخ</th>
                                                <th>الوقت</th>
                                                <th>المدة</th>
                                                <th>الحالة</th>
                                                <th>حالة الدفع</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sessions as $session)
                                                <tr>
                                                    <td>{{ $session->id }}</td>
                                                    <td>
                                                        <div class="user-info">
                                                            <img src="{{ $session->booking->user->profile_image ? asset('storage/' . $session->booking->user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $session->booking->user->full_name }}" class="user-avatar">
                                                            <div>
                                                                <span class="user-name">{{ $session->booking->user->full_name }}</span>
                                                                <span class="user-email">{{ $session->booking->user->email }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $session->booking->service->name }}</td>
                                                    <td>{{ $session->date->format('Y-m-d') }}</td>
                                                    <td>{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</td>
                                                    <td>{{ $session->duration }} دقيقة</td>
                                                    <td>
                                                        <span class="status-badge status-{{ $session->status }}">
                                                            {{ __('sessions.status.' . $session->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="payment-badge payment-{{ $session->payment_status }}">
                                                            {{ __('payments.status.' . $session->payment_status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="{{ route('specialist.sessions.show', $session->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($session->status == 'scheduled')
                                                                <a href="{{ route('specialist.sessions.start', $session->id) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="بدء الجلسة">
                                                                    <i class="fas fa-play"></i>
                                                                </a>
                                                                <a href="{{ route('specialist.sessions.reschedule', $session->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="إعادة جدولة">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                </a>
                                                                <a href="{{ route('specialist.sessions.cancel', $session->id) }}" class="btn btn-sm btn-danger cancel-session" data-bs-toggle="tooltip" title="إلغاء" data-id="{{ $session->id }}">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                            @endif
                                                            @if($session->status == 'in_progress')
                                                                <a href="{{ route('specialist.sessions.end', $session->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="إنهاء الجلسة">
                                                                    <i class="fas fa-stop"></i>
                                                                </a>
                                                            @endif
                                                            @if($session->status == 'completed')
                                                                <a href="{{ route('specialist.sessions.notes', $session->id) }}" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="ملاحظات الجلسة">
                                                                    <i class="fas fa-sticky-note"></i>
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('specialist.chat', ['user_id' => $session->booking->user->id]) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="محادثة مع العميل">
                                                                <i class="fas fa-comments"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="pagination-container">
                                    {{ $sessions->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <h5>لا توجد جلسات</h5>
                                    <p>لم يتم العثور على أي جلسات تطابق معايير البحث.</p>
                                    <a href="{{ route('specialist.sessions.index') }}" class="btn btn-primary">
                                        <i class="fas fa-sync"></i> عرض جميع الجلسات
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-chart-pie"></i> إحصائيات الجلسات
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="chart-container">
                                        <canvas id="sessionsChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="stats-container">
                                        <div class="stats-card">
                                            <div class="stats-card-icon">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                            <div class="stats-card-info">
                                                <h6>إجمالي الجلسات</h6>
                                                <p>{{ $totalSessions }}</p>
                                            </div>
                                        </div>
                                        <div class="stats-card">
                                            <div class="stats-card-icon">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div class="stats-card-info">
                                                <h6>الجلسات المجدولة</h6>
                                                <p>{{ $scheduledSessions }}</p>
                                            </div>
                                        </div>
                                        <div class="stats-card">
                                            <div class="stats-card-icon">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                            <div class="stats-card-info">
                                                <h6>الجلسات المكتملة</h6>
                                                <p>{{ $completedSessions }}</p>
                                            </div>
                                        </div>
                                        <div class="stats-card">
                                            <div class="stats-card-icon">
                                                <i class="fas fa-calendar-times"></i>
                                            </div>
                                            <div class="stats-card-info">
                                                <h6>الجلسات الملغاة</h6>
                                                <p>{{ $cancelledSessions }}</p>
                                            </div>
                                        </div>
                                        <div class="stats-card">
                                            <div class="stats-card-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="stats-card-info">
                                                <h6>إجمالي ساعات الجلسات</h6>
                                                <p>{{ $totalHours }} ساعة</p>
                                            </div>
                                        </div>
                                        <div class="stats-card">
                                            <div class="stats-card-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="stats-card-info">
                                                <h6>إجمالي الإيرادات</h6>
                                                <p>{{ number_format($totalRevenue) }} ر.س</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إلغاء الجلسة -->
<div class="modal fade" id="cancelSessionModal" tabindex="-1" aria-labelledby="cancelSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelSessionModalLabel">إلغاء الجلسة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="cancelSessionForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="cancel_reason">سبب الإلغاء</label>
                        <select class="form-select" id="cancel_reason" name="cancel_reason" required>
                            <option value="">اختر سبب الإلغاء</option>
                            <option value="schedule_conflict">تعارض في الجدول</option>
                            <option value="client_request">بناءً على طلب العميل</option>
                            <option value="emergency">حالة طارئة</option>
                            <option value="other">سبب آخر</option>
                        </select>
                    </div>
                    <div class="form-group mb-3" id="other_reason_container" style="display: none;">
                        <label for="other_reason">سبب آخر</label>
                        <textarea class="form-control" id="other_reason" name="other_reason" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="notify_client">إشعار العميل</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notify_client" name="notify_client" checked>
                            <label class="form-check-label" for="notify_client">
                                إرسال إشعار للعميل بإلغاء الجلسة
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="reschedule_option">خيارات إعادة الجدولة</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="offer_reschedule" name="offer_reschedule" checked>
                            <label class="form-check-label" for="offer_reschedule">
                                عرض إعادة جدولة الجلسة للعميل
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .sessions-page {
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
    
    /* جدول الجلسات */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        font-weight: 600;
        color: #333;
        border-top: none;
    }
    
    .user-info {
        display: flex;
        align-items: center;
    }
    
    .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-left: 10px;
        object-fit: cover;
    }
    
    .user-name {
        display: block;
        font-weight: 600;
        color: #333;
    }
    
    .user-email {
        display: block;
        font-size: 12px;
        color: #666;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-scheduled {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .status-in_progress {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .status-completed {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .status-cancelled {
        background-color: #ffebee;
        color: #d32f2f;
    }
    
    .payment-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .payment-paid {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .payment-unpaid {
        background-color: #ffebee;
        color: #d32f2f;
    }
    
    .payment-partial {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .payment-refunded {
        background-color: #f3e5f5;
        color: #8e24aa;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    /* الإحصائيات */
    .chart-container {
        height: 300px;
        margin-bottom: 20px;
    }
    
    .stats-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        height: 100%;
    }
    
    .stats-card {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        background-color: #f0e6f5;
    }
    
    .stats-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background-color: #f0e6f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #6a1b9a;
        margin-left: 15px;
    }
    
    .stats-card-info {
        flex: 1;
    }
    
    .stats-card-info h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .stats-card-info p {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 0;
        color: #6a1b9a;
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
    
    /* الترقيم الصفحي */
    .pagination-container {
        margin-top: 20px;
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
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .dashboard-actions {
            margin-top: 15px;
            justify-content: flex-start;
        }
        
        .chart-container {
            height: 250px;
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
        
        .action-buttons {
            flex-wrap: wrap;
        }
        
        .chart-container {
            height: 200px;
        }
    }
    
    @media (max-width: 575px) {
        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-avatar {
            margin-bottom: 5px;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // تهيئة مخطط الجلسات
        var sessionsChartEl = document.getElementById('sessionsChart');
        if (sessionsChartEl) {
            var sessionsChart = new Chart(sessionsChartEl, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'الجلسات المجدولة',
                            data: @json($scheduledData),
                            backgroundColor: 'rgba(25, 118, 210, 0.2)',
                            borderColor: 'rgba(25, 118, 210, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'الجلسات المكتملة',
                            data: @json($completedData),
                            backgroundColor: 'rgba(56, 142, 60, 0.2)',
                            borderColor: 'rgba(56, 142, 60, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'الجلسات الملغاة',
                            data: @json($cancelledData),
                            backgroundColor: 'rgba(211, 47, 47, 0.2)',
                            borderColor: 'rgba(211, 47, 47, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // إعادة تعيين الفلتر
        $('#resetFilters').on('click', function() {
            $('#filterForm')[0].reset();
            $('#filterForm').submit();
        });
        
        // تصدير CSV
        $('#exportCSV').on('click', function() {
            window.location.href = "{{ route('specialist.sessions.export', ['format' => 'csv']) }}" + "?" + $('#filterForm').serialize();
        });
        
        // تصدير PDF
        $('#exportPDF').on('click', function() {
            window.location.href = "{{ route('specialist.sessions.export', ['format' => 'pdf']) }}" + "?" + $('#filterForm').serialize();
        });
        
        // طباعة
        $('#printSessions').on('click', function() {
            window.open("{{ route('specialist.sessions.print') }}" + "?" + $('#filterForm').serialize(), '_blank');
        });
        
        // إظهار حقل السبب الآخر عند اختياره
        $('#cancel_reason').on('change', function() {
            if ($(this).val() === 'other') {
                $('#other_reason_container').show();
            } else {
                $('#other_reason_container').hide();
            }
        });
        
        // إلغاء الجلسة
        $('.cancel-session').on('click', function(e) {
            e.preventDefault();
            var sessionId = $(this).data('id');
            var cancelUrl = "{{ route('specialist.sessions.cancel', ':id') }}".replace(':id', sessionId);
            $('#cancelSessionForm').attr('action', cancelUrl);
            $('#cancelSessionModal').modal('show');
        });
        
        // تفعيل tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
