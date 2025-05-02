@extends('layouts.dashboard')

@section('title', 'النسخ الاحتياطي')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">النسخ الاحتياطي</h4>
                    <p class="card-category">إدارة النسخ الاحتياطية لقاعدة البيانات والملفات</p>
                </div>
                <div class="card-body">
                    <div class="backups-container">
                        <ul class="nav nav-tabs" id="backupTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="database-tab" data-bs-toggle="tab" data-bs-target="#database" type="button" role="tab" aria-controls="database" aria-selected="true">قاعدة البيانات</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab" aria-controls="files" aria-selected="false">الملفات</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="false">الجدولة</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">الإعدادات</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="backupTabsContent">
                            <!-- قاعدة البيانات -->
                            <div class="tab-pane fade show active" id="database" role="tabpanel" aria-labelledby="database-tab">
                                <div class="tab-header d-flex justify-content-between align-items-center my-3">
                                    <h5 class="mb-0">نسخ قاعدة البيانات الاحتياطية</h5>
                                    <div>
                                        <button class="btn btn-success" id="createDatabaseBackup">
                                            <i class="fas fa-database"></i> إنشاء نسخة احتياطية جديدة
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>اسم الملف</th>
                                                <th>الحجم</th>
                                                <th>تاريخ الإنشاء</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($databaseBackups) && count($databaseBackups) > 0)
                                                @foreach($databaseBackups as $backup)
                                                    <tr>
                                                        <td>{{ $backup->filename }}</td>
                                                        <td>{{ $backup->size }}</td>
                                                        <td>{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $backup->status == 'completed' ? 'success' : ($backup->status == 'in_progress' ? 'warning' : 'danger') }}">
                                                                {{ $backup->status == 'completed' ? 'مكتمل' : ($backup->status == 'in_progress' ? 'جاري التنفيذ' : 'فشل') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('admin.backups.database.download', $backup->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                <a href="{{ route('admin.backups.database.restore', $backup->id) }}" class="btn btn-sm btn-warning restore-backup" data-type="database" data-id="{{ $backup->id }}">
                                                                    <i class="fas fa-undo"></i>
                                                                </a>
                                                                <button class="btn btn-sm btn-danger delete-backup" data-type="database" data-id="{{ $backup->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">لا توجد نسخ احتياطية لقاعدة البيانات</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- الملفات -->
                            <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
                                <div class="tab-header d-flex justify-content-between align-items-center my-3">
                                    <h5 class="mb-0">نسخ الملفات الاحتياطية</h5>
                                    <div>
                                        <button class="btn btn-success" id="createFilesBackup">
                                            <i class="fas fa-file-archive"></i> إنشاء نسخة احتياطية جديدة
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>اسم الملف</th>
                                                <th>الحجم</th>
                                                <th>تاريخ الإنشاء</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($filesBackups) && count($filesBackups) > 0)
                                                @foreach($filesBackups as $backup)
                                                    <tr>
                                                        <td>{{ $backup->filename }}</td>
                                                        <td>{{ $backup->size }}</td>
                                                        <td>{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $backup->status == 'completed' ? 'success' : ($backup->status == 'in_progress' ? 'warning' : 'danger') }}">
                                                                {{ $backup->status == 'completed' ? 'مكتمل' : ($backup->status == 'in_progress' ? 'جاري التنفيذ' : 'فشل') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('admin.backups.files.download', $backup->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                <a href="{{ route('admin.backups.files.restore', $backup->id) }}" class="btn btn-sm btn-warning restore-backup" data-type="files" data-id="{{ $backup->id }}">
                                                                    <i class="fas fa-undo"></i>
                                                                </a>
                                                                <button class="btn btn-sm btn-danger delete-backup" data-type="files" data-id="{{ $backup->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">لا توجد نسخ احتياطية للملفات</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- الجدولة -->
                            <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                                <div class="tab-header my-3">
                                    <h5 class="mb-3">جدولة النسخ الاحتياطي التلقائي</h5>
                                    <p class="text-muted">قم بإعداد جدولة للنسخ الاحتياطي التلقائي لقاعدة البيانات والملفات</p>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">جدولة نسخ قاعدة البيانات</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.backups.schedule.database') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="dbBackupFrequency">التكرار</label>
                                                        <select class="form-control" id="dbBackupFrequency" name="frequency" required>
                                                            <option value="daily" {{ isset($dbSchedule) && $dbSchedule->frequency == 'daily' ? 'selected' : '' }}>يومي</option>
                                                            <option value="weekly" {{ isset($dbSchedule) && $dbSchedule->frequency == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                                                            <option value="monthly" {{ isset($dbSchedule) && $dbSchedule->frequency == 'monthly' ? 'selected' : '' }}>شهري</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="dbBackupTime">وقت التنفيذ</label>
                                                        <input type="time" class="form-control" id="dbBackupTime" name="time" value="{{ isset($dbSchedule) ? $dbSchedule->time : '00:00' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="weekly-options" style="{{ isset($dbSchedule) && $dbSchedule->frequency == 'weekly' ? '' : 'display: none;' }}">
                                                <div class="form-group mb-3">
                                                    <label>يوم التنفيذ</label>
                                                    <div class="d-flex flex-wrap">
                                                        @php
                                                            $days = ['sunday' => 'الأحد', 'monday' => 'الإثنين', 'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة', 'saturday' => 'السبت'];
                                                        @endphp
                                                        
                                                        @foreach($days as $value => $label)
                                                            <div class="form-check me-3 mb-2">
                                                                <input class="form-check-input" type="radio" name="day_of_week" id="db_{{ $value }}" value="{{ $value }}" {{ isset($dbSchedule) && $dbSchedule->day_of_week == $value ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="db_{{ $value }}">
                                                                    {{ $label }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="monthly-options" style="{{ isset($dbSchedule) && $dbSchedule->frequency == 'monthly' ? '' : 'display: none;' }}">
                                                <div class="form-group mb-3">
                                                    <label for="dbDayOfMonth">يوم الشهر</label>
                                                    <select class="form-control" id="dbDayOfMonth" name="day_of_month">
                                                        @for($i = 1; $i <= 31; $i++)
                                                            <option value="{{ $i }}" {{ isset($dbSchedule) && $dbSchedule->day_of_month == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="dbBackupRetention">الاحتفاظ بالنسخ (عدد الأيام)</label>
                                                <input type="number" class="form-control" id="dbBackupRetention" name="retention_days" value="{{ isset($dbSchedule) ? $dbSchedule->retention_days : 30 }}" min="1" required>
                                                <small class="form-text text-muted">سيتم حذف النسخ الاحتياطية الأقدم من هذه المدة تلقائياً</small>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="dbBackupActive" name="is_active" value="1" {{ isset($dbSchedule) && $dbSchedule->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dbBackupActive">
                                                    تفعيل الجدولة
                                                </label>
                                            </div>
                                            <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">جدولة نسخ الملفات</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.backups.schedule.files') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="filesBackupFrequency">التكرار</label>
                                                        <select class="form-control" id="filesBackupFrequency" name="frequency" required>
                                                            <option value="daily" {{ isset($filesSchedule) && $filesSchedule->frequency == 'daily' ? 'selected' : '' }}>يومي</option>
                                                            <option value="weekly" {{ isset($filesSchedule) && $filesSchedule->frequency == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                                                            <option value="monthly" {{ isset($filesSchedule) && $filesSchedule->frequency == 'monthly' ? 'selected' : '' }}>شهري</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="filesBackupTime">وقت التنفيذ</label>
                                                        <input type="time" class="form-control" id="filesBackupTime" name="time" value="{{ isset($filesSchedule) ? $filesSchedule->time : '00:00' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="weekly-options-files" style="{{ isset($filesSchedule) && $filesSchedule->frequency == 'weekly' ? '' : 'display: none;' }}">
                                                <div class="form-group mb-3">
                                                    <label>يوم التنفيذ</label>
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($days as $value => $label)
                                                            <div class="form-check me-3 mb-2">
                                                                <input class="form-check-input" type="radio" name="day_of_week" id="files_{{ $value }}" value="{{ $value }}" {{ isset($filesSchedule) && $filesSchedule->day_of_week == $value ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="files_{{ $value }}">
                                                                    {{ $label }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="monthly-options-files" style="{{ isset($filesSchedule) && $filesSchedule->frequency == 'monthly' ? '' : 'display: none;' }}">
                                                <div class="form-group mb-3">
                                                    <label for="filesDayOfMonth">يوم الشهر</label>
                                                    <select class="form-control" id="filesDayOfMonth" name="day_of_month">
                                                        @for($i = 1; $i <= 31; $i++)
                                                            <option value="{{ $i }}" {{ isset($filesSchedule) && $filesSchedule->day_of_month == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="filesBackupRetention">الاحتفاظ بالنسخ (عدد الأيام)</label>
                                                <input type="number" class="form-control" id="filesBackupRetention" name="retention_days" value="{{ isset($filesSchedule) ? $filesSchedule->retention_days : 30 }}" min="1" required>
                                                <small class="form-text text-muted">سيتم حذف النسخ الاحتياطية الأقدم من هذه المدة تلقائياً</small>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label>المجلدات المضمنة</label>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="includeUploads" name="include_uploads" value="1" {{ isset($filesSchedule) && $filesSchedule->include_uploads ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="includeUploads">
                                                        مجلد التحميلات (uploads)
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="includePublic" name="include_public" value="1" {{ isset($filesSchedule) && $filesSchedule->include_public ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="includePublic">
                                                        المجلد العام (public)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="filesBackupActive" name="is_active" value="1" {{ isset($filesSchedule) && $filesSchedule->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="filesBackupActive">
                                                    تفعيل الجدولة
                                                </label>
                                            </div>
                                            <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الإعدادات -->
                            <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                                <div class="tab-header my-3">
                                    <h5 class="mb-3">إعدادات النسخ الاحتياطي</h5>
                                    <p class="text-muted">تكوين إعدادات النسخ الاحتياطي ومكان التخزين</p>
                                </div>
                                
                                <form action="{{ route('admin.backups.settings.update') }}" method="POST">
                                    @csrf
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">إعدادات التخزين</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="storageDriver">مكان التخزين</label>
                                                <select class="form-control" id="storageDriver" name="storage_driver" required>
                                                    <option value="local" {{ isset($settings) && $settings->storage_driver == 'local' ? 'selected' : '' }}>التخزين المحلي</option>
                                                    <option value="s3" {{ isset($settings) && $settings->storage_driver == 's3' ? 'selected' : '' }}>Amazon S3</option>
                                                    <option value="dropbox" {{ isset($settings) && $settings->storage_driver == 'dropbox' ? 'selected' : '' }}>Dropbox</option>
                                                    <option value="google" {{ isset($settings) && $settings->storage_driver == 'google' ? 'selected' : '' }}>Google Drive</option>
                                                </select>
                                            </div>
                                            
                                            <div class="storage-settings local-settings" style="{{ isset($settings) && $settings->storage_driver == 'local' ? '' : 'display: none;' }}">
                                                <div class="form-group mb-3">
                                                    <label for="localPath">مسار التخزين المحلي</label>
                                                    <input type="text" class="form-control" id="localPath" name="local_path" value="{{ isset($settings) ? $settings->local_path : '/backups' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="storage-settings s3-settings" style="{{ isset($settings) && $settings->storage_driver == 's3' ? '' : 'display: none;' }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="s3Key">AWS Access Key</label>
                                                            <input type="text" class="form-control" id="s3Key" name="s3_key" value="{{ isset($settings) ? $settings->s3_key : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="s3Secret">AWS Secret Key</label>
                                                            <input type="password" class="form-control" id="s3Secret" name="s3_secret" value="{{ isset($settings) ? $settings->s3_secret : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="s3Region">AWS Region</label>
                                                            <input type="text" class="form-control" id="s3Region" name="s3_region" value="{{ isset($settings) ? $settings->s3_region : 'us-east-1' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label for="s3Bucket">S3 Bucket</label>
                                                            <input type="text" class="form-control" id="s3Bucket" name="s3_bucket" value="{{ isset($settings) ? $settings->s3_bucket : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="s3Path">S3 Path</label>
                                                    <input type="text" class="form-control" id="s3Path" name="s3_path" value="{{ isset($settings) ? $settings->s3_path : 'backups' }}">
                                                </div>
                                            </div>
                                            
                                            <!-- يمكن إضافة إعدادات Dropbox و Google Drive بنفس الطريقة -->
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">إعدادات الإشعارات</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notifyOnSuccess" name="notify_on_success" value="1" {{ isset($settings) && $settings->notify_on_success ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notifyOnSuccess">
                                                    إرسال إشعار عند نجاح النسخ الاحتياطي
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notifyOnFailure" name="notify_on_failure" value="1" {{ isset($settings) && $settings->notify_on_failure ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notifyOnFailure">
                                                    إرسال إشعار عند فشل النسخ الاحتياطي
                                                </label>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="notificationEmail">البريد الإلكتروني للإشعارات</label>
                                                <input type="email" class="form-control" id="notificationEmail" name="notification_email" value="{{ isset($settings) ? $settings->notification_email : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">إعدادات الضغط</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="compressionType">نوع الضغط</label>
                                                <select class="form-control" id="compressionType" name="compression_type" required>
                                                    <option value="zip" {{ isset($settings) && $settings->compression_type == 'zip' ? 'selected' : '' }}>ZIP</option>
                                                    <option value="gzip" {{ isset($settings) && $settings->compression_type == 'gzip' ? 'selected' : '' }}>GZIP</option>
                                                </select>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="encryptBackups" name="encrypt_backups" value="1" {{ isset($settings) && $settings->encrypt_backups ? 'checked' : '' }}>
                                                <label class="form-check-label" for="encryptBackups">
                                                    تشفير النسخ الاحتياطية
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-info">
                    <h5 class="mb-0">سجل النسخ الاحتياطي</h5>
                </div>
                <div class="card-body">
                    <div class="backup-logs" style="max-height: 300px; overflow-y: auto;">
                        @if(isset($backupLogs) && count($backupLogs) > 0)
                            @foreach($backupLogs as $log)
                                <div class="log-entry {{ $log->status == 'error' ? 'text-danger' : ($log->status == 'warning' ? 'text-warning' : '') }}">
                                    <span class="log-time">[{{ $log->created_at->format('Y-m-d H:i:s') }}]</span>
                                    <span class="log-message">{{ $log->message }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted">لا توجد سجلات</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-success">
                    <h5 class="mb-0">حالة التخزين</h5>
                </div>
                <div class="card-body">
                    <div class="storage-status">
                        <div class="storage-info mb-3">
                            <h6>المساحة المستخدمة للنسخ الاحتياطية</h6>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ isset($storageInfo) ? $storageInfo->usage_percentage : 0 }}%" aria-valuenow="{{ isset($storageInfo) ? $storageInfo->usage_percentage : 0 }}" aria-valuemin="0" aria-valuemax="100">{{ isset($storageInfo) ? $storageInfo->usage_percentage : 0 }}%</div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small>{{ isset($storageInfo) ? $storageInfo->used_space : '0 MB' }} مستخدمة</small>
                                <small>من أصل {{ isset($storageInfo) ? $storageInfo->total_space : '0 GB' }}</small>
                            </div>
                        </div>
                        <div class="storage-details">
                            <div class="row">
                                <div class="col-6">
                                    <div class="storage-item">
                                        <h6>نسخ قاعدة البيانات</h6>
                                        <p class="mb-0">{{ isset($storageInfo) ? $storageInfo->db_backups_count : 0 }} نسخة ({{ isset($storageInfo) ? $storageInfo->db_backups_size : '0 MB' }})</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="storage-item">
                                        <h6>نسخ الملفات</h6>
                                        <p class="mb-0">{{ isset($storageInfo) ? $storageInfo->files_backups_count : 0 }} نسخة ({{ isset($storageInfo) ? $storageInfo->files_backups_size : '0 MB' }})</p>
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

<!-- Modal: جاري إنشاء النسخة الاحتياطية -->
<div class="modal fade" id="backupProgressModal" tabindex="-1" aria-labelledby="backupProgressModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="backupProgressModalLabel">جاري إنشاء النسخة الاحتياطية</h5>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="backupProgressBar"></div>
                </div>
                <p class="text-center" id="backupProgressText">جاري تجهيز النسخة الاحتياطية...</p>
                <p class="text-muted text-center small">يرجى عدم إغلاق هذه النافذة حتى اكتمال العملية</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal: تأكيد استعادة النسخة الاحتياطية -->
<div class="modal fade" id="restoreConfirmModal" tabindex="-1" aria-labelledby="restoreConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restoreConfirmModalLabel">تأكيد استعادة النسخة الاحتياطية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>تحذير:</strong> سيؤدي استعادة النسخة الاحتياطية إلى استبدال البيانات الحالية بالبيانات الموجودة في النسخة الاحتياطية. هذا الإجراء لا يمكن التراجع عنه.
                </div>
                <p>هل أنت متأكد من رغبتك في استعادة هذه النسخة الاحتياطية؟</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <a href="#" class="btn btn-warning" id="confirmRestoreBtn">تأكيد الاستعادة</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .backups-container {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
        padding: 10px 10px 0;
    }
    
    .nav-tabs .nav-link {
        border-radius: 5px 5px 0 0;
        font-weight: 500;
        color: #495057;
    }
    
    .nav-tabs .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        color: #6a1b9a;
    }
    
    .tab-content {
        padding: 20px;
    }
    
    .tab-header {
        margin-bottom: 20px;
    }
    
    .log-entry {
        padding: 5px 0;
        border-bottom: 1px solid #f5f5f5;
        font-family: monospace;
        font-size: 13px;
    }
    
    .log-time {
        font-weight: bold;
        margin-left: 10px;
    }
    
    .storage-item {
        background-color: #f9f9f9;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
    }
    
    .storage-item h6 {
        margin-bottom: 5px;
        color: #6a1b9a;
    }
    
    @media (max-width: 767px) {
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 10px;
        }
        
        .nav-tabs .nav-item {
            margin-bottom: 0;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // إنشاء نسخة احتياطية لقاعدة البيانات
        $('#createDatabaseBackup').on('click', function() {
            $('#backupProgressModal').modal('show');
            simulateBackupProgress('database');
        });
        
        // إنشاء نسخة احتياطية للملفات
        $('#createFilesBackup').on('click', function() {
            $('#backupProgressModal').modal('show');
            simulateBackupProgress('files');
        });
        
        // محاكاة تقدم النسخ الاحتياطي
        function simulateBackupProgress(type) {
            var progress = 0;
            var progressBar = $('#backupProgressBar');
            var progressText = $('#backupProgressText');
            
            var typeText = type === 'database' ? 'قاعدة البيانات' : 'الملفات';
            
            var interval = setInterval(function() {
                progress += Math.floor(Math.random() * 10) + 1;
                
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    
                    progressBar.css('width', progress + '%').attr('aria-valuenow', progress);
                    progressText.text('تم إنشاء نسخة احتياطية من ' + typeText + ' بنجاح!');
                    
                    setTimeout(function() {
                        $('#backupProgressModal').modal('hide');
                        location.reload(); // إعادة تحميل الصفحة لعرض النسخة الاحتياطية الجديدة
                    }, 1500);
                } else {
                    progressBar.css('width', progress + '%').attr('aria-valuenow', progress);
                    
                    if (progress < 20) {
                        progressText.text('جاري تجهيز ' + typeText + ' للنسخ الاحتياطي...');
                    } else if (progress < 40) {
                        progressText.text('جاري إنشاء نسخة احتياطية من ' + typeText + '...');
                    } else if (progress < 60) {
                        progressText.text('جاري ضغط البيانات...');
                    } else if (progress < 80) {
                        progressText.text('جاري حفظ النسخة الاحتياطية...');
                    } else {
                        progressText.text('جاري إكمال العملية...');
                    }
                }
            }, 500);
        }
        
        // إظهار/إخفاء خيارات التكرار لقاعدة البيانات
        $('#dbBackupFrequency').on('change', function() {
            var frequency = $(this).val();
            
            if (frequency === 'weekly') {
                $('.weekly-options').show();
                $('.monthly-options').hide();
            } else if (frequency === 'monthly') {
                $('.weekly-options').hide();
                $('.monthly-options').show();
            } else {
                $('.weekly-options').hide();
                $('.monthly-options').hide();
            }
        });
        
        // إظهار/إخفاء خيارات التكرار للملفات
        $('#filesBackupFrequency').on('change', function() {
            var frequency = $(this).val();
            
            if (frequency === 'weekly') {
                $('.weekly-options-files').show();
                $('.monthly-options-files').hide();
            } else if (frequency === 'monthly') {
                $('.weekly-options-files').hide();
                $('.monthly-options-files').show();
            } else {
                $('.weekly-options-files').hide();
                $('.monthly-options-files').hide();
            }
        });
        
        // إظهار/إخفاء إعدادات التخزين
        $('#storageDriver').on('change', function() {
            var driver = $(this).val();
            
            $('.storage-settings').hide();
            $('.' + driver + '-settings').show();
        });
        
        // تأكيد استعادة النسخة الاحتياطية
        $('.restore-backup').on('click', function(e) {
            e.preventDefault();
            
            var type = $(this).data('type');
            var id = $(this).data('id');
            var url = $(this).attr('href');
            
            $('#confirmRestoreBtn').attr('href', url);
            $('#restoreConfirmModal').modal('show');
        });
        
        // حذف نسخة احتياطية
        $('.delete-backup').on('click', function() {
            var type = $(this).data('type');
            var id = $(this).data('id');
            
            if (confirm('هل أنت متأكد من حذف هذه النسخة الاحتياطية؟')) {
                // هنا يمكن إضافة طلب AJAX لحذف النسخة الاحتياطية
                alert('تم حذف النسخة الاحتياطية بنجاح');
                $(this).closest('tr').remove();
            }
        });
    });
</script>
@endsection
