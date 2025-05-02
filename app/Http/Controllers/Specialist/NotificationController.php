<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the specialist\'s notifications.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // جلب إشعارات المستخدم الحالي (المختص) مع تقسيم الصفحات
        $notifications = Auth::user()->notifications()->paginate(15);
        // عرض صفحة الإشعارات مع تمرير بيانات الإشعارات
        return view(\'specialist.notifications.index\', compact(\'notifications\'));
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(Request $request, $id)
    {
        // البحث عن الإشعار المحدد للمستخدم الحالي أو إرجاع خطأ 404
        $notification = Auth::user()->notifications()->findOrFail($id);
        // تحديد الإشعار كمقروء
        $notification->markAsRead();

        // إعادة التوجيه إلى الصفحة السابقة مع رسالة نجاح
        return redirect()->back()->with(\'success\', \'تم تحديد الإشعار كمقروء.\');
    }

    /**
     * Mark all unread notifications as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead(Request $request)
    {
        // تحديد جميع الإشعارات غير المقروءة للمستخدم الحالي كمقروءة
        Auth::user()->unreadNotifications->markAsRead();

        // إعادة التوجيه إلى الصفحة السابقة مع رسالة نجاح
        return redirect()->back()->with(\'success\', \'تم تحديد جميع الإشعارات كمقروءة.\');
    }
}

