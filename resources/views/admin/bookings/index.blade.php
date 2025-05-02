@extends('layouts.dashboard')

@section('title', 'إدارة الحجوزات - نفسجي')

@section('content')
<div class="content-wrapper">
    <!-- رأس الصفحة -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">إدارة الحجوزات</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">الحجوزات</li>
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
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h5 class="stats-title">إجمالي الحجوزات</h5>
                            <h2 class="stats-value">{{ $totalBookings }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5 class="stats-title">الحجوزات المؤكدة</h5>
                            <h2 class="stats-value">{{ $confirmedBookings }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="stats-title">قيد الانتظار</h5>
                            <h2 class="stats-value">{{ $pendingBookings }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-icon bg-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h5 class="stats-title">الحجوزات الملغاة</h5>
                            <h2 class="stats-value">{{ $cancelledBookings }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قائمة الحجوزات -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">قائمة الحجوزات</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.bookings.export') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i> تصدير
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- أدوات البحث والتصفية -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <form action="{{ route('admin.bookings.index') }}" method="GET" class="d-flex">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="البحث عن حجز..." value="{{ request('search') }}">
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
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index') }}">الكل</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'pending']) }}">قيد الانتظار</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}">مؤكد</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'completed']) }}">مكتمل</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}">ملغي</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['date' => 'today']) }}">اليوم</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['date' => 'tomorrow']) }}">غداً</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['date' => 'this_week']) }}">هذا الأسبوع</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['date' => 'next_week']) }}">الأسبوع القادم</a></li>
                                            </ul>
                                        </div>
                                        <div class="btn-group ms-2">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-sort me-1"></i> ترتيب
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['sort' => 'date', 'direction' => 'asc']) }}">التاريخ (تصاعدي)</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['sort' => 'date', 'direction' => 'desc']) }}">التاريخ (تنازلي)</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['sort' => 'created_at', 'direction' => 'desc']) }}">تاريخ الحجز (الأحدث)</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['sort' => 'amount', 'direction' => 'desc']) }}">المبلغ (الأعلى)</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- جدول الحجوزات -->
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>رقم الحجز</th>
                                            <th>العميل</th>
                                            <th>المختص</th>
                                            <th>الخدمة</th>
                                            <th>التاريخ والوقت</th>
                                            <th>المبلغ</th>
                                            <th>طريقة الدفع</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $booking->user->avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $booking->user->name }}" class="rounded-circle me-2" width="40">
                                                    <div>
                                                        <h6 class="mb-0">{{ $booking->user->name }}</h6>
                                                        <small class="text-muted">{{ $booking->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $booking->specialist->avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $booking->specialist->name }}" class="rounded-circle me-2" width="40">
                                                    <div>
                                                        <h6 class="mb-0">{{ $booking->specialist->name }}</h6>
                                                        <small class="text-muted">{{ $booking->specialist->specialization }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $booking->service->name }}</td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-calendar-day me-1"></i>
                                                    {{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '—' }}
                                                </div>
                                                <div>
                                                    <i class="fas fa-clock me-1"></i> {{ $booking->time }}
                                                </div>
                                            </td>
                                            <td>{{ number_format($booking->amount, 0) }} ر.س</td>
                                            <td>
                                                @if($booking->payment_method == 'credit_card')
                                                    <span class="badge bg-info">بطاقة ائتمان</span>
                                                @elseif($booking->payment_method == 'bank_transfer')
                                                    <span class="badge bg-primary">تحويل بنكي</span>
                                                @elseif($booking->payment_method == 'wallet')
                                                    <span class="badge bg-success">محفظة إلكترونية</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $booking->payment_method }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge bg-success">مؤكد</span>
                                                @elseif($booking->status == 'completed')
                                                    <span class="badge bg-info">مكتمل</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-danger">ملغي</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <span class="visually-hidden">المزيد</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($booking->status == 'pending')
                                                        <li>
                                                            <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-check-circle me-1"></i> تأكيد الحجز
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        @if($booking->status == 'confirmed')
                                                        <li>
                                                            <form action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-info">
                                                                    <i class="fas fa-check-double me-1"></i> إكمال الجلسة
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        @if($booking->status != 'cancelled' && $booking->status != 'completed')
                                                        <li>
                                                            <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-times-circle me-1"></i> إلغاء الحجز
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="dropdown-item">
                                                                <i class="fas fa-edit me-1"></i> تعديل
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-1"></i> حذف
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">لا توجد حجوزات</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- ترقيم الصفحات -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bookings->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تقويم الحجوزات -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">تقويم الحجوزات</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-bs-toggle="collapse" data-bs-target="#calendarCollapse" aria-expanded="true" aria-controls="calendarCollapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="collapse show" id="calendarCollapse">
                            <div class="card-body">
                                <div id="bookingsCalendar"></div>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<style>
    .fc-event {
        cursor: pointer;
    }
    .fc-event-title {
        font-weight: bold;
    }
    .fc-event-time {
        font-size: 0.9em;
    }
    .fc-day-today {
        background-color: rgba(106, 76, 147, 0.1) !important;
    }
    .fc-button-primary {
        background-color: #6a4c93 !important;
        border-color: #6a4c93 !important;
    }
    .fc-button-primary:hover {
        background-color: #5a3d83 !important;
        border-color: #5a3d83 !important;
    }
    .fc-button-active {
        background-color: #5a3d83 !important;
        border-color: #5a3d83 !important;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ar.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة التقويم
        const calendarEl = document.getElementById('bookingsCalendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            direction: 'rtl',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'اليوم',
                month: 'شهر',
                week: 'أسبوع',
                day: 'يوم',
                list: 'قائمة'
            },
            themeSystem: 'bootstrap',
            events: {!! json_encode($calendarEvents) !!},
            eventClick: function(info) {
                // عرض تفاصيل الحجز عند النقر على الحدث
                window.location.href = `/admin/bookings/${info.event.id}`;
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
                hour12: false
            },
            eventClassNames: function(arg) {
                // تعيين لون الحدث بناءً على حالة الحجز
                if (arg.event.extendedProps.status === 'pending') {
                    return ['bg-warning'];
                } else if (arg.event.extendedProps.status === 'confirmed') {
                    return ['bg-success'];
                } else if (arg.event.extendedProps.status === 'completed') {
                    return ['bg-info'];
                } else if (arg.event.extendedProps.status === 'cancelled') {
                    return ['bg-danger'];
                }
                return [];
            }
        });
        calendar.render();
    });
</script>
@endsection
