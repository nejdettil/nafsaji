@extends('layouts.dashboard')

@section('title', 'تفاصيل الدفع')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">تفاصيل الدفع</h1>
        <p class="mb-4">عرض تفاصيل عملية الدفع رقم #{{ $payment->id }}.</p>

        <!-- Payment Details Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">معلومات الدفع</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>رقم الدفع:</strong> {{ $payment->id }}</p>
                        <p><strong>المبلغ:</strong> {{ $payment->amount }} {{ $payment->currency ?? 'SAR' }}</p>
                        <p><strong>الحالة:</strong>
                            @if($payment->status == 'paid')
                                <span class="badge badge-success">مدفوع</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge badge-warning">قيد الانتظار</span>
                            @elseif($payment->status == 'failed')
                                <span class="badge badge-danger">فشل</span>
                            @else
                                <span class="badge badge-secondary">{{ $payment->status }}</span>
                            @endif
                        </p>
                        <p><strong>طريقة الدفع:</strong> {{ $payment->payment_method ?? 'غير محدد' }}</p>
                        <p><strong>معرف عملية الدفع (Provider):</strong> {{ $payment->transaction_id ?? 'لا يوجد' }}</p>
                        <p><strong>تاريخ الإنشاء:</strong> {{ $payment->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>تاريخ التحديث:</strong> {{ $payment->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($payment->booking)
                            <p><strong>رقم الحجز:</strong> <a href="{{ route('admin.bookings.show', $payment->booking->id) }}">{{ $payment->booking->id }}</a></p>
                            @if($payment->booking->user)
                                <p><strong>المستخدم:</strong> <a href="#">{{ $payment->booking->user->name }}</a> ({{ $payment->booking->user->email }})</p>
                            @endif
                            @if($payment->booking->specialist && $payment->booking->specialist->user)
                                <p><strong>المختص:</strong> <a href="#">{{ $payment->booking->specialist->user->name }}</a></p>
                            @endif
                            @if($payment->booking->service)
                                <p><strong>الخدمة:</strong> {{ $payment->booking->service->name }}</p>
                            @endif
                            @if($payment->booking->package)
                                <p><strong>الباقة:</strong> {{ $payment->booking->package->name }}</p>
                            @endif
                            @if($payment->booking->session)
                                <p><strong>الجلسة:</strong> {{ $payment->booking->session->title ?? 'جلسة فردية' }}</p>
                            @endif
                        @else
                            <p>لا يوجد حجز مرتبط بهذا الدفع.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(isset($activityLog) && $activityLog->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">سجل النشاط</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>الوصف</th>
                                <th>المستخدم</th>
                                <th>التاريخ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($activityLog as $log)
                                <tr>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->causer ? $log->causer->name : 'النظام' }}</td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">العودة إلى قائمة المدفوعات</a>

    </div>
@endsection
