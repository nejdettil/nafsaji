@extends('layouts.app')

@section('title', 'تصنيفات المدونة - نفسجي للتمكين النفسي')

@section('content')
<div class="categories-page">
    <!-- قسم الترويسة -->
    <section class="categories-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1 class="main-title">تصنيفات المدونة</h1>
                        <p class="lead">استكشف مقالاتنا حسب التصنيفات المختلفة</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">المدونة</a></li>
                                <li class="breadcrumb-item active" aria-current="page">التصنيفات</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم التصنيفات الرئيسية -->
    <section class="main-categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">التصنيفات الرئيسية</h2>
                <div class="section-divider"></div>
                <p class="section-description">تصفح مقالاتنا حسب التصنيفات المختلفة في مجال الصحة النفسية والتمكين النفسي</p>
            </div>
            
            <div class="row">
                @foreach($mainCategories as $category)
                <div class="col-lg-4 col-md-6">
                    <div class="category-card">
                        <div class="card">
                            <div class="category-icon">
                                <i class="{{ $category->icon }}"></i>
                            </div>
                            <div class="card-body">
                                <h3 class="category-name">{{ $category->name }}</h3>
                                <p class="category-description">{{ $category->description }}</p>
                                <div class="category-stats">
                                    <div class="posts-count">
                                        <i class="far fa-file-alt"></i>
                                        <span>{{ $category->posts_count }} مقال</span>
                                    </div>
                                    <div class="views-count">
                                        <i class="far fa-eye"></i>
                                        <span>{{ $category->views_count }} مشاهدة</span>
                                    </div>
                                </div>
                                <a href="{{ route('blog.index', ['category' => $category->id]) }}" class="btn btn-primary">تصفح المقالات</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- قسم جميع التصنيفات -->
    <section class="all-categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">جميع التصنيفات</h2>
                <div class="section-divider"></div>
            </div>
            
            <div class="categories-filter">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <div class="search-box">
                            <form action="{{ route('blog.categories') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="ابحث عن تصنيف..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="filter-tabs">
                    <ul class="nav nav-pills" id="categoriesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">الكل</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="popular-tab" data-bs-toggle="pill" data-bs-target="#popular" type="button" role="tab" aria-controls="popular" aria-selected="false">الأكثر شعبية</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="recent-tab" data-bs-toggle="pill" data-bs-target="#recent" type="button" role="tab" aria-controls="recent" aria-selected="false">الأحدث</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="alphabetical-tab" data-bs-toggle="pill" data-bs-target="#alphabetical" type="button" role="tab" aria-controls="alphabetical" aria-selected="false">أبجدي</button>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="tab-content" id="categoriesTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="row">
                        @foreach($categories as $category)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('blog.index', ['category' => $category->id]) }}" class="category-item">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="category-icon">
                                            <i class="{{ $category->icon }}"></i>
                                        </div>
                                        <h4 class="category-name">{{ $category->name }}</h4>
                                        <div class="category-count">{{ $category->posts_count }} مقال</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="pagination-container">
                        {{ $categories->appends(request()->query())->links() }}
                    </div>
                </div>
                
                <div class="tab-pane fade" id="popular" role="tabpanel" aria-labelledby="popular-tab">
                    <div class="row">
                        @foreach($popularCategories as $category)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('blog.index', ['category' => $category->id]) }}" class="category-item">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="category-icon">
                                            <i class="{{ $category->icon }}"></i>
                                        </div>
                                        <h4 class="category-name">{{ $category->name }}</h4>
                                        <div class="category-count">{{ $category->posts_count }} مقال</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="tab-pane fade" id="recent" role="tabpanel" aria-labelledby="recent-tab">
                    <div class="row">
                        @foreach($recentCategories as $category)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('blog.index', ['category' => $category->id]) }}" class="category-item">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="category-icon">
                                            <i class="{{ $category->icon }}"></i>
                                        </div>
                                        <h4 class="category-name">{{ $category->name }}</h4>
                                        <div class="category-count">{{ $category->posts_count }} مقال</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="tab-pane fade" id="alphabetical" role="tabpanel" aria-labelledby="alphabetical-tab">
                    <div class="row">
                        @foreach($alphabeticalCategories as $category)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('blog.index', ['category' => $category->id]) }}" class="category-item">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="category-icon">
                                            <i class="{{ $category->icon }}"></i>
                                        </div>
                                        <h4 class="category-name">{{ $category->name }}</h4>
                                        <div class="category-count">{{ $category->posts_count }} مقال</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الوسوم الشائعة -->
    <section class="popular-tags">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">الوسوم الشائعة</h2>
                <div class="section-divider"></div>
                <p class="section-description">استكشف المقالات حسب الوسوم الأكثر استخداماً</p>
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

    <!-- قسم إحصائيات المدونة -->
    <section class="blog-stats">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">إحصائيات المدونة</h2>
                <div class="section-divider"></div>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $totalPosts }}</div>
                            <div class="stat-label">إجمالي المقالات</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $totalCategories }}</div>
                            <div class="stat-label">إجمالي التصنيفات</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $totalTags }}</div>
                            <div class="stat-label">إجمالي الوسوم</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $totalViews }}</div>
                            <div class="stat-label">إجمالي المشاهدات</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الاشتراك في النشرة البريدية -->
    <section class="newsletter-section">
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
    .categories-page section {
        padding: 50px 0;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 40px;
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
    
    .section-description {
        max-width: 700px;
        margin: 0 auto;
        color: #555;
    }
    
    /* قسم الترويسة */
    .categories-header {
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
    
    .breadcrumb {
        justify-content: center;
        background-color: transparent;
        padding: 0;
        margin-bottom: 0;
    }
    
    .breadcrumb-item a {
        color: #6a1b9a;
        text-decoration: none;
    }
    
    .breadcrumb-item.active {
        color: #666;
    }
    
    /* قسم التصنيفات الرئيسية */
    .main-categories {
        padding: 60px 0;
    }
    
    .category-card {
        margin-bottom: 30px;
    }
    
    .category-card .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
        text-align: center;
    }
    
    .category-card .card:hover {
        transform: translateY(-5px);
    }
    
    .category-icon {
        font-size: 50px;
        color: #6a1b9a;
        margin: 20px 0;
    }
    
    .category-name {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
    }
    
    .category-description {
        font-size: 14px;
        color: #555;
        margin-bottom: 20px;
    }
    
    .category-stats {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .posts-count, .views-count {
        display: flex;
        align-items: center;
        margin: 0 10px;
        font-size: 14px;
        color: #666;
    }
    
    .posts-count i, .views-count i {
        margin-left: 5px;
        color: #6a1b9a;
    }
    
    /* قسم جميع التصنيفات */
    .all-categories {
        background-color: #f8f9fa;
        padding: 60px 0;
    }
    
    .categories-filter {
        margin-bottom: 30px;
    }
    
    .search-box {
        margin-bottom: 20px;
    }
    
    .filter-tabs {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }
    
    .filter-tabs .nav-pills {
        background-color: #fff;
        border-radius: 50px;
        padding: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .filter-tabs .nav-link {
        border-radius: 50px;
        padding: 8px 20px;
        margin: 0 5px;
        color: #333;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .filter-tabs .nav-link.active {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    .category-item {
        display: block;
        margin-bottom: 20px;
        text-decoration: none;
    }
    
    .category-item .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        text-align: center;
        height: 100%;
    }
    
    .category-item .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .category-item .category-icon {
        font-size: 30px;
        margin: 15px 0;
    }
    
    .category-item .category-name {
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .category-item .category-count {
        font-size: 12px;
        color: #666;
    }
    
    .pagination-container {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }
    
    /* قسم الوسوم الشائعة */
    .popular-tags {
        padding: 60px 0;
    }
    
    .tags-cloud {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 30px;
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
    
    /* قسم إحصائيات المدونة */
    .blog-stats {
        background-color: #f8f9fa;
        padding: 60px 0;
    }
    
    .stat-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        text-align: center;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 40px;
        color: #6a1b9a;
        margin-bottom: 15px;
    }
    
    .stat-value {
        font-size: 30px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
    }
    
    /* قسم الاشتراك في النشرة البريدية */
    .newsletter-section {
        padding: 60px 0;
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
        .categories-page section {
            padding: 40px 0;
        }
        
        .main-title {
            font-size: 28px;
        }
        
        .section-title {
            font-size: 24px;
        }
        
        .filter-tabs .nav-link {
            padding: 6px 12px;
            font-size: 14px;
        }
        
        .stat-card {
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديث الصفحة عند تغيير التصنيف
        $('#categoriesTab button').on('click', function() {
            const tabId = $(this).attr('id');
            const filter = tabId.replace('-tab', '');
            
            // تحديث URL بدون إعادة تحميل الصفحة
            const url = new URL(window.location);
            url.searchParams.set('filter', filter);
            window.history.pushState({}, '', url);
        });
        
        // تهيئة التبويبات بناءً على المعلمة في URL
        const urlParams = new URLSearchParams(window.location.search);
        const filter = urlParams.get('filter');
        
        if (filter) {
            $(`#${filter}-tab`).tab('show');
        }
    });
</script>
@endsection
