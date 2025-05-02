<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\Session;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingSystemController extends Controller
{
    /**
     * عرض صفحة البحث عن المختصين والخدمات
     */
    public function search(Request $request)
    {
        // البحث عن المختصين
        $specialistsQuery = Specialist::with(['user', 'categories', 'services'])
            ->where('is_active', true);
            
        // البحث حسب التخصص
        if ($request->has('specialization') && !empty($request->specialization)) {
            $specialistsQuery->where('specialization', 'like', '%' . $request->specialization . '%');
        }
        
        // البحث حسب الفئة
        if ($request->has('category_id') && !empty($request->category_id)) {
            $specialistsQuery->whereHas('categories', function ($query) use ($request) {
                $query->where('service_categories.id', $request->category_id);
            });
        }
        
        // البحث حسب التقييم
        if ($request->has('min_rating') && !empty($request->min_rating)) {
            $specialistsQuery->where('average_rating', '>=', $request->min_rating);
        }
        
        // البحث حسب سعر الاستشارة
        if ($request->has('max_fee') && !empty($request->max_fee)) {
            $specialistsQuery->where('consultation_fee', '<=', $request->max_fee);
        }
        
        // البحث حسب سنوات الخبرة
        if ($request->has('min_experience') && !empty($request->min_experience)) {
            $specialistsQuery->where('experience_years', '>=', $request->min_experience);
        }
        
        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'average_rating');
        $orderDirection = $request->input('order_direction', 'desc');
        $specialistsQuery->orderBy($orderBy, $orderDirection);
        
        $specialists = $specialistsQuery->paginate(9);
        
        // البحث عن الخدمات
        $servicesQuery = Service::with('category')
            ->where('is_active', true);
            
        // البحث حسب اسم الخدمة
        if ($request->has('service_name') && !empty($request->service_name)) {
            $servicesQuery->where('name', 'like', '%' . $request->service_name . '%');
        }
        
        // البحث حسب الفئة
        if ($request->has('category_id') && !empty($request->category_id)) {
            $servicesQuery->where('category_id', $request->category_id);
        }
        
        // البحث حسب السعر
        if ($request->has('max_price') && !empty($request->max_price)) {
            $servicesQuery->where('price', '<=', $request->max_price);
        }
        
        // ترتيب النتائج
        $orderBy = $request->input('service_order_by', 'price');
        $orderDirection = $request->input('service_order_direction', 'asc');
        $servicesQuery->orderBy($orderBy, $orderDirection);
        
        $services = $servicesQuery->paginate(12);
        
        // الحصول على قائمة الفئات للتصفية
        $categories = \App\Models\ServiceCategory::where('is_active', true)->get();
        
        return view('booking.search', compact('specialists', 'services', 'categories'));
    }
    
    /**
     * عرض صفحة تفاصيل المختص مع إمكانية الحجز
     */
    public function specialistDetails($id)
    {
        $specialist = Specialist::with(['user', 'categories', 'services', 'reviews' => function ($query) {
            $query->where('is_approved', true)->orderBy('created_at', 'desc');
        }])
        ->where('is_active', true)
        ->findOrFail($id);
        
        // الحصول على الخدمات المتاحة للمختص
        $services = $specialist->services()->where('is_active', true)->get();
        
        // الحصول على الأوقات المتاحة للحجز
        $availableTimes = $this->getAvailableTimes($specialist);
        
        return view('booking.specialist-details', compact('specialist', 'services', 'availableTimes'));
    }
    
    /**
     * عرض صفحة تفاصيل الخدمة مع إمكانية الحجز
     */
    public function serviceDetails($id)
    {
        $service = Service::with(['category', 'specialists' => function ($query) {
            $query->where('is_active', true);
        }])
        ->where('is_active', true)
        ->findOrFail($id);
        
        // الحصول على المختصين المتاحين لهذه الخدمة
        $specialists = $service->specialists()->where('is_active', true)->get();
        
        return view('booking.service-details', compact('service', 'specialists'));
    }
    
    /**
     * عرض صفحة بدء عملية الحجز
     */
    public function startBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'specialist_id' => 'required_without:service_id|exists:specialists,id',
            'service_id' => 'required_without:specialist_id|exists:services,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'يرجى تحديد المختص أو الخدمة للمتابعة');
        }
        
        // إذا تم تحديد المختص فقط
        if ($request->has('specialist_id') && !$request->has('service_id')) {
            $specialist = Specialist::with(['user', 'services'])
                ->where('is_active', true)
                ->findOrFail($request->specialist_id);
            
            $services = $specialist->services()->where('is_active', true)->get();
            
            return view('booking.start', compact('specialist', 'services'));
        }
        
        // إذا تم تحديد الخدمة فقط
        if ($request->has('service_id') && !$request->has('specialist_id')) {
            $service = Service::with('category')
                ->where('is_active', true)
                ->findOrFail($request->service_id);
            
            $specialists = $service->specialists()
                ->where('is_active', true)
                ->get();
            
            return view('booking.start', compact('service', 'specialists'));
        }
        
        // إذا تم تحديد المختص والخدمة
        $specialist = Specialist::with('user')
            ->where('is_active', true)
            ->findOrFail($request->specialist_id);
        
        $service = Service::where('is_active', true)
            ->findOrFail($request->service_id);
        
        // التحقق من أن المختص يقدم هذه الخدمة
        $specialistService = $specialist->services()->where('services.id', $service->id)->first();
        
        if (!$specialistService) {
            return redirect()->back()->with('error', 'المختص المحدد لا يقدم هذه الخدمة');
        }
        
        // الحصول على الأوقات المتاحة للحجز
        $availableTimes = $this->getAvailableTimes($specialist);
        
        return view('booking.start', compact('specialist', 'service', 'availableTimes'));
    }
    
    /**
     * عرض صفحة اختيار الوقت والتاريخ للحجز
     */
    public function selectDateTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'specialist_id' => 'required|exists:specialists,id',
            'service_id' => 'required|exists:services,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'يرجى تحديد المختص والخدمة للمتابعة');
        }
        
        $specialist = Specialist::with('user')
            ->where('is_active', true)
            ->findOrFail($request->specialist_id);
        
        $service = Service::where('is_active', true)
            ->findOrFail($request->service_id);
        
        // التحقق من أن المختص يقدم هذه الخدمة
        $specialistService = $specialist->services()->where('services.id', $service->id)->first();
        
        if (!$specialistService) {
            return redirect()->back()->with('error', 'المختص المحدد لا يقدم هذه الخدمة');
        }
        
        // الحصول على الأوقات المتاحة للحجز
        $availableTimes = $this->getAvailableTimes($specialist);
        
        return view('booking.select-datetime', compact('specialist', 'service', 'availableTimes'));
    }
    
    /**
     * عرض صفحة تأكيد تفاصيل الحجز
     */
    public function confirmBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'specialist_id' => 'required|exists:specialists,id',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'session_type' => 'required|in:online,in_person,phone',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $specialist = Specialist::with('user')
            ->where('is_active', true)
            ->findOrFail($request->specialist_id);
        
        $service = Service::where('is_active', true)
            ->findOrFail($request->service_id);
        
        // التحقق من أن المختص يقدم هذه الخدمة
        $specialistService = $specialist->services()->where('services.id', $service->id)->first();
        
        if (!$specialistService) {
            return redirect()->back()->with('error', 'المختص المحدد لا يقدم هذه الخدمة');
        }
        
        // التحقق من توفر الوقت المحدد
        $bookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
        
        if (!$this->isTimeAvailable($specialist, $bookingDateTime)) {
            return redirect()->back()->with('error', 'الوقت المحدد غير متاح، يرجى اختيار وقت آخر');
        }
        
        // حساب السعر النهائي
        $price = $service->price;
        $finalPrice = $price;
        
        // تطبيق الخصومات إن وجدت
        $discount = 0;
        $discountPercentage = 0;
        
        // التحقق من وجود كود خصم
        if ($request->has('discount_code') && !empty($request->discount_code)) {
            $discountCode = \App\Models\DiscountCode::where('code', $request->discount_code)
                ->where('is_active', true)
                ->where('expiry_date', '>=', now())
                ->first();
            
            if ($discountCode) {
                $discountPercentage = $discountCode->discount_percentage;
                $discount = ($price * $discountPercentage) / 100;
                $finalPrice = $price - $discount;
            }
        }
        
        // تخزين بيانات الحجز في الجلسة
        $request->session()->put('booking_data', [
            'specialist_id' => $request->specialist_id,
            'service_id' => $request->service_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'session_type' => $request->session_type,
            'notes' => $request->notes,
            'price' => $price,
            'discount' => $discount,
            'discount_percentage' => $discountPercentage,
            'final_price' => $finalPrice,
            'discount_code' => $request->discount_code ?? null,
        ]);
        
        return view('booking.confirm', compact('specialist', 'service', 'bookingDateTime', 'price', 'discount', 'discountPercentage', 'finalPrice'));
    }
    
    /**
     * إنشاء الحجز وتوجيه المستخدم إلى صفحة الدفع
     */
    public function processBooking(Request $request)
    {
        // التحقق من تسجيل دخول المستخدم
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'يرجى تسجيل الدخول لإكمال عملية الحجز');
        }
        
        // استرجاع بيانات الحجز من الجلسة
        $bookingData = $request->session()->get('booking_data');
        
        if (!$bookingData) {
            return redirect()->route('booking.search')->with('error', 'انتهت صلاحية بيانات الحجز، يرجى البدء من جديد');
        }
        
        $user = Auth::user();
        
        try {
            // إنشاء الحجز
            $booking = Booking::create([
                'user_id' => $user->id,
                'specialist_id' => $bookingData['specialist_id'],
                'service_id' => $bookingData['service_id'],
                'booking_date' => $bookingData['booking_date'],
                'booking_time' => $bookingData['booking_time'],
                'session_type' => $bookingData['session_type'],
                'notes' => $bookingData['notes'],
                'status' => 'pending',
                'price' => $bookingData['price'],
                'discount' => $bookingData['discount'],
                'final_price' => $bookingData['final_price'],
                'discount_code' => $bookingData['discount_code'],
                'booking_number' => 'BK-' . strtoupper(Str::random(8)),
                'is_paid' => false,
            ]);
            
            // إرسال إشعار للمختص
            $specialist = Specialist::find($bookingData['specialist_id']);
            
            Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'حجز جديد',
                'content' => 'لديك حجز جديد من ' . $user->name,
                'type' => 'new_booking',
                'is_read' => false,
                'link' => route('specialist.bookings.show', $booking->id),
            ]);
            
            // حذف بيانات الحجز من الجلسة
            $request->session()->forget('booking_data');
            
            // توجيه المستخدم إلى صفحة الدفع
            return redirect()->route('booking.payment', $booking->id);
        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء الحجز: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الحجز، يرجى المحاولة مرة أخرى');
        }
    }
    
    /**
     * عرض صفحة الدفع
     */
    public function showPayment($id)
    {
        $booking = Booking::with(['user', 'specialist.user', 'service'])
            ->findOrFail($id);
        
        // التحقق من أن المستخدم هو صاحب الحجز
        if (Auth::id() !== $booking->user_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        
        // التحقق من أن الحجز غير مدفوع
        if ($booking->is_paid) {
            return redirect()->route('user.bookings.show', $booking->id)->with('info', 'تم دفع هذا الحجز بالفعل');
        }
        
        // الحصول على طرق الدفع المتاحة
        $paymentMethods = [
            'credit_card' => 'بطاقة ائتمان',
            'bank_transfer' => 'تحويل بنكي',
            'wallet' => 'محفظة إلكترونية',
            'paypal' => 'PayPal',
            'apple_pay' => 'Apple Pay',
            'mada' => 'مدى',
            'other' => 'أخرى',
        ];
        
        return view('booking.payment', compact('booking', 'paymentMethods'));
    }
    
    /**
     * معالجة الدفع
     */
    public function processPayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:credit_card,bank_transfer,wallet,paypal,apple_pay,mada,other',
            'terms_accepted' => 'required|accepted',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $booking = Booking::with(['user', 'specialist.user', 'service'])
            ->findOrFail($id);
        
        // التحقق من أن المستخدم هو صاحب الحجز
        if (Auth::id() !== $booking->user_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        
        // التحقق من أن الحجز غير مدفوع
        if ($booking->is_paid) {
            return redirect()->route('user.bookings.show', $booking->id)->with('info', 'تم دفع هذا الحجز بالفعل');
        }
        
        try {
            // إنشاء الدفعة
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'transaction_id' => 'TR-' . strtoupper(Str::random(8)),
                'amount' => $booking->final_price,
                'payment_method' => $request->payment_method,
                'status' => 'pending', // سيتم تحديثه بعد معالجة الدفع
                'notes' => $request->notes ?? null,
            ]);
            
            // معالجة الدفع حسب الطريقة المختارة
            $paymentResult = $this->processPaymentMethod($request->payment_method, $payment, $request->all());
            
            if ($paymentResult) {
                // تحديث حالة الدفع
                $payment->update(['status' => 'completed']);
                
                // تحديث حالة الحجز
                $booking->is_paid = true;
                $booking->status = 'confirmed';
                $booking->save();
                
                // إنشاء جلسة
                $session = Session::create([
                    'user_id' => $booking->user_id,
                    'specialist_id' => $booking->specialist_id,
                    'booking_id' => $booking->id,
                    'service_id' => $booking->service_id,
                    'session_date' => $booking->booking_date,
                    'session_time' => $booking->booking_time,
                    'session_type' => $booking->session_type,
                    'status' => 'scheduled',
                    'session_number' => 'SN-' . strtoupper(Str::random(8)),
                ]);
                
                // إرسال إشعارات
                $this->sendBookingConfirmationNotifications($booking, $payment, $session);
                
                return redirect()->route('booking.success', $booking->id);
            } else {
                // تحديث حالة الدفع
                $payment->update(['status' => 'failed']);
                
                return redirect()->route('booking.payment', $booking->id)->with('error', 'فشلت عملية الدفع، يرجى المحاولة مرة أخرى');
            }
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الدفع: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة الدفع، يرجى المحاولة مرة أخرى');
        }
    }
    
    /**
     * عرض صفحة نجاح الحجز
     */
    public function bookingSuccess($id)
    {
        $booking = Booking::with(['user', 'specialist.user', 'service', 'sessions', 'payments'])
            ->findOrFail($id);
        
        // التحقق من أن المستخدم هو صاحب الحجز
        if (Auth::id() !== $booking->user_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        
        // التحقق من أن الحجز مدفوع
        if (!$booking->is_paid) {
            return redirect()->route('booking.payment', $booking->id)->with('info', 'يرجى إكمال عملية الدفع أولاً');
        }
        
        $session = $booking->sessions->first();
        $payment = $booking->payments->where('status', 'completed')->first();
        
        return view('booking.success', compact('booking', 'session', 'payment'));
    }
    
    /**
     * إلغاء الحجز
     */
    public function cancelBooking(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $booking = Booking::with(['user', 'specialist.user', 'service', 'sessions'])
            ->findOrFail($id);
        
        // التحقق من أن المستخدم هو صاحب الحجز أو المختص المعني
        $user = Auth::user();
        $isOwner = $user->id === $booking->user_id;
        $isSpecialist = $user->hasRole('specialist') && $user->specialist && $user->specialist->id === $booking->specialist_id;
        
        if (!$isOwner && !$isSpecialist && !$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بإلغاء هذا الحجز');
        }
        
        // التحقق من إمكانية إلغاء الحجز
        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('info', 'تم إلغاء هذا الحجز بالفعل');
        }
        
        if ($booking->status === 'completed') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء حجز مكتمل');
        }
        
        // التحقق من سياسة الإلغاء (مثلاً، لا يمكن الإلغاء قبل أقل من 24 ساعة من موعد الجلسة)
        $bookingDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time);
        $hoursUntilBooking = now()->diffInHours($bookingDateTime, false);
        
        if ($hoursUntilBooking < 24 && !$user->hasRole('admin')) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء الحجز قبل أقل من 24 ساعة من الموعد');
        }
        
        try {
            // تحديث حالة الحجز
            $booking->status = 'cancelled';
            $booking->cancellation_reason = $request->cancellation_reason;
            $booking->cancelled_by = $user->id;
            $booking->cancelled_at = now();
            $booking->save();
            
            // تحديث حالة الجلسات المرتبطة
            foreach ($booking->sessions as $session) {
                if ($session->status !== 'completed') {
                    $session->status = 'cancelled';
                    $session->cancellation_reason = $request->cancellation_reason;
                    $session->save();
                }
            }
            
            // إرسال إشعارات الإلغاء
            $this->sendBookingCancellationNotifications($booking, $user);
            
            // إذا كان الحجز مدفوعاً، قم بإنشاء طلب استرداد
            if ($booking->is_paid) {
                $refundRequest = \App\Models\RefundRequest::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'amount' => $booking->final_price,
                    'reason' => $request->cancellation_reason,
                    'status' => 'pending',
                    'requested_by' => $user->id,
                ]);
                
                // إرسال إشعار للإدارة بطلب الاسترداد
                $admins = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'admin');
                })->get();
                
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'طلب استرداد جديد',
                        'content' => 'هناك طلب استرداد جديد للحجز رقم ' . $booking->booking_number,
                        'type' => 'refund_request',
                        'is_read' => false,
                        'link' => route('admin.refunds.show', $refundRequest->id),
                    ]);
                }
            }
            
            return redirect()->back()->with('success', 'تم إلغاء الحجز بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إلغاء الحجز: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء الحجز، يرجى المحاولة مرة أخرى');
        }
    }
    
    /**
     * إعادة جدولة الحجز
     */
    public function rescheduleBooking(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'reschedule_reason' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $booking = Booking::with(['user', 'specialist.user', 'service', 'sessions'])
            ->findOrFail($id);
        
        // التحقق من أن المستخدم هو صاحب الحجز أو المختص المعني
        $user = Auth::user();
        $isOwner = $user->id === $booking->user_id;
        $isSpecialist = $user->hasRole('specialist') && $user->specialist && $user->specialist->id === $booking->specialist_id;
        
        if (!$isOwner && !$isSpecialist && !$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بإعادة جدولة هذا الحجز');
        }
        
        // التحقق من إمكانية إعادة جدولة الحجز
        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'لا يمكن إعادة جدولة حجز ملغي');
        }
        
        if ($booking->status === 'completed') {
            return redirect()->back()->with('error', 'لا يمكن إعادة جدولة حجز مكتمل');
        }
        
        // التحقق من توفر الوقت الجديد
        $newBookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
        
        if (!$this->isTimeAvailable($booking->specialist, $newBookingDateTime, $booking->id)) {
            return redirect()->back()->with('error', 'الوقت المحدد غير متاح، يرجى اختيار وقت آخر');
        }
        
        try {
            // حفظ التاريخ والوقت القديم للإشعارات
            $oldBookingDate = $booking->booking_date;
            $oldBookingTime = $booking->booking_time;
            
            // تحديث الحجز
            $booking->booking_date = $request->booking_date;
            $booking->booking_time = $request->booking_time;
            $booking->reschedule_reason = $request->reschedule_reason;
            $booking->rescheduled_by = $user->id;
            $booking->rescheduled_at = now();
            $booking->save();
            
            // تحديث الجلسة المرتبطة
            $session = $booking->sessions->first();
            if ($session && $session->status !== 'completed') {
                $session->session_date = $request->booking_date;
                $session->session_time = $request->booking_time;
                $session->save();
            }
            
            // إرسال إشعارات إعادة الجدولة
            $this->sendRescheduleNotifications($booking, $user, $oldBookingDate, $oldBookingTime);
            
            return redirect()->back()->with('success', 'تم إعادة جدولة الحجز بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إعادة جدولة الحجز: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إعادة جدولة الحجز، يرجى المحاولة مرة أخرى');
        }
    }
    
    /**
     * الحصول على الأوقات المتاحة للمختص
     */
    private function getAvailableTimes(Specialist $specialist)
    {
        $availableTimes = [];
        $schedule = $specialist->schedule ?? [];
        
        // الحصول على الحجوزات الحالية للمختص
        $bookings = Booking::where('specialist_id', $specialist->id)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->get();
        
        // الحصول على الإجازات
        $vacations = $specialist->vacations ?? [];
        
        // إنشاء قائمة بالأيام المتاحة للأسبوعين القادمين
        for ($i = 0; $i < 14; $i++) {
            $date = Carbon::today()->addDays($i);
            $dayName = strtolower($date->format('l'));
            
            // التحقق من أن اليوم متاح في جدول المختص
            $daySchedule = collect($schedule)->firstWhere('day', $dayName);
            
            if ($daySchedule && isset($daySchedule['is_available']) && $daySchedule['is_available']) {
                // التحقق من أن اليوم ليس ضمن الإجازات
                $isVacationDay = false;
                foreach ($vacations as $vacation) {
                    $vacationStart = Carbon::parse($vacation['start_date']);
                    $vacationEnd = Carbon::parse($vacation['end_date']);
                    
                    if ($date->between($vacationStart, $vacationEnd)) {
                        $isVacationDay = true;
                        break;
                    }
                }
                
                if (!$isVacationDay) {
                    $dateString = $date->format('Y-m-d');
                    $availableTimes[$dateString] = [];
                    
                    // إضافة الفترات المتاحة
                    if (isset($daySchedule['slots']) && is_array($daySchedule['slots'])) {
                        foreach ($daySchedule['slots'] as $slot) {
                            $startTime = Carbon::parse($slot['start_time']);
                            $endTime = Carbon::parse($slot['end_time']);
                            
                            // إنشاء فترات زمنية بمقدار ساعة
                            $currentTime = clone $startTime;
                            while ($currentTime < $endTime) {
                                $timeString = $currentTime->format('H:i');
                                $dateTimeString = $dateString . ' ' . $timeString;
                                $dateTime = Carbon::parse($dateTimeString);
                                
                                // التحقق من أن الوقت غير محجوز بالفعل
                                $isBooked = $bookings->contains(function ($booking) use ($dateString, $timeString) {
                                    return $booking->booking_date == $dateString && $booking->booking_time == $timeString;
                                });
                                
                                if (!$isBooked) {
                                    $availableTimes[$dateString][] = $timeString;
                                }
                                
                                $currentTime->addHour();
                            }
                        }
                    }
                    
                    // إذا لم يكن هناك أوقات متاحة في هذا اليوم، قم بإزالته
                    if (empty($availableTimes[$dateString])) {
                        unset($availableTimes[$dateString]);
                    }
                }
            }
        }
        
        return $availableTimes;
    }
    
    /**
     * التحقق من توفر وقت محدد
     */
    private function isTimeAvailable(Specialist $specialist, Carbon $dateTime, $excludeBookingId = null)
    {
        $date = $dateTime->format('Y-m-d');
        $time = $dateTime->format('H:i');
        $dayName = strtolower($dateTime->format('l'));
        
        // التحقق من أن اليوم متاح في جدول المختص
        $schedule = $specialist->schedule ?? [];
        $daySchedule = collect($schedule)->firstWhere('day', $dayName);
        
        if (!$daySchedule || !isset($daySchedule['is_available']) || !$daySchedule['is_available']) {
            return false;
        }
        
        // التحقق من أن الوقت ضمن الفترات المتاحة
        $isWithinSlot = false;
        if (isset($daySchedule['slots']) && is_array($daySchedule['slots'])) {
            foreach ($daySchedule['slots'] as $slot) {
                $startTime = Carbon::parse($slot['start_time']);
                $endTime = Carbon::parse($slot['end_time']);
                $checkTime = Carbon::parse($time);
                
                if ($checkTime >= $startTime && $checkTime < $endTime) {
                    $isWithinSlot = true;
                    break;
                }
            }
        }
        
        if (!$isWithinSlot) {
            return false;
        }
        
        // التحقق من أن اليوم ليس ضمن الإجازات
        $vacations = $specialist->vacations ?? [];
        foreach ($vacations as $vacation) {
            $vacationStart = Carbon::parse($vacation['start_date']);
            $vacationEnd = Carbon::parse($vacation['end_date']);
            
            if ($dateTime->between($vacationStart, $vacationEnd)) {
                return false;
            }
        }
        
        // التحقق من أن الوقت غير محجوز بالفعل
        $bookingQuery = Booking::where('specialist_id', $specialist->id)
            ->where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress']);
        
        if ($excludeBookingId) {
            $bookingQuery->where('id', '!=', $excludeBookingId);
        }
        
        $existingBooking = $bookingQuery->first();
        
        return !$existingBooking;
    }
    
    /**
     * معالجة الدفع حسب الطريقة المختارة
     */
    private function processPaymentMethod($paymentMethod, Payment $payment, $data)
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
     * إرسال إشعارات تأكيد الحجز
     */
    private function sendBookingConfirmationNotifications(Booking $booking, Payment $payment, Session $session)
    {
        try {
            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'تم تأكيد الحجز',
                'content' => 'تم تأكيد حجزك مع المختص ' . $booking->specialist->user->name . ' بنجاح',
                'type' => 'booking_confirmed',
                'is_read' => false,
                'link' => route('user.bookings.show', $booking->id),
            ]);
            
            // إرسال إشعار للمختص
            Notification::create([
                'user_id' => $booking->specialist->user_id,
                'title' => 'تم تأكيد حجز',
                'content' => 'تم تأكيد حجز جديد من ' . $booking->user->name,
                'type' => 'booking_confirmed',
                'is_read' => false,
                'link' => route('specialist.bookings.show', $booking->id),
            ]);
            
            // إرسال بريد إلكتروني للمستخدم
            try {
                // Mail::to($booking->user->email)->send(new \App\Mail\BookingConfirmed($booking, $session));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد تأكيد الحجز للمستخدم: ' . $e->getMessage());
            }
            
            // إرسال بريد إلكتروني للمختص
            try {
                // Mail::to($booking->specialist->user->email)->send(new \App\Mail\NewBooking($booking, $session));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد الحجز الجديد للمختص: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات تأكيد الحجز: ' . $e->getMessage());
        }
    }
    
    /**
     * إرسال إشعارات إلغاء الحجز
     */
    private function sendBookingCancellationNotifications(Booking $booking, $cancelledBy)
    {
        try {
            // إذا تم الإلغاء بواسطة المستخدم
            if ($cancelledBy->id === $booking->user_id) {
                // إرسال إشعار للمختص
                Notification::create([
                    'user_id' => $booking->specialist->user_id,
                    'title' => 'تم إلغاء حجز',
                    'content' => 'قام ' . $booking->user->name . ' بإلغاء الحجز',
                    'type' => 'booking_cancelled',
                    'is_read' => false,
                    'link' => route('specialist.bookings.show', $booking->id),
                ]);
            }
            // إذا تم الإلغاء بواسطة المختص
            elseif ($cancelledBy->hasRole('specialist') && $cancelledBy->specialist && $cancelledBy->specialist->id === $booking->specialist_id) {
                // إرسال إشعار للمستخدم
                Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'تم إلغاء الحجز',
                    'content' => 'قام المختص ' . $booking->specialist->user->name . ' بإلغاء الحجز',
                    'type' => 'booking_cancelled',
                    'is_read' => false,
                    'link' => route('user.bookings.show', $booking->id),
                ]);
            }
            // إذا تم الإلغاء بواسطة الإدارة
            elseif ($cancelledBy->hasRole('admin')) {
                // إرسال إشعار للمستخدم
                Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'تم إلغاء الحجز',
                    'content' => 'تم إلغاء حجزك مع المختص ' . $booking->specialist->user->name . ' من قبل الإدارة',
                    'type' => 'booking_cancelled',
                    'is_read' => false,
                    'link' => route('user.bookings.show', $booking->id),
                ]);
                
                // إرسال إشعار للمختص
                Notification::create([
                    'user_id' => $booking->specialist->user_id,
                    'title' => 'تم إلغاء حجز',
                    'content' => 'تم إلغاء حجز ' . $booking->user->name . ' من قبل الإدارة',
                    'type' => 'booking_cancelled',
                    'is_read' => false,
                    'link' => route('specialist.bookings.show', $booking->id),
                ]);
            }
            
            // إرسال بريد إلكتروني بالإلغاء
            try {
                // Mail::to($booking->user->email)->send(new \App\Mail\BookingCancelled($booking));
                // Mail::to($booking->specialist->user->email)->send(new \App\Mail\BookingCancelled($booking));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد إلغاء الحجز: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إلغاء الحجز: ' . $e->getMessage());
        }
    }
    
    /**
     * إرسال إشعارات إعادة جدولة الحجز
     */
    private function sendRescheduleNotifications(Booking $booking, $rescheduledBy, $oldDate, $oldTime)
    {
        try {
            $oldDateTime = Carbon::parse($oldDate . ' ' . $oldTime)->format('Y-m-d H:i');
            $newDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time)->format('Y-m-d H:i');
            
            // إذا تمت إعادة الجدولة بواسطة المستخدم
            if ($rescheduledBy->id === $booking->user_id) {
                // إرسال إشعار للمختص
                Notification::create([
                    'user_id' => $booking->specialist->user_id,
                    'title' => 'تم إعادة جدولة حجز',
                    'content' => 'قام ' . $booking->user->name . ' بإعادة جدولة الحجز من ' . $oldDateTime . ' إلى ' . $newDateTime,
                    'type' => 'booking_rescheduled',
                    'is_read' => false,
                    'link' => route('specialist.bookings.show', $booking->id),
                ]);
            }
            // إذا تمت إعادة الجدولة بواسطة المختص
            elseif ($rescheduledBy->hasRole('specialist') && $rescheduledBy->specialist && $rescheduledBy->specialist->id === $booking->specialist_id) {
                // إرسال إشعار للمستخدم
                Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'تم إعادة جدولة الحجز',
                    'content' => 'قام المختص ' . $booking->specialist->user->name . ' بإعادة جدولة الحجز من ' . $oldDateTime . ' إلى ' . $newDateTime,
                    'type' => 'booking_rescheduled',
                    'is_read' => false,
                    'link' => route('user.bookings.show', $booking->id),
                ]);
            }
            // إذا تمت إعادة الجدولة بواسطة الإدارة
            elseif ($rescheduledBy->hasRole('admin')) {
                // إرسال إشعار للمستخدم
                Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'تم إعادة جدولة الحجز',
                    'content' => 'تمت إعادة جدولة حجزك مع المختص ' . $booking->specialist->user->name . ' من ' . $oldDateTime . ' إلى ' . $newDateTime . ' من قبل الإدارة',
                    'type' => 'booking_rescheduled',
                    'is_read' => false,
                    'link' => route('user.bookings.show', $booking->id),
                ]);
                
                // إرسال إشعار للمختص
                Notification::create([
                    'user_id' => $booking->specialist->user_id,
                    'title' => 'تم إعادة جدولة حجز',
                    'content' => 'تمت إعادة جدولة حجز ' . $booking->user->name . ' من ' . $oldDateTime . ' إلى ' . $newDateTime . ' من قبل الإدارة',
                    'type' => 'booking_rescheduled',
                    'is_read' => false,
                    'link' => route('specialist.bookings.show', $booking->id),
                ]);
            }
            
            // إرسال بريد إلكتروني بإعادة الجدولة
            try {
                // Mail::to($booking->user->email)->send(new \App\Mail\BookingRescheduled($booking, $oldDateTime));
                // Mail::to($booking->specialist->user->email)->send(new \App\Mail\BookingRescheduled($booking, $oldDateTime));
            } catch (\Exception $e) {
                Log::error('خطأ في إرسال بريد إعادة جدولة الحجز: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال إشعارات إعادة جدولة الحجز: ' . $e->getMessage());
        }
    }
}
