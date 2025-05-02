<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Specialist;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * عرض قائمة التقييمات
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Review::with(['user', 'specialist.user']);

        // إذا كان المستخدم مختص، يعرض فقط التقييمات الخاصة به
        if ($user->hasRole('specialist')) {
            $specialist = $user->specialist;
            $query->where('specialist_id', $specialist->id);
        } 
        // إذا كان المستخدم عادي، يعرض فقط تقييماته
        elseif (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // تصفية حسب التقييم
        if ($request->has('rating') && !empty($request->rating)) {
            $query->where('rating', $request->rating);
        }

        // تصفية حسب الحالة
        if ($request->has('is_approved') && $request->is_approved !== '') {
            $query->where('is_approved', $request->is_approved == '1');
        }

        // تصفية حسب التاريخ
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // البحث عن تقييمات مستخدم معين (للإدارة فقط)
        if ($user->hasRole('admin') && $request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        // البحث عن تقييمات مختص معين (للإدارة فقط)
        if ($user->hasRole('admin') && $request->has('specialist_id') && !empty($request->specialist_id)) {
            $query->where('specialist_id', $request->specialist_id);
        }

        // ترتيب النتائج
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $reviews = $query->paginate($request->input('per_page', 10));

        return view('reviews.index', compact('reviews'));
    }

    /**
     * عرض تفاصيل تقييم محدد
     */
    public function show($id)
    {
        $user = Auth::user();
        $review = Review::with(['user', 'specialist.user', 'session', 'booking'])->findOrFail($id);

        // التحقق من صلاحية الوصول
        if (!$user->hasRole('admin') && $user->id !== $review->user_id && 
            (!$user->hasRole('specialist') || $user->specialist->id !== $review->specialist_id)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذا التقييم');
        }

        return view('reviews.show', compact('review'));
    }

    /**
     * عرض صفحة إضافة تقييم جديد
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // التحقق من وجود معرف الجلسة أو الحجز
        if (!$request->has('session_id') && !$request->has('booking_id')) {
            return redirect()->route('home')->with('error', 'يجب تحديد الجلسة أو الحجز');
        }

        // إذا كان هناك معرف جلسة
        if ($request->has('session_id')) {
            $session = Session::with(['specialist.user', 'booking'])->findOrFail($request->session_id);
            
            // التحقق من أن المستخدم هو صاحب الجلسة
            if ($user->id !== $session->user_id) {
                return redirect()->route('home')->with('error', 'غير مصرح لك بإضافة تقييم لهذه الجلسة');
            }
            
            // التحقق من أن الجلسة مكتملة
            if ($session->status !== 'completed') {
                return redirect()->route('user.sessions.show', $session->id)->with('error', 'لا يمكن إضافة تقييم لجلسة غير مكتملة');
            }
            
            // التحقق من عدم وجود تقييم سابق لهذه الجلسة
            $existingReview = Review::where('session_id', $session->id)->first();
            if ($existingReview) {
                return redirect()->route('reviews.show', $existingReview->id)->with('info', 'يوجد تقييم سابق لهذه الجلسة');
            }
            
            return view('reviews.create', compact('session'));
        }
        
        // إذا كان هناك معرف حجز
        if ($request->has('booking_id')) {
            $booking = Booking::with(['specialist.user', 'service'])->findOrFail($request->booking_id);
            
            // التحقق من أن المستخدم هو صاحب الحجز
            if ($user->id !== $booking->user_id) {
                return redirect()->route('home')->with('error', 'غير مصرح لك بإضافة تقييم لهذا الحجز');
            }
            
            // التحقق من أن الحجز مكتمل
            if ($booking->status !== 'completed') {
                return redirect()->route('user.bookings.show', $booking->id)->with('error', 'لا يمكن إضافة تقييم لحجز غير مكتمل');
            }
            
            // التحقق من عدم وجود تقييم سابق لهذا الحجز
            $existingReview = Review::where('booking_id', $booking->id)->first();
            if ($existingReview) {
                return redirect()->route('reviews.show', $existingReview->id)->with('info', 'يوجد تقييم سابق لهذا الحجز');
            }
            
            return view('reviews.create', compact('booking'));
        }
    }

    /**
     * حفظ تقييم جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required_without:booking_id|exists:sessions,id',
            'booking_id' => 'required_without:session_id|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
            'anonymous' => 'boolean',
        ]);

        $user = Auth::user();
        
        try {
            // التحقق من صحة الجلسة أو الحجز
            if ($request->has('session_id')) {
                $session = Session::findOrFail($request->session_id);
                
                // التحقق من أن المستخدم هو صاحب الجلسة
                if ($user->id !== $session->user_id) {
                    return redirect()->route('home')->with('error', 'غير مصرح لك بإضافة تقييم لهذه الجلسة');
                }
                
                // التحقق من أن الجلسة مكتملة
                if ($session->status !== 'completed') {
                    return redirect()->route('user.sessions.show', $session->id)->with('error', 'لا يمكن إضافة تقييم لجلسة غير مكتملة');
                }
                
                // التحقق من عدم وجود تقييم سابق لهذه الجلسة
                $existingReview = Review::where('session_id', $session->id)->first();
                if ($existingReview) {
                    return redirect()->route('reviews.show', $existingReview->id)->with('info', 'يوجد تقييم سابق لهذه الجلسة');
                }
                
                $specialistId = $session->specialist_id;
                $bookingId = $session->booking_id;
            } else {
                $booking = Booking::findOrFail($request->booking_id);
                
                // التحقق من أن المستخدم هو صاحب الحجز
                if ($user->id !== $booking->user_id) {
                    return redirect()->route('home')->with('error', 'غير مصرح لك بإضافة تقييم لهذا الحجز');
                }
                
                // التحقق من أن الحجز مكتمل
                if ($booking->status !== 'completed') {
                    return redirect()->route('user.bookings.show', $booking->id)->with('error', 'لا يمكن إضافة تقييم لحجز غير مكتمل');
                }
                
                // التحقق من عدم وجود تقييم سابق لهذا الحجز
                $existingReview = Review::where('booking_id', $booking->id)->first();
                if ($existingReview) {
                    return redirect()->route('reviews.show', $existingReview->id)->with('info', 'يوجد تقييم سابق لهذا الحجز');
                }
                
                $specialistId = $booking->specialist_id;
                $sessionId = null;
            }

            // إنشاء التقييم
            $review = Review::create([
                'user_id' => $user->id,
                'specialist_id' => $specialistId,
                'session_id' => $request->session_id ?? null,
                'booking_id' => $request->booking_id ?? null,
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
                'is_anonymous' => $request->has('anonymous'),
                'is_approved' => true, // يمكن تغييره حسب سياسة الموقع
            ]);

            // تحديث متوسط تقييم المختص
            $specialist = Specialist::find($specialistId);
            $averageRating = Review::where('specialist_id', $specialistId)
                                  ->where('is_approved', true)
                                  ->avg('rating');
            
            $specialist->update([
                'average_rating' => $averageRating,
                'reviews_count' => Review::where('specialist_id', $specialistId)
                                        ->where('is_approved', true)
                                        ->count(),
            ]);

            // إرسال إشعار للمختص
            Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'تقييم جديد',
                'content' => 'قام ' . ($request->has('anonymous') ? 'مستخدم مجهول' : $user->name) . ' بإضافة تقييم جديد بتقييم ' . $request->rating . ' نجوم',
                'type' => 'new_review',
                'is_read' => false,
                'link' => route('specialist.reviews.show', $review->id),
            ]);

            return redirect()->route('reviews.show', $review->id)->with('success', 'تم إضافة التقييم بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إضافة التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة التقييم، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض صفحة تعديل تقييم
     */
    public function edit($id)
    {
        $user = Auth::user();
        $review = Review::with(['user', 'specialist.user', 'session', 'booking'])->findOrFail($id);

        // التحقق من صلاحية التعديل (فقط صاحب التقييم)
        if ($user->id !== $review->user_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بتعديل هذا التقييم');
        }

        // التحقق من إمكانية تعديل التقييم (خلال 7 أيام من إنشائه)
        if ($review->created_at->diffInDays(now()) > 7) {
            return redirect()->route('reviews.show', $review->id)->with('error', 'لا يمكن تعديل التقييم بعد مرور 7 أيام من إنشائه');
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * تحديث تقييم
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
            'anonymous' => 'boolean',
        ]);

        $user = Auth::user();
        $review = Review::findOrFail($id);

        // التحقق من صلاحية التعديل (فقط صاحب التقييم)
        if ($user->id !== $review->user_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بتعديل هذا التقييم');
        }

        // التحقق من إمكانية تعديل التقييم (خلال 7 أيام من إنشائه)
        if ($review->created_at->diffInDays(now()) > 7) {
            return redirect()->route('reviews.show', $review->id)->with('error', 'لا يمكن تعديل التقييم بعد مرور 7 أيام من إنشائه');
        }

        try {
            // تحديث التقييم
            $review->update([
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
                'is_anonymous' => $request->has('anonymous'),
                'is_approved' => true, // يمكن تغييره حسب سياسة الموقع
                'is_edited' => true,
            ]);

            // تحديث متوسط تقييم المختص
            $specialist = Specialist::find($review->specialist_id);
            $averageRating = Review::where('specialist_id', $review->specialist_id)
                                  ->where('is_approved', true)
                                  ->avg('rating');
            
            $specialist->update([
                'average_rating' => $averageRating,
            ]);

            // إرسال إشعار للمختص
            Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'تم تعديل تقييم',
                'content' => 'قام ' . ($review->is_anonymous ? 'مستخدم مجهول' : $user->name) . ' بتعديل تقييمه إلى ' . $request->rating . ' نجوم',
                'type' => 'review_updated',
                'is_read' => false,
                'link' => route('specialist.reviews.show', $review->id),
            ]);

            return redirect()->route('reviews.show', $review->id)->with('success', 'تم تحديث التقييم بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث التقييم، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * حذف تقييم
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $review = Review::findOrFail($id);

        // التحقق من صلاحية الحذف (صاحب التقييم أو الأدمن)
        if (!$user->hasRole('admin') && $user->id !== $review->user_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بحذف هذا التقييم');
        }

        try {
            // حذف التقييم
            $review->delete();

            // تحديث متوسط تقييم المختص
            $specialist = Specialist::find($review->specialist_id);
            $averageRating = Review::where('specialist_id', $review->specialist_id)
                                  ->where('is_approved', true)
                                  ->avg('rating');
            
            $specialist->update([
                'average_rating' => $averageRating ?: 0,
                'reviews_count' => Review::where('specialist_id', $review->specialist_id)
                                        ->where('is_approved', true)
                                        ->count(),
            ]);

            // إرسال إشعار للمختص (إذا كان الحذف بواسطة المستخدم)
            if ($user->id === $review->user_id) {
                Notification::create([
                    'user_id' => $specialist->user_id,
                    'title' => 'تم حذف تقييم',
                    'content' => 'قام ' . ($review->is_anonymous ? 'مستخدم مجهول' : $user->name) . ' بحذف تقييمه',
                    'type' => 'review_deleted',
                    'is_read' => false,
                    'link' => route('specialist.reviews.index'),
                ]);
            }

            return redirect()->route('reviews.index')->with('success', 'تم حذف التقييم بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حذف التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف التقييم، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * الموافقة على تقييم (للإدارة فقط)
     */
    public function approve($id)
    {
        $user = Auth::user();
        
        // فقط الأدمن يمكنه الموافقة على التقييمات
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالموافقة على التقييمات');
        }

        $review = Review::findOrFail($id);

        try {
            // تحديث حالة التقييم
            $review->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => $user->id,
            ]);

            // تحديث متوسط تقييم المختص
            $specialist = Specialist::find($review->specialist_id);
            $averageRating = Review::where('specialist_id', $review->specialist_id)
                                  ->where('is_approved', true)
                                  ->avg('rating');
            
            $specialist->update([
                'average_rating' => $averageRating,
                'reviews_count' => Review::where('specialist_id', $review->specialist_id)
                                        ->where('is_approved', true)
                                        ->count(),
            ]);

            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $review->user_id,
                'title' => 'تمت الموافقة على تقييمك',
                'content' => 'تمت الموافقة على تقييمك للمختص ' . $specialist->user->name,
                'type' => 'review_approved',
                'is_read' => false,
                'link' => route('reviews.show', $review->id),
            ]);

            // إرسال إشعار للمختص
            Notification::create([
                'user_id' => $specialist->user_id,
                'title' => 'تقييم جديد معتمد',
                'content' => 'تمت الموافقة على تقييم جديد من ' . ($review->is_anonymous ? 'مستخدم مجهول' : $review->user->name) . ' بتقييم ' . $review->rating . ' نجوم',
                'type' => 'review_approved',
                'is_read' => false,
                'link' => route('specialist.reviews.show', $review->id),
            ]);

            return redirect()->route('admin.reviews.show', $review->id)->with('success', 'تمت الموافقة على التقييم بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في الموافقة على التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء الموافقة على التقييم، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * رفض تقييم (للإدارة فقط)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        
        // فقط الأدمن يمكنه رفض التقييمات
        if (!$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'غير مصرح لك برفض التقييمات');
        }

        $review = Review::findOrFail($id);

        try {
            // تحديث حالة التقييم
            $review->update([
                'is_approved' => false,
                'rejection_reason' => $request->rejection_reason,
                'rejected_at' => now(),
                'rejected_by' => $user->id,
            ]);

            // تحديث متوسط تقييم المختص
            $specialist = Specialist::find($review->specialist_id);
            $averageRating = Review::where('specialist_id', $review->specialist_id)
                                  ->where('is_approved', true)
                                  ->avg('rating');
            
            $specialist->update([
                'average_rating' => $averageRating ?: 0,
                'reviews_count' => Review::where('specialist_id', $review->specialist_id)
                                        ->where('is_approved', true)
                                        ->count(),
            ]);

            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $review->user_id,
                'title' => 'تم رفض تقييمك',
                'content' => 'تم رفض تقييمك للمختص ' . $specialist->user->name . ' للسبب التالي: ' . $request->rejection_reason,
                'type' => 'review_rejected',
                'is_read' => false,
                'link' => route('reviews.show', $review->id),
            ]);

            return redirect()->route('admin.reviews.show', $review->id)->with('success', 'تم رفض التقييم بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في رفض التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء رفض التقييم، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * إضافة رد على تقييم (للمختص فقط)
     */
    public function addReply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();
        $review = Review::findOrFail($id);

        // التحقق من صلاحية إضافة رد (فقط المختص المعني بالتقييم)
        if (!$user->hasRole('specialist') || $user->specialist->id !== $review->specialist_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بإضافة رد على هذا التقييم');
        }

        try {
            // تحديث التقييم بإضافة الرد
            $review->update([
                'specialist_reply' => $request->reply,
                'replied_at' => now(),
            ]);

            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $review->user_id,
                'title' => 'رد جديد على تقييمك',
                'content' => 'قام المختص ' . $user->name . ' بالرد على تقييمك',
                'type' => 'review_reply',
                'is_read' => false,
                'link' => route('reviews.show', $review->id),
            ]);

            return redirect()->route('specialist.reviews.show', $review->id)->with('success', 'تم إضافة الرد بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في إضافة رد على التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الرد، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * تعديل الرد على تقييم (للمختص فقط)
     */
    public function updateReply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();
        $review = Review::findOrFail($id);

        // التحقق من صلاحية تعديل الرد (فقط المختص المعني بالتقييم)
        if (!$user->hasRole('specialist') || $user->specialist->id !== $review->specialist_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بتعديل الرد على هذا التقييم');
        }

        // التحقق من وجود رد سابق
        if (!$review->specialist_reply) {
            return redirect()->route('specialist.reviews.show', $review->id)->with('error', 'لا يوجد رد سابق لتعديله');
        }

        try {
            // تحديث الرد
            $review->update([
                'specialist_reply' => $request->reply,
                'replied_at' => now(),
                'reply_edited' => true,
            ]);

            // إرسال إشعار للمستخدم
            Notification::create([
                'user_id' => $review->user_id,
                'title' => 'تم تعديل الرد على تقييمك',
                'content' => 'قام المختص ' . $user->name . ' بتعديل الرد على تقييمك',
                'type' => 'review_reply_updated',
                'is_read' => false,
                'link' => route('reviews.show', $review->id),
            ]);

            return redirect()->route('specialist.reviews.show', $review->id)->with('success', 'تم تعديل الرد بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في تعديل الرد على التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تعديل الرد، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * حذف الرد على تقييم (للمختص فقط)
     */
    public function deleteReply($id)
    {
        $user = Auth::user();
        $review = Review::findOrFail($id);

        // التحقق من صلاحية حذف الرد (فقط المختص المعني بالتقييم)
        if (!$user->hasRole('specialist') || $user->specialist->id !== $review->specialist_id) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بحذف الرد على هذا التقييم');
        }

        // التحقق من وجود رد سابق
        if (!$review->specialist_reply) {
            return redirect()->route('specialist.reviews.show', $review->id)->with('error', 'لا يوجد رد سابق لحذفه');
        }

        try {
            // حذف الرد
            $review->update([
                'specialist_reply' => null,
                'replied_at' => null,
                'reply_edited' => false,
            ]);

            return redirect()->route('specialist.reviews.show', $review->id)->with('success', 'تم حذف الرد بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حذف الرد على التقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الرد، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * الإبلاغ عن تقييم غير مناسب
     */
    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $review = Review::findOrFail($id);

        try {
            // إنشاء تقرير
            $report = \App\Models\Report::create([
                'user_id' => $user->id,
                'reportable_type' => 'App\Models\Review',
                'reportable_id' => $review->id,
                'reason' => $request->reason,
                'details' => $request->details,
                'status' => 'pending',
            ]);

            // إرسال إشعار للإدارة
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'بلاغ جديد عن تقييم',
                    'content' => 'قام ' . $user->name . ' بالإبلاغ عن تقييم غير مناسب',
                    'type' => 'review_report',
                    'is_read' => false,
                    'link' => route('admin.reports.show', $report->id),
                ]);
            }

            return redirect()->back()->with('success', 'تم إرسال البلاغ بنجاح، وسيتم مراجعته من قبل الإدارة');
        } catch (\Exception $e) {
            Log::error('خطأ في الإبلاغ عن تقييم: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إرسال البلاغ، يرجى المحاولة مرة أخرى');
        }
    }

    /**
     * عرض تقييمات مختص معين
     */
    public function specialistReviews($specialistId)
    {
        $specialist = Specialist::with('user')->findOrFail($specialistId);
        
        $reviews = Review::with('user')
                        ->where('specialist_id', $specialistId)
                        ->where('is_approved', true)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('reviews.specialist', compact('specialist', 'reviews'));
    }

    /**
     * عرض تقييمات المستخدم الحالي
     */
    public function userReviews()
    {
        $user = Auth::user();
        
        $reviews = Review::with(['specialist.user', 'session', 'booking'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('reviews.user', compact('reviews'));
    }
}
