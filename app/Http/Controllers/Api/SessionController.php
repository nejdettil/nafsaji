<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    /**
     * عرض قائمة الجلسات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Session::with(['user', 'specialist.user', 'booking.service']);

        // إذا كان المستخدم مختص، يعرض فقط الجلسات الخاصة به
        if ($user->isSpecialist()) {
            $specialist = $user->specialist;
            $query->where('specialist_id', $specialist->id);
        } 
        // إذا كان المستخدم عادي، يعرض فقط جلساته
        elseif (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // تصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب التاريخ
        if ($request->has('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'start_time');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $sessions = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => true,
            'data' => $sessions
        ]);
    }

    /**
     * عرض جلسة محددة
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = request()->user();
        $session = Session::with(['user', 'specialist.user', 'booking.service'])->findOrFail($id);

        // التحقق من صلاحية الوصول
        if (!$user->isAdmin() && $user->id !== $session->user_id && ($user->isSpecialist() && $user->specialist->id !== $session->specialist_id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بالوصول إلى هذه الجلسة'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $session
        ]);
    }

    /**
     * تحديث جلسة محددة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية التعديل (فقط المختص أو الأدمن)
        if (!$user->isAdmin() && (!$user->isSpecialist() || $user->specialist->id !== $session->specialist_id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بتعديل هذه الجلسة'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'in:scheduled,in_progress,completed,cancelled,no_show',
            'specialist_notes' => 'nullable|string',
            'summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'recording_url' => 'nullable|string',
            'end_time' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $session->update($request->all());

        // إذا تم تغيير الحالة إلى مكتملة، قم بتحديث حالة الحجز أيضاً
        if ($request->has('status') && $request->status === 'completed') {
            $booking = $session->booking;
            $booking->status = 'completed';
            $booking->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الجلسة بنجاح',
            'data' => $session->load(['user', 'specialist.user', 'booking.service'])
        ]);
    }

    /**
     * إضافة تقييم ومراجعة للجلسة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addReview(Request $request, $id)
    {
        $user = $request->user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية إضافة التقييم (فقط المستخدم صاحب الجلسة)
        if ($user->id !== $session->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بإضافة تقييم لهذه الجلسة'
            ], 403);
        }

        // التحقق من أن الجلسة مكتملة
        if ($session->status !== 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إضافة تقييم لجلسة غير مكتملة'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $session->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة التقييم بنجاح',
            'data' => $session
        ]);
    }

    /**
     * بدء جلسة من حجز
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $bookingId
     * @return \Illuminate\Http\Response
     */
    public function startFromBooking(Request $request, $bookingId)
    {
        $user = $request->user();
        $booking = Booking::findOrFail($bookingId);

        // التحقق من صلاحية بدء الجلسة (فقط المختص أو الأدمن)
        if (!$user->isAdmin() && (!$user->isSpecialist() || $user->specialist->id !== $booking->specialist_id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك ببدء هذه الجلسة'
            ], 403);
        }

        // التحقق من أن الحجز مؤكد
        if ($booking->status !== 'confirmed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن بدء جلسة لحجز غير مؤكد'
            ], 422);
        }

        // التحقق من عدم وجود جلسة سابقة لهذا الحجز
        $existingSession = Session::where('booking_id', $booking->id)->first();
        if ($existingSession) {
            return response()->json([
                'status' => false,
                'message' => 'توجد جلسة مرتبطة بهذا الحجز بالفعل'
            ], 422);
        }

        $session = Session::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'specialist_id' => $booking->specialist_id,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // تحديث حالة الحجز
        $booking->status = 'in_progress';
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'تم بدء الجلسة بنجاح',
            'data' => $session->load(['user', 'specialist.user', 'booking.service'])
        ]);
    }

    /**
     * إنهاء جلسة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function endSession(Request $request, $id)
    {
        $user = $request->user();
        $session = Session::findOrFail($id);

        // التحقق من صلاحية إنهاء الجلسة (فقط المختص أو الأدمن)
        if (!$user->isAdmin() && (!$user->isSpecialist() || $user->specialist->id !== $session->specialist_id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بإنهاء هذه الجلسة'
            ], 403);
        }

        // التحقق من أن الجلسة قيد التقدم
        if ($session->status !== 'in_progress') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إنهاء جلسة غير قيد التقدم'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'specialist_notes' => 'nullable|string',
            'summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'recording_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $endTime = now();
        $startTime = new \DateTime($session->start_time);
        $duration = $startTime->diff($endTime);
        $durationMinutes = ($duration->h * 60) + $duration->i;

        $session->update([
            'end_time' => $endTime,
            'duration_minutes' => $durationMinutes,
            'status' => 'completed',
            'specialist_notes' => $request->specialist_notes,
            'summary' => $request->summary,
            'recommendations' => $request->recommendations,
            'recording_url' => $request->recording_url,
        ]);

        // تحديث حالة الحجز
        $booking = $session->booking;
        $booking->status = 'completed';
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'تم إنهاء الجلسة بنجاح',
            'data' => $session->load(['user', 'specialist.user', 'booking.service'])
        ]);
    }
}
