<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * عرض قائمة المدفوعات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package']);

        // إذا كان المستخدم مختص، يعرض فقط المدفوعات المرتبطة به
        if ($user->isSpecialist()) {
            $specialist = $user->specialist;
            $query->whereHas('booking', function ($q) use ($specialist) {
                $q->where('specialist_id', $specialist->id);
            });
        } 
        // إذا كان المستخدم عادي، يعرض فقط مدفوعاته
        elseif (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // تصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب طريقة الدفع
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // تصفية حسب التاريخ
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $payments = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => true,
            'data' => $payments
        ]);
    }

    /**
     * إنشاء دفعة جديدة
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required_without:package_id|exists:bookings,id',
            'package_id' => 'required_without:booking_id|exists:packages,id',
            'transaction_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:credit_card,bank_transfer,wallet,paypal,other',
            'notes' => 'nullable|string',
            'receipt_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // التحقق من صحة الحجز أو الباقة
        if ($request->has('booking_id')) {
            $booking = Booking::findOrFail($request->booking_id);
            
            // التحقق من أن المستخدم هو صاحب الحجز أو أدمن
            if (!$user->isAdmin() && $user->id !== $booking->user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مصرح لك بإنشاء دفعة لهذا الحجز'
                ], 403);
            }
            
            // التحقق من أن الحجز غير مدفوع بالفعل
            if ($booking->is_paid) {
                return response()->json([
                    'status' => false,
                    'message' => 'تم دفع هذا الحجز بالفعل'
                ], 422);
            }
        } elseif ($request->has('package_id')) {
            $package = Package::findOrFail($request->package_id);
            
            // التحقق من أن الباقة نشطة
            if (!$package->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'هذه الباقة غير متاحة حالياً'
                ], 422);
            }
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'booking_id' => $request->booking_id,
            'package_id' => $request->package_id,
            'transaction_id' => $request->transaction_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending', // يمكن تغييره لاحقاً بناءً على نتيجة معالجة الدفع
            'notes' => $request->notes,
            'receipt_url' => $request->receipt_url,
        ]);

        // تحديث حالة الحجز إذا كان الدفع مرتبط بحجز
        if ($request->has('booking_id')) {
            $booking->is_paid = true;
            $booking->status = 'confirmed';
            $booking->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الدفعة بنجاح',
            'data' => $payment->load(['user', 'booking.specialist.user', 'booking.service', 'package'])
        ], 201);
    }

    /**
     * عرض دفعة محددة
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = request()->user();
        $payment = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package'])->findOrFail($id);

        // التحقق من صلاحية الوصول
        if (!$user->isAdmin() && $user->id !== $payment->user_id && 
            ($user->isSpecialist() && $payment->booking && $user->specialist->id !== $payment->booking->specialist_id)) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بالوصول إلى هذه الدفعة'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $payment
        ]);
    }

    /**
     * تحديث حالة الدفع
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        
        // فقط الأدمن يمكنه تحديث حالة الدفع
        if (!$user->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بتحديث حالة الدفع'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = Payment::findOrFail($id);
        $payment->status = $request->status;
        
        if ($request->has('transaction_id')) {
            $payment->transaction_id = $request->transaction_id;
        }
        
        if ($request->has('notes')) {
            $payment->notes = $request->notes;
        }
        
        $payment->save();

        // إذا كان الدفع مرتبط بحجز، قم بتحديث حالة الحجز
        if ($payment->booking_id) {
            $booking = $payment->booking;
            
            if ($request->status === 'completed') {
                $booking->is_paid = true;
                $booking->status = 'confirmed';
            } elseif ($request->status === 'failed' || $request->status === 'refunded') {
                $booking->is_paid = false;
                if ($booking->status === 'confirmed') {
                    $booking->status = 'pending';
                }
            }
            
            $booking->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة الدفع بنجاح',
            'data' => $payment->load(['user', 'booking.specialist.user', 'booking.service', 'package'])
        ]);
    }

    /**
     * استرداد دفعة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refund(Request $request, $id)
    {
        $user = $request->user();
        
        // فقط الأدمن يمكنه استرداد الدفع
        if (!$user->isAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك باسترداد الدفع'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'refund_amount' => 'required|numeric|min:0',
            'refund_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = Payment::findOrFail($id);
        
        // التحقق من أن الدفع مكتمل
        if ($payment->status !== 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن استرداد دفعة غير مكتملة'
            ], 422);
        }

        $payment->status = 'refunded';
        $payment->refund_amount = $request->refund_amount;
        $payment->refund_id = $request->refund_id;
        $payment->refund_date = now();
        
        if ($request->has('notes')) {
            $payment->notes = $request->notes;
        }
        
        $payment->save();

        // إذا كان الدفع مرتبط بحجز، قم بتحديث حالة الحجز
        if ($payment->booking_id) {
            $booking = $payment->booking;
            $booking->is_paid = false;
            
            // إذا لم يكن الحجز مكتملاً بالفعل، قم بتغيير حالته إلى ملغي
            if ($booking->status !== 'completed') {
                $booking->status = 'cancelled';
                $booking->cancellation_reason = 'تم استرداد المبلغ';
            }
            
            $booking->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم استرداد الدفعة بنجاح',
            'data' => $payment->load(['user', 'booking.specialist.user', 'booking.service', 'package'])
        ]);
    }
}
