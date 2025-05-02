@extends('layouts.app')

@section('title', 'إنشاء حجز جديد')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">إنشاء حجز جديد</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('booking.store') }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="service_id" class="col-md-4 col-form-label text-md-end">الخدمة</label>
                            <div class="col-md-6">
                                <select id="service_id" class="form-control @error('service_id') is-invalid @enderror" name="service_id" required>
                                    <option value="">-- اختر الخدمة --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} ({{ $service->price }} ر.س)
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="specialist_id" class="col-md-4 col-form-label text-md-end">المختص</label>
                            <div class="col-md-6">
                                <select id="specialist_id" class="form-control @error('specialist_id') is-invalid @enderror" name="specialist_id" required>
                                    <option value="">-- اختر المختص --</option>
                                    @foreach($specialists as $specialist)
                                        <option value="{{ $specialist->id }}" {{ old('specialist_id') == $specialist->id ? 'selected' : '' }}>
                                            {{ $specialist->name }} ({{ $specialist->specialization }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('specialist_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="preferred_date" class="col-md-4 col-form-label text-md-end">التاريخ المفضل</label>
                            <div class="col-md-6">
                                <input id="preferred_date" type="date" class="form-control @error('preferred_date') is-invalid @enderror" name="preferred_date" value="{{ old('preferred_date') }}" required>
                                @error('preferred_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="preferred_time" class="col-md-4 col-form-label text-md-end">الوقت المفضل</label>
                            <div class="col-md-6">
                                <select id="preferred_time" class="form-control @error('preferred_time') is-invalid @enderror" name="preferred_time" required>
                                    <option value="">-- اختر الوقت --</option>
                                    <option value="morning" {{ old('preferred_time') == 'morning' ? 'selected' : '' }}>صباحاً (9:00 - 12:00)</option>
                                    <option value="afternoon" {{ old('preferred_time') == 'afternoon' ? 'selected' : '' }}>ظهراً (12:00 - 3:00)</option>
                                    <option value="evening" {{ old('preferred_time') == 'evening' ? 'selected' : '' }}>مساءً (3:00 - 6:00)</option>
                                    <option value="night" {{ old('preferred_time') == 'night' ? 'selected' : '' }}>ليلاً (6:00 - 9:00)</option>
                                </select>
                                @error('preferred_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="notes" class="col-md-4 col-form-label text-md-end">ملاحظات</label>
                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="4">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    إنشاء الحجز
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
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
