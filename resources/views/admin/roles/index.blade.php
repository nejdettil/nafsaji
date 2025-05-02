@extends('layouts.dashboard')

@section('title', 'إدارة الأدوار والصلاحيات - نفسجي للتمكين النفسي')

@section('content')
<div class="roles-management-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">إدارة الأدوار والصلاحيات</h1>
                    <p class="dashboard-subtitle">إدارة أدوار المستخدمين وصلاحياتهم في النظام</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            <i class="fas fa-plus-circle"></i> إضافة دور جديد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-user-tag"></i> قائمة الأدوار
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($roles) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>الاسم</th>
                                                <th>الوصف</th>
                                                <th>عدد المستخدمين</th>
                                                <th>عدد الصلاحيات</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $role)
                                                <tr>
                                                    <td>
                                                        <span class="role-badge role-{{ $role->name }}">{{ __('roles.' . $role->name) }}</span>
                                                    </td>
                                                    <td>{{ $role->description ?? __('roles.' . $role->name . '_description') }}</td>
                                                    <td>{{ $role->users_count }}</td>
                                                    <td>{{ $role->permissions_count }}</td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button type="button" class="btn btn-sm btn-primary edit-role" data-id="{{ $role->id }}" data-bs-toggle="tooltip" title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @if(!in_array($role->name, ['admin', 'user', 'specialist']))
                                                                <button type="button" class="btn btn-sm btn-danger delete-role" data-id="{{ $role->id }}" data-bs-toggle="tooltip" title="حذف">
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
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <h5>لا توجد أدوار</h5>
                                    <p>لم يتم العثور على أي أدوار في النظام.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                        <i class="fas fa-plus-circle"></i> إضافة دور جديد
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-info-circle"></i> معلومات الأدوار
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="info-box">
                                <h6>ما هي الأدوار؟</h6>
                                <p>الأدوار هي مجموعات من الصلاحيات التي تحدد ما يمكن للمستخدم القيام به في النظام. يمكن تعيين دور واحد أو أكثر لكل مستخدم.</p>
                            </div>
                            
                            <div class="info-box mt-4">
                                <h6>الأدوار الافتراضية</h6>
                                <ul class="role-info-list">
                                    <li>
                                        <span class="role-badge role-admin">مدير</span>
                                        <p>يمتلك جميع الصلاحيات في النظام ويمكنه إدارة جميع الموارد.</p>
                                    </li>
                                    <li>
                                        <span class="role-badge role-specialist">مختص</span>
                                        <p>يمكنه إدارة الجلسات والحجوزات الخاصة به وتقديم الخدمات للمستخدمين.</p>
                                    </li>
                                    <li>
                                        <span class="role-badge role-user">مستخدم</span>
                                        <p>يمكنه حجز الجلسات والاستفادة من خدمات المنصة.</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-key"></i> قائمة الصلاحيات
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="search-box">
                                    <input type="text" class="form-control" id="permissionSearch" placeholder="بحث في الصلاحيات...">
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($permissions) > 0)
                                <div class="permissions-list">
                                    @foreach($permissions_by_group as $group => $group_permissions)
                                        <div class="permission-group">
                                            <h6 class="permission-group-title">{{ $group }}</h6>
                                            <div class="row">
                                                @foreach($group_permissions as $permission)
                                                    <div class="col-md-4 col-sm-6 permission-item">
                                                        <div class="permission-card">
                                                            <div class="permission-name">{{ $permission->display_name ?? $permission->name }}</div>
                                                            <div class="permission-description">{{ $permission->description }}</div>
                                                            <div class="permission-roles">
                                                                @foreach($permission->roles as $role)
                                                                    <span class="role-badge role-{{ $role->name }}">{{ __('roles.' . $role->name) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <h5>لا توجد صلاحيات</h5>
                                    <p>لم يتم العثور على أي صلاحيات في النظام.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة دور جديد -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoleModalLabel">إضافة دور جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الدور <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="form-text">يجب أن يكون الاسم بالإنجليزية وبدون مسافات (مثال: content_manager)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">الاسم المعروض <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="display_name" name="display_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الصلاحيات <span class="text-danger">*</span></label>
                        <div class="permissions-selection">
                            @foreach($permissions_by_group as $group => $group_permissions)
                                <div class="permission-group-selection">
                                    <div class="permission-group-header">
                                        <div class="form-check">
                                            <input class="form-check-input permission-group-checkbox" type="checkbox" id="group_{{ Str::slug($group) }}">
                                            <label class="form-check-label" for="group_{{ Str::slug($group) }}">
                                                <strong>{{ $group }}</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="permission-group-items">
                                        <div class="row">
                                            @foreach($group_permissions as $permission)
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}">
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->display_name ?? $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة الدور</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تعديل دور -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">تعديل الدور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="editRoleForm" action="" method="POST">
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // البحث في الصلاحيات
        $('#permissionSearch').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            
            $('.permission-item').each(function() {
                const permissionName = $(this).find('.permission-name').text().toLowerCase();
                const permissionDescription = $(this).find('.permission-description').text().toLowerCase();
                
                if (permissionName.includes(searchText) || permissionDescription.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            // إظهار/إخفاء عناوين المجموعات
            $('.permission-group').each(function() {
                const visibleItems = $(this).find('.permission-item:visible').length;
                
                if (visibleItems === 0) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
        
        // تحديد/إلغاء تحديد جميع صلاحيات المجموعة
        $('.permission-group-checkbox').change(function() {
            const isChecked = $(this).prop('checked');
            $(this).closest('.permission-group-selection').find('.permission-checkbox').prop('checked', isChecked);
        });
        
        // تحديث حالة checkbox المجموعة عند تغيير حالة checkboxes الصلاحيات
        $('.permission-checkbox').change(function() {
            const groupSelection = $(this).closest('.permission-group-selection');
            const totalPermissions = groupSelection.find('.permission-checkbox').length;
            const checkedPermissions = groupSelection.find('.permission-checkbox:checked').length;
            
            groupSelection.find('.permission-group-checkbox').prop('checked', totalPermissions === checkedPermissions);
        });
        
        // تحميل بيانات الدور للتعديل
        $('.edit-role').click(function() {
            const roleId = $(this).data('id');
            $('#editRoleForm').attr('action', `/admin/roles/${roleId}`);
            
            // إظهار المودال مع مؤشر التحميل
            $('#editRoleModal').modal('show');
            
            // تحميل بيانات الدور عبر AJAX
            $.ajax({
                url: `/admin/roles/${roleId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#editRoleModal .modal-body').html(response);
                    
                    // تفعيل وظائف تحديد المجموعات
                    $('.permission-group-checkbox').change(function() {
                        const isChecked = $(this).prop('checked');
                        $(this).closest('.permission-group-selection').find('.permission-checkbox').prop('checked', isChecked);
                    });
                    
                    $('.permission-checkbox').change(function() {
                        const groupSelection = $(this).closest('.permission-group-selection');
                        const totalPermissions = groupSelection.find('.permission-checkbox').length;
                        const checkedPermissions = groupSelection.find('.permission-checkbox:checked').length;
                        
                        groupSelection.find('.permission-group-checkbox').prop('checked', totalPermissions === checkedPermissions);
                    });
                },
                error: function() {
                    $('#editRoleModal .modal-body').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل بيانات الدور</div>');
                }
            });
        });
        
        // حذف دور
        $('.delete-role').click(function() {
            const roleId = $(this).data('id');
            
            if (confirm('هل أنت متأكد من رغبتك في حذف هذا الدور؟ سيتم إزالة هذا الدور من جميع المستخدمين المرتبطين به.')) {
                $.ajax({
                    url: `/admin/roles/${roleId}`,
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
                        toastr.error('حدث خطأ أثناء حذف الدور');
                    }
                });
            }
        });
    });
</script>
@endsection
