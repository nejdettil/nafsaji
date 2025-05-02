<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * عرض نموذج إنشاء حجز جديد
     */
    public function create()
    {
        // الحصول على قائمة الخدمات المتاحة
        $services = Service::where('status', 'active')->get();
        
        // الحصول على قائمة المختصين المتاحين
        $specialists = User::where('role', 'specialist')
            ->where('status', 'active')
            ->get();
        
        return view('bookings.create', compact('services', 'specialists'));
    }
    
    /**
     * حفظ حجز جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'specialist_id' => 'required|exists:users,id',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required',
            'notes' => 'nullable|string',
        ]);
        
        // إنشاء الحجز
        $booking = new Booking();
        $booking->user_id = Auth::id() ?? null;
        $booking->service_id = $validated['service_id'];
        $booking->specialist_id = $validated['specialist_id'];
        $booking->preferred_date = $validated['preferred_date'];
        $booking->preferred_time = $validated['preferred_time'];
        $booking->notes = $validated['notes'] ?? null;
        $booking->status = 'pending';
        $booking->save();
        
        if (Auth::check()) {
            return redirect()->route('user.bookings')
                ->with('success', 'تم إنشاء الحجز بنجاح. سيتم التواصل معك قريباً لتأكيد الحجز.');
        } else {
            return redirect()->route('booking.success', $booking->id)
                ->with('success', 'تم إنشاء الحجز بنجاح. سيتم التواصل معك قريباً لتأكيد الحجز.');
        }
    }
    
    /**
     * عرض صفحة نجاح الحجز
     */
    public function success($id)
    {
        $booking = Booking::findOrFail($id);
        return view('bookings.success', compact('booking'));
    }
    
    /**
     * عرض تفاصيل الحجز
     */
    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        
        // التحقق من أن المستخدم هو صاحب الحجز أو مدير
        if (Auth::check() && (Auth::id() == $booking->user_id || Auth::user()->hasRole('admin'))) {
            return view('bookings.show', compact('booking'));
        }
        
        abort(403, 'غير مصرح لك بالوصول إلى هذا الحجز');
    }
    
    /**
     * إلغاء حجز
     */
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);
        
        // التحقق من أن المستخدم هو صاحب الحجز
        if (Auth::check() && Auth::id() == $booking->user_id) {
            $booking->status = 'cancelled';
            $booking->save();
            
            return redirect()->route('user.bookings')
                ->with('success', 'تم إلغاء الحجز بنجاح');
        }
        
        abort(403, 'غير مصرح لك بإلغاء هذا الحجز');
    }
}
