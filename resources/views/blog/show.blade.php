@extends('layouts.app')

@section('title', '{{ $post->title }} - نفسجي للتمكين النفسي')

@section('meta')
<meta name="description" content="{{ Str::limit(strip_tags($post->excerpt), 160) }}">
<meta property="og:title" content="{{ $post->title }} - نفسجي للتمكين النفسي">
<meta property="og:description" content="{{ Str::limit(strip_tags($post->excerpt), 160) }}">
<meta property="og:image" content="{{ $post->featured_image }}">
<meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
<meta property="og:type" content="article">
<meta property="article:published_time" content="{{ $post->created_at->toIso8601String() }}">
<meta property="article:author" content="{{ $post->author->name }}">
@foreach($post->tags as $tag)
<meta property="article:tag" content="{{ $tag->name }}">
@endforeach
@endsection

@section('content')
<div class="blog-post-page">
    <!-- قسم الترويسة -->
    <section class="post-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">المدونة</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('blog.index', ['category' => $post->category->id]) }}">{{ $post->category->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
                            </ol>
                        </nav>
                        <div class="post-category">
                            <a href="{{ route('blog.index', ['category' => $post->category->id]) }}">{{ $post->category->name }}</a>
                        </div>
                        <h1 class="post-title">{{ $post->title }}</h1>
                        <div class="post-meta">
                            <div class="post-author">
                                <img src="{{ $post->author->profile_photo_url }}" alt="{{ $post->author->name }}" class="author-image">
                                <div class="author-info">
                                    <span class="author-name">{{ $post->author->name }}</span>
                                    <span class="author-title">{{ $post->author->title }}</span>
                                </div>
                            </div>
                            <div class="post-details">
                                <div class="post-date">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ $post->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="post-reading-time">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $post->reading_time }} دقيقة قراءة</span>
                                </div>
                                <div class="post-views">
                                    <i class="far fa-eye"></i>
                                    <span>{{ $post->views_count }} مشاهدة</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم صورة المقال -->
    <section class="post-featured-image">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="featured-image">
                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="img-fluid rounded">
                        @if($post->image_caption)
                            <div class="image-caption">{{ $post->image_caption }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم محتوى المقال -->
    <section class="post-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="content-wrapper">
                        <div class="post-excerpt">
                            {{ $post->excerpt }}
                        </div>
                        
                        <div class="post-body">
                            {!! $post->content !!}
                        </div>
                        
                        <div class="post-tags">
                            <h4>الوسوم:</h4>
                            <div class="tags-list">
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('blog.index', ['tag' => $tag->id]) }}" class="tag-item">{{ $tag->name }}</a>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="post-share">
                            <h4>شارك المقال:</h4>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" class="share-button facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-button twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . route('blog.show', $post->slug)) }}" target="_blank" class="share-button whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-button telegram">
                                    <i class="fab fa-telegram-plane"></i>
                                </a>
                                <a href="mailto:?subject={{ urlencode($post->title) }}&body={{ urlencode('اقرأ هذا المقال المفيد: ' . route('blog.show', $post->slug)) }}" class="share-button email">
                                    <i class="far fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="post-author-bio">
                            <div class="author-image">
                                <img src="{{ $post->author->profile_photo_url }}" alt="{{ $post->author->name }}" class="img-fluid rounded-circle">
                            </div>
                            <div class="author-info">
                                <h4 class="author-name">{{ $post->author->name }}</h4>
                                <p class="author-title">{{ $post->author->title }}</p>
                                <p class="author-bio">{{ $post->author->bio }}</p>
                                <div class="author-social">
                                    @if($post->author->facebook_url)
                                        <a href="{{ $post->author->facebook_url }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                    @endif
                                    @if($post->author->twitter_url)
                                        <a href="{{ $post->author->twitter_url }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                    @endif
                                    @if($post->author->instagram_url)
                                        <a href="{{ $post->author->instagram_url }}" target="_blank"><i class="fab fa-instagram"></i></a>
                                    @endif
                                    @if($post->author->linkedin_url)
                                        <a href="{{ $post->author->linkedin_url }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="post-navigation">
                            <div class="row">
                                @if($previousPost)
                                    <div class="col-md-6">
                                        <a href="{{ route('blog.show', $previousPost->slug) }}" class="post-nav-link prev">
                                            <div class="post-nav-arrow">
                                                <i class="fas fa-arrow-right"></i>
                                                <span>المقال السابق</span>
                                            </div>
                                            <div class="post-nav-title">{{ $previousPost->title }}</div>
                                        </a>
                                    </div>
                                @endif
                                
                                @if($nextPost)
                                    <div class="col-md-6">
                                        <a href="{{ route('blog.show', $nextPost->slug) }}" class="post-nav-link next">
                                            <div class="post-nav-arrow">
                                                <span>المقال التالي</span>
                                                <i class="fas fa-arrow-left"></i>
                                            </div>
                                            <div class="post-nav-title">{{ $nextPost->title }}</div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- قسم التعليقات -->
                    <div class="post-comments">
                        <div class="comments-header">
                            <h3>التعليقات ({{ $comments->count() }})</h3>
                        </div>
                        
                        @if($comments->count() > 0)
                            <div class="comments-list">
                                @foreach($comments as $comment)
                                    <div class="comment-item" id="comment-{{ $comment->id }}">
                                        <div class="comment-avatar">
                                            <img src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <h4 class="comment-author">{{ $comment->user->name }}</h4>
                                                <div class="comment-date">
                                                    <i class="far fa-clock"></i>
                                                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div class="comment-body">
                                                <p>{{ $comment->content }}</p>
                                            </div>
                                            <div class="comment-actions">
                                                <button class="btn btn-sm btn-link reply-btn" data-comment-id="{{ $comment->id }}">
                                                    <i class="far fa-comment"></i> رد
                                                </button>
                                                <button class="btn btn-sm btn-link like-btn {{ $comment->is_liked ? 'active' : '' }}" data-comment-id="{{ $comment->id }}">
                                                    <i class="far fa-thumbs-up"></i> إعجاب ({{ $comment->likes_count }})
                                                </button>
                                                @if(auth()->check() && (auth()->id() == $comment->user_id || auth()->user()->hasRole('admin')))
                                                    <button class="btn btn-sm btn-link edit-btn" data-comment-id="{{ $comment->id }}">
                                                        <i class="far fa-edit"></i> تعديل
                                                    </button>
                                                    <form action="{{ route('blog.comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا التعليق؟')">
                                                            <i class="far fa-trash-alt"></i> حذف
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            
                                            <!-- نموذج الرد على التعليق -->
                                            <div class="reply-form-container" id="reply-form-{{ $comment->id }}" style="display: none;">
                                                <form action="{{ route('blog.comments.reply', $comment->id) }}" method="POST" class="reply-form">
                                                    @csrf
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="content" rows="3" placeholder="اكتب ردك هنا..." required></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary">إرسال الرد</button>
                                                        <button type="button" class="btn btn-secondary cancel-reply" data-comment-id="{{ $comment->id }}">إلغاء</button>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <!-- نموذج تعديل التعليق -->
                                            @if(auth()->check() && (auth()->id() == $comment->user_id || auth()->user()->hasRole('admin')))
                                                <div class="edit-form-container" id="edit-form-{{ $comment->id }}" style="display: none;">
                                                    <form action="{{ route('blog.comments.update', $comment->id) }}" method="POST" class="edit-form">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <textarea class="form-control" name="content" rows="3" required>{{ $comment->content }}</textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-primary">حفظ التعديل</button>
                                                            <button type="button" class="btn btn-secondary cancel-edit" data-comment-id="{{ $comment->id }}">إلغاء</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                            
                                            <!-- الردود على التعليق -->
                                            @if($comment->replies->count() > 0)
                                                <div class="comment-replies">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="reply-item" id="reply-{{ $reply->id }}">
                                                            <div class="reply-avatar">
                                                                <img src="{{ $reply->user->profile_photo_url }}" alt="{{ $reply->user->name }}" class="img-fluid rounded-circle">
                                                            </div>
                                                            <div class="reply-content">
                                                                <div class="reply-header">
                                                                    <h5 class="reply-author">{{ $reply->user->name }}</h5>
                                                                    <div class="reply-date">
                                                                        <i class="far fa-clock"></i>
                                                                        <span>{{ $reply->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="reply-body">
                                                                    <p>{{ $reply->content }}</p>
                                                                </div>
                                                                <div class="reply-actions">
                                                                    <button class="btn btn-sm btn-link like-btn {{ $reply->is_liked ? 'active' : '' }}" data-reply-id="{{ $reply->id }}">
                                                                        <i class="far fa-thumbs-up"></i> إعجاب ({{ $reply->likes_count }})
                                                                    </button>
                                                                    @if(auth()->check() && (auth()->id() == $reply->user_id || auth()->user()->hasRole('admin')))
                                                                        <button class="btn btn-sm btn-link edit-reply-btn" data-reply-id="{{ $reply->id }}">
                                                                            <i class="far fa-edit"></i> تعديل
                                                                        </button>
                                                                        <form action="{{ route('blog.replies.destroy', $reply->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا الرد؟')">
                                                                                <i class="far fa-trash-alt"></i> حذف
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                                
                                                                <!-- نموذج تعديل الرد -->
                                                                @if(auth()->check() && (auth()->id() == $reply->user_id || auth()->user()->hasRole('admin')))
                                                                    <div class="edit-reply-form-container" id="edit-reply-form-{{ $reply->id }}" style="display: none;">
                                                                        <form action="{{ route('blog.replies.update', $reply->id) }}" method="POST" class="edit-reply-form">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="form-group">
                                                                                <textarea class="form-control" name="content" rows="2" required>{{ $reply->content }}</textarea>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <button type="submit" class="btn btn-primary">حفظ التعديل</button>
                                                                                <button type="button" class="btn btn-secondary cancel-edit-reply" data-reply-id="{{ $reply->id }}">إلغاء</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="comments-pagination">
                                {{ $comments->links() }}
                            </div>
                        @else
                            <div class="no-comments">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <span>لا توجد تعليقات على هذا المقال حتى الآن. كن أول من يعلق!</span>
                                </div>
                            </div>
                        @endif
                        
                        <!-- نموذج إضافة تعليق جديد -->
                        <div class="add-comment">
                            <h3>أضف تعليقك</h3>
                            @if(auth()->check())
                                <form action="{{ route('blog.comments.store', $post->id) }}" method="POST" class="comment-form">
                                    @csrf
                                    <div class="form-group">
                                        <textarea class="form-control" name="content" rows="4" placeholder="اكتب تعليقك هنا..." required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">إرسال التعليق</button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <span>يجب <a href="{{ route('login') }}">تسجيل الدخول</a> لتتمكن من إضافة تعليق.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="sidebar">
                        <!-- بحث في المدونة -->
                        <div class="sidebar-widget search-widget">
                            <div class="widget-title">
                                <h3>بحث</h3>
                            </div>
                            <div class="widget-content">
                                <form action="{{ route('blog.index') }}" method="GET" class="search-form">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" placeholder="ابحث في المدونة..." required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- معلومات الكاتب -->
                        <div class="sidebar-widget author-widget">
                            <div class="widget-title">
                                <h3>الكاتب</h3>
                            </div>
                            <div class="widget-content">
                                <div class="author-card">
                                    <div class="author-image">
                                        <img src="{{ $post->author->profile_photo_url }}" alt="{{ $post->author->name }}" class="img-fluid rounded-circle">
                                    </div>
                                    <h4 class="author-name">{{ $post->author->name }}</h4>
                                    <p class="author-title">{{ $post->author->title }}</p>
                                    <p class="author-bio">{{ Str::limit($post->author->bio, 100) }}</p>
                                    <div class="author-social">
                                        @if($post->author->facebook_url)
                                            <a href="{{ $post->author->facebook_url }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                        @endif
                                        @if($post->author->twitter_url)
                                            <a href="{{ $post->author->twitter_url }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                        @endif
                                        @if($post->author->instagram_url)
                                            <a href="{{ $post->author->instagram_url }}" target="_blank"><i class="fab fa-instagram"></i></a>
                                        @endif
                                        @if($post->author->linkedin_url)
                                            <a href="{{ $post->author->linkedin_url }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                        @endif
                                    </div>
                                    <a href="{{ route('blog.author', $post->author->id) }}" class="btn btn-outline-primary btn-sm">عرض جميع مقالات الكاتب</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- مقالات ذات صلة -->
                        <div class="sidebar-widget related-posts-widget">
                            <div class="widget-title">
                                <h3>مقالات ذات صلة</h3>
                            </div>
                            <div class="widget-content">
                                @if($relatedPosts->count() > 0)
                                    <div class="related-posts-list">
                                        @foreach($relatedPosts as $relatedPost)
                                            <div class="related-post-item">
                                                <div class="post-image">
                                                    <a href="{{ route('blog.show', $relatedPost->slug) }}">
                                                        <img src="{{ $relatedPost->featured_image }}" alt="{{ $relatedPost->title }}" class="img-fluid">
                                                    </a>
                                                </div>
                                                <div class="post-info">
                                                    <h4 class="post-title">
                                                        <a href="{{ route('blog.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                                                    </h4>
                                                    <div class="post-date">
                                                        <i class="far fa-calendar-alt"></i>
                                                        <span>{{ $relatedPost->created_at->format('d M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <span>لا توجد مقالات ذات صلة حالياً.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- التصنيفات -->
                        <div class="sidebar-widget categories-widget">
                            <div class="widget-title">
                                <h3>التصنيفات</h3>
                            </div>
                            <div class="widget-content">
                                <ul class="categories-list">
                                    @foreach($categories as $category)
                                        <li class="category-item {{ $post->category_id == $category->id ? 'active' : '' }}">
                                            <a href="{{ route('blog.index', ['category' => $category->id]) }}">
                                                <span class="category-name">{{ $category->name }}</span>
                                                <span class="category-count">{{ $category->posts_count }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        
                        <!-- الوسوم -->
                        <div class="sidebar-widget tags-widget">
                            <div class="widget-title">
                                <h3>الوسوم</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tags-cloud">
                                    @foreach($tags as $tag)
                                        <a href="{{ route('blog.index', ['tag' => $tag->id]) }}" class="tag-item {{ $post->tags->contains('id', $tag->id) ? 'active' : '' }}">
                                            {{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- الاشتراك في النشرة البريدية -->
                        <div class="sidebar-widget newsletter-widget">
                            <div class="widget-title">
                                <h3>النشرة البريدية</h3>
                            </div>
                            <div class="widget-content">
                                <p>اشترك في نشرتنا البريدية للحصول على أحدث المقالات والنصائح في مجال الصحة النفسية.</p>
                                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" class="form-control" name="email" placeholder="بريدك الإلكتروني" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary w-100">اشترك الآن</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- قسم المقالات المقترحة -->
    <section class="suggested-posts">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">قد يعجبك أيضاً</h2>
                <div class="section-divider"></div>
            </div>
            <div class="row">
                @foreach($suggestedPosts as $suggestedPost)
                    <div class="col-lg-4 col-md-6">
                        <div class="post-card">
                            <div class="card">
                                <div class="post-image">
                                    <a href="{{ route('blog.show', $suggestedPost->slug) }}">
                                        <img src="{{ $suggestedPost->featured_image }}" alt="{{ $suggestedPost->title }}" class="card-img-top">
                                        <div class="post-category">
                                            <span>{{ $suggestedPost->category->name }}</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title">
                                        <a href="{{ route('blog.show', $suggestedPost->slug) }}">{{ $suggestedPost->title }}</a>
                                    </h3>
                                    <div class="post-meta">
                                        <div class="post-author">
                                            <img src="{{ $suggestedPost->author->profile_photo_url }}" alt="{{ $suggestedPost->author->name }}" class="author-image">
                                            <span>{{ $suggestedPost->author->name }}</span>
                                        </div>
                                        <div class="post-date">
                                            <i class="far fa-calendar-alt"></i>
                                            <span>{{ $suggestedPost->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <p class="card-text">{{ Str::limit($suggestedPost->excerpt, 100) }}</p>
                                    <a href="{{ route('blog.show', $suggestedPost->slug) }}" class="btn btn-link">اقرأ المزيد <i class="fas fa-long-arrow-alt-left"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .blog-post-page section {
        padding: 30px 0;
    }
    
    /* قسم الترويسة */
    .post-header {
        background-color: #f8f9fa;
        padding: 40px 0;
    }
    
    .page-header {
        text-align: center;
    }
    
    .breadcrumb {
        justify-content: center;
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
    }
    
    .breadcrumb-item a {
        color: #6a1b9a;
        text-decoration: none;
    }
    
    .breadcrumb-item.active {
        color: #666;
    }
    
    .post-category {
        margin-bottom: 15px;
    }
    
    .post-category a {
        background-color: #6a1b9a;
        color: #fff;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    
    .post-category a:hover {
        background-color: #4a148c;
    }
    
    .post-title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
    }
    
    .post-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .post-author {
        display: flex;
        align-items: center;
        margin-left: 20px;
    }
    
    .author-image {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
    }
    
    .author-info {
        display: flex;
        flex-direction: column;
    }
    
    .author-name {
        font-weight: 600;
        color: #333;
    }
    
    .author-title {
        font-size: 12px;
        color: #666;
    }
    
    .post-details {
        display: flex;
        align-items: center;
    }
    
    .post-date, .post-reading-time, .post-views {
        display: flex;
        align-items: center;
        margin-left: 15px;
        font-size: 14px;
        color: #666;
    }
    
    .post-date i, .post-reading-time i, .post-views i {
        margin-left: 5px;
        color: #6a1b9a;
    }
    
    /* قسم صورة المقال */
    .post-featured-image {
        margin-top: -20px;
    }
    
    .featured-image {
        position: relative;
        margin-bottom: 30px;
    }
    
    .featured-image img {
        width: 100%;
        height: auto;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .image-caption {
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 8px 15px;
        font-size: 12px;
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
        text-align: center;
    }
    
    /* قسم محتوى المقال */
    .post-content {
        padding: 40px 0;
    }
    
    .content-wrapper {
        margin-bottom: 40px;
    }
    
    .post-excerpt {
        font-size: 18px;
        line-height: 1.8;
        color: #555;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .post-body {
        font-size: 16px;
        line-height: 1.8;
        color: #333;
        margin-bottom: 30px;
    }
    
    .post-body h2 {
        font-size: 24px;
        font-weight: 700;
        margin: 30px 0 15px;
        color: #333;
    }
    
    .post-body h3 {
        font-size: 20px;
        font-weight: 600;
        margin: 25px 0 15px;
        color: #333;
    }
    
    .post-body p {
        margin-bottom: 20px;
    }
    
    .post-body ul, .post-body ol {
        margin-bottom: 20px;
        padding-right: 20px;
    }
    
    .post-body li {
        margin-bottom: 10px;
    }
    
    .post-body img {
        max-width: 100%;
        height: auto;
        margin: 20px 0;
        border-radius: 5px;
    }
    
    .post-body blockquote {
        background-color: #f8f9fa;
        border-right: 4px solid #6a1b9a;
        padding: 20px;
        margin: 20px 0;
        font-style: italic;
        color: #555;
    }
    
    .post-tags {
        margin-bottom: 30px;
    }
    
    .post-tags h4 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }
    
    .tags-list {
        display: flex;
        flex-wrap: wrap;
    }
    
    .tag-item {
        display: inline-block;
        background-color: #f0e6f5;
        color: #6a1b9a;
        padding: 5px 15px;
        border-radius: 20px;
        margin-left: 10px;
        margin-bottom: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .tag-item:hover {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    .post-share {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eee;
    }
    
    .post-share h4 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }
    
    .share-buttons {
        display: flex;
    }
    
    .share-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .share-button.facebook {
        background-color: #3b5998;
    }
    
    .share-button.twitter {
        background-color: #1da1f2;
    }
    
    .share-button.whatsapp {
        background-color: #25d366;
    }
    
    .share-button.telegram {
        background-color: #0088cc;
    }
    
    .share-button.email {
        background-color: #ea4335;
    }
    
    .share-button:hover {
        opacity: 0.8;
        transform: translateY(-3px);
    }
    
    .post-author-bio {
        display: flex;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .post-author-bio .author-image {
        width: 100px;
        height: 100px;
        margin-left: 20px;
    }
    
    .post-author-bio .author-info {
        flex: 1;
    }
    
    .post-author-bio .author-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .post-author-bio .author-title {
        font-size: 14px;
        color: #6a1b9a;
        margin-bottom: 10px;
    }
    
    .post-author-bio .author-bio {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }
    
    .author-social {
        display: flex;
    }
    
    .author-social a {
        width: 30px;
        height: 30px;
        background-color: #6a1b9a;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .author-social a:hover {
        background-color: #4a148c;
        transform: translateY(-3px);
    }
    
    .post-navigation {
        margin-bottom: 40px;
    }
    
    .post-nav-link {
        display: block;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        text-decoration: none;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .post-nav-link:hover {
        background-color: #f0e6f5;
    }
    
    .post-nav-link.prev {
        text-align: right;
    }
    
    .post-nav-link.next {
        text-align: left;
    }
    
    .post-nav-arrow {
        display: flex;
        align-items: center;
        color: #6a1b9a;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .post-nav-arrow i {
        margin: 0 5px;
    }
    
    .post-nav-title {
        color: #333;
        font-size: 14px;
    }
    
    /* قسم التعليقات */
    .post-comments {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid #eee;
    }
    
    .comments-header {
        margin-bottom: 30px;
    }
    
    .comments-header h3 {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }
    
    .comments-list {
        margin-bottom: 30px;
    }
    
    .comment-item {
        display: flex;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eee;
    }
    
    .comment-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        margin-left: 20px;
    }
    
    .comment-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .comment-content {
        flex: 1;
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .comment-author {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }
    
    .comment-date {
        font-size: 12px;
        color: #666;
    }
    
    .comment-body {
        margin-bottom: 15px;
    }
    
    .comment-body p {
        font-size: 14px;
        color: #555;
        margin-bottom: 0;
    }
    
    .comment-actions {
        display: flex;
        margin-bottom: 15px;
    }
    
    .comment-actions .btn-link {
        color: #6a1b9a;
        padding: 0;
        margin-left: 15px;
        font-size: 12px;
        text-decoration: none;
    }
    
    .comment-actions .btn-link i {
        margin-left: 5px;
    }
    
    .comment-actions .btn-link.active {
        color: #4a148c;
        font-weight: 600;
    }
    
    .reply-form-container, .edit-form-container {
        margin-bottom: 15px;
    }
    
    .comment-replies {
        margin-top: 20px;
        padding-right: 20px;
        border-right: 2px solid #eee;
    }
    
    .reply-item {
        display: flex;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .reply-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .reply-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-left: 15px;
    }
    
    .reply-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .reply-content {
        flex: 1;
    }
    
    .reply-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .reply-author {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }
    
    .reply-date {
        font-size: 12px;
        color: #666;
    }
    
    .reply-body {
        margin-bottom: 10px;
    }
    
    .reply-body p {
        font-size: 14px;
        color: #555;
        margin-bottom: 0;
    }
    
    .reply-actions {
        display: flex;
    }
    
    .reply-actions .btn-link {
        color: #6a1b9a;
        padding: 0;
        margin-left: 15px;
        font-size: 12px;
        text-decoration: none;
    }
    
    .reply-actions .btn-link i {
        margin-left: 5px;
    }
    
    .add-comment {
        margin-top: 40px;
    }
    
    .add-comment h3 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
    }
    
    /* القائمة الجانبية */
    .sidebar {
        position: sticky;
        top: 20px;
    }
    
    .sidebar-widget {
        margin-bottom: 30px;
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
    }
    
    .widget-title {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .widget-title h3 {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 0;
    }
    
    .author-card {
        text-align: center;
    }
    
    .author-card .author-image {
        width: 80px;
        height: 80px;
        margin: 0 auto 15px;
    }
    
    .author-card .author-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .author-card .author-title {
        font-size: 14px;
        color: #6a1b9a;
        margin-bottom: 10px;
    }
    
    .author-card .author-bio {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }
    
    .author-card .author-social {
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .related-posts-list {
        margin-bottom: 0;
    }
    
    .related-post-item {
        display: flex;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .related-post-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .related-post-item .post-image {
        width: 80px;
        height: 60px;
        border-radius: 5px;
        overflow: hidden;
        margin-left: 10px;
    }
    
    .related-post-item .post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .related-post-item .post-info {
        flex: 1;
    }
    
    .related-post-item .post-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .related-post-item .post-title a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .related-post-item .post-title a:hover {
        color: #6a1b9a;
    }
    
    .related-post-item .post-date {
        font-size: 12px;
        color: #666;
    }
    
    .categories-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .category-item {
        margin-bottom: 10px;
    }
    
    .category-item:last-child {
        margin-bottom: 0;
    }
    
    .category-item a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 15px;
        background-color: #fff;
        border-radius: 5px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .category-item a:hover {
        background-color: #f0e6f5;
    }
    
    .category-item.active a {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    .category-count {
        background-color: #f0f0f0;
        color: #666;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        transition: all 0.3s ease;
    }
    
    .category-item.active .category-count {
        background-color: #fff;
        color: #6a1b9a;
    }
    
    .tags-cloud {
        display: flex;
        flex-wrap: wrap;
    }
    
    .tags-widget .tag-item {
        margin-bottom: 10px;
    }
    
    .tags-widget .tag-item.active {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    .newsletter-widget p {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }
    
    /* قسم المقالات المقترحة */
    .suggested-posts {
        background-color: #f8f9fa;
        padding: 50px 0;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 30px;
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
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .sidebar {
            margin-top: 40px;
            position: static;
        }
    }
    
    @media (max-width: 767px) {
        .post-title {
            font-size: 24px;
        }
        
        .post-meta {
            flex-direction: column;
        }
        
        .post-author {
            margin-bottom: 10px;
            margin-left: 0;
        }
        
        .post-details {
            flex-wrap: wrap;
        }
        
        .post-date, .post-reading-time, .post-views {
            margin-bottom: 5px;
        }
        
        .post-author-bio {
            flex-direction: column;
            text-align: center;
        }
        
        .post-author-bio .author-image {
            margin: 0 auto 15px;
        }
        
        .author-social {
            justify-content: center;
        }
        
        .comment-item {
            flex-direction: column;
        }
        
        .comment-avatar {
            margin: 0 auto 15px;
        }
        
        .comment-header {
            flex-direction: column;
            text-align: center;
        }
        
        .comment-date {
            margin-top: 5px;
        }
        
        .comment-actions {
            justify-content: center;
        }
        
        .reply-item {
            flex-direction: column;
        }
        
        .reply-avatar {
            margin: 0 auto 10px;
        }
        
        .reply-header {
            flex-direction: column;
            text-align: center;
        }
        
        .reply-date {
            margin-top: 5px;
        }
        
        .reply-actions {
            justify-content: center;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تبديل عرض نموذج الرد على التعليق
        $('.reply-btn').on('click', function() {
            const commentId = $(this).data('comment-id');
            $(`#reply-form-${commentId}`).toggle();
        });
        
        // إلغاء الرد
        $('.cancel-reply').on('click', function() {
            const commentId = $(this).data('comment-id');
            $(`#reply-form-${commentId}`).hide();
        });
        
        // تبديل عرض نموذج تعديل التعليق
        $('.edit-btn').on('click', function() {
            const commentId = $(this).data('comment-id');
            $(`#edit-form-${commentId}`).toggle();
        });
        
        // إلغاء التعديل
        $('.cancel-edit').on('click', function() {
            const commentId = $(this).data('comment-id');
            $(`#edit-form-${commentId}`).hide();
        });
        
        // تبديل عرض نموذج تعديل الرد
        $('.edit-reply-btn').on('click', function() {
            const replyId = $(this).data('reply-id');
            $(`#edit-reply-form-${replyId}`).toggle();
        });
        
        // إلغاء تعديل الرد
        $('.cancel-edit-reply').on('click', function() {
            const replyId = $(this).data('reply-id');
            $(`#edit-reply-form-${replyId}`).hide();
        });
        
        // الإعجاب بالتعليق
        $('.like-btn').on('click', function() {
            const commentId = $(this).data('comment-id');
            const replyId = $(this).data('reply-id');
            const button = $(this);
            
            $.ajax({
                url: replyId ? `/blog/replies/${replyId}/like` : `/blog/comments/${commentId}/like`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const likesCount = response.likes_count;
                        button.find('span').text(`إعجاب (${likesCount})`);
                        
                        if (response.is_liked) {
                            button.addClass('active');
                            button.find('i').removeClass('far').addClass('fas');
                        } else {
                            button.removeClass('active');
                            button.find('i').removeClass('fas').addClass('far');
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        // المستخدم غير مسجل دخول
                        alert('يجب تسجيل الدخول للإعجاب بالتعليق');
                    } else {
                        alert('حدث خطأ أثناء معالجة طلبك');
                    }
                }
            });
        });
        
        // تهيئة مشاركة المقال
        $('.share-button').on('click', function(e) {
            const width = 600;
            const height = 400;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;
            
            if (!$(this).hasClass('email')) {
                e.preventDefault();
                window.open(
                    this.href,
                    '',
                    `width=${width},height=${height},left=${left},top=${top}`
                );
            }
        });
        
        // تمييز الروابط الداخلية في محتوى المقال
        $('.post-body a').each(function() {
            const href = $(this).attr('href');
            if (href && !href.startsWith('http') && !href.startsWith('#')) {
                $(this).attr('href', `{{ url('/') }}/${href}`);
            }
            if (href && href.startsWith('http') && !href.includes(window.location.hostname)) {
                $(this).attr('target', '_blank');
                $(this).attr('rel', 'noopener noreferrer');
            }
        });
        
        // تهيئة الصور في محتوى المقال
        $('.post-body img').each(function() {
            $(this).addClass('img-fluid');
            
            // إضافة خاصية الضغط على الصورة لعرضها بحجم أكبر
            $(this).on('click', function() {
                const src = $(this).attr('src');
                $('body').append(`
                    <div class="image-modal">
                        <div class="image-modal-content">
                            <span class="image-modal-close">&times;</span>
                            <img src="${src}" class="img-fluid">
                        </div>
                    </div>
                `);
                $('.image-modal').fadeIn();
            });
        });
        
        // إغلاق النافذة المنبثقة للصورة
        $(document).on('click', '.image-modal, .image-modal-close', function() {
            $('.image-modal').fadeOut(function() {
                $(this).remove();
            });
        });
        
        // منع إغلاق النافذة المنبثقة عند النقر على الصورة نفسها
        $(document).on('click', '.image-modal-content img', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endsection
