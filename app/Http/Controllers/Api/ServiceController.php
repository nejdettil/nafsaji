<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات
     */
    public function index(Request $request)
    {
        $query = Service::query()->where('is_active', true);
        
        // البحث حسب الاسم أو الوصف
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب الفئة
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // التصفية حسب السعر
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // الترتيب
        if ($request->has('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort === 'popularity') {
                $query->orderBy('popularity', 'desc');
            }
        } else {
            $query->orderBy('popularity', 'desc');
        }
        
        $services = $query->with('category')->paginate(12);
        
        return response()->json([
            'status' => true,
            'services' => $services
        ]);
    }
    
    /**
     * عرض تفاصيل خدمة محددة
     */
    public function show($id)
    {
        $service = Service::with('category')->findOrFail($id);
        
        // التحقق من أن الخدمة نشطة
        if (!$service->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'الخدمة غير متاحة حالياً'
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'service' => $service
        ]);
    }
    
    /**
     * عرض قائمة فئات الخدمات
     */
    public function categories()
    {
        $categories = ServiceCategory::where('is_active', true)->get();
        
        return response()->json([
            'status' => true,
            'categories' => $categories
        ]);
    }
    
    /**
     * عرض قائمة الباقات
     */
    public function packages(Request $request)
    {
        $query = Package::query()->where('is_active', true);
        
        // البحث حسب الاسم أو الوصف
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب السعر
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // الترتيب
        if ($request->has('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort === 'popularity') {
                $query->orderBy('popularity', 'desc');
            }
        } else {
            $query->orderBy('popularity', 'desc');
        }
        
        $packages = $query->with('services')->paginate(6);
        
        return response()->json([
            'status' => true,
            'packages' => $packages
        ]);
    }
    
    /**
     * عرض تفاصيل باقة محددة
     */
    public function showPackage($id)
    {
        $package = Package::with('services')->findOrFail($id);
        
        // التحقق من أن الباقة نشطة
        if (!$package->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'الباقة غير متاحة حالياً'
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'package' => $package
        ]);
    }
    
    /**
     * إنشاء خدمة جديدة (للمدير فقط)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:service_categories,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $serviceData = [
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'duration' => $request->duration,
            'is_active' => true,
            'popularity' => 0,
        ];
        
        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/services'), $imageName);
            $serviceData['image'] = 'uploads/services/' . $imageName;
        }
        
        $service = Service::create($serviceData);
        
        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الخدمة بنجاح',
            'service' => $service
        ], 201);
    }
    
    /**
     * تحديث خدمة (للمدير فقط)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $service = Service::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:service_categories,id',
            'price' => 'sometimes|numeric|min:0',
            'duration' => 'sometimes|integer|min:15',
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // تحديث البيانات
        if ($request->has('name')) {
            $service->name = $request->name;
        }
        
        if ($request->has('description')) {
            $service->description = $request->description;
        }
        
        if ($request->has('category_id')) {
            $service->category_id = $request->category_id;
        }
        
        if ($request->has('price')) {
            $service->price = $request->price;
        }
        
        if ($request->has('duration')) {
            $service->duration = $request->duration;
        }
        
        if ($request->has('is_active')) {
            $service->is_active = $request->is_active;
        }
        
        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/services'), $imageName);
            $service->image = 'uploads/services/' . $imageName;
        }
        
        $service->save();
        
        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الخدمة بنجاح',
            'service' => $service
        ]);
    }
    
    /**
     * حذف خدمة (للمدير فقط)
     */
    public function destroy($id)
    {
        $user = request()->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $service = Service::findOrFail($id);
        
        // بدلاً من الحذف الفعلي، نقوم بتعطيل الخدمة
        $service->is_active = false;
        $service->save();
        
        return response()->json([
            'status' => true,
            'message' => 'تم حذف الخدمة بنجاح'
        ]);
    }
    
    /**
     * إنشاء فئة خدمات جديدة (للمدير فقط)
     */
    public function storeCategory(Request $request)
    {
        $user = $request->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $category = ServiceCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'is_active' => true,
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الفئة بنجاح',
            'category' => $category
        ], 201);
    }
    
    /**
     * إنشاء باقة جديدة (للمدير فقط)
     */
    public function storePackage(Request $request)
    {
        $user = $request->user();
        
        // التحقق من الصلاحيات
        if (!$user->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بهذه العملية'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $packageData = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => true,
            'popularity' => 0,
        ];
        
        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/packages'), $imageName);
            $packageData['image'] = 'uploads/packages/' . $imageName;
        }
        
        $package = Package::create($packageData);
        
        // ربط الخدمات بالباقة
        $package->services()->attach($request->services);
        
        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الباقة بنجاح',
            'package' => $package->load('services')
        ], 201);
    }
}
