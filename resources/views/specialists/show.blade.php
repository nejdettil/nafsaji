@extends('layouts.app')

@section('title', '{{ $specialist->user->name }} - نفسجي')

@section('content')
    <div class="specialist-profile-page">
        <!-- قسم العنوان الرئيسي -->
        <section class="page-header bg-gradient-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-3">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('specialists') }}" class="text-white">المختصون</a></li>
                                <li class="breadcrumb-item active text-white-50" aria-current="page">{{ $specialist->user->name }}</li>
                            </ol>
                        </nav>
                        <h1 class="display-4 fw-bold mb-3">{{ $specialist->user->name }}</h1>
                        <p class="specialist-title lead mb-3">{{ $specialist->title }}</p>
                        <div class="specialist-meta d-flex flex-wrap mb-4">
                            <div class="me-4 mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span>{{ $specialist->location }}</span>
                            </div>
                            <div class="me-4 mb-2">
                                <i class="fas fa-calendar-check me-2"></i>
                                <span>{{ $specialist->experience_years }} سنوات خبرة</span>
                            </div>
                            <div class="me-4 mb-2">
                                <i class="fas fa-users me-2"></i>
                                <span>{{ $specialist->sessions_count }} جلسة</span>
                            </div>
                        </div>
                        <div class="specialist-rating d-flex align-items-center mb-4">
                            <div class="rating-stars me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $specialist->average_rating ? 'text-warning' : 'text-white-50' }}"></i>
                                @endfor
                            </div>
                            <div class="rating-value">
                                <span class="fw-bold">{{ number_format($specialist->average_rating, 1) }}</span>
                                <span class="text-white-50">({{ $specialist->reviews_count }} تقييم)</span>
                            </div>
                        </div>
                        <a href="{{ route('specialists.book', $specialist->id) }}" class="btn btn-light btn-lg me-2">حجز جلسة</a>
                        <a href="#reviews" class="btn btn-outline-light btn-lg">التقييمات</a>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="specialist-avatar">
                            <img src="{{ $specialist->profile_image ? asset('storage/' . $specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->user->name }}" class="img-fluid rounded-circle specialist-image">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم تفاصيل المختص -->
        <section class="specialist-details py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- نبذة عن المختص -->
                        <div class="profile-section mb-5">
                            <h2 class="section-title mb-4">نبذة عن المختص</h2>
                            <div class="profile-about">
                                <p>{{ $specialist->bio }}</p>
                            </div>
                        </div>

                        <!-- التخصصات -->
                        <div class="profile-section mb-5">
                            <h2 class="section-title mb-4">التخصصات</h2>
                            <div class="specializations-list">
                                <div class="row">
                                    @if(isset($specialist->specializations) && is_iterable($specialist->specializations))
                                        @foreach($specialist->specializations as $specialization)
                                            <div class="col-md-6 mb-3">
                                                <div class="specialization-item">
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                    <span>{{ $specialization->name }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                لا توجد تخصصات متاحة لهذا المختص حالياً.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- الشهادات والمؤهلات -->
                        <div class="profile-section mb-5">
                            <h2 class="section-title mb-4">الشهادات والمؤهلات</h2>
                            <div class="certifications-list">
                                <div class="timeline">
                                    @if(isset($specialist->certifications) && is_iterable($specialist->certifications))
                                        @foreach($specialist->certifications as $certification)
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h4 class="timeline-title">{{ $certification->title }}</h4>
                                                    <p class="timeline-info">{{ $certification->institution }}</p>
                                                    <p class="timeline-date">{{ $certification->year }}</p>
                                                    @if($certification->description)
                                                        <p class="timeline-description">{{ $certification->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            لا توجد شهادات أو مؤهلات متاحة لهذا المختص حالياً.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- الخبرات العملية -->
                        <div class="profile-section mb-5">
                            <h2 class="section-title mb-4">الخبرات العملية</h2>
                            <div class="experiences-list">
                                <div class="timeline">
                                    @if(isset($specialist->experiences) && is_iterable($specialist->experiences))
                                        @foreach($specialist->experiences as $experience)
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h4 class="timeline-title">{{ $experience->title }}</h4>
                                                    <p class="timeline-info">{{ $experience->company }}</p>
                                                    <p class="timeline-date">{{ $experience->from_year }} - {{ $experience->to_year ?? 'حتى الآن' }}</p>
                                                    @if($experience->description)
                                                        <p class="timeline-description">{{ $experience->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            لا توجد خبرات عملية متاحة لهذا المختص حالياً.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- الشهادات والمؤهلات -->
                        <div class="profile-section mb-5">
                            <h2 class="section-title mb-4">الشهادات والمؤهلات</h2>
                            <div class="certifications-list">
                                <div class="timeline">
                                    @if(isset($specialist->certifications) && is_iterable($specialist->certifications))
                                        @foreach($specialist->certifications as $certification)
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h4 class="timeline-title">{{ $certification->title }}</h4>
                                                    <p class="timeline-info">{{ $certification->institution }}</p>
                                                    <p class="timeline-date">{{ $certification->year }}</p>
                                                    @if($certification->description)
                                                        <p class="timeline-description">{{ $certification->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            لا توجد شهادات أو مؤهلات متاحة لهذا المختص حالياً.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- التقييمات -->
                        <div class="profile-section mb-5" id="reviews">
                            <h2 class="section-title mb-4">التقييمات</h2>
                            <div class="reviews-list">
                                @if(isset($specialist->reviews) && is_iterable($specialist->reviews))
                                    @forelse($specialist->reviews as $review)
                                        <div class="review-item mb-4">
                                            <div class="d-flex">
                                                <div class="review-avatar me-3">
                                                    <img src="{{ $review->user->profile_image ? asset('storage/' . $review->user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $review->user->name }}" class="rounded-circle" width="50" height="50">
                                                </div>
                                                <div class="review-content">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h5 class="mb-0">{{ $review->user->name }}</h5>
                                                        <div class="review-date text-muted small">{{ $review->created_at->format('Y-m-d') }}</div>
                                                    </div>
                                                    <div class="rating-stars mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="review-text">{{ $review->comment }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="far fa-comment-dots fa-3x text-muted"></i>
                                            </div>
                                            <p class="text-muted">لا توجد تقييمات لهذا المختص حتى الآن</p>
                                        </div>
                                    @endforelse

                                    @if($specialist->reviews_count > 3)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('specialists.reviews', $specialist->id) }}" class="btn btn-outline-primary">عرض جميع التقييمات</a>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="far fa-comment-dots fa-3x text-muted"></i>
                                        </div>
                                        <p class="text-muted">لا توجد تقييمات لهذا المختص حتى الآن</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- بطاقة الحجز -->
                        <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 100px; z-index: 999;">
                            <div class="card-body p-4">
                                <h3 class="card-title mb-4">احجز جلسة مع {{ $specialist->user->name }}</h3>

                                <div class="specialist-info mb-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>سعر الجلسة:</span>
                                        <span class="fw-bold text-primary">{{ $specialist->session_price }} ر.س</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>مدة الجلسة:</span>
                                        <span class="fw-bold">{{ $specialist->session_duration }} دقيقة</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>متوسط وقت الرد:</span>
                                        <span class="fw-bold">{{ $specialist->response_time }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('specialists.book', $specialist->id) }}" class="btn btn-primary btn-lg w-100 mb-3">حجز جلسة</a>

                                <div class="booking-features mt-4">
                                    <div class="feature-item d-flex align-items-center mb-3">
                                        <i class="fas fa-calendar-check text-primary me-2"></i>
                                        <span>حجز فوري ومؤكد</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-3">
                                        <i class="fas fa-lock text-primary me-2"></i>
                                        <span>دفع آمن ومضمون</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-3">
                                        <i class="fas fa-undo text-primary me-2"></i>
                                        <span>إمكانية الإلغاء قبل 24 ساعة</span>
                                    </div>
                                    <div class="feature-item d-flex align-items-center">
                                        <i class="fas fa-headset text-primary me-2"></i>
                                        <span>دعم فني على مدار الساعة</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- بطاقة الخدمات -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h3 class="card-title mb-4">الخدمات المقدمة</h3>

                                <div class="services-list">
                                    @if(isset($specialist->services) && is_iterable($specialist->services))
                                        @foreach($specialist->services as $service)
                                            <div class="service-item mb-3 p-3 border rounded">
                                                <h5 class="service-name">
                                                    <a href="{{ route('services.show', $service->id) }}">{{ $service->name }}</a>
                                                </h5>
                                                <div class="service-details mb-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>المدة:</span>
                                                        <span class="fw-bold">{{ $service->duration }} دقيقة</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>السعر:</span>
                                                        <span class="fw-bold text-primary">{{ $service->price }} ر.س</span>
                                                    </div>
                                                    @if($service->discount_price)
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>السعر بعد الخصم:</span>
                                                            <span class="fw-bold text-success">{{ $service->discount_price }} ر.س</span>
                                                        </div>
                                                        <div class="discount-badge mb-2">
                                                            <span class="badge bg-danger">خصم {{ number_format((1 - $service->discount_price / $service->price) * 100) }}%</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <a href="{{ route('services.book', $service->id) }}" class="btn btn-outline-primary btn-sm w-100">حجز الخدمة</a>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            لا توجد خدمات متاحة لهذا المختص حالياً.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- بطاقة أوقات العمل -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h3 class="card-title mb-4">أوقات العمل</h3>

                                <div class="working-hours">
                                    @if(isset($specialist->working_hours) && is_iterable($specialist->working_hours))
                                        <ul class="list-group list-group-flush">
                                            @foreach($specialist->working_hours as $day => $hours)
                                                <li class="list-group-item d-flex justify-content-between px-0">
                                                    <span>{{ $day }}</span>
                                                    <span>{{ $hours }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="alert alert-info">
                                            لا توجد معلومات متاحة عن أوقات العمل حالياً.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم المختصين المشابهين -->
        <section class="similar-specialists py-5 bg-light">
            <div class="container">
                <h2 class="section-title text-center mb-5">مختصون مشابهون</h2>

                <div class="row">
                    @if(isset($similarSpecialists) && is_iterable($similarSpecialists))
                        @foreach($similarSpecialists as $similarSpecialist)
                            <div class="col-md-4 mb-4">
                                <div class="card specialist-card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="specialist-avatar mb-3">
                                            <img src="{{ $similarSpecialist->profile_image ? asset('storage/' . $similarSpecialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $similarSpecialist->user->name }}" class="rounded-circle" width="100" height="100">
                                        </div>
                                        <h3 class="card-title h5">{{ $similarSpecialist->user->name }}</h3>
                                        <p class="specialist-title text-muted mb-3">{{ $similarSpecialist->title }}</p>
                                        <div class="specialist-rating d-flex justify-content-center align-items-center mb-3">
                                            <div class="rating-stars me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $similarSpecialist->average_rating ? 'text-warning' : 'text-muted' }} small"></i>
                                                @endfor
                                            </div>
                                            <span class="rating-value small">{{ number_format($similarSpecialist->average_rating, 1) }}</span>
                                            <span class="rating-count small text-muted">({{ $similarSpecialist->reviews_count }})</span>
                                        </div>
                                        <div class="specialist-meta d-flex justify-content-center flex-wrap mb-3">
                                            <div class="me-3 mb-2">
                                                <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                <span class="small">{{ $similarSpecialist->location }}</span>
                                            </div>
                                            <div class="me-3 mb-2">
                                                <i class="fas fa-calendar-check text-primary me-1"></i>
                                                <span class="small">{{ $similarSpecialist->experience_years }} سنوات خبرة</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <a href="{{ route('specialists.show', $similarSpecialist->id) }}" class="btn btn-outline-primary btn-sm">عرض الملف</a>
                                            <a href="{{ route('specialists.book', $similarSpecialist->id) }}" class="btn btn-primary btn-sm">حجز جلسة</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                لا يوجد مختصون مشابهون متاحون حالياً.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
