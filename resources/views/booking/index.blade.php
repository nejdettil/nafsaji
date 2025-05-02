@extends('layouts.app')

@section('title', 'حجز جلسة - نفسجي')

@section('content')
<div class="booking-page">
    <!-- قسم العنوان الرئيسي -->
    <section class="page-header bg-gradient-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">حجز جلسة</h1>
                    <p class="lead mb-4">اختر المختص والخدمة المناسبة واحجز جلستك بكل سهولة</p>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('assets/images/booking-hero.svg') }}" alt="حجز جلسة" class="img-fluid" style="max-height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- قسم خطوات الحجز -->
    <section class="booking-steps-section py-5">
        <div class="container">
            <div class="booking-steps-container">
                <div class="booking-steps">
                    <div class="booking-step {{ $currentStep >= 1 ? 'active' : '' }}" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-title">اختيار المختص</div>
                    </div>
                    <div class="booking-step {{ $currentStep >= 2 ? 'active' : '' }}" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-title">اختيار الخدمة</div>
                    </div>
                    <div class="booking-step {{ $currentStep >= 3 ? 'active' : '' }}" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-title">تحديد الموعد</div>
                    </div>
                    <div class="booking-step {{ $currentStep >= 4 ? 'active' : '' }}" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-title">تأكيد الحجز</div>
                    </div>
                </div>
            </div>

            <!-- محتوى خطوات الحجز -->
            <div class="booking-steps-content mt-5">
                <!-- الخطوة 1: اختيار المختص -->
                @if($currentStep == 1)
                <div class="step-content" id="step-1">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent">
                            <h3 class="card-title">اختر المختص المناسب لك</h3>
                        </div>
                        <div class="card-body">
                            <!-- فلتر البحث -->
                            <div class="specialists-filter mb-4">
                                <form action="{{ route('booking.step1') }}" method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="search" class="form-label">البحث</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" id="search" name="search" placeholder="ابحث باسم المختص أو التخصص" value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="specialization" class="form-label">التخصص</label>
                                        <select class="form-select" id="specialization" name="specialization">
                                            <option value="">جميع التخصصات</option>
                                            @foreach($specializations as $specialization)
                                            <option value="{{ $specialization }}" {{ request('specialization') == $specialization ? 'selected' : '' }}>{{ $specialization }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="rating" class="form-label">التقييم</label>
                                        <select class="form-select" id="rating" name="rating">
                                            <option value="">جميع التقييمات</option>
                                            <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 نجوم</option>
                                            <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4 نجوم وأعلى</option>
                                            <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3 نجوم وأعلى</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">بحث</button>
                                    </div>
                                </form>
                            </div>

                            <!-- قائمة المختصين -->
                            <div class="specialists-list">
                                <div class="row">
                                    @if(count($specialists) > 0)
                                        @foreach($specialists as $specialist)
                                        <div class="col-md-6 mb-4">
                                            <div class="specialist-card">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="specialist-avatar me-3">
                                                                <img src="{{ $specialist->avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->name }}" class="rounded-circle" width="80" height="80">
                                                                <div class="specialist-availability {{ $specialist->is_available ? 'available' : 'unavailable' }}">
                                                                    <i class="fas fa-{{ $specialist->is_available ? 'check' : 'times' }}"></i>
                                                                </div>
                                                            </div>
                                                            <div class="specialist-info flex-grow-1">
                                                                <h4 class="specialist-name">{{ $specialist->name }}</h4>
                                                                <p class="specialist-title text-muted mb-1">{{ $specialist->title }}</p>
                                                                <p class="specialist-specialization mb-2">{{ $specialist->specialization }}</p>
                                                                
                                                                <div class="specialist-rating mb-2">
                                                                    <div class="rating-stars">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            @if($i <= $specialist->average_rating)
                                                                                <i class="fas fa-star text-warning"></i>
                                                                            @elseif($i - 0.5 <= $specialist->average_rating)
                                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                                            @else
                                                                                <i class="far fa-star text-warning"></i>
                                                                            @endif
                                                                        @endfor
                                                                    </div>
                                                                    <span class="rating-value">{{ number_format($specialist->average_rating, 1) }}</span>
                                                                    <span class="rating-count">({{ $specialist->reviews_count }} تقييم)</span>
                                                                </div>
                                                                
                                                                <div class="specialist-experience d-flex mb-2">
                                                                    <div class="experience-item me-3">
                                                                        <i class="fas fa-calendar-check text-muted me-1"></i>
                                                                        <span>{{ $specialist->experience_years }} سنوات خبرة</span>
                                                                    </div>
                                                                    <div class="experience-item">
                                                                        <i class="fas fa-users text-muted me-1"></i>
                                                                        <span>{{ $specialist->sessions_count }} جلسة</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="specialist-services mt-3">
                                                            <h6 class="text-muted mb-2">الخدمات</h6>
                                                            <div class="services-tags">
                                                                @foreach($specialist->services->take(3) as $service)
                                                                <span class="service-tag">{{ $service->name }}</span>
                                                                @endforeach
                                                                @if($specialist->services->count() > 3)
                                                                <span class="service-tag more-tag">+{{ $specialist->services->count() - 3 }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                                                        <a href="{{ route('specialists.show', $specialist->id) }}" class="btn btn-link" target="_blank">عرض الملف الشخصي</a>
                                                        <form action="{{ route('booking.selectSpecialist') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="specialist_id" value="{{ $specialist->id }}">
                                                            <button type="submit" class="btn btn-primary">اختيار</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="col-12 text-center py-5">
                                            <img src="{{ asset('assets/images/no-specialists.svg') }}" alt="لا يوجد مختصين" class="img-fluid mb-3" style="max-height: 150px;">
                                            <h5>لم يتم العثور على مختصين</h5>
                                            <p class="text-muted">يرجى تعديل معايير البحث أو المحاولة لاحقاً</p>
                                            <a href="{{ route('booking.step1') }}" class="btn btn-primary mt-3">عرض جميع المختصين</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- ترقيم الصفحات -->
                            @if($specialists->hasPages())
                            <div class="pagination-container mt-4">
                                {{ $specialists->appends(request()->query())->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- الخطوة 2: اختيار الخدمة -->
                @if($currentStep == 2)
                <div class="step-content" id="step-2">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent">
                            <h3 class="card-title">اختر الخدمة المناسبة</h3>
                        </div>
                        <div class="card-body">
                            <!-- معلومات المختص المختار -->
                            <div class="selected-specialist mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $selectedSpecialist->avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $selectedSpecialist->name }}" class="rounded-circle me-3" width="60" height="60">
                                            <div>
                                                <h4 class="mb-1">{{ $selectedSpecialist->name }}</h4>
                                                <p class="text-muted mb-0">{{ $selectedSpecialist->specialization }}</p>
                                            </div>
                                            <div class="ms-auto">
                                                <a href="{{ route('booking.step1') }}" class="btn btn-outline-primary">تغيير المختص</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- قائمة الخدمات -->
                            <div class="services-list">
                                <div class="row">
                                    @foreach($services as $service)
                                    <div class="col-md-6 mb-4">
                                        <div class="service-card">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="service-icon mb-3">
                                                        <i class="{{ $service->icon ?? 'fas fa-brain' }}"></i>
                                                    </div>
                                                    <h4 class="service-name">{{ $service->name }}</h4>
                                                    <p class="service-description">{{ $service->description }}</p>
                                                    <div class="service-meta d-flex justify-content-between align-items-center">
                                                        <div class="service-price">
                                                            <span class="amount">{{ number_format($service->price, 0) }}</span>
                                                            <span class="currency">ر.س</span>
                                                        </div>
                                                        <div class="service-duration">
                                                            <i class="far fa-clock me-1"></i>
                                                            <span>{{ $service->duration }} دقيقة</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent text-center">
                                                    <form action="{{ route('booking.selectService') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                                                        <button type="submit" class="btn btn-primary w-100">اختيار</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- الباقات -->
                            @if(count($packages) > 0)
                            <div class="packages-section mt-4">
                                <h4 class="section-title mb-3">الباقات المتاحة</h4>
                                <div class="row">
                                    @foreach($packages as $package)
                                    <div class="col-md-6 mb-4">
                                        <div class="package-card">
                                            <div class="card h-100 {{ $package->is_featured ? 'featured' : '' }}">
                                                @if($package->is_featured)
                                                <div class="package-badge">الأكثر طلباً</div>
                                                @endif
                                                <div class="card-body">
                                                    <h4 class="package-name">{{ $package->name }}</h4>
                                                    <p class="package-description">{{ $package->description }}</p>
                                                    <div class="package-features mb-3">
                                                        <ul class="list-unstyled">
                                                            @foreach(explode("\n", $package->features) as $feature)
                                                            <li><i class="fas fa-check-circle text-success me-2"></i> {{ $feature }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div class="package-meta d-flex justify-content-between align-items-center">
                                                        <div class="package-price">
                                                            <span class="amount">{{ number_format($package->price, 0) }}</span>
                                                            <span class="currency">ر.س</span>
                                                            @if($package->original_price > $package->price)
                                                            <span class="original-price">{{ number_format($package->original_price, 0) }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="package-sessions">
                                                            <i class="fas fa-calendar-check me-1"></i>
                                                            <span>{{ $package->sessions_count }} جلسات</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent text-center">
                                                    <form action="{{ route('booking.selectPackage') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                                                        <button type="submit" class="btn btn-primary w-100">اختيار</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- الخطوة 3: تحديد الموعد -->
                @if($currentStep == 3)
                <div class="step-content" id="step-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent">
                            <h3 class="card-title">اختر الموعد المناسب</h3>
                        </div>
                        <div class="card-body">
                            <!-- معلومات المختص والخدمة المختارة -->
                            <div class="selected-info mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 d-flex align-items-center">
                                                <img src="{{ $selectedSpecialist->avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $selectedSpecialist->name }}" class="rounded-circle me-3" width="60" height="60">
                                                <div>
                                                    <h4 class="mb-1">{{ $selectedSpecialist->name }}</h4>
                                                    <p class="text-muted mb-0">{{ $selectedSpecialist->specialization }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
                                                <div class="text-md-end">
                                                    <h5 class="mb-1">{{ $selectedService->name }}</h5>
                                                    <div class="d-flex align-items-center justify-content-md-end">
                                                        <span class="me-3">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ $selectedService->duration }} دقيقة
                                                        </span>
                                                        <span class="service-price">
                                                            {{ number_format($selectedService->price, 0) }} ر.س
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent d-flex justify-content-between">
                                        <a href="{{ route('booking.step1') }}" class="btn btn-outline-primary">تغيير المختص</a>
                                        <a href="{{ route('booking.step2') }}" class="btn btn-outline-primary">تغيير الخدمة</a>
                                    </div>
                                </div>
                            </div>

                            <!-- تقويم المواعيد -->
                            <div class="appointment-calendar mb-4">
                                <div class="row">
                                    <div class="col-md-7 mb-4 mb-md-0">
                                        <div class="calendar-container">
                                            <div id="appointmentCalendar"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="time-slots-container">
                                            <h5 class="mb-3">المواعيد المتاحة <span id="selectedDateDisplay" class="text-primary"></span></h5>
                                            <div id="timeSlots" class="time-slots">
                                                <div class="text-center py-5">
                                                    <img src="{{ asset('assets/images/calendar-select.svg') }}" alt="اختر تاريخ" class="img-fluid mb-3" style="max-height: 100px;">
                                                    <p class="text-muted">يرجى اختيار تاريخ من التقويم لعرض المواعيد المتاحة</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- نوع الجلسة -->
                            <div class="session-type mb-4">
                                <h5 class="mb-3">نوع الجلسة</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check session-type-card">
                                            <input class="form-check-input" type="radio" name="session_type" id="online" value="online" checked>
                                            <label class="form-check-label" for="online">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="session-type-icon me-3">
                                                                <i class="fas fa-video"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1">جلسة عبر الإنترنت</h5>
                                                                <p class="text-muted mb-0">جلسة فيديو مباشرة مع المختص</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check session-type-card">
                                            <input class="form-check-input" type="radio" name="session_type" id="in_person" value="in_person">
                                            <label class="form-check-label" for="in_person">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="session-type-icon me-3">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1">جلسة حضورية</h5>
                                                                <p class="text-muted mb-0">جلسة وجهاً لوجه في عيادة المختص</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ملاحظات إضافية -->
                            <div class="additional-notes mb-4">
                                <h5 class="mb-3">ملاحظات إضافية (اختياري)</h5>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أضف أي ملاحظات أو معلومات إضافية ترغب في مشاركتها مع المختص"></textarea>
                            </div>

                            <!-- زر التأكيد -->
                            <div class="text-center">
                                <form action="{{ route('booking.selectDateTime') }}" method="POST" id="dateTimeForm">
                                    @csrf
                                    <input type="hidden" name="appointment_date" id="appointmentDate">
                                    <input type="hidden" name="appointment_time" id="appointmentTime">
                                    <input type="hidden" name="session_type" id="sessionType" value="online">
                                    <input type="hidden" name="notes" id="notesInput">
                                    <button type="submit" class="btn btn-primary btn-lg" id="confirmDateTimeBtn" disabled>تأكيد الموعد</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- الخطوة 4: تأكيد الحجز -->
                @if($currentStep == 4)
                <div class="step-content" id="step-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent">
                            <h3 class="card-title">تأكيد الحجز والدفع</h3>
                        </div>
                        <div class="card-body">
                            <!-- ملخص الحجز -->
                            <div class="booking-summary mb-4">
                                <h5 class="mb-3">ملخص الحجز</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex mb-3">
                                                    <img src="{{ $selectedSpecialist->avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $selectedSpecialist->name }}" class="rounded-circle me-3" width="60" height="60">
                                                    <div>
                                                        <h5 class="mb-1">{{ $selectedSpecialist->name }}</h5>
                                                        <p class="text-muted mb-0">{{ $selectedSpecialist->specialization }}</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="booking-detail mb-2">
                                                    <span class="detail-label">الخدمة:</span>
                                                    <span class="detail-value">{{ $selectedService->name }}</span>
                                                </div>
                                                
                                                <div class="booking-detail mb-2">
                                                    <span class="detail-label">المدة:</span>
                                                    <span class="detail-value">{{ $selectedService->duration }} دقيقة</span>
                                                </div>
                                                
                                                <div class="booking-detail mb-2">
                                                    <span class="detail-label">نوع الجلسة:</span>
                                                    <span class="detail-value">{{ $sessionType == 'online' ? 'عبر الإنترنت' : 'حضورية' }}</span>
                                                </div>
                                                
                                                @if(!empty($notes))
                                                <div class="booking-detail mb-2">
                                                    <span class="detail-label">ملاحظات:</span>
                                                    <span class="detail-value">{{ $notes }}</span>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="appointment-date-time mb-3 text-md-end">
                                                    <div class="appointment-date">
                                                        <i class="far fa-calendar-alt me-2"></i>
                                                        <span>{{ $appointmentDateFormatted }}</span>
                                                    </div>
                                                    <div class="appointment-time">
                                                        <i class="far fa-clock me-2"></i>
                                                        <span>{{ $appointmentTimeFormatted }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="price-details">
                                                    <div class="price-item d-flex justify-content-between mb-2">
                                                        <span>سعر الخدمة</span>
                                                        <span>{{ number_format($selectedService->price, 0) }} ر.س</span>
                                                    </div>
                                                    
                                                    @if($discount > 0)
                                                    <div class="price-item d-flex justify-content-between mb-2 text-success">
                                                        <span>خصم</span>
                                                        <span>- {{ number_format($discount, 0) }} ر.س</span>
                                                    </div>
                                                    @endif
                                                    
                                                    @if($tax > 0)
                                                    <div class="price-item d-flex justify-content-between mb-2">
                                                        <span>ضريبة القيمة المضافة (15%)</span>
                                                        <span>{{ number_format($tax, 0) }} ر.س</span>
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="price-total d-flex justify-content-between mt-3 pt-3 border-top">
                                                        <span class="fw-bold">الإجمالي</span>
                                                        <span class="fw-bold fs-5">{{ number_format($totalAmount, 0) }} ر.س</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent d-flex justify-content-between">
                                        <a href="{{ route('booking.step1') }}" class="btn btn-outline-primary">تغيير المختص</a>
                                        <a href="{{ route('booking.step2') }}" class="btn btn-outline-primary">تغيير الخدمة</a>
                                        <a href="{{ route('booking.step3') }}" class="btn btn-outline-primary">تغيير الموعد</a>
                                    </div>
                                </div>
                            </div>

                            <!-- طرق الدفع -->
                            <div class="payment-methods mb-4">
                                <h5 class="mb-3">اختر طريقة الدفع</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check payment-method-card">
                                            <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                            <label class="form-check-label" for="credit_card">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="payment-method-icon me-3">
                                                                <i class="fas fa-credit-card"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1">بطاقة ائتمانية</h5>
                                                                <p class="text-muted mb-0">Visa, Mastercard, Mada</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check payment-method-card">
                                            <input class="form-check-input" type="radio" name="payment_method" id="apple_pay" value="apple_pay">
                                            <label class="form-check-label" for="apple_pay">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="payment-method-icon me-3">
                                                                <i class="fab fa-apple-pay"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1">Apple Pay</h5>
                                                                <p class="text-muted mb-0">الدفع السريع</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check payment-method-card">
                                            <input class="form-check-input" type="radio" name="payment_method" id="stc_pay" value="stc_pay">
                                            <label class="form-check-label" for="stc_pay">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="payment-method-icon me-3">
                                                                <i class="fas fa-mobile-alt"></i>
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1">STC Pay</h5>
                                                                <p class="text-muted mb-0">الدفع الإلكتروني</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- معلومات بطاقة الائتمان -->
                            <div class="credit-card-form mb-4" id="creditCardForm">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="card_number" class="form-label">رقم البطاقة</label>
                                                <input type="text" class="form-control" id="card_number" placeholder="0000 0000 0000 0000">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="card_name" class="form-label">الاسم على البطاقة</label>
                                                <input type="text" class="form-control" id="card_name" placeholder="الاسم كما يظهر على البطاقة">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="expiry_date" class="form-label">تاريخ الانتهاء</label>
                                                <input type="text" class="form-control" id="expiry_date" placeholder="MM/YY">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="cvv" class="form-label">رمز الأمان (CVV)</label>
                                                <input type="text" class="form-control" id="cvv" placeholder="123">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- الشروط والأحكام -->
                            <div class="terms-conditions mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms_agree">
                                    <label class="form-check-label" for="terms_agree">
                                        أوافق على <a href="{{ route('terms') }}" target="_blank">الشروط والأحكام</a> و<a href="{{ route('privacy') }}" target="_blank">سياسة الخصوصية</a>
                                    </label>
                                </div>
                            </div>

                            <!-- زر تأكيد الحجز -->
                            <div class="text-center">
                                <form action="{{ route('booking.confirm') }}" method="POST" id="confirmBookingForm">
                                    @csrf
                                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="credit_card">
                                    <button type="submit" class="btn btn-primary btn-lg" id="confirmBookingBtn" disabled>تأكيد الحجز والدفع</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<style>
    .page-header {
        padding: 80px 0;
        background: linear-gradient(135deg, #6a4c93 0%, #9163cb 100%);
        border-radius: 0 0 50px 50px;
        margin-bottom: 50px;
    }
    .booking-steps-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .booking-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .booking-steps::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }
    .booking-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    .booking-step.active .step-number {
        background-color: #6a4c93;
        color: white;
    }
    .step-title {
        font-size: 0.9rem;
        color: #6c757d;
        text-align: center;
        transition: all 0.3s ease;
    }
    .booking-step.active .step-title {
        color: #6a4c93;
        font-weight: bold;
    }
    .specialist-card {
        transition: all 0.3s ease;
    }
    .specialist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .specialist-avatar {
        position: relative;
    }
    .specialist-availability {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: white;
    }
    .specialist-availability.available {
        background-color: #28a745;
    }
    .specialist-availability.unavailable {
        background-color: #6c757d;
    }
    .specialist-name {
        font-weight: bold;
        color: #343a40;
        margin-bottom: 5px;
    }
    .specialist-rating {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .services-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .service-tag {
        background-color: rgba(106, 76, 147, 0.1);
        color: #6a4c93;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    .service-tag.more-tag {
        background-color: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }
    .service-card {
        transition: all 0.3s ease;
    }
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .service-icon {
        width: 60px;
        height: 60px;
        background-color: rgba(106, 76, 147, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #6a4c93;
    }
    .service-name {
        font-weight: bold;
        color: #343a40;
        margin-bottom: 10px;
    }
    .service-price .amount {
        font-weight: bold;
        color: #6a4c93;
        font-size: 1.2rem;
    }
    .package-card {
        transition: all 0.3s ease;
    }
    .package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .package-card .card.featured {
        border: 2px solid #6a4c93;
    }
    .package-badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: #6a4c93;
        color: white;
        padding: 5px 15px;
        border-radius: 0 15px 0 15px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .package-name {
        font-weight: bold;
        color: #343a40;
        margin-bottom: 10px;
    }
    .package-price .amount {
        font-weight: bold;
        color: #6a4c93;
        font-size: 1.2rem;
    }
    .package-price .original-price {
        font-size: 1rem;
        color: #dc3545;
        text-decoration: line-through;
        margin-left: 10px;
    }
    .calendar-container {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    .time-slots-container {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
    .time-slots {
        max-height: 300px;
        overflow-y: auto;
    }
    .time-slot {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    .time-slot:hover {
        background-color: rgba(106, 76, 147, 0.05);
    }
    .time-slot.selected {
        background-color: rgba(106, 76, 147, 0.1);
        border-color: #6a4c93;
    }
    .time-slot.unavailable {
        background-color: #f8f9fa;
        color: #adb5bd;
        cursor: not-allowed;
    }
    .session-type-card input[type="radio"],
    .payment-method-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    .session-type-card label,
    .payment-method-card label {
        width: 100%;
        margin-bottom: 0;
        cursor: pointer;
    }
    .session-type-card input[type="radio"]:checked + label .card,
    .payment-method-card input[type="radio"]:checked + label .card {
        border-color: #6a4c93;
        box-shadow: 0 0 0 1px #6a4c93;
    }
    .session-type-icon,
    .payment-method-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(106, 76, 147, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #6a4c93;
    }
    .booking-detail {
        display: flex;
        align-items: flex-start;
    }
    .detail-label {
        min-width: 100px;
        color: #6c757d;
    }
    .detail-value {
        font-weight: 500;
    }
    .appointment-date-time {
        font-size: 1.1rem;
        font-weight: 500;
    }
    .price-total {
        color: #6a4c93;
    }
    .fc-event {
        cursor: pointer;
    }
    .fc-day-today {
        background-color: rgba(106, 76, 147, 0.1) !important;
    }
    .fc-button-primary {
        background-color: #6a4c93 !important;
        border-color: #6a4c93 !important;
    }
    .fc-button-primary:hover {
        background-color: #5a3d83 !important;
        border-color: #5a3d83 !important;
    }
    .fc-button-active {
        background-color: #5a3d83 !important;
        border-color: #5a3d83 !important;
    }
    .pagination-container {
        display: flex;
        justify-content: center;
    }
    .pagination {
        --bs-pagination-color: #6a4c93;
        --bs-pagination-hover-color: #6a4c93;
        --bs-pagination-focus-color: #6a4c93;
        --bs-pagination-active-bg: #6a4c93;
        --bs-pagination-active-border-color: #6a4c93;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ar.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة التقويم (في الخطوة 3)
        @if($currentStep == 3)
        const calendarEl = document.getElementById('appointmentCalendar');
        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'ar',
                direction: 'rtl',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                buttonText: {
                    today: 'اليوم'
                },
                themeSystem: 'bootstrap',
                selectable: true,
                selectConstraint: {
                    start: new Date().setHours(0, 0, 0, 0),
                    end: new Date(new Date().setMonth(new Date().getMonth() + 3))
                },
                selectAllow: function(selectInfo) {
                    return selectInfo.start >= new Date().setHours(0, 0, 0, 0);
                },
                events: {!! json_encode($availableDates) !!},
                dateClick: function(info) {
                    // التحقق من أن التاريخ المحدد ليس في الماضي
                    const selectedDate = new Date(info.dateStr);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        return;
                    }
                    
                    // تحديث التاريخ المحدد
                    document.getElementById('appointmentDate').value = info.dateStr;
                    document.getElementById('selectedDateDisplay').textContent = new Date(info.dateStr).toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                    
                    // تحميل المواعيد المتاحة للتاريخ المحدد
                    loadTimeSlots(info.dateStr);
                    
                    // تحديث حالة التقويم
                    const allDates = document.querySelectorAll('.fc-daygrid-day');
                    allDates.forEach(date => {
                        date.classList.remove('selected-date');
                    });
                    info.dayEl.classList.add('selected-date');
                }
            });
            calendar.render();
            
            // دالة تحميل المواعيد المتاحة
            function loadTimeSlots(date) {
                const timeSlotsContainer = document.getElementById('timeSlots');
                timeSlotsContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div><p class="mt-2">جاري تحميل المواعيد المتاحة...</p></div>';
                
                // محاكاة طلب AJAX لجلب المواعيد المتاحة
                setTimeout(function() {
                    // هذه بيانات تجريبية، في التطبيق الفعلي ستأتي من الخادم
                    const availableTimeSlots = {!! json_encode($timeSlots) !!};
                    const selectedDateTimeSlots = availableTimeSlots[date] || [];
                    
                    if (selectedDateTimeSlots.length > 0) {
                        let html = '';
                        selectedDateTimeSlots.forEach(slot => {
                            const isAvailable = slot.available;
                            html += `
                                <div class="time-slot ${isAvailable ? '' : 'unavailable'}" data-time="${slot.time}" ${isAvailable ? 'onclick="selectTimeSlot(this)"' : ''}>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>${slot.time_formatted}</span>
                                        <span class="badge ${isAvailable ? 'bg-success' : 'bg-secondary'}">${isAvailable ? 'متاح' : 'محجوز'}</span>
                                    </div>
                                </div>
                            `;
                        });
                        timeSlotsContainer.innerHTML = html;
                    } else {
                        timeSlotsContainer.innerHTML = `
                            <div class="text-center py-4">
                                <img src="{{ asset('assets/images/no-slots.svg') }}" alt="لا توجد مواعيد متاحة" class="img-fluid mb-3" style="max-height: 100px;">
                                <p class="text-muted">لا توجد مواعيد متاحة في هذا اليوم</p>
                                <p class="small">يرجى اختيار يوم آخر أو التواصل مع المختص</p>
                            </div>
                        `;
                    }
                }, 1000);
            }
            
            // تحديد نوع الجلسة
            const sessionTypeRadios = document.querySelectorAll('input[name="session_type"]');
            sessionTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('sessionType').value = this.value;
                });
            });
            
            // تحديث الملاحظات
            const notesTextarea = document.getElementById('notes');
            notesTextarea.addEventListener('input', function() {
                document.getElementById('notesInput').value = this.value;
            });
        }
        
        // دالة تحديد وقت الموعد
        window.selectTimeSlot = function(element) {
            const timeSlots = document.querySelectorAll('.time-slot');
            timeSlots.forEach(slot => {
                slot.classList.remove('selected');
            });
            element.classList.add('selected');
            
            document.getElementById('appointmentTime').value = element.getAttribute('data-time');
            document.getElementById('confirmDateTimeBtn').disabled = false;
        };
        @endif
        
        // تهيئة نموذج تأكيد الحجز (في الخطوة 4)
        @if($currentStep == 4)
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
        const creditCardForm = document.getElementById('creditCardForm');
        const termsCheckbox = document.getElementById('terms_agree');
        const confirmBookingBtn = document.getElementById('confirmBookingBtn');
        const paymentMethodInput = document.getElementById('paymentMethodInput');
        
        // تغيير طريقة الدفع
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                paymentMethodInput.value = this.value;
                
                if (this.value === 'credit_card') {
                    creditCardForm.style.display = 'block';
                } else {
                    creditCardForm.style.display = 'none';
                }
            });
        });
        
        // تفعيل زر التأكيد عند الموافقة على الشروط
        termsCheckbox.addEventListener('change', function() {
            confirmBookingBtn.disabled = !this.checked;
        });
        
        // تقديم نموذج تأكيد الحجز
        document.getElementById('confirmBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // هنا يمكن إضافة التحقق من صحة بيانات البطاقة إذا كانت طريقة الدفع هي بطاقة ائتمانية
            
            // إرسال النموذج
            this.submit();
        });
        @endif
    });
</script>
@endsection
