@extends('layouts.app')

@section('title', 'الخدمات - نفسجي')

@section('content')
<div class="services-page">
    <!-- قسم العنوان الرئيسي -->
    <section class="page-header bg-gradient-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">خدماتنا</h1>
                    <p class="lead mb-4">نقدم مجموعة متنوعة من الخدمات النفسية المتخصصة لمساعدتك في رحلتك نحو الصحة النفسية والتوازن الداخلي</p>
                    <a href="{{ route('specialists') }}" class="btn btn-light btn-lg">احجز جلستك الآن</a>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('assets/images/services-hero.svg') }}" alt="خدمات نفسجي" class="img-fluid" style="max-height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- قسم فئات الخدمات -->
    <section class="service-categories py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">فئات الخدمات</h2>
                <p class="text-muted">اختر الفئة التي تناسب احتياجاتك</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="categories-filter mb-5">
                        <ul class="nav nav-pills justify-content-center" id="categories-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">جميع الفئات</button>
                            </li>
                            @foreach($categories as $category)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="category-{{ $category->id }}-tab" data-bs-toggle="pill" data-bs-target="#category-{{ $category->id }}" type="button" role="tab" aria-controls="category-{{ $category->id }}" aria-selected="false">{{ $category->name }}</button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="categories-tabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="row">
                        @foreach($services as $service)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card service-card h-100">
                                <div class="card-body">
                                    <div class="service-icon mb-3">
                                        <i class="{{ $service->icon ?? 'fas fa-brain' }}"></i>
                                    </div>
                                    <h3 class="card-title">{{ $service->name }}</h3>
                                    <p class="card-text">{{ $service->short_description }}</p>
                                    <div class="service-meta d-flex justify-content-between align-items-center">
                                        <span class="service-price">{{ number_format($service->price, 0) }} ر.س</span>
                                        <span class="service-duration"><i class="far fa-clock me-1"></i> {{ $service->duration }} دقيقة</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-center">
                                    <a href="{{ route('services.show', $service->id) }}" class="btn btn-outline-primary">التفاصيل</a>
                                    <a href="{{ route('services.book', $service->id) }}" class="btn btn-primary">احجز الآن</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @foreach($categories as $category)
                <div class="tab-pane fade" id="category-{{ $category->id }}" role="tabpanel" aria-labelledby="category-{{ $category->id }}-tab">
                    <div class="row">
                        @php $categoryServices = $services->where('category_id', $category->id); @endphp
                        @if($categoryServices->count() > 0)
                            @foreach($categoryServices as $service)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card service-card h-100">
                                    <div class="card-body">
                                        <div class="service-icon mb-3">
                                            <i class="{{ $service->icon ?? 'fas fa-brain' }}"></i>
                                        </div>
                                        <h3 class="card-title">{{ $service->name }}</h3>
                                        <p class="card-text">{{ $service->short_description }}</p>
                                        <div class="service-meta d-flex justify-content-between align-items-center">
                                            <span class="service-price">{{ number_format($service->price, 0) }} ر.س</span>
                                            <span class="service-duration"><i class="far fa-clock me-1"></i> {{ $service->duration }} دقيقة</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 text-center">
                                        <a href="{{ route('services.show', $service->id) }}" class="btn btn-outline-primary">التفاصيل</a>
                                        <a href="{{ route('services.book', $service->id) }}" class="btn btn-primary">احجز الآن</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center py-5">
                                <img src="{{ asset('assets/images/no-services.svg') }}" alt="لا توجد خدمات" class="img-fluid mb-3" style="max-height: 150px;">
                                <h5>لا توجد خدمات في هذه الفئة حالياً</h5>
                                <p class="text-muted">يرجى اختيار فئة أخرى أو العودة لاحقاً</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- قسم الباقات -->
    <section class="packages-section py-5 bg-light">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">باقاتنا المميزة</h2>
                <p class="text-muted">اختر الباقة المناسبة لاحتياجاتك واستفد من العروض الخاصة</p>
            </div>

            <div class="row">
                @foreach($packages as $package)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card package-card h-100 {{ $package->is_featured ? 'featured' : '' }}">
                        @if($package->is_featured)
                        <div class="package-badge">الأكثر طلباً</div>
                        @endif
                        <div class="card-header text-center bg-transparent">
                            <h3 class="package-title">{{ $package->name }}</h3>
                            <div class="package-price">
                                <span class="currency">ر.س</span>
                                <span class="amount">{{ number_format($package->price, 0) }}</span>
                                @if($package->original_price > $package->price)
                                <span class="original-price">{{ number_format($package->original_price, 0) }}</span>
                                @endif
                            </div>
                            <p class="package-description">{{ $package->short_description }}</p>
                        </div>
                        <div class="card-body">
                            <ul class="package-features">
                                @foreach(explode("\n", $package->features) as $feature)
                                <li><i class="fas fa-check-circle text-success me-2"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <a href="{{ route('packages.show', $package->id) }}" class="btn btn-outline-primary">التفاصيل</a>
                            <a href="{{ route('packages.book', $package->id) }}" class="btn btn-primary">اشترك الآن</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- قسم الأسئلة الشائعة -->
    <section class="faq-section py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">الأسئلة الشائعة</h2>
                <p class="text-muted">إجابات على الأسئلة الأكثر شيوعاً حول خدماتنا</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        @foreach($faqs as $index => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <p class="mb-3">لم تجد إجابة على سؤالك؟</p>
                <a href="{{ route('contact') }}" class="btn btn-outline-primary">تواصل معنا</a>
            </div>
        </div>
    </section>

    <!-- قسم الدعوة للعمل -->
    <section class="cta-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="fw-bold mb-4">ابدأ رحلتك نحو الصحة النفسية اليوم</h2>
                    <p class="lead mb-4">نحن هنا لمساعدتك في كل خطوة من رحلتك نحو التوازن النفسي والسعادة</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('specialists') }}" class="btn btn-light btn-lg">تصفح المختصين</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">سجل الآن</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
    .page-header {
        padding: 80px 0;
        background: linear-gradient(135deg, #6a4c93 0%, #9163cb 100%);
        border-radius: 0 0 50px 50px;
        margin-bottom: 50px;
    }
    .service-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
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
        margin-bottom: 20px;
    }
    .service-price {
        font-weight: bold;
        color: #6a4c93;
        font-size: 1.2rem;
    }
    .service-duration {
        color: #6c757d;
    }
    .categories-filter .nav-pills {
        gap: 10px;
    }
    .categories-filter .nav-link {
        border-radius: 30px;
        padding: 8px 20px;
        color: #6a4c93;
        background-color: transparent;
        border: 1px solid #6a4c93;
    }
    .categories-filter .nav-link.active {
        background-color: #6a4c93;
        color: white;
    }
    .package-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }
    .package-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    .package-card.featured {
        border: 2px solid #6a4c93;
        transform: scale(1.05);
    }
    .package-card.featured:hover {
        transform: scale(1.05) translateY(-10px);
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
    .package-title {
        color: #6a4c93;
        font-weight: bold;
    }
    .package-price {
        margin: 20px 0;
    }
    .package-price .currency {
        font-size: 1rem;
        vertical-align: top;
        margin-right: 5px;
    }
    .package-price .amount {
        font-size: 2.5rem;
        font-weight: bold;
        color: #6a4c93;
    }
    .package-price .original-price {
        font-size: 1.2rem;
        color: #dc3545;
        text-decoration: line-through;
        margin-left: 10px;
    }
    .package-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .package-features li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .package-features li:last-child {
        border-bottom: none;
    }
    .accordion-button:not(.collapsed) {
        background-color: rgba(106, 76, 147, 0.1);
        color: #6a4c93;
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(106, 76, 147, 0.25);
    }
    .cta-section {
        background: linear-gradient(135deg, #6a4c93 0%, #9163cb 100%);
        border-radius: 50px 50px 0 0;
        margin-top: 50px;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل التبويبات
        const triggerTabList = [].slice.call(document.querySelectorAll('#categories-tab button'));
        triggerTabList.forEach(function(triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
    });
</script>
@endsection
