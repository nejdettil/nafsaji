@extends('layouts.dashboard')

@section('title', 'الحجوزات - نفسجي للتمكين النفسي')

@section('content')
<div class="bookings-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">الحجوزات</h1>
                    <p class="dashboard-subtitle">إدارة جميع حجوزاتك للجلسات النفسية</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-quick-actions">
                        <a href="{{ route('specialists') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> حجز جلسة جديدة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bookings-content">
        <div class="container-fluid">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div class="booking-filters">
                        <ul class="nav nav-pills" id="bookingsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('status') == 'all' || !request('status') ? 'active' : '' }}" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="{{ request('status') == 'all' || !request('status') ? 'true' : 'false' }}">
                                    الكل
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('status') == 'upcoming' ? 'active' : '' }}" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="{{ request('status') == 'upcoming' ? 'true' : 'false' }}">
                                    القادمة
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="{{ request('status') == 'completed' ? 'true' : 'false' }}">
                                    المكتملة
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}" id="cancelled-tab" data-bs-toggle="pill" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="{{ request('status') == 'cancelled' ? 'true' : 'false' }}">
                                    الملغاة
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="booking-search">
                        <form action="{{ route('user.bookings') }}" method="GET" class="search-form">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="البحث عن حجز..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="dashboard-card-body">
                    <div class="tab-content" id="bookingsTabContent">
                        <!-- قسم جميع الحجوزات -->
                        <div class="tab-pane fade {{ request('status') == 'all' || !request('status') ? 'show active' : '' }}" id="all" role="tabpanel" aria-labelledby="all-tab">
                            @if(count($bookings) > 0)
                                <div class="bookings-list">
                                    @foreach($bookings as $booking)
                                        <div class="booking-item">
                                            <div class="booking-status {{ $booking->status }}">
                                                <span class="status-indicator"></span>
                                                <span class="status-text">
                                                    @if($booking->status == 'upcoming')
                                                        قادمة
                                                    @elseif($booking->status == 'completed')
                                                        مكتملة
                                                    @elseif($booking->status == 'cancelled')
                                                        ملغاة
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="booking-date">
                                                <div class="date-day">{{ \Carbon\Carbon::parse($booking->session_date)->format('d') }}</div>
                                                <div class="date-month">{{ \Carbon\Carbon::parse($booking->session_date)->translatedFormat('M') }}</div>
                                                <div class="date-year">{{ \Carbon\Carbon::parse($booking->session_date)->format('Y') }}</div>
                                            </div>
                                            <div class="booking-details">
                                                <h5 class="booking-service">{{ $booking->service->name }}</h5>
                                                <div class="booking-specialist">
                                                    <img src="{{ $booking->specialist->profile_image ? asset('storage/' . $booking->specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $booking->specialist->user->full_name }}" class="specialist-img">
                                                    <span>{{ $booking->specialist->user->full_name }}</span>
                                                </div>
                                                <div class="booking-info">
                                                    <div class="booking-time">
                                                        <i class="far fa-clock"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->session_time)->format('h:i A') }}</span>
                                                    </div>
                                                    <div class="booking-duration">
                                                        <i class="fas fa-hourglass-half"></i>
                                                        <span>{{ $booking->service->duration }} دقيقة</span>
                                                    </div>
                                                    <div class="booking-type">
                                                        <i class="{{ $booking->session_type == 'online' ? 'fas fa-video' : 'fas fa-map-marker-alt' }}"></i>
                                                        <span>{{ $booking->session_type == 'online' ? 'عبر الإنترنت' : 'حضوري' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="booking-price">
                                                <div class="price-amount">{{ $booking->total_amount }} {{ $booking->currency }}</div>
                                                <div class="payment-status {{ $booking->payment_status }}">
                                                    @if($booking->payment_status == 'paid')
                                                        <i class="fas fa-check-circle"></i> مدفوع
                                                    @elseif($booking->payment_status == 'pending')
                                                        <i class="fas fa-clock"></i> قيد الانتظار
                                                    @elseif($booking->payment_status == 'refunded')
                                                        <i class="fas fa-undo"></i> مسترجع
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="booking-actions">
                                                <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> تفاصيل
                                                </a>
                                                
                                                @if($booking->status == 'upcoming')
                                                    @if($booking->session_type == 'online')
                                                        <a href="{{ route('session.join', $booking->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-video"></i> انضمام
                                                        </a>
                                                    @endif
                                                    
                                                    @if(\Carbon\Carbon::parse($booking->session_date . ' ' . $booking->session_time)->diffInHours(now()) > 24)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelBookingModal" data-booking-id="{{ $booking->id }}">
                                                            <i class="fas fa-times"></i> إلغاء
                                                        </button>
                                                    @endif
                                                    
                                                    <a href="{{ route('booking.reschedule', $booking->id) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-calendar-alt"></i> إعادة جدولة
                                                    </a>
                                                @endif
                                                
                                                @if($booking->status == 'completed' && !$booking->has_review)
                                                    <a href="{{ route('booking.review', $booking->id) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-star"></i> تقييم
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="pagination-container">
                                    {{ $bookings->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <img src="{{ asset('assets/images/empty-bookings.svg') }}" alt="لا توجد حجوزات" class="empty-state-img">
                                    <h6>لا توجد حجوزات</h6>
                                    <p>لم تقم بحجز أي جلسات حتى الآن.</p>
                                    <a href="{{ route('booking.create') }}" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus"></i> حجز جلسة جديدة
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <!-- قسم الحجوزات القادمة -->
                        <div class="tab-pane fade {{ request('status') == 'upcoming' ? 'show active' : '' }}" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                            @if(count($upcomingBookings) > 0)
                                <div class="bookings-list">
                                    @foreach($upcomingBookings as $booking)
                                        <div class="booking-item">
                                            <div class="booking-status upcoming">
                                                <span class="status-indicator"></span>
                                                <span class="status-text">قادمة</span>
                                            </div>
                                            <div class="booking-date">
                                                <div class="date-day">{{ \Carbon\Carbon::parse($booking->session_date)->format('d') }}</div>
                                                <div class="date-month">{{ \Carbon\Carbon::parse($booking->session_date)->translatedFormat('M') }}</div>
                                                <div class="date-year">{{ \Carbon\Carbon::parse($booking->session_date)->format('Y') }}</div>
                                            </div>
                                            <div class="booking-details">
                                                <h5 class="booking-service">{{ $booking->service->name }}</h5>
                                                <div class="booking-specialist">
                                                    <img src="{{ $booking->specialist->profile_image ? asset('storage/' . $booking->specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $booking->specialist->user->full_name }}" class="specialist-img">
                                                    <span>{{ $booking->specialist->user->full_name }}</span>
                                                </div>
                                                <div class="booking-info">
                                                    <div class="booking-time">
                                                        <i class="far fa-clock"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->session_time)->format('h:i A') }}</span>
                                                    </div>
                                                    <div class="booking-duration">
                                                        <i class="fas fa-hourglass-half"></i>
                                                        <span>{{ $booking->service->duration }} دقيقة</span>
                                                    </div>
                                                    <div class="booking-type">
                                                        <i class="{{ $booking->session_type == 'online' ? 'fas fa-video' : 'fas fa-map-marker-alt' }}"></i>
                                                        <span>{{ $booking->session_type == 'online' ? 'عبر الإنترنت' : 'حضوري' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="booking-price">
                                                <div class="price-amount">{{ $booking->total_amount }} {{ $booking->currency }}</div>
                                                <div class="payment-status {{ $booking->payment_status }}">
                                                    @if($booking->payment_status == 'paid')
                                                        <i class="fas fa-check-circle"></i> مدفوع
                                                    @elseif($booking->payment_status == 'pending')
                                                        <i class="fas fa-clock"></i> قيد الانتظار
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="booking-actions">
                                                <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> تفاصيل
                                                </a>
                                                
                                                @if($booking->session_type == 'online')
                                                    <a href="{{ route('session.join', $booking->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-video"></i> انضمام
                                                    </a>
                                                @endif
                                                
                                                @if(\Carbon\Carbon::parse($booking->session_date . ' ' . $booking->session_time)->diffInHours(now()) > 24)
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelBookingModal" data-booking-id="{{ $booking->id }}">
                                                        <i class="fas fa-times"></i> إلغاء
                                                    </button>
                                                @endif
                                                
                                                <a href="{{ route('booking.reschedule', $booking->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-calendar-alt"></i> إعادة جدولة
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="pagination-container">
                                    {{ $upcomingBookings->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <img src="{{ asset('assets/images/empty-upcoming.svg') }}" alt="لا توجد حجوزات قادمة" class="empty-state-img">
                                    <h6>لا توجد حجوزات قادمة</h6>
                                    <p>لم تقم بحجز أي جلسات قادمة حتى الآن.</p>
                                    <a href="{{ route('booking.create') }}" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus"></i> حجز جلسة جديدة
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <!-- قسم الحجوزات المكتملة -->
                        <div class="tab-pane fade {{ request('status') == 'completed' ? 'show active' : '' }}" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                            @if(count($completedBookings) > 0)
                                <div class="bookings-list">
                                    @foreach($completedBookings as $booking)
                                        <div class="booking-item">
                                            <div class="booking-status completed">
                                                <span class="status-indicator"></span>
                                                <span class="status-text">مكتملة</span>
                                            </div>
                                            <div class="booking-date">
                                                <div class="date-day">{{ \Carbon\Carbon::parse($booking->session_date)->format('d') }}</div>
                                                <div class="date-month">{{ \Carbon\Carbon::parse($booking->session_date)->translatedFormat('M') }}</div>
                                                <div class="date-year">{{ \Carbon\Carbon::parse($booking->session_date)->format('Y') }}</div>
                                            </div>
                                            <div class="booking-details">
                                                <h5 class="booking-service">{{ $booking->service->name }}</h5>
                                                <div class="booking-specialist">
                                                    <img src="{{ $booking->specialist->profile_image ? asset('storage/' . $booking->specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $booking->specialist->user->full_name }}" class="specialist-img">
                                                    <span>{{ $booking->specialist->user->full_name }}</span>
                                                </div>
                                                <div class="booking-info">
                                                    <div class="booking-time">
                                                        <i class="far fa-clock"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->session_time)->format('h:i A') }}</span>
                                                    </div>
                                                    <div class="booking-duration">
                                                        <i class="fas fa-hourglass-half"></i>
                                                        <span>{{ $booking->service->duration }} دقيقة</span>
                                                    </div>
                                                    <div class="booking-type">
                                                        <i class="{{ $booking->session_type == 'online' ? 'fas fa-video' : 'fas fa-map-marker-alt' }}"></i>
                                                        <span>{{ $booking->session_type == 'online' ? 'عبر الإنترنت' : 'حضوري' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="booking-price">
                                                <div class="price-amount">{{ $booking->total_amount }} {{ $booking->currency }}</div>
                                                <div class="payment-status {{ $booking->payment_status }}">
                                                    @if($booking->payment_status == 'paid')
                                                        <i class="fas fa-check-circle"></i> مدفوع
                                                    @elseif($booking->payment_status == 'refunded')
                                                        <i class="fas fa-undo"></i> مسترجع
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="booking-actions">
                                                <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> تفاصيل
                                                </a>
                                                
                                                @if(!$booking->has_review)
                                                    <a href="{{ route('booking.review', $booking->id) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-star"></i> تقييم
                                                    </a>
                                                @endif
                                                
                                                <a href="{{ route('booking.create', ['specialist_id' => $booking->specialist_id]) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-calendar-plus"></i> حجز جديد
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="pagination-container">
                                    {{ $completedBookings->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <img src="{{ asset('assets/images/empty-completed.svg') }}" alt="لا توجد حجوزات مكتملة" class="empty-state-img">
                                    <h6>لا توجد حجوزات مكتملة</h6>
                                    <p>لم تكمل أي جلسات حتى الآن.</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- قسم الحجوزات الملغاة -->
                        <div class="tab-pane fade {{ request('status') == 'cancelled' ? 'show active' : '' }}" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                            @if(count($cancelledBookings) > 0)
                                <div class="bookings-list">
                                    @foreach($cancelledBookings as $booking)
                                        <div class="booking-item">
                                            <div class="booking-status cancelled">
                                                <span class="status-indicator"></span>
                                                <span class="status-text">ملغاة</span>
                                            </div>
                                            <div class="booking-date">
                                                <div class="date-day">{{ \Carbon\Carbon::parse($booking->session_date)->format('d') }}</div>
                                                <div class="date-month">{{ \Carbon\Carbon::parse($booking->session_date)->translatedFormat('M') }}</div>
                                                <div class="date-year">{{ \Carbon\Carbon::parse($booking->session_date)->format('Y') }}</div>
                                            </div>
                                            <div class="booking-details">
                                                <h5 class="booking-service">{{ $booking->service->name }}</h5>
                                                <div class="booking-specialist">
                                                    <img src="{{ $booking->specialist->profile_image ? asset('storage/' . $booking->specialist->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $booking->specialist->user->full_name }}" class="specialist-img">
                                                    <span>{{ $booking->specialist->user->full_name }}</span>
                                                </div>
                                                <div class="booking-info">
                                                    <div class="booking-time">
                                                        <i class="far fa-clock"></i>
                                                        <span>{{ \Carbon\Carbon::parse($booking->session_time)->format('h:i A') }}</span>
                                                    </div>
                                                    <div class="booking-duration">
                                                        <i class="fas fa-hourglass-half"></i>
                                                        <span>{{ $booking->service->duration }} دقيقة</span>
                                                    </div>
                                                    <div class="booking-type">
                                                        <i class="{{ $booking->session_type == 'online' ? 'fas fa-video' : 'fas fa-map-marker-alt' }}"></i>
                                                        <span>{{ $booking->session_type == 'online' ? 'عبر الإنترنت' : 'حضوري' }}</span>
                                                    </div>
                                                </div>
                                                <div class="booking-cancel-reason">
                                                    <i class="fas fa-info-circle"></i>
                                                    <span>سبب الإلغاء: {{ $booking->cancellation_reason ?: 'غير محدد' }}</span>
                                                </div>
                                            </div>
                                            <div class="booking-price">
                                                <div class="price-amount">{{ $booking->total_amount }} {{ $booking->currency }}</div>
                                                <div class="payment-status {{ $booking->payment_status }}">
                                                    @if($booking->payment_status == 'refunded')
                                                        <i class="fas fa-undo"></i> مسترجع
                                                    @elseif($booking->payment_status == 'paid')
                                                        <i class="fas fa-check-circle"></i> مدفوع
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="booking-actions">
                                                <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> تفاصيل
                                                </a>
                                                
                                                <a href="{{ route('booking.create', ['specialist_id' => $booking->specialist_id]) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-calendar-plus"></i> حجز جديد
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="pagination-container">
                                    {{ $cancelledBookings->appends(request()->query())->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <img src="{{ asset('assets/images/empty-cancelled.svg') }}" alt="لا توجد حجوزات ملغاة" class="empty-state-img">
                                    <h6>لا توجد حجوزات ملغاة</h6>
                                    <p>ليس لديك أي حجوزات ملغاة.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إلغاء الحجز -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">تأكيد إلغاء الحجز</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إلغاء هذا الحجز؟</p>
                <p class="text-danger">ملاحظة: قد يتم تطبيق سياسة الإلغاء وفقاً لشروط الخدمة.</p>
                
                <form id="cancelBookingForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="form-group">
                        <label for="cancellation_reason">سبب الإلغاء (اختياري)</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" placeholder="يرجى ذكر سبب الإلغاء..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmCancelButton">تأكيد الإلغاء</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .bookings-page {
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
    
    .dashboard-quick-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .bookings-content {
        margin-bottom: 30px;
    }
    
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
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .booking-filters .nav-pills {
        background-color: #f8f9fa;
        border-radius: 50px;
        padding: 5px;
    }
    
    .booking-filters .nav-link {
        border-radius: 50px;
        padding: 8px 20px;
        margin: 0 5px;
        color: #333;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .booking-filters .nav-link.active {
        background-color: #6a1b9a;
        color: #fff;
    }
    
    .booking-search .search-form {
        display: flex;
        align-items: center;
    }
    
    .booking-search .form-control {
        height: 40px;
        border-radius: 20px 0 0 20px;
        border-right: none;
    }
    
    .booking-search .btn {
        border-radius: 0 20px 20px 0;
        height: 40px;
        padding: 0 15px;
    }
    
    .dashboard-card-body {
        padding: 20px;
    }
    
    /* قائمة الحجوزات */
    .bookings-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .booking-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        position: relative;
        flex-wrap: wrap;
    }
    
    .booking-item:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    
    .booking-status {
        display: flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 15px;
    }
    
    .booking-status.upcoming {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .booking-status.completed {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .booking-status.cancelled {
        background-color: #ffebee;
        color: #d32f2f;
    }
    
    .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-left: 5px;
    }
    
    .booking-status.upcoming .status-indicator {
        background-color: #1976d2;
    }
    
    .booking-status.completed .status-indicator {
        background-color: #388e3c;
    }
    
    .booking-status.cancelled .status-indicator {
        background-color: #d32f2f;
    }
    
    .booking-date {
        width: 70px;
        height: 70px;
        background-color: #fff;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .date-day {
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
        color: #333;
    }
    
    .date-month {
        font-size: 14px;
        color: #666;
        margin-bottom: 2px;
    }
    
    .date-year {
        font-size: 12px;
        color: #888;
    }
    
    .booking-details {
        flex: 1;
        min-width: 200px;
    }
    
    .booking-service {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .booking-specialist {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .specialist-img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-left: 5px;
        object-fit: cover;
    }
    
    .booking-info {
        display: flex;
        gap: 15px;
        margin-bottom: 5px;
    }
    
    .booking-time, .booking-duration, .booking-type {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: #666;
    }
    
    .booking-time i, .booking-duration i, .booking-type i {
        margin-left: 5px;
        color: #6a1b9a;
    }
    
    .booking-cancel-reason {
        font-size: 14px;
        color: #d32f2f;
        margin-top: 5px;
        display: flex;
        align-items: center;
    }
    
    .booking-cancel-reason i {
        margin-left: 5px;
    }
    
    .booking-price {
        margin: 0 15px;
        text-align: center;
    }
    
    .price-amount {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .payment-status {
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
    }
    
    .payment-status.paid {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .payment-status.pending {
        background-color: #fff8e1;
        color: #ffa000;
    }
    
    .payment-status.refunded {
        background-color: #f3e5f5;
        color: #8e24aa;
    }
    
    .payment-status i {
        margin-left: 5px;
    }
    
    .booking-actions {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    /* حالة فارغة */
    .empty-state {
        text-align: center;
        padding: 30px;
    }
    
    .empty-state-img {
        max-width: 200px;
        margin-bottom: 20px;
    }
    
    .empty-state h6 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .empty-state p {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }
    
    /* التصفح */
    .pagination-container {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }
    
    /* مودال إلغاء الحجز */
    .modal-content {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    
    .modal-title {
        font-weight: 600;
        color: #333;
    }
    
    .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .dashboard-header {
            padding: 20px 0;
        }
        
        .dashboard-quick-actions {
            margin-top: 15px;
            justify-content: flex-start;
        }
        
        .dashboard-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .booking-filters, .booking-search {
            width: 100%;
        }
        
        .booking-filters .nav-pills {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        
        .booking-filters .nav-link {
            padding: 8px 10px;
            margin: 0 2px;
            font-size: 14px;
        }
        
        .booking-search .search-form {
            width: 100%;
        }
    }
    
    @media (max-width: 767px) {
        .booking-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .booking-status {
            align-self: flex-start;
            margin-bottom: 10px;
            margin-left: 0;
        }
        
        .booking-date {
            margin-bottom: 10px;
            margin-left: 0;
            align-self: flex-start;
        }
        
        .booking-details {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .booking-info {
            flex-direction: column;
            gap: 5px;
        }
        
        .booking-price {
            margin: 0 0 15px 0;
            align-self: flex-start;
            text-align: left;
        }
        
        .booking-actions {
            align-self: flex-end;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديث URL عند تغيير التبويب
        $('#bookingsTab button').on('click', function() {
            const tabId = $(this).attr('id');
            const status = tabId.replace('-tab', '');
            
            // تحديث URL بدون إعادة تحميل الصفحة
            const url = new URL(window.location);
            url.searchParams.set('status', status);
            window.history.pushState({}, '', url);
        });
        
        // تهيئة مودال إلغاء الحجز
        $('#cancelBookingModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const bookingId = button.data('booking-id');
            const form = $('#cancelBookingForm');
            
            form.attr('action', `/bookings/${bookingId}`);
        });
        
        // إرسال نموذج إلغاء الحجز
        $('#confirmCancelButton').on('click', function() {
            $('#cancelBookingForm').submit();
        });
    });
</script>
@endsection
