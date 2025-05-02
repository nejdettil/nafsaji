<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialist;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class SpecialistController extends Controller
{
    /**
     * عرض قائمة المختصين في لوحة الإدارة
     */
    public function index()
    {
        $specialists = Specialist::with(['user', 'services', 'categories'])->get();
        return view('admin.specialists.index', compact('specialists'));
    }

    /**
     * عرض نموذج إنشاء مختص جديد
     */
    public function create()
    {
        $categories = ServiceCategory::all();
        return view('admin.specialists.create', compact('categories'));
    }

    /**
     * حفظ مختص جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string|max:255',
            'bio' => 'required|string',
            'years_experience' => 'required|integer|min:0',
            'session_price' => 'required|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:service_categories,id',
        ]);

        $specialist = Specialist::create([
            'user_id' => $request->user_id,
            'specialization' => $request->specialization,
            'bio' => $request->bio,
            'years_experience' => $request->years_experience,
            'session_price' => $request->session_price,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        // ربط التصنيفات
        $specialist->categories()->attach($request->categories);

        return redirect()->route('admin.specialists.index')
            ->with('success', 'تم إنشاء المختص بنجاح');
    }

    /**
     * عرض بيانات مختص محدد
     */
    public function show($id)
    {
        $specialist = Specialist::with([
            'user', 
            'services', 
            'categories', 
            'reviews.user',
            'education',
            'experience',
            'certifications',
            'bookings.user'
        ])->findOrFail($id);

        return view('admin.specialists.show', compact('specialist'));
    }

    /**
     * عرض نموذج تعديل بيانات مختص
     */
    public function edit($id)
    {
        $specialist = Specialist::with(['categories'])->findOrFail($id);
        $categories = ServiceCategory::all();
        
        return view('admin.specialists.edit', compact('specialist', 'categories'));
    }

    /**
     * تحديث بيانات مختص
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'specialization' => 'required|string|max:255',
            'bio' => 'required|string',
            'years_experience' => 'required|integer|min:0',
            'session_price' => 'required|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:service_categories,id',
        ]);

        $specialist = Specialist::findOrFail($id);
        
        $specialist->update([
            'specialization' => $request->specialization,
            'bio' => $request->bio,
            'years_experience' => $request->years_experience,
            'session_price' => $request->session_price,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        // تحديث التصنيفات
        $specialist->categories()->sync($request->categories);

        return redirect()->route('admin.specialists.index')
            ->with('success', 'تم تحديث بيانات المختص بنجاح');
    }

    /**
     * تفعيل/تعطيل مختص
     */
    public function toggleActive($id)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->is_active = !$specialist->is_active;
        $specialist->save();

        $status = $specialist->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$status} المختص بنجاح");
    }

    /**
     * تمييز/إلغاء تمييز مختص
     */
    public function toggleFeatured($id)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->is_featured = !$specialist->is_featured;
        $specialist->save();

        $status = $specialist->is_featured ? 'تمييز' : 'إلغاء تمييز';
        return redirect()->back()->with('success', "تم {$status} المختص بنجاح");
    }

    /**
     * حذف مختص
     */
    public function destroy($id)
    {
        $specialist = Specialist::findOrFail($id);
        
        // التحقق من وجود حجوزات مرتبطة
        if ($specialist->bookings()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن حذف المختص لوجود حجوزات مرتبطة به');
        }
        
        // حذف العلاقات
        $specialist->categories()->detach();
        $specialist->services()->delete();
        $specialist->reviews()->delete();
        $specialist->education()->delete();
        $specialist->experience()->delete();
        $specialist->certifications()->delete();
        
        // حذف المختص
        $specialist->delete();

        return redirect()->route('admin.specialists.index')
            ->with('success', 'تم حذف المختص بنجاح');
    }

    /**
     * عرض تقييمات المختص
     */
    public function reviews($id)
    {
        $specialist = Specialist::with(['reviews.user'])->findOrFail($id);
        return view('admin.specialists.reviews', compact('specialist'));
    }

    /**
     * حذف تقييم
     */
    public function deleteReview($id, $reviewId)
    {
        $review = Review::where('specialist_id', $id)
                       ->where('id', $reviewId)
                       ->firstOrFail();
        
        $review->delete();
        
        // تحديث متوسط التقييم للمختص
        $specialist = Specialist::findOrFail($id);
        $specialist->updateAverageRating();

        return redirect()->back()->with('success', 'تم حذف التقييم بنجاح');
    }
}
