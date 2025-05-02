@extends('layouts.dashboard')

@section('title', 'عرض بيانات المختص - نفسجي للتمكين النفسي')

@section('content')
<div class="specialists-show-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">بيانات المختص</h1>
                    <p class="dashboard-subtitle">عرض معلومات المختص النفسي بالتفصيل</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <a href="{{ route('admin.users.edit', $specialist->user_id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> تعديل المختص
                        </a>
                        <a href="{{ route('admin.specialists.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-right"></i> العودة إلى قائمة المختصين
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="dashboard-card specialist-profile-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-user-md"></i> الملف الشخصي
                            </h5>
                        </div>
                        <div class="dashboard-card-body text-center">
                            <div class="user-avatar-large">
                                <img src="{{ $specialist->user->profile_image ? asset('storage/' . $specialist->user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $specialist->user->name }}" class="img-fluid rounded-circle">
                            </div>
                            <h4 class="user-name mt-3">{{ $specialist->user->name }}</h4>
                            <p class="user-email">{{ $specialist->user->email }}</p>
                            <p class="specialist-title">{{ $specialist->specialization }}</p>
                            
                            <div class="specialist-status mt-3">
                                <span class="status-badge status-{{ $specialist->is_verified ? 'verified' : 'unverified' }}">
                                    {{ $specialist->is_verified ? 'تم التحقق' : 'غير محقق' }}
                                </span>
                                <span class="status-badge status-{{ $specialist->is_available ? 'available' : 'unavailable' }}">
                                    {{ $specialist->is_available ? 'متاح' : 'غير متاح' }}
                                </span>
                            </div>
                            
                            <div class="specialist-actions mt-4">
                                <form action="{{ route('admin.specialists.verify', $specialist->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_verified" value="{{ $specialist->is_verified ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-sm btn-{{ $specialist->is_verified ? 'warning' : 'success' }}">
                                        <i class="fas fa-{{ $specialist->is_verified ? 'times' : 'check' }}"></i> 
                                        {{ $specialist->is_verified ? 'إلغاء التحقق' : 'تحقق من المختص' }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.specialists.destroy', $specialist->id) }}" method="POST" class="d-inline-block delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i> حذف المختص
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card mt-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-info-circle"></i> معلومات إضافية
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <ul class="specialist-info-list">
                                <li>
                                    <span class="info-label">تاريخ التسجيل:</span>
                                    <span class="info-value">{{ $specialist->created_at->format('Y-m-d') }}</span>
                                </li>
                                <li>
                                    <span class="info-label">آخر تحديث:</span>
                                    <span class="info-value">{{ $specialist->updated_at->format('Y-m-d') }}</span>
                                </li>
                                <li>
                                    <span class="info-label">عدد الجلسات:</span>
                                    <span class="info-value">{{ $specialist->sessions_count ?? 0 }}</span>
                                </li>
                                <li>
                                    <span class="info-label">التقييم:</span>
                                    <span class="info-value">
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= ($specialist->rating ?? 0) ? 'filled' : '' }}"></i>
                                            @endfor
                                            <span class="rating-value">{{ number_format($specialist->rating ?? 0, 1) }}</span>
                                        </div>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-address-card"></i> بيانات المختص
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الاسم الكامل</label>
                                        <p class="form-control-static">{{ $specialist->user->name }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">البريد الإلكتروني</label>
                                        <p class="form-control-static">{{ $specialist->user->email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">رقم الهاتف</label>
                                        <p class="form-control-static">{{ $specialist->user->phone ?? 'غير متوفر' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">التخصص</label>
                                        <p class="form-control-static">{{ $specialist->specialization }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">سنوات الخبرة</label>
                                        <p class="form-control-static">{{ $specialist->years_of_experience }} سنة</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المؤهل العلمي</label>
                                        <p class="form-control-static">{{ $specialist->qualification ?? 'غير متوفر' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($specialist->bio)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">نبذة عن المختص</label>
                                        <p class="form-control-static">{{ $specialist->bio }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="dashboard-card mt-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-certificate"></i> الشهادات والمؤهلات
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($specialist->certificates ?? []) > 0)
                                <div class="certificates-list">
                                    @foreach($specialist->certificates as $certificate)
                                        <div class="certificate-item">
                                            <div class="certificate-icon">
                                                <i class="fas fa-certificate"></i>
                                            </div>
                                            <div class="certificate-content">
                                                <h6 class="certificate-title">{{ $certificate->title }}</h6>
                                                <p class="certificate-issuer">{{ $certificate->issuer }}</p>
                                                <p class="certificate-date">{{ $certificate->issue_date->format('Y-m-d') }}</p>
                                                @if($certificate->file)
                                                    <a href="{{ asset('storage/' . $certificate->file) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-file-pdf"></i> عرض الشهادة
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>لا توجد شهادات مسجلة لهذا المختص.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="dashboard-card mt-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-calendar-alt"></i> الجلسات القادمة
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($specialist->upcoming_sessions ?? []) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>المستخدم</th>
                                                <th>الخدمة</th>
                                                <th>التاريخ</th>
                                                <th>الوقت</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($specialist->upcoming_sessions as $session)
                                                <tr>
                                                    <td>{{ $session->booking->user->name }}</td>
                                                    <td>{{ $session->booking->service->name }}</td>
                                                    <td>{{ $session->date->format('Y-m-d') }}</td>
                                                    <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                                                    <td>
                                                        <span class="status-badge status-{{ $session->status }}">
                                                            {{ __('sessions.status.' . $session->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.sessions.show', $session->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state">
                                    <p>لا توجد جلسات قادمة لهذا المختص.</p>
                                </div>
                            @endif
                        </div>
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
        // تأكيد حذف المختص
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            
            if (confirm('هل أنت متأكد من رغبتك في حذف هذا المختص؟ لا يمكن التراجع عن هذا الإجراء.')) {
                this.submit();
            }
        });
    });
</script>
@endsection
