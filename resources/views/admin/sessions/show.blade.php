@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل الجلسة</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="card-title">معلومات الجلسة</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>رقم الجلسة</th>
                                            <td>{{ $session->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>وقت البدء</th>
                                            <td>{{ $session->start_time }}</td>
                                        </tr>
                                        <tr>
                                            <th>وقت الانتهاء</th>
                                            <td>{{ $session->end_time }}</td>
                                        </tr>
                                        <tr>
                                            <th>المدة</th>
                                            <td>
                                                @php
                                                    $start = new DateTime($session->start_time);
                                                    $end = new DateTime($session->end_time);
                                                    $diff = $start->diff($end);
                                                    echo $diff->format('%h ساعة و %i دقيقة');
                                                @endphp
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>الحالة</th>
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
                                        </tr>
                                        <tr>
                                            <th>تاريخ الإنشاء</th>
                                            <td>{{ $session->created_at }}</td>
                                        </tr>
                                        <tr>
                                            <th>آخر تحديث</th>
                                            <td>{{ $session->updated_at }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="card-title">معلومات الحجز</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>رقم الحجز</th>
                                            <td>{{ $session->booking->id ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>المستخدم</th>
                                            <td>{{ $session->booking->user->name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>المختص</th>
                                            <td>{{ $session->booking->specialist->name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>الخدمة</th>
                                            <td>{{ $session->booking->service->name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الحجز</th>
                                            <td>{{ $session->booking->created_at ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-secondary">
                                    <h5 class="card-title">ملاحظات الجلسة</h5>
                                </div>
                                <div class="card-body">
                                    <div class="p-3 bg-light">
                                        {!! nl2br(e($session->notes)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="btn-group">
                                <a href="{{ route('admin.sessions.edit', $session->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> تعديل الجلسة
                                </a>
                                <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> حذف الجلسة
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
