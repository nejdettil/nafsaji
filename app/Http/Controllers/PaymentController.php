<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * عرض قائمة المدفوعات للمستخدم الحالي
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package']);

        // إذا كان المستخدم مختص، يعرض فقط المدفوعات المرتبطة به
        if ($user->hasRole('specialist')) {
            $specialist = $user->specialist;
            $query->whereHas('booking', function ($q) use ($specialist) {
                $q->where('specialist_id', $specialist->id);
            });
        } 
        // إذا كان المستخدم عادي، يعرض فقط مدفوعاته
        elseif (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // تصفية حسب طريقة الدفع
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }

        // تصفية حسب التاريخ
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // البحث عن مدفوعات مستخدم معين (للإدارة فقط)
        if ($user->hasRole('admin') && $request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        // البحث عن مدفوعات مختص معين (للإدارة فقط)
        if ($user->hasRole('admin') && $request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('specialist_id', $request->specialist_id);
            });
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $payments = $query->paginate($request->input('per_page', 10));

        return view('payments.index', compact('payments'));
    }

    /**
     * عرض تفاصيل دفعة محددة
     */
    public function show($id)
    {
        $user = Auth::user();
        $payment = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package'])->findOrFail($id);

        // التحقق من صلاحية الوصول
        if (!$user->hasRole('admin') && $user->id !== $payment->user_id && 
            ($user->hasRole('specialist') && $payment->booking && $user->specialist->id !== $payment->booking->specialist_id)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الدفعة');
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * عرض صفحة إنشاء دفعة جديدة
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // التحقق من وجود معرف الحجز أو الباقة
        if (!$request->has('booking_id') && !$request->has('package_id')) {
            return redirect()->route('home')->with('error', 'يجب تحديد الحجز أو الباقة');
        }

        // إذا كان هناك معرف حجز
        if ($request->has('booking_id')) {
            $booking = Booking::with(['user', 'specialist.user', 'service'])->findOrFail($request->booking_id);
            
            // التحقق من أن المستخدم هو صاحب الحجز أو أدمن
            if (!$user->hasRole('admin') && $user->id !== $booking->user_id) {
                return redirect()->route('home')->with('error', 'غير مصرح لك بإنشاء دفعة لهذا الحجز');
            }
            
            // التحقق من أن الحجز غير مدفوع بالفعل
            if ($booking->is_paid) {
                return redirect()->route('bookings.show', $booking->id)->with('info', 'تم دفع هذا الحجز بالفعل');
            }
            
            return view('payments.create', compact('booking'));
        }
        
        // إذا كان هناك معرف باقة
        if ($request->has('package_id')) {
            $package = Package::findOrFail($request->package_id);
            
            // التحقق من أن الباقة نشطة
            if (!$package->is_active) {
                return redirect()->route('packages.index')->with('error', 'هذه الباقة غير متاحة حالياً');
            }
            
            return view('payments.create', compact('package'));
        }
    }

    /**
     * حفظ دفعة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required_without:package_id|exists:bookings,id',
            'package_id' => 'required_without:booking_id|exists:packages,id',
            'payment_method' => 'required|in:credit_card,bank_transfer,wallet,paypal,apple_pay,mada,other',
            'notes' => 'nullable|string',
            'terms_accepted' => 'required|accepted',
        ]);

        $user = Auth::user();
        
        // التحقق من صحة الحجز أو الباقة
        if ($request->has('booking_id')) {
            $booking = Booking::findOrFail($request->booking_id);
            
            // التحقق من أن المستخدم هو صاحب الحجز أو أدمن
            if (!$user->hasRole('admin') && $user->id !== $booking->user_id) {
                return redirect()->route('home')->with('error', 'غير مصرح لك بإنشاء دفعة لهذا الحجز');
            }
            
            // التحقق من أن الحجز غير مدفوع بالفعل
            if ($booking->is_paid) {
                return redirect()->route('bookings.show', $booking->id)->with('info', 'تم دفع هذا الحجز بالفعل');
            }
            
            $amount = $booking->final_price;
            $packageId = null;
        } elseif ($request->has('package_id')) {
            $package = Package::findOrFail($request->package_id);
            
            // التحقق من أن الباقة نشطة
            if (!$package->is_active) {
                return redirect()->route('packages.index')->with('error', 'هذه الباقة غير متاحة حالياً');
            }
            
            $amount = $package->price;
            $bookingId = null;
            $packageId = $package->id;
        } else {
            return redirect()->route('home')->with('error', 'يجب تحديد الحجز أو الباقة');
        }

        try {
            // إنشاء الدفعة
            $payment = Payment::create([
                'user_id' => $user->id,
                'booking_id' => $request->booking_id ?? null,
                'package_id' => $packageId,
                'transaction_id' => 'TR-' . strtoupper(Str::random(8)),
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'status' => 'pending', // سيتم تحديثه بعد معالجة الدفع
                'notes' => $request->notes,
            ]);

            // معالجة الدفع حسب الطريقة المختارة
            $paymentResult = $this->processPayment($request->payment_method, $payment, $request->all());
            
            if ($paymentResult) {
                // تحديث حالة الدفع
                $payment->update(['status' => 'completed']);
                
                // تحديث حالة الحجز إذا كان الدفع مرتبط بحجز
                if ($request->has('booking_id')) {
                    $booking->is_paid = true;
                    $booking->status = 'confirmed';
                    $booking->save();
                    
                    // إرسال إشعارات
                    $this->sendPaymentCompletedNotifications($payment);
                    
                    return redirect()->route('bookings.show', $booking->id)->with('success', 'تم إتمام الدفع بنجاح');
                } else {
                    // إنشاء حجز للباقة
                    // هنا يمكن إضافة منطق إنشاء حجز للباقة
                    
                    return redirect()->route('packages.show', $package->id)->with('success', 'تم إتمام الدفع بنجاح');
                }
            } else {
                // تحديث حالة الدفع
                $payment->update(['status' => 'failed']);
                
                return redirect()->back()->with('error', 'فشلت عملية الدفع، يرجى المحاولة مرة أخرى');
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء الدفعة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الدفعة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * معالجة الدفع
     */
    private function processPayment($paymentMethod, Payment $payment, $data)
    {
        try {
            switch ($paymentMethod) {
                case 'credit_card':
                    return $this->processCreditCardPayment($data, $payment);
                case 'bank_transfer':
                    return $this->processBankTransferPayment($data, $payment);
                case 'wallet':
                    return $this->processWalletPayment($data, $payment);
                case 'paypal':
                    return $this->processPayPalPayment($data, $payment);
                case 'apple_pay':
                    return $this->processApplePayPayment($data, $payment);
                case 'mada':
                    return $this->processMadaPayment($data, $payment);
                case 'other':
                    return $this->processOtherPayment($data, $payment);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * معالجة الدفع ببطاقة الائتمان
     */
    private function processCreditCardPayment($data, Payment $payment)
    {
        try {
            // هنا يتم التكامل مع بوابة الدفع الفعلية
            // يمكن استخدام Stripe أو PayFort أو أي بوابة دفع أخرى
            
            // تسجيل محاولة الدفع
            Log::info('محاولة دفع ببطاقة الائتمان: ' . $payment->transaction_id);
            
            // هذه مجرد محاكاة للدفع الناجح
            return true;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع ببطاقة الائتمان: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * معالجة الدفع عبر التحويل البنكي
     */
    private function processBankTransferPayment($data, Payment $payment)
    {
        try {
            // في حالة التحويل البنكي، يتم تأكيد الدفع ولكن يبقى في حالة انتظار التحقق من التحويل
            
            // تسجيل محاولة الدفع
            Log::info('طلب دفع عبر التحويل البنكي: ' . $payment->transaction_id);
            
            // تحديث حالة الدفع إلى "في انتظار التحقق"
            $payment->update(['status' => 'pending_verification']);
            
            return true;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع عبر التحويل البنكي: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * معالجة الدفع عبر المحفظة الإلكترونية
     */
    private function processWalletPayment($data, Payment $payment)
    {
        try {
            // هنا يتم التكامل مع نظام المحفظة الإلكترونية
            
            // تسجيل محاولة الدفع
            Log::info('محاولة دفع عبر المحفظة الإلكترونية: ' . $payment->transaction_id);
            
            // هذه مجرد محاكاة للدفع الناجح
            return true;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع عبر المحفظة الإلكترونية: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * معالجة الدفع عبر PayPal
     */
    private function processPayPalPayment($data, Payment $payment)
    {
        try {
            // هنا يتم التكامل مع PayPal
            
            // تسجيل محاولة الدفع
            Log::info('محاولة دفع عبر PayPal: ' . $payment->transaction_id);
            
            // هذه مجرد محاكاة للدفع الناجح
            return true;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع عبر PayPal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * معالجة الدفع عبر Apple Pay
     */
    private function processApplePayPayment($data, Payment $payment)
    {
        try {
            // هنا يتم التكامل مع Apple Pay
            
            // تسجيل محاولة الدفع
            Log::info('محاولة دفع عبر Apple Pay: ' . $payment->transaction_id);
            
            // هذه مجرد محاكاة للدفع الناجح
            return true;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع عبر Apple Pay: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * معالجة الدفع عبر مدى
     */
    private function processMadaPayment($data, Payment $payment)
    {
        try {
            // هنا يتم التكامل مع بوابة الدفع لمدى
            
            // تسجيل محاولة الدفع
            Log::info('محاولة دفع عبر مدى: ' . $payment->transaction_id);
            
            // هذه مجرد محاكاة للدفع الناجح
            return true;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع عبر مدى: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * معالجة الدفع بطرق أخرى
     */
    private function processOtherPayment($data, Payment $payment)
    {
        try {
            // تسجيل محاولة الدفع
            Log::info('محاولة دفع بطريقة أخرى: ' . $payment->transaction_id);
            
            // هذه مجرد محاكاة للدفع الناجح
            return true;
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع بطريقة أخرى: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * عرض صفحة تحديث حالة الدفع (للإدارة فقط)
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // فقط الأدمن يمكنه تحديث حالة الدفع
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بتحديث حالة الدفع');
        }

        $payment = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package'])->findOrFail($id);

        return view('payments.edit', compact('payment'));
    }

    /**
     * تحديث حالة الدفع (للإدارة فقط)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,pending_verification,completed,failed,refunded',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // فقط الأدمن يمكنه تحديث حالة الدفع
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بتحديث حالة الدفع');
        }

        try {
            $payment = Payment::findOrFail($id);
            
            // حفظ الحالة القديمة للإشعارات
            $oldStatus = $payment->status;
            
            $payment->status = $request->status;
            
            if ($request->has('transaction_id') && !empty($request->transaction_id)) {
                $payment->transaction_id = $request->transaction_id;
            }
            
            if ($request->has('notes')) {
                $payment->notes = $request->notes;
            }
            
            $payment->save();

            // إذا كان الدفع مرتبط بحجز، قم بتحديث حالة الحجز
            if ($payment->booking_id) {
                $booking = $payment->booking;
                
                if ($request->status === 'completed' && $oldStatus !== 'completed') {
                    $booking->is_paid = true;
                    $booking->status = 'confirmed';
                    
                    // إرسال إشعارات إكمال الدفع
                    $this->sendPaymentCompletedNotifications($payment);
                } elseif (($request->status === 'failed' || $request->status === 'refunded') && $oldStatus === 'completed') {
                    $booking->is_paid = false;
                    if ($booking->status === 'confirmed' && $booking->status !== 'completed') {
                        $booking->status = 'pending';
                    }
                    
                    // إرسال إشعارات فشل أو استرداد الدفع
                    if ($request->status === 'failed') {
                        $this->sendPaymentFailedNotifications($payment);
                    } else {
                        $this->sendPaymentRefundedNotifications($payment);
                    }
                }
                
                $booking->save();
            }

            return redirect()->route('payments.show', $payment->id)->with('success', 'تم تحديث حالة الدفع بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث حالة الدفع: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث حالة الدفع، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض صفحة استرداد دفعة (للإدارة فقط)
     */
    public function showRefundForm($id)
    {
        $user = Auth::user();
        
        // فقط الأدمن يمكنه استرداد الدفع
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك باسترداد الدفع');
        }

        $payment = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package'])->findOrFail($id);
        
        // التحقق من أن الدفع مكتمل
        if ($payment->status !== 'completed') {
            return redirect()->route('payments.show', $payment->id)->with('error', 'لا يمكن استرداد دفعة غير مكتملة');
        }

        return view('payments.refund', compact('payment'));
    }

    /**
     * استرداد دفعة (للإدارة فقط)
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'refund_reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // فقط الأدمن يمكنه استرداد الدفع
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك باسترداد الدفع');
        }

        try {
            $payment = Payment::findOrFail($id);
            
            // التحقق من أن الدفع مكتمل
            if ($payment->status !== 'completed') {
                return redirect()->route('payments.show', $payment->id)->with('error', 'لا يمكن استرداد دفعة غير مكتملة');
            }
            
            // التحقق من أن مبلغ الاسترداد لا يتجاوز مبلغ الدفع
            if ($request->refund_amount > $payment->amount) {
                return redirect()->back()->with('error', 'مبلغ الاسترداد لا يمكن أن يتجاوز مبلغ الدفع');
            }

            // إنشاء معاملة استرداد جديدة
            $refund = Payment::create([
                'user_id' => $payment->user_id,
                'booking_id' => $payment->booking_id,
                'package_id' => $payment->package_id,
                'transaction_id' => 'RF-' . strtoupper(Str::random(8)),
                'amount' => $request->refund_amount,
                'payment_method' => $payment->payment_method,
                'status' => 'completed',
                'notes' => $request->notes,
                'is_refund' => true,
                'parent_payment_id' => $payment->id,
                'refund_reason' => $request->refund_reason,
            ]);

            // تحديث الدفعة الأصلية
            $payment->status = 'refunded';
            $payment->refund_amount = $request->refund_amount;
            $payment->refund_date = now();
            $payment->save();

            // إذا كان الدفع مرتبط بحجز، قم بتحديث حالة الحجز
            if ($payment->booking_id) {
                $booking = $payment->booking;
                
                // إذا كان الاسترداد كاملاً
                if ($request->refund_amount >= $payment->amount) {
                    $booking->is_paid = false;
                    
                    // إذا لم يكن الحجز مكتملاً بالفعل، قم بتغيير حالته إلى ملغي
                    if ($booking->status !== 'completed') {
                        $booking->status = 'cancelled';
                        $booking->cancellation_reason = $request->refund_reason;
                    }
                }
                
                $booking->save();
            }

            // إرسال إشعارات استرداد الدفع
            $this->sendPaymentRefundedNotifications($payment, $refund);

            return redirect()->route('payments.show', $refund->id)->with('success', 'تم استرداد الدفعة بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في استرداد الدفعة: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء استرداد الدفعة، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض صفحة تقرير المدفوعات (للإدارة فقط)
     */
    public function report(Request $request)
    {
        $user = Auth::user();
        
        // فقط الأدمن يمكنه عرض تقرير المدفوعات
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بعرض تقرير المدفوعات');
        }

        $query = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package']);
        
        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // تصفية حسب طريقة الدفع
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // تصفية حسب التاريخ
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // تصفية حسب نوع الدفع (عادي أو استرداد)
        if ($request->has('is_refund') && $request->is_refund !== '') {
            $query->where('is_refund', $request->is_refund == '1');
        }
        
        // البحث عن مدفوعات مستخدم معين
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
        
        // البحث عن مدفوعات مختص معين
        if ($request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('specialist_id', $request->specialist_id);
            });
        }

        $payments = $query->get();
        
        // حساب الإحصائيات
        $totalAmount = $payments->where('is_refund', false)->where('status', 'completed')->sum('amount');
        $totalRefunds = $payments->where('is_refund', true)->sum('amount');
        $netAmount = $totalAmount - $totalRefunds;
        
        $paymentsByMethod = $payments->where('is_refund', false)->where('status', 'completed')
                                    ->groupBy('payment_method')
                                    ->map(function ($items, $key) {
                                        return [
                                            'method' => $key,
                                            'count' => $items->count(),
                                            'amount' => $items->sum('amount'),
                                        ];
                                    });
        
        $paymentsByMonth = $payments->where('is_refund', false)->where('status', 'completed')
                                   ->groupBy(function ($item) {
                                       return Carbon::parse($item->created_at)->format('Y-m');
                                   })
                                   ->map(function ($items, $key) {
                                       return [
                                           'month' => $key,
                                           'count' => $items->count(),
                                           'amount' => $items->sum('amount'),
                                       ];
                                   });

        return view('payments.report', compact(
            'payments',
            'totalAmount',
            'totalRefunds',
            'netAmount',
            'paymentsByMethod',
            'paymentsByMonth'
        ));
    }

    /**
     * تصدير تقرير المدفوعات (للإدارة فقط)
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // فقط الأدمن يمكنه تصدير تقرير المدفوعات
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بتصدير تقرير المدفوعات');
        }

        $query = Payment::with(['user', 'booking.specialist.user', 'booking.service', 'package']);
        
        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // تصفية حسب طريقة الدفع
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // تصفية حسب التاريخ
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // تصفية حسب نوع الدفع (عادي أو استرداد)
        if ($request->has('is_refund') && $request->is_refund !== '') {
            $query->where('is_refund', $request->is_refund == '1');
        }
        
        // البحث عن مدفوعات مستخدم معين
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
        
        // البحث عن مدفوعات مختص معين
        if ($request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('specialist_id', $request->specialist_id);
            });
        }

        $payments = $query->get();
        
        // تحويل البيانات إلى تنسيق CSV
        $csvData = [];
        $csvData[] = [
            'رقم الدفعة',
            'المستخدم',
            'المبلغ',
            'طريقة الدفع',
            'الحالة',
            'رقم المعاملة',
            'نوع الدفع',
            'تاريخ الدفع',
            'الحجز/الباقة',
            'المختص',
            'ملاحظات',
        ];
        
        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->id,
                $payment->user->name,
                $payment->amount,
                $this->getPaymentMethodName($payment->payment_method),
                $this->getPaymentStatusName($payment->status),
                $payment->transaction_id,
                $payment->is_refund ? 'استرداد' : 'دفع',
                $payment->created_at->format('Y-m-d H:i:s'),
                $payment->booking_id ? ('حجز #' . $payment->booking_id) : ($payment->package_id ? ('باقة #' . $payment->package_id) : ''),
                $payment->booking && $payment->booking->specialist ? $payment->booking->specialist->user->name : '',
                $payment->notes,
            ];
        }
        
        // إنشاء ملف CSV
        $filename = 'payments_report_' . date('Y-m-d') . '.csv';
        $filepath = storage_path('app/public/' . $filename);
        
        $file = fopen($filepath, 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        
        return response()->download($filepath, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }

    /**
     * الحصول على اسم طريقة الدفع
     */
    private function getPaymentMethodName($method)
    {
        $methods = [
            'credit_card' => 'بطاقة ائتمان',
            'bank_transfer' => 'تحويل بنكي',
            'wallet' => 'محفظة إلكترونية',
            'paypal' => 'PayPal',
            'apple_pay' => 'Apple Pay',
            'mada' => 'مدى',
            'other' => 'أخرى',
        ];
        
        return $methods[$method] ?? $method;
    }

    /**
     * الحصول على اسم حالة الدفع
     */
    private function getPaymentStatusName($status)
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'pending_verification' => 'في انتظار التحقق',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'refunded' => 'مسترد',
        ];
        
        return $statuses[$status] ?? $status;
    }

    /**
     * إرسال إشعارات إكمال الدفع
     */
    private function sendPaymentCompletedNotifications(Payment $payment)
    {
        try {
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $payment->user_id,
                'title' => 'تم تأكيد الدفع',
                'content' => 'تم تأكيد دفعك بمبلغ ' . $payment->amount . ' ر.س بنجاح',
                'type' => 'payment_completed',
                'is_read' => false,
                'link' => route('user.payments.show', $payment->id),
            ]);

            // إرسال إشعار للمختص (إذا كان الدفع مرتبط بحجز)
            if ($payment->booking && $payment->booking->specialist) {
                Notification::create([
                    'user_id' => $payment->booking->specialist->user_id,
                    'title' => 'دفع جديد',
                    'content' => 'تم استلام دفعة جديدة من ' . $payment->user->name . ' بمبلغ ' . $payment->amount . ' ر.س',
                    'type' => 'payment_received',
                    'is_read' => false,
                    'link' => route('specialist.bookings.show', $payment->booking_id),
                ]);
            }

            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($payment->user->email)->send(new \App\Mail\PaymentCompleted($payment));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد تأكيد الدفع للمستخدم: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إكمال الدفع: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات فشل الدفع
     */
    private function sendPaymentFailedNotifications(Payment $payment)
    {
        try {
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $payment->user_id,
                'title' => 'فشل الدفع',
                'content' => 'فشلت عملية الدفع الخاصة بك بمبلغ ' . $payment->amount . ' ر.س، يرجى المحاولة مرة أخرى',
                'type' => 'payment_failed',
                'is_read' => false,
                'link' => route('user.payments.show', $payment->id),
            ]);

            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($payment->user->email)->send(new \App\Mail\PaymentFailed($payment));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد فشل الدفع للمستخدم: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات فشل الدفع: ' . $e->getMessage());
        }
    }

    /**
     * إرسال إشعارات استرداد الدفع
     */
    private function sendPaymentRefundedNotifications(Payment $payment, Payment $refund = null)
    {
        try {
            $refundAmount = $refund ? $refund->amount : $payment->refund_amount;
            
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $payment->user_id,
                'title' => 'تم استرداد الدفع',
                'content' => 'تم استرداد مبلغ ' . $refundAmount . ' ر.س من دفعتك بنجاح',
                'type' => 'payment_refunded',
                'is_read' => false,
                'link' => route('user.payments.show', $refund ? $refund->id : $payment->id),
            ]);

            // إرسال إشعار للمختص (إذا كان الدفع مرتبط بحجز)
            if ($payment->booking && $payment->booking->specialist) {
                Notification::create([
                    'user_id' => $payment->booking->specialist->user_id,
                    'title' => 'تم استرداد دفع',
                    'content' => 'تم استرداد مبلغ ' . $refundAmount . ' ر.س من دفعة ' . $payment->user->name,
                    'type' => 'payment_refunded',
                    'is_read' => false,
                    'link' => route('specialist.bookings.show', $payment->booking_id),
                ]);
            }

            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($payment->user->email)->send(new \App\Mail\PaymentRefunded($payment, $refund));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد استرداد الدفع للمستخدم: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات استرداد الدفع: ' . $e->getMessage());
        }
    }
}
