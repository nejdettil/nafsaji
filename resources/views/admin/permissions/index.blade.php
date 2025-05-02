@extends('layouts.dashboard')

@section('title', 'إدارة الصلاحيات - نفسجي للتمكين النفسي')

@section('content')
<div class="permissions-management-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">إدارة الصلاحيات</h1>
                    <p class="dashboard-subtitle">إدارة صلاحيات النظام وتعيينها للأدوار</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                            <i class="fas fa-plus-circle"></i> إضافة صلاحية جديدة
                        </button>
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
                                <i class="fas fa-key"></i> قائمة الصلاحيات
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="search-box">
                                    <form action="{{ route('admin.permissions.index') }}" method="GET">
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
                                        <li><a class="dropdown-item {{ request('group') == 'all' || !request('group') ? 'active' : '' }}" href="{{ route('admin.permissions.index', array_merge(request()->except('group', 'page'), ['group' => 'all'])) }}">جميع المجموعات</a></li>
                                        @foreach($permission_groups as $group)
                                            <li><a class="dropdown-item {{ request('group') == $group ? 'active' : '' }}" href="{{ route('admin.permissions.index', array_merge(request()->except('group', 'page'), ['group' => $group])) }}">{{ $group }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($permissions) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check">
                                                        <input class="form-check-input select-all" type="checkbox" id="selectAll">
                                                        <label class="form-check-label" for="selectAll"></label>
                                                    </div>
                                                </th>
                                                <th>الاسم</th>
                                                <th>الاسم المعروض</th>
                                                <th>المجموعة</th>
                                                <th>الوصف</th>
                                                <th>الأدوار</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permissions as $permission)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input select-item" type="checkbox" id="permission{{ $permission->id }}" value="{{ $permission->id }}">
                                                            <label class="form-check-label" for="permission{{ $permission->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $permission->name }}</td>
                                                    <td>{{ $permission->display_name }}</td>
                                                    <td>{{ $permission->group }}</td>
                                                    <td>{{ $permission->description }}</td>
                                                    <td>
                                                        <div class="role-badges">
                                                            @foreach($permission->roles as $role)
                                                                <span class="role-badge role-{{ $role->name }}">{{ __('roles.' . $role->name) }}</span>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button type="button" class="btn btn-sm btn-primary edit-permission" data-id="{{ $permission->id }}" data-bs-toggle="tooltip" title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @if(!in_array($permission->name, ['manage_users', 'manage_roles', 'manage_permissions']))
                                                                <button type="button" class="btn btn-sm btn-danger delete-permission" data-id="{{ $permission->id }}" data-bs-toggle="tooltip" title="حذف">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif
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
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="assign-role">تعيين إلى دور</a></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="remove-role">إزالة من دور</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="delete">حذف المحدد</a></li>
                                            </ul>
                                        </div>
                                        <span class="selected-count ms-2">0 محدد</span>
                                    </div>
                                    
                                    <div class="pagination-container">
                                        {{ $permissions->appends(request()->except('page'))->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <h5>لا توجد صلاحيات</h5>
                                    <p>لم يتم العثور على أي صلاحيات مطابقة لمعايير البحث.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                                        <i class="fas fa-plus-circle"></i> إضافة صلاحية جديدة
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-chart-pie"></i> توزيع الصلاحيات حسب المجموعة
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <canvas id="permissionsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-info-circle"></i> معلومات الصلاحيات
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="info-box">
                                <h6>ما هي الصلاحيات؟</h6>
                                <p>الصلاحيات هي إذن للقيام بإجراء معين في النظام. يتم تجميع الصلاحيات في أدوار لتسهيل إدارتها وتعيينها للمستخدمين.</p>
                            </div>
                            
                            <div class="info-box mt-4">
                                <h6>إحصائيات الصلاحيات</h6>
                                <ul class="permission-stats-list">
                                    <li>
                                        <span class="stat-label">إجمالي الصلاحيات:</span>
                                        <span class="stat-value">{{ $total_permissions }}</span>
                                    </li>
                                    <li>
                                        <span class="stat-label">عدد المجموعات:</span>
                                        <span class="stat-value">{{ count($permission_groups) }}</span>
                                    </li>
                                    <li>
                                        <span class="stat-label">الصلاحيات المستخدمة:</span>
                                        <span class="stat-value">{{ $used_permissions }} ({{ number_format(($used_permissions / $total_permissions) * 100, 1) }}%)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة صلاحية جديدة -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPermissionModalLabel">إضافة صلاحية جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الصلاحية <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="form-text">يجب أن يكون الاسم بالإنجليزية وبدون مسافات (مثال: create_posts)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">الاسم المعروض <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="display_name" name="display_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="group" class="form-label">المجموعة <span class="text-danger">*</span></label>
                        <select class="form-select" id="group" name="group" required>
                            <option value="" selected disabled>اختر المجموعة</option>
                            @foreach($permission_groups as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 d-none" id="newGroupContainer">
                        <label for="new_group" class="form-label">اسم المجموعة الجديدة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_group" name="new_group">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">تعيين إلى الأدوار</label>
                        <div class="roles-selection">
                            @foreach($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}">
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        <span class="role-badge role-{{ $role->name }}">{{ __('roles.' . $role->name) }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة الصلاحية</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تعديل صلاحية -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPermissionModalLabel">تعديل الصلاحية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="editPermissionForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- سيتم تحميل محتوى النموذج عبر AJAX -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
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

<!-- Modal تعيين إلى دور -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRoleModalLabel">تعيين الصلاحيات إلى دور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="assignRoleForm" action="{{ route('admin.permissions.assign-role') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role_id" class="form-label">اختر الدور <span class="text-danger">*</span></label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="" selected disabled>اختر الدور</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ __('roles.' . $role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <input type="hidden" name="permission_ids" id="assignPermissionIds" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تعيين إلى الدور</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal إزالة من دور -->
<div class="modal fade" id="removeRoleModal" tabindex="-1" aria-labelledby="removeRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeRoleModalLabel">إزالة الصلاحيات من دور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="removeRoleForm" action="{{ route('admin.permissions.remove-role') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="remove_role_id" class="form-label">اختر الدور <span class="text-danger">*</span></label>
                        <select class="form-select" id="remove_role_id" name="role_id" required>
                            <option value="" selected disabled>اختر الدور</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ __('roles.' . $role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <input type="hidden" name="permission_ids" id="removePermissionIds" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إزالة من الدور</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // إظهار/إخفاء حقل المجموعة الجديدة
        $('#group').change(function() {
            if ($(this).val() === 'other') {
                $('#newGroupContainer').removeClass('d-none');
                $('#new_group').prop('required', true);
            } else {
                $('#newGroupContainer').addClass('d-none');
                $('#new_group').prop('required', false);
            }
        });
        
        // تحديد الكل
        $('#selectAll').change(function() {
            $('.select-item').prop('checked', $(this).prop('checked'));
            updateBulkActions();
        });
        
        // تحديث زر الإجراءات الجماعية
        $('.select-item').change(function() {
            updateBulkActions();
        });
        
        function updateBulkActions() {
            const selectedCount = $('.select-item:checked').length;
            $('.selected-count').text(selectedCount + ' محدد');
            
            if (selectedCount > 0) {
                $('#bulkActionsDropdown').prop('disabled', false);
            } else {
                $('#bulkActionsDropdown').prop('disabled', true);
            }
        }
        
        // تحميل بيانات الصلاحية للتعديل
        $('.edit-permission').click(function() {
            const permissionId = $(this).data('id');
            $('#editPermissionForm').attr('action', `/admin/permissions/${permissionId}`);
            
            // إظهار المودال مع مؤشر التحميل
            $('#editPermissionModal').modal('show');
            
            // تحميل بيانات الصلاحية عبر AJAX
            $.ajax({
                url: `/admin/permissions/${permissionId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#editPermissionModal .modal-body').html(response);
                    
                    // إظهار/إخفاء حقل المجموعة الجديدة
                    $('#edit_group').change(function() {
                        if ($(this).val() === 'other') {
                            $('#editNewGroupContainer').removeClass('d-none');
                            $('#edit_new_group').prop('required', true);
                        } else {
                            $('#editNewGroupContainer').addClass('d-none');
                            $('#edit_new_group').prop('required', false);
                        }
                    });
                },
                error: function() {
                    $('#editPermissionModal .modal-body').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل بيانات الصلاحية</div>');
                }
            });
        });
        
        // حذف صلاحية
        $('.delete-permission').click(function() {
            const permissionId = $(this).data('id');
            
            if (confirm('هل أنت متأكد من رغبتك في حذف هذه الصلاحية؟ سيتم إزالة هذه الصلاحية من جميع الأدوار المرتبطة بها.')) {
                $.ajax({
                    url: `/admin/permissions/${permissionId}`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            // إعادة تحميل الصفحة
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('حدث خطأ أثناء حذف الصلاحية');
                    }
                });
            }
        });
        
        // الإجراءات الجماعية
        $('.bulk-action').click(function(e) {
            e.preventDefault();
            
            const action = $(this).data('action');
            const selectedIds = $('.select-item:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedIds.length === 0) {
                return;
            }
            
            switch (action) {
                case 'assign-role':
                    $('#assignPermissionIds').val(selectedIds.join(','));
                    $('#assignRoleModal').modal('show');
                    break;
                case 'remove-role':
                    $('#removePermissionIds').val(selectedIds.join(','));
                    $('#removeRoleModal').modal('show');
                    break;
                case 'delete':
                    if (confirm('هل أنت متأكد من رغبتك في حذف الصلاحيات المحددة؟ سيتم إزالة هذه الصلاحيات من جميع الأدوار المرتبطة بها.')) {
                        $.ajax({
                            url: '/admin/permissions/bulk-delete',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                ids: selectedIds
                            },
                            success: function(response) {
                                if (response.status) {
                                    toastr.success(response.message);
                                    // إعادة تحميل الصفحة
                                    location.reload();
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error('حدث خطأ أثناء حذف الصلاحيات');
                            }
                        });
                    }
                    break;
            }
        });
        
        // رسم مخطط توزيع الصلاحيات
        const ctx = document.getElementById('permissionsChart').getContext('2d');
        const permissionsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($permission_groups) !!},
                datasets: [{
                    data: {!! json_encode($permissions_by_group_count) !!},
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#5a5c69', '#6f42c1', '#fd7e14', '#20c9a6', '#858796'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                family: 'Tajawal, sans-serif'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
