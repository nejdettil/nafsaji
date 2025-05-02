@extends('layouts.app')

@section('title', 'الباقات والخدمات - نفسجي للتمكين النفسي')

@section('content')
    <div class="packages-page">
        <!-- قسم العنوان الرئيسي -->
        <section class="page-header bg-gradient-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="display-4 fw-bold mb-4">باقاتنا وخدماتنا</h1>
                        <p class="lead mb-4">مجموعة متنوعة من الباقات والخدمات النفسية المصممة لتلبية احتياجاتك</p>
                        <div class="d-flex gap-3">
                            <a href="#packages-list" class="btn btn-light btn-lg">استعراض الباقات</a>
                            <a href="#services-list" class="btn btn-outline-light btn-lg">خدماتنا المميزة</a>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="{{ asset('assets/images/packages-hero.svg') }}" alt="باقات وخدمات نفسجي" class="img-fluid" style="max-height: 300px;">
                    </div>
                </div>
            </div>
        </section>

        <!-- قسم الباقات -->
        <section id="packages-list" class="py-5">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">باقاتنا المميزة</h2>
                    <div class="section-divider"></div>
                    <p class="section-subtitle">اختر الباقة المناسبة لاحتياجاتك النفسية</p>
                </div>

                <div class="row">
                    @if(isset($packages) && $packages->count() > 0)
                        @foreach($packages as $package)
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="card package-card h-100">
                                    <div class="card-header bg-{{ $package->color ?? 'primary' }} text-white text-center py-3">
                                        <h3 class="package-title mb-0">{{ $package->name }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="package-price text-center mb-4">
                                            <span class="currency">ر.س</span>
                                            <span class="amount">{{ $package->price }}</span>
                                            <span class="period">/{{ $package->duration }}</span>
                                        </div>
                                        <ul class="package-features list-unstyled">
                                            @if(isset($package->features) && is_array($package->features))
                                                @foreach($package->features as $feature)
                                                    <li><i class="fas fa-check-circle text-success me-2"></i> {{ $feature }}</li>
                                                @endforeach
                                            @else
                                                <li><i class="fas fa-check-circle text-success me-2"></i> جلسات استشارية مع مختصين معتمدين</li>
                                                <li><i class="fas fa-check-circle text-success me-2"></i> متابعة مستمرة للحالة</li>
                                                <li><i class="fas fa-check-circle text-success me-2"></i> تقارير دورية عن التقدم</li>
                                                <li><i class="fas fa-check-circle text-success me-2"></i> مواد تعليمية وإرشادية</li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="card-footer bg-light border-0 text-center py-3">
                                        <a href="{{ route('packages.show', $package) }}" class="btn btn-primary">اختر هذه الباقة</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center">
                            <p>لا توجد باقات متاحة حالياً</p>
                        </div>
                    @endif
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('packages.index') }}" class="btn btn-outline-primary btn-lg">عرض جميع الباقات</a>
                </div>
            </div>
        </section>

        <!-- قسم الخدمات المميزة -->
        <section id="services-list" class="py-5 bg-light">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">خدماتنا المميزة</h2>
                    <div class="section-divider"></div>
                    <p class="section-subtitle">مجموعة متنوعة من الخدمات النفسية المتخصصة</p>
                </div>

                <div class="row">
                    @if(isset($featuredServices) && $featuredServices->count() > 0)
                        @foreach($featuredServices as $service)
                            <div class="col-md-4 mb-4">
                                <div class="card service-card h-100">
                                    <img src="{{ asset($service->image_url) }}" class="card-img-top" alt="{{ $service->name }}">
                                    <div class="card-body">
                                        <h3 class="card-title">{{ $service->name }}</h3>
                                        <p class="card-text">{{ $service->short_description }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="service-price">{{ $service->price }} ر.س</span>
                                            <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-primary">التفاصيل</a>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-0">
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i> {{ $service->category->name }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center">
                            <p>لا توجد خدمات مميزة متاحة حالياً</p>
                        </div>
                    @endif
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-lg">عرض جميع الخدمات</a>
                </div>
            </div>
        </section>

        <!-- قسم التصنيفات -->
        <section class="py-5">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">تصفح حسب التصنيف</h2>
                    <div class="section-divider"></div>
                    <p class="section-subtitle">اختر التصنيف المناسب لاحتياجاتك</p>
                </div>

                <div class="row">
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <div class="col-md-3 col-6 mb-4">
                                <a href="{{ route('services.index', ['category' => $category->slug]) }}" class="category-card d-block text-center p-4 h-100">
                                    <div class="category-icon mb-3">
                                        <i class="{{ $category->icon ?? 'fas fa-brain' }} fa-3x"></i>
                                    </div>
                                    <h3 class="category-title h5">{{ $category->name }}</h3>
                                    <span class="badge bg-primary">{{ $category->services_count }} خدمة</span>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center">
                            <p>لا توجد تصنيفات متاحة حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- قسم الاتصال -->
        <section class="py-5 bg-gradient-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 mx-auto text-center">
                        <h2 class="mb-4">هل تحتاج إلى مساعدة في اختيار الخدمة المناسبة؟</h2>
                        <p class="lead mb-4">فريقنا جاهز لمساعدتك في اختيار الخدمة أو الباقة المناسبة لاحتياجاتك</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('contact') }}" class="btn btn-light btn-lg">تواصل معنا</a>
                            <a href="tel:+966500000000" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-phone-alt me-2"></i> اتصل بنا
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
