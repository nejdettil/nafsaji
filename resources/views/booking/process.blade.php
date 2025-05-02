@extends('layouts.app')

@section('title', 'إتمام الحجز - نفسجي')

@section('content')
<div class="booking-process-page">
    <!-- قسم العنوان الرئيسي -->
    <section class="page-header bg-gradient-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">إتمام الحجز</h1>
                    <p class="lead mb-4">أنت على بعد خطوات قليلة من إكمال حجز جلستك</p>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('assets/images/booking-process.svg') }}" alt="إتمام الحجز" class="img-fluid" style="max-height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- قسم مراحل الحجز -->
    <section class="booking-steps py-5">
        <div class="container">
            <div class="booking-progress mb-5">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $step * 25 }}%;" aria-valuenow="{{ $step * 25 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <div class="step {{ $step >= 1 ? 'active' : '' }}">
                        <div class="step-icon">1</div>
                        <div class="step-text">اختيار المختص</div>
                    </div>
                    <div class="step {{ $step >= 2 ? 'active' : '' }}">
                        <div class="step-icon">2</div>
                        <div class="step-text">اختيار الخدمة</div>
                    </div>
                    <div class="step {{ $step >= 3 ? 'active' : '' }}">
                        <div class="step-icon">3</div>
                        <div class="step-text">تحديد الموعد</div>
                    </div>
                    <div class="step {{ $step >= 4 ? 'active' : '' }}">
                        <div class="step-icon">4</div>
                        <div class="step-text">الدفع والتأكيد</div>
                    </div>
                </div>
            </div>

            <!-- محتوى الخطوة الحالية -->
            <div class="booking-step-content">
                @if($step == 1)
                    <!-- اختيار المختص -->
                    <div class="step-content-inner">
                        <h2 class="section-title mb-4">اختر المختص المناسب</h2>
                        <div class="row">
                            @foreach($specialists as $specialist)
                            <div class="col-md-4 mb-4">
                                <div class="specialist-card card h-100">
                                    <div class="card-body">
                                        <div class="specialist-img mb-3">
                                            <img src="{{ $specialist->profile_image }}" alt="{{ $specialist->name }}" class="img-fluid rounded-circle">
                                        </div>
                                        <h3 class="specialist-name">{{ $specialist->name }}</h3>
                                        <p class="specialist-title text-muted">{{ $specialist->title }}</p>
                                        <div class="specialist-rating mb-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $specialist->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="rating-count">({{ $specialist->reviews_count }})</span>
                                        </div>
                                        <p class="specialist-bio">{{ Str::limit($specialist->bio, 100) }}</p>
                                    </div>
                                    <div class="card-footer bg-white border-0">
                                        <a href="{{ route('booking.process', ['step' => 2, 'specialist_id' => $specialist->id]) }}" class="btn btn-primary w-100">اختيار</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($step == 2)
                    <!-- اختيار الخدمة -->
                    <div class="step-content-inner">
                        <h2 class="section-title mb-4">اختر الخدمة المناسبة</h2>
                        <div class="selected-specialist mb-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $specialist->profile_image }}" alt="{{ $specialist->name }}" class="rounded-circle" width="60" height="60">
                                <div class="ms-3">
                                    <h4 class="mb-0">{{ $specialist->name }}</h4>
                                    <p class="text-muted mb-0">{{ $specialist->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="services-list">
                            @foreach($services as $service)
                            <div class="service-card card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h3 class="service-name">{{ $service->name }}</h3>
                                            <p class="service-description mb-2">{{ $service->description }}</p>
                                            <div class="service-details d-flex">
                                                <div class="me-4"><i class="far fa-clock me-1"></i> {{ $service->duration }} دقيقة</div>
                                                <div><i class="fas fa-tag me-1"></i> {{ $service->category->name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                            <div class="service-price mb-3">
                                                <span class="price-amount">{{ $service->price }}</span>
                                                <span class="price-currency">ر.س</span>
                                            </div>
                                            <a href="{{ route('booking.process', ['step' => 3, 'specialist_id' => $specialist->id, 'service_id' => $service->id]) }}" class="btn btn-primary">اختيار</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="step-navigation mt-4">
                            <a href="{{ route('booking.process', ['step' => 1]) }}" class="btn btn-outline-primary"><i class="fas fa-arrow-right me-2"></i> الرجوع</a>
                        </div>
                    </div>
                @elseif($step == 3)
                    <!-- تحديد الموعد -->
                    <div class="step-content-inner">
                        <h2 class="section-title mb-4">اختر الموعد المناسب</h2>
                        <div class="selected-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ $specialist->profile_image }}" alt="{{ $specialist->name }}" class="rounded-circle" width="50" height="50">
                                        <div class="ms-3">
                                            <h5 class="mb-0">{{ $specialist->name }}</h5>
                                            <p class="text-muted mb-0">{{ $specialist->title }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="service-info p-3 bg-light rounded">
                                        <h5>{{ $service->name }}</h5>
                                        <div class="d-flex justify-content-between">
                                            <div><i class="far fa-clock me-1"></i> {{ $service->duration }} دقيقة</div>
                                            <div class="service-price">
                                                <span class="price-amount">{{ $service->price }}</span>
                                                <span class="price-currency">ر.س</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="date-time-picker">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="calendar-container mb-4">
                                        <h4 class="mb-3">اختر التاريخ</h4>
                                        <div id="booking-calendar" class="booking-calendar"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="time-slots-container">
                                        <h4 class="mb-3">اختر الوقت</h4>
                                        <div class="time-slots">
                                            <div class="alert alert-info" id="date-selection-message">
                                                الرجاء اختيار تاريخ من التقويم أولاً
                                            </div>
                                            <div id="time-slots-list" class="d-none">
                                                <!-- سيتم ملء هذا القسم بالأوقات المتاحة عبر JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="step-navigation mt-4 d-flex justify-content-between">
                            <a href="{{ route('booking.process', ['step' => 2, 'specialist_id' => $specialist->id]) }}" class="btn btn-outline-primary"><i class="fas fa-arrow-right me-2"></i> الرجوع</a>
                            <button id="continue-to-payment" class="btn btn-primary d-none">المتابعة للدفع <i class="fas fa-arrow-left ms-2"></i></button>
                        </div>
                    </div>
                @elseif($step == 4)
                    <!-- الدفع والتأكيد -->
                    <div class="step-content-inner">
                        <h2 class="section-title mb-4">الدفع وتأكيد الحجز</h2>
                        <div class="booking-summary mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">ملخص الحجز</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="summary-item mb-3">
                                                <div class="summary-label">المختص</div>
                                                <div class="summary-value">{{ $specialist->name }}</div>
                                            </div>
                                            <div class="summary-item mb-3">
                                                <div class="summary-label">الخدمة</div>
                                                <div class="summary-value">{{ $service->name }}</div>
                                            </div>
                                            <div class="summary-item mb-3">
                                                <div class="summary-label">المدة</div>
                                                <div class="summary-value">{{ $service->duration }} دقيقة</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="summary-item mb-3">
                                                <div class="summary-label">التاريخ</div>
                                                <div class="summary-value">{{ $booking_date }}</div>
                                            </div>
                                            <div class="summary-item mb-3">
                                                <div class="summary-label">الوقت</div>
                                                <div class="summary-value">{{ $booking_time }}</div>
                                            </div>
                                            <div class="summary-item mb-3">
                                                <div class="summary-label">السعر</div>
                                                <div class="summary-value price">{{ $service->price }} ر.س</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="payment-methods mb-4">
                            <h4 class="mb-3">اختر طريقة الدفع</h4>
                            <div class="payment-options">
                                <div class="form-check payment-option mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                    <label class="form-check-label d-flex align-items-center" for="credit_card">
                                        <span class="me-3">بطاقة ائتمان</span>
                                        <div class="payment-icons">
                                            <i class="fab fa-cc-visa mx-1"></i>
                                            <i class="fab fa-cc-mastercard mx-1"></i>
                                            <i class="fab fa-cc-amex mx-1"></i>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check payment-option mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="mada" value="mada">
                                    <label class="form-check-label d-flex align-items-center" for="mada">
                                        <span class="me-3">مدى</span>
                                        <div class="payment-icons">
                                            <img src="{{ asset('assets/images/mada-logo.png') }}" alt="مدى" height="25">
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check payment-option mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="apple_pay" value="apple_pay">
                                    <label class="form-check-label d-flex align-items-center" for="apple_pay">
                                        <span class="me-3">Apple Pay</span>
                                        <div class="payment-icons">
                                            <i class="fab fa-apple-pay mx-1"></i>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="credit-card-form mb-4" id="credit_card_form">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="card_number" class="form-label">رقم البطاقة</label>
                                        <input type="text" class="form-control" id="card_number" placeholder="0000 0000 0000 0000">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiry_date" class="form-label">تاريخ الانتهاء</label>
                                            <input type="text" class="form-control" id="expiry_date" placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">رمز الأمان (CVV)</label>
                                            <input type="text" class="form-control" id="cvv" placeholder="123">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="card_holder" class="form-label">اسم حامل البطاقة</label>
                                        <input type="text" class="form-control" id="card_holder" placeholder="الاسم كما يظهر على البطاقة">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="terms-agreement mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms_agreement">
                                <label class="form-check-label" for="terms_agreement">
                                    أوافق على <a href="{{ route('terms') }}" target="_blank">الشروط والأحكام</a> و<a href="{{ route('privacy') }}" target="_blank">سياسة الخصوصية</a>
                                </label>
                            </div>
                        </div>
                        <div class="step-navigation mt-4 d-flex justify-content-between">
                            <a href="{{ route('booking.process', ['step' => 3, 'specialist_id' => $specialist->id, 'service_id' => $service->id]) }}" class="btn btn-outline-primary"><i class="fas fa-arrow-right me-2"></i> الرجوع</a>
                            <button id="confirm-booking" class="btn btn-primary" disabled>تأكيد الحجز والدفع</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- قسم الأسئلة الشائعة -->
    <section class="booking-faq py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5">الأسئلة الشائعة حول الحجز</h2>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="bookingFaqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    كيف يمكنني إلغاء أو تعديل موعد الجلسة؟
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#bookingFaqAccordion">
                                <div class="accordion-body">
                                    يمكنك إلغاء أو تعديل موعد الجلسة من خلال لوحة التحكم الخاصة بك في حسابك الشخصي. يرجى العلم أنه يجب إلغاء الجلسة قبل 24 ساعة على الأقل من موعدها لاسترداد المبلغ كاملاً. أما في حالة الإلغاء قبل أقل من 24 ساعة، فسيتم خصم 50% من قيمة الجلسة كرسوم إلغاء.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    ما هي طرق الدفع المتاحة؟
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#bookingFaqAccordion">
                                <div class="accordion-body">
                                    نوفر عدة طرق للدفع تشمل بطاقات الائتمان (فيزا، ماستركارد، أمريكان إكسبريس)، بطاقات مدى، وApple Pay. جميع المعاملات المالية مشفرة وآمنة لضمان حماية بياناتك الشخصية.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    هل يمكنني تغيير المختص بعد الحجز؟
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#bookingFaqAccordion">
                                <div class="accordion-body">
                                    نعم، يمكنك تغيير المختص قبل موعد الجلسة بـ 48 ساعة على الأقل. يرجى التواصل مع خدمة العملاء لمساعدتك في هذا الأمر. قد يتم تطبيق فرق في السعر إذا كان هناك اختلاف في أسعار الخدمات بين المختصين.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    كيف تتم الجلسات؟ هل هي حضورية أم عن بعد؟
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#bookingFaqAccordion">
                                <div class="accordion-body">
                                    نوفر خيارين للجلسات: حضورية في مقر نفسجي، أو عن بعد عبر تطبيق الفيديو الخاص بنا. يمكنك اختيار الطريقة المناسبة لك أثناء عملية الحجز. الجلسات عن بعد توفر نفس جودة الجلسات الحضورية مع مرونة أكبر في المواعيد والمكان.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة التقويم
        const bookingCalendar = document.getElementById('booking-calendar');
        if (bookingCalendar) {
            const calendar = new FullCalendar.Calendar(bookingCalendar, {
                initialView: 'dayGridMonth',
                locale: 'ar',
                direction: 'rtl',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                selectable: true,
                selectConstraint: {
                    start: new Date(),
                },
                validRange: {
                    start: new Date()
                },
                select: function(info) {
                    const selectedDate = info.startStr;
                    loadTimeSlots(selectedDate);
                }
            });
            calendar.render();
        }

        // تحميل الأوقات المتاحة
        function loadTimeSlots(date) {
            // هنا سيتم استدعاء API لجلب الأوقات المتاحة
            // هذا مثال بسيط للتوضيح
            const timeSlotsList = document.getElementById('time-slots-list');
            const dateSelectionMessage = document.getElementById('date-selection-message');
            
            dateSelectionMessage.classList.add('d-none');
            timeSlotsList.classList.remove('d-none');
            timeSlotsList.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div></div>';
            
            // محاكاة طلب API
            setTimeout(() => {
                const availableSlots = [
                    { time: '09:00', available: true },
                    { time: '10:00', available: true },
                    { time: '11:00', available: false },
                    { time: '12:00', available: true },
                    { time: '13:00', available: false },
                    { time: '14:00', available: true },
                    { time: '15:00', available: true },
                    { time: '16:00', available: false },
                    { time: '17:00', available: true },
                    { time: '18:00', available: true }
                ];
                
                let slotsHtml = '';
                availableSlots.forEach(slot => {
                    const buttonClass = slot.available ? 'btn-outline-primary' : 'btn-outline-secondary disabled';
                    slotsHtml += `
                        <button class="btn ${buttonClass} time-slot-btn m-1" ${slot.available ? '' : 'disabled'} data-time="${slot.time}">
                            ${slot.time}
                        </button>
                    `;
                });
                
                timeSlotsList.innerHTML = slotsHtml;
                
                // إضافة مستمعي الأحداث لأزرار الأوقات
                document.querySelectorAll('.time-slot-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        document.querySelectorAll('.time-slot-btn').forEach(btn => {
                            btn.classList.remove('active');
                        });
                        this.classList.add('active');
                        
                        // تفعيل زر المتابعة
                        const continueButton = document.getElementById('continue-to-payment');
                        continueButton.classList.remove('d-none');
                        
                        // تخزين الوقت المحدد
                        const selectedTime = this.getAttribute('data-time');
                        continueButton.setAttribute('data-selected-time', selectedTime);
                        continueButton.setAttribute('data-selected-date', date);
                    });
                });
                
                // إضافة مستمع الحدث لزر المتابعة
                const continueButton = document.getElementById('continue-to-payment');
                if (continueButton) {
                    continueButton.addEventListener('click', function() {
                        const selectedDate = this.getAttribute('data-selected-date');
                        const selectedTime = this.getAttribute('data-selected-time');
                        
                        // توجيه المستخدم إلى صفحة الدفع مع البيانات المحددة
                        window.location.href = `{{ route('booking.process', ['step' => 4, 'specialist_id' => $specialist->id ?? 0, 'service_id' => $service->id ?? 0]) }}&date=${selectedDate}&time=${selectedTime}`;
                    });
                }
            }, 1000);
        }
        
        // التعامل مع نموذج الدفع
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
        const creditCardForm = document.getElementById('credit_card_form');
        const termsAgreement = document.getElementById('terms_agreement');
        const confirmButton = document.getElementById('confirm-booking');
        
        if (paymentMethodRadios.length > 0) {
            paymentMethodRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'credit_card') {
                        creditCardForm.style.display = 'block';
                    } else {
                        creditCardForm.style.display = 'none';
                    }
                });
            });
        }
        
        if (termsAgreement && confirmButton) {
            termsAgreement.addEventListener('change', function() {
                confirmButton.disabled = !this.checked;
            });
            
            confirmButton.addEventListener('click', function() {
                // هنا سيتم إرسال بيانات الدفع والحجز
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري معالجة الطلب...';
                this.disabled = true;
                
                // محاكاة عملية الدفع
                setTimeout(() => {
                    window.location.href = "{{ route('booking.confirmation') }}";
                }, 2000);
            });
        }
    });
</script>
@endsection

@section('styles')
<style>
    .booking-process-page {
        background-color: #f8f9fa;
    }
    
    .booking-progress {
        margin-top: 30px;
    }
    
    .step {
        text-align: center;
        width: 25%;
    }
    
    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
    }
    
    .step.active .step-icon {
        background-color: #0d6efd;
        color: white;
    }
    
    .step-text {
        font-size: 14px;
        color: #6c757d;
    }
    
    .step.active .step-text {
        color: #0d6efd;
        font-weight: bold;
    }
    
    .booking-step-content {
        margin-top: 40px;
        padding: 30px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .specialist-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        text-align: center;
    }
    
    .specialist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .specialist-img img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    
    .service-card {
        transition: transform 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .service-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .service-price {
        font-size: 1.25rem;
        font-weight: bold;
        color: #0d6efd;
    }
    
    .booking-calendar {
        height: 400px;
    }
    
    .time-slot-btn {
        width: 80px;
    }
    
    .time-slot-btn.active {
        background-color: #0d6efd;
        color: white;
    }
    
    .summary-item {
        margin-bottom: 15px;
    }
    
    .summary-label {
        font-weight: bold;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .summary-value {
        font-size: 1.1rem;
    }
    
    .summary-value.price {
        color: #0d6efd;
        font-weight: bold;
    }
    
    .payment-option {
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .payment-option:hover {
        border-color: #0d6efd;
    }
    
    .payment-icons {
        font-size: 1.5rem;
    }
    
    @media (max-width: 767.98px) {
        .step-text {
            font-size: 12px;
        }
        
        .booking-step-content {
            padding: 20px 15px;
        }
    }
</style>
@endsection
