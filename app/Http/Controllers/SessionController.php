<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Booking;
use App\Models\User;
use App\Models\Specialist;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    /**
     * عرض قائمة الجلسات للمستخدم الحالي
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Session::with(['user', 'specialist.user', 'booking.service']);

        // إذا كان المستخدم مختص، يعرض فقط الجلسات الخاصة به
        if ($user->hasRole('specialist')) {
            $specialist = $user->specialist;
            $query->where('specialist_id', $specialist->id);
        } 
        // إذا كان المستخدم عادي، يعرض فقط جلساته
        elseif (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // تصفية حسب التاريخ
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('session_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('session_date', '<=', $request->date_to);
        }

        // تصفية حسب نوع الجلسة
        if ($request->has('session_type') && !empty($request->session_type)) {
            $query->where('session_type', $request->session_type);
        }

        // البحث عن جلسات مستخدم معين (للإدارة فقط)
        if ($user->hasRole('admin') && $request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        // البحث عن جلسات مختص معين (للإدارة فقط)
        if ($user->hasRole('admin') && $request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->where('specialist_id', $request->specialist_id);
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'session_date');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $sessions = $query->paginate($request->input('per_page', 10));

        return view('sessions.index', compact('sessions'));
    }

    /**
     * عرض تفاصيل جلسة محددة
     */
    public function show($id)
    {
        $user = Auth::user();
        $session = Session::with(['user', 'specialist.user', 'booking.service'])->findOrFail($id);

        // التحقق من صلاحية الوصول
        if (!$user->hasRole('admin') && $user->id !== $session->user_id && 
            (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الجلسة');
        }

        return view('sessions.show', compact('session'));
    }

    /**
     * عرض صفحة بدء جلسة جديدة
     */
    public function create($bookingId)
    {
        $user = Auth::user();
        $booking = Booking::with(['user', 'specialist.user', 'service'])->findOrFail($bookingId);

        // التحقق من صلاحية بدء الجلسة (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $booking->specialist_id)) {
            return redirect()->route('specialist.bookings.index')->with('error', 'غير مصرح لك ببدء هذه الجلسة');
        }

        // التحقق من أن الحجز مؤكد
        if ($booking->status !== 'confirmed') {
            return redirect()->route('specialist.bookings.show', $booking->id)->with('error', 'لا يمكن بدء جلسة لحجز غير مؤكد');
        }

        // التحقق من عدم وجود جلسة سابقة لهذا الحجز
        $existingSession = Session::where('booking_id', $booking->id)->first();
        if ($existingSession) {
            return redirect()->route('sessions.show', $existingSession->id)->with('info', 'توجد جلسة مرتبطة بهذا الحجز بالفعل');
        }

        return view('sessions.create', compact('booking'));
    }

    /**
     * حفظ جلسة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'session_type' => 'required|in:online,in_person',
            'meeting_link' => 'nullable|url|required_if:session_type,online',
            'meeting_password' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $booking = Booking::findOrFail($request->booking_id);

        // التحقق من صلاحية بدء الجلسة (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $booking->specialist_id)) {
            return redirect()->route('specialist.bookings.index')->with('error', 'غير مصرح لك ببدء هذه الجلسة');
        }

        try {
            // إنشاء الجلسة
            $session = Session::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'specialist_id' => $booking->specialist_id,
                'session_date' => $booking->booking_date,
                'duration' => $booking->duration,
                'status' => 'scheduled',
                'session_type' => $request->session_type,
                'meeting_link' => $request->meeting_link,
                'meeting_password' => $request->meeting_password,
                'notes' => $request->notes,
            ]);

            // إرسال إشعارات
            $this->sendSessionCreatedNotifications($session);

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم إنشاء الجلسة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء الجلسة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الجلسة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض صفحة تعديل جلسة
     */
    public function edit($id)
    {
        $user = Auth::user();
        $session = Session::with(['booking', 'user', 'specialist'])->findOrFail($id);

        // التحقق من صلاحية التعديل (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بتعديل هذه الجلسة');
        }

        return view('sessions.edit', compact('session'));
    }

    /**
     * تحديث جلسة محددة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'session_type' => 'required|in:online,in_person',
            'meeting_link' => 'nullable|url|required_if:session_type,online',
            'meeting_password' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,no_show',
        ]);

        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية التعديل (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بتعديل هذه الجلسة');
        }

        try {
            // حفظ الحالة القديمة للإشعارات
            $oldStatus = $session->status;

            // تحديث الجلسة
            $session->update([
                'session_type' => $request->session_type,
                'meeting_link' => $request->meeting_link,
                'meeting_password' => $request->meeting_password,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            // إذا تم تغيير الحالة إلى مكتملة، قم بتحديث حالة الحجز أيضاً
            if ($request->status === 'completed' && $oldStatus !== 'completed') {
                $booking = $session->booking;
                $booking->status = 'completed';
                $booking->save();

                // إضافة ملاحظات المختص وملخص الجلسة
                if ($request->has('specialist_notes')) {
                    $session->update([
                        'specialist_notes' => $request->specialist_notes,
                        'summary' => $request->summary,
                        'recommendations' => $request->recommendations,
                        'end_time' => now(),
                    ]);
                }

                // إرسال إشعارات إكمال الجلسة
                $this->sendSessionCompletedNotifications($session);
            }
            // إذا تم تغيير الحالة إلى قيد التقدم
            elseif ($request->status === 'in_progress' && $oldStatus !== 'in_progress') {
                $booking = $session->booking;
                $booking->status = 'in_progress';
                $booking->save();

                // تحديث وقت بدء الجلسة
                $session->update([
                    'start_time' => now(),
                ]);

                // إرسال إشعارات بدء الجلسة
                $this->sendSessionStartedNotifications($session);
            }
            // إذا تم تغيير الحالة إلى ملغية
            elseif ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
                $booking = $session->booking;
                $booking->status = 'cancelled';
                $booking->save();

                // إرسال إشعارات إلغاء الجلسة
                $this->sendSessionCancelledNotifications($session);
            }
            // إذا تم تغيير الحالة إلى عدم حضور
            elseif ($request->status === 'no_show' && $oldStatus !== 'no_show') {
                $booking = $session->booking;
                $booking->status = 'no_show';
                $booking->save();

                // إرسال إشعارات عدم الحضور
                $this->sendSessionNoShowNotifications($session);
            }

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم تحديث الجلسة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث الجلسة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الجلسة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * بدء جلسة
     */
    public function start($id)
    {
        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية بدء الجلسة (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك ببدء هذه الجلسة');
        }

        // التحقق من أن الجلسة مجدولة
        if ($session->status !== 'scheduled') {
            return redirect()->route('sessions.show', $session->id)->with('error', 'لا يمكن بدء جلسة غير مجدولة');
        }

        try {
            // تحديث حالة الجلسة
            $session->update([
                'status' => 'in_progress',
                'start_time' => now(),
            ]);

            // تحديث حالة الحجز
            $booking = $session->booking;
            $booking->status = 'in_progress';
            $booking->save();

            // إرسال إشعارات بدء الجلسة
            $this->sendSessionStartedNotifications($session);

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم بدء الجلسة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في بدء الجلسة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء بدء الجلسة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * إنهاء جلسة
     */
    public function end(Request $request, $id)
    {
        $request->validate([
            'specialist_notes' => 'nullable|string',
            'summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية إنهاء الجلسة (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بإنهاء هذه الجلسة');
        }

        // التحقق من أن الجلسة قيد التقدم
        if ($session->status !== 'in_progress') {
            return redirect()->route('sessions.show', $session->id)->with('error', 'لا يمكن إنهاء جلسة غير قيد التقدم');
        }

        try {
            $endTime = now();
            $startTime = new \DateTime($session->start_time);
            $duration = $startTime->diff($endTime);
            $durationMinutes = ($duration->h * 60) + $duration->i;

            // تحديث الجلسة
            $session->update([
                'end_time' => $endTime,
                'duration_minutes' => $durationMinutes,
                'status' => 'completed',
                'specialist_notes' => $request->specialist_notes,
                'summary' => $request->summary,
                'recommendations' => $request->recommendations,
            ]);

            // تحديث حالة الحجز
            $booking = $session->booking;
            $booking->status = 'completed';
            $booking->save();

            // إرسال إشعارات إكمال الجلسة
            $this->sendSessionCompletedNotifications($session);

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم إنهاء الجلسة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إنهاء الجلسة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنهاء الجلسة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * إلغاء جلسة
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية إلغاء الجلسة (المختص أو المستخدم أو الأدمن)
        $canCancel = $user->hasRole('admin') || 
                    ($user->hasRole('specialist') && $user->specialist->id === $session->specialist_id) || 
                    $user->id === $session->user_id;

        if (!$canCancel) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بإلغاء هذه الجلسة');
        }

        // التحقق من أن الجلسة ليست مكتملة أو ملغاة بالفعل
        if (in_array($session->status, ['completed', 'cancelled'])) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'لا يمكن إلغاء جلسة مكتملة أو ملغاة بالفعل');
        }

        try {
            // تحديث الجلسة
            $session->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
            ]);

            // تحديث حالة الحجز
            $booking = $session->booking;
            $booking->status = 'cancelled';
            $booking->save();

            // إرسال إشعارات إلغاء الجلسة
            $this->sendSessionCancelledNotifications($session);

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم إلغاء الجلسة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إلغاء الجلسة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء الجلسة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * تسجيل عدم حضور المستخدم
     */
    public function markNoShow($id)
    {
        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية تسجيل عدم الحضور (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بتسجيل عدم حضور لهذه الجلسة');
        }

        // التحقق من أن الجلسة مجدولة
        if ($session->status !== 'scheduled') {
            return redirect()->route('sessions.show', $session->id)->with('error', 'لا يمكن تسجيل عدم حضور لجلسة غير مجدولة');
        }

        try {
            // تحديث الجلسة
            $session->update([
                'status' => 'no_show',
                'no_show_recorded_at' => now(),
            ]);

            // تحديث حالة الحجز
            $booking = $session->booking;
            $booking->status = 'no_show';
            $booking->save();

            // إرسال إشعارات عدم الحضور
            $this->sendSessionNoShowNotifications($session);

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم تسجيل عدم حضور المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تسجيل عدم الحضور: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تسجيل عدم الحضور، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * إضافة تقييم ومراجعة للجلسة
     */
    public function addReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية إضافة التقييم (فقط المستخدم صاحب الجلسة)
        if ($user->id !== $session->user_id) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بإضافة تقييم لهذه الجلسة');
        }

        // التحقق من أن الجلسة مكتملة
        if ($session->status !== 'completed') {
            return redirect()->route('sessions.show', $session->id)->with('error', 'لا يمكن إضافة تقييم لجلسة غير مكتملة');
        }

        try {
            // تحديث الجلسة
            $session->update([
                'rating' => $request->rating,
                'review' => $request->review,
                'reviewed_at' => now(),
            ]);

            // تحديث متوسط تقييم المختص
            $specialist = $session->specialist;
            $averageRating = Session::where('specialist_id', $specialist->id)
                                  ->where('status', 'completed')
                                  ->whereNotNull('rating')
                                  ->avg('rating');
            
            $specialist->update([
                'average_rating' => $averageRating,
                'reviews_count' => Session::where('specialist_id', $specialist->id)
                                        ->where('status', 'completed')
                                        ->whereNotNull('rating')
                                        ->count(),
            ]);

            // إرسال إشعار للمختص
            Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'تقييم جديد',
                'content' => 'قام ' . $user->name . ' بتقييم الجلسة بتاريخ ' . $session->session_date->format('d/m/Y') . ' بتقييم ' . $request->rating . ' نجوم',
                'type' => 'review',
                'is_read' => false,
                'link' => route('specialist.sessions.show', $session->id),
            ]);

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم إضافة التقييم بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إضافة التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة التقييم، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض صفحة إضافة ملاحظات للجلسة
     */
    public function showNotesForm($id)
    {
        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية إضافة الملاحظات (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بإضافة ملاحظات لهذه الجلسة');
        }

        return view('sessions.notes', compact('session'));
    }

    /**
     * حفظ ملاحظات الجلسة
     */
    public function saveNotes(Request $request, $id)
    {
        $request->validate([
            'specialist_notes' => 'nullable|string',
            'summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية إضافة الملاحظات (فقط المختص أو الأدمن)
        if (!$user->hasRole('admin') && (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بإضافة ملاحظات لهذه الجلسة');
        }

        try {
            // تحديث الجلسة
            $session->update([
                'specialist_notes' => $request->specialist_notes,
                'summary' => $request->summary,
                'recommendations' => $request->recommendations,
            ]);

            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $session->user_id,
                'title' => 'تم إضافة ملاحظات للجلسة',
                'content' => 'قام المختص ' . $session->specialist->user->name . ' بإضافة ملاحظات وتوصيات للجلسة بتاريخ ' . $session->session_date->format('d/m/Y'),
                'type' => 'session_notes',
                'is_read' => false,
                'link' => route('user.sessions.show', $session->id),
            ]);

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم حفظ ملاحظات الجلسة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حفظ ملاحظات الجلسة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ ملاحظات الجلسة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض صفحة تحميل ملفات الجلسة
     */
    public function showFilesForm($id)
    {
        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية تحميل الملفات (المختص أو المستخدم أو الأدمن)
        $canUpload = $user->hasRole('admin') || 
                    ($user->hasRole('specialist') && $user->specialist->id === $session->specialist_id) || 
                    $user->id === $session->user_id;

        if (!$canUpload) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بتحميل ملفات لهذه الجلسة');
        }

        return view('sessions.files', compact('session'));
    }

    /**
     * تحميل ملفات للجلسة
     */
    public function uploadFiles(Request $request, $id)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // الحد الأقصى 10 ميجابايت
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية تحميل الملفات (المختص أو المستخدم أو الأدمن)
        $canUpload = $user->hasRole('admin') || 
                    ($user->hasRole('specialist') && $user->specialist->id === $session->specialist_id) || 
                    $user->id === $session->user_id;

        if (!$canUpload) {
            return redirect()->route('sessions.show', $session->id)->with('error', 'غير مصرح لك بتحميل ملفات لهذه الجلسة');
        }

        try {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('session_files', $fileName, 'public');

                    // إنشاء ملف جديد
                    \App\Models\SessionFile::create([
                        'session_id' => $session->id,
                        'user_id' => $user->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'description' => $request->description,
                    ]);
                }

                // إرسال إشعار للطرف الآخر
                $recipientId = ($user->id === $session->user_id) ? $session->specialist->user_id : $session->user_id;
                $senderName = $user->name;
                
                Notification::create([
                    'user_id' => $recipientId,
                    'title' => 'ملفات جديدة للجلسة',
                    'content' => 'قام ' . $senderName . ' بتحميل ملفات جديدة للجلسة بتاريخ ' . $session->session_date->format('d/m/Y'),
                    'type' => 'session_files',
                    'is_read' => false,
                    'link' => route(($user->id === $session->user_id) ? 'specialist.sessions.show' : 'user.sessions.show', $session->id),
                ]);
            }

            return redirect()->route('sessions.show', $session->id)->with('success', 'تم تحميل الملفات بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تحميل ملفات الجلسة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل الملفات، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض صفحة الدردشة للجلسة
     */
    public function showChat($id)
    {
        $user = Auth::user();
        $session = Session::with(['user', 'specialist.user', 'messages'])->findOrFail($id);

        // التحقق من صلاحية الوصول
        if (!$user->hasRole('admin') && $user->id !== $session->user_id && 
            (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الدردشة');
        }

        return view('sessions.chat', compact('session'));
    }

    /**
     * إرسال رسالة في دردشة الجلسة
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية الوصول
        if (!$user->hasRole('admin') && $user->id !== $session->user_id && 
            (!$user->hasRole('specialist') || $user->specialist->id !== $session->specialist_id)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الدردشة');
        }

        try {
            // إنشاء رسالة جديدة
            $message = \App\Models\SessionMessage::create([
                'session_id' => $session->id,
                'user_id' => $user->id,
                'message' => $request->message,
            ]);

            // إرسال إشعار للطرف الآخر
            $recipientId = ($user->id === $session->user_id) ? $session->specialist->user_id : $session->user_id;
            $senderName = $user->name;
            
            Notification::create([
                'user_id' => $recipientId,
                'title' => 'رسالة جديدة في الدردشة',
                'content' => 'لديك رسالة جديدة من ' . $senderName . ' في دردشة الجلسة بتاريخ ' . $session->session_date->format('d/m/Y'),
                'type' => 'session_message',
                'is_read' => false,
                'link' => route(($user->id === $session->user_id) ? 'specialist.sessions.chat' : 'user.sessions.chat', $session->id),
            ]);

            return redirect()->back()->with('success', 'تم إرسال الرسالة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال رسالة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إرسال الرسالة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * إرسال إشعارات إنشاء الجلسة
     */
    private function sendSessionCreatedNotifications(Session $session)
    {
        try {
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $session->user_id,
                'title' => 'تم إنشاء جلسة جديدة',
                'content' => 'تم إنشاء جلسة جديدة مع المختص ' . $session->specialist->user->name . ' بتاريخ ' . $session->session_date->format('d/m/Y') . ' الساعة ' . $session->session_date->format('H:i'),
                'type' => 'session_created',
                'is_read' => false,
                'link' => route('user.sessions.show', $session->id),
            ]);

            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($session->user->email)->send(new \App\Mail\SessionCreated($session));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد إنشاء الجلسة للمستخدم: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إنشاء الجلسة: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات بدء الجلسة
     */
    private function sendSessionStartedNotifications(Session $session)
    {
        try {
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $session->user_id,
                'title' => 'تم بدء الجلسة',
                'content' => 'تم بدء الجلسة مع المختص ' . $session->specialist->user->name . '. يرجى الانضمام الآن.',
                'type' => 'session_started',
                'is_read' => false,
                'link' => route('user.sessions.show', $session->id),
            ]);

            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($session->user->email)->send(new \App\Mail\SessionStarted($session));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد بدء الجلسة للمستخدم: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات بدء الجلسة: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات إكمال الجلسة
     */
    private function sendSessionCompletedNotifications(Session $session)
    {
        try {
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $session->user_id,
                'title' => 'تم إكمال الجلسة',
                'content' => 'تم إكمال الجلسة مع المختص ' . $session->specialist->user->name . '. يرجى تقييم الجلسة.',
                'type' => 'session_completed',
                'is_read' => false,
                'link' => route('user.sessions.show', $session->id),
            ]);

            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($session->user->email)->send(new \App\Mail\SessionCompleted($session));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد إكمال الجلسة للمستخدم: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إكمال الجلسة: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات إلغاء الجلسة
     */
    private function sendSessionCancelledNotifications(Session $session)
    {
        try {
            // تحديد من قام بالإلغاء
            $cancelledBy = User::find($session->cancelled_by);
            $cancellerName = $cancelledBy ? $cancelledBy->name : 'النظام';
            $isSpecialist = $cancelledBy && $cancelledBy->hasRole('specialist');

            // إرسال إشعار للمستخدم (إذا لم يكن هو من قام بالإلغاء)
            if (!$cancelledBy || $cancelledBy->id !== $session->user_id) {
                Notification::create([
                    'user_id' => $session->user_id,
                    'title' => 'تم إلغاء الجلسة',
                    'content' => 'تم إلغاء الجلسة مع المختص ' . $session->specialist->user->name . ' بتاريخ ' . $session->session_date->format('d/m/Y') . ' بواسطة ' . $cancellerName,
                    'type' => 'session_cancelled',
                    'is_read' => false,
                    'link' => route('user.sessions.show', $session->id),
                ]);

                // إرسال بريد إلكتروني للمستخدم
                try {
                    // Mail::to($session->user->email)->send(new \App\Mail\SessionCancelled($session));
                } catch (\Exception $e) {
                    Log::error('خطأ في إرسال بريد إلغاء الجلسة للمستخدم: ' . $e->getMessage());
                }
            }

            // إرسال إشعار للمختص (إذا لم يكن هو من قام بالإلغاء)
            if (!$isSpecialist) {
                Notification::create([
                    'user_id' => $session->specialist->user_id,
                    'title' => 'تم إلغاء الجلسة',
                    'content' => 'تم إلغاء الجلسة مع ' . $session->user->name . ' بتاريخ ' . $session->session_date->format('d/m/Y') . ' بواسطة ' . $cancellerName,
                    'type' => 'session_cancelled',
                    'is_read' => false,
                    'link' => route('specialist.sessions.show', $session->id),
                ]);

                // إرسال بريد إلكتروني للمختص
                try {
                    // Mail::to($session->specialist->user->email)->send(new \App\Mail\SessionCancelledSpecialist($session));
                } catch (\Exception $e) {
                    Log::error('خطأ في إرسال بريد إلغاء الجلسة للمختص: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إلغاء الجلسة: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات عدم الحضور
     */
    private function sendSessionNoShowNotifications(Session $session)
    {
        try {
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $session->user_id,
                'title' => 'تم تسجيل عدم حضور',
                'content' => 'تم تسجيل عدم حضورك للجلسة مع المختص ' . $session->specialist->user->name . ' بتاريخ ' . $session->session_date->format('d/m/Y'),
                'type' => 'session_no_show',
                'is_read' => false,
                'link' => route('user.sessions.show', $session->id),
            ]);

            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($session->user->email)->send(new \App\Mail\SessionNoShow($session));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد عدم الحضور للمستخدم: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات عدم الحضور: ' . $e->getMessage());
        }
    }
}
