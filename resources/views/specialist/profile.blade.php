@extends('layouts.dashboard')

@section('title', 'الملف الشخصي للمختص - نفسجي')

@section('content')
<div class="content-wrapper">
    <!-- رأس الصفحة -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">الملف الشخصي</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('specialist.dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">الملف الشخصي</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- محتوى الصفحة -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- بطاقة المعلومات الشخصية -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" 
                                     src="{{ $specialist->profile_image ? asset('storage/' . $specialist->profile_image) : asset('assets/images/default-specialist.png') }}" 
                                     alt="صورة المختص">
                            </div>

                            <h3 class="profile-username text-center">{{ $specialist->name }}</h3>
                            <p class="text-muted text-center">{{ $specialist->title }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>التخصص</b> <a class="float-left">{{ $specialist->specialization }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>عدد الجلسات</b> <a class="float-left">{{ $specialist->sessions_count ?? 0 }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>التقييم</b> <a class="float-left">
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $specialist->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="rating-value">({{ number_format($specialist->rating, 1) }})</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>

                            <button type="button" class="btn btn-primary btn-block" data-bs-toggle="modal" data-bs-target="#changeProfileImageModal">
                                <i class="fas fa-camera me-2"></i> تغيير الصورة الشخصية
                            </button>
                        </div>
                    </div>

                    <!-- بطاقة معلومات الاتصال -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">معلومات الاتصال</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-envelope me-1"></i> البريد الإلكتروني</strong>
                            <p class="text-muted">{{ $specialist->email }}</p>
                            <hr>

                            <strong><i class="fas fa-phone me-1"></i> رقم الهاتف</strong>
                            <p class="text-muted">{{ $specialist->phone ?? 'لم يتم تحديده' }}</p>
                            <hr>

                            <strong><i class="fas fa-map-marker-alt me-1"></i> العنوان</strong>
                            <p class="text-muted">{{ $specialist->address ?? 'لم يتم تحديده' }}</p>
                        </div>
                    </div>

                    <!-- بطاقة الخدمات -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">الخدمات المقدمة</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($specialist->services as $service)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $service->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $service->price }} ر.س</span>
                                </li>
                                @empty
                                <li class="list-group-item text-center">لا توجد خدمات مضافة</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('specialist.services') }}" class="btn btn-sm btn-outline-primary">إدارة الخدمات</a>
                        </div>
                    </div>
                </div>

                <!-- بطاقة تعديل المعلومات الشخصية -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#personal_info" data-bs-toggle="tab">المعلومات الشخصية</a></li>
                                <li class="nav-item"><a class="nav-link" href="#professional_info" data-bs-toggle="tab">المعلومات المهنية</a></li>
                                <li class="nav-item"><a class="nav-link" href="#availability" data-bs-toggle="tab">أوقات العمل</a></li>
                                <li class="nav-item"><a class="nav-link" href="#change_password" data-bs-toggle="tab">تغيير كلمة المرور</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- تعديل المعلومات الشخصية -->
                                <div class="active tab-pane" id="personal_info">
                                    <form class="form-horizontal" id="updateProfileForm" method="POST" action="{{ route('specialist.profile.update') }}">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="form-group row mb-3">
                                            <label for="name" class="col-sm-2 col-form-label">الاسم الكامل</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="name" name="name" value="{{ $specialist->name }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="email" class="col-sm-2 col-form-label">البريد الإلكتروني</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="email" name="email" value="{{ $specialist->email }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="phone" class="col-sm-2 col-form-label">رقم الهاتف</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $specialist->phone }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="address" class="col-sm-2 col-form-label">العنوان</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="address" name="address" value="{{ $specialist->address }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="city" class="col-sm-2 col-form-label">المدينة</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="city" name="city" value="{{ $specialist->city }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="birthdate" class="col-sm-2 col-form-label">تاريخ الميلاد</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ $specialist->birthdate ? $specialist->birthdate->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="gender" class="col-sm-2 col-form-label">الجنس</label>
                                            <div class="col-sm-10">
                                                <select class="form-control" id="gender" name="gender">
                                                    <option value="male" {{ $specialist->gender == 'male' ? 'selected' : '' }}>ذكر</option>
                                                    <option value="female" {{ $specialist->gender == 'female' ? 'selected' : '' }}>أنثى</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- تعديل المعلومات المهنية -->
                                <div class="tab-pane" id="professional_info">
                                    <form class="form-horizontal" id="updateProfessionalInfoForm" method="POST" action="{{ route('specialist.profile.update-professional') }}">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="form-group row mb-3">
                                            <label for="title" class="col-sm-2 col-form-label">المسمى الوظيفي</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="title" name="title" value="{{ $specialist->title }}">
                                                <small class="form-text text-muted">مثال: أخصائي نفسي، معالج سلوكي، مرشد أسري</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="specialization" class="col-sm-2 col-form-label">التخصص</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="specialization" name="specialization" value="{{ $specialist->specialization }}">
                                                <small class="form-text text-muted">مثال: علم النفس الإكلينيكي، العلاج المعرفي السلوكي</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="experience_years" class="col-sm-2 col-form-label">سنوات الخبرة</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" id="experience_years" name="experience_years" value="{{ $specialist->experience_years }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="bio" class="col-sm-2 col-form-label">نبذة شخصية</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="bio" name="bio" rows="5">{{ $specialist->bio }}</textarea>
                                                <small class="form-text text-muted">اكتب نبذة مختصرة عن خبراتك ومؤهلاتك (500 حرف كحد أقصى)</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="education" class="col-sm-2 col-form-label">المؤهلات العلمية</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="education" name="education" rows="3">{{ $specialist->education }}</textarea>
                                                <small class="form-text text-muted">اذكر شهاداتك العلمية والجامعات التي تخرجت منها</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="certifications" class="col-sm-2 col-form-label">الشهادات المهنية</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" id="certifications" name="certifications" rows="3">{{ $specialist->certifications }}</textarea>
                                                <small class="form-text text-muted">اذكر الشهادات والدورات المهنية التي حصلت عليها</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="languages" class="col-sm-2 col-form-label">اللغات</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="languages" name="languages" value="{{ $specialist->languages }}">
                                                <small class="form-text text-muted">اللغات التي تتقنها (مثال: العربية، الإنجليزية)</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- إعدادات أوقات العمل -->
                                <div class="tab-pane" id="availability">
                                    <form class="form-horizontal" id="updateAvailabilityForm" method="POST" action="{{ route('specialist.profile.update-availability') }}">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-4">
                                            <h5>أيام العمل</h5>
                                            <div class="row">
                                                @php
                                                    $days = ['sunday' => 'الأحد', 'monday' => 'الاثنين', 'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة', 'saturday' => 'السبت'];
                                                @endphp
                                                
                                                @foreach($days as $day_key => $day_name)
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input day-checkbox" type="checkbox" id="{{ $day_key }}_available" name="working_days[{{ $day_key }}]" value="1" {{ isset($specialist->availability->working_days[$day_key]) && $specialist->availability->working_days[$day_key] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $day_key }}_available">
                                                            {{ $day_name }}
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="row mt-2 time-slots {{ isset($specialist->availability->working_days[$day_key]) && $specialist->availability->working_days[$day_key] ? '' : 'd-none' }}" id="{{ $day_key }}_time_slots">
                                                        <div class="col-6">
                                                            <label for="{{ $day_key }}_start_time" class="form-label">من</label>
                                                            <input type="time" class="form-control" id="{{ $day_key }}_start_time" name="working_hours[{{ $day_key }}][start]" value="{{ $specialist->availability->working_hours[$day_key]['start'] ?? '09:00' }}">
                                                        </div>
                                                        <div class="col-6">
                                                            <label for="{{ $day_key }}_end_time" class="form-label">إلى</label>
                                                            <input type="time" class="form-control" id="{{ $day_key }}_end_time" name="working_hours[{{ $day_key }}][end]" value="{{ $specialist->availability->working_hours[$day_key]['end'] ?? '17:00' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <h5>إعدادات الجلسات</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="session_duration" class="form-label">مدة الجلسة الافتراضية (بالدقائق)</label>
                                                    <input type="number" class="form-control" id="session_duration" name="session_duration" value="{{ $specialist->availability->session_duration ?? 60 }}" min="15" step="15">
                                                    <small class="form-text text-muted">المدة الافتراضية للجلسة (يمكن تعديلها لكل خدمة)</small>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="break_time" class="form-label">وقت الراحة بين الجلسات (بالدقائق)</label>
                                                    <input type="number" class="form-control" id="break_time" name="break_time" value="{{ $specialist->availability->break_time ?? 15 }}" min="0" step="5">
                                                    <small class="form-text text-muted">الوقت بين نهاية جلسة وبداية الجلسة التالية</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <h5>الإجازات والعطلات</h5>
                                            <div class="vacation-dates">
                                                @if(isset($specialist->availability->vacation_dates) && count($specialist->availability->vacation_dates) > 0)
                                                    @foreach($specialist->availability->vacation_dates as $index => $vacation)
                                                    <div class="row mb-3 vacation-row">
                                                        <div class="col-md-5">
                                                            <label class="form-label">من</label>
                                                            <input type="date" class="form-control" name="vacation_dates[{{ $index }}][start]" value="{{ $vacation['start'] }}">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label">إلى</label>
                                                            <input type="date" class="form-control" name="vacation_dates[{{ $index }}][end]" value="{{ $vacation['end'] }}">
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger remove-vacation">حذف</button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @else
                                                    <div class="row mb-3 vacation-row">
                                                        <div class="col-md-5">
                                                            <label class="form-label">من</label>
                                                            <input type="date" class="form-control" name="vacation_dates[0][start]">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label">إلى</label>
                                                            <input type="date" class="form-control" name="vacation_dates[0][end]">
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger remove-vacation">حذف</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-success add-vacation mt-2">
                                                <i class="fas fa-plus me-1"></i> إضافة إجازة
                                            </button>
                                        </div>
                                        
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- تغيير كلمة المرور -->
                                <div class="tab-pane" id="change_password">
                                    <form class="form-horizontal" id="changePasswordForm" method="POST" action="{{ route('specialist.password.update') }}">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="form-group row mb-3">
                                            <label for="current_password" class="col-sm-3 col-form-label">كلمة المرور الحالية</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" id="current_password" name="current_password">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="new_password" class="col-sm-3 col-form-label">كلمة المرور الجديدة</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" id="new_password" name="new_password">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label for="new_password_confirmation" class="col-sm-3 col-form-label">تأكيد كلمة المرور</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مودال تغيير الصورة الشخصية -->
<div class="modal fade" id="changeProfileImageModal" tabindex="-1" aria-labelledby="changeProfileImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeProfileImageModalLabel">تغيير الصورة الشخصية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="profileImageForm" method="POST" action="{{ route('specialist.profile.update-image') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img id="profile-image-preview" src="{{ $specialist->profile_image ? asset('storage/' . $specialist->profile_image) : asset('assets/images/default-specialist.png') }}" 
                             alt="معاينة الصورة" class="img-fluid rounded-circle" style="max-width: 150px; max-height: 150px;">
                    </div>
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">اختر صورة جديدة</label>
                        <input class="form-control" type="file" id="profile_image" name="profile_image" accept="image/*">
                        <small class="form-text text-muted">الصيغ المدعومة: JPG، PNG، GIF. الحجم الأقصى: 2 ميجابايت.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // معاينة الصورة قبل الرفع
        const profileImageInput = document.getElementById('profile_image');
        const profileImagePreview = document.getElementById('profile-image-preview');
        
        if (profileImageInput && profileImagePreview) {
            profileImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileImagePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        
        // التعامل مع أيام العمل وأوقات العمل
        const dayCheckboxes = document.querySelectorAll('.day-checkbox');
        if (dayCheckboxes.length > 0) {
            dayCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const dayKey = this.id.replace('_available', '');
                    const timeSlots = document.getElementById(`${dayKey}_time_slots`);
                    
                    if (this.checked) {
                        timeSlots.classList.remove('d-none');
                    } else {
                        timeSlots.classList.add('d-none');
                    }
                });
            });
        }
        
        // إضافة وحذف الإجازات
        const addVacationButton = document.querySelector('.add-vacation');
        const vacationDatesContainer = document.querySelector('.vacation-dates');
        
        if (addVacationButton && vacationDatesContainer) {
            addVacationButton.addEventListener('click', function() {
                const vacationRows = document.querySelectorAll('.vacation-row');
                const newIndex = vacationRows.length;
                
                const newRow = document.createElement('div');
                newRow.className = 'row mb-3 vacation-row';
                newRow.innerHTML = `
                    <div class="col-md-5">
                        <label class="form-label">من</label>
                        <input type="date" class="form-control" name="vacation_dates[${newIndex}][start]">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">إلى</label>
                        <input type="date" class="form-control" name="vacation_dates[${newIndex}][end]">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-vacation">حذف</button>
                    </div>
                `;
                
                vacationDatesContainer.appendChild(newRow);
                
                // إضافة مستمع الحدث لزر الحذف الجديد
                newRow.querySelector('.remove-vacation').addEventListener('click', function() {
                    this.closest('.vacation-row').remove();
                });
            });
            
            // إضافة مستمعي الأحداث لأزرار الحذف الموجودة
            document.querySelectorAll('.remove-vacation').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.vacation-row').remove();
                });
            });
        }
        
        // التحقق من صحة نموذج تغيير كلمة المرور
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(event) {
                const currentPassword = document.getElementById('current_password').value;
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('new_password_confirmation').value;
                
                if (!currentPassword || !newPassword || !confirmPassword) {
                    event.preventDefault();
                    alert('يرجى ملء جميع حقول كلمة المرور');
                    return;
                }
                
                if (newPassword !== confirmPassword) {
                    event.preventDefault();
                    alert('كلمة المرور الجديدة وتأكيدها غير متطابقين');
                    return;
                }
                
                if (newPassword.length < 8) {
                    event.preventDefault();
                    alert('يجب أن تكون كلمة المرور الجديدة 8 أحرف على الأقل');
                    return;
                }
            });
        }
        
        // عرض رسائل النجاح والخطأ
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');
        const errorMessage = urlParams.get('error');
        
        if (successMessage) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${decodeURIComponent(successMessage)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
            `;
            document.querySelector('.content-header').appendChild(alertDiv);
        }
        
        if (errorMessage) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${decodeURIComponent(errorMessage)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
            `;
            document.querySelector('.content-header').appendChild(alertDiv);
        }
    });
</script>
@endsection

@section('styles')
<style>
    .profile-user-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }
    
    .nav-pills .nav-link.active {
        background-color: #007bff;
    }
    
    .rating-stars {
        display: inline-block;
    }
    
    .rating-stars .fa-star {
        font-size: 0.9rem;
    }
    
    .rating-value {
        font-size: 0.9rem;
        margin-left: 5px;
    }
    
    #profile-image-preview {
        object-fit: cover;
        border: 3px solid #f8f9fa;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endsection
