@extends('layouts.app')

@section('title', 'من نحن - نفسجي للتمكين النفسي')

@section('content')
<div class="about-page">
    <!-- قسم الترويسة -->
    <section class="about-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-header-content">
                        <h1 class="main-title">من نحن</h1>
                        <p class="lead">نفسجي للتمكين النفسي هي منصة متخصصة تهدف إلى تقديم خدمات الدعم النفسي والاستشارات المتخصصة بطريقة سهلة وميسرة للجميع.</p>
                        <p>نسعى لتحسين الصحة النفسية في المجتمع من خلال توفير خدمات نفسية عالية الجودة وبأسعار مناسبة، مع ضمان الخصوصية والراحة للمستفيدين.</p>
                        <div class="about-stats">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-number">{{ $stats['specialists_count'] }}+</div>
                                        <div class="stat-title">مختص نفسي</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-number">{{ $stats['sessions_count'] }}+</div>
                                        <div class="stat-title">جلسة نفسية</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-item">
                                        <div class="stat-number">{{ $stats['users_count'] }}+</div>
                                        <div class="stat-title">مستفيد</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-header-image">
                        <img src="{{ asset('assets/images/about/about-header.jpg') }}" alt="نفسجي للتمكين النفسي" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم قصتنا -->
    <section class="about-story">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">قصتنا</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">كيف بدأت رحلة نفسجي للتمكين النفسي</p>
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="story-content">
                        <p>بدأت فكرة نفسجي للتمكين النفسي في عام 2020، عندما لاحظت المؤسسة آلاء زينو الحاجة الملحة لتوفير خدمات نفسية متخصصة بطريقة سهلة وميسرة للجميع، خاصة في ظل الظروف الصعبة التي يمر بها العالم.</p>
                        
                        <p>كانت الرؤية واضحة منذ البداية: إنشاء منصة تجمع بين أفضل المختصين النفسيين وتتيح للمستفيدين الوصول إليهم بسهولة وخصوصية، مع التركيز على جودة الخدمة وتوفير تجربة سلسة للمستخدمين.</p>
                        
                        <p>بدأنا بفريق صغير من المختصين المتميزين، وتوسعنا تدريجياً مع الحفاظ على معايير الجودة العالية في اختيار المختصين وتقديم الخدمات. اليوم، أصبحت نفسجي واحدة من المنصات الرائدة في مجال الدعم النفسي، ونفخر بثقة آلاف المستفيدين الذين اختاروا نفسجي لرحلتهم نحو الصحة النفسية.</p>
                        
                        <div class="story-timeline">
                            <div class="timeline-item">
                                <div class="timeline-year">2020</div>
                                <div class="timeline-content">
                                    <h4>بداية الفكرة</h4>
                                    <p>تأسيس الفكرة الأولية لمنصة نفسجي للتمكين النفسي</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-year">2021</div>
                                <div class="timeline-content">
                                    <h4>إطلاق المنصة</h4>
                                    <p>إطلاق النسخة التجريبية من المنصة مع فريق من المختصين المتميزين</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-year">2022</div>
                                <div class="timeline-content">
                                    <h4>التوسع</h4>
                                    <p>توسيع نطاق الخدمات وزيادة عدد المختصين والمستفيدين</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-year">2023</div>
                                <div class="timeline-content">
                                    <h4>تطوير المنصة</h4>
                                    <p>إطلاق نسخة محدثة من المنصة مع ميزات جديدة وتحسينات في تجربة المستخدم</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-year">2024</div>
                                <div class="timeline-content">
                                    <h4>الشراكات الاستراتيجية</h4>
                                    <p>بناء شراكات استراتيجية مع مؤسسات تعليمية وصحية لتوسيع نطاق التأثير</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-year">2025</div>
                                <div class="timeline-content">
                                    <h4>التطلع للمستقبل</h4>
                                    <p>استمرار التطوير والابتكار لتقديم أفضل الخدمات النفسية للمستفيدين</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم رؤيتنا ورسالتنا -->
    <section class="about-vision-mission">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="vision-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <h3 class="card-title">رؤيتنا</h3>
                                <p class="card-text">أن نكون المنصة الرائدة في تقديم خدمات الدعم النفسي والاستشارات المتخصصة في العالم العربي، وأن نساهم في بناء مجتمع يتمتع بصحة نفسية أفضل من خلال توفير خدمات نفسية عالية الجودة وميسرة للجميع.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mission-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-icon">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <h3 class="card-title">رسالتنا</h3>
                                <p class="card-text">تمكين الأفراد من الوصول إلى خدمات الدعم النفسي بسهولة وخصوصية، وتوفير منصة تجمع بين أفضل المختصين النفسيين والمستفيدين، مع التركيز على جودة الخدمة وتوفير تجربة سلسة للمستخدمين، والمساهمة في نشر الوعي بأهمية الصحة النفسية في المجتمع.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="values-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <h3 class="card-title">قيمنا</h3>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="value-item">
                                            <div class="value-icon">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <h4 class="value-title">الخصوصية والسرية</h4>
                                            <p class="value-text">نحافظ على خصوصية وسرية بيانات المستفيدين والمختصين بأعلى معايير الأمان.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="value-item">
                                            <div class="value-icon">
                                                <i class="fas fa-certificate"></i>
                                            </div>
                                            <h4 class="value-title">الجودة والاحترافية</h4>
                                            <p class="value-text">نلتزم بتقديم خدمات نفسية عالية الجودة من خلال مختصين مؤهلين ومعتمدين.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="value-item">
                                            <div class="value-icon">
                                                <i class="fas fa-hands-helping"></i>
                                            </div>
                                            <h4 class="value-title">التعاطف والدعم</h4>
                                            <p class="value-text">نؤمن بأهمية التعاطف والدعم في رحلة الصحة النفسية لكل فرد.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="value-item">
                                            <div class="value-icon">
                                                <i class="fas fa-universal-access"></i>
                                            </div>
                                            <h4 class="value-title">الوصول للجميع</h4>
                                            <p class="value-text">نسعى لجعل خدمات الدعم النفسي متاحة للجميع بغض النظر عن الموقع أو الظروف.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="value-item">
                                            <div class="value-icon">
                                                <i class="fas fa-lightbulb"></i>
                                            </div>
                                            <h4 class="value-title">الابتكار والتطوير</h4>
                                            <p class="value-text">نلتزم بالتطوير المستمر والابتكار في تقديم خدماتنا لتلبية احتياجات المستفيدين.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="value-item">
                                            <div class="value-icon">
                                                <i class="fas fa-balance-scale"></i>
                                            </div>
                                            <h4 class="value-title">المصداقية والشفافية</h4>
                                            <p class="value-text">نتعامل بمصداقية وشفافية مع المستفيدين والمختصين في جميع جوانب عملنا.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم فريق العمل -->
    <section class="about-team">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">فريق العمل</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">تعرف على الفريق الذي يقف خلف نجاح نفسجي للتمكين النفسي</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="{{ asset('assets/images/team/alaa-zeno.jpg') }}" alt="آلاء زينو" class="img-fluid">
                            <div class="member-social">
                                <a href="https://www.instagram.com/alaa.zeno/" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">آلاء زينو</h4>
                            <p class="member-position">المؤسس والرئيس التنفيذي</p>
                            <p class="member-bio">مختصة في علم النفس الإكلينيكي مع خبرة أكثر من 10 سنوات في مجال الصحة النفسية. أسست نفسجي للتمكين النفسي بهدف توفير خدمات نفسية عالية الجودة للجميع.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="{{ asset('assets/images/team/team-member-2.jpg') }}" alt="د. سارة الأحمد" class="img-fluid">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">د. سارة الأحمد</h4>
                            <p class="member-position">المدير الطبي</p>
                            <p class="member-bio">دكتوراه في الطب النفسي مع خبرة واسعة في مجال الصحة النفسية. تشرف على جودة الخدمات النفسية المقدمة في المنصة وتطوير البرامج العلاجية.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="{{ asset('assets/images/team/team-member-3.jpg') }}" alt="م. محمد العلي" class="img-fluid">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">م. محمد العلي</h4>
                            <p class="member-position">مدير التكنولوجيا</p>
                            <p class="member-bio">مهندس برمجيات مع خبرة أكثر من 8 سنوات في تطوير المنصات الإلكترونية. يقود فريق التطوير التقني للمنصة ويشرف على تحسين تجربة المستخدم.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="{{ asset('assets/images/team/team-member-4.jpg') }}" alt="أ. نور الحسن" class="img-fluid">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">أ. نور الحسن</h4>
                            <p class="member-position">مدير العلاقات العامة</p>
                            <p class="member-bio">متخصصة في العلاقات العامة والتسويق مع خبرة واسعة في مجال الصحة النفسية. تعمل على بناء الشراكات الاستراتيجية ونشر الوعي بأهمية الصحة النفسية.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="{{ asset('assets/images/team/team-member-5.jpg') }}" alt="أ. خالد المحمود" class="img-fluid">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">أ. خالد المحمود</h4>
                            <p class="member-position">مدير خدمة العملاء</p>
                            <p class="member-bio">متخصص في خدمة العملاء مع خبرة في مجال الرعاية الصحية. يشرف على فريق خدمة العملاء ويعمل على تحسين تجربة المستفيدين والمختصين.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="{{ asset('assets/images/team/team-member-6.jpg') }}" alt="أ. ريم الخطيب" class="img-fluid">
                            <div class="member-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4 class="member-name">أ. ريم الخطيب</h4>
                            <p class="member-position">مدير المحتوى</p>
                            <p class="member-bio">متخصصة في إنتاج المحتوى النفسي والتوعوي. تشرف على إعداد المحتوى التوعوي والتثقيفي في المنصة وتطوير البرامج التوعوية.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الشركاء -->
    <section class="about-partners">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">شركاؤنا</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">نفخر بشراكاتنا مع مؤسسات رائدة في مجال الصحة النفسية والتعليم</p>
            </div>
            <div class="partners-slider">
                <div class="row">
                    @foreach($partners as $partner)
                        <div class="col-md-3 col-6">
                            <div class="partner-item">
                                <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="img-fluid">
                                <div class="partner-overlay">
                                    <h4>{{ $partner->name }}</h4>
                                    <p>{{ $partner->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الشهادات -->
    <section class="about-testimonials">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">ماذا يقول عملاؤنا</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">آراء المستفيدين من خدمات نفسجي للتمكين النفسي</p>
            </div>
            <div class="testimonials-slider">
                <div class="row">
                    @foreach($testimonials as $testimonial)
                        <div class="col-lg-4 col-md-6">
                            <div class="testimonial-item">
                                <div class="testimonial-content">
                                    <div class="testimonial-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $testimonial->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="testimonial-text">{{ $testimonial->content }}</p>
                                    <div class="testimonial-author">
                                        <div class="author-image">
                                            <img src="{{ $testimonial->user->profile_photo_url }}" alt="{{ $testimonial->user->name }}" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="author-info">
                                            <h4 class="author-name">{{ $testimonial->user->name }}</h4>
                                            <p class="author-title">{{ $testimonial->user_title }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الاتصال -->
    <section class="about-contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h2>تواصل معنا</h2>
                        <p>نحن هنا للإجابة على استفساراتك ومساعدتك في الحصول على الدعم النفسي الذي تحتاجه.</p>
                        <div class="contact-details">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-text">
                                    <h4>العنوان</h4>
                                    <p>{{ $contact_info['address'] }}</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="contact-text">
                                    <h4>الهاتف</h4>
                                    <p>{{ $contact_info['phone'] }}</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-text">
                                    <h4>البريد الإلكتروني</h4>
                                    <p>{{ $contact_info['email'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="social-links">
                            <h4>تابعنا على وسائل التواصل الاجتماعي</h4>
                            <div class="social-icons">
                                <a href="https://www.facebook.com/people/Nafsaji/100089054826728/?mibextid=LQQJ4d" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com/nafsajii" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                <a href="#"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-form-card">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">أرسل لنا رسالة</h3>
                                <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="name">الاسم</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="email">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="subject">الموضوع</label>
                                        <input type="text" class="form-control" id="subject" name="subject" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="message">الرسالة</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary w-100">إرسال</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الأسئلة الشائعة -->
    <section class="about-faq">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">الأسئلة الشائعة</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">إجابات على الأسئلة الأكثر شيوعاً حول نفسجي للتمكين النفسي</p>
            </div>
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                    ما هي خدمات نفسجي للتمكين النفسي؟
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>نفسجي للتمكين النفسي هي منصة متخصصة تقدم خدمات الدعم النفسي والاستشارات المتخصصة عبر الإنترنت. تشمل خدماتنا الجلسات الفردية مع مختصين نفسيين معتمدين، والبرامج العلاجية المتخصصة، وورش العمل والدورات التدريبية، بالإضافة إلى المحتوى التوعوي والتثقيفي في مجال الصحة النفسية.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    كيف يمكنني حجز جلسة مع مختص نفسي؟
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>يمكنك حجز جلسة مع مختص نفسي من خلال الخطوات التالية:</p>
                                    <ol>
                                        <li>إنشاء حساب في منصة نفسجي أو تسجيل الدخول إذا كان لديك حساب بالفعل.</li>
                                        <li>البحث عن المختص المناسب لاحتياجاتك من خلال صفحة المختصين.</li>
                                        <li>اختيار المختص المناسب والاطلاع على ملفه الشخصي وتقييمات المستفيدين السابقين.</li>
                                        <li>اختيار الوقت والتاريخ المناسب من جدول المختص.</li>
                                        <li>إكمال عملية الحجز ودفع رسوم الجلسة.</li>
                                        <li>ستصلك رسالة تأكيد بتفاصيل الجلسة ورابط الانضمام إليها.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    هل المختصين في نفسجي معتمدين ومؤهلين؟
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>نعم، جميع المختصين في منصة نفسجي معتمدين ومؤهلين. نحن نتبع معايير صارمة في اختيار المختصين، حيث يجب أن يكون لديهم:</p>
                                    <ul>
                                        <li>شهادة جامعية في علم النفس أو الطب النفسي أو الإرشاد النفسي من جامعة معترف بها.</li>
                                        <li>ترخيص مهني ساري المفعول من الجهات المختصة.</li>
                                        <li>خبرة عملية لا تقل عن 3 سنوات في مجال التخصص.</li>
                                        <li>اجتياز عملية التقييم والمقابلة الشخصية من فريق نفسجي.</li>
                                    </ul>
                                    <p>كما نقوم بمراجعة دورية لأداء المختصين وتقييمات المستفيدين لضمان جودة الخدمات المقدمة.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    كيف يتم ضمان خصوصية وسرية المعلومات؟
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>نحن نولي أهمية قصوى لخصوصية وسرية معلومات المستفيدين. نتبع إجراءات صارمة لحماية البيانات، بما في ذلك:</p>
                                    <ul>
                                        <li>تشفير جميع البيانات والاتصالات باستخدام تقنيات التشفير المتقدمة.</li>
                                        <li>الالتزام بمعايير أمان البيانات العالمية.</li>
                                        <li>عدم مشاركة أي معلومات شخصية مع أي طرف ثالث دون موافقة صريحة من المستفيد.</li>
                                        <li>التزام جميع المختصين بقواعد السرية المهنية وأخلاقيات المهنة.</li>
                                        <li>تخزين البيانات في خوادم آمنة ومحمية.</li>
                                    </ul>
                                    <p>يمكنك الاطلاع على سياسة الخصوصية الكاملة <a href="{{ route('privacy') }}">من هنا</a>.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                    ما هي طرق الدفع المتاحة؟
                                </button>
                            </h2>
                            <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>نوفر عدة طرق للدفع لتسهيل عملية حجز الجلسات، بما في ذلك:</p>
                                    <ul>
                                        <li>بطاقات الائتمان والخصم (فيزا، ماستركارد، مدى)</li>
                                        <li>المحافظ الإلكترونية (Apple Pay، Google Pay)</li>
                                        <li>التحويل البنكي</li>
                                        <li>نقاط نفسجي (يمكن اكتسابها من خلال البرامج والجلسات السابقة)</li>
                                    </ul>
                                    <p>جميع المعاملات المالية تتم بشكل آمن ومشفر لضمان حماية بياناتك المالية.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('faq') }}" class="btn btn-outline-primary">عرض المزيد من الأسئلة الشائعة</a>
            </div>
        </div>
    </section>

    <!-- قسم الدعوة للعمل -->
    <section class="about-cta">
        <div class="container">
            <div class="cta-card">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="cta-title">ابدأ رحلتك نحو صحة نفسية أفضل اليوم</h2>
                        <p class="cta-text">انضم إلى آلاف المستفيدين الذين وثقوا بنفسجي للتمكين النفسي واحصل على الدعم النفسي الذي تحتاجه من مختصين مؤهلين.</p>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <a href="{{ route('specialists.index') }}" class="btn btn-primary btn-lg">ابحث عن مختص الآن</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .about-page section {
        padding: 60px 0;
    }
    
    .section-header {
        margin-bottom: 40px;
    }
    
    .section-title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }
    
    .section-divider {
        width: 80px;
        height: 3px;
        background-color: #6a1b9a;
        margin: 0 auto 15px;
    }
    
    .section-subtitle {
        font-size: 18px;
        color: #666;
    }
    
    /* قسم الترويسة */
    .about-header {
        background-color: #f8f9fa;
        padding: 80px 0;
    }
    
    .about-header-content {
        padding-right: 30px;
    }
    
    .main-title {
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
    }
    
    .lead {
        font-size: 20px;
        margin-bottom: 20px;
        color: #555;
    }
    
    .about-stats {
        margin-top: 30px;
    }
    
    .stat-item {
        text-align: center;
        padding: 15px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }
    
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #6a1b9a;
        margin-bottom: 5px;
    }
    
    .stat-title {
        font-size: 16px;
        color: #666;
    }
    
    .about-header-image img {
        width: 100%;
        height: auto;
    }
    
    /* قسم قصتنا */
    .about-story {
        background-color: #fff;
    }
    
    .story-content {
        font-size: 16px;
        line-height: 1.8;
        color: #555;
    }
    
    .story-content p {
        margin-bottom: 20px;
    }
    
    .story-timeline {
        margin-top: 40px;
    }
    
    .timeline-item {
        display: flex;
        margin-bottom: 30px;
        position: relative;
    }
    
    .timeline-item:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        right: 20px;
        width: 2px;
        background-color: #e0e0e0;
    }
    
    .timeline-item:last-child:before {
        display: none;
    }
    
    .timeline-year {
        width: 80px;
        height: 40px;
        background-color: #6a1b9a;
        color: #fff;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-left: 30px;
        position: relative;
        z-index: 1;
    }
    
    .timeline-content {
        flex: 1;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .timeline-content h4 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .timeline-content p {
        font-size: 14px;
        color: #666;
        margin-bottom: 0;
    }
    
    /* قسم رؤيتنا ورسالتنا */
    .about-vision-mission {
        background-color: #f8f9fa;
    }
    
    .vision-card, .mission-card, .values-card {
        height: 100%;
    }
    
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .card-body {
        padding: 30px;
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        background-color: #6a1b9a;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 24px;
    }
    
    .card-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }
    
    .card-text {
        font-size: 16px;
        line-height: 1.8;
        color: #555;
    }
    
    .value-item {
        margin-bottom: 30px;
    }
    
    .value-icon {
        width: 50px;
        height: 50px;
        background-color: #f0e6f5;
        color: #6a1b9a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 20px;
    }
    
    .value-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .value-text {
        font-size: 14px;
        color: #666;
    }
    
    /* قسم فريق العمل */
    .about-team {
        background-color: #fff;
    }
    
    .team-member {
        margin-bottom: 30px;
    }
    
    .member-image {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .member-image img {
        width: 100%;
        height: auto;
        transition: transform 0.3s ease;
    }
    
    .member-image:hover img {
        transform: scale(1.05);
    }
    
    .member-social {
        position: absolute;
        bottom: -40px;
        left: 0;
        right: 0;
        background-color: rgba(106, 27, 154, 0.8);
        padding: 10px 0;
        display: flex;
        justify-content: center;
        transition: bottom 0.3s ease;
    }
    
    .member-image:hover .member-social {
        bottom: 0;
    }
    
    .member-social a {
        width: 30px;
        height: 30px;
        background-color: #fff;
        color: #6a1b9a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    
    .member-social a:hover {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    .member-info {
        text-align: center;
    }
    
    .member-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .member-position {
        font-size: 14px;
        color: #6a1b9a;
        margin-bottom: 10px;
    }
    
    .member-bio {
        font-size: 14px;
        color: #666;
    }
    
    /* قسم الشركاء */
    .about-partners {
        background-color: #f8f9fa;
    }
    
    .partner-item {
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .partner-item img {
        max-width: 80%;
        max-height: 80px;
        transition: all 0.3s ease;
    }
    
    .partner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(106, 27, 154, 0.9);
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .partner-item:hover .partner-overlay {
        opacity: 1;
    }
    
    .partner-item:hover img {
        transform: scale(0.8);
    }
    
    .partner-overlay h4 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .partner-overlay p {
        font-size: 12px;
        text-align: center;
    }
    
    /* قسم الشهادات */
    .about-testimonials {
        background-color: #fff;
    }
    
    .testimonial-item {
        margin-bottom: 30px;
    }
    
    .testimonial-content {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }
    
    .testimonial-content:after {
        content: '';
        position: absolute;
        bottom: -10px;
        right: 30px;
        width: 20px;
        height: 20px;
        background-color: #f8f9fa;
        transform: rotate(45deg);
    }
    
    .testimonial-rating {
        margin-bottom: 15px;
        color: #ffc107;
    }
    
    .testimonial-text {
        font-size: 14px;
        line-height: 1.8;
        color: #555;
        margin-bottom: 20px;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
    }
    
    .author-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-left: 15px;
    }
    
    .author-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .author-name {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .author-title {
        font-size: 12px;
        color: #666;
    }
    
    /* قسم الاتصال */
    .about-contact {
        background-color: #f8f9fa;
    }
    
    .contact-info {
        padding: 30px;
    }
    
    .contact-info h2 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }
    
    .contact-info p {
        font-size: 16px;
        color: #555;
        margin-bottom: 30px;
    }
    
    .contact-details {
        margin-bottom: 30px;
    }
    
    .contact-item {
        display: flex;
        margin-bottom: 20px;
    }
    
    .contact-icon {
        width: 50px;
        height: 50px;
        background-color: #f0e6f5;
        color: #6a1b9a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        font-size: 20px;
    }
    
    .contact-text h4 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .contact-text p {
        font-size: 16px;
        color: #555;
        margin-bottom: 0;
    }
    
    .social-links h4 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }
    
    .social-icons {
        display: flex;
    }
    
    .social-icons a {
        width: 40px;
        height: 40px;
        background-color: #f0e6f5;
        color: #6a1b9a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
        font-size: 18px;
        transition: all 0.3s ease;
    }
    
    .social-icons a:hover {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    .contact-form-card {
        height: 100%;
    }
    
    /* قسم الأسئلة الشائعة */
    .about-faq {
        background-color: #fff;
    }
    
    .accordion-item {
        border: none;
        margin-bottom: 15px;
    }
    
    .accordion-button {
        background-color: #f8f9fa;
        border-radius: 8px !important;
        font-weight: 600;
        color: #333;
        padding: 15px 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .accordion-button:not(.collapsed) {
        background-color: #f0e6f5;
        color: #6a1b9a;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: #f0e6f5;
    }
    
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236a1b9a'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .accordion-body {
        padding: 20px;
        background-color: #fff;
        border-radius: 0 0 8px 8px;
    }
    
    /* قسم الدعوة للعمل */
    .about-cta {
        background-color: #f8f9fa;
        padding-bottom: 80px;
    }
    
    .cta-card {
        background-color: #6a1b9a;
        color: #fff;
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(106, 27, 154, 0.3);
    }
    
    .cta-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .cta-text {
        font-size: 16px;
        margin-bottom: 0;
        opacity: 0.9;
    }
    
    .cta-card .btn-primary {
        background-color: #fff;
        color: #6a1b9a;
        border-color: #fff;
        font-weight: 600;
        padding: 12px 30px;
    }
    
    .cta-card .btn-primary:hover {
        background-color: #f0e6f5;
        color: #6a1b9a;
        border-color: #f0e6f5;
    }
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .about-header-content {
            padding-right: 0;
            margin-bottom: 30px;
        }
        
        .cta-card {
            text-align: center;
        }
        
        .cta-card .btn-primary {
            margin-top: 20px;
        }
    }
    
    @media (max-width: 767px) {
        .about-page section {
            padding: 40px 0;
        }
        
        .main-title {
            font-size: 32px;
        }
        
        .section-title {
            font-size: 28px;
        }
        
        .timeline-item {
            flex-direction: column;
        }
        
        .timeline-item:before {
            right: 40px;
            top: 40px;
            bottom: 0;
            width: 2px;
        }
        
        .timeline-year {
            margin-bottom: 15px;
            margin-left: 0;
            align-self: flex-start;
        }
        
        .contact-item {
            flex-direction: column;
        }
        
        .contact-icon {
            margin-bottom: 10px;
            margin-left: 0;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تهيئة السلايدر للشركاء
        $('.partners-slider').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });
        
        // تهيئة السلايدر للشهادات
        $('.testimonials-slider').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 4000,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });
        
        // تأثير التمرير السلس للروابط
        $('a[href*="#"]').on('click', function(e) {
            if (this.hash !== '') {
                e.preventDefault();
                
                const hash = this.hash;
                
                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 100
                }, 800);
            }
        });
        
        // تأثير ظهور العناصر عند التمرير
        $(window).scroll(function() {
            $('.fade-in').each(function() {
                const position = $(this).offset().top;
                const scroll = $(window).scrollTop();
                const windowHeight = $(window).height();
                
                if (scroll > position - windowHeight + 100) {
                    $(this).addClass('visible');
                }
            });
        });
        
        // تشغيل تأثير الظهور عند تحميل الصفحة
        $('.fade-in').each(function() {
            const position = $(this).offset().top;
            const scroll = $(window).scrollTop();
            const windowHeight = $(window).height();
            
            if (scroll > position - windowHeight + 100) {
                $(this).addClass('visible');
            }
        });
    });
</script>
@endsection
