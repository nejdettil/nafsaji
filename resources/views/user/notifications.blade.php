@extends('layouts.dashboard')

@section('title', 'الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">الإشعارات</h4>
                    <p class="card-category">جميع الإشعارات والتنبيهات الخاصة بك</p>
                </div>
                <div class="card-body">
                    <div class="notifications-container">
                        <div class="notifications-header">
                            <div class="notifications-actions">
                                <button class="btn btn-sm btn-outline-primary mark-all-read">
                                    <i class="fas fa-check-double"></i> تعيين الكل كمقروء
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter"></i> تصفية
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item active" href="#">جميع الإشعارات</a></li>
                                        <li><a class="dropdown-item" href="#">غير مقروءة</a></li>
                                        <li><a class="dropdown-item" href="#">الحجوزات</a></li>
                                        <li><a class="dropdown-item" href="#">المدفوعات</a></li>
                                        <li><a class="dropdown-item" href="#">الجلسات</a></li>
                                        <li><a class="dropdown-item" href="#">النظام</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="notifications-count">
                                <span class="badge bg-primary">{{ count($notifications ?? []) }}</span> إشعار
                            </div>
                        </div>

                        <div class="notifications-list">
                            @if(isset($notifications) && count($notifications) > 0)
                                @foreach($notifications as $notification)
                                    <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                        <div class="notification-icon">
                                            @if($notification->type == 'booking')
                                                <div class="icon-circle bg-primary">
                                                    <i class="fas fa-calendar-check text-white"></i>
                                                </div>
                                            @elseif($notification->type == 'payment')
                                                <div class="icon-circle bg-success">
                                                    <i class="fas fa-money-bill text-white"></i>
                                                </div>
                                            @elseif($notification->type == 'session')
                                                <div class="icon-circle bg-info">
                                                    <i class="fas fa-video text-white"></i>
                                                </div>
                                            @elseif($notification->type == 'review')
                                                <div class="icon-circle bg-warning">
                                                    <i class="fas fa-star text-white"></i>
                                                </div>
                                            @else
                                                <div class="icon-circle bg-secondary">
                                                    <i class="fas fa-bell text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                {{ $notification->title ?? 'إشعار جديد' }}
                                                @if(!$notification->read_at)
                                                    <span class="badge bg-danger">جديد</span>
                                                @endif
                                            </div>
                                            <div class="notification-text">{{ $notification->message ?? '' }}</div>
                                            <div class="notification-meta">
                                                <span class="notification-time">{{ $notification->created_at ? $notification->created_at->diffForHumans() : 'منذ قليل' }}</span>
                                                @if($notification->action_url)
                                                    <a href="{{ $notification->action_url }}" class="notification-action">{{ $notification->action_text ?? 'عرض' }}</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="notification-actions">
                                            <button class="btn btn-sm btn-link mark-read" title="تعيين كمقروء">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link delete-notification" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-notifications">
                                    <div class="empty-icon">
                                        <i class="fas fa-bell-slash"></i>
                                    </div>
                                    <div class="empty-text">لا توجد إشعارات حالياً</div>
                                </div>
                            @endif
                        </div>

                        @if(isset($notifications) && count($notifications) > 0 && $notifications->hasPages())
                            <div class="notifications-pagination">
                                {{ $notifications->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .notifications-container {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background-color: #f9f9f9;
    }
    
    .notifications-actions {
        display: flex;
        gap: 10px;
    }
    
    .notifications-count {
        font-weight: 500;
    }
    
    .notifications-list {
        max-height: 600px;
        overflow-y: auto;
    }
    
    .notification-item {
        display: flex;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s;
    }
    
    .notification-item:hover {
        background-color: #f9f9f9;
    }
    
    .notification-item.unread {
        background-color: #f0f7ff;
    }
    
    .notification-icon {
        margin-left: 15px;
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-title {
        font-weight: 600;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
    }
    
    .notification-title .badge {
        margin-right: 10px;
        font-size: 10px;
    }
    
    .notification-text {
        color: #555;
        margin-bottom: 5px;
    }
    
    .notification-meta {
        display: flex;
        font-size: 12px;
        color: #777;
    }
    
    .notification-time {
        margin-left: 15px;
    }
    
    .notification-action {
        color: #6a1b9a;
        text-decoration: none;
    }
    
    .notification-action:hover {
        text-decoration: underline;
    }
    
    .notification-actions {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .notification-actions .btn {
        padding: 0;
        margin: 3px 0;
        color: #777;
    }
    
    .notification-actions .btn:hover {
        color: #333;
    }
    
    .empty-notifications {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 50px 20px;
        color: #777;
    }
    
    .empty-icon {
        font-size: 48px;
        margin-bottom: 15px;
        color: #ccc;
    }
    
    .empty-text {
        font-size: 16px;
    }
    
    .notifications-pagination {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        background-color: #f9f9f9;
    }
    
    @media (max-width: 767px) {
        .notifications-header {
            flex-direction: column;
            gap: 10px;
        }
        
        .notifications-actions {
            width: 100%;
            justify-content: space-between;
        }
        
        .notification-item {
            flex-direction: column;
        }
        
        .notification-icon {
            margin-left: 0;
            margin-bottom: 10px;
        }
        
        .notification-actions {
            flex-direction: row;
            justify-content: flex-end;
            margin-top: 10px;
        }
        
        .notification-actions .btn {
            margin: 0 5px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تعيين جميع الإشعارات كمقروءة
        $('.mark-all-read').on('click', function() {
            if (confirm('هل أنت متأكد من تعيين جميع الإشعارات كمقروءة؟')) {
                // هنا يمكن إضافة طلب AJAX لتحديث حالة جميع الإشعارات
                $('.notification-item').removeClass('unread');
                alert('تم تعيين جميع الإشعارات كمقروءة');
            }
        });
        
        // تعيين إشعار واحد كمقروء
        $('.mark-read').on('click', function() {
            // هنا يمكن إضافة طلب AJAX لتحديث حالة الإشعار
            $(this).closest('.notification-item').removeClass('unread');
        });
        
        // حذف إشعار
        $('.delete-notification').on('click', function() {
            if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
                // هنا يمكن إضافة طلب AJAX لحذف الإشعار
                $(this).closest('.notification-item').fadeOut(300, function() {
                    $(this).remove();
                    
                    // التحقق مما إذا كانت هناك إشعارات متبقية
                    if ($('.notification-item').length === 0) {
                        $('.notifications-list').html(`
                            <div class="empty-notifications">
                                <div class="empty-icon">
                                    <i class="fas fa-bell-slash"></i>
                                </div>
                                <div class="empty-text">لا توجد إشعارات حالياً</div>
                            </div>
                        `);
                    }
                });
            }
        });
    });
</script>
@endsection
