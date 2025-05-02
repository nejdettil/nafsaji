@extends('layouts.dashboard')

@section('title', 'التقييمات - نفسجي للتمكين النفسي')

@section('content')
<div class="reviews-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">التقييمات</h1>
                    <p class="dashboard-subtitle">إدارة ومراجعة تقييمات العملاء</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#requestReviewModal">
                            <i class="fas fa-star"></i> طلب تقييم
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="dashboard-card mb-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-chart-bar"></i> ملخص التقييمات
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="rating-summary">
                                <div class="overall-rating">
                                    <div class="rating-value">{{ number_format($averageRating, 1) }}</div>
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating))
                                                <i class="fas fa-star"></i>
                                            @elseif($i - 0.5 <= $averageRating)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="rating-count">{{ $totalReviews }} تقييم</div>
                                </div>
                                
                                <div class="rating-bars">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="rating-bar-item">
                                            <div class="rating-label">{{ $i }} <i class="fas fa-star"></i></div>
                                            <div class="rating-bar">
                                                <div class="rating-bar-fill" style="width: {{ $totalReviews > 0 ? ($ratingCounts[$i] / $totalReviews) * 100 : 0 }}%"></div>
                                            </div>
                                            <div class="rating-count">{{ $ratingCounts[$i] }}</div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="rating-stats">
                                <div class="stats-item">
                                    <h6>معدل الاستجابة</h6>
                                    <p>{{ number_format($responseRate, 1) }}%</p>
                                </div>
                                <div class="stats-item">
                                    <h6>متوسط وقت الاستجابة</h6>
                                    <p>{{ $averageResponseTime }} ساعة</p>
                                </div>
                                <div class="stats-item">
                                    <h6>التقييمات الإيجابية</h6>
                                    <p>{{ number_format($positiveReviewsRate, 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-lightbulb"></i> نصائح لتحسين التقييمات
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="tips-list">
                                <div class="tip-item">
                                    <div class="tip-icon">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <div class="tip-content">
                                        <h6>الرد على التقييمات</h6>
                                        <p>الرد على جميع التقييمات يظهر اهتمامك بآراء العملاء ويزيد من ثقتهم.</p>
                                    </div>
                                </div>
                                <div class="tip-item">
                                    <div class="tip-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="tip-content">
                                        <h6>سرعة الاستجابة</h6>
                                        <p>الرد السريع على التقييمات السلبية يساعد في حل المشكلات قبل تفاقمها.</p>
                                    </div>
                                </div>
                                <div class="tip-item">
                                    <div class="tip-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="tip-content">
                                        <h6>طلب التقييمات</h6>
                                        <p>لا تتردد في طلب التقييمات من العملاء الراضين بعد اكتمال الجلسات.</p>
                                    </div>
                                </div>
                                <div class="tip-item">
                                    <div class="tip-icon">
                                        <i class="fas fa-thumbs-up"></i>
                                    </div>
                                    <div class="tip-content">
                                        <h6>التعامل مع النقد</h6>
                                        <p>تقبل النقد البناء واستخدمه لتحسين خدماتك وتجربة العملاء.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-star"></i> جميع التقييمات
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter"></i> تصفية
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item filter-item" href="#" data-filter="all">جميع التقييمات</a></li>
                                        <li><a class="dropdown-item filter-item" href="#" data-filter="positive">التقييمات الإيجابية (4-5)</a></li>
                                        <li><a class="dropdown-item filter-item" href="#" data-filter="neutral">التقييمات المحايدة (3)</a></li>
                                        <li><a class="dropdown-item filter-item" href="#" data-filter="negative">التقييمات السلبية (1-2)</a></li>
                                        <li><a class="dropdown-item filter-item" href="#" data-filter="unanswered">التقييمات بدون رد</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-sort"></i> ترتيب
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item sort-item" href="#" data-sort="newest">الأحدث أولاً</a></li>
                                        <li><a class="dropdown-item sort-item" href="#" data-sort="oldest">الأقدم أولاً</a></li>
                                        <li><a class="dropdown-item sort-item" href="#" data-sort="highest">الأعلى تقييماً</a></li>
                                        <li><a class="dropdown-item sort-item" href="#" data-sort="lowest">الأقل تقييماً</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($reviews) > 0)
                                <div class="reviews-list">
                                    @foreach($reviews as $review)
                                        <div class="review-item" data-rating="{{ $review->rating }}" data-has-response="{{ $review->specialist_response ? 'true' : 'false' }}">
                                            <div class="review-header">
                                                <div class="review-user">
                                                    <img src="{{ $review->user->profile_image ? asset('storage/' . $review->user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $review->user->full_name }}" class="review-avatar">
                                                    <div class="review-user-info">
                                                        <h6 class="review-user-name">{{ $review->user->full_name }}</h6>
                                                        <div class="review-meta">
                                                            <span class="review-date">{{ $review->created_at->format('Y-m-d') }}</span>
                                                            <span class="review-service">{{ $review->service->name }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="review-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="review-content">
                                                <p>{{ $review->comment }}</p>
                                            </div>
                                            @if($review->specialist_response)
                                                <div class="review-response">
                                                    <div class="response-header">
                                                        <i class="fas fa-reply"></i> ردك:
                                                        <span class="response-date">{{ $review->response_date->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="response-content">
                                                        <p>{{ $review->specialist_response }}</p>
                                                    </div>
                                                    <div class="response-actions">
                                                        <button type="button" class="btn btn-sm btn-link edit-response" data-id="{{ $review->id }}" data-response="{{ $review->specialist_response }}">
                                                            <i class="fas fa-edit"></i> تعديل الرد
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="review-actions">
                                                    <button type="button" class="btn btn-sm btn-outline-primary add-response" data-id="{{ $review->id }}">
                                                        <i class="fas fa-reply"></i> الرد على التقييم
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="pagination-container">
                                    {{ $reviews->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h5>لا توجد تقييمات</h5>
                                    <p>لم يقم أي عميل بتقييم خدماتك بعد.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestReviewModal">
                                        <i class="fas fa-star"></i> طلب تقييم
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

<!-- Modal إضافة رد -->
<div class="modal fade" id="addResponseModal" tabindex="-1" aria-labelledby="addResponseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addResponseModalLabel">الرد على التقييم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('specialist.reviews.respond', 0) }}" method="POST" id="addResponseForm">
                @csrf
                <input type="hidden" id="review_id" name="review_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="specialist_response">ردك على التقييم</label>
                        <textarea class="form-control" id="specialist_response" name="specialist_response" rows="4" required></textarea>
                        <small class="form-text text-muted">اكتب رداً مهذباً ومهنياً على تقييم العميل. تذكر أن ردك سيكون مرئياً للجميع.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إرسال الرد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تعديل رد -->
<div class="modal fade" id="editResponseModal" tabindex="-1" aria-labelledby="editResponseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editResponseModalLabel">تعديل الرد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('specialist.reviews.update-response', 0) }}" method="POST" id="editResponseForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_review_id" name="review_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_specialist_response">تعديل ردك</label>
                        <textarea class="form-control" id="edit_specialist_response" name="specialist_response" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal طلب تقييم -->
<div class="modal fade" id="requestReviewModal" tabindex="-1" aria-labelledby="requestReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestReviewModalLabel">طلب تقييم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('specialist.reviews.request') }}" method="POST" id="requestReviewForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="client_id">العميل</label>
                        <select class="form-select" id="client_id" name="client_id" required>
                            <option value="">اختر العميل</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="service_id">الخدمة</label>
                        <select class="form-select" id="service_id" name="service_id" required>
                            <option value="">اختر الخدمة</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="message">رسالة شخصية (اختياري)</label>
                        <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                        <small class="form-text text-muted">يمكنك إضافة رسالة شخصية لتشجيع العميل على ترك تقييم.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .reviews-page {
        background-color: #f8f9fa;
    }
    
    .dashboard-header {
        background-color: #fff;
        padding: 30px 0;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .dashboard-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }
    
    .dashboard-subtitle {
        color: #666;
        margin-bottom: 0;
    }
    
    .dashboard-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .dashboard-content {
        margin-bottom: 30px;
    }
    
    /* بطاقات المحتوى */
    .dashboard-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .dashboard-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .dashboard-card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 0;
        color: #333;
        display: flex;
        align-items: center;
    }
    
    .dashboard-card-title i {
        margin-left: 10px;
        color: #6a1b9a;
    }
    
    .dashboard-card-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dashboard-card-body {
        padding: 20px;
    }
    
    /* ملخص التقييمات */
    .rating-summary {
        margin-bottom: 20px;
    }
    
    .overall-rating {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .rating-value {
        font-size: 48px;
        font-weight: 700;
        color: #6a1b9a;
        line-height: 1;
        margin-bottom: 10px;
    }
    
    .rating-stars {
        font-size: 24px;
        color: #ffc107;
        margin-bottom: 5px;
    }
    
    .rating-count {
        font-size: 14px;
        color: #666;
    }
    
    .rating-bars {
        margin-top: 20px;
    }
    
    .rating-bar-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .rating-label {
        width: 40px;
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }
    
    .rating-bar {
        flex: 1;
        height: 8px;
        background-color: #f0f0f0;
        border-radius: 4px;
        margin: 0 10px;
        overflow: hidden;
    }
    
    .rating-bar-fill {
        height: 100%;
        background-color: #6a1b9a;
        border-radius: 4px;
    }
    
    .rating-count {
        width: 30px;
        font-size: 14px;
        color: #666;
        text-align: right;
    }
    
    /* إحصائيات التقييمات */
    .rating-stats {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 20px;
    }
    
    .stats-item {
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .stats-item:hover {
        background-color: #f0e6f5;
    }
    
    .stats-item h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .stats-item p {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 0;
        color: #6a1b9a;
    }
    
    /* نصائح لتحسين التقييمات */
    .tips-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .tip-item {
        display: flex;
        align-items: flex-start;
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .tip-item:hover {
        background-color: #f0e6f5;
    }
    
    .tip-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background-color: #f0e6f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #6a1b9a;
        margin-left: 15px;
    }
    
    .tip-content {
        flex: 1;
    }
    
    .tip-content h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .tip-content p {
        font-size: 14px;
        margin-bottom: 0;
        color: #666;
    }
    
    /* قائمة التقييمات */
    .reviews-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .review-item {
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }
    
    .review-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }
    
    .review-user {
        display: flex;
        align-items: center;
    }
    
    .review-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        object-fit: cover;
    }
    
    .review-user-info {
        display: flex;
        flex-direction: column;
    }
    
    .review-user-name {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 0;
        color: #333;
    }
    
    .review-meta {
        display: flex;
        gap: 10px;
        font-size: 12px;
        color: #666;
    }
    
    .review-rating {
        color: #ffc107;
    }
    
    .review-content {
        margin-bottom: 10px;
    }
    
    .review-content p {
        margin-bottom: 0;
        color: #333;
    }
    
    .review-response {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-top: 10px;
    }
    
    .response-header {
        font-size: 12px;
        font-weight: 600;
        color: #6a1b9a;
        margin-bottom: 5px;
        display: flex;
        justify-content: space-between;
    }
    
    .response-date {
        color: #666;
        font-weight: normal;
    }
    
    .response-content p {
        margin-bottom: 0;
        font-size: 14px;
        color: #333;
    }
    
    .response-actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
    }
    
    .review-actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
    }
    
    /* الترقيم الصفحي */
    .pagination-container {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
    
    .pagination {
        --bs-pagination-color: #6a1b9a;
        --bs-pagination-hover-color: #6a1b9a;
        --bs-pagination-focus-color: #6a1b9a;
        --bs-pagination-active-bg: #6a1b9a;
        --bs-pagination-active-border-color: #6a1b9a;
    }
    
    /* حالة فارغة */
    .empty-state {
        text-align: center;
        padding: 30px 20px;
    }
    
    .empty-state-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #f0e6f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #6a1b9a;
        margin: 0 auto 15px;
    }
    
    .empty-state h5 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .empty-state p {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
    }
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .dashboard-actions {
            margin-top: 15px;
            justify-content: flex-start;
        }
    }
    
    @media (max-width: 767px) {
        .dashboard-header {
            padding: 20px 0;
        }
        
        .dashboard-title {
            font-size: 20px;
        }
        
        .dashboard-actions {
            flex-wrap: wrap;
        }
        
        .review-header {
            flex-direction: column;
        }
        
        .review-rating {
            margin-top: 10px;
        }
    }
    
    @media (max-width: 575px) {
        .review-user {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .review-avatar {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // إضافة رد على التقييم
        $('.add-response').on('click', function() {
            var reviewId = $(this).data('id');
            $('#review_id').val(reviewId);
            
            var respondUrl = "{{ route('specialist.reviews.respond', ':id') }}".replace(':id', reviewId);
            $('#addResponseForm').attr('action', respondUrl);
            
            $('#addResponseModal').modal('show');
        });
        
        // تعديل رد على التقييم
        $('.edit-response').on('click', function() {
            var reviewId = $(this).data('id');
            var response = $(this).data('response');
            
            $('#edit_review_id').val(reviewId);
            $('#edit_specialist_response').val(response);
            
            var updateUrl = "{{ route('specialist.reviews.update-response', ':id') }}".replace(':id', reviewId);
            $('#editResponseForm').attr('action', updateUrl);
            
            $('#editResponseModal').modal('show');
        });
        
        // تصفية التقييمات
        $('.filter-item').on('click', function(e) {
            e.preventDefault();
            
            var filter = $(this).data('filter');
            $('.review-item').show();
            
            if (filter === 'positive') {
                $('.review-item').not('[data-rating="4"], [data-rating="5"]').hide();
            } else if (filter === 'neutral') {
                $('.review-item').not('[data-rating="3"]').hide();
            } else if (filter === 'negative') {
                $('.review-item').not('[data-rating="1"], [data-rating="2"]').hide();
            } else if (filter === 'unanswered') {
                $('.review-item').not('[data-has-response="false"]').hide();
            }
            
            $('#filterDropdown').text($(this).text());
        });
        
        // ترتيب التقييمات
        $('.sort-item').on('click', function(e) {
            e.preventDefault();
            
            var sort = $(this).data('sort');
            var reviewsList = $('.reviews-list');
            var reviews = $('.review-item').get();
            
            reviews.sort(function(a, b) {
                if (sort === 'newest') {
                    return new Date($(b).find('.review-date').text()) - new Date($(a).find('.review-date').text());
                } else if (sort === 'oldest') {
                    return new Date($(a).find('.review-date').text()) - new Date($(b).find('.review-date').text());
                } else if (sort === 'highest') {
                    return parseInt($(b).data('rating')) - parseInt($(a).data('rating'));
                } else if (sort === 'lowest') {
                    return parseInt($(a).data('rating')) - parseInt($(b).data('rating'));
                }
            });
            
            $.each(reviews, function(index, item) {
                reviewsList.append(item);
            });
            
            $('#sortDropdown').text($(this).text());
        });
        
        // تحديث قائمة الخدمات بناءً على العميل المختار
        $('#client_id').on('change', function() {
            var clientId = $(this).val();
            
            if (clientId) {
                $.ajax({
                    url: "{{ route('specialist.client.services') }}",
                    type: 'GET',
                    data: {
                        client_id: clientId
                    },
                    success: function(response) {
                        var serviceSelect = $('#service_id');
                        serviceSelect.empty();
                        serviceSelect.append('<option value="">اختر الخدمة</option>');
                        
                        $.each(response.services, function(index, service) {
                            serviceSelect.append('<option value="' + service.id + '">' + service.name + '</option>');
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
