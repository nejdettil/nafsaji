@extends('layouts.app')

@section('title', 'صفحة الاتصال - نفسجي')

@section('content')
<div class="contact-page">
    <!-- قسم العنوان الرئيسي -->
    <section class="page-header bg-gradient-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">تواصل معنا</h1>
                    <p class="lead mb-4">نحن هنا للإجابة على استفساراتك ومساعدتك في رحلتك نحو الصحة النفسية</p>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('assets/images/contact-hero.svg') }}" alt="تواصل معنا" class="img-fluid" style="max-height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- قسم معلومات الاتصال والنموذج -->
    <section class="contact-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <!-- معلومات الاتصال -->
                    <div class="contact-info">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h2 class="section-title mb-4">معلومات الاتصال</h2>
                                
                                <div class="contact-item d-flex align-items-start mb-4">
                                    <div class="contact-icon me-3">
                                        <div class="icon-circle bg-primary-light">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="contact-details">
                                        <h5 class="mb-2">العنوان</h5>
                                        <p class="mb-0">دمشق، سوريا</p>
                                    </div>
                                </div>
                                
                                <div class="contact-item d-flex align-items-start mb-4">
                                    <div class="contact-icon me-3">
                                        <div class="icon-circle bg-primary-light">
                                            <i class="fas fa-phone-alt text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="contact-details">
                                        <h5 class="mb-2">الهاتف</h5>
                                        <p class="mb-0">
                                            <a href="tel:+963XXXXXXXXX" class="text-body">+963 XX XXX XXXX</a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="contact-item d-flex align-items-start mb-4">
                                    <div class="contact-icon me-3">
                                        <div class="icon-circle bg-primary-light">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="contact-details">
                                        <h5 class="mb-2">البريد الإلكتروني</h5>
                                        <p class="mb-0">
                                            <a href="mailto:info@nafsaji.com" class="text-body">info@nafsaji.com</a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="contact-item d-flex align-items-start">
                                    <div class="contact-icon me-3">
                                        <div class="icon-circle bg-primary-light">
                                            <i class="fas fa-clock text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="contact-details">
                                        <h5 class="mb-2">ساعات العمل</h5>
                                        <p class="mb-0">من الأحد إلى الخميس: 9:00 صباحاً - 5:00 مساءً</p>
                                        <p class="mb-0">الجمعة والسبت: مغلق</p>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h5 class="mb-3">تابعنا على</h5>
                                <div class="social-links">
                                    <a href="https://www.facebook.com/people/Nafsaji/100089054826728/" target="_blank" class="social-link facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="https://www.instagram.com/nafsajii/" target="_blank" class="social-link instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" target="_blank" class="social-link twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" target="_blank" class="social-link linkedin">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <!-- نموذج الاتصال -->
                    <div class="contact-form">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="section-title mb-4">أرسل رسالة</h2>
                                
                                @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif
                                
                                <form action="{{ route('contact.send') }}" method="POST" id="contactForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">رقم الهاتف</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                            @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="subject" class="form-label">الموضوع <span class="text-danger">*</span></label>
                                            <select class="form-select @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                                <option value="" selected disabled>اختر الموضوع</option>
                                                <option value="استفسار عام" {{ old('subject') == 'استفسار عام' ? 'selected' : '' }}>استفسار عام</option>
                                                <option value="حجز جلسة" {{ old('subject') == 'حجز جلسة' ? 'selected' : '' }}>حجز جلسة</option>
                                                <option value="استفسار عن الخدمات" {{ old('subject') == 'استفسار عن الخدمات' ? 'selected' : '' }}>استفسار عن الخدمات</option>
                                                <option value="الانضمام كمختص" {{ old('subject') == 'الانضمام كمختص' ? 'selected' : '' }}>الانضمام كمختص</option>
                                                <option value="الدعم الفني" {{ old('subject') == 'الدعم الفني' ? 'selected' : '' }}>الدعم الفني</option>
                                                <option value="أخرى" {{ old('subject') == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                            </select>
                                            @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label for="message" class="form-label">الرسالة <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                                            @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input @error('privacy_policy') is-invalid @enderror" type="checkbox" id="privacy_policy" name="privacy_policy" required {{ old('privacy_policy') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="privacy_policy">
                                                    أوافق على <a href="{{ route('privacy-policy') }}" target="_blank">سياسة الخصوصية</a> وأسمح بمعالجة بياناتي الشخصية
                                                </label>
                                                @error('privacy_policy')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-lg">إرسال الرسالة</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الخريطة -->
    <section class="map-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="map-container">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d106456.51594432085!2d36.23063065!3d33.5073755!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1518e6dc413cc6a7%3A0x6b9f66ebd1e394f2!2sDamascus%2C%20Syria!5e0!3m2!1sen!2s!4v1650000000000!5m2!1sen!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الأسئلة الشائعة -->
    <section class="faq-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title-center">الأسئلة الشائعة</h2>
                    <p class="lead">إليك بعض الإجابات على الأسئلة الأكثر شيوعاً</p>
                </div>
                
                <div class="col-lg-10 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    كيف يمكنني حجز جلسة مع مختص؟
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    يمكنك حجز جلسة مع مختص من خلال زيارة صفحة المختصين واختيار المختص المناسب لك، ثم النقر على زر "حجز جلسة" واتباع الخطوات البسيطة لإكمال الحجز. يمكنك أيضاً التواصل معنا مباشرة عبر نموذج الاتصال أو الهاتف للمساعدة في حجز جلسة.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    ما هي طرق الدفع المتاحة؟
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    نوفر عدة طرق للدفع لتسهيل عملية حجز الجلسات، بما في ذلك الدفع عبر البطاقات الائتمانية، والتحويل البنكي، والدفع النقدي عند الحضور للجلسة (في حالة الجلسات الحضورية). يمكنك اختيار طريقة الدفع المناسبة لك أثناء عملية الحجز.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    هل يمكنني إلغاء أو إعادة جدولة جلستي؟
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    نعم، يمكنك إلغاء أو إعادة جدولة جلستك من خلال حسابك الشخصي على الموقع. يرجى ملاحظة أنه يجب إلغاء أو إعادة جدولة الجلسة قبل 24 ساعة على الأقل من موعدها لتجنب أي رسوم إلغاء. إذا كنت بحاجة إلى مساعدة، يمكنك التواصل معنا مباشرة.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    كيف يمكنني الانضمام كمختص إلى منصة نفسجي؟
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    إذا كنت مختصاً في مجال الصحة النفسية وترغب في الانضمام إلى منصة نفسجي، يمكنك التقدم بطلب من خلال صفحة "انضم إلينا" على الموقع. ستحتاج إلى تقديم معلوماتك الشخصية والمهنية، بالإضافة إلى المؤهلات والشهادات ذات الصلة. سيقوم فريقنا بمراجعة طلبك والتواصل معك في أقرب وقت ممكن.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                    هل الجلسات عبر الإنترنت آمنة وخاصة؟
                                </button>
                            </h2>
                            <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    نعم، نحن نأخذ خصوصية وأمان جلساتك على محمل الجد. جميع الجلسات عبر الإنترنت تتم من خلال منصة آمنة ومشفرة لضمان الخصوصية التامة. نلتزم بأعلى معايير السرية والخصوصية في جميع التفاعلات بين المختصين والعملاء.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الاشتراك في النشرة البريدية -->
    <section class="newsletter-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-3">اشترك في النشرة البريدية</h2>
                    <p class="mb-4">احصل على أحدث المقالات والنصائح النفسية مباشرة إلى بريدك الإلكتروني</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                        @csrf
                        <div class="input-group mb-3 mx-auto" style="max-width: 500px;">
                            <input type="email" class="form-control" name="email" placeholder="البريد الإلكتروني" required>
                            <button class="btn btn-light text-primary" type="submit">اشتراك</button>
                        </div>
                    </form>
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
    .section-title {
        position: relative;
        padding-bottom: 15px;
        color: #343a40;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 50px;
        height: 3px;
        background-color: #6a4c93;
    }
    .section-title-center {
        position: relative;
        padding-bottom: 15px;
        color: #343a40;
        display: inline-block;
    }
    .section-title-center::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background-color: #6a4c93;
    }
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .bg-primary-light {
        background-color: rgba(106, 76, 147, 0.1);
    }
    .text-primary {
        color: #6a4c93 !important;
    }
    .contact-item h5 {
        color: #343a40;
        font-weight: 600;
    }
    .social-links {
        display: flex;
        gap: 10px;
    }
    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .social-link:hover {
        transform: translateY(-3px);
    }
    .social-link.facebook {
        background-color: #3b5998;
    }
    .social-link.instagram {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    }
    .social-link.twitter {
        background-color: #1da1f2;
    }
    .social-link.linkedin {
        background-color: #0077b5;
    }
    .map-container {
        border-radius: 0.375rem;
        overflow: hidden;
    }
    .accordion-button:not(.collapsed) {
        background-color: rgba(106, 76, 147, 0.1);
        color: #6a4c93;
    }
    .accordion-button:focus {
        border-color: rgba(106, 76, 147, 0.25);
        box-shadow: 0 0 0 0.25rem rgba(106, 76, 147, 0.25);
    }
    .btn-primary {
        background-color: #6a4c93;
        border-color: #6a4c93;
    }
    .btn-primary:hover, .btn-primary:focus {
        background-color: #5a3f7d;
        border-color: #5a3f7d;
    }
    .form-control:focus, .form-select:focus {
        border-color: rgba(106, 76, 147, 0.25);
        box-shadow: 0 0 0 0.25rem rgba(106, 76, 147, 0.25);
    }
    .bg-primary {
        background-color: #6a4c93 !important;
    }
    .newsletter-section .form-control {
        border: none;
    }
    .newsletter-section .btn-light {
        border: none;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التحقق من صحة النموذج
        const contactForm = document.getElementById('contactForm');
        
        if (contactForm) {
            contactForm.addEventListener('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                this.classList.add('was-validated');
            }, false);
        }
    });
</script>
@endsection
