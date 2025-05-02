<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecialistController extends Controller
{
    /**
     * عرض قائمة المختصين المتاحين
     */
    public function index(Request $request)
    {
        $query = Specialist::query()->where('is_verified', true)->where('is_available', true);
        
        // البحث حسب الاسم أو التخصص
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhere('specialization', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب التخصص
        if ($request->has('specialization')) {
            $query->where('specialization', $request->specialization);
        }
        
        // التصفية حسب سنوات الخبرة
        if ($request->has('min_experience')) {
            $query->where('experience_years', '>=', $request->min_experience);
        }
        
        // الترتيب
        if ($request->has('sort')) {
            if ($request->sort === 'experience') {
                $query->orderBy('experience_years', 'desc');
            } elseif ($request->sort === 'rating') {
                $query->orderBy('rating', 'desc');
            }
        } else {
            $query->orderBy('rating', 'desc');
        }
        
        $specialists = $query->with(['user' => function($q) {
            $q->select('id', 'name', 'email', 'phone', 'profile_image');
        }])->paginate(12);
        
        return response()->json([
            'status' => true,
            'specialists' => $specialists
        ]);
    }
    
    /**
     * عرض بيانات مختص محدد
     */
    public function show($id)
    {
        $specialist = Specialist::with(['user' => function($q) {
            $q->select('id', 'name', 'email', 'phone', 'profile_image');
        }, 'services', 'availableTimes'])->findOrFail($id);
        
        // التحقق من أن المختص متاح ومتحقق
        if (!$specialist->is_verified || !$specialist->is_available) {
            return response()->json([
                'status' => false,
                'message' => 'المختص غير متاح حالياً'
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'specialist' => $specialist
        ]);
    }
    
    /**
     * تحديث حالة الإتاحة للمختص
     */
    public function updateAvailability(Request $request)
    {
        $user = $request->user();
        
        // التحقق من أن المستخدم مختص
        if (!$user->hasRole('specialist')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $specialist = Specialist::where('user_id', $user->id)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'is_available' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $specialist->is_available = $request->is_available;
        $specialist->save();
        
        return response()->json([
            'status' => true,
            'message' => $request->is_available ? 'تم تفعيل حالة الإتاحة بنجاح' : 'تم إلغاء حالة الإتاحة بنجاح',
            'specialist' => $specialist
        ]);
    }
    
    /**
     * تحديث بيانات المختص
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        // التحقق من أن المستخدم مختص
        if (!$user->hasRole('specialist')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $specialist = Specialist::where('user_id', $user->id)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'specialization' => 'sometimes|string|max:255',
            'experience_years' => 'sometimes|integer|min:0',
            'bio' => 'sometimes|string',
            'hourly_rate' => 'sometimes|numeric|min:0',
            'certificates' => 'sometimes|array',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // تحديث البيانات
        if ($request->has('specialization')) {
            $specialist->specialization = $request->specialization;
        }
        
        if ($request->has('experience_years')) {
            $specialist->experience_years = $request->experience_years;
        }
        
        if ($request->has('bio')) {
            $specialist->bio = $request->bio;
        }
        
        if ($request->has('hourly_rate')) {
            $specialist->hourly_rate = $request->hourly_rate;
        }
        
        // معالجة الشهادات إذا تم تحميلها
        if ($request->hasFile('certificates')) {
            $certificates = [];
            
            foreach ($request->file('certificates') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/certificates'), $fileName);
                $certificates[] = 'uploads/certificates/' . $fileName;
            }
            
            // إذا كان لديه شهادات سابقة، نضيف الجديدة إليها
            if ($specialist->certificates) {
                $existingCertificates = json_decode($specialist->certificates, true) ?: [];
                $certificates = array_merge($existingCertificates, $certificates);
            }
            
            $specialist->certificates = json_encode($certificates);
        }
        
        $specialist->save();
        
        return response()->json([
            'status' => true,
            'message' => 'تم تحديث بيانات المختص بنجاح',
            'specialist' => $specialist
        ]);
    }
    
    /**
     * إضافة أوقات الإتاحة للمختص
     */
    public function addAvailableTimes(Request $request)
    {
        $user = $request->user();
        
        // التحقق من أن المستخدم مختص
        if (!$user->hasRole('specialist')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $specialist = Specialist::where('user_id', $user->id)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'available_times' => 'required|array',
            'available_times.*.day' => 'required|integer|min:0|max:6',
            'available_times.*.start_time' => 'required|date_format:H:i',
            'available_times.*.end_time' => 'required|date_format:H:i|after:available_times.*.start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // حذف الأوقات القديمة وإضافة الجديدة
        $specialist->availableTimes()->delete();
        
        foreach ($request->available_times as $time) {
            $specialist->availableTimes()->create([
                'day_of_week' => $time['day'],
                'start_time' => $time['start_time'],
                'end_time' => $time['end_time'],
            ]);
        }
        
        return response()->json([
            'status' => true,
            'message' => 'تم تحديث أوقات الإتاحة بنجاح',
            'available_times' => $specialist->availableTimes
        ]);
    }
    
    /**
     * الحصول على قائمة عملاء المختص
     */
    public function getClients(Request $request)
    {
        $user = $request->user();
        
        // التحقق من أن المستخدم مختص
        if (!$user->hasRole('specialist')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $specialist = Specialist::where('user_id', $user->id)->firstOrFail();
        
        // الحصول على العملاء الذين لديهم حجوزات مع المختص
        $clients = User::whereHas('bookings', function($q) use ($specialist) {
            $q->where('specialist_id', $specialist->id);
        })->select('id', 'name', 'email', 'phone', 'profile_image')->paginate(10);
        
        return response()->json([
            'status' => true,
            'clients' => $clients
        ]);
    }
    
    /**
     * الحصول على إحصائيات المختص
     */
    public function getStatistics(Request $request)
    {
        $user = $request->user();
        
        // التحقق من أن المستخدم مختص
        if (!$user->hasRole('specialist')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $specialist = Specialist::where('user_id', $user->id)->firstOrFail();
        
        // إحصائيات الحجوزات
        $totalBookings = $specialist->bookings()->count();
        $completedSessions = $specialist->sessions()->where('status', 'completed')->count();
        $activeClients = User::whereHas('bookings', function($q) use ($specialist) {
            $q->where('specialist_id', $specialist->id);
        })->count();
        
        // إحصائيات المدفوعات
        $totalEarnings = $specialist->payments()->sum('amount');
        $monthlyEarnings = $specialist->payments()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        return response()->json([
            'status' => true,
            'statistics' => [
                'total_bookings' => $totalBookings,
                'completed_sessions' => $completedSessions,
                'active_clients' => $activeClients,
                'total_earnings' => $totalEarnings,
                'monthly_earnings' => $monthlyEarnings,
                'rating' => $specialist->rating,
            ]
        ]);
    }
}
