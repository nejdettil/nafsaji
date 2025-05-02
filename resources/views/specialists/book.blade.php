@extends('layouts.app')

@section('title', 'حجز جلسة مع ' . $specialist->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">حجز جلسة مع {{ $specialist->name }}</h2>
                    
                    <div class="specialist-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $specialist->avatar ?? asset('assets/images/avatar-placeholder.jpg') }}" alt="{{ $specialist->name }}" class="rounded-circle me-3" width="60" height="60">
                            <div>
                                <h5 class="mb-1">{{ $specialist->name }}</h5>
                                <p class="text-muted mb-0">{{ $specialist->specialization }}</p>
                            </div>
                        </div>
                        <p>{{ $specialist->bio }}</p>
                    </div>

                    <form action="{{ route('bookings.store') }}" method="POST" id="booking-form">
                        @csrf
                        <input type="hidden" name="specialist_id" value="{{ $specialist->id }}">
                        
                        <div class="mb-4">
                            <h5>اختر الخدمة</h5>
                            <div class="row">
                                @foreach($services as $service)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="service_id" id="service-{{ $service->id }}" value="{{ $service->id }}" required>
                                        <label class="form-check-label d-flex justify-content-between" for="service-{{ $service->id }}">
                                            <span>{{ $service->name }}</span>
                                            <span class="text-primary">{{ $service->price }} ر.س</span>
                                        </label>
                                        <small class="d-block text-muted">{{ $service->duration }} دقيقة</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        @if(count($packages) > 0)
                        <div class="mb-4">
                            <h5>أو اختر باقة</h5>
                            <div class="row">
                                @foreach($packages as $package)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="package_id" id="package-{{ $package->id }}" value="{{ $package->id }}">
                                        <label class="form-check-label d-flex justify-content-between" for="package-{{ $package->id }}">
                                            <span>{{ $package->name }}</span>
                                            <span class="text-primary">{{ $package->price }} ر.س</span>
                                        </label>
                                        <small class="d-block text-muted">{{ $package->description }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <h5>اختر التاريخ والوقت</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="booking-date" class="form-label">التاريخ</label>
                                    <input type="date" class="form-control" id="booking-date" name="date" required min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="booking-time" class="form-label">الوقت</label>
                                    <select class="form-select" id="booking-time" name="time" required>
                                        <option value="" selected disabled>اختر الوقت</option>
                                        <!-- سيتم تحميل الأوقات المتاحة عبر JavaScript بناءً على التاريخ المختار -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>معلومات إضافية</h5>
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أي معلومات إضافية ترغب في مشاركتها مع المختص"></textarea>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">تأكيد الحجز</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">ملخص الحجز</h5>
                    <div id="booking-summary">
                        <p class="mb-1"><strong>المختص:</strong> <span>{{ $specialist->name }}</span></p>
                        <p class="mb-1"><strong>الخدمة:</strong> <span id="selected-service">لم يتم الاختيار بعد</span></p>
                        <p class="mb-1"><strong>التاريخ:</strong> <span id="selected-date">لم يتم الاختيار بعد</span></p>
                        <p class="mb-1"><strong>الوقت:</strong> <span id="selected-time">لم يتم الاختيار بعد</span></p>
                        <p class="mb-4"><strong>السعر:</strong> <span id="selected-price">0</span> ر.س</p>
                        
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            سيتم تأكيد الحجز بعد إتمام عملية الدفع.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">معلومات مهمة</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> يمكنك إلغاء الحجز قبل 24 ساعة من الموعد.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> ستصلك رسالة تأكيد بعد إتمام الحجز.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> يرجى الالتزام بالموعد المحدد.</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> للاستفسارات، يرجى التواصل مع خدمة العملاء.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookingForm = document.getElementById('booking-form');
        const serviceRadios = document.querySelectorAll('input[name="service_id"]');
        const packageRadios = document.querySelectorAll('input[name="package_id"]');
        const dateInput = document.getElementById('booking-date');
        const timeSelect = document.getElementById('booking-time');
        
        // عناصر ملخص الحجز
        const selectedServiceElement = document.getElementById('selected-service');
        const selectedDateElement = document.getElementById('selected-date');
        const selectedTimeElement = document.getElementById('selected-time');
        const selectedPriceElement = document.getElementById('selected-price');
        
        // تحديث ملخص الحجز عند اختيار خدمة
        serviceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    // إلغاء تحديد الباقات إذا تم اختيار خدمة
                    packageRadios.forEach(packageRadio => {
                        packageRadio.checked = false;
                    });
                    
                    const serviceName = this.nextElementSibling.querySelector('span').textContent;
                    const servicePrice = this.nextElementSibling.querySelector('span.text-primary').textContent;
                    
                    selectedServiceElement.textContent = serviceName;
                    selectedPriceElement.textContent = servicePrice.replace(' ر.س', '');
                }
            });
        });
        
        // تحديث ملخص الحجز عند اختيار باقة
        packageRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    // إلغاء تحديد الخدمات إذا تم اختيار باقة
                    serviceRadios.forEach(serviceRadio => {
                        serviceRadio.checked = false;
                    });
                    
                    const packageName = this.nextElementSibling.querySelector('span').textContent;
                    const packagePrice = this.nextElementSibling.querySelector('span.text-primary').textContent;
                    
                    selectedServiceElement.textContent = packageName + ' (باقة)';
                    selectedPriceElement.textContent = packagePrice.replace(' ر.س', '');
                }
            });
        });
        
        // تحديث ملخص الحجز عند اختيار التاريخ
        dateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const formattedDate = new Intl.DateTimeFormat('ar-SA', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(selectedDate);
            
            selectedDateElement.textContent = formattedDate;
            
            // تحميل الأوقات المتاحة بناءً على التاريخ المختار
            loadAvailableTimes(this.value);
        });
        
        // تحديث ملخص الحجز عند اختيار الوقت
        timeSelect.addEventListener('change', function() {
            selectedTimeElement.textContent = this.options[this.selectedIndex].text;
        });
        
        // تحميل الأوقات المتاحة
        function loadAvailableTimes(date) {
            // هنا يمكن إضافة طلب AJAX لجلب الأوقات المتاحة من الخادم
            // لأغراض العرض، سنضيف بعض الأوقات الافتراضية
            
            // إفراغ القائمة الحالية
            timeSelect.innerHTML = '<option value="" selected disabled>اختر الوقت</option>';
            
            // إضافة أوقات افتراضية
            const times = [
                { value: '09:00', text: '09:00 صباحاً' },
                { value: '10:00', text: '10:00 صباحاً' },
                { value: '11:00', text: '11:00 صباحاً' },
                { value: '12:00', text: '12:00 ظهراً' },
                { value: '13:00', text: '01:00 مساءً' },
                { value: '14:00', text: '02:00 مساءً' },
                { value: '15:00', text: '03:00 مساءً' },
                { value: '16:00', text: '04:00 مساءً' }
            ];
            
            times.forEach(time => {
                const option = document.createElement('option');
                option.value = time.value;
                option.textContent = time.text;
                timeSelect.appendChild(option);
            });
        }
        
        // التحقق من صحة النموذج قبل الإرسال
        bookingForm.addEventListener('submit', function(event) {
            let isValid = true;
            let errorMessage = '';
            
            // التحقق من اختيار خدمة أو باقة
            const serviceSelected = Array.from(serviceRadios).some(radio => radio.checked);
            const packageSelected = Array.from(packageRadios).some(radio => radio.checked);
            
            if (!serviceSelected && !packageSelected) {
                isValid = false;
                errorMessage = 'يرجى اختيار خدمة أو باقة';
            }
            
            // التحقق من اختيار التاريخ والوقت
            if (!dateInput.value) {
                isValid = false;
                errorMessage = errorMessage ? errorMessage + '\nيرجى اختيار التاريخ' : 'يرجى اختيار التاريخ';
            }
            
            if (!timeSelect.value) {
                isValid = false;
                errorMessage = errorMessage ? errorMessage + '\nيرجى اختيار الوقت' : 'يرجى اختيار الوقت';
            }
            
            if (!isValid) {
                event.preventDefault();
                alert(errorMessage);
            }
        });
    });
</script>
@endsection
