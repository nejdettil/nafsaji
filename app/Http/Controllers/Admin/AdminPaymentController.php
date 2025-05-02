<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    /**
     * عرض قائمة المدفوعات
     */
    public function index()
    {
        $payments = Payment::with(['booking', 'booking.user', 'booking.service'])->paginate(10);
        
        // إحصائيات المدفوعات
        $totalPayments = Payment::sum('amount');
        $successfulPayments = Payment::where('status', 'successful')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        $failedPayments = Payment::where('status', 'failed')->sum('amount');
        
        // إحصائيات المدفوعات الشهرية
        $monthlyPayments = DB::table('payments')
            ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
        
        return view('admin.payments.index', compact(
            'payments', 
            'totalPayments', 
            'successfulPayments', 
            'pendingPayments', 
            'failedPayments',
            'monthlyPayments'
        ));
    }

    /**
     * عرض دفعة محددة
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking', 'booking.user', 'booking.specialist', 'booking.service']);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * تحديث حالة الدفع
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,successful,failed',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $payment->notes,
        ]);

        // تحديث حالة الحجز إذا كانت الدفعة ناجحة
        if ($validated['status'] === 'successful' && $payment->booking) {
            $payment->booking->update([
                'payment_status' => 'paid',
            ]);
        }

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'تم تحديث حالة الدفع بنجاح');
    }
}
