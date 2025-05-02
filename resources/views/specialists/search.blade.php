@extends('layouts.app')

@section('title', 'البحث عن المختصين - نفسجي للتمكين النفسي')

@section('content')
<div class="specialists-search-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header">
                    <h1 class="main-title">البحث عن المختصين</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('specialists.index') }}">المختصين</a></li>
                            <li class="breadcrumb-item active" aria-current="page">البحث عن المختصين</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="search-filters card">
                    <div class="card-header">
                        <h3>خيارات البحث</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('specialists.search') }}" method="GET" id="search-form">
                            <div class="form-group mb-3">
                                <label for="keyword">كلمة البحث</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" value="{{ request('keyword') }}" placeholder="اسم المختص أو التخصص...">
                            </div>

                            <div class="form-group mb-3">
                                <label for="specialization">التخصص</label>
                                <select class="form-control" id="specialization" name="specialization">
                                    <option value="">جميع التخصصات</option>
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization->id }}" {{ request('specialization') == $specialization->id ? 'selected' : '' }}>
                                            {{ $specialization->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="gender">الجنس</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">الكل</option>
                                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="rating">التقييم</label>
                                <select class="form-control" id="rating" name="rating">
                                    <option value="">الكل</option>
                                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 نجوم</option>
                                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 نجوم وأعلى</option>
                                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 نجوم وأعلى</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="availability">التوفر</label>
                                <select class="form-control" id="availability" name="availability">
                                    <option value="">الكل</option>
                                    <option value="today" {{ request('availability') == 'today' ? 'selected' : '' }}>متاح اليوم</option>
                                    <option value="week" {{ request('availability') == 'week' ? 'selected' : '' }}>متاح هذا الأسبوع</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">السعر</label>
                                <div class="price-range-slider">
                                    <div class="range-slider">
                                        <input type="range" class="form-range" id="price_min" name="price_min" min="0" max="1000" step="50" value="{{ request('price_min', 0) }}">
                                        <input type="range" class="form-range" id="price_max" name="price_max" min="0" max="1000" step="50" value="{{ request('price_max', 1000) }}">
                                    </div>
                                    <div class="price-display">
                                        <span id="price_min_display">{{ request('price_min', 0) }}</span> - 
                                        <span id="price_max_display">{{ request('price_max', 1000) }}</span> ريال
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="language">اللغة</label>
                                <select class="form-control" id="language" name="language">
                                    <option value="">الكل</option>
                                    <option value="arabic" {{ request('language') == 'arabic' ? 'selected' : '' }}>العربية</option>
                                    <option value="english" {{ request('language') == 'english' ? 'selected' : '' }}>الإنجليزية</option>
                                    <option value="french" {{ request('language') == 'french' ? 'selected' : '' }}>الفرنسية</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="sort">ترتيب النتائج</label>
                                <select class="form-control" id="sort" name="sort">
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>التقييم (الأعلى أولاً)</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>السعر (الأقل أولاً)</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>السعر (الأعلى أولاً)</option>
                                    <option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>الخبرة (الأكثر أولاً)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary w-100">بحث</button>
                            </div>
                            <div class="form-group mt-2">
                                <a href="{{ route('specialists.search') }}" class="btn btn-outline-secondary w-100">إعادة ضبط</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="search-results">
                    <div class="search-summary mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3>نتائج البحث ({{ $specialists->total() }})</h3>
                                <p>عرض {{ $specialists->firstItem() ?? 0 }} - {{ $specialists->lastItem() ?? 0 }} من {{ $specialists->total() }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="view-options">
                                    <button class="btn btn-sm btn-outline-secondary view-grid active" data-view="grid">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary view-list" data-view="list">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($specialists->count() > 0)
                        <div class="specialists-grid" id="specialists-container">
                            <div class="row">
                                @foreach($specialists as $specialist)
                                    <div class="col-md-6 mb-4">
                                        <div class="specialist-card">
                                            <div class="card">
                                                <div class="specialist-header">
                                                    <div class="specialist-image">
                                                        <img src="{{ $specialist->user->profile_photo_url }}" alt="{{ $specialist->user->name }}" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div class="specialist-info">
                                                        <h4 class="specialist-name">
                                                            <a href="{{ route('specialists.show', $specialist->id) }}">{{ $specialist->user->name }}</a>
                                                        </h4>
                                                        <p class="specialist-title">{{ $specialist->title }}</p>
                                                        <div class="specialist-rating">
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
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="specialist-details">
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
                                                    <div class="specialist-bio">
                                                        <p>{{ Str::limit($specialist->bio, 150) }}</p>
                                                    </div>
                                                    <div class="specialist-actions">
                                                        <a href="{{ route('specialists.show', $specialist->id) }}" class="btn btn-outline-primary">عرض الملف الشخصي</a>
                                                        <a href="{{ route('specialists.book', $specialist->id) }}" class="btn btn-primary">حجز جلسة</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="pagination-container">
                            {{ $specialists->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="no-results">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span>لم يتم العثور على نتائج مطابقة لمعايير البحث. يرجى تعديل معايير البحث والمحاولة مرة أخرى.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديث عرض نطاق السعر
        $('#price_min, #price_max').on('input', function() {
            $('#price_min_display').text($('#price_min').val());
            $('#price_max_display').text($('#price_max').val());
        });

        // تبديل طريقة العرض (شبكة/قائمة)
        $('.view-options button').on('click', function() {
            const viewType = $(this).data('view');
            $('.view-options button').removeClass('active');
            $(this).addClass('active');
            
            if (viewType === 'grid') {
                $('#specialists-container').removeClass('specialists-list').addClass('specialists-grid');
            } else {
                $('#specialists-container').removeClass('specialists-grid').addClass('specialists-list');
            }
        });

        // تحديث النتائج تلقائياً عند تغيير أي من خيارات البحث
        $('#specialization, #gender, #rating, #availability, #language, #sort').on('change', function() {
            $('#search-form').submit();
        });
    });
</script>
@endsection

@section('styles')
<style>
    .specialists-search-page {
        padding: 40px 0;
    }
    
    .search-filters {
        position: sticky;
        top: 20px;
    }
    
    .price-range-slider {
        margin-top: 10px;
    }
    
    .price-display {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }
    
    .specialists-grid .specialist-card {
        height: 100%;
    }
    
    .specialists-list .row {
        display: block;
    }
    
    .specialists-list .col-md-6 {
        max-width: 100%;
        flex: 0 0 100%;
    }
    
    .specialists-list .specialist-card .card {
        display: flex;
        flex-direction: row;
    }
    
    .specialists-list .specialist-header {
        width: 30%;
        padding: 20px;
        border-right: 1px solid #eee;
    }
    
    .specialists-list .card-body {
        width: 70%;
    }
    
    .specialist-header {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .specialist-image {
        width: 80px;
        height: 80px;
        margin-left: 15px;
    }
    
    .specialist-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .specialist-info {
        flex: 1;
    }
    
    .specialist-name {
        margin-bottom: 5px;
        font-size: 18px;
    }
    
    .specialist-title {
        color: #666;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .specialist-rating {
        display: flex;
        align-items: center;
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
        margin-bottom: 15px;
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
    
    .specialist-bio {
        margin-bottom: 15px;
        color: #666;
    }
    
    .specialist-actions {
        display: flex;
        justify-content: space-between;
    }
    
    .view-options {
        display: inline-block;
    }
    
    .pagination-container {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }
    
    .no-results {
        padding: 40px 0;
        text-align: center;
    }
    
    @media (max-width: 767px) {
        .specialists-list .specialist-card .card {
            flex-direction: column;
        }
        
        .specialists-list .specialist-header,
        .specialists-list .card-body {
            width: 100%;
        }
        
        .specialist-actions {
            flex-direction: column;
        }
        
        .specialist-actions .btn {
            margin-bottom: 10px;
        }
    }
</style>
@endsection
