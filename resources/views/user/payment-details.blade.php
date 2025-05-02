@extends('layouts.dashboard')

@section('title', 'تفاصيل الدفع')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">تفاصيل الدفع #{{ $payment->id ?? 'N/A' }}</h4>
                    <p class="card-category">معلومات كاملة عن عملية الدفع</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-info-card">
                                <h5 class="info-title">معلومات الدفع</h5>
                                <div class="info-item">
                                    <span class="info-label">رقم الدفع:</span>
                                    <span class="info-value">{{ $payment->id ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">تاريخ الدفع:</span>
                                    <span class="info-value">{{ $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">المبلغ:</span>
                                    <span class="info-value">{{ $payment->amount ?? 'N/A' }} ريال</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">طريقة الدفع:</span>
                                    <span class="info-value">{{ $payment->payment_method ?? 'N/A' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">حالة الدفع:</span>
                                    <span class="info-value status-badge status-{{ $payment->status ?? 'pending' }}">
                                        @if($payment->status == 'pending')
                                            قيد الانتظار
                                        @elseif($payment->status == 'completed')
                                            مكتمل
                                        @elseif($payment->status == 'failed')
                                            فاشل
                                        @else
                                            {{ $payment->status ?? 'N/A' }}
                                        @endif
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">رقم المعاملة:</span>
                                    <span class="info-value">{{ $payment->transaction_id ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-info-card">
                                <h5 class="info-title">معلومات الحجز</h5>
                                @if(isset($payment->booking) && $payment->booking)
                                    <div class="info-item">
                                        <span class="info-label">رقم الحجز:</span>
                                        <span class="info-value">{{ $payment->booking->id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">تاريخ الحجز:</span>
                                        <span class="info-value">{{ $payment->booking->created_at ? $payment->booking->created_at->format('Y-m-d H:i') : 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">تاريخ الجلسة:</span>
                                        <span class="info-value">{{ $payment->booking->preferred_date ? date('Y-m-d H:i', strtotime($payment->booking->preferred_date)) : 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">حالة الحجز:</span>
                                        <span class="info-value status-badge status-{{ $payment->booking->status ?? 'pending' }}">
                                            @if($payment->booking->status == 'pending')
                                                قيد الانتظار
                                            @elseif($payment->booking->status == 'confirmed')
                                                مؤكد
                                            @elseif($payment->booking->status == 'completed')
                                                مكتمل
                                            @elseif($payment->booking->status == 'cancelled')
                                                ملغي
                                            @else
                                                {{ $payment->booking->status ?? 'N/A' }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('user.bookings.show', $payment->booking->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> عرض تفاصيل الحجز
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> لم يتم العثور على معلومات الحجز لهذا الدفع.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="payment-info-card">
                                <h5 class="info-title">معلومات الخدمة</h5>
                                @if(isset($payment->booking) && $payment->booking && isset($payment->booking->service) && $payment->booking->service)
                                    <div class="info-item">
                                        <span class="info-label">اسم الخدمة:</span>
                                        <span class="info-value">{{ $payment->booking->service->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">وصف الخدمة:</span>
                                        <span class="info-value">{{ $payment->booking->service->description ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">السعر:</span>
                                        <span class="info-value">{{ $payment->booking->service->price ?? 'N/A' }} ريال</span>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> لم يتم العثور على معلومات الخدمة لهذا الدفع.
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-info-card">
                                <h5 class="info-title">معلومات المختص</h5>
                                @if(isset($payment->booking) && $payment->booking && isset($payment->booking->specialist) && $payment->booking->specialist)
                                    <div class="specialist-info">
                                        <div class="specialist-avatar">
                                            <img src="{{ $payment->booking->specialist->user->profile_image ? asset($payment->booking->specialist->user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $payment->booking->specialist->user->name ?? 'المختص' }}">
                                        </div>
                                        <div class="specialist-details">
                                            <div class="info-item">
                                                <span class="info-label">اسم المختص:</span>
                                                <span class="info-value">{{ $payment->booking->specialist->user->name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">التخصص:</span>
                                                <span class="info-value">{{ $payment->booking->specialist->specialization ?? 'N/A' }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">البريد الإلكتروني:</span>
                                                <span class="info-value">{{ $payment->booking->specialist->user->email ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> لم يتم العثور على معلومات المختص لهذا الدفع.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="payment-info-card">
                                <h5 class="info-title">تفاصيل الفاتورة</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered invoice-table">
                                        <thead>
                                            <tr>
                                                <th>الوصف</th>
                                                <th>السعر</th>
                                                <th>الكمية</th>
                                                <th>المجموع</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($payment->booking) && $payment->booking && isset($payment->booking->service) && $payment->booking->service)
                                                <tr>
                                                    <td>{{ $payment->booking->service->name ?? 'خدمة استشارية' }}</td>
                                                    <td>{{ $payment->booking->service->price ?? $payment->amount ?? 0 }} ريال</td>
                                                    <td>1</td>
                                                    <td>{{ $payment->booking->service->price ?? $payment->amount ?? 0 }} ريال</td>
                                                </tr>
                                                @if(isset($payment->discount) && $payment->discount > 0)
                                                    <tr>
                                                        <td colspan="3" class="text-end">خصم</td>
                                                        <td>{{ $payment->discount ?? 0 }} ريال</td>
                                                    </tr>
                                                @endif
                                                @if(isset($payment->tax) && $payment->tax > 0)
                                                    <tr>
                                                        <td colspan="3" class="text-end">ضريبة القيمة المضافة (15%)</td>
                                                        <td>{{ $payment->tax ?? 0 }} ريال</td>
                                                    </tr>
                                                @endif
                                                <tr class="total-row">
                                                    <td colspan="3" class="text-end fw-bold">المجموع الكلي</td>
                                                    <td class="fw-bold">{{ $payment->amount ?? 0 }} ريال</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">لا توجد تفاصيل للفاتورة</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="payment-actions">
                                <a href="{{ route('user.payments') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> العودة إلى المدفوعات
                                </a>
                                
                                @if($payment->status == 'completed')
                                    <a href="#" class="btn btn-primary" onclick="printInvoice()">
                                        <i class="fas fa-print"></i> طباعة الفاتورة
                                    </a>
                                    
                                    <a href="#" class="btn btn-success" onclick="downloadInvoice()">
                                        <i class="fas fa-download"></i> تحميل الفاتورة
                                    </a>
                                @endif
                                
                                @if($payment->status == 'pending')
                                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#completePaymentModal">
                                        <i class="fas fa-credit-card"></i> إتمام الدفع
                                    </a>
                                @endif
                                
                                @if($payment->status == 'failed')
                                    <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#retryPaymentModal">
                                        <i class="fas fa-redo"></i> إعادة المحاولة
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

<!-- Modal: إتمام الدفع -->
<div class="modal fade" id="completePaymentModal" tabindex="-1" aria-labelledby="completePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completePaymentModalLabel">إتمام عملية الدفع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>يرجى اختيار طريقة الدفع لإتمام العملية:</p>
                
                <div class="payment-methods">
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="credit_card" checked>
                        <label class="form-check-label" for="creditCard">
                            <i class="fas fa-credit-card"></i> بطاقة ائتمانية
                        </label>
                    </div>
                    
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="mada" value="mada">
                        <label class="form-check-label" for="mada">
                            <i class="fas fa-credit-card"></i> مدى
                        </label>
                    </div>
                    
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="applePay" value="apple_pay">
                        <label class="form-check-label" for="applePay">
                            <i class="fab fa-apple-pay"></i> Apple Pay
                        </label>
                    </div>
                    
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="stcPay" value="stc_pay">
                        <label class="form-check-label" for="stcPay">
                            <i class="fas fa-mobile-alt"></i> STC Pay
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary">متابعة الدفع</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: إعادة محاولة الدفع -->
<div class="modal fade" id="retryPaymentModal" tabindex="-1" aria-labelledby="retryPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="retryPaymentModalLabel">إعادة محاولة الدفع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> فشلت عملية الدفع السابقة. هل ترغب في إعادة المحاولة؟
                </div>
                
                <p>يرجى اختيار طريقة الدفع:</p>
                
                <div class="payment-methods">
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="retryPaymentMethod" id="retryCreditCard" value="credit_card" checked>
                        <label class="form-check-label" for="retryCreditCard">
                            <i class="fas fa-credit-card"></i> بطاقة ائتمانية
                        </label>
                    </div>
                    
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="retryPaymentMethod" id="retryMada" value="mada">
                        <label class="form-check-label" for="retryMada">
                            <i class="fas fa-credit-card"></i> مدى
                        </label>
                    </div>
                    
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="retryPaymentMethod" id="retryApplePay" value="apple_pay">
                        <label class="form-check-label" for="retryApplePay">
                            <i class="fab fa-apple-pay"></i> Apple Pay
                        </label>
                    </div>
                    
                    <div class="form-check payment-method-item">
                        <input class="form-check-input" type="radio" name="retryPaymentMethod" id="retryStcPay" value="stc_pay">
                        <label class="form-check-label" for="retryStcPay">
                            <i class="fas fa-mobile-alt"></i> STC Pay
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-warning">إعادة المحاولة</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .payment-info-card {
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
    
    .status-completed {
        background-color: #c3e6cb;
        color: #155724;
    }
    
    .status-failed {
        background-color: #f5c6cb;
        color: #721c24;
    }
    
    .status-confirmed {
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
    
    .invoice-table {
        margin-top: 15px;
    }
    
    .invoice-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .total-row {
        background-color: #f8f9fa;
    }
    
    .payment-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .payment-methods {
        margin-top: 15px;
    }
    
    .payment-method-item {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #eee;
        border-radius: 5px;
    }
    
    .payment-method-item:hover {
        background-color: #f8f9fa;
    }
    
    @media (max-width: 767px) {
        .info-item {
            flex-direction: column;
        }
        
        .info-label {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .payment-actions {
            flex-direction: column;
            gap: 10px;
        }
        
        .payment-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function printInvoice() {
        window.print();
    }
    
    function downloadInvoice() {
        // هنا يمكن إضافة كود لتحميل الفاتورة كملف PDF
        alert('جاري تحميل الفاتورة...');
    }
    
    $(document).ready(function() {
        // تنفيذ أي سكريبت خاص بالصفحة هنا
    });
</script>
@endsection
