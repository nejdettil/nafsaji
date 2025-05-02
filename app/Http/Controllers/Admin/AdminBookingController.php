<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    /**
     * عرض قائمة الحجوزات
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'specialist', 'service'])->paginate(10);
        
        // إحصائيات الحجوزات
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        
        return view('admin.bookings.index', compact(
            'bookings', 
            'totalBookings', 
            'pendingBookings', 
            'completedBookings', 
            'cancelledBookings'
        ));
    }

    /**
     * عرض حجز محدد
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'specialist', 'service', 'payments']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * تحديث حالة الحجز
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $booking->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['notes'] ?? $booking->admin_notes,
        ]);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    /**
     * حذف حجز محدد
     */
    public function destroy(Booking $booking)
    {
        // التحقق من عدم وجود مدفوعات مرتبطة بالحجز
        if ($booking->payments()->count() > 0) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'لا يمكن حذف الحجز لأنه مرتبط بمدفوعات');
        }

        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'تم حذف الحجز بنجاح');
    }
}
