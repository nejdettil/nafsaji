@extends('layouts.specialist')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">الحجوزات</h1>
    </div>

    <!-- بطاقة الحجوزات -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الحجوزات</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">تصفية حسب:</div>
                    <a class="dropdown-item" href="{{ route('specialist.bookings', ['status' => 'pending']) }}">الحجوزات المعلقة</a>
                    <a class="dropdown-item" href="{{ route('specialist.bookings', ['status' => 'confirmed']) }}">الحجوزات المؤكدة</a>
                    <a class="dropdown-item" href="{{ route('specialist.bookings', ['status' => 'rejected']) }}">الحجوزات المرفوضة</a>
                    <a class="dropdown-item" href="{{ route('specialist.bookings', ['status' => 'cancelled']) }}">الحجوزات الملغاة</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('specialist.bookings') }}">جميع الحجوزات</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="bookingsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>رقم الحجز</th>
                            <th>المستخدم</th>
                            <th>الخدمة</th>
                            <th>التاريخ المفضل</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ $booking->service->name }}</td>
                                <td>{{ $booking->preferred_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $booking->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $booking->status == 'pending' ? 'warning' : ($booking->status == 'confirmed' ? 'success' : ($booking->status == 'rejected' ? 'danger' : 'secondary')) }}">
                                        @if($booking->status == 'pending')
                                            معلق
                                        @elseif($booking->status == 'confirmed')
                                            مؤكد
                                        @elseif($booking->status == 'rejected')
                                            مرفوض
                                        @else
                                            ملغي
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('specialist.bookings.show', $booking->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد حجوزات</td>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#bookingsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            },
            "paging": false,
            "info": false,
            "searching": true,
            "order": [[4, 'desc']]
        });
    });
</script>
@endsection
