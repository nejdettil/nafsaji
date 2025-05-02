@extends('layouts.dashboard')

@section('title', 'إدارة الباقات - نفسجي للتمكين النفسي')

@section('content')
<div class="packages-management-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">إدارة الباقات</h1>
                    <p class="dashboard-subtitle">إدارة باقات الخدمات المقدمة في المنصة</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> إضافة باقة جديدة
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
                                <i class="fas fa-box"></i> قائمة الباقات
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="search-box">
                                    <form action="{{ route('admin.packages.index') }}" method="GET">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="بحث..." name="search" value="{{ request('search') }}">
                                            <button class="btn btn-outline-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="dropdown d-inline-block ms-2">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter"></i> تصفية
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item {{ request('status') == 'all' || !request('status') ? 'active' : '' }}" href="{{ route('admin.packages.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}">جميع الباقات</a></li>
                                        <li><a class="dropdown-item {{ request('status') == 'active' ? 'active' : '' }}" href="{{ route('admin.packages.index', array_merge(request()->except('status', 'page'), ['status' => 'active'])) }}">الباقات النشطة</a></li>
                                        <li><a class="dropdown-item {{ request('status') == 'inactive' ? 'active' : '' }}" href="{{ route('admin.packages.index', array_merge(request()->except('status', 'page'), ['status' => 'inactive'])) }}">الباقات غير النشطة</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(isset($packages) && count($packages) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check">
                                                        <input class="form-check-input select-all" type="checkbox" id="selectAllPackages">
                                                        <label class="form-check-label" for="selectAllPackages"></label>
                                                    </div>
                                                </th>
                                                <th>الباقة</th>
                                                <th>الخدمات</th>
                                                <th>السعر</th>
                                                <th>الخصم</th>
                                                <th>المدة</th>
                                                <th>الحجوزات</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($packages as $package)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input select-item" type="checkbox" id="package{{ $package->id }}" value="{{ $package->id }}">
                                                            <label class="form-check-label" for="package{{ $package->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="package-info">
                                                            @if($package->image)
                                                                <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" class="package-image">
                                                            @else
                                                                <div class="package-icon">
                                                                    <i class="fas fa-box"></i>
                                                                </div>
                                                            @endif
                                                            <div class="package-details">
                                                                <h6 class="package-name">{{ $package->name }}</h6>
                                                                <p class="package-description">{{ Str::limit($package->description, 50) }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
<span class="services-count">
    {{ is_countable($package->services) ? count($package->services) : 0 }}
</span>

                                                    </td>
                                                    <td>
                                                        <span class="price">{{ $package->price }} ريال</span>
                                                    </td>
                                                    <td>
                                                        <span class="discount">{{ $package->discount }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="duration">{{ $package->duration }} يوم</span>
                                                    </td>
                                                    <td>
                                                        <span class="bookings-count">{{ $package->bookings_count ?? 0 }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-switch" type="checkbox" id="statusSwitch{{ $package->id }}" {{ $package->status == 'active' ? 'checked' : '' }} data-id="{{ $package->id }}">
                                                            <label class="form-check-label" for="statusSwitch{{ $package->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="{{ route('admin.packages.show', $package->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-danger delete-package" data-id="{{ $package->id }}" data-bs-toggle="tooltip" title="حذف">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="bulk-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                                إجراءات جماعية
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="activate">تفعيل المحدد</a></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="deactivate">تعطيل المحدد</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="feature">تمييز المحدد</a></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="unfeature">إلغاء تمييز المحدد</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="delete">حذف المحدد</a></li>
                                            </ul>
                                        </div>
                                        <span class="selected-count ms-2">0 محدد</span>
                                    </div>

                                    <div class="pagination-container">
                                        {{ $packages->appends(request()->except('page'))->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <h5 class="empty-state-title">لا توجد باقات</h5>
                                    <p class="empty-state-description">لم يتم العثور على أي باقات. يمكنك إضافة باقات جديدة من خلال النقر على زر "إضافة باقة جديدة".</p>
                                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus-circle"></i> إضافة باقة جديدة
                                    </a>
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
                <form id="deletePackageForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- نموذج الإجراءات الجماعية -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">تأكيد الإجراء الجماعي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage">هل أنت متأكد من تنفيذ هذا الإجراء على العناصر المحددة؟</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="confirmBulkAction">تأكيد</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تبديل حالة الباقة
        $('.status-switch').change(function() {
            const packageId = $(this).data('id');
            const status = $(this).prop('checked') ? 'active' : 'inactive';
            
            $.ajax({
                url: "{{ route('admin.packages.update-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: packageId,
                    status: status
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error('حدث خطأ أثناء تحديث الحالة');
                    console.error(xhr.responseText);
                }
            });
        });

        // حذف باقة
        $('.delete-package').click(function() {
            const packageId = $(this).data('id');
            $('#deletePackageForm').attr('action', `{{ url('admin/packages') }}/${packageId}`);
            $('#deletePackageModal').modal('show');
        });

        // تحديد الكل
        $('#selectAllPackages').change(function() {
            $('.select-item').prop('checked', $(this).prop('checked'));
            updateBulkActionButton();
        });

        // تحديث زر الإجراءات الجماعية عند تغيير التحديد
        $('.select-item').change(function() {
            updateBulkActionButton();
        });

        // تحديث زر الإجراءات الجماعية
        function updateBulkActionButton() {
            const selectedCount = $('.select-item:checked').length;
            $('.selected-count').text(`${selectedCount} محدد`);
            
            if (selectedCount > 0) {
                $('#bulkActionsDropdown').prop('disabled', false);
            } else {
                $('#bulkActionsDropdown').prop('disabled', true);
            }
        }

        // الإجراءات الجماعية
        $('.bulk-action').click(function(e) {
            e.preventDefault();
            
            const action = $(this).data('action');
            const selectedIds = $('.select-item:checked').map(function() {
                return $(this).val();
            }).get();
            
            let actionText = '';
            let btnClass = 'btn-primary';
            
            switch(action) {
                case 'activate':
                    actionText = 'تفعيل';
                    break;
                case 'deactivate':
                    actionText = 'تعطيل';
                    break;
                case 'feature':
                    actionText = 'تمييز';
                    break;
                case 'unfeature':
                    actionText = 'إلغاء تمييز';
                    break;
                case 'delete':
                    actionText = 'حذف';
                    btnClass = 'btn-danger';
                    break;
            }
            
            $('#bulkActionMessage').text(`هل أنت متأكد من ${actionText} العناصر المحددة (${selectedIds.length} عنصر)؟`);
            $('#confirmBulkAction').removeClass().addClass(`btn ${btnClass}`).text(actionText);
            $('#confirmBulkAction').data('action', action);
            $('#confirmBulkAction').data('ids', selectedIds);
            $('#bulkActionModal').modal('show');
        });

        // تأكيد الإجراء الجماعي
        $('#confirmBulkAction').click(function() {
            const action = $(this).data('action');
            const ids = $(this).data('ids');
            
            $.ajax({
                url: "{{ route('admin.packages.bulk-action') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    action: action,
                    ids: ids
                },
                success: function(response) {
                    $('#bulkActionModal').modal('hide');
                    toastr.success(response.message);
                    
                    // إعادة تحميل الصفحة بعد الإجراء الجماعي
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    toastr.error('حدث خطأ أثناء تنفيذ الإجراء');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection
