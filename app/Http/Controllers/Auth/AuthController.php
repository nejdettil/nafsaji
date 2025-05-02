<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * تسجيل مستخدم جديد
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * تسجيل مختص جديد
     */
    public function registerSpecialist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'bio' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // إنشاء حساب المستخدم أولاً
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        // إنشاء سجل المختص
        $specialist = Specialist::create([
            'user_id' => $user->id,
            'specialization' => $request->specialization,
            'experience_years' => $request->experience,
            'bio' => $request->bio,
            'is_verified' => false, // يحتاج إلى تحقق من الإدارة
            'is_available' => true,
        ]);

        // إسناد دور المختص
        $user->assignRole('specialist');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'تم تقديم طلب التسجيل بنجاح، سيتم مراجعته من قبل الإدارة',
            'user' => $user,
            'specialist' => $specialist,
            'token' => $token
        ], 201);
    }

    /**
     * تسجيل الدخول
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'status' => false,
                'message' => 'الحساب غير مفعل، يرجى التواصل مع الإدارة'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // تحديد نوع المستخدم ولوحة التحكم المناسبة
        $dashboard = 'user-dashboard';
        if ($user->hasRole('admin')) {
            $dashboard = 'admin-dashboard';
        } elseif ($user->hasRole('specialist')) {
            $specialist = Specialist::where('user_id', $user->id)->first();
            if ($specialist && !$specialist->is_verified) {
                return response()->json([
                    'status' => false,
                    'message' => 'حساب المختص قيد المراجعة، سيتم إعلامك عند الموافقة'
                ], 403);
            }
            $dashboard = 'specialist-dashboard';
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token,
            'dashboard' => $dashboard
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * الحصول على بيانات المستخدم الحالي
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        // إذا كان المستخدم مختص، نقوم بإرفاق بيانات المختص
        if ($user->hasRole('specialist')) {
            $user->load('specialist');
        }

        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث البيانات الأساسية
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        // معالجة الصورة الشخصية إذا تم تحميلها
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/profiles'), $imageName);
            $user->profile_image = 'uploads/profiles/' . $imageName;
        }

        $user->save();

        // إذا كان المستخدم مختص وتم تقديم بيانات إضافية
        if ($user->hasRole('specialist') && ($request->has('bio') || $request->has('specialization') || $request->has('experience'))) {
            $specialist = Specialist::where('user_id', $user->id)->first();
            
            if ($specialist) {
                if ($request->has('bio')) {
                    $specialist->bio = $request->bio;
                }
                
                if ($request->has('specialization')) {
                    $specialist->specialization = $request->specialization;
                }
                
                if ($request->has('experience')) {
                    $specialist->experience_years = $request->experience;
                }
                
                $specialist->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث البيانات بنجاح',
            'user' => $user
        ]);
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 401);
        }

        // تحديث كلمة المرور
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }
}
