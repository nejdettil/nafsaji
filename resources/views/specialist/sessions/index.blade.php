@extends('layouts.specialist')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">الجلسات</h1>
    </div>

    <!-- بطاقة الجلسات -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الجلسات</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">تصفية حسب:</div>
                    <a class="dropdown-item" href="{{ route('specialist.sessions', ['status' => 'scheduled']) }}">الجلسات المجدولة</a>
                    <a class="dropdown-item" href="{{ route('specialist.sessions', ['status' => 'in-progress']) }}">الجلسات الجارية</a>
                    <a class="dropdown-item" href="{{ route('specialist.sessions', ['status' => 'completed']) }}">الجلسات المكتملة</a>
                    <a class="dropdown-item" href="{{ route('specialist.sessions', ['status' => 'cancelled']) }}">الجلسات الملغاة</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('specialist.sessions') }}">جميع الجلسات</a>
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
                <table class="table table-bordered" id="sessionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>رقم الجلسة</th>
                            <th>المستخدم</th>
                            <th>الخدمة</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>{{ $session->id }}</td>
                                <td>{{ $session->booking->user->name }}</td>
                                <td>{{ $session->booking->service->name }}</td>
                                <td>{{ $session->start_time->format('Y-m-d H:i') }}</td>
                                <td>{{ $session->end_time->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $session->status == 'scheduled' ? 'warning' : ($session->status == 'in-progress' ? 'info' : ($session->status == 'completed' ? 'success' : 'danger')) }}">
                                        @if($session->status == 'scheduled')
                                            مجدولة
                                        @elseif($session->status == 'in-progress')
                                            قيد التنفيذ
                                        @elseif($session->status == 'completed')
                                            مكتملة
                                        @else
                                            ملغية
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('specialist.sessions.show', $session->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد جلسات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ترقيم الصفحات -->
            <div class="d-flex justify-content-center mt-4">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#sessionsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            },
            "paging": false,
            "info": false,
            "searching": true,
            "order": [[3, 'desc']]
        });
    });
</script>
@endsection
