@extends('layouts.dashboard')

@section('title', 'تعديل بيانات المستخدم - نفسجي للتمكين النفسي')

@section('content')
<div class="users-edit-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">تعديل بيانات المستخدم</h1>
                    <p class="dashboard-subtitle">تحديث معلومات المستخدم في النظام</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i> عرض بيانات المستخدم
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-right"></i> العودة إلى قائمة المستخدمين
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
                    <div class="dashboard-card user-profile-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-user"></i> الملف الشخصي
                            </h5>
                        </div>
                        <div class="dashboard-card-body text-center">
                            <div class="user-avatar-large">
                                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $user->name }}" class="img-fluid rounded-circle">
                            </div>
                            <h4 class="user-name mt-3">{{ $user->name }}</h4>
                            <p class="user-email">{{ $user->email }}</p>
                            
                            <div class="user-roles mt-3">
                                @foreach($user->roles as $role)
                                    <span class="role-badge role-{{ $role->name }}">{{ __('roles.' . $role->name) }}</span>
                                @endforeach
                            </div>
                            
                            <div class="user-status mt-3">
                                <span class="status-badge status-{{ $user->is_active ? 'active' : 'inactive' }}">
                                    {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            
                            <div class="user-actions mt-4">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                    <i class="fas fa-key"></i> تغيير كلمة المرور
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-edit"></i> تعديل بيانات المستخدم
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">الدور <span class="text-danger">*</span></label>
                                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                                <option value="" disabled>اختر الدور</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ (old('role') == $role->name || $user->hasRole($role->name)) ? 'selected' : '' }}>{{ __('roles.' . $role->name) }}</option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="profile_image" class="form-label">صورة الملف الشخصي</label>
                                            <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image">
                                            @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">الصيغ المدعومة: JPG، PNG، GIF. الحجم الأقصى: 2MB.</div>
                                            @if($user->profile_image)
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                                    <label class="form-check-label" for="remove_image">
                                                        إزالة الصورة الحالية
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    حساب نشط
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">ملاحظات</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $user->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> حفظ التغييرات
                                            </button>
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times"></i> إلغاء
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    @if($user->hasRole('specialist') && isset($user->specialist))
                    <div class="dashboard-card mt-4">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-user-md"></i> بيانات المختص
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <form action="{{ route('admin.specialists.verify', $user->specialist->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">التخصص</label>
                                            <p class="form-control-static">{{ $user->specialist->specialization }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">سنوات الخبرة</label>
                                            <p class="form-control-static">{{ $user->specialist->years_of_experience }} سنة</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="is_verified" class="form-label">حالة التحقق</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" value="1" {{ old('is_verified', $user->specialist->is_verified) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_verified">
                                                    تم التحقق من المختص
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">الإتاحة</label>
                                            <p class="form-control-static">
                                                <span class="status-badge status-{{ $user->specialist->is_available ? 'available' : 'unavailable' }}">
                                                    {{ $user->specialist->is_available ? 'متاح' : 'غير متاح' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> حفظ حالة التحقق
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تغيير كلمة المرور -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">تغيير كلمة المرور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تبديل عرض كلمة المرور
        $('.toggle-password').click(function() {
            const passwordInput = $(this).siblings('input');
            const icon = $(this).find('i');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
</script>
@endsection
