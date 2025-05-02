@extends('layouts.app')

@section('title', 'حجز خدمة ' . $service->name)

@section('content')
<div class="booking-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="booking-form-container">
                    <h2 class="section-title">حجز خدمة {{ $service->name }}</h2>
                    <p class="section-description">يرجى ملء النموذج التالي لحجز الخدمة</p>
                    
                    <form action="{{ route('booking.store') }}" method="POST" class="booking-form">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        
                        <div class="form-group">
                            <label for="specialist_id">اختر المختص</label>
                            <select name="specialist_id" id="specialist_id" class="form-control @error('specialist_id') is-invalid @enderror" required>
                                <option value="">-- اختر المختص --</option>
                                @foreach($specialists as $specialist)
                                <option value="{{ $specialist->id }}">{{ $specialist->name }} - {{ $specialist->specialization }}</option>
                                @endforeach
                            </select>
                            @error('specialist_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="booking_date">تاريخ الحجز</label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control @error('booking_date') is-invalid @enderror" required min="{{ date('Y-m-d') }}">
                            @error('booking_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="booking_time">وقت الحجز</label>
                            <select name="booking_time" id="booking_time" class="form-control @error('booking_time') is-invalid @enderror" required>
                                <option value="">-- اختر الوقت --</option>
                                <option value="09:00">09:00 صباحاً</option>
                                <option value="10:00">10:00 صباحاً</option>
                                <option value="11:00">11:00 صباحاً</option>
                                <option value="12:00">12:00 ظهراً</option>
                                <option value="13:00">01:00 مساءً</option>
                                <option value="14:00">02:00 مساءً</option>
                                <option value="15:00">03:00 مساءً</option>
                                <option value="16:00">04:00 مساءً</option>
                                <option value="17:00">05:00 مساءً</option>
                            </select>
                            @error('booking_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">ملاحظات إضافية</label>
                            <textarea name="notes" id="notes" rows="4" class="form-control @error('notes') is-invalid @enderror"></textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">تأكيد الحجز</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="service-summary">
                    <h3>تفاصيل الخدمة</h3>
                    <div class="service-image">
                        <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="img-fluid">
                    </div>
                    <h4>{{ $service->name }}</h4>
                    <div class="service-price">
                        <span class="price">{{ $service->price }} ريال</span>
                        @if($service->old_price)
                        <span class="old-price">{{ $service->old_price }} ريال</span>
                        @endif
                    </div>
                    <div class="service-description">
                        <p>{{ $service->short_description }}</p>
                    </div>
                    <div class="service-features">
                        <h5>مميزات الخدمة</h5>
                        <ul>
                            @if(isset($service->features) && is_array($service->features))
                                @foreach($service->features as $feature)
                                <li>{{ $feature }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                
                <div class="booking-help">
                    <h5>تحتاج مساعدة؟</h5>
                    <p>يمكنك التواصل معنا عبر:</p>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> <a href="tel:+966500000000">+966 50 000 0000</a></li>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:info@nafsaji.com">info@nafsaji.com</a></li>
                        <li><i class="fas fa-comments"></i> <a href="#" class="open-chat">المحادثة المباشرة</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديث أوقات الحجز المتاحة عند اختيار المختص والتاريخ
        $('#specialist_id, #booking_date').change(function() {
            const specialistId = $('#specialist_id').val();
            const bookingDate = $('#booking_date').val();
            
            if (specialistId && bookingDate) {
                // يمكن هنا إضافة طلب AJAX لجلب الأوقات المتاحة من الخادم
                // $.ajax({
                //     url: '/api/available-times',
                //     method: 'GET',
                //     data: { specialist_id: specialistId, date: bookingDate, service_id: {{ $service->id }} },
                //     success: function(response) {
                //         // تحديث قائمة الأوقات المتاحة
                //         updateAvailableTimes(response.times);
                //     }
                // });
            }
        });
        
        function updateAvailableTimes(times) {
            const timeSelect = $('#booking_time');
            timeSelect.empty();
            timeSelect.append('<option value="">-- اختر الوقت --</option>');
            
            times.forEach(function(time) {
                timeSelect.append(`<option value="${time.value}">${time.label}</option>`);
            });
        }
    });
</script>
@endsection
@endsection
