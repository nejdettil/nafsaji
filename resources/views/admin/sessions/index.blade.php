@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إدارة الجلسات</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إضافة جلسة جديدة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المستخدم</th>
                                    <th>المختص</th>
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
                                        <td>{{ $session->booking->user->name ?? 'غير محدد' }}</td>
                                        <td>{{ $session->booking->specialist->name ?? 'غير محدد' }}</td>
                                        <td>{{ $session->start_time }}</td>
                                        <td>{{ $session->end_time }}</td>
                                        <td>
                                            @if($session->status == 'scheduled')
                                                <span class="badge badge-info">مجدولة</span>
                                            @elseif($session->status == 'in-progress')
                                                <span class="badge badge-warning">قيد التنفيذ</span>
                                            @elseif($session->status == 'completed')
                                                <span class="badge badge-success">مكتملة</span>
                                            @elseif($session->status == 'cancelled')
                                                <span class="badge badge-danger">ملغاة</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.sessions.show', $session->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.sessions.edit', $session->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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

                    <div class="mt-4">
                        {{ $sessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
