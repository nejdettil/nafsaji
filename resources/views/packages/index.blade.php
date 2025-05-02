@extends('layouts.app')

@section('title', 'إدارة الباقات')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">إدارة الباقات</h1>
            <p class="text-muted">عرض وإدارة باقات الخدمات المتاحة</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.services.packages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle ml-1"></i> إضافة باقة جديدة
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($packages) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>اسم الباقة</th>
                                <th>الخدمات</th>
                                <th>السعر</th>
                                <th>الخصم</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->services_count }} خدمة</td>
                                    <td>{{ $package->price }} ر.س</td>
                                    <td>{{ $package->discount ?? 0 }}%</td>
                                    <td>
                                        @if($package->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>{{ $package->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.services.packages.edit', $package->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.services.packages.destroy', $package->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذه الباقة؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $packages->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    لا توجد باقات متاحة حالياً. <a href="{{ route('admin.services.packages.create') }}">إضافة باقة جديدة</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
