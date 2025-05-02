@extends('layouts.specialist')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الحجز</h1>
        <a href="{{ route('specialist.bookings') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> العودة للحجوزات
        </a>
    </div>

    <!-- بطاقة تفاصيل الحجز -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الحجز</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>رقم الحجز:</strong> {{ $booking->id }}</p>
                            <p><strong>المستخدم:</strong> {{ $booking->user->name }}</p>
                            <p><strong>البريد الإلكتروني:</strong> {{ $booking->user->email }}</p>
                            <p><strong>الخدمة:</strong> {{ $booking->service->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>التاريخ المفضل:</strong> {{ $booking->preferred_date->format('Y-m-d H:i') }}</p>
                            <p><strong>تاريخ الطلب:</strong> {{ $booking->created_at->format('Y-m-d H:i') }}</p>
                            <p>
                                <strong>الحالة:</strong>
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
                            </p>
                            <p><strong>السعر:</strong> {{ $booking->service->price }} ريال</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>تفاصيل الطلب:</h5>
                            <p>{{ $booking->details ?? 'لا توجد تفاصيل إضافية' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>ملاحظات:</h5>
                            <p>{{ $booking->notes ?? 'لا توجد ملاحظات' }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- نموذج تحديث حالة الحجز -->
                    @if($booking->status == 'pending')
                    <form action="{{ route('specialist.bookings.update-status', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">تحديث الحالة:</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>معلق</option>
                                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                        <option value="rejected" {{ $booking->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">ملاحظات:</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">تحديث الحالة</button>
                        </div>
                    </form>
                    @endif

                    <!-- معلومات الجلسة إذا كان الحجز مؤكداً -->
                    @if($booking->status == 'confirmed' && $booking->session)
                    <div class="alert alert-info mt-4">
                        <h5>معلومات الجلسة:</h5>
                        <p><strong>رقم الجلسة:</strong> {{ $booking->session->id }}</p>
                        <p><strong>تاريخ البدء:</strong> {{ $booking->session->start_time->format('Y-m-d H:i') }}</p>
                        <p><strong>تاريخ الانتهاء:</strong> {{ $booking->session->end_time->format('Y-m-d H:i') }}</p>
                        <p>
                            <strong>حالة الجلسة:</strong>
                            <span class="badge badge-{{ $booking->session->status == 'scheduled' ? 'warning' : ($booking->session->status == 'in-progress' ? 'info' : ($booking->session->status == 'completed' ? 'success' : 'danger')) }}">
                                @if($booking->session->status == 'scheduled')
                                    مجدولة
                                @elseif($booking->session->status == 'in-progress')
                                    قيد التنفيذ
                                @elseif($booking->session->status == 'completed')
                                    مكتملة
                                @else
                                    ملغية
                                @endif
                            </span>
                        </p>
                        <div class="text-center mt-3">
                            <a href="{{ route('specialist.sessions.show', $booking->session->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> عرض تفاصيل الجلسة
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- بطاقة معلومات المستخدم -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المستخدم</h6>
                </div>
                <div class="card-body text-center">
                    @if($booking->user->avatar)
                        <img src="{{ asset('storage/' . $booking->user->avatar) }}" alt="صورة المستخدم" class="img-profile rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('img/default-avatar.png') }}" alt="صورة المستخدم" class="img-profile rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                    
                    <h5>{{ $booking->user->name }}</h5>
                    <p class="text-muted">{{ $booking->user->email }}</p>
                    
                    <hr>
                    
                    <div class="text-left">
                        <p><strong>رقم الهاتف:</strong> {{ $booking->user->phone ?? 'غير متوفر' }}</p>
                        <p><strong>تاريخ التسجيل:</strong> {{ $booking->user->created_at->format('Y-m-d') }}</p>
                        <p><strong>عدد الحجوزات السابقة:</strong> {{ $booking->user->bookings->count() }}</p>
                    </div>
                    
                    <a href="#" class="btn btn-info btn-sm mt-3" data-toggle="modal" data-target="#contactModal">
                        <i class="fas fa-envelope"></i> التواصل مع المستخدم
                    </a>
                </div>
            </div>

            <!-- بطاقة معلومات الخدمة -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الخدمة</h6>
                </div>
                <div class="card-body">
                    <h5 class="text-center mb-3">{{ $booking->service->name }}</h5>
                    
                    <p><strong>الوصف:</strong> {{ $booking->service->description }}</p>
                    <p><strong>المدة:</strong> {{ $booking->service->duration }} دقيقة</p>
                    <p><strong>السعر:</strong> {{ $booking->service->price }} ريال</p>
                    
                    @if($booking->service->is_online)
                        <p><span class="badge badge-success">خدمة عبر الإنترنت</span></p>
                    @else
                        <p><span class="badge badge-info">خدمة حضورية</span></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة التواصل مع المستخدم -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">إرسال رسالة إلى {{ $booking->user->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="messageSubject">الموضوع</label>
                        <input type="text" class="form-control" id="messageSubject">
                    </div>
                    <div class="form-group">
                        <label for="messageContent">نص الرسالة</label>
                        <textarea class="form-control" id="messageContent" rows="5"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary">إرسال</button>
            </div>
        </div>
    </div>
</div>
@endsection
