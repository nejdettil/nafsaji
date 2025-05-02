@extends('layouts.dashboard')

@section('title', 'إدارة المستخدمين - نفسجي للتمكين النفسي')

@section('content')
<div class="users-management-page">
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="dashboard-title">إدارة المستخدمين</h1>
                    <p class="dashboard-subtitle">إدارة حسابات المستخدمين والصلاحيات</p>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-actions">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importUsersModal">
                            <i class="fas fa-file-import"></i> استيراد مستخدمين
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export"></i> تصدير
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.users.export', ['format' => 'excel']) }}">Excel</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users.export', ['format' => 'csv']) }}">CSV</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users.export', ['format' => 'pdf']) }}">PDF</a></li>
                            </ul>
                        </div>
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
                                <i class="fas fa-users"></i> قائمة المستخدمين
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="search-box">
                                    <form action="{{ route('admin.users.index') }}" method="GET">
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
                                        <li><a class="dropdown-item {{ request('status') == 'all' || !request('status') ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}">جميع المستخدمين</a></li>
                                        <li><a class="dropdown-item {{ request('status') == 'active' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('status', 'page'), ['status' => 'active'])) }}">المستخدمين النشطين</a></li>
                                        <li><a class="dropdown-item {{ request('status') == 'inactive' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('status', 'page'), ['status' => 'inactive'])) }}">المستخدمين غير النشطين</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item {{ request('role') == 'admin' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}">المدراء</a></li>
                                        <li><a class="dropdown-item {{ request('role') == 'user' ? 'active' : '' }}" href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'user'])) }}">المستخدمين العاديين</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            @if(count($users) > 0)
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
                                                <th>المستخدم</th>
                                                <th>البريد الإلكتروني</th>
                                                <th>رقم الهاتف</th>
                                                <th>الدور</th>
                                                <th>الحالة</th>
                                                <th>تاريخ التسجيل</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input select-item" type="checkbox" id="user{{ $user->id }}" value="{{ $user->id }}">
                                                            <label class="form-check-label" for="user{{ $user->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="user-info">
                                                            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/images/default-avatar.png') }}" alt="{{ $user->full_name }}" class="user-avatar">
                                                            <div class="user-details">
                                                                <h6 class="user-name">{{ $user->full_name }}</h6>
                                                                <p class="user-id">#{{ $user->id }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->phone ?? '-' }}</td>
                                                    <td>
                                                        @foreach($user->roles as $role)
                                                            <span class="role-badge role-{{ $role->name }}">{{ __('roles.' . $role->name) }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-switch" type="checkbox" id="statusSwitch{{ $user->id }}" {{ $user->status == 'active' ? 'checked' : '' }} data-id="{{ $user->id }}">
                                                            <label class="form-check-label" for="statusSwitch{{ $user->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-primary edit-user" data-id="{{ $user->id }}" data-bs-toggle="tooltip" title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger delete-user" data-id="{{ $user->id }}" data-bs-toggle="tooltip" title="حذف">
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
                                                <li><a class="dropdown-item bulk-action" href="#" data-action="delete">حذف المحدد</a></li>
                                            </ul>
                                        </div>
                                        <span class="selected-count ms-2">0 محدد</span>
                                    </div>
                                    
                                    <div class="pagination-container">
                                        {{ $users->appends(request()->except('page'))->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h5>لا يوجد مستخدمين</h5>
                                    <p>لم يتم العثور على أي مستخدمين مطابقين لمعايير البحث.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                        <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- إحصائيات المستخدمين -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-chart-line"></i> إحصائيات المستخدمين
                            </h5>
                            <div class="dashboard-card-actions">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary active" data-period="week">أسبوعي</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-period="month">شهري</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-period="year">سنوي</button>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            <canvas id="usersChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-chart-pie"></i> توزيع المستخدمين
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <canvas id="userDistributionChart" height="260"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة مستخدم جديد -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">إضافة مستخدم جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name">الاسم الأول</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name">الاسم الأخير</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone">رقم الهاتف</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password">كلمة المرور</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password_confirmation">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="role">الدور</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">اختر الدور</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ __('roles.' . $role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="status">الحالة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" selected>نشط</option>
                                    <option value="inactive">غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="address">العنوان</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_welcome_email" name="send_welcome_email" checked>
                            <label class="form-check-label" for="send_welcome_email">
                                إرسال بريد إلكتروني ترحيبي
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المستخدم</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal تعديل مستخدم -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">تعديل المستخدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.users.update', 0) }}" method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_first_name">الاسم الأول</label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_last_name">الاسم الأخير</label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_email">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_phone">رقم الهاتف</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_password">كلمة المرور (اتركها فارغة للاحتفاظ بالحالية)</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_password_confirmation">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_role">الدور</label>
                                <select class="form-select" id="edit_role" name="role" required>
                                    <option value="">اختر الدور</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ __('roles.' . $role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_status">الحالة</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="active">نشط</option>
                                    <option value="inactive">غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_address">العنوان</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
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

<!-- Modal حذف مستخدم -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">حذف المستخدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.users.destroy', 0) }}" method="POST" id="deleteUserForm">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete_user_id" name="id">
                <div class="modal-body">
                    <p>هل أنت متأكد من رغبتك في حذف هذا المستخدم؟</p>
                    <p class="text-danger">تحذير: هذا الإجراء لا يمكن التراجع عنه.</p>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="delete_confirm" name="delete_confirm" required>
                            <label class="form-check-label" for="delete_confirm">
                                نعم، أنا متأكد من رغبتي في حذف هذا المستخدم
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal استيراد مستخدمين -->
<div class="modal fade" id="importUsersModal" tabindex="-1" aria-labelledby="importUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importUsersModalLabel">استيراد مستخدمين</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" id="importUsersForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="import_file">ملف الاستيراد</label>
                        <input type="file" class="form-control" id="import_file" name="import_file" accept=".csv, .xlsx" required>
                        <small class="form-text text-muted">يجب أن يكون الملف بتنسيق CSV أو Excel.</small>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_header_row" name="has_header_row" checked>
                            <label class="form-check-label" for="has_header_row">
                                الملف يحتوي على صف عناوين
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <a href="{{ route('admin.users.download-template') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> تنزيل قالب الاستيراد
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">استيراد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal الإجراءات الجماعية -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">تأكيد الإجراء الجماعي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.users.bulk-action') }}" method="POST" id="bulkActionForm">
                @csrf
                <input type="hidden" id="bulk_action_type" name="action">
                <input type="hidden" id="bulk_action_ids" name="ids">
                <div class="modal-body">
                    <p id="bulk_action_message"></p>
                    <div class="form-group mb-3" id="bulk_action_confirm_container">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bulk_action_confirm" name="confirm" required>
                            <label class="form-check-label" for="bulk_action_confirm" id="bulk_action_confirm_label">
                                نعم، أنا متأكد من رغبتي في تنفيذ هذا الإجراء
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="bulk_action_submit">تأكيد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* أنماط عامة للصفحة */
    .users-management-page {
        background-color: #f8f9fa;
    }
    
    .dashboard-header {
        background-color: #fff;
        padding: 30px 0;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .dashboard-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }
    
    .dashboard-subtitle {
        color: #666;
        margin-bottom: 0;
    }
    
    .dashboard-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .dashboard-content {
        margin-bottom: 30px;
    }
    
    /* بطاقات المحتوى */
    .dashboard-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .dashboard-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .dashboard-card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 0;
        color: #333;
        display: flex;
        align-items: center;
    }
    
    .dashboard-card-title i {
        margin-left: 10px;
        color: #6a1b9a;
    }
    
    .dashboard-card-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dashboard-card-body {
        padding: 20px;
    }
    
    /* جدول المستخدمين */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        font-weight: 600;
        color: #333;
        border-top: none;
    }
    
    .user-info {
        display: flex;
        align-items: center;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        object-fit: cover;
    }
    
    .user-details {
        display: flex;
        flex-direction: column;
    }
    
    .user-name {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 0;
        color: #333;
    }
    
    .user-id {
        font-size: 12px;
        margin-bottom: 0;
        color: #666;
    }
    
    .role-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .role-admin {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .role-user {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .role-specialist {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .form-switch {
        padding-right: 2.5em;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    /* مربع البحث */
    .search-box {
        max-width: 300px;
    }
    
    /* الإجراءات الجماعية */
    .bulk-actions {
        display: flex;
        align-items: center;
    }
    
    .selected-count {
        font-size: 14px;
        color: #666;
    }
    
    /* الترقيم الصفحي */
    .pagination-container {
        display: flex;
        justify-content: center;
    }
    
    .pagination {
        --bs-pagination-color: #6a1b9a;
        --bs-pagination-hover-color: #6a1b9a;
        --bs-pagination-focus-color: #6a1b9a;
        --bs-pagination-active-bg: #6a1b9a;
        --bs-pagination-active-border-color: #6a1b9a;
    }
    
    /* حالة فارغة */
    .empty-state {
        text-align: center;
        padding: 30px 20px;
    }
    
    .empty-state-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #f0e6f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #6a1b9a;
        margin: 0 auto 15px;
    }
    
    .empty-state h5 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .empty-state p {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
    }
    
    /* تصميم متجاوب */
    @media (max-width: 991px) {
        .dashboard-actions {
            margin-top: 15px;
            justify-content: flex-start;
        }
    }
    
    @media (max-width: 767px) {
        .dashboard-header {
            padding: 20px 0;
        }
        
        .dashboard-title {
            font-size: 20px;
        }
        
        .dashboard-actions {
            flex-wrap: wrap;
        }
        
        .table-responsive {
            border: none;
        }
    }
    
    @media (max-width: 575px) {
        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-avatar {
            margin-bottom: 10px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // تهيئة الرسم البياني للمستخدمين
        var usersChartEl = document.getElementById('usersChart');
        if (usersChartEl) {
            var usersChart = new Chart(usersChartEl, {
                type: 'line',
                data: {
                    labels: @json($usersChartData['labels']),
                    datasets: [{
                        label: 'المستخدمين الجدد',
                        data: @json($usersChartData['data']),
                        backgroundColor: 'rgba(106, 27, 154, 0.1)',
                        borderColor: '#6a1b9a',
                        borderWidth: 2,
                        pointBackgroundColor: '#6a1b9a',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
            
            // تغيير فترة الرسم البياني
            $('.btn-group button').on('click', function() {
                $('.btn-group button').removeClass('active');
                $(this).addClass('active');
                
                var period = $(this).data('period');
                var url = "{{ route('admin.users-chart-data') }}";
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        period: period
                    },
                    success: function(response) {
                        usersChart.data.labels = response.labels;
                        usersChart.data.datasets[0].data = response.data;
                        usersChart.update();
                    }
                });
            });
        }
        
        // تهيئة الرسم البياني لتوزيع المستخدمين
        var userDistributionChartEl = document.getElementById('userDistributionChart');
        if (userDistributionChartEl) {
            var userDistributionChart = new Chart(userDistributionChartEl, {
                type: 'doughnut',
                data: {
                    labels: @json($userDistributionData['labels']),
                    datasets: [{
                        data: @json($userDistributionData['data']),
                        backgroundColor: [
                            '#6a1b9a',
                            '#8e24aa',
                            '#ab47bc',
                            '#ce93d8',
                            '#e1bee7'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                boxWidth: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = Math.round((value / total) * 100);
                                    return label + ': ' + percentage + '%';
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
        
        // تحديد/إلغاء تحديد جميع العناصر
        $('#selectAll').on('change', function() {
            $('.select-item').prop('checked', $(this).is(':checked'));
            updateBulkActions();
        });
        
        // تحديث حالة زر الإجراءات الجماعية عند تغيير التحديد
        $('.select-item').on('change', function() {
            updateBulkActions();
        });
        
        // تحديث حالة الإجراءات الجماعية
        function updateBulkActions() {
            var selectedCount = $('.select-item:checked').length;
            $('.selected-count').text(selectedCount + ' محدد');
            
            if (selectedCount > 0) {
                $('#bulkActionsDropdown').prop('disabled', false);
            } else {
                $('#bulkActionsDropdown').prop('disabled', true);
            }
        }
        
        // تغيير حالة المستخدم
        $('.status-switch').on('change', function() {
            var userId = $(this).data('id');
            var isActive = $(this).is(':checked');
            
            $.ajax({
                url: "{{ route('admin.users.update-status') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: userId,
                    status: isActive ? 'active' : 'inactive'
                },
                success: function(response) {
                    if (response.success) {
                        // يمكن إضافة إشعار نجاح هنا
                    } else {
                        // إعادة الحالة السابقة في حالة الفشل
                        $(this).prop('checked', !isActive);
                    }
                }.bind(this),
                error: function() {
                    // إعادة الحالة السابقة في حالة الخطأ
                    $(this).prop('checked', !isActive);
                }.bind(this)
            });
        });
        
        // تعديل المستخدم
        $('.edit-user').on('click', function() {
            var userId = $(this).data('id');
            
            // جلب بيانات المستخدم
            $.ajax({
                url: "{{ route('admin.users.get') }}",
                type: 'GET',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        var user = response.user;
                        
                        $('#edit_user_id').val(user.id);
                        $('#edit_first_name').val(user.first_name);
                        $('#edit_last_name').val(user.last_name);
                        $('#edit_email').val(user.email);
                        $('#edit_phone').val(user.phone);
                        $('#edit_address').val(user.address);
                        $('#edit_status').val(user.status);
                        $('#edit_role').val(user.roles[0].name);
                        
                        var updateUrl = "{{ route('admin.users.update', ':id') }}".replace(':id', user.id);
                        $('#editUserForm').attr('action', updateUrl);
                        
                        $('#editUserModal').modal('show');
                    }
                }
            });
        });
        
        // حذف المستخدم
        $('.delete-user').on('click', function() {
            var userId = $(this).data('id');
            
            $('#delete_user_id').val(userId);
            
            var deleteUrl = "{{ route('admin.users.destroy', ':id') }}".replace(':id', userId);
            $('#deleteUserForm').attr('action', deleteUrl);
            
            $('#deleteUserModal').modal('show');
        });
        
        // الإجراءات الجماعية
        $('.bulk-action').on('click', function(e) {
            e.preventDefault();
            
            var action = $(this).data('action');
            var selectedIds = [];
            
            $('.select-item:checked').each(function() {
                selectedIds.push($(this).val());
            });
            
            $('#bulk_action_type').val(action);
            $('#bulk_action_ids').val(selectedIds.join(','));
            
            // تعيين رسالة التأكيد
            if (action === 'activate') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في تفعيل المستخدمين المحددين؟');
                $('#bulk_action_submit').removeClass('btn-danger').addClass('btn-primary').text('تأكيد التفعيل');
                $('#bulk_action_confirm_container').hide();
            } else if (action === 'deactivate') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في تعطيل المستخدمين المحددين؟');
                $('#bulk_action_submit').removeClass('btn-danger').addClass('btn-primary').text('تأكيد التعطيل');
                $('#bulk_action_confirm_container').hide();
            } else if (action === 'delete') {
                $('#bulk_action_message').text('هل أنت متأكد من رغبتك في حذف المستخدمين المحددين؟');
                $('#bulk_action_message').append('<p class="text-danger">تحذير: هذا الإجراء لا يمكن التراجع عنه.</p>');
                $('#bulk_action_submit').removeClass('btn-primary').addClass('btn-danger').text('تأكيد الحذف');
                $('#bulk_action_confirm_label').text('نعم، أنا متأكد من رغبتي في حذف المستخدمين المحددين');
                $('#bulk_action_confirm_container').show();
            }
            
            $('#bulkActionModal').modal('show');
        });
        
        // التحقق من صحة نموذج إضافة مستخدم
        $('#addUserForm').on('submit', function(e) {
            var password = $('#password').val();
            var passwordConfirmation = $('#password_confirmation').val();
            
            if (password !== passwordConfirmation) {
                e.preventDefault();
                alert('كلمة المرور وتأكيدها غير متطابقين');
            }
        });
        
        // التحقق من صحة نموذج تعديل مستخدم
        $('#editUserForm').on('submit', function(e) {
            var password = $('#edit_password').val();
            var passwordConfirmation = $('#edit_password_confirmation').val();
            
            if (password && password !== passwordConfirmation) {
                e.preventDefault();
                alert('كلمة المرور وتأكيدها غير متطابقين');
            }
        });
    });
</script>
@endsection
