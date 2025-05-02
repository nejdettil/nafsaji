<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * عرض صفحة تعديل الملف الشخصي
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * تحديث معلومات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('profile.edit')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return redirect()->route('profile.edit')->with('success', 'تم تحديث كلمة المرور بنجاح');
    }
    
    /**
     * تحديث صورة الملف الشخصي
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = Auth::user();
        
        // حذف الصورة القديمة إذا وجدت
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        
        // تخزين الصورة الجديدة
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
        $user->save();
        
        return redirect()->route('profile.edit')->with('success', 'تم تحديث صورة الملف الشخصي بنجاح');
    }
    
    /**
     * حذف صورة الملف الشخصي
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
        }
        
        return redirect()->route('profile.edit')->with('success', 'تم حذف صورة الملف الشخصي بنجاح');
    }
    
    /**
     * تحديث إعدادات الإشعارات
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        $user->email_notifications = $request->has('email_notifications');
        $user->sms_notifications = $request->has('sms_notifications');
        $user->browser_notifications = $request->has('browser_notifications');
        $user->save();
        
        return redirect()->route('profile.edit')->with('success', 'تم تحديث إعدادات الإشعارات بنجاح');
    }
}
