<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * عرض عدد الإشعارات غير المقروءة
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read_at', null)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * عرض قائمة الإشعارات
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * تحديث حالة الإشعار إلى مقروء
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id != Auth::id()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $notification->read_at = now();
        $notification->save();

        return response()->json(['success' => true]);
    }

    /**
     * تحديث حالة جميع الإشعارات إلى مقروءة
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
