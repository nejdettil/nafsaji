@extends('layouts.app')

@section('title', 'المدونة - نفسجي للتمكين النفسي')

@section('content')
<div class="blog-page">
    <!-- قسم الترويسة -->
    <section class="blog-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1 class="main-title">المدونة</h1>
                        <p class="lead">مقالات ومحتوى توعوي في مجال الصحة النفسية</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item active" aria-current="page">المدونة</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم البحث والتصفية -->
    <section class="blog-search">
        <div class="container">
            <div class="search-filter-card">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('blog.index') }}" method="GET" class="search-form">
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="search">ابحث في المدونة</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="search" name="search" placeholder="ابحث عن مقالات..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="category">التصنيف</label>
                                        <select class="form-select" id="category" name="category">
                                            <option value="">جميع التصنيفات</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="sort">الترتيب</label>
                                        <select class="form-select" id="sort" name="sort">
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>الأحدث أولاً</option>
                                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم أولاً</option>
                                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر قراءة</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="tag">الوسوم</label>
                                        <select class="form-select" id="tag" name="tag">
                                            <option value="">جميع الوسوم</option>
                                            @foreach($tags as $tag)
                                                <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم المقالات المميزة -->
    @if(isset($featuredPosts) && count($featuredPosts) > 0 && !request('search') && !request('category') && !request('tag'))
    <section class="featured-posts">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">مقالات مميزة</h2>
                <div class="section-divider"></div>
            </div>
            <div class="featured-posts-slider">
                <div class="row">
                    @foreach($featuredPosts as $post)
                    <div class="col-lg-4">
                        <div class="featured-post-card">
                            <div class="card">
                                <div class="post-image">
                                    <a href="{{ route('blog.show', $post->slug) }}">
                                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="card-img-top">
                                        <div class="post-category">
                                            <span>{{ $post->category->name }}</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>
                                    <div class="post-meta">
                                        <div class="post-author">
                                            <img src="{{ $post->author->profile_photo_url }}" alt="{{ $post->author->name }}" class="author-image">
                                            <span>{{ $post->author->name }}</span>
                                        </div>
                                        <div class="post-date">
                                            <i class="far fa-calendar-alt"></i>
                                            <span>{{ $post->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <p class="card-text">{{ Str::limit($post->excerpt, 120) }}</p>
                                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-long-arrow-alt-left"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- قسم المقالات الحديثة -->
    <section class="blog-posts">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    @if(request('search'))
                        نتائج البحث عن: "{{ request('search') }}"
                    @elseif(request('category'))
                        مقالات في تصنيف: {{ $categoryName }}
                    @elseif(request('tag'))
                        مقالات بوسم: {{ $tagName }}
                    @else
                        أحدث المقالات
                    @endif
                </h2>
                <div class="section-divider"></div>
            </div>

            @if($posts->count() > 0)
            <div class="row">
                @foreach($posts as $post)
                <div class="col-lg-4 col-md-6">
                    <div class="post-card">
                        <div class="card">
                            <div class="post-image">
                                <a href="{{ route('blog.show', $post->slug) }}">
                                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="card-img-top">
                                    <div class="post-category">
                                        <span>{{ $post->category->name }}</span>
                                    </div>
                                </a>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <div class="post-meta">
                                    <div class="post-author">
                                        <img src="{{ $post->author->profile_photo_url }}" alt="{{ $post->author->name }}" class="author-image">
                                        <span>{{ $post->author->name }}</span>
                                    </div>
                                    <div class="post-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <span>{{ $post->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <p class="card-text">{{ Str::limit($post->excerpt, 120) }}</p>
                                <div class="post-footer">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-long-arrow-alt-left"></i></a>
                                    <div class="post-stats">
                                        <span class="post-views" title="عدد المشاهدات">
                                            <i class="far fa-eye"></i> {{ $post->views_count }}
                                        </span>
                                        <span class="post-comments" title="عدد التعليقات">
                                            <i class="far fa-comment"></i> {{ $post->comments_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pagination-container">
                {{ $posts->appends(request()->query())->links() }}
            </div>
            @else
            <div class="no-posts">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>لا توجد مقالات متاحة حالياً. يرجى المحاولة مرة أخرى لاحقاً أو تغيير معايير البحث.</span>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- قسم التصنيفات -->
    <section class="blog-categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">تصفح حسب التصنيف</h2>
                <div class="section-divider"></div>
            </div>
            <div class="row">
                @foreach($categories as $category)
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="{{ route('blog.index', ['category' => $category->id]) }}" class="category-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="category-icon">
                                    <i class="{{ $category->icon }}"></i>
                                </div>
                                <h3 class="category-name">{{ $category->name }}</h3>
                                <div class="category-count">{{ $category->posts_count }} مقال</div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- قسم الوسوم -->
    <section class="blog-tags">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">الوسوم الشائعة</h2>
                <div class="section-divider"></div>
            </div>
            <div class="tags-cloud">
                @foreach($popularTags as $tag)
                <a href="{{ route('blog.index', ['tag' => $tag->id]) }}" class="tag-item" style="font-size: {{ 14 + ($tag->posts_count / 2) }}px;">
                    {{ $tag->name }}
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- قسم الاشتراك في النشرة البريدية -->
    <section class="blog-newsletter">
        <div class="container">
            <div class="newsletter-card">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="newsletter-content">
                            <h2>اشترك في نشرتنا البريدية</h2>
                            <p>احصل على أحدث المقالات والنصائح في مجال الصحة النفسية مباشرة إلى بريدك الإلكتروني.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                            @csrf
                            <div class="input-group">
                                <input type="email" class="form-control" name="email" placeholder="أدخل بريدك الإلكتروني" required>
                                <button type="submit" class="btn btn-primary">اشترك الآن</button>
                            </div>
                            @if(session('newsletter_success'))
                                <div class="alert alert-success mt-2">
                                    {{ session('newsletter_success') }}
                                </div>
                            @endif
                            @error('email')
                                <div class="text-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </form>
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
    .blog-page section {
        padding: 40px 0;
    }
    
    .section-header {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .section-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
    }
    
    .section-divider {
        width: 60px;
        height: 3px;
        background-color: #6a1b9a;
        margin: 0 auto 15px;
    }
    
    /* قسم الترويسة */
    .blog-header {
        background-color: #f8f9fa;
        padding: 60px 0;
        text-align: center;
    }
    
    .main-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }
    
    .lead {
        font-size: 18px;
        color: #555;
        margin-bottom: 20px;
    }
    
    /* قسم البحث والتصفية */
    .blog-search {
        margin-top: -20px;
        margin-bottom: 20px;
    }
    
    .search-filter-card {
        margin-bottom: 30px;
    }
    
    .search-filter-card .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .search-filter-card .card-body {
        padding: 20px;
    }
    
    .search-form .form-group {
        margin-bottom: 15px;
    }
    
    .search-form label {
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    /* قسم المقالات المميزة */
    .featured-posts {
        background-color: #f8f9fa;
        padding: 50px 0;
    }
    
    .featured-post-card {
        margin-bottom: 30px;
    }
    
    .featured-post-card .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .featured-post-card .card:hover {
        transform: translateY(-5px);
    }
    
    .post-image {
        position: relative;
        overflow: hidden;
    }
    
    .post-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .post-image:hover img {
        transform: scale(1.05);
    }
    
    .post-category {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: #6a1b9a;
        color: #fff;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .card-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .card-title a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .card-title a:hover {
        color: #6a1b9a;
    }
    
    .post-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 12px;
        color: #666;
    }
    
    .post-author {
        display: flex;
        align-items: center;
    }
    
    .author-image {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        margin-left: 5px;
    }
    
    .post-date {
        display: flex;
        align-items: center;
    }
    
    .post-date i {
        margin-left: 5px;
    }
    
    .card-text {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }
    
    .btn-link {
        color: #6a1b9a;
        padding: 0;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    
    .btn-link:hover {
        color: #4a148c;
    }
    
    .btn-link i {
        margin-right: 5px;
    }
    
    /* قسم المقالات الحديثة */
    .blog-posts {
        padding: 50px 0;
    }
    
    .post-card {
        margin-bottom: 30px;
    }
    
    .post-card .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
    }
    
    .post-card .card:hover {
        transform: translateY(-5px);
    }
    
    .post-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
    }
    
    .post-stats {
        display: flex;
        align-items: center;
    }
    
    .post-views, .post-comments {
        display: flex;
        align-items: center;
        margin-right: 10px;
        font-size: 12px;
        color: #666;
    }
    
    .post-views i, .post-comments i {
        margin-left: 5px;
    }
    
    .pagination-container {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }
    
    .no-posts {
        margin-top: 20px;
    }
    
    /* قسم التصنيفات */
    .blog-categories {
        background-color: #f8f9fa;
        padding: 50px 0;
    }
    
    .category-card {
        display: block;
        margin-bottom: 20px;
        text-decoration: none;
    }
    
    .category-card .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
        text-align: center;
        height: 100%;
    }
    
    .category-card .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .category-icon {
        font-size: 30px;
        color: #6a1b9a;
        margin-bottom: 10px;
    }
    
    .category-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .category-count {
        font-size: 12px;
        color: #666;
    }
    
    /* قسم الوسوم */
    .blog-tags {
        padding: 50px 0;
    }
    
    .tags-cloud {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .tag-item {
        display: inline-block;
        background-color: #f0e6f5;
        color: #6a1b9a;
        padding: 5px 15px;
        border-radius: 20px;
        margin: 5px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .tag-item:hover {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    /* قسم الاشتراك في النشرة البريدية */
    .blog-newsletter {
        background-color: #f8f9fa;
        padding: 50px 0;
    }
    
    .newsletter-card {
        background-color: #6a1b9a;
        color: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(106, 27, 154, 0.3);
    }
    
    .newsletter-content h2 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .newsletter-content p {
        opacity: 0.9;
        margin-bottom: 0;
    }
    
    .newsletter-form {
        margin-top: 15px;
    }
    
    .newsletter-form .form-control {
        height: 50px;
        border-radius: 25px 0 0 25px;
        border: none;
        padding: 0 20px;
    }
    
    .newsletter-form .btn {
        border-radius: 0 25px 25px 0;
        height: 50px;
        padding: 0 25px;
        background-color: #4a148c;
        border-color: #4a148c;
    }
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .newsletter-content {
            margin-bottom: 20px;
            text-align: center;
        }
    }
    
    @media (max-width: 767px) {
        .blog-page section {
            padding: 30px 0;
        }
        
        .main-title {
            font-size: 28px;
        }
        
        .section-title {
            font-size: 24px;
        }
        
        .post-meta {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .post-date {
            margin-top: 5px;
        }
        
        .post-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .post-stats {
            margin-top: 10px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديث الصفحة عند تغيير التصنيف أو الترتيب أو الوسم
        $('#category, #sort, #tag').on('change', function() {
            $('.search-form').submit();
        });
        
        // تهيئة السلايدر للمقالات المميزة
        $('.featured-posts-slider').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
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
    });
</script>
@endsection
