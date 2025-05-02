@extends('layouts.app')

@section('title', 'تقييم المختصين - نفسجي للتمكين النفسي')

@section('content')
<div class="specialists-review-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header">
                    <h1 class="main-title">تقييم المختصين</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('specialists.index') }}">المختصين</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('specialists.show', $specialist->id) }}">{{ $specialist->user->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">تقييم المختص</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="specialist-profile-card card mb-4">
                    <div class="card-body text-center">
                        <div class="specialist-image mb-3">
                            <img src="{{ $specialist->user->profile_photo_url }}" alt="{{ $specialist->user->name }}" class="img-fluid rounded-circle">
                        </div>
                        <h3 class="specialist-name">{{ $specialist->user->name }}</h3>
                        <p class="specialist-title">{{ $specialist->title }}</p>
                        <div class="specialist-rating mb-3">
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $specialist->average_rating)
                                        <i class="fas fa-star"></i>
                                    @elseif($i - 0.5 <= $specialist->average_rating)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-value">{{ number_format($specialist->average_rating, 1) }}</span>
                            <span class="rating-count">({{ $specialist->reviews_count }} تقييم)</span>
                        </div>
                        <div class="specialist-details text-start">
                            <div class="detail-item">
                                <i class="fas fa-graduation-cap"></i>
                                <span>{{ $specialist->specialization }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-briefcase"></i>
                                <span>{{ $specialist->experience }} سنوات خبرة</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-language"></i>
                                <span>{{ $specialist->languages }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>{{ $specialist->session_price }} ريال / الجلسة</span>
                            </div>
                        </div>
                        <div class="specialist-actions mt-3">
                            <a href="{{ route('specialists.show', $specialist->id) }}" class="btn btn-outline-primary w-100 mb-2">عرض الملف الشخصي</a>
                            <a href="{{ route('specialists.book', $specialist->id) }}" class="btn btn-primary w-100">حجز جلسة</a>
                        </div>
                    </div>
                </div>

                <div class="rating-summary card mb-4">
                    <div class="card-header">
                        <h3>ملخص التقييمات</h3>
                    </div>
                    <div class="card-body">
                        <div class="overall-rating text-center mb-4">
                            <div class="rating-circle">
                                <span class="rating-value">{{ number_format($specialist->average_rating, 1) }}</span>
                                <span class="rating-max">/5</span>
                            </div>
                            <div class="stars mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $specialist->average_rating)
                                        <i class="fas fa-star"></i>
                                    @elseif($i - 0.5 <= $specialist->average_rating)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="rating-count mt-1">{{ $specialist->reviews_count }} تقييم</p>
                        </div>

                        <div class="rating-bars">
                            <div class="rating-bar-item">
                                <div class="rating-label">
                                    <span class="stars-count">5</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $ratingPercentages[5] }}%" aria-valuenow="{{ $ratingPercentages[5] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="rating-percentage">{{ $ratingPercentages[5] }}%</span>
                            </div>
                            <div class="rating-bar-item">
                                <div class="rating-label">
                                    <span class="stars-count">4</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $ratingPercentages[4] }}%" aria-valuenow="{{ $ratingPercentages[4] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="rating-percentage">{{ $ratingPercentages[4] }}%</span>
                            </div>
                            <div class="rating-bar-item">
                                <div class="rating-label">
                                    <span class="stars-count">3</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $ratingPercentages[3] }}%" aria-valuenow="{{ $ratingPercentages[3] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="rating-percentage">{{ $ratingPercentages[3] }}%</span>
                            </div>
                            <div class="rating-bar-item">
                                <div class="rating-label">
                                    <span class="stars-count">2</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $ratingPercentages[2] }}%" aria-valuenow="{{ $ratingPercentages[2] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="rating-percentage">{{ $ratingPercentages[2] }}%</span>
                            </div>
                            <div class="rating-bar-item">
                                <div class="rating-label">
                                    <span class="stars-count">1</span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $ratingPercentages[1] }}%" aria-valuenow="{{ $ratingPercentages[1] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="rating-percentage">{{ $ratingPercentages[1] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                @if(auth()->check() && auth()->user()->hasCompletedSessionWith($specialist->id))
                    <div class="add-review-card card mb-4">
                        <div class="card-header">
                            <h3>أضف تقييمك</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('specialists.review.store', $specialist->id) }}" method="POST" id="review-form">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="rating">التقييم</label>
                                    <div class="rating-stars">
                                        <div class="stars-input">
                                            <input type="radio" name="rating" id="rating-5" value="5" {{ old('rating') == 5 ? 'checked' : '' }}>
                                            <label for="rating-5"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" name="rating" id="rating-4" value="4" {{ old('rating') == 4 ? 'checked' : '' }}>
                                            <label for="rating-4"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" name="rating" id="rating-3" value="3" {{ old('rating') == 3 ? 'checked' : '' }}>
                                            <label for="rating-3"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" name="rating" id="rating-2" value="2" {{ old('rating') == 2 ? 'checked' : '' }}>
                                            <label for="rating-2"><i class="fas fa-star"></i></label>
                                            
                                            <input type="radio" name="rating" id="rating-1" value="1" {{ old('rating') == 1 ? 'checked' : '' }}>
                                            <label for="rating-1"><i class="fas fa-star"></i></label>
                                        </div>
                                        <div class="rating-text">
                                            <span id="rating-text">اختر تقييمك</span>
                                        </div>
                                    </div>
                                    @error('rating')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="title">عنوان التقييم</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="عنوان يلخص تجربتك">
                                    @error('title')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="comment">تعليقك</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="5" placeholder="اكتب تجربتك مع المختص...">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>تقييم الجوانب المختلفة</label>
                                    <div class="aspect-ratings">
                                        <div class="aspect-rating-item">
                                            <span class="aspect-name">الكفاءة المهنية</span>
                                            <div class="aspect-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" name="professional_rating" id="professional-{{ $i }}" value="{{ $i }}" {{ old('professional_rating') == $i ? 'checked' : '' }}>
                                                    <label for="professional-{{ $i }}"><i class="fas fa-star"></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="aspect-rating-item">
                                            <span class="aspect-name">التواصل والتفاعل</span>
                                            <div class="aspect-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" name="communication_rating" id="communication-{{ $i }}" value="{{ $i }}" {{ old('communication_rating') == $i ? 'checked' : '' }}>
                                                    <label for="communication-{{ $i }}"><i class="fas fa-star"></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="aspect-rating-item">
                                            <span class="aspect-name">الالتزام بالمواعيد</span>
                                            <div class="aspect-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" name="punctuality_rating" id="punctuality-{{ $i }}" value="{{ $i }}" {{ old('punctuality_rating') == $i ? 'checked' : '' }}>
                                                    <label for="punctuality-{{ $i }}"><i class="fas fa-star"></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="aspect-rating-item">
                                            <span class="aspect-name">القيمة مقابل السعر</span>
                                            <div class="aspect-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" name="value_rating" id="value-{{ $i }}" value="{{ $i }}" {{ old('value_rating') == $i ? 'checked' : '' }}>
                                                    <label for="value-{{ $i }}"><i class="fas fa-star"></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="recommend" name="recommend" value="1" {{ old('recommend') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recommend">
                                            أنصح بهذا المختص للآخرين
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">إرسال التقييم</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif(auth()->check())
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <span>يمكنك إضافة تقييم فقط بعد إكمال جلسة مع هذا المختص.</span>
                        <a href="{{ route('booking.specialist', $specialist->id) }}" class="alert-link">احجز جلسة الآن</a>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <span>يجب تسجيل الدخول لإضافة تقييم.</span>
                        <a href="{{ route('login') }}" class="alert-link">تسجيل الدخول</a>
                    </div>
                @endif

                <div class="reviews-list card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>التقييمات ({{ $reviews->total() }})</h3>
                        <div class="reviews-filter">
                            <select class="form-select" id="reviews-sort">
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>الأحدث أولاً</option>
                                <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>الأعلى تقييماً</option>
                                <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>الأقل تقييماً</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($reviews->count() > 0)
                            <div class="reviews-container">
                                @foreach($reviews as $review)
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <img src="{{ $review->user->profile_photo_url }}" alt="{{ $review->user->name }}" class="reviewer-image">
                                                <div class="reviewer-details">
                                                    <h4 class="reviewer-name">{{ $review->user->name }}</h4>
                                                    <div class="review-date">{{ $review->created_at->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                <div class="stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="rating-value">{{ $review->rating }}.0</span>
                                            </div>
                                        </div>
                                        <div class="review-title">{{ $review->title }}</div>
                                        <div class="review-content">
                                            <p>{{ $review->comment }}</p>
                                        </div>
                                        @if($review->recommend)
                                            <div class="review-recommend">
                                                <i class="fas fa-thumbs-up"></i>
                                                <span>ينصح بهذا المختص</span>
                                            </div>
                                        @endif
                                        <div class="review-aspects">
                                            <div class="aspect-item">
                                                <span class="aspect-label">الكفاءة المهنية:</span>
                                                <div class="aspect-rating">
                                                    <div class="stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->professional_rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="aspect-item">
                                                <span class="aspect-label">التواصل والتفاعل:</span>
                                                <div class="aspect-rating">
                                                    <div class="stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->communication_rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="aspect-item">
                                                <span class="aspect-label">الالتزام بالمواعيد:</span>
                                                <div class="aspect-rating">
                                                    <div class="stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->punctuality_rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="aspect-item">
                                                <span class="aspect-label">القيمة مقابل السعر:</span>
                                                <div class="aspect-rating">
                                                    <div class="stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->value_rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="review-actions">
                                            <button class="btn btn-sm btn-outline-secondary helpful-btn" data-review-id="{{ $review->id }}">
                                                <i class="far fa-thumbs-up"></i>
                                                <span>مفيد ({{ $review->helpful_count }})</span>
                                            </button>
                                            @if(auth()->check() && auth()->id() == $review->user_id)
                                                <div class="review-owner-actions">
                                                    <a href="{{ route('specialists.review.edit', ['specialist' => $specialist->id, 'review' => $review->id]) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                        <span>تعديل</span>
                                                    </a>
                                                    <form action="{{ route('specialists.review.destroy', ['specialist' => $specialist->id, 'review' => $review->id]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا التقييم؟')">
                                                            <i class="fas fa-trash"></i>
                                                            <span>حذف</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                        @if($review->specialist_comment)
                                            <div class="specialist-response">
                                                <div class="response-header">
                                                    <img src="{{ $specialist->user->profile_photo_url }}" alt="{{ $specialist->user->name }}" class="specialist-image">
                                                    <div class="response-details">
                                                        <h5 class="specialist-name">{{ $specialist->user->name }} <span class="badge bg-primary">المختص</span></h5>
                                                        <div class="response-date">{{ $review->specialist_comment_at->format('d M Y') }}</div>
                                                    </div>
                                                </div>
                                                <div class="response-content">
                                                    <p>{{ $review->specialist_comment }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="pagination-container mt-4">
                                {{ $reviews->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="no-reviews">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <span>لا توجد تقييمات لهذا المختص حتى الآن. كن أول من يقيم!</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديث نص التقييم عند اختيار النجوم
        $('.rating-stars input[name="rating"]').on('change', function() {
            const ratingValue = $(this).val();
            const ratingTexts = {
                '5': 'ممتاز',
                '4': 'جيد جداً',
                '3': 'جيد',
                '2': 'مقبول',
                '1': 'سيء'
            };
            $('#rating-text').text(ratingTexts[ratingValue]);
        });

        // تغيير ترتيب التقييمات
        $('#reviews-sort').on('change', function() {
            const sortValue = $(this).val();
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', sortValue);
            window.location.href = currentUrl.toString();
        });

        // زر "مفيد"
        $('.helpful-btn').on('click', function() {
            const reviewId = $(this).data('review-id');
            const button = $(this);
            
            $.ajax({
                url: `/reviews/${reviewId}/helpful`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const helpfulCount = response.helpful_count;
                        button.find('span').text(`مفيد (${helpfulCount})`);
                        
                        if (response.is_helpful) {
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
                        alert('يجب تسجيل الدخول لتقييم مدى فائدة المراجعة');
                    } else {
                        alert('حدث خطأ أثناء معالجة طلبك');
                    }
                }
            });
        });
    });
</script>
@endsection

@section('styles')
<style>
    .specialists-review-page {
        padding: 40px 0;
    }
    
    .specialist-image {
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }
    
    .specialist-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .specialist-rating {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stars {
        color: #ffc107;
        margin-left: 5px;
    }
    
    .rating-count {
        color: #666;
        font-size: 12px;
    }
    
    .specialist-details {
        margin-top: 20px;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .detail-item i {
        width: 20px;
        margin-left: 10px;
        color: #6c757d;
    }
    
    .rating-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #f8f9fa;
        border: 3px solid #ffc107;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .rating-value {
        font-size: 24px;
        font-weight: bold;
        line-height: 1;
    }
    
    .rating-max {
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
        display: flex;
        align-items: center;
        width: 80px;
    }
    
    .stars-count {
        margin-left: 5px;
    }
    
    .progress {
        flex: 1;
        height: 10px;
        margin: 0 10px;
    }
    
    .rating-percentage {
        width: 40px;
        text-align: left;
        font-size: 12px;
    }
    
    .rating-stars {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .stars-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
    }
    
    .stars-input input {
        display: none;
    }
    
    .stars-input label {
        cursor: pointer;
        font-size: 30px;
        color: #ddd;
        margin: 0 5px;
    }
    
    .stars-input label:hover,
    .stars-input label:hover ~ label,
    .stars-input input:checked ~ label {
        color: #ffc107;
    }
    
    .rating-text {
        margin-top: 10px;
        font-size: 14px;
        color: #666;
    }
    
    .aspect-ratings {
        margin-top: 10px;
    }
    
    .aspect-rating-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .aspect-name {
        width: 150px;
    }
    
    .aspect-stars {
        display: flex;
        flex-direction: row-reverse;
    }
    
    .aspect-stars input {
        display: none;
    }
    
    .aspect-stars label {
        cursor: pointer;
        font-size: 18px;
        color: #ddd;
        margin: 0 2px;
    }
    
    .aspect-stars label:hover,
    .aspect-stars label:hover ~ label,
    .aspect-stars input:checked ~ label {
        color: #ffc107;
    }
    
    .review-item {
        border-bottom: 1px solid #eee;
        padding: 20px 0;
    }
    
    .review-item:last-child {
        border-bottom: none;
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }
    
    .reviewer-info {
        display: flex;
        align-items: center;
    }
    
    .reviewer-image {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-left: 10px;
    }
    
    .reviewer-name {
        margin-bottom: 5px;
        font-size: 16px;
    }
    
    .review-date {
        color: #666;
        font-size: 12px;
    }
    
    .review-title {
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .review-recommend {
        display: inline-block;
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 5px 10px;
        border-radius: 4px;
        margin: 10px 0;
    }
    
    .review-recommend i {
        margin-left: 5px;
    }
    
    .review-aspects {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        margin: 10px 0;
    }
    
    .aspect-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .aspect-label {
        width: 150px;
        font-size: 14px;
    }
    
    .aspect-rating .stars {
        font-size: 14px;
    }
    
    .review-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }
    
    .helpful-btn.active {
        background-color: #e8f5e9;
        color: #2e7d32;
        border-color: #2e7d32;
    }
    
    .specialist-response {
        background-color: #f0f8ff;
        padding: 15px;
        border-radius: 4px;
        margin-top: 15px;
    }
    
    .response-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .specialist-image {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
    }
    
    .specialist-name {
        margin-bottom: 5px;
        font-size: 16px;
    }
    
    .response-date {
        color: #666;
        font-size: 12px;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
    }
    
    @media (max-width: 767px) {
        .review-header {
            flex-direction: column;
        }
        
        .review-rating {
            margin-top: 10px;
        }
        
        .aspect-rating-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .aspect-name {
            margin-bottom: 5px;
        }
        
        .review-actions {
            flex-direction: column;
        }
        
        .review-owner-actions {
            margin-top: 10px;
        }
    }
</style>
@endsection
