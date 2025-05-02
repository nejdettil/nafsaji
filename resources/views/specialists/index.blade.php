@extends('layouts.app')

@section('title', 'المختصين - نفسجي')

@section('content')
<div class="specialists-page">
    <!-- قسم العنوان الرئيسي -->
    <section class="page-header bg-gradient-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">المختصين النفسيين</h1>
                    <p class="lead mb-4">فريق متكامل من المختصين النفسيين ذوي الخبرة والكفاءة لمساعدتك في رحلتك نحو الصحة النفسية</p>
                    <div class="d-flex gap-3">
                        <a href="#specialists-list" class="btn btn-light btn-lg">تصفح المختصين</a>
                        <a href="{{ route('services') }}" class="btn btn-outline-light btn-lg">استعرض خدماتنا</a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('assets/images/specialists-hero.svg') }}" alt="مختصين نفسجي" class="img-fluid" style="max-height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- قسم البحث والتصفية -->
    <section class="search-filter-section py-5">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('specialists') }}" method="GET" class="row g-3">
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
                            <label for="service" class="form-label">الخدمة</label>
                            <select class="form-select" id="service" name="service_id">
                                <option value="">جميع الخدمات</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">بحث</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم قائمة المختصين -->
    <section id="specialists-list" class="specialists-list-section py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">تعرف على مختصينا</h2>
                <p class="text-muted">اختر المختص المناسب لاحتياجاتك واحجز جلستك الآن</p>
            </div>

            <div class="row">
                @if(count($specialists) > 0)
                    @foreach($specialists as $specialist)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card specialist-card h-100">
                            <div class="specialist-header">
                                <div class="specialist-availability {{ $specialist->is_available ? 'available' : 'unavailable' }}">
                                    {{ $specialist->is_available ? 'متاح الآن' : 'غير متاح حالياً' }}
                                </div>
                                <div class="specialist-cover" style="background-image: url('{{ $specialist->cover_image ?? asset('assets/images/default-cover.jpg') }}')"></div>
                            </div>
                            <div class="card-body text-center">
                                <div class="specialist-avatar">
                                    <img src="{{ $specialist->avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->name }}" class="rounded-circle">
                                </div>
                                <h3 class="specialist-name mt-3">{{ $specialist->name }}</h3>
                                <p class="specialist-title text-muted">{{ $specialist->title }}</p>
                                <p class="specialist-specialization">{{ $specialist->specialization }}</p>
                                
                                <div class="specialist-rating mb-3">
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
                                
                                <div class="specialist-services mb-3">
                                    <h6 class="text-muted mb-2">الخدمات</h6>
                                    <div class="services-tags">
                                        @foreach($specialist->services as $service)
                                        <span class="service-tag">{{ $service->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="specialist-experience mb-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="experience-item">
                                                <div class="experience-value">{{ $specialist->experience_years }}</div>
                                                <div class="experience-label">سنوات الخبرة</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="experience-item">
                                                <div class="experience-value">{{ $specialist->sessions_count }}</div>
                                                <div class="experience-label">جلسة</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="experience-item">
                                                <div class="experience-value">{{ $specialist->clients_count }}</div>
                                                <div class="experience-label">عميل</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-center">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('specialists.show', $specialist->id) }}" class="btn btn-outline-primary">عرض الملف الشخصي</a>
                                    <a href="{{ route('specialists.book', $specialist->id) }}" class="btn btn-primary">احجز موعد</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <img src="{{ asset('assets/images/no-specialists.svg') }}" alt="لا يوجد مختصين" class="img-fluid mb-3" style="max-height: 200px;">
                        <h4>لم يتم العثور على مختصين</h4>
                        <p class="text-muted">يرجى تعديل معايير البحث أو المحاولة لاحقاً</p>
                        <a href="{{ route('specialists') }}" class="btn btn-primary mt-3">عرض جميع المختصين</a>
                    </div>
                @endif
            </div>

            <!-- ترقيم الصفحات -->
            @if($specialists->hasPages())
            <div class="pagination-container mt-5">
                {{ $specialists->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </section>

    <!-- قسم كيفية عمل الجلسات -->
    <section class="how-it-works-section py-5 bg-light">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">كيف تعمل جلساتنا؟</h2>
                <p class="text-muted">خطوات بسيطة للحصول على الدعم النفسي الذي تحتاجه</p>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="step-card text-center">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="step-title">اختر المختص</h4>
                        <p class="step-description">تصفح قائمة المختصين واختر المختص المناسب لاحتياجاتك</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="step-card text-center">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4 class="step-title">احجز موعد</h4>
                        <p class="step-description">اختر الوقت المناسب لك واحجز جلستك بسهولة</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="step-card text-center">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4 class="step-title">ادفع بأمان</h4>
                        <p class="step-description">استخدم طرق الدفع الآمنة والمتعددة لتأكيد حجزك</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="step-card text-center">
                        <div class="step-number">4</div>
                        <div class="step-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <h4 class="step-title">احضر الجلسة</h4>
                        <p class="step-description">استمتع بجلستك عبر الإنترنت أو حضورياً حسب اختيارك</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم التقييمات -->
    <section class="testimonials-section py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">ماذا يقول عملاؤنا</h2>
                <p class="text-muted">آراء وتجارب عملائنا مع مختصينا</p>
            </div>

            <div class="testimonials-slider">
                <div class="row">
                    @foreach($testimonials as $testimonial)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <div class="testimonial-rating mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $testimonial->rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="testimonial-text">{{ $testimonial->comment }}</p>
                            </div>
                            <div class="testimonial-author d-flex align-items-center mt-3">
                                <img src="{{ $testimonial->user_avatar ?? asset('assets/images/default-avatar.png') }}" alt="{{ $testimonial->user_name }}" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h5 class="mb-0">{{ $testimonial->user_name }}</h5>
                                    <p class="text-muted mb-0">{{ $testimonial->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
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
                        <a href="#specialists-list" class="btn btn-light btn-lg">تصفح المختصين</a>
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
    .specialist-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }
    .specialist-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    .specialist-header {
        position: relative;
    }
    .specialist-cover {
        height: 120px;
        background-size: cover;
        background-position: center;
    }
    .specialist-avatar {
        margin-top: -50px;
        position: relative;
        z-index: 1;
    }
    .specialist-avatar img {
        width: 100px;
        height: 100px;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .specialist-availability {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 1;
    }
    .specialist-availability.available {
        background-color: rgba(40, 167, 69, 0.9);
        color: white;
    }
    .specialist-availability.unavailable {
        background-color: rgba(108, 117, 125, 0.9);
        color: white;
    }
    .specialist-name {
        font-weight: bold;
        color: #343a40;
        margin-bottom: 5px;
    }
    .specialist-title {
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    .specialist-specialization {
        color: #6a4c93;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .specialist-rating {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .rating-stars {
        color: #ffc107;
    }
    .rating-value {
        font-weight: bold;
        color: #343a40;
    }
    .rating-count {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .services-tags {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    .service-tag {
        background-color: rgba(106, 76, 147, 0.1);
        color: #6a4c93;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    .specialist-experience {
        margin-top: 15px;
    }
    .experience-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #6a4c93;
    }
    .experience-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .step-card {
        background-color: white;
        border-radius: 15px;
        padding: 30px 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
        height: 100%;
    }
    .step-number {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        background-color: #6a4c93;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .step-icon {
        width: 70px;
        height: 70px;
        background-color: rgba(106, 76, 147, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #6a4c93;
        margin: 0 auto 20px;
    }
    .step-title {
        color: #343a40;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .step-description {
        color: #6c757d;
        margin-bottom: 0;
    }
    .testimonial-card {
        background-color: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
    .testimonial-text {
        color: #343a40;
        font-style: italic;
        margin-bottom: 0;
    }
    .cta-section {
        background: linear-gradient(135deg, #6a4c93 0%, #9163cb 100%);
        border-radius: 50px 50px 0 0;
        margin-top: 50px;
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تنعيم التمرير إلى قسم المختصين عند النقر على الزر
        document.querySelectorAll('a[href="#specialists-list"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endsection
