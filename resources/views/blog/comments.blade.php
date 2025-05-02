@extends('layouts.app')

@section('title', 'التعليقات - نفسجي للتمكين النفسي')

@section('content')
<div class="comments-page">
    <!-- قسم الترويسة -->
    <section class="comments-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1 class="main-title">إدارة التعليقات</h1>
                        <p class="lead">مشاركة الآراء والتفاعل مع المحتوى</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">المدونة</a></li>
                                <li class="breadcrumb-item active" aria-current="page">التعليقات</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم إدارة التعليقات -->
    <section class="comments-management">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="comments-sidebar">
                        <div class="card">
                            <div class="card-header">
                                <h3>لوحة التحكم</h3>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="{{ route('user.dashboard') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                                </a>
                                <a href="{{ route('user.profile') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-user"></i> الملف الشخصي
                                </a>
                                <a href="{{ route('blog.comments.index') }}" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-comments"></i> تعليقاتي
                                </a>
                                <a href="{{ route('user.bookings') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-calendar-check"></i> حجوزاتي
                                </a>
                                <a href="{{ route('user.favorites') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-heart"></i> المفضلة
                                </a>
                                <a href="{{ route('user.notifications') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-bell"></i> الإشعارات
                                    @if($unreadNotificationsCount > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $unreadNotificationsCount }}</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-9">
                    <div class="comments-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3>تعليقاتي</h3>
                                    <div class="comments-filter">
                                        <select class="form-select" id="comments-filter">
                                            <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>جميع التعليقات</option>
                                            <option value="approved" {{ request('filter') == 'approved' ? 'selected' : '' }}>التعليقات المعتمدة</option>
                                            <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>التعليقات قيد المراجعة</option>
                                            <option value="replies" {{ request('filter') == 'replies' ? 'selected' : '' }}>الردود على تعليقاتي</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($comments->count() > 0)
                                    <div class="comments-list">
                                        @foreach($comments as $comment)
                                            <div class="comment-item" id="comment-{{ $comment->id }}">
                                                <div class="comment-header">
                                                    <div class="comment-meta">
                                                        <div class="comment-post">
                                                            <a href="{{ route('blog.show', $comment->post->slug) }}">{{ $comment->post->title }}</a>
                                                        </div>
                                                        <div class="comment-date">
                                                            <i class="far fa-clock"></i>
                                                            <span>{{ $comment->created_at->format('d M Y, h:i A') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="comment-status">
                                                        @if($comment->is_approved)
                                                            <span class="badge bg-success">معتمد</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">قيد المراجعة</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="comment-body">
                                                    <p>{{ $comment->content }}</p>
                                                </div>
                                                <div class="comment-footer">
                                                    <div class="comment-stats">
                                                        <span class="comment-likes" title="الإعجابات">
                                                            <i class="far fa-thumbs-up"></i> {{ $comment->likes_count }}
                                                        </span>
                                                        <span class="comment-replies" title="الردود">
                                                            <i class="far fa-comment"></i> {{ $comment->replies_count }}
                                                        </span>
                                                    </div>
                                                    <div class="comment-actions">
                                                        <button class="btn btn-sm btn-outline-primary edit-comment-btn" data-comment-id="{{ $comment->id }}">
                                                            <i class="far fa-edit"></i> تعديل
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="{{ $comment->id }}">
                                                            <i class="far fa-trash-alt"></i> حذف
                                                        </button>
                                                        <a href="{{ route('blog.show', $comment->post->slug) }}#comment-{{ $comment->id }}" class="btn btn-sm btn-outline-secondary">
                                                            <i class="far fa-eye"></i> عرض
                                                        </a>
                                                    </div>
                                                </div>
                                                
                                                <!-- نموذج تعديل التعليق -->
                                                <div class="edit-comment-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                                    <form action="{{ route('blog.comments.update', $comment->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <textarea class="form-control" name="content" rows="3" required>{{ $comment->content }}</textarea>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <button type="submit" class="btn btn-primary">حفظ التعديل</button>
                                                            <button type="button" class="btn btn-secondary cancel-edit-btn" data-comment-id="{{ $comment->id }}">إلغاء</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                
                                                <!-- الردود على التعليق -->
                                                @if($comment->replies_count > 0 && request('filter') == 'replies')
                                                    <div class="comment-replies">
                                                        <div class="replies-header">
                                                            <h5>الردود ({{ $comment->replies_count }})</h5>
                                                        </div>
                                                        <div class="replies-list">
                                                            @foreach($comment->replies as $reply)
                                                                <div class="reply-item" id="reply-{{ $reply->id }}">
                                                                    <div class="reply-header">
                                                                        <div class="reply-author">
                                                                            <img src="{{ $reply->user->profile_photo_url }}" alt="{{ $reply->user->name }}" class="author-image">
                                                                            <span>{{ $reply->user->name }}</span>
                                                                        </div>
                                                                        <div class="reply-date">
                                                                            <i class="far fa-clock"></i>
                                                                            <span>{{ $reply->created_at->format('d M Y, h:i A') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="reply-body">
                                                                        <p>{{ $reply->content }}</p>
                                                                    </div>
                                                                    <div class="reply-footer">
                                                                        <div class="reply-likes">
                                                                            <i class="far fa-thumbs-up"></i> {{ $reply->likes_count }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="pagination-container mt-4">
                                        {{ $comments->appends(request()->query())->links() }}
                                    </div>
                                @else
                                    <div class="no-comments">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <span>لا توجد تعليقات لعرضها حالياً.</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- نافذة تأكيد الحذف -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCommentModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا التعليق؟ لا يمكن التراجع عن هذا الإجراء.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteCommentForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .comments-page section {
        padding: 40px 0;
    }
    
    /* قسم الترويسة */
    .comments-header {
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
    
    /* القائمة الجانبية */
    .comments-sidebar {
        margin-bottom: 30px;
    }
    
    .comments-sidebar .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .comments-sidebar .card-header {
        background-color: #6a1b9a;
        color: #fff;
        padding: 15px 20px;
        border-bottom: none;
    }
    
    .comments-sidebar .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .comments-sidebar .list-group-item {
        padding: 12px 20px;
        border-left: none;
        border-right: none;
        border-color: #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .comments-sidebar .list-group-item i {
        margin-left: 10px;
        color: #6a1b9a;
    }
    
    .comments-sidebar .list-group-item.active {
        background-color: #f0e6f5;
        color: #6a1b9a;
        border-color: #f0f0f0;
    }
    
    .comments-sidebar .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .comments-sidebar .badge {
        margin-right: 5px;
    }
    
    /* محتوى التعليقات */
    .comments-content .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .comments-content .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
    
    .comments-content .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
        color: #333;
    }
    
    .comments-filter {
        width: 200px;
    }
    
    .comments-content .card-body {
        padding: 20px;
    }
    
    /* قائمة التعليقات */
    .comments-list {
        margin-bottom: 20px;
    }
    
    .comment-item {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .comment-meta {
        display: flex;
        flex-direction: column;
    }
    
    .comment-post {
        margin-bottom: 5px;
    }
    
    .comment-post a {
        color: #6a1b9a;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .comment-post a:hover {
        color: #4a148c;
    }
    
    .comment-date {
        font-size: 12px;
        color: #666;
    }
    
    .comment-date i {
        margin-left: 5px;
    }
    
    .comment-body {
        margin-bottom: 15px;
    }
    
    .comment-body p {
        margin-bottom: 0;
        color: #333;
    }
    
    .comment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .comment-stats {
        display: flex;
        align-items: center;
    }
    
    .comment-likes, .comment-replies {
        display: flex;
        align-items: center;
        margin-left: 15px;
        font-size: 14px;
        color: #666;
    }
    
    .comment-likes i, .comment-replies i {
        margin-left: 5px;
        color: #6a1b9a;
    }
    
    .comment-actions .btn {
        margin-right: 5px;
    }
    
    .edit-comment-form {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    /* الردود على التعليقات */
    .comment-replies {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .replies-header {
        margin-bottom: 15px;
    }
    
    .replies-header h5 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }
    
    .replies-list {
        padding-right: 20px;
        border-right: 3px solid #eee;
    }
    
    .reply-item {
        background-color: #fff;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .reply-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .reply-author {
        display: flex;
        align-items: center;
    }
    
    .reply-author .author-image {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-left: 10px;
    }
    
    .reply-author span {
        font-weight: 600;
        color: #333;
    }
    
    .reply-date {
        font-size: 12px;
        color: #666;
    }
    
    .reply-date i {
        margin-left: 5px;
    }
    
    .reply-body {
        margin-bottom: 10px;
    }
    
    .reply-body p {
        margin-bottom: 0;
        color: #333;
    }
    
    .reply-footer {
        display: flex;
        justify-content: flex-end;
    }
    
    .reply-likes {
        display: flex;
        align-items: center;
        font-size: 12px;
        color: #666;
    }
    
    .reply-likes i {
        margin-left: 5px;
        color: #6a1b9a;
    }
    
    /* التصميم المتجاوب */
    @media (max-width: 991px) {
        .comments-sidebar {
            margin-bottom: 30px;
        }
    }
    
    @media (max-width: 767px) {
        .comments-header {
            padding: 40px 0;
        }
        
        .main-title {
            font-size: 28px;
        }
        
        .comment-header {
            flex-direction: column;
        }
        
        .comment-status {
            margin-top: 10px;
        }
        
        .comment-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .comment-actions {
            margin-top: 10px;
        }
        
        .comments-filter {
            width: 100%;
            margin-top: 10px;
        }
        
        .comments-content .card-header {
            flex-direction: column;
        }
        
        .comments-content .card-header h3 {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تغيير الفلتر
        $('#comments-filter').on('change', function() {
            const filter = $(this).val();
            window.location.href = "{{ route('blog.comments.index') }}?filter=" + filter;
        });
        
        // تبديل عرض نموذج تعديل التعليق
        $('.edit-comment-btn').on('click', function() {
            const commentId = $(this).data('comment-id');
            $(`#edit-form-${commentId}`).toggle();
        });
        
        // إلغاء التعديل
        $('.cancel-edit-btn').on('click', function() {
            const commentId = $(this).data('comment-id');
            $(`#edit-form-${commentId}`).hide();
        });
        
        // تهيئة نافذة تأكيد الحذف
        $('.delete-comment-btn').on('click', function() {
            const commentId = $(this).data('comment-id');
            $('#deleteCommentForm').attr('action', `/blog/comments/${commentId}`);
            $('#deleteCommentModal').modal('show');
        });
    });
</script>
@endsection
