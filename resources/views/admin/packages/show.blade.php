@extends('layouts.dashboard')

@section('title', 'تفاصيل الباقة - نفسجي للتمكين النفسي')

@section('content')
<div class="package-show-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">تفاصيل الباقة</h1>
                    <p class="dashboard-subtitle">عرض معلومات الباقة "{{ $package->name ?? '' }}"</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> تعديل الباقة
                        </a>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-right"></i> العودة إلى قائمة الباقات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="dashboard-card mb-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-box"></i> معلومات الباقة
                            </h5>
                            <div class="dashboard-card-actions">
                                <span class="badge {{ $package->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $package->status == 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                                @if($package->is_featured)
                                    <span class="badge bg-primary">مميز</span>
                                @endif
                                @if($package->is_limited)
                                    <span class="badge bg-warning">عرض محدود</span>
                                @endif
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="package-image-container mb-3">
                                        @if($package->image)
                                            <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" class="img-fluid rounded">
                                        @else
                                            <div class="package-placeholder">
                                                <i class="fas fa-box fa-4x"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h4 class="package-name">{{ $package->name }}</h4>
                                    <p class="package-description">{{ $package->description }}</p>
                                    
                                    <div class="package-details mt-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <span class="detail-label">السعر الأصلي:</span>
                                                    <span class="detail-value">{{ $package->price }} ريال</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <span class="detail-label">نسبة الخصم:</span>
                                                    <span class="detail-value">{{ $package->discount }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <span class="detail-label">السعر النهائي:</span>
                                                    <span class="detail-value price-after-discount">{{ $package->price - ($package->price * $package->discount / 100) }} ريال</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <span class="detail-label">مدة الباقة:</span>
                                                    <span class="detail-value">{{ $package->duration }} يوم</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <span class="detail-label">تاريخ الإنشاء:</span>
                                                    <span class="detail-value">{{ $package->created_at->format('Y-m-d') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <span class="detail-label">آخر تحديث:</span>
                                                    <span class="detail-value">{{ $package->updated_at->format('Y-m-d') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card mb-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-hand-holding-heart"></i> الخدمات المضمنة
                            </h5>
                            <div class="dashboard-card-actions">
                                <span class="badge bg-info">{{ count($package->services) }} خدمة</span>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($package->services) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الخدمة</th>
                                                <th>التصنيف</th>
                                                <th>السعر</th>
                                                <th>المدة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($package->services as $index => $service)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="service-info">
                                                            @if($service->image)
                                                                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="service-image-sm">
                                                            @else
                                                                <div class="service-icon-sm">
                                                                    <i class="fas fa-hand-holding-heart"></i>
                                                                </div>
                                                            @endif
                                                            <div class="service-details">
                                                                <h6 class="service-name">{{ $service->name }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="category-badge">{{ $service->category->name }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="price">{{ $service->price }} ريال</span>
                                                    </td>
                                                    <td>
                                                        <span class="duration">{{ $service->duration }} دقيقة</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.services.show', $service->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    لا توجد خدمات مضمنة في هذه الباقة.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="dashboard-card mb-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-chart-line"></i> إحصائيات الباقة
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="stats-item">
                                <div class="stats-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="stats-details">
                                    <h6 class="stats-title">إجمالي الحجوزات</h6>
                                    <p class="stats-value">{{ $package->bookings_count ?? 0 }}</p>
                                </div>
                            </div>
                            <div class="stats-item">
                                <div class="stats-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="stats-details">
                                    <h6 class="stats-title">عدد المشاهدات</h6>
                                    <p class="stats-value">{{ $package->views_count ?? 0 }}</p>
                                </div>
                            </div>
                            <div class="stats-item">
                                <div class="stats-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="stats-details">
                                    <h6 class="stats-title">إجمالي الإيرادات</h6>
                                    <p class="stats-value">{{ $package->revenue ?? 0 }} ريال</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card mb-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-link"></i> روابط سريعة
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="quick-links">
                                <a href="{{ route('packages.show', $package->slug) }}" class="btn btn-outline-primary btn-block mb-2" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> عرض في الواجهة الأمامية
                                </a>
                                <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-outline-primary btn-block mb-2">
                                    <i class="fas fa-edit"></i> تعديل الباقة
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-block" data-bs-toggle="modal" data-bs-target="#deletePackageModal">
                                    <i class="fas fa-trash-alt"></i> حذف الباقة
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-history"></i> آخر الحجوزات
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            @if(isset($recentBookings) && count($recentBookings) > 0)
                                <div class="recent-bookings">
                                    @foreach($recentBookings as $booking)
                                        <div class="booking-item">
                                            <div class="booking-user">
                                                <div class="user-avatar">
                                                    @if($booking->user->avatar)
                                                        <img src="{{ asset('storage/' . $booking->user->avatar) }}" alt="{{ $booking->user->name }}">
                                                    @else
                                                        <div class="avatar-placeholder">
                                                            {{ substr($booking->user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="user-details">
                                                    <h6 class="user-name">{{ $booking->user->name }}</h6>
                                                    <p class="booking-date">{{ $booking->created_at->format('Y-m-d') }}</p>
                                                </div>
                                            </div>
                                            <div class="booking-status">
                                                <span class="badge {{ $booking->status == 'completed' ? 'bg-success' : ($booking->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $booking->status == 'completed' ? 'مكتمل' : ($booking->status == 'pending' ? 'قيد الانتظار' : 'ملغي') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.bookings.index', ['package_id' => $package->id]) }}" class="btn btn-sm btn-outline-primary">
                                        عرض جميع الحجوزات
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    لا توجد حجوزات لهذه الباقة حتى الآن.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نموذج حذف الباقة -->
<div class="modal fade" id="deletePackageModal" tabindex="-1" aria-labelledby="deletePackageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePackageModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف هذه الباقة؟ هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
