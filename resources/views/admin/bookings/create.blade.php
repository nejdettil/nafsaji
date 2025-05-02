@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إضافة حجز جديد</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.bookings.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="user_id">المستخدم</label>
                            <select name="user_id" id="user_id" class="form-control select2" required>
                                <option value="">اختر المستخدم</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="specialist_id">المختص</label>
                            <select name="specialist_id" id="specialist_id" class="form-control select2" required>
                                <option value="">اختر المختص</option>
                                @foreach($specialists as $specialist)
                                    <option value="{{ $specialist->id }}">{{ $specialist->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="service_id">الخدمة</label>
                            <select name="service_id" id="service_id" class="form-control select2" required>
                                <option value="">اختر الخدمة</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->price }} ريال)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="booking_date">تاريخ الحجز</label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="booking_time">وقت الحجز</label>
                            <input type="time" name="booking_time" id="booking_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending">قيد الانتظار</option>
                                <option value="confirmed">مؤكد</option>
                                <option value="completed">مكتمل</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">ملاحظات</label>
                            <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        $('.select2').select2();
    });
</script>
@endsection
