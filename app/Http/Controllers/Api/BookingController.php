<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\Session;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * عرض قائمة الحجوزات للمستخدم الحالي
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Booking::where('user_id', $user->id);
        
        // التصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // التصفية حسب التاريخ
        if ($request->has('date')) {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $query->whereDate('booking_date', $date);
        }
        
        // الترتيب
        $query->orderBy('booking_date', 'desc');
        
        $bookings = $query->with(['specialist.user', 'service'])->paginate(10);
        
        return response()->json([
            'status' => true,
            'bookings' => $bookings
        ]);
    }
    
    /**
     * إنشاء حجز جديد
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'specialist_id' => 'required|exists:specialists,id',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after:now',
            'booking_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // التحقق من توفر المختص في الوقت المطلوب
        $specialist = Specialist::findOrFail($request->specialist_id);
        
        if (!$specialist->is_verified || !$specialist->is_available) {
            return response()->json([
                'status' => false,
                'message' => 'المختص غير متاح حالياً'
            ], 400);
        }
        
        // التحقق من توفر الوقت
        $bookingDate = Carbon::parse($request->booking_date);
        $dayOfWeek = $bookingDate->dayOfWeek;
        $bookingTime = Carbon::parse($request->booking_time)->format('H:i');
        
        $availableTime = $specialist->availableTimes()
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $bookingTime)
            ->where('end_time', '>', $bookingTime)
            ->first();
        
        if (!$availableTime) {
            return response()->json([
                'status' => false,
                'message' => 'المختص غير متاح في هذا الوقت'
            ], 400);
        }
        
        // التحقق من عدم وجود حجز آخر في نفس الوقت
        $existingBooking = Booking::where('specialist_id', $request->specialist_id)
            ->whereDate('booking_date', $bookingDate->format('Y-m-d'))
            ->whereTime('booking_time', $bookingTime)
            ->where('status', '!=', 'cancelled')
            ->first();
        
        if ($existingBooking) {
            return response()->json([
                'status' => false,
                'message' => 'هذا الوقت محجوز بالفعل'
            ], 400);
        }
        
        // الحصول على سعر الخدمة
        $service = Service::findOrFail($request->service_id);
        
        // إنشاء الحجز
        $booking = Booking::create([
            'user_id' => $user->id,
            'specialist_id' => $request->specialist_id,
            'service_id' => $request->service_id,
            'booking_date' => $bookingDate->format('Y-m-d'),
            'booking_time' => $bookingTime,
            'duration' => $service->duration,
            'amount' => $service->price,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الحجز بنجاح، يرجى إكمال عملية الدفع',
            'booking' => $booking->load(['specialist.user', 'service'])
        ], 201);
    }
    
    /**
     * عرض تفاصيل حجز محدد
     */
    public function show($id)
    {
        $user = request()->user();
        
        $booking = Booking::with(['specialist.user', 'service', 'session', 'payment'])
            ->where(function($query) use ($user) {
                // المستخدم يمكنه رؤية حجوزاته فقط
                if ($user->hasRole('user')) {
                    $query->where('user_id', $user->id);
                }
                // المختص يمكنه رؤية الحجوزات المرتبطة به
                elseif ($user->hasRole('specialist')) {
                    $specialist = Specialist::where('user_id', $user->id)->first();
                    if ($specialist) {
                        $query->where('specialist_id', $specialist->id);
                    }
                }
                // المدير يمكنه رؤية جميع الحجوزات
            })
            ->findOrFail($id);
        
        return response()->json([
            'status' => true,
            'booking' => $booking
        ]);
    }
    
    /**
     * إلغاء حجز
     */
    public function cancel($id)
    {
        $user = request()->user();
        
        $booking = Booking::where(function($query) use ($user) {
                // المستخدم يمكنه إلغاء حجوزاته فقط
                if ($user->hasRole('user')) {
                    $query->where('user_id', $user->id);
                }
                // المختص يمكنه إلغاء الحجوزات المرتبطة به
                elseif ($user->hasRole('specialist')) {
                    $specialist = Specialist::where('user_id', $user->id)->first();
                    if ($specialist) {
                        $query->where('specialist_id', $specialist->id);
                    }
                }
                // المدير يمكنه إلغاء جميع الحجوزات
            })
            ->findOrFail($id);
        
        // التحقق من إمكانية إلغاء الحجز
        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إلغاء هذا الحجز'
            ], 400);
        }
        
        // التحقق من سياسة الإلغاء (مثلاً: يمكن الإلغاء قبل 24 ساعة من الموعد)
        $bookingDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time);
        $now = Carbon::now();
        $hoursUntilBooking = $now->diffInHours($bookingDateTime, false);
        
        if ($hoursUntilBooking < 24 && !$user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إلغاء الحجز قبل أقل من 24 ساعة من الموعد'
            ], 400);
        }
        
        // إلغاء الحجز
        $booking->status = 'cancelled';
        $booking->save();
        
        // إذا كان هناك دفع، يتم إنشاء طلب استرداد
        $payment = Payment::where('booking_id', $booking->id)->where('status', 'completed')->first();
        if ($payment) {
            // هنا يمكن إضافة منطق استرداد المبلغ حسب سياسة الموقع
            // مثلاً: إذا كان الإلغاء قبل 48 ساعة، يتم استرداد كامل المبلغ
            // وإذا كان قبل 24 ساعة، يتم استرداد 50% من المبلغ
            
            if ($hoursUntilBooking >= 48) {
                $refundAmount = $payment->amount;
            } elseif ($hoursUntilBooking >= 24) {
                $refundAmount = $payment->amount * 0.5;
            } else {
                $refundAmount = 0;
            }
            
            if ($refundAmount > 0) {
                // إنشاء طلب استرداد
                // هنا يمكن إضافة منطق التكامل مع بوابة الدفع لإجراء الاسترداد
            }
        }
        
        return response()->json([
            'status' => true,
            'message' => 'تم إلغاء الحجز بنجاح',
            'booking' => $booking->load(['specialist.user', 'service'])
        ]);
    }
    
    /**
     * تأكيد حجز (للمختص أو المدير)
     */
    public function confirm($id)
    {
        $user = request()->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('specialist') && !$user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $booking = Booking::findOrFail($id);
        
        // إذا كان المستخدم مختص، نتحقق من أن الحجز مرتبط به
        if ($user->hasRole('specialist')) {
            $specialist = Specialist::where('user_id', $user->id)->first();
            if (!$specialist || $booking->specialist_id !== $specialist->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مصرح لك بهذه العملية'
                ], 403);
            }
        }
        
        // التحقق من حالة الحجز
        if ($booking->status !== 'pending' && $booking->status !== 'payment_completed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن تأكيد هذا الحجز'
            ], 400);
        }
        
        // تأكيد الحجز
        $booking->status = 'confirmed';
        $booking->save();
        
        return response()->json([
            'status' => true,
            'message' => 'تم تأكيد الحجز بنجاح',
            'booking' => $booking->load(['specialist.user', 'service'])
        ]);
    }
    
    /**
     * إكمال الدفع للحجز
     */
    public function completePayment(Request $request, $id)
    {
        $user = $request->user();
        
        $booking = Booking::where('user_id', $user->id)->findOrFail($id);
        
        // التحقق من حالة الحجز
        if ($booking->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إكمال الدفع لهذا الحجز'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:credit_card,mada,apple_pay,stc_pay',
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // هنا يمكن إضافة منطق التكامل مع بوابة الدفع للتحقق من صحة المعاملة
        
        // إنشاء سجل الدفع
        $payment = Payment::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'specialist_id' => $booking->specialist_id,
            'amount' => $booking->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'status' => 'completed',
        ]);
        
        // تحديث حالة الحجز
        $booking->status = 'payment_completed';
        $booking->save();
        
        return response()->json([
            'status' => true,
            'message' => 'تم إكمال عملية الدفع بنجاح',
            'booking' => $booking->load(['specialist.user', 'service']),
            'payment' => $payment
        ]);
    }
    
    /**
     * بدء جلسة لحجز محدد
     */
    public function startSession(Request $request, $id)
    {
        $user = $request->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('specialist')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $specialist = Specialist::where('user_id', $user->id)->first();
        if (!$specialist) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $booking = Booking::where('specialist_id', $specialist->id)->findOrFail($id);
        
        // التحقق من حالة الحجز
        if ($booking->status !== 'confirmed' && $booking->status !== 'payment_completed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن بدء جلسة لهذا الحجز'
            ], 400);
        }
        
        // التحقق من وقت الحجز
        $bookingDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time);
        $now = Carbon::now();
        $minutesUntilBooking = $now->diffInMinutes($bookingDateTime, false);
        
        // السماح ببدء الجلسة قبل 15 دقيقة من الموعد أو بعده بحد أقصى 15 دقيقة
        if ($minutesUntilBooking > 15 || $minutesUntilBooking < -15) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن بدء الجلسة في هذا الوقت'
            ], 400);
        }
        
        // التحقق من عدم وجود جلسة سابقة
        $existingSession = Session::where('booking_id', $booking->id)->first();
        if ($existingSession) {
            return response()->json([
                'status' => false,
                'message' => 'توجد جلسة مرتبطة بهذا الحجز بالفعل'
            ], 400);
        }
        
        // إنشاء الجلسة
        $session = Session::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'specialist_id' => $specialist->id,
            'start_time' => now(),
            'status' => 'in_progress',
            'meeting_url' => $request->meeting_url ?? null,
        ]);
        
        // تحديث حالة الحجز
        $booking->status = 'in_session';
        $booking->save();
        
        return response()->json([
            'status' => true,
            'message' => 'تم بدء الجلسة بنجاح',
            'session' => $session,
            'booking' => $booking->load(['specialist.user', 'service'])
        ]);
    }
    
    /**
     * إنهاء جلسة
     */
    public function endSession(Request $request, $id)
    {
        $user = $request->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('specialist')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $specialist = Specialist::where('user_id', $user->id)->first();
        if (!$specialist) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $session = Session::where('specialist_id', $specialist->id)->findOrFail($id);
        
        // التحقق من حالة الجلسة
        if ($session->status !== 'in_progress') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إنهاء هذه الجلسة'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // تحديث الجلسة
        $session->end_time = now();
        $session->duration = $session->start_time->diffInMinutes($session->end_time);
        $session->notes = $request->notes;
        $session->recommendations = $request->recommendations;
        $session->status = 'completed';
        $session->save();
        
        // تحديث حالة الحجز
        $booking = Booking::find($session->booking_id);
        if ($booking) {
            $booking->status = 'completed';
            $booking->save();
        }
        
        return response()->json([
            'status' => true,
            'message' => 'تم إنهاء الجلسة بنجاح',
            'session' => $session,
            'booking' => $booking ? $booking->load(['specialist.user', 'service']) : null
        ]);
    }
    
    /**
     * تقييم جلسة
     */
    public function rateSession(Request $request, $id)
    {
        $user = $request->user();
        
        $session = Session::where('user_id', $user->id)->findOrFail($id);
        
        // التحقق من حالة الجلسة
        if ($session->status !== 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن تقييم هذه الجلسة'
            ], 400);
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
        
        // تحديث الجلسة
        $session->rating = $request->rating;
        $session->review = $request->review;
        $session->save();
        
        // تحديث متوسط تقييم المختص
        $specialist = Specialist::find($session->specialist_id);
        if ($specialist) {
            $averageRating = Session::where('specialist_id', $specialist->id)
                ->whereNotNull('rating')
                ->avg('rating');
            
            $specialist->rating = $averageRating;
            $specialist->save();
        }
        
        return response()->json([
            'status' => true,
            'message' => 'تم تقييم الجلسة بنجاح',
            'session' => $session
        ]);
    }
}
