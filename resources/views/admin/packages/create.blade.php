@extends('layouts.dashboard')

@section('title', 'إضافة باقة جديدة - نفسجي للتمكين النفسي')

@section('content')
<div class="package-create-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">إضافة باقة جديدة</h1>
                    <p class="dashboard-subtitle">إنشاء باقة جديدة من الخدمات المقدمة في المنصة</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
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
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-box"></i> معلومات الباقة
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data" id="packageForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">اسم الباقة <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="slug" class="form-label">الرابط المختصر</label>
                                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}">
                                                <small class="form-text text-muted">سيتم إنشاؤه تلقائيًا إذا تركته فارغًا</small>
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">وصف الباقة <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="price" class="form-label">السعر الأصلي <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required>
                                                    <span class="input-group-text">ريال</span>
                                                </div>
                                                @error('price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="discount" class="form-label">نسبة الخصم</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" value="{{ old('discount', 0) }}" min="0" max="100">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                @error('discount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="final_price" class="form-label">السعر النهائي</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="final_price" readonly>
                                                    <span class="input-group-text">ريال</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="duration" class="form-label">مدة الباقة <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration') }}" min="1" required>
                                                    <span class="input-group-text">يوم</span>
                                                </div>
                                                @error('duration')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="status" class="form-label">الحالة</label>
                                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">صورة الباقة</label>
                                            <div class="image-upload-container">
                                                <div class="image-preview" id="imagePreview">
                                                    <img src="{{ asset('images/placeholder.jpg') }}" alt="معاينة الصورة" id="previewImage">
                                                </div>
                                                <div class="image-upload-controls">
                                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                                    <small class="form-text text-muted">الحد الأقصى للحجم: 2 ميجابايت. الأبعاد المثالية: 600×400 بكسل.</small>
                                                </div>
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">خيارات إضافية</label>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">باقة مميزة</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="is_limited" name="is_limited" {{ old('is_limited') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_limited">عرض محدود</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <h5 class="mb-3">الخدمات المضمنة في الباقة</h5>
                                    <div class="services-selection">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="services-search">
                                                    <input type="text" class="form-control" id="serviceSearch" placeholder="البحث عن خدمة...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="services-list">
                                            @if(isset($services) && count($services) > 0)
                                                <div class="row">
                                                    @foreach($services as $service)
                                                        <div class="col-md-4 mb-3 service-item">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input service-checkbox" type="checkbox" id="service{{ $service->id }}" name="services[]" value="{{ $service->id }}" {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="service{{ $service->id }}">
                                                                            <div class="service-info">
                                                                                <h6 class="service-name">{{ $service->name }}</h6>
                                                                                <p class="service-price">{{ $service->price }} ريال</p>
                                                                                <p class="service-category">{{ $service->category->name }}</p>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    لا توجد خدمات متاحة. يرجى <a href="{{ route('admin.services.create') }}">إضافة خدمات</a> أولاً.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ الباقة
                                    </button>
                                    <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
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
    $(document).ready(function() {
        // تحديث السعر النهائي عند تغيير السعر أو الخصم
        function updateFinalPrice() {
            const price = parseFloat($('#price').val()) || 0;
            const discount = parseFloat($('#discount').val()) || 0;
            const finalPrice = price - (price * discount / 100);
            $('#final_price').val(finalPrice.toFixed(2));
        }

        $('#price, #discount').on('input', updateFinalPrice);
        updateFinalPrice(); // تحديث عند تحميل الصفحة

        // معاينة الصورة
        $('#image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // البحث في الخدمات
        $('#serviceSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.service-item').each(function() {
                const serviceName = $(this).find('.service-name').text().toLowerCase();
                const serviceCategory = $(this).find('.service-category').text().toLowerCase();
                
                if (serviceName.includes(searchTerm) || serviceCategory.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // إنشاء الرابط المختصر تلقائيًا
        $('#name').on('input', function() {
            if (!$('#slug').val()) {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/\s+/g, '-')           // استبدال المسافات بشرطات
                    .replace(/[^\w\-]+/g, '')       // إزالة الأحرف غير الأبجدية الرقمية
                    .replace(/\-\-+/g, '-')         // استبدال الشرطات المتعددة بشرطة واحدة
                    .replace(/^-+/, '')             // إزالة الشرطات من البداية
                    .replace(/-+$/, '');            // إزالة الشرطات من النهاية
                
                $('#slug').val(slug);
            }
        });

        // التحقق من النموذج قبل الإرسال
        $('#packageForm').submit(function(e) {
            const servicesSelected = $('.service-checkbox:checked').length;
            if (servicesSelected === 0) {
                e.preventDefault();
                toastr.error('يرجى اختيار خدمة واحدة على الأقل للباقة');
            }
        });
    });
</script>
@endsection
