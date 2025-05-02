@extends('layouts.dashboard')

@section('title', 'عرض فئة الخدمة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">فئة الخدمة: {{ $category->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.services.categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                        <a href="{{ route('admin.services.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">معلومات الفئة</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">الاسم</th>
                                            <td>{{ $category->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>الوصف</th>
                                            <td>{{ $category->description ?: 'لا يوجد وصف' }}</td>
                                        </tr>
                                        <tr>
                                            <th>الحالة</th>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge badge-success">نشط</span>
                                                @else
                                                    <span class="badge badge-danger">غير نشط</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الإنشاء</th>
                                            <td>{{ $category->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>آخر تحديث</th>
                                            <td>{{ $category->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">إحصائيات</h4>
                                </div>
                                <div class="card-body">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-briefcase"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">عدد الخدمات</span>
                                            <span class="info-box-number">{{ $services->total() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">الخدمات في هذه الفئة</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>اسم الخدمة</th>
                                                    <th>السعر</th>
                                                    <th>المدة</th>
                                                    <th>الحالة</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($services as $service)
                                                    <tr>
                                                        <td>{{ $service->id }}</td>
                                                        <td>{{ $service->name }}</td>
                                                        <td>{{ $service->price }} ريال</td>
                                                        <td>{{ $service->duration }} دقيقة</td>
                                                        <td>
                                                            @if($service->is_active)
                                                                <span class="badge badge-success">نشط</span>
                                                            @else
                                                                <span class="badge badge-danger">غير نشط</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.services.show', $service) }}" class="btn btn-info btn-sm">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">لا توجد خدمات في هذه الفئة</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-4">
                                        {{ $services->links() }}
                                    </div>
                                </div>
                            </div>
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
    $(function () {
        // يمكن إضافة أي سكريبتات خاصة بالصفحة هنا
    });
</script>
@endsection
