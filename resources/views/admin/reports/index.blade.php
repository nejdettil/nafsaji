@extends('layouts.dashboard')

@section('title', 'التقارير - لوحة تحكم المدير')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تقارير النظام</h4>
                </div>
                <div class="card-body">
                    <!-- ملخص الإحصائيات -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">إجمالي المستخدمين</h5>
                                    <h2 class="mt-4 mb-2">{{ $totalUsers }}</h2>
                                    <p class="mb-0">
                                        <a href="{{ route('admin.reports.users') }}" class="text-white">عرض التفاصيل <i class="fas fa-arrow-right"></i></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">إجمالي المختصين</h5>
                                    <h2 class="mt-4 mb-2">{{ $totalSpecialists }}</h2>
                                    <p class="mb-0">
                                        <a href="{{ route('admin.reports.specialists') }}" class="text-white">عرض التفاصيل <i class="fas fa-arrow-right"></i></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">إجمالي الحجوزات</h5>
                                    <h2 class="mt-4 mb-2">{{ $totalBookings }}</h2>
                                    <p class="mb-0">
                                        <a href="{{ route('admin.reports.bookings') }}" class="text-white">عرض التفاصيل <i class="fas fa-arrow-right"></i></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">إجمالي المدفوعات</h5>
                                    <h2 class="mt-4 mb-2">{{ number_format($totalPayments) }} ر.س</h2>
                                    <p class="mb-0">
                                        <a href="{{ route('admin.reports.payments') }}" class="text-white">عرض التفاصيل <i class="fas fa-arrow-right"></i></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- رسم بياني للمدفوعات الشهرية -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">المدفوعات الشهرية</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyPaymentsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- رسم بياني للحجوزات حسب الحالة -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">الحجوزات حسب الحالة</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="bookingStatusChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- روابط سريعة للتقارير -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">التقارير المتاحة</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <a href="{{ route('admin.reports.bookings') }}" class="btn btn-outline-primary btn-block mb-3">
                                                <i class="fas fa-calendar-check fa-2x mb-2"></i><br>
                                                تقارير الحجوزات
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-success btn-block mb-3">
                                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i><br>
                                                تقارير المدفوعات
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <a href="{{ route('admin.reports.users') }}" class="btn btn-outline-info btn-block mb-3">
                                                <i class="fas fa-users fa-2x mb-2"></i><br>
                                                تقارير المستخدمين
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <a href="{{ route('admin.reports.specialists') }}" class="btn btn-outline-warning btn-block mb-3">
                                                <i class="fas fa-user-md fa-2x mb-2"></i><br>
                                                تقارير المختصين
                                            </a>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // رسم بياني للمدفوعات الشهرية
    var monthlyPaymentsCtx = document.getElementById('monthlyPaymentsChart').getContext('2d');
    var monthlyPaymentsChart = new Chart(monthlyPaymentsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'المدفوعات الشهرية (ر.س)',
                data: {!! json_encode($paymentAmounts) !!},
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

    // رسم بياني للحجوزات حسب الحالة
    var bookingStatusCtx = document.getElementById('bookingStatusChart').getContext('2d');
    var bookingStatusData = {
        labels: [
            'قيد الانتظار',
            'مؤكدة',
            'مكتملة',
            'ملغاة'
        ],
        datasets: [{
            data: [
                {{ $bookingsByStatus['pending'] ?? 0 }},
                {{ $bookingsByStatus['confirmed'] ?? 0 }},
                {{ $bookingsByStatus['completed'] ?? 0 }},
                {{ $bookingsByStatus['cancelled'] ?? 0 }}
            ],
            backgroundColor: [
                'rgba(255, 206, 86, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 99, 132, 0.7)'
            ],
            borderColor: [
                'rgba(255, 206, 86, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    };
    var bookingStatusChart = new Chart(bookingStatusCtx, {
        type: 'pie',
        data: bookingStatusData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
