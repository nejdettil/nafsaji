@extends('layouts.dashboard')

@section('title', 'تفاصيل الحجز')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">تفاصيل الحجز #{{ $booking->id ?? 'N/A' }}</h4>
                    <p class="card-category">معلومات كاملة عن الحجز</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="booking-info-card">
                                <h5 class="info-title">معلومات الحجز</h5>
                                <div class="info-item">
                                    <span class="info-label">رقم الحجز:</span>
                                    <span class="info-value">{{ $booking->id ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">تاريخ الحجز:</span>
                                    <span class="info-value">{{ $booking->created_at ? $booking->created_at->format('Y-m-d H:i') : 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">تاريخ الجلسة:</span>
                                    <span class="info-value">{{ $booking->preferred_date ? date('Y-m-d H:i', strtotime($booking->preferred_date)) : 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">حالة الحجز:</span>
                                    <span class="info-value status-badge status-{{ $booking->status ?? 'pending' }}">
                                        @if($booking->status == 'pending')
                                            قيد الانتظار
                                        @elseif($booking->status == 'confirmed')
                                            مؤكد
                                        @elseif($booking->status == 'completed')
                                            مكتمل
                                        @elseif($booking->status == 'cancelled')
                                            ملغي
                                        @else
                                            {{ $booking->status ?? 'N/A' }}
                                        @endif
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">نوع الجلسة:</span>
                                    <span class="info-value">{{ $booking->session_type ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">مدة الجلسة:</span>
                                    <span class="info-value">{{ $booking->duration ?? 'N/A' }} دقيقة</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="booking-info-card">
                                <h5 class="info-title">معلومات المختص</h5>
                                <div class="specialist-info">
                                    <div class="specialist-avatar">
                                        <img src="{{ $booking->specialist->user->profile_image ? asset($booking->specialist->user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $booking->specialist->user->name ?? 'المختص' }}">
                                    </div>
                                    <div class="specialist-details">
                                        <div class="info-item">
                                            <span class="info-label">اسم المختص:</span>
                                            <span class="info-value">{{ $booking->specialist->user->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">التخصص:</span>
                                            <span class="info-value">{{ $booking->specialist->specialization ?? 'N/A' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">البريد الإلكتروني:</span>
                                            <span class="info-value">{{ $booking->specialist->user->email ?? 'N/A' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">رقم الهاتف:</span>
                                            <span class="info-value">{{ $booking->specialist->user->phone ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="booking-info-card">
                                <h5 class="info-title">معلومات الخدمة</h5>
                                <div class="info-item">
                                    <span class="info-label">اسم الخدمة:</span>
                                    <span class="info-value">{{ $booking->service->name ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">وصف الخدمة:</span>
                                    <span class="info-value">{{ $booking->service->description ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">السعر:</span>
                                    <span class="info-value">{{ $booking->service->price ?? 'N/A' }} ريال</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="booking-info-card">
                                <h5 class="info-title">معلومات الدفع</h5>
                                @if(isset($booking->payment) && $booking->payment)
                                    <div class="info-item">
                                        <span class="info-label">رقم الدفع:</span>
                                        <span class="info-value">{{ $booking->payment->id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">تاريخ الدفع:</span>
                                        <span class="info-value">{{ $booking->payment->created_at ? $booking->payment->created_at->format('Y-m-d H:i') : 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">المبلغ:</span>
                                        <span class="info-value">{{ $booking->payment->amount ?? 'N/A' }} ريال</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">طريقة الدفع:</span>
                                        <span class="info-value">{{ $booking->payment->payment_method ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">حالة الدفع:</span>
                                        <span class="info-value status-badge status-{{ $booking->payment->status ?? 'pending' }}">
                                            @if($booking->payment->status == 'pending')
                                                قيد الانتظار
                                            @elseif($booking->payment->status == 'completed')
                                                مكتمل
                                            @elseif($booking->payment->status == 'failed')
                                                فاشل
                                            @else
                                                {{ $booking->payment->status ?? 'N/A' }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('user.payments.show', $booking->payment->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> عرض تفاصيل الدفع
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> لم يتم العثور على معلومات الدفع لهذا الحجز.
                                    </div>
                                    @if($booking->status == 'pending')
                                        <a href="#" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                            <i class="fas fa-credit-card"></i> إتمام الدفع
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="booking-info-card">
                                <h5 class="info-title">ملاحظات</h5>
                                <div class="notes-content">
                                    @if(isset($booking->notes) && $booking->notes)
                                        {{ $booking->notes }}
                                    @else
                                        <p class="text-muted">لا توجد ملاحظات لهذا الحجز.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="booking-actions">
                                <a href="{{ route('user.bookings') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> العودة إلى الحجوزات
                                </a>
                                
                                @if($booking->status == 'pending')
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">
                                        <i class="fas fa-times"></i> إلغاء الحجز
                                    </button>
                                @endif
                                
                                @if($booking->status == 'confirmed' && (!isset($booking->session) || !$booking->session))
                                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#joinSessionModal">
                                        <i class="fas fa-video"></i> الانضمام إلى الجلسة
                                    </a>
                                @endif
                                
                                @if($booking->status == 'completed' && (!isset($booking->review) || !$booking->review))
                                    <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                        <i class="fas fa-star"></i> تقييم الجلسة
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: إلغاء الحجز -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">تأكيد إلغاء الحجز</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إلغاء هذا الحجز؟</p>
                <p class="text-danger">ملاحظة: قد يتم تطبيق سياسة الإلغاء وفقاً لشروط الخدمة.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <form action="{{ route('user.bookings.destroy', $booking->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: الانضمام إلى الجلسة -->
<div class="modal fade" id="joinSessionModal" tabindex="-1" aria-labelledby="joinSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="joinSessionModalLabel">الانضمام إلى الجلسة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>أنت على وشك الانضمام إلى جلستك مع المختص {{ $booking->specialist->user->name ?? 'N/A' }}.</p>
                <p>يرجى التأكد من توفر اتصال إنترنت مستقر وبيئة هادئة للجلسة.</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> مدة الجلسة: {{ $booking->duration ?? 'N/A' }} دقيقة
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" class="btn btn-primary">انضمام الآن</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: تقييم الجلسة -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">تقييم الجلسة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="rating">التقييم:</label>
                        <div class="rating-stars">
                            <div class="rating-container">
                                <input type="radio" id="star5" name="rating" value="5" /><label for="star5"></label>
                                <input type="radio" id="star4" name="rating" value="4" /><label for="star4"></label>
                                <input type="radio" id="star3" name="rating" value="3" /><label for="star3"></label>
                                <input type="radio" id="star2" name="rating" value="2" /><label for="star2"></label>
                                <input type="radio" id="star1" name="rating" value="1" /><label for="star1"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="comment">تعليقك:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="اكتب تعليقك هنا..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">إرسال التقييم</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .booking-info-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 20px;
        height: 100%;
    }
    
    .info-title {
        color: #6a1b9a;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .info-item {
        margin-bottom: 10px;
        display: flex;
        flex-wrap: wrap;
    }
    
    .info-label {
        font-weight: 600;
        color: #555;
        width: 120px;
    }
    
    .info-value {
        color: #333;
        flex: 1;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-pending {
        background-color: #ffeeba;
        color: #856404;
    }
    
    .status-confirmed {
        background-color: #c3e6cb;
        color: #155724;
    }
    
    .status-completed {
        background-color: #b8daff;
        color: #004085;
    }
    
    .status-cancelled {
        background-color: #f5c6cb;
        color: #721c24;
    }
    
    .specialist-info {
        display: flex;
        align-items: flex-start;
    }
    
    .specialist-avatar {
        width: 80px;
        height: 80px;
        margin-right: 15px;
        border-radius: 50%;
        overflow: hidden;
    }
    
    .specialist-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .specialist-details {
        flex: 1;
    }
    
    .notes-content {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 5px;
        min-height: 100px;
    }
    
    .booking-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    /* تصميم نجوم التقييم */
    .rating-stars {
        display: flex;
        justify-content: center;
        margin: 15px 0;
    }
    
    .rating-container {
        display: flex;
        flex-direction: row-reverse;
        font-size: 30px;
    }
    
    .rating-container input {
        display: none;
    }
    
    .rating-container label {
        cursor: pointer;
        color: #ccc;
        padding: 0 5px;
    }
    
    .rating-container label:before {
        content: '\f005';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
    }
    
    .rating-container input:checked ~ label {
        color: #ffc107;
    }
    
    .rating-container label:hover,
    .rating-container label:hover ~ label {
        color: #ffc107;
    }
    
    @media (max-width: 767px) {
        .info-item {
            flex-direction: column;
        }
        
        .info-label {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .booking-actions {
            flex-direction: column;
            gap: 10px;
        }
        
        .booking-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تنفيذ أي سكريبت خاص بالصفحة هنا
    });
</script>
@endsection
