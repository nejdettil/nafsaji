@extends('layouts.app')

@section('title', 'تعديل الملف الشخصي - نفسجي')

@section('content')
<div class="container py-5 mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="profile-image mb-3">
                        @if(Auth::user()->profile_photo_path)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="rounded-circle img-fluid" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; font-size: 3rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                </div>
            </div>
            
            <div class="list-group shadow-sm">
                <a href="#personal-info" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                    <i class="fas fa-user-circle me-2"></i> المعلومات الشخصية
                </a>
                <a href="#change-password" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-key me-2"></i> تغيير كلمة المرور
                </a>
                <a href="#profile-photo" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-camera me-2"></i> صورة الملف الشخصي
                </a>
                <a href="#notifications-settings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-bell me-2"></i> إعدادات الإشعارات
                </a>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="tab-content">
                <!-- المعلومات الشخصية -->
                <div class="tab-pane fade show active" id="personal-info">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">المعلومات الشخصية</h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', Auth::user()->address) }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">نبذة عني</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', Auth::user()->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- تغيير كلمة المرور -->
                <div class="tab-pane fade" id="change-password">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">تغيير كلمة المرور</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update-password') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- صورة الملف الشخصي -->
                <div class="tab-pane fade" id="profile-photo">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">صورة الملف الشخصي</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="text-center mb-4">
                                    @if(Auth::user()->profile_photo_path)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px; font-size: 4rem;">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">اختر صورة جديدة</label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">يجب أن تكون الصورة بصيغة JPG أو PNG أو GIF ولا يزيد حجمها عن 2 ميجابايت.</div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">تحديث الصورة</button>
                                    @if(Auth::user()->profile_photo_path)
                                        <a href="{{ route('profile.delete-photo') }}" class="btn btn-outline-danger ms-2" onclick="return confirm('هل أنت متأكد من حذف الصورة؟')">حذف الصورة</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الإشعارات -->
                <div class="tab-pane fade" id="notifications-settings">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">إعدادات الإشعارات</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update-notifications') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" {{ Auth::user()->email_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_notifications">إشعارات البريد الإلكتروني</label>
                                    </div>
                                    <div class="form-text">استلام الإشعارات عبر البريد الإلكتروني.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" {{ Auth::user()->sms_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_notifications">إشعارات الرسائل النصية</label>
                                    </div>
                                    <div class="form-text">استلام الإشعارات عبر الرسائل النصية.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="browser_notifications" name="browser_notifications" {{ Auth::user()->browser_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="browser_notifications">إشعارات المتصفح</label>
                                    </div>
                                    <div class="form-text">استلام الإشعارات عبر المتصفح.</div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                                </div>
                            </form>
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
    // تفعيل التبويبات
    document.addEventListener('DOMContentLoaded', function() {
        const triggerTabList = [].slice.call(document.querySelectorAll('.list-group-item'))
        triggerTabList.forEach(function (triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl)
            
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
                
                // إزالة الكلاس active من جميع العناصر
                triggerTabList.forEach(el => el.classList.remove('active'))
                // إضافة الكلاس active للعنصر المحدد
                triggerEl.classList.add('active')
            })
        })
        
        // التحقق من وجود هاش في الرابط وتفعيل التبويب المناسب
        if (window.location.hash) {
            const hash = window.location.hash;
            const triggerEl = document.querySelector(`a[href="${hash}"]`);
            if (triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                tabTrigger.show();
                
                // إزالة الكلاس active من جميع العناصر
                triggerTabList.forEach(el => el.classList.remove('active'))
                // إضافة الكلاس active للعنصر المحدد
                triggerEl.classList.add('active')
            }
        }
    });
</script>
@endsection
