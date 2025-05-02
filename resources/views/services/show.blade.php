@extends('layouts.app')

@section('title', '{{ $service->name }} - نفسجي')

@section('content')
    <div class="service-details-page">
        <!-- قسم العنوان الرئيسي -->
        <section class="page-header bg-gradient-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-3">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-white">الخدمات</a></li>
                                <li class="breadcrumb-item active text-white-50" aria-current="page">{{ $service->name }}</li>
                            </ol>
                        </nav>
                        <h1 class="display-4 fw-bold mb-4">{{ $service->name }}</h1>
                        <div class="service-category mb-3">
                            <span class="badge bg-light text-primary">{{ $service->category->name }}</span>
                        </div>
                        <p class="lead mb-4">{{ $service->short_description }}</p>
                        <a href="{{ route('services.book', $service->id) }}" class="btn btn-light btn-lg me-2">احجز الآن</a>
                        <a href="#specialists" class="btn btn-outline-light btn-lg">المختصون</a>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="{{ $service->image ? asset('storage/' . $service->image) : asset('assets/images/service-default.svg') }}" alt="{{ $service->name }}" class="img-fluid rounded-3 service-image">
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم تفاصيل الخدمة -->
        <section class="service-details py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h2 class="section-title mb-4">عن الخدمة</h2>
                                <div class="service-description">
                                    {!! $service->description !!}
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h2 class="section-title mb-4">لمن تناسب هذه الخدمة؟</h2>
                                <div class="service-target">
                                    <ul class="service-benefits-list">
                                        @if(isset($service->target_audience) && is_iterable($service->target_audience))
                                            @foreach($service->target_audience as $target)
                                                <li>
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                    {{ $target }}
                                                </li>
                                            @endforeach
                                        @else
                                            <li>
                                                <div class="alert alert-info">
                                                    لا توجد معلومات متاحة حول الفئات المستهدفة لهذه الخدمة حالياً.
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h2 class="section-title mb-4">الفوائد والنتائج</h2>
                                <div class="service-benefits">
                                    <ul class="service-benefits-list">
                                        @if(isset($service->benefits) && is_iterable($service->benefits))
                                            @foreach($service->benefits as $benefit)
                                                <li>
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                    {{ $benefit }}
                                                </li>
                                            @endforeach
                                        @else
                                            <li>
                                                <div class="alert alert-info">
                                                    لا توجد معلومات متاحة حول فوائد ونتائج هذه الخدمة حالياً.
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h2 class="section-title mb-4">كيف تتم الجلسات؟</h2>
                                <div class="service-process">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="process-card text-center p-3 h-100">
                                                <div class="process-icon mb-3">
                                                    <i class="fas fa-video fa-3x text-primary"></i>
                                                </div>
                                                <h4>عن بعد</h4>
                                                <p class="text-muted">جلسات عبر الفيديو من أي مكان تفضله</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="process-card text-center p-3 h-100">
                                                <div class="process-icon mb-3">
                                                    <i class="fas fa-building fa-3x text-primary"></i>
                                                </div>
                                                <h4>حضوري</h4>
                                                <p class="text-muted">جلسات وجهاً لوجه في مقر نفسجي</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="process-steps mt-4">
                                        <div class="step-item d-flex mb-4">
                                            <div class="step-number">1</div>
                                            <div class="step-content ms-3">
                                                <h5>الحجز واختيار المختص</h5>
                                                <p>اختر المختص المناسب لك وحدد موعد الجلسة بما يناسب جدولك</p>
                                            </div>
                                        </div>
                                        <div class="step-item d-flex mb-4">
                                            <div class="step-number">2</div>
                                            <div class="step-content ms-3">
                                                <h5>الجلسة الأولى</h5>
                                                <p>سيقوم المختص بالتعرف عليك وفهم احتياجاتك وتحديد خطة العلاج المناسبة</p>
                                            </div>
                                        </div>
                                        <div class="step-item d-flex mb-4">
                                            <div class="step-number">3</div>
                                            <div class="step-content ms-3">
                                                <h5>الجلسات المتابعة</h5>
                                                <p>جلسات دورية لمتابعة التقدم وتطبيق الأساليب العلاجية المناسبة</p>
                                            </div>
                                        </div>
                                        <div class="step-item d-flex">
                                            <div class="step-number">4</div>
                                            <div class="step-content ms-3">
                                                <h5>المتابعة والدعم</h5>
                                                <p>دعم مستمر ومتابعة لضمان تحقيق النتائج المرجوة</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- قسم التقييمات -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h2 class="section-title mb-0">تقييمات العملاء</h2>
                                    <div class="rating-summary">
                                        <div class="d-flex align-items-center">
                                            <div class="rating-stars me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $service->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <div class="rating-value">
                                                <span class="fw-bold">{{ number_format($service->average_rating, 1) }}</span>
                                                <span class="text-muted">({{ $service->reviews_count }} تقييم)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews-list">
                                    @if(isset($service->reviews) && is_iterable($service->reviews))
                                        @forelse($service->reviews as $review)
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
                                                <p class="text-muted">لا توجد تقييمات لهذه الخدمة حتى الآن</p>
                                            </div>
                                        @endforelse

                                        @if($service->reviews_count > 3)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('services.reviews', $service->id) }}" class="btn btn-outline-primary">عرض جميع التقييمات</a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="far fa-comment-dots fa-3x text-muted"></i>
                                            </div>
                                            <p class="text-muted">لا توجد تقييمات لهذه الخدمة حتى الآن</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- قسم الأسئلة الشائعة -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h2 class="section-title mb-4">الأسئلة الشائعة</h2>
                                <div class="faqs-list">
                                    <div class="accordion" id="faqAccordion">
                                        @if(isset($service->faqs) && is_iterable($service->faqs))
                                            @foreach($service->faqs as $index => $faq)
                                                <div class="accordion-item border-0 mb-3">
                                                    <h3 class="accordion-header" id="faqHeading{{ $index }}">
                                                        <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="false" aria-controls="faqCollapse{{ $index }}">
                                                            {{ $faq->question }}
                                                        </button>
                                                    </h3>
                                                    <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="faqHeading{{ $index }}" data-bs-parent="#faqAccordion">
                                                        <div class="accordion-body">
                                                            {{ $faq->answer }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info">
                                                لا توجد أسئلة شائعة متاحة لهذه الخدمة حالياً.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- بطاقة الحجز -->
                        <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 100px; z-index: 999;">
                            <div class="card-body p-4">
                                <h3 class="card-title mb-4">احجز هذه الخدمة</h3>

                                <div class="service-info mb-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>المدة:</span>
                                        <span class="fw-bold">{{ $service->duration }} دقيقة</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>السعر:</span>
                                        <span class="fw-bold text-primary">{{ $service->price }} ر.س</span>
                                    </div>
                                    @if($service->discount_price)
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>السعر بعد الخصم:</span>
                                            <span class="fw-bold text-success">{{ $service->discount_price }} ر.س</span>
                                        </div>
                                        <div class="discount-badge mb-3">
                                            <span class="badge bg-danger">خصم {{ number_format((1 - $service->discount_price / $service->price) * 100) }}%</span>
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('services.book', $service->id) }}" class="btn btn-primary btn-lg w-100 mb-3">احجز الآن</a>

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

                        <!-- بطاقة الباقات -->
                        @if(isset($service->packages) && is_iterable($service->packages) && count($service->packages) > 0)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body p-4">
                                    <h3 class="card-title mb-4">باقات الخدمة</h3>

                                    <div class="packages-list">
                                        @foreach($service->packages as $package)
                                            <div class="package-item mb-3 p-3 border rounded">
                                                <h5 class="package-name">{{ $package->name }}</h5>
                                                <div class="package-details mb-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>عدد الجلسات:</span>
                                                        <span class="fw-bold">{{ $package->sessions_count }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>المدة:</span>
                                                        <span class="fw-bold">{{ $package->duration }} دقيقة</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>السعر:</span>
                                                        <span class="fw-bold text-primary">{{ $package->price }} ر.س</span>
                                                    </div>
                                                    @if($package->discount_price)
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>السعر بعد الخصم:</span>
                                                            <span class="fw-bold text-success">{{ $package->discount_price }} ر.س</span>
                                                        </div>
                                                        <div class="discount-badge mb-2">
                                                            <span class="badge bg-danger">خصم {{ number_format((1 - $package->discount_price / $package->price) * 100) }}%</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <a href="{{ route('packages.book', $package->id) }}" class="btn btn-outline-primary btn-sm w-100">احجز الباقة</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- بطاقة المختصين -->
                        <div class="card border-0 shadow-sm" id="specialists">
                            <div class="card-body p-4">
                                <h3 class="card-title mb-4">المختصون المقدمون للخدمة</h3>

                                <div class="specialists-list">
                                    @if(isset($service->specialists) && is_iterable($service->specialists))
                                        @foreach($service->specialists as $specialist)
                                            <div class="specialist-item mb-3">
                                                <div class="d-flex">
                                                    <div class="specialist-avatar me-3">
                                                        <img src="{{ $specialist->profile_image ? asset('storage/' . $specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->user->name }}" class="rounded-circle" width="60" height="60">
                                                    </div>
                                                    <div class="specialist-info">
                                                        <h5 class="specialist-name mb-1">
                                                            <a href="{{ route('specialists.show', $specialist->id) }}">{{ $specialist->user->name }}</a>
                                                        </h5>
                                                        <p class="specialist-title mb-1">{{ $specialist->title }}</p>
                                                        <div class="specialist-rating d-flex align-items-center mb-2">
                                                            <div class="rating-stars me-2">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star {{ $i <= $specialist->average_rating ? 'text-warning' : 'text-muted' }} small"></i>
                                                                @endfor
                                                            </div>
                                                            <span class="rating-value small">{{ number_format($specialist->average_rating, 1) }}</span>
                                                            <span class="rating-count small text-muted">({{ $specialist->reviews_count }})</span>
                                                        </div>
                                                        <a href="{{ route('specialists.book', $specialist->id) }}" class="btn btn-sm btn-outline-primary">حجز جلسة</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            لا يوجد مختصون متاحون لهذه الخدمة حالياً.
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('specialists.index', ['service_id' => $service->id]) }}" class="btn btn-outline-primary">عرض جميع المختصين</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم الخدمات المشابهة -->
        <section class="related-services py-5 bg-light">
            <div class="container">
                <h2 class="section-title text-center mb-5">خدمات مشابهة قد تهمك</h2>

                <div class="row">
                    @if(isset($relatedServices) && is_iterable($relatedServices))
                        @foreach($relatedServices as $relatedService)
                            <div class="col-md-4 mb-4">
                                <div class="card service-card h-100 border-0 shadow-sm">
                                    <img src="{{ $relatedService->image ? asset('storage/' . $relatedService->image) : asset('assets/images/service-default.svg') }}" class="card-img-top" alt="{{ $relatedService->name }}">
                                    <div class="card-body">
                                        <div class="service-category mb-2">
                                            <span class="badge bg-primary">{{ $relatedService->category->name }}</span>
                                        </div>
                                        <h3 class="card-title h5">{{ $relatedService->name }}</h3>
                                        <p class="card-text text-muted">{{ Str::limit($relatedService->short_description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="service-price">
                                                @if($relatedService->discount_price)
                                                    <span class="text-decoration-line-through text-muted me-2">{{ $relatedService->price }} ر.س</span>
                                                    <span class="fw-bold text-primary">{{ $relatedService->discount_price }} ر.س</span>
                                                @else
                                                    <span class="fw-bold text-primary">{{ $relatedService->price }} ر.س</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('services.show', $relatedService->id) }}" class="btn btn-outline-primary btn-sm">التفاصيل</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                لا توجد خدمات مشابهة متاحة حالياً.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
