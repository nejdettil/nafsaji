@extends('layouts.dashboard')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">التقارير والإحصائيات</h4>
                    <p class="card-category">تحليل أداء جلساتك وإحصائيات المستخدمين</p>
                </div>
                <div class="card-body">
                    <div class="reports-container">
                        <div class="reports-header">
                            <div class="reports-filters">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="periodFilter">الفترة الزمنية</label>
                                            <select class="form-control" id="periodFilter">
                                                <option value="week">الأسبوع الحالي</option>
                                                <option value="month" selected>الشهر الحالي</option>
                                                <option value="quarter">الربع الحالي</option>
                                                <option value="year">السنة الحالية</option>
                                                <option value="custom">فترة مخصصة</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="serviceFilter">الخدمة</label>
                                            <select class="form-control" id="serviceFilter">
                                                <option value="all" selected>جميع الخدمات</option>
                                                @if(isset($services) && count($services) > 0)
                                                    @foreach($services as $service)
                                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 custom-date-range" style="display: none;">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="startDate">من تاريخ</label>
                                                    <input type="date" class="form-control" id="startDate">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="endDate">إلى تاريخ</label>
                                                    <input type="date" class="form-control" id="endDate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group d-flex align-items-end h-100">
                                            <button class="btn btn-primary w-100" id="applyFilters">تطبيق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reports-summary mt-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="summary-card bg-primary text-white">
                                        <div class="summary-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h3 class="summary-value">{{ $totalSessions ?? 0 }}</h3>
                                            <p class="summary-label">إجمالي الجلسات</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card bg-success text-white">
                                        <div class="summary-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h3 class="summary-value">{{ $totalRevenue ?? 0 }} ريال</h3>
                                            <p class="summary-label">إجمالي الإيرادات</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card bg-info text-white">
                                        <div class="summary-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h3 class="summary-value">{{ $totalHours ?? 0 }} ساعة</h3>
                                            <p class="summary-label">إجمالي الساعات</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card bg-warning text-white">
                                        <div class="summary-icon">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h3 class="summary-value">{{ $averageRating ?? 0 }}/5</h3>
                                            <p class="summary-label">متوسط التقييم</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reports-charts mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h5 class="chart-title">الجلسات الشهرية</h5>
                                        </div>
                                        <div class="chart-body">
                                            <canvas id="sessionsChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h5 class="chart-title">الإيرادات الشهرية</h5>
                                        </div>
                                        <div class="chart-body">
                                            <canvas id="revenueChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h5 class="chart-title">توزيع الخدمات</h5>
                                        </div>
                                        <div class="chart-body">
                                            <canvas id="servicesChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h5 class="chart-title">توزيع التقييمات</h5>
                                        </div>
                                        <div class="chart-body">
                                            <canvas id="ratingsChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reports-details mt-4">
                            <div class="card">
                                <div class="card-header card-header-info">
                                    <h5 class="mb-0">تفاصيل الجلسات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>المستخدم</th>
                                                    <th>الخدمة</th>
                                                    <th>المدة</th>
                                                    <th>الإيرادات</th>
                                                    <th>التقييم</th>
                                                    <th>الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($sessions) && count($sessions) > 0)
                                                    @foreach($sessions as $session)
                                                        <tr>
                                                            <td>{{ date('Y-m-d H:i', strtotime($session->date . ' ' . $session->time)) }}</td>
                                                            <td>{{ $session->booking->user->name ?? 'N/A' }}</td>
                                                            <td>{{ $session->booking->service->name ?? 'N/A' }}</td>
                                                            <td>{{ $session->duration ?? 0 }} دقيقة</td>
                                                            <td>{{ $session->booking->payment->amount ?? 0 }} ريال</td>
                                                            <td>
                                                                @if(isset($session->review) && $session->review)
                                                                    <div class="rating">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i class="fas fa-star {{ $i <= $session->review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                                        @endfor
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">لا يوجد تقييم</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $session->status == 'scheduled' ? 'warning' : ($session->status == 'completed' ? 'success' : 'secondary') }}">
                                                                    {{ $session->status == 'scheduled' ? 'مجدولة' : ($session->status == 'completed' ? 'مكتملة' : $session->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">لا توجد بيانات متاحة</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reports-actions mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-success w-100" id="exportExcel">
                                        <i class="fas fa-file-excel me-2"></i> تصدير إلى Excel
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger w-100" id="exportPdf">
                                        <i class="fas fa-file-pdf me-2"></i> تصدير إلى PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .reports-container {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .reports-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .summary-card {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        height: 100%;
    }
    
    .summary-icon {
        font-size: 36px;
        margin-left: 15px;
    }
    
    .summary-content {
        flex: 1;
    }
    
    .summary-value {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }
    
    .summary-label {
        margin: 0;
        opacity: 0.8;
    }
    
    .chart-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        height: 100%;
    }
    
    .chart-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background-color: #f9f9f9;
    }
    
    .chart-title {
        margin: 0;
        font-weight: 600;
    }
    
    .chart-body {
        padding: 20px;
    }
    
    .rating {
        display: flex;
    }
    
    .rating i {
        margin-left: 2px;
    }
    
    @media (max-width: 767px) {
        .summary-card {
            margin-bottom: 15px;
        }
        
        .chart-card {
            margin-bottom: 15px;
        }
        
        .reports-actions .btn {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إظهار/إخفاء حقول التاريخ المخصص
        document.getElementById('periodFilter').addEventListener('change', function() {
            var customDateRange = document.querySelector('.custom-date-range');
            if (this.value === 'custom') {
                customDateRange.style.display = 'block';
            } else {
                customDateRange.style.display = 'none';
            }
        });
        
        // تطبيق الفلاتر
        document.getElementById('applyFilters').addEventListener('click', function() {
            // هنا يمكن إضافة كود لتحديث البيانات بناءً على الفلاتر المحددة
            alert('تم تطبيق الفلاتر');
        });
        
        // تصدير إلى Excel
        document.getElementById('exportExcel').addEventListener('click', function() {
            alert('جاري تصدير البيانات إلى ملف Excel...');
        });
        
        // تصدير إلى PDF
        document.getElementById('exportPdf').addEventListener('click', function() {
            alert('جاري تصدير البيانات إلى ملف PDF...');
        });
        
        // رسم البيانات
        renderCharts();
    });
    
    function renderCharts() {
        // رسم مخطط الجلسات الشهرية
        var sessionsCtx = document.getElementById('sessionsChart').getContext('2d');
        var sessionsChart = new Chart(sessionsCtx, {
            type: 'bar',
            data: {
                labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                datasets: [{
                    label: 'عدد الجلسات',
                    data: [12, 19, 3, 5, 2, 3, 8, 14, 10, 15, 9, 6],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // رسم مخطط الإيرادات الشهرية
        var revenueCtx = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                datasets: [{
                    label: 'الإيرادات (ريال)',
                    data: [1200, 1900, 300, 500, 200, 300, 800, 1400, 1000, 1500, 900, 600],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // رسم مخطط توزيع الخدمات
        var servicesCtx = document.getElementById('servicesChart').getContext('2d');
        var servicesChart = new Chart(servicesCtx, {
            type: 'pie',
            data: {
                labels: ['استشارة نفسية', 'علاج سلوكي', 'إرشاد أسري', 'إرشاد زواجي', 'تنمية مهارات'],
                datasets: [{
                    data: [30, 20, 25, 15, 10],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        // رسم مخطط توزيع التقييمات
        var ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
        var ratingsChart = new Chart(ratingsCtx, {
            type: 'doughnut',
            data: {
                labels: ['5 نجوم', '4 نجوم', '3 نجوم', '2 نجوم', '1 نجمة'],
                datasets: [{
                    data: [45, 30, 15, 7, 3],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 99, 132, 0.7)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
</script>
@endsection
