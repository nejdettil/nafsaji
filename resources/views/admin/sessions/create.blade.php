@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إضافة جلسة جديدة</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
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

                    <form action="{{ route('admin.sessions.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="booking_id">الحجز</label>
                            <select name="booking_id" id="booking_id" class="form-control select2" required>
                                <option value="">اختر الحجز</option>
                                @foreach($bookings as $booking)
                                    <option value="{{ $booking->id }}">
                                        {{ $booking->user->name ?? 'غير محدد' }} - 
                                        {{ $booking->specialist->name ?? 'غير محدد' }} - 
                                        {{ $booking->created_at->format('Y-m-d') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="start_time">وقت البدء</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="end_time">وقت الانتهاء</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="scheduled">مجدولة</option>
                                <option value="in-progress">قيد التنفيذ</option>
                                <option value="completed">مكتملة</option>
                                <option value="cancelled">ملغاة</option>
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
