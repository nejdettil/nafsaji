@extends('layouts.dashboard')

@section('title', 'إدارة المدفوعات - نفسجي')

@section('content')
<div class="content-wrapper">
    <!-- رأس الصفحة -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">إدارة المدفوعات</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">المدفوعات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- محتوى الصفحة -->
    <div class="content">
        <div class="container-fluid">
            <!-- بطاقات الإحصائيات -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h5 class="stats-title">إجمالي المدفوعات</h5>
                            <h2 class="stats-value">{{ number_format($totalPayments, 0) }} ر.س</h2>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5 class="stats-title">المدفوعات المكتملة</h5>
                            <h2 class="stats-value">{{ number_format($completedPayments, 0) }} ر.س</h2>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="stats-title">قيد المعالجة</h5>
                            <h2 class="stats-value">{{ number_format($pendingPayments, 0) }} ر.س</h2>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-icon bg-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h5 class="stats-title">المدفوعات المرفوضة</h5>
                            <h2 class="stats-value">{{ number_format($failedPayments, 0) }} ر.س</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الرسم البياني للمدفوعات -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">تحليل المدفوعات</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary payment-period" data-period="week">أسبوعي</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary payment-period active" data-period="month">شهري</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary payment-period" data-period="year">سنوي</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="paymentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قائمة المدفوعات -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">قائمة المدفوعات</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.payments.export') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i> تصدير
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- أدوات البحث والتصفية -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <form action="{{ route('admin.payments.index') }}" method="GET" class="d-flex">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="البحث عن مدفوعات..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-end">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-filter me-1"></i> تصفية
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index') }}">الكل</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['status' => 'completed']) }}">مكتمل</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['status' => 'pending']) }}">قيد المعالجة</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['status' => 'failed']) }}">مرفوض</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['status' => 'refunded']) }}">مسترجع</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['payment_method' => 'credit_card']) }}">بطاقة ائتمان</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['payment_method' => 'bank_transfer']) }}">تحويل بنكي</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['payment_method' => 'wallet']) }}">محفظة إلكترونية</a></li>
                                            </ul>
                                        </div>
                                        <div class="btn-group ms-2">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-sort me-1"></i> ترتيب
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['sort' => 'created_at', 'direction' => 'desc']) }}">الأحدث</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['sort' => 'created_at', 'direction' => 'asc']) }}">الأقدم</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['sort' => 'amount', 'direction' => 'desc']) }}">المبلغ (الأعلى)</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.payments.index', ['sort' => 'amount', 'direction' => 'asc']) }}">المبلغ (الأقل)</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- جدول المدفوعات -->
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>رقم العملية</th>
                                            <th>المستخدم</th>
                                            <th>رقم الحجز</th>
                                            <th>المبلغ</th>
                                            <th>طريقة الدفع</th>
                                            <th>رقم المرجع</th>
                                            <th>تاريخ العملية</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                        <tr>
                                            <td>#{{ $payment->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ optional($payment->user)->avatar ? asset(optional($payment->user)->avatar) : asset('assets/images/default-avatar.png') }}" alt="صورة المستخدم" class="avatar-xs rounded-circle me-2">
                                                    <div>
                                                        <h6 class="mb-0">{{ optional($payment->user)->name ?? 'مستخدم محذوف' }}</h6>
                                                        <small class="text-muted">{{ optional($payment->user)->email ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $payment->booking_id) }}">#{{ $payment->booking_id }}</a>
                                            </td>
                                            <td>{{ number_format($payment->amount, 0) }} ر.س</td>
                                            <td>
                                                @if($payment->payment_method == 'credit_card')
                                                    <span class="badge bg-info">بطاقة ائتمان</span>
                                                @elseif($payment->payment_method == 'bank_transfer')
                                                    <span class="badge bg-primary">تحويل بنكي</span>
                                                @elseif($payment->payment_method == 'wallet')
                                                    <span class="badge bg-success">محفظة إلكترونية</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $payment->payment_method }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-monospace">{{ $payment->transaction_id }}</span>
                                            </td>
                                            <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if($payment->status == 'completed')
                                                    <span class="badge bg-success">مكتمل</span>
                                                @elseif($payment->status == 'pending')
                                                    <span class="badge bg-warning">قيد المعالجة</span>
                                                @elseif($payment->status == 'failed')
                                                    <span class="badge bg-danger">مرفوض</span>
                                                @elseif($payment->status == 'refunded')
                                                    <span class="badge bg-info">مسترجع</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="visually-hidden">المزيد</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($payment->status == 'pending')
                                                        <li>
                                                            <form action="{{ route('admin.payments.complete', $payment->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-check-circle me-1"></i> تأكيد الدفع
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.payments.fail', $payment->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-times-circle me-1"></i> رفض الدفع
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        @if($payment->status == 'completed')
                                                        <li>
                                                            <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-info">
                                                                    <i class="fas fa-undo me-1"></i> استرجاع المبلغ
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a href="{{ route('admin.payments.invoice', $payment->id) }}" class="dropdown-item">
                                                                <i class="fas fa-file-invoice me-1"></i> عرض الفاتورة
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('admin.payments.receipt', $payment->id) }}" class="dropdown-item">
                                                                <i class="fas fa-receipt me-1"></i> طباعة الإيصال
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">لا توجد مدفوعات</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- ترقيم الصفحات -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $payments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات طرق الدفع -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">طرق الدفع</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="paymentMethodsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">حالات المدفوعات</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="paymentStatusChart"></canvas>
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
    .stats-card {
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        color: white;
        font-size: 1.5rem;
    }
    .stats-title {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .stats-value {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .text-monospace {
        font-family: monospace;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // رسم بياني للمدفوعات
        const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
        const paymentsChart = new Chart(paymentsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($paymentsChartLabels) !!},
                datasets: [{
                    label: 'المدفوعات المكتملة',
                    data: {!! json_encode($completedPaymentsData) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }, {
                    label: 'المدفوعات قيد المعالجة',
                    data: {!! json_encode($pendingPaymentsData) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }, {
                    label: 'المدفوعات المرفوضة',
                    data: {!! json_encode($failedPaymentsData) !!},
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' ر.س';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toLocaleString() + ' ر.س';
                            }
                        }
                    }
                }
            }
        });

        // رسم بياني لطرق الدفع
        const methodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        const methodsChart = new Chart(methodsCtx, {
            type: 'doughnut',
            data: {
                labels: ['بطاقة ائتمان', 'تحويل بنكي', 'محفظة إلكترونية', 'أخرى'],
                datasets: [{
                    data: {!! json_encode($paymentMethodsData) !!},
                    backgroundColor: [
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgba(23, 162, 184, 1)',
                        'rgba(0, 123, 255, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // رسم بياني لحالات المدفوعات
        const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['مكتمل', 'قيد المعالجة', 'مرفوض', 'مسترجع'],
                datasets: [{
                    data: {!! json_encode($paymentStatusData) !!},
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(23, 162, 184, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // تغيير فترة الرسم البياني للمدفوعات
        const periodButtons = document.querySelectorAll('.payment-period');
        periodButtons.forEach(button => {
            button.addEventListener('click', function() {
                // إزالة الفئة النشطة من جميع الأزرار
                periodButtons.forEach(btn => btn.classList.remove('active'));
                // إضافة الفئة النشطة للزر المحدد
                this.classList.add('active');
                
                const period = this.getAttribute('data-period');
                
                // إرسال طلب AJAX لجلب البيانات الجديدة
                fetch(`/admin/payments/chart-data?period=${period}`)
                    .then(response => response.json())
                    .then(data => {
                        // تحديث بيانات الرسم البياني
                        paymentsChart.data.labels = data.labels;
                        paymentsChart.data.datasets[0].data = data.completed;
                        paymentsChart.data.datasets[1].data = data.pending;
                        paymentsChart.data.datasets[2].data = data.failed;
                        paymentsChart.update();
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            });
        });
    });
</script>
@endsection
