@extends('layouts.app')

@section('title', 'تعديل الباقة')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-0">تعديل الباقة</h1>
            <p class="text-muted">تعديل بيانات باقة "{{ $package->name }}"</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.services.packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">اسم الباقة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $package->name) }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="slug" class="form-label">الاسم المختصر (Slug)</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $package->slug) }}">
                        <small class="text-muted">سيتم إنشاؤه تلقائيًا إذا تركته فارغًا</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">وصف الباقة <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $package->description) }}</textarea>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label">السعر (ر.س) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $package->price) }}" step="0.01" min="0" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="discount" class="form-label">نسبة الخصم (%)</label>
                        <input type="number" class="form-control" id="discount" name="discount" value="{{ old('discount', $package->discount) }}" min="0" max="100">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="duration" class="form-label">مدة الباقة (بالأيام)</label>
                        <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration', $package->duration) }}" min="1">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">الخدمات المضمنة <span class="text-danger">*</span></label>
                        <div class="card">
                            <div class="card-body">
                                @if(count($services) > 0)
                                    <div class="row">
                                        @foreach($services as $service)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="services[]" value="{{ $service->id }}" id="service_{{ $service->id }}" 
                                                        {{ in_array($service->id, old('services', $package->services->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="service_{{ $service->id }}">
                                                        {{ $service->name }} ({{ $service->price }} ر.س)
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        لا توجد خدمات متاحة. <a href="{{ route('admin.services.create') }}">إضافة خدمة جديدة</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">صورة الباقة</label>
                        @if($package->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        @endif
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">اترك هذا الحقل فارغًا للاحتفاظ بالصورة الحالية</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="is_active" class="form-label">الحالة</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" {{ old('is_active', $package->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ old('is_active', $package->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="features" class="form-label">مميزات الباقة</label>
                        <textarea class="form-control" id="features" name="features" rows="3" placeholder="أدخل كل ميزة في سطر منفصل">{{ old('features', $package->features) }}</textarea>
                        <small class="text-muted">أدخل كل ميزة في سطر منفصل</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.services.packages.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // تحويل اسم الباقة إلى slug تلقائيًا إذا كان الحقل فارغًا
    document.getElementById('name').addEventListener('input', function() {
        const nameValue = this.value;
        const slugField = document.getElementById('slug');
        
        if (!slugField.value || slugField.value === '{{ $package->slug }}') {
            slugField.value = nameValue
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }
    });
</script>
@endsection
