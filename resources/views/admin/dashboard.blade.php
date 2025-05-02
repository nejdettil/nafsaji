@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')

@section('content')
    <div class="container-fluid">
        <!-- صف الإحصائيات -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="card-category">المستخدمين</p>
                        <h3 class="card-title">{{ $usersCount ?? 0 }}</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-user-plus"></i>
                            <a href="{{ route('admin.users.index') }}">عرض جميع المستخدمين</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <p class="card-category">المختصين</p>
                        <h3 class="card-title">{{ $specialistsCount ?? 0 }}</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-user-plus"></i>
                            <a href="{{ route('admin.specialists.index') }}">عرض جميع المختصين</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <p class="card-category">الحجوزات</p>
                        <h3 class="card-title">{{ $bookingsCount ?? 0 }}</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-calendar"></i>
                            <a href="{{ route('admin.bookings.index') }}">عرض جميع الحجوزات</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <p class="card-category">المدفوعات</p>
                        <h3 class="card-title">{{ $paymentsTotal ?? 0 }} ريال</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-money-check"></i>
                            <a href="{{ route('admin.payments.index') }}">عرض جميع المدفوعات</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- صف الرسوم البيانية -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-chart">
                    <div class="card-header card-header-success">
                        <h4 class="card-title">الحجوزات الشهرية</h4>
                        <p class="card-category">إحصائيات الحجوزات خلال الأشهر الماضية</p>
                    </div>
                    <div class="card-body">
                        <div id="bookingsChart" style="height: 250px;"></div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-calendar"></i> تم التحديث منذ دقيقة
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-chart">
                    <div class="card-header card-header-warning">
                        <h4 class="card-title">الإيرادات الشهرية</h4>
                        <p class="card-category">إحصائيات الإيرادات خلال الأشهر الماضية</p>
                    </div>
                    <div class="card-body">
                        <div id="revenueChart" style="height: 250px;"></div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-money-bill"></i> تم التحديث منذ دقيقة
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- صف الجداول -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">أحدث الحجوزات</h4>
                        <p class="card-category">قائمة بأحدث الحجوزات في النظام</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-primary">
                                <tr>
                                    <th>رقم الحجز</th>
                                    <th>المستخدم</th>
                                    <th>المختص</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($latestBookings) && count($latestBookings) > 0)
                                    @foreach($latestBookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->specialist->user->name }}</td>
                                            <td>{{ $booking->date }}</td>
                                            <td>{{ $booking->status }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد حجوزات حديثة</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-calendar"></i>
                            <a href="{{ route('admin.bookings.index') }}">عرض جميع الحجوزات</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header card-header-info">
                        <h4 class="card-title">أحدث المستخدمين</h4>
                        <p class="card-category">قائمة بأحدث المستخدمين المسجلين في النظام</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-info">
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($latestUsers) && count($latestUsers) > 0)
                                    @foreach($latestUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">لا يوجد مستخدمين جدد</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fas fa-users"></i>
                            <a href="{{ route('admin.users.index') }}">عرض جميع المستخدمين</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            // بيانات الرسم البياني للحجوزات (يمكن استبدالها ببيانات حقيقية من الخادم)
            var bookingsOptions = {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'الحجوزات',
                    data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
                }],
                xaxis: {
                    categories: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر']
                },
                colors: ['#4caf50']
            };

            var bookingsChart = new ApexCharts(document.querySelector("#bookingsChart"), bookingsOptions);
            bookingsChart.render();

            // بيانات الرسم البياني للإيرادات (يمكن استبدالها ببيانات حقيقية من الخادم)
            var revenueOptions = {
                chart: {
                    type: 'line',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'الإيرادات',
                    data: [10000, 15000, 12000, 20000, 18000, 25000, 30000, 35000, 40000]
                }],
                xaxis: {
                    categories: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر']
                },
                colors: ['#ff9800']
            };

            var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
            revenueChart.render();
        });
    </script>
@endsection
