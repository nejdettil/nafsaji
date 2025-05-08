<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * عرض جميع الإشعارات.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(10);
        return view('notifications.index', compact('notifications')); // أنشئ هذا الملف إذا احتجت عرضًا بصريًا
    }

    /**
     * جلب عدد الإشعارات غير المقروءة.
     */
    public function count()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * جلب قائمة مصغرة بالإشعارات.
     */
    public function list()
    {
        return response()->json([
            'notifications' => Auth::user()->notifications()->latest()->take(10)->get(),
        ]);
    }

    /**
     * تأشير إشعار كمقروء.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'تمت القراءة.']);
    }

    /**
     * تأشير جميع الإشعارات كمقروءة.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'تم تعليم كل الإشعارات كمقروءة.']);
    }

    /**
     * حذف إشعار واحد.
     */
    public function delete($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'تم الحذف.']);
    }

    /**
     * حذف كل الإشعارات.
     */
    public function deleteAll()
    {
        Auth::user()->notifications()->delete();
        return response()->json(['message' => 'تم حذف كل الإشعارات.']);
    }
}
