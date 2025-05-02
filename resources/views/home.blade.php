@extends('layouts.app')

@section('title', 'نفسجي - للتمكين النفسي')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">رحلتك نحو الصحة النفسية تبدأ هنا</h1>
                <p class="hero-text">نفسجي منصة متخصصة في تقديم خدمات الدعم والاستشارات النفسية عن بعد، تهدف إلى تمكين الأفراد نفسياً وتحسين جودة حياتهم.</p>
                <div class="hero-buttons">
                    <a href="{{ route('specialists.index') }}" class="btn btn-primary btn-lg me-3">ابحث عن مختص</a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-lg">استكشف خدماتنا</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/images/hero-image.png') }}" alt="نفسجي للتمكين النفسي" class="img-fluid hero-image">
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">خدماتنا</h2>
            <p class="section-subtitle">نقدم مجموعة متنوعة من الخدمات النفسية لمساعدتك في رحلتك نحو الصحة النفسية</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="service-title">الاستشارات النفسية</h3>
                    <p class="service-text">جلسات استشارية مع مختصين مؤهلين لمساعدتك في التعامل مع التحديات النفسية المختلفة.</p>
                    <a href="{{ route('services.show', 1) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="service-title">العلاج النفسي</h3>
                    <p class="service-text">برامج علاجية متخصصة للتعامل مع الاضطرابات النفسية المختلفة وتحسين الصحة النفسية.</p>
                    <a href="{{ route('services.show', 2) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="service-title">الإرشاد الأسري</h3>
                    <p class="service-text">جلسات إرشادية للأسر لتحسين التواصل وحل المشكلات وبناء علاقات أسرية صحية.</p>
                    <a href="{{ route('services.show', 3) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="service-title">الإرشاد الزواجي</h3>
                    <p class="service-text">جلسات متخصصة للأزواج لتحسين التواصل وحل الخلافات وتعزيز العلاقة الزوجية.</p>
                    <a href="{{ route('services.show', 4) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3 class="service-title">علاج الأطفال والمراهقين</h3>
                    <p class="service-text">خدمات متخصصة للأطفال والمراهقين للتعامل مع المشكلات السلوكية والنفسية.</p>
                    <a href="{{ route('services.show', 5) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="service-title">تنمية المهارات الشخصية</h3>
                    <p class="service-text">برامج لتطوير المهارات الشخصية وتعزيز الثقة بالنفس وتحسين جودة الحياة.</p>
                    <a href="{{ route('services.show', 6) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('services.index') }}" class="btn btn-primary">عرض جميع الخدمات</a>
        </div>
    </div>
</section>

<!-- Specialists Section -->
<section class="specialists-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">المختصين المميزين</h2>
            <p class="section-subtitle">فريق من المختصين المؤهلين والمعتمدين لتقديم أفضل الخدمات النفسية</p>
        </div>
        
        <div class="row" id="featuredSpecialists">
            <!-- سيتم تحميل المختصين ديناميكياً هنا -->
            @foreach($specialists as $specialist)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="specialist-card">
                    <img src="{{ $specialist->user->profile_image ? asset($specialist->user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->user->name }}" class="specialist-img">
                    <h4 class="specialist-name">{{ $specialist->user->name }}</h4>
                    <p class="specialist-title">{{ $specialist->specialization }}</p>
                    <div class="specialist-rating">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $specialist->rating)
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $specialist->rating)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                        <span class="rating-value">{{ number_format($specialist->rating, 1) }}</span>
                    </div>
                    <p class="specialist-bio">{{ Str::limit($specialist->bio, 100) }}</p>
                    <a href="{{ route('specialists.show', $specialist->id) }}" class="btn btn-outline-primary">عرض الملف الشخصي</a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('specialists.index') }}" class="btn btn-primary">عرض جميع المختصين</a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">كيف تعمل منصة نفسجي؟</h2>
            <p class="section-subtitle">خطوات بسيطة للحصول على الدعم النفسي الذي تحتاجه</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="step-card text-center">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="step-title">ابحث عن مختص</h4>
                    <p class="step-text">استعرض قائمة المختصين واختر المختص المناسب لاحتياجاتك.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="step-card text-center">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h4 class="step-title">احجز موعداً</h4>
                    <p class="step-text">اختر الوقت المناسب لك واحجز موعداً مع المختص.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="step-card text-center">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4 class="step-title">ادفع بأمان</h4>
                    <p class="step-text">استخدم وسائل الدفع الآمنة لتأكيد حجزك.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="step-card text-center">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h4 class="step-title">ابدأ الجلسة</h4>
                    <p class="step-text">انضم إلى الجلسة عبر الإنترنت في الموعد المحدد.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">آراء العملاء</h2>
            <p class="section-subtitle">ماذا يقول عملاؤنا عن تجربتهم مع نفسجي</p>
        </div>
        
        <div class="testimonials-slider">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"تجربتي مع نفسجي كانت رائعة، المختصين محترفين وساعدوني كثيراً في التغلب على مشكلاتي النفسية. أنصح بشدة بهذه المنصة."</p>
                        <div class="testimonial-author">
                            <img src="{{ asset('assets/images/testimonial-1.jpg') }}" alt="أحمد محمد" class="testimonial-img">
                            <div class="testimonial-info">
                                <h5 class="author-name">أحمد محمد</h5>
                                <p class="author-title">عميل</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="testimonial-text">"سهولة الاستخدام وجودة الخدمة جعلتني أستمر مع نفسجي. الجلسات عن بعد وفرت علي الكثير من الوقت والجهد، والنتائج كانت مذهلة."</p>
                        <div class="testimonial-author">
                            <img src="{{ asset('assets/images/testimonial-2.jpg') }}" alt="سارة أحمد" class="testimonial-img">
                            <div class="testimonial-info">
                                <h5 class="author-name">سارة أحمد</h5>
                                <p class="author-title">عميلة</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"كنت متردداً في البداية، لكن بعد تجربة الجلسة الأولى أدركت قيمة هذه الخدمة. المختصين محترفين ويقدمون نصائح عملية ساعدتني كثيراً."</p>
                        <div class="testimonial-author">
                            <img src="{{ asset('assets/images/testimonial-3.jpg') }}" alt="محمد علي" class="testimonial-img">
                            <div class="testimonial-info">
                                <h5 class="author-name">محمد علي</h5>
                                <p class="author-title">عميل</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5">
    <div class="container">
        <div class="cta-card bg-primary text-white p-5 rounded-lg">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="cta-title">هل أنت مستعد لبدء رحلتك نحو الصحة النفسية؟</h2>
                    <p class="cta-text">انضم إلى آلاف العملاء الذين يثقون بنفسجي للحصول على الدعم النفسي الذي يحتاجونه.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">سجل الآن</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
