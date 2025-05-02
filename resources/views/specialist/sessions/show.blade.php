@extends('layouts.specialist')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الجلسة</h1>
        <a href="{{ route('specialist.sessions') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> العودة للجلسات
        </a>
    </div>

    <!-- بطاقة تفاصيل الجلسة -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الجلسة</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>رقم الجلسة:</strong> {{ $session->id }}</p>
                            <p><strong>المستخدم:</strong> {{ $session->booking->user->name }}</p>
                            <p><strong>البريد الإلكتروني:</strong> {{ $session->booking->user->email }}</p>
                            <p><strong>الخدمة:</strong> {{ $session->booking->service->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>تاريخ البدء:</strong> {{ $session->start_time->format('Y-m-d H:i') }}</p>
                            <p><strong>تاريخ الانتهاء:</strong> {{ $session->end_time->format('Y-m-d H:i') }}</p>
                            <p><strong>المدة:</strong> {{ $session->start_time->diffInMinutes($session->end_time) }} دقيقة</p>
                            <p>
                                <strong>الحالة:</strong>
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
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>ملاحظات:</h5>
                            <p>{{ $session->notes ?? 'لا توجد ملاحظات' }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- نموذج تحديث حالة الجلسة -->
                    <form action="{{ route('specialist.sessions.update-status', $session->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">تحديث الحالة:</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="scheduled" {{ $session->status == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                        <option value="in-progress" {{ $session->status == 'in-progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="completed" {{ $session->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                        <option value="cancelled" {{ $session->status == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">ملاحظات جديدة:</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $session->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">تحديث الجلسة</button>
                        </div>
                    </form>
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
                    @if($session->booking->user->avatar)
                        <img src="{{ asset('storage/' . $session->booking->user->avatar) }}" alt="صورة المستخدم" class="img-profile rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('img/default-avatar.png') }}" alt="صورة المستخدم" class="img-profile rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                    
                    <h5>{{ $session->booking->user->name }}</h5>
                    <p class="text-muted">{{ $session->booking->user->email }}</p>
                    
                    <hr>
                    
                    <div class="text-left">
                        <p><strong>رقم الهاتف:</strong> {{ $session->booking->user->phone ?? 'غير متوفر' }}</p>
                        <p><strong>تاريخ التسجيل:</strong> {{ $session->booking->user->created_at->format('Y-m-d') }}</p>
                        <p><strong>عدد الجلسات السابقة:</strong> {{ $session->booking->user->bookings->count() }}</p>
                    </div>
                    
                    <a href="#" class="btn btn-info btn-sm mt-3" data-toggle="modal" data-target="#contactModal">
                        <i class="fas fa-envelope"></i> التواصل مع المستخدم
                    </a>
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
                <h5 class="modal-title" id="contactModalLabel">إرسال رسالة إلى {{ $session->booking->user->name }}</h5>
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
