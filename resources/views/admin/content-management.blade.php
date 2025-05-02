@extends('layouts.dashboard')

@section('title', 'إدارة المحتوى')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">إدارة المحتوى</h4>
                    <p class="card-category">إدارة محتوى الموقع والصفحات الثابتة</p>
                </div>
                <div class="card-body">
                    <div class="content-management-container">
                        <ul class="nav nav-tabs" id="contentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pages-tab" data-bs-toggle="tab" data-bs-target="#pages" type="button" role="tab" aria-controls="pages" aria-selected="true">الصفحات الثابتة</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="blog-tab" data-bs-toggle="tab" data-bs-target="#blog" type="button" role="tab" aria-controls="blog" aria-selected="false">المدونة</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab" aria-controls="faq" aria-selected="false">الأسئلة الشائعة</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="testimonials-tab" data-bs-toggle="tab" data-bs-target="#testimonials" type="button" role="tab" aria-controls="testimonials" aria-selected="false">آراء العملاء</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="banners-tab" data-bs-toggle="tab" data-bs-target="#banners" type="button" role="tab" aria-controls="banners" aria-selected="false">البانرات الإعلانية</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="contentTabsContent">
                            <!-- الصفحات الثابتة -->
                            <div class="tab-pane fade show active" id="pages" role="tabpanel" aria-labelledby="pages-tab">
                                <div class="tab-header d-flex justify-content-between align-items-center my-3">
                                    <h5 class="mb-0">الصفحات الثابتة</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPageModal">
                                        <i class="fas fa-plus"></i> إضافة صفحة جديدة
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>العنوان</th>
                                                <th>الرابط</th>
                                                <th>آخر تحديث</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($pages) && count($pages) > 0)
                                                @foreach($pages as $page)
                                                    <tr>
                                                        <td>{{ $page->title }}</td>
                                                        <td>{{ $page->slug }}</td>
                                                        <td>{{ $page->updated_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $page->is_active ? 'success' : 'secondary' }}">
                                                                {{ $page->is_active ? 'منشورة' : 'مسودة' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.content.pages.edit', $page->id) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admin.content.pages.show', $page->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-danger delete-item" data-id="{{ $page->id }}" data-type="page">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">لا توجد صفحات</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- المدونة -->
                            <div class="tab-pane fade" id="blog" role="tabpanel" aria-labelledby="blog-tab">
                                <div class="tab-header d-flex justify-content-between align-items-center my-3">
                                    <h5 class="mb-0">المدونة</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPostModal">
                                        <i class="fas fa-plus"></i> إضافة مقال جديد
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>العنوان</th>
                                                <th>الكاتب</th>
                                                <th>التصنيف</th>
                                                <th>تاريخ النشر</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($posts) && count($posts) > 0)
                                                @foreach($posts as $post)
                                                    <tr>
                                                        <td>{{ $post->title }}</td>
                                                        <td>{{ $post->author->name }}</td>
                                                        <td>{{ $post->category->name }}</td>
                                                        <td>{{ $post->published_at ? $post->published_at->format('Y-m-d') : 'غير منشور' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $post->status == 'published' ? 'success' : ($post->status == 'draft' ? 'secondary' : 'warning') }}">
                                                                {{ $post->status == 'published' ? 'منشور' : ($post->status == 'draft' ? 'مسودة' : 'قيد المراجعة') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.content.blog.edit', $post->id) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admin.content.blog.show', $post->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-danger delete-item" data-id="{{ $post->id }}" data-type="post">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">لا توجد مقالات</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- الأسئلة الشائعة -->
                            <div class="tab-pane fade" id="faq" role="tabpanel" aria-labelledby="faq-tab">
                                <div class="tab-header d-flex justify-content-between align-items-center my-3">
                                    <h5 class="mb-0">الأسئلة الشائعة</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                                        <i class="fas fa-plus"></i> إضافة سؤال جديد
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>السؤال</th>
                                                <th>التصنيف</th>
                                                <th>الترتيب</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($faqs) && count($faqs) > 0)
                                                @foreach($faqs as $faq)
                                                    <tr>
                                                        <td>{{ $faq->question }}</td>
                                                        <td>{{ $faq->category }}</td>
                                                        <td>{{ $faq->order }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $faq->is_active ? 'success' : 'secondary' }}">
                                                                {{ $faq->is_active ? 'نشط' : 'غير نشط' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info edit-faq" data-id="{{ $faq->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger delete-item" data-id="{{ $faq->id }}" data-type="faq">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">لا توجد أسئلة شائعة</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- آراء العملاء -->
                            <div class="tab-pane fade" id="testimonials" role="tabpanel" aria-labelledby="testimonials-tab">
                                <div class="tab-header d-flex justify-content-between align-items-center my-3">
                                    <h5 class="mb-0">آراء العملاء</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                                        <i class="fas fa-plus"></i> إضافة رأي جديد
                                    </button>
                                </div>
                                
                                <div class="testimonials-grid">
                                    @if(isset($testimonials) && count($testimonials) > 0)
                                        @foreach($testimonials as $testimonial)
                                            <div class="testimonial-card">
                                                <div class="testimonial-header">
                                                    <div class="testimonial-avatar">
                                                        <img src="{{ $testimonial->avatar ? asset($testimonial->avatar) : asset('assets/images/default-avatar.png') }}" alt="{{ $testimonial->name }}">
                                                    </div>
                                                    <div class="testimonial-info">
                                                        <h5 class="testimonial-name">{{ $testimonial->name }}</h5>
                                                        <div class="testimonial-rating">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <div class="testimonial-status">
                                                        <span class="badge bg-{{ $testimonial->is_active ? 'success' : 'secondary' }}">
                                                            {{ $testimonial->is_active ? 'نشط' : 'غير نشط' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="testimonial-content">
                                                    <p>{{ $testimonial->content }}</p>
                                                </div>
                                                <div class="testimonial-footer">
                                                    <div class="testimonial-date">
                                                        <i class="fas fa-calendar-alt"></i> {{ $testimonial->created_at->format('Y-m-d') }}
                                                    </div>
                                                    <div class="testimonial-actions">
                                                        <button class="btn btn-sm btn-info edit-testimonial" data-id="{{ $testimonial->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger delete-item" data-id="{{ $testimonial->id }}" data-type="testimonial">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-comment-dots"></i>
                                            </div>
                                            <h5>لا توجد آراء للعملاء</h5>
                                            <p>قم بإضافة آراء العملاء لعرضها في الموقع</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                                                <i class="fas fa-plus"></i> إضافة رأي جديد
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- البانرات الإعلانية -->
                            <div class="tab-pane fade" id="banners" role="tabpanel" aria-labelledby="banners-tab">
                                <div class="tab-header d-flex justify-content-between align-items-center my-3">
                                    <h5 class="mb-0">البانرات الإعلانية</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                                        <i class="fas fa-plus"></i> إضافة بانر جديد
                                    </button>
                                </div>
                                
                                <div class="banners-grid">
                                    @if(isset($banners) && count($banners) > 0)
                                        @foreach($banners as $banner)
                                            <div class="banner-card">
                                                <div class="banner-image">
                                                    <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}">
                                                    <div class="banner-overlay">
                                                        <div class="banner-actions">
                                                            <button class="btn btn-sm btn-info edit-banner" data-id="{{ $banner->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger delete-item" data-id="{{ $banner->id }}" data-type="banner">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="banner-content">
                                                    <h5 class="banner-title">{{ $banner->title }}</h5>
                                                    <div class="banner-details">
                                                        <div class="banner-location">
                                                            <span class="badge bg-info">{{ $banner->location }}</span>
                                                        </div>
                                                        <div class="banner-status">
                                                            <span class="badge bg-{{ $banner->is_active ? 'success' : 'secondary' }}">
                                                                {{ $banner->is_active ? 'نشط' : 'غير نشط' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="banner-dates">
                                                        <small>من: {{ $banner->start_date }} إلى: {{ $banner->end_date ?: 'غير محدد' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-images"></i>
                                            </div>
                                            <h5>لا توجد بانرات إعلانية</h5>
                                            <p>قم بإضافة بانرات إعلانية لعرضها في الموقع</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                                                <i class="fas fa-plus"></i> إضافة بانر جديد
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: إضافة صفحة جديدة -->
<div class="modal fade" id="addPageModal" tabindex="-1" aria-labelledby="addPageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPageModalLabel">إضافة صفحة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.content.pages.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="pageTitle">عنوان الصفحة</label>
                        <input type="text" class="form-control" id="pageTitle" name="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pageSlug">الرابط (Slug)</label>
                        <input type="text" class="form-control" id="pageSlug" name="slug" required>
                        <small class="form-text text-muted">مثال: about-us, terms-of-service</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pageContent">محتوى الصفحة</label>
                        <textarea class="form-control rich-editor" id="pageContent" name="content" rows="10" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pageMeta">وصف ميتا (Meta Description)</label>
                        <textarea class="form-control" id="pageMeta" name="meta_description" rows="2"></textarea>
                        <small class="form-text text-muted">وصف قصير للصفحة يظهر في نتائج البحث (150-160 حرف)</small>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="pageActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="pageActive">
                            نشر الصفحة
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: إضافة مقال جديد -->
<div class="modal fade" id="addPostModal" tabindex="-1" aria-labelledby="addPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPostModalLabel">إضافة مقال جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.content.blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="postTitle">عنوان المقال</label>
                        <input type="text" class="form-control" id="postTitle" name="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="postCategory">التصنيف</label>
                        <select class="form-control" id="postCategory" name="category_id" required>
                            <option value="">اختر التصنيف</option>
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="postImage">صورة المقال</label>
                        <input type="file" class="form-control" id="postImage" name="image" accept="image/*">
                    </div>
                    <div class="form-group mb-3">
                        <label for="postExcerpt">ملخص المقال</label>
                        <textarea class="form-control" id="postExcerpt" name="excerpt" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="postContent">محتوى المقال</label>
                        <textarea class="form-control rich-editor" id="postContent" name="content" rows="10" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="postTags">الوسوم (Tags)</label>
                        <input type="text" class="form-control" id="postTags" name="tags">
                        <small class="form-text text-muted">أدخل الوسوم مفصولة بفواصل</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="postStatus">الحالة</label>
                        <select class="form-control" id="postStatus" name="status" required>
                            <option value="draft">مسودة</option>
                            <option value="published">منشور</option>
                            <option value="pending">قيد المراجعة</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: إضافة سؤال شائع -->
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFaqModalLabel">إضافة سؤال شائع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.content.faq.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="faqQuestion">السؤال</label>
                        <input type="text" class="form-control" id="faqQuestion" name="question" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="faqAnswer">الإجابة</label>
                        <textarea class="form-control" id="faqAnswer" name="answer" rows="5" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="faqCategory">التصنيف</label>
                        <input type="text" class="form-control" id="faqCategory" name="category" required>
                        <small class="form-text text-muted">مثال: عام، الخدمات، الدفع</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="faqOrder">الترتيب</label>
                        <input type="number" class="form-control" id="faqOrder" name="order" value="0" min="0">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="faqActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="faqActive">
                            نشط
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: إضافة رأي عميل -->
<div class="modal fade" id="addTestimonialModal" tabindex="-1" aria-labelledby="addTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTestimonialModalLabel">إضافة رأي عميل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.content.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="testimonialName">اسم العميل</label>
                        <input type="text" class="form-control" id="testimonialName" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="testimonialAvatar">الصورة الشخصية</label>
                        <input type="file" class="form-control" id="testimonialAvatar" name="avatar" accept="image/*">
                    </div>
                    <div class="form-group mb-3">
                        <label for="testimonialContent">المحتوى</label>
                        <textarea class="form-control" id="testimonialContent" name="content" rows="4" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="testimonialRating">التقييم</label>
                        <select class="form-control" id="testimonialRating" name="rating" required>
                            <option value="5">5 نجوم</option>
                            <option value="4">4 نجوم</option>
                            <option value="3">3 نجوم</option>
                            <option value="2">2 نجوم</option>
                            <option value="1">1 نجمة</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="testimonialActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="testimonialActive">
                            نشط
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: إضافة بانر إعلاني -->
<div class="modal fade" id="addBannerModal" tabindex="-1" aria-labelledby="addBannerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBannerModalLabel">إضافة بانر إعلاني</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.content.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="bannerTitle">العنوان</label>
                        <input type="text" class="form-control" id="bannerTitle" name="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="bannerImage">الصورة</label>
                        <input type="file" class="form-control" id="bannerImage" name="image" accept="image/*" required>
                        <small class="form-text text-muted">الأبعاد الموصى بها: 1200×400 بكسل</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="bannerUrl">الرابط</label>
                        <input type="url" class="form-control" id="bannerUrl" name="url" placeholder="https://">
                    </div>
                    <div class="form-group mb-3">
                        <label for="bannerLocation">الموقع</label>
                        <select class="form-control" id="bannerLocation" name="location" required>
                            <option value="home_slider">سلايدر الصفحة الرئيسية</option>
                            <option value="home_middle">منتصف الصفحة الرئيسية</option>
                            <option value="sidebar">الشريط الجانبي</option>
                            <option value="services_top">أعلى صفحة الخدمات</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="bannerStartDate">تاريخ البداية</label>
                                <input type="date" class="form-control" id="bannerStartDate" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="bannerEndDate">تاريخ النهاية</label>
                                <input type="date" class="form-control" id="bannerEndDate" name="end_date">
                                <small class="form-text text-muted">اتركه فارغاً للعرض بشكل دائم</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="bannerActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="bannerActive">
                            نشط
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    .content-management-container {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
        padding: 10px 10px 0;
    }
    
    .nav-tabs .nav-link {
        border-radius: 5px 5px 0 0;
        font-weight: 500;
        color: #495057;
    }
    
    .nav-tabs .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        color: #6a1b9a;
    }
    
    .tab-content {
        padding: 20px;
    }
    
    .tab-header {
        margin-bottom: 20px;
    }
    
    .testimonials-grid, .banners-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .testimonial-card, .banner-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .testimonial-header {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .testimonial-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-left: 15px;
    }
    
    .testimonial-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .testimonial-info {
        flex: 1;
    }
    
    .testimonial-name {
        margin: 0;
        font-weight: 600;
        font-size: 16px;
    }
    
    .testimonial-rating {
        color: #ffc107;
        font-size: 14px;
    }
    
    .testimonial-content {
        padding: 15px;
        min-height: 100px;
    }
    
    .testimonial-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        background-color: #f9f9f9;
        border-top: 1px solid #f5f5f5;
    }
    
    .testimonial-date {
        font-size: 12px;
        color: #777;
    }
    
    .banner-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .banner-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .banner-image:hover .banner-overlay {
        opacity: 1;
    }
    
    .banner-content {
        padding: 15px;
    }
    
    .banner-title {
        margin: 0 0 10px;
        font-weight: 600;
    }
    
    .banner-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .banner-dates {
        font-size: 12px;
        color: #777;
    }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 50px 20px;
        text-align: center;
        color: #777;
    }
    
    .empty-icon {
        font-size: 48px;
        margin-bottom: 15px;
        color: #ccc;
    }
    
    @media (max-width: 767px) {
        .testimonials-grid, .banners-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        // تهيئة محرر النصوص الغني
        $('.rich-editor').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            lang: 'ar-AR',
            direction: 'rtl'
        });
        
        // توليد الرابط (Slug) تلقائياً من العنوان
        $('#pageTitle').on('keyup', function() {
            var title = $(this).val();
            var slug = title.toLowerCase()
                .replace(/\s+/g, '-')           // استبدال المسافات بشرطات
                .replace(/[^\u0621-\u064A\u0660-\u0669a-z0-9-]/g, '') // إزالة الأحرف غير العربية والإنجليزية والأرقام والشرطات
                .replace(/-+/g, '-');          // استبدال الشرطات المتعددة بشرطة واحدة
            
            $('#pageSlug').val(slug);
        });
        
        // حذف عنصر
        $('.delete-item').on('click', function() {
            var id = $(this).data('id');
            var type = $(this).data('type');
            var typeText = '';
            
            switch(type) {
                case 'page':
                    typeText = 'الصفحة';
                    break;
                case 'post':
                    typeText = 'المقال';
                    break;
                case 'faq':
                    typeText = 'السؤال';
                    break;
                case 'testimonial':
                    typeText = 'رأي العميل';
                    break;
                case 'banner':
                    typeText = 'البانر';
                    break;
                default:
                    typeText = 'العنصر';
            }
            
            if (confirm('هل أنت متأكد من حذف ' + typeText + '؟')) {
                // هنا يمكن إضافة طلب AJAX لحذف العنصر
                alert('تم حذف ' + typeText + ' بنجاح');
            }
        });
        
        // تعديل سؤال شائع
        $('.edit-faq').on('click', function() {
            var id = $(this).data('id');
            // هنا يمكن إضافة طلب AJAX لجلب بيانات السؤال وعرضها في نافذة التعديل
            alert('تعديل السؤال رقم ' + id);
        });
        
        // تعديل رأي عميل
        $('.edit-testimonial').on('click', function() {
            var id = $(this).data('id');
            // هنا يمكن إضافة طلب AJAX لجلب بيانات الرأي وعرضها في نافذة التعديل
            alert('تعديل رأي العميل رقم ' + id);
        });
        
        // تعديل بانر
        $('.edit-banner').on('click', function() {
            var id = $(this).data('id');
            // هنا يمكن إضافة طلب AJAX لجلب بيانات البانر وعرضها في نافذة التعديل
            alert('تعديل البانر رقم ' + id);
        });
    });
</script>
@endsection
