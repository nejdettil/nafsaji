@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل الحجز</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="card-title">معلومات الحجز</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>رقم الحجز</th>
                                            <td>{{ $booking->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الحجز</th>
                                            <td>{{ $booking->booking_date }}</td>
                                        </tr>
                                        <tr>
                                            <th>وقت الحجز</th>
                                            <td>{{ $booking->booking_time }}</td>
                                        </tr>
                                        <tr>
                                            <th>الحالة</th>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge badge-warning">قيد الانتظار</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge badge-info">مؤكد</span>
                                                @elseif($booking->status == 'completed')
                                                    <span class="badge badge-success">مكتمل</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge badge-danger">ملغي</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الإنشاء</th>
                                            <td>{{ $booking->created_at }}</td>
                                        </tr>
                                        <tr>
                                            <th>آخر تحديث</th>
                                            <td>{{ $booking->updated_at }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="card-title">معلومات المستخدم والمختص</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>المستخدم</th>
                                            <td>{{ $booking->user->name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>البريد الإلكتروني</th>
                                            <td>{{ $booking->user->email ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>رقم الهاتف</th>
                                            <td>{{ $booking->user->phone ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>المختص</th>
                                            <td>{{ $booking->specialist->name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>تخصص المختص</th>
                                            <td>{{ $booking->specialist->specialization ?? 'غير محدد' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success">
                                    <h5 class="card-title">معلومات الخدمة</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>الخدمة</th>
                                            <td>{{ $booking->service->name ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>الوصف</th>
                                            <td>{{ $booking->service->description ?? 'غير محدد' }}</td>
                                        </tr>
                                        <tr>
                                            <th>السعر</th>
                                            <td>{{ $booking->service->price ?? 0 }} ريال</td>
                                        </tr>
                                        <tr>
                                            <th>المدة</th>
                                            <td>{{ $booking->service->duration ?? 0 }} دقيقة</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-secondary">
                                    <h5 class="card-title">ملاحظات الحجز</h5>
                                </div>
                                <div class="card-body">
                                    <div class="p-3 bg-light">
                                        {!! nl2br(e($booking->notes)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="btn-group">
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> تعديل الحجز
                                </a>
                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحجز؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> حذف الحجز
                                    </button>
                                </form>
                                
                                @if($booking->status == 'confirmed')
                                <a href="{{ route('admin.sessions.create', ['booking_id' => $booking->id]) }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> إنشاء جلسة
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
