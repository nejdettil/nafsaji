@extends('layouts.dashboard')

@section('title', 'الجدول الزمني')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">الجدول الزمني</h4>
                    <p class="card-category">إدارة مواعيد جلساتك وتنظيم وقتك</p>
                </div>
                <div class="card-body">
                    <div class="schedule-container">
                        <div class="schedule-header">
                            <div class="schedule-actions">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSessionModal">
                                    <i class="fas fa-plus"></i> إضافة موعد جديد
                                </button>
                                <div class="btn-group view-options">
                                    <button type="button" class="btn btn-outline-secondary active" data-view="month">شهري</button>
                                    <button type="button" class="btn btn-outline-secondary" data-view="week">أسبوعي</button>
                                    <button type="button" class="btn btn-outline-secondary" data-view="day">يومي</button>
                                </div>
                            </div>
                            <div class="schedule-navigation">
                                <button class="btn btn-sm btn-outline-secondary" id="prevBtn">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <h5 class="current-date">أبريل 2025</h5>
                                <button class="btn btn-sm btn-outline-secondary" id="nextBtn">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="todayBtn">اليوم</button>
                            </div>
                        </div>

                        <div class="calendar-container">
                            <div id="calendar"></div>
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
                    <h4 class="card-title">الجلسات القادمة</h4>
                    <p class="card-category">جلساتك المجدولة للأيام القادمة</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-info">
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>المستخدم</th>
                                    <th>الخدمة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($upcomingSessions) && count($upcomingSessions) > 0)
                                    @foreach($upcomingSessions as $session)
                                        <tr>
                                            <td>{{ date('Y-m-d', strtotime($session->date)) }}</td>
                                            <td>{{ date('H:i', strtotime($session->time)) }}</td>
                                            <td>{{ $session->booking->user->name ?? 'N/A' }}</td>
                                            <td>{{ $session->booking->service->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $session->status == 'scheduled' ? 'warning' : ($session->status == 'in_progress' ? 'success' : 'secondary') }}">
                                                    {{ $session->status == 'scheduled' ? 'مجدولة' : ($session->status == 'in_progress' ? 'جارية' : $session->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('specialist.sessions.show', $session->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($session->status == 'scheduled')
                                                    <a href="#" class="btn btn-sm btn-success start-session" data-id="{{ $session->id }}">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد جلسات قادمة</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-success">
                    <h4 class="card-title">أوقات الإتاحة</h4>
                    <p class="card-category">إدارة أوقات توفرك للجلسات</p>
                </div>
                <div class="card-body">
                    <div class="availability-container">
                        <div class="availability-header">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                                <i class="fas fa-plus"></i> إضافة وقت إتاحة
                            </button>
                        </div>
                        
                        <div class="availability-list">
                            @if(isset($availabilitySlots) && count($availabilitySlots) > 0)
                                @foreach($availabilitySlots as $day => $slots)
                                    <div class="availability-day">
                                        <div class="day-header">
                                            <h5>{{ $day }}</h5>
                                        </div>
                                        <div class="day-slots">
                                            @foreach($slots as $slot)
                                                <div class="time-slot">
                                                    <span class="slot-time">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                                                    <div class="slot-actions">
                                                        <button class="btn btn-sm btn-link edit-slot" data-id="{{ $slot->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-link delete-slot" data-id="{{ $slot->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-availability">
                                    <div class="empty-icon">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="empty-text">لم تقم بإضافة أوقات إتاحة بعد</div>
                                    <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                                        <i class="fas fa-plus"></i> إضافة وقت إتاحة
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: إضافة موعد جديد -->
<div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSessionModalLabel">إضافة موعد جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('specialist.schedule.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="sessionDate">التاريخ</label>
                        <input type="date" class="form-control" id="sessionDate" name="date" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="sessionTime">الوقت</label>
                        <input type="time" class="form-control" id="sessionTime" name="time" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="sessionDuration">المدة (بالدقائق)</label>
                        <select class="form-control" id="sessionDuration" name="duration" required>
                            <option value="30">30 دقيقة</option>
                            <option value="45">45 دقيقة</option>
                            <option value="60" selected>60 دقيقة</option>
                            <option value="90">90 دقيقة</option>
                            <option value="120">120 دقيقة</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="sessionService">الخدمة</label>
                        <select class="form-control" id="sessionService" name="service_id" required>
                            <option value="">اختر الخدمة</option>
                            @if(isset($services) && count($services) > 0)
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="sessionNotes">ملاحظات</label>
                        <textarea class="form-control" id="sessionNotes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: إضافة وقت إتاحة -->
<div class="modal fade" id="addAvailabilityModal" tabindex="-1" aria-labelledby="addAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAvailabilityModalLabel">إضافة وقت إتاحة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('specialist.availability.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="availabilityDay">اليوم</label>
                        <select class="form-control" id="availabilityDay" name="day" required>
                            <option value="sunday">الأحد</option>
                            <option value="monday">الإثنين</option>
                            <option value="tuesday">الثلاثاء</option>
                            <option value="wednesday">الأربعاء</option>
                            <option value="thursday">الخميس</option>
                            <option value="friday">الجمعة</option>
                            <option value="saturday">السبت</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="availabilityStartTime">وقت البداية</label>
                        <input type="time" class="form-control" id="availabilityStartTime" name="start_time" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="availabilityEndTime">وقت النهاية</label>
                        <input type="time" class="form-control" id="availabilityEndTime" name="end_time" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="isRecurring" name="is_recurring" value="1" checked>
                        <label class="form-check-label" for="isRecurring">
                            تكرار أسبوعي
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-success">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<style>
    .schedule-container {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .schedule-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background-color: #f9f9f9;
    }
    
    .schedule-actions {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .schedule-navigation {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .current-date {
        margin: 0;
        font-weight: 600;
        min-width: 120px;
        text-align: center;
    }
    
    .calendar-container {
        padding: 20px;
    }
    
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
    }
    
    .fc-event-title {
        font-weight: 600;
    }
    
    .fc-event-time {
        font-weight: normal;
    }
    
    .availability-container {
        padding: 15px;
    }
    
    .availability-header {
        margin-bottom: 20px;
    }
    
    .availability-day {
        margin-bottom: 20px;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .day-header {
        background-color: #f9f9f9;
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }
    
    .day-header h5 {
        margin: 0;
        font-weight: 600;
    }
    
    .day-slots {
        padding: 10px 15px;
    }
    
    .time-slot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .time-slot:last-child {
        border-bottom: none;
    }
    
    .slot-time {
        font-weight: 500;
    }
    
    .slot-actions {
        display: flex;
        gap: 5px;
    }
    
    .empty-availability {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
        color: #777;
    }
    
    .empty-icon {
        font-size: 48px;
        margin-bottom: 15px;
        color: #ccc;
    }
    
    .empty-text {
        font-size: 16px;
        margin-bottom: 10px;
    }
    
    @media (max-width: 767px) {
        .schedule-header {
            flex-direction: column;
            gap: 15px;
        }
        
        .schedule-actions {
            width: 100%;
            justify-content: space-between;
        }
        
        .view-options {
            display: none;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ar.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            direction: 'rtl',
            initialView: 'dayGridMonth',
            headerToolbar: false,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekNumbers: false,
            navLinks: true,
            editable: true,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            events: [
                // هنا يمكن إضافة الأحداث من الخادم
                @if(isset($sessions) && count($sessions) > 0)
                    @foreach($sessions as $session)
                        {
                            id: '{{ $session->id }}',
                            title: '{{ $session->booking->user->name ?? "جلسة" }}',
                            start: '{{ $session->date }}T{{ $session->time }}',
                            end: '{{ $session->end_date }}T{{ $session->end_time }}',
                            backgroundColor: '{{ $session->status == "scheduled" ? "#ffc107" : ($session->status == "completed" ? "#28a745" : "#17a2b8") }}',
                            borderColor: '{{ $session->status == "scheduled" ? "#ffc107" : ($session->status == "completed" ? "#28a745" : "#17a2b8") }}',
                            url: '{{ route("specialist.sessions.show", $session->id) }}'
                        },
                    @endforeach
                @endif
            ],
            select: function(info) {
                // فتح نافذة إضافة موعد جديد مع تعبئة التاريخ المحدد
                $('#sessionDate').val(info.startStr);
                $('#addSessionModal').modal('show');
            },
            eventClick: function(info) {
                // التنقل إلى صفحة تفاصيل الجلسة عند النقر على حدث
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault(); // منع السلوك الافتراضي
                }
            }
        });
        
        calendar.render();
        
        // التنقل بين الشهور
        document.getElementById('prevBtn').addEventListener('click', function() {
            calendar.prev();
            updateCurrentDate();
        });
        
        document.getElementById('nextBtn').addEventListener('click', function() {
            calendar.next();
            updateCurrentDate();
        });
        
        document.getElementById('todayBtn').addEventListener('click', function() {
            calendar.today();
            updateCurrentDate();
        });
        
        // تغيير طريقة العرض
        document.querySelectorAll('.view-options button').forEach(function(button) {
            button.addEventListener('click', function() {
                var view = this.getAttribute('data-view');
                
                // إزالة الفئة النشطة من جميع الأزرار
                document.querySelectorAll('.view-options button').forEach(function(btn) {
                    btn.classList.remove('active');
                });
                
                // إضافة الفئة النشطة إلى الزر المحدد
                this.classList.add('active');
                
                // تغيير طريقة العرض
                if (view === 'month') {
                    calendar.changeView('dayGridMonth');
                } else if (view === 'week') {
                    calendar.changeView('timeGridWeek');
                } else if (view === 'day') {
                    calendar.changeView('timeGridDay');
                }
                
                updateCurrentDate();
            });
        });
        
        // تحديث عنوان التاريخ الحالي
        function updateCurrentDate() {
            var dateTitle = '';
            var view = calendar.view;
            var date = calendar.getDate();
            
            if (view.type === 'dayGridMonth') {
                dateTitle = new Intl.DateTimeFormat('ar', { year: 'numeric', month: 'long' }).format(date);
            } else if (view.type === 'timeGridWeek') {
                var start = new Date(view.activeStart);
                var end = new Date(view.activeEnd);
                end.setDate(end.getDate() - 1);
                dateTitle = new Intl.DateTimeFormat('ar', { day: 'numeric', month: 'short' }).format(start) + ' - ' + 
                            new Intl.DateTimeFormat('ar', { day: 'numeric', month: 'short' }).format(end);
            } else if (view.type === 'timeGridDay') {
                dateTitle = new Intl.DateTimeFormat('ar', { year: 'numeric', month: 'long', day: 'numeric' }).format(date);
            }
            
            document.querySelector('.current-date').textContent = dateTitle;
        }
        
        // تحديث عنوان التاريخ عند تحميل الصفحة
        updateCurrentDate();
        
        // بدء الجلسة
        document.querySelectorAll('.start-session').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                var sessionId = this.getAttribute('data-id');
                
                if (confirm('هل أنت متأكد من بدء هذه الجلسة؟')) {
                    // هنا يمكن إضافة طلب AJAX لبدء الجلسة
                    alert('تم بدء الجلسة بنجاح');
                    window.location.href = '/specialist/sessions/' + sessionId + '/start';
                }
            });
        });
        
        // حذف وقت إتاحة
        document.querySelectorAll('.delete-slot').forEach(function(button) {
            button.addEventListener('click', function() {
                var slotId = this.getAttribute('data-id');
                
                if (confirm('هل أنت متأكد من حذف وقت الإتاحة هذا؟')) {
                    // هنا يمكن إضافة طلب AJAX لحذف وقت الإتاحة
                    this.closest('.time-slot').remove();
                }
            });
        });
        
        // تعديل وقت إتاحة
        document.querySelectorAll('.edit-slot').forEach(function(button) {
            button.addEventListener('click', function() {
                var slotId = this.getAttribute('data-id');
                
                // هنا يمكن إضافة كود لفتح نافذة تعديل وقت الإتاحة
                alert('تعديل وقت الإتاحة رقم ' + slotId);
            });
        });
    });
</script>
@endsection
