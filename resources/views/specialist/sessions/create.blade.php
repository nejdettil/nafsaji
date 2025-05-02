@extends('layouts.dashboard')

@section('title', 'إنشاء جلسة جديدة - لوحة تحكم المختص')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إنشاء جلسة جديدة</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('specialist.sessions.store') }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="booking_id" class="col-md-3 col-form-label">الحجز المرتبط</label>
                            <div class="col-md-9">
                                <select id="booking_id" class="form-control @error('booking_id') is-invalid @enderror" name="booking_id" required>
                                    <option value="">-- اختر الحجز --</option>
                                    @foreach($availableBookings as $booking)
                                        <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                            {{ $booking->user->name }} - {{ $booking->service->name }} ({{ $booking->preferred_date }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('booking_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @if(count($availableBookings) == 0)
                                    <div class="alert alert-info mt-2">
                                        لا توجد حجوزات مؤكدة متاحة لإنشاء جلسات جديدة. يمكنك تأكيد الحجوزات المعلقة أولاً.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="session_date" class="col-md-3 col-form-label">تاريخ الجلسة</label>
                            <div class="col-md-9">
                                <input id="session_date" type="date" class="form-control @error('session_date') is-invalid @enderror" name="session_date" value="{{ old('session_date') }}" required>
                                @error('session_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="start_time" class="col-md-3 col-form-label">وقت البدء</label>
                            <div class="col-md-9">
                                <input id="start_time" type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="end_time" class="col-md-3 col-form-label">وقت الانتهاء</label>
                            <div class="col-md-9">
                                <input id="end_time" type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="session_type" class="col-md-3 col-form-label">نوع الجلسة</label>
                            <div class="col-md-9">
                                <select id="session_type" class="form-control @error('session_type') is-invalid @enderror" name="session_type" required>
                                    <option value="">-- اختر نوع الجلسة --</option>
                                    <option value="in-person" {{ old('session_type') == 'in-person' ? 'selected' : '' }}>حضوري</option>
                                    <option value="video" {{ old('session_type') == 'video' ? 'selected' : '' }}>فيديو</option>
                                    <option value="voice" {{ old('session_type') == 'voice' ? 'selected' : '' }}>صوتي</option>
                                </select>
                                @error('session_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="notes" class="col-md-3 col-form-label">ملاحظات</label>
                            <div class="col-md-9">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="4">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    إنشاء الجلسة
                                </button>
                                <a href="{{ route('specialist.sessions') }}" class="btn btn-secondary">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
