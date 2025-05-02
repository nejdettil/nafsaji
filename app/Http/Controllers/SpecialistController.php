<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialist;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class SpecialistController extends Controller
{
    /**
     * عرض قائمة المختصين
     */
    public function index(Request $request)
    {
        // استعلام أساسي للمختصين
        $query = Specialist::with(['services', 'categories', 'user'])
                          ->where('is_active', true);

        // البحث
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })->orWhere('specialization', 'like', "%{$searchTerm}%")
              ->orWhere('bio', 'like', "%{$searchTerm}%");
        }

        // تصفية حسب التخصص
        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('service_categories.id', $request->category);
            });
            $categoryName = ServiceCategory::find($request->category)->name ?? '';
        }

        // تصفية حسب التقييم
        if ($request->has('rating') && !empty($request->rating)) {
            $query->where('average_rating', '>=', $request->rating);
        }

        // الترتيب
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'rating':
                    $query->orderBy('average_rating', 'desc');
                    break;
                case 'experience':
                    $query->orderBy('years_experience', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('session_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('session_price', 'desc');
                    break;
                default:
                    $query->orderBy('average_rating', 'desc');
                    break;
            }
        } else {
            $query->orderBy('average_rating', 'desc');
        }

        // الحصول على المختصين مع ترقيم الصفحات
        $specialists = $query->paginate(12);

        // الحصول على المختصين المميزين
        $featuredSpecialists = Specialist::with(['services', 'categories', 'user'])
                                       ->where('is_active', true)
                                       ->where('is_featured', true)
                                       ->orderBy('average_rating', 'desc')
                                       ->take(4)
                                       ->get();

        // الحصول على جميع التصنيفات
        $categories = ServiceCategory::withCount(['specialists' => function($query) {
                                        $query->where('is_active', true);
                                    }])
                                   ->orderBy('name')
                                   ->get();

        return view('specialists.index', compact(
            'specialists', 
            'featuredSpecialists', 
            'categories',
            'categoryName'
        ));
    }

    /**
     * عرض صفحة مختص محدد
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
            'certifications'
        ])->findOrFail($id);

        // التحقق من أن المختص نشط
        if (!$specialist->is_active) {
            abort(404);
        }

        // زيادة عدد المشاهدات
        $specialist->increment('views_count');

        // الحصول على المختصين المشابهين
        $similarSpecialists = Specialist::with(['user', 'categories'])
                                      ->where('id', '!=', $specialist->id)
                                      ->where('is_active', true)
                                      ->whereHas('categories', function($q) use ($specialist) {
                                          $q->whereIn('service_categories.id', $specialist->categories->pluck('id'));
                                      })
                                      ->orderBy('average_rating', 'desc')
                                      ->take(3)
                                      ->get();

        // الحصول على الأوقات المتاحة للحجز
        $availableTimes = $specialist->getAvailableTimes();

        return view('specialists.show', compact('specialist', 'similarSpecialists', 'availableTimes'));
    }

    /**
     * إضافة تقييم لمختص
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|min:3|max:1000',
        ]);

        $specialist = Specialist::findOrFail($id);

        // التحقق من أن المستخدم قد حجز جلسة مع هذا المختص من قبل
        $hasBooking = \App\Models\Booking::where('user_id', Auth::id())
                                       ->where('specialist_id', $specialist->id)
                                       ->where('status', 'completed')
                                       ->exists();

        if (!$hasBooking) {
            return redirect()->back()->with('error', 'يمكنك فقط تقييم المختصين الذين حجزت معهم جلسات سابقة');
        }

        // التحقق من أن المستخدم لم يقم بتقييم هذا المختص من قبل
        $existingReview = Review::where('user_id', Auth::id())
                               ->where('specialist_id', $specialist->id)
                               ->first();

        if ($existingReview) {
            // تحديث التقييم الموجود
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            $message = 'تم تحديث تقييمك بنجاح';
        } else {
            // إنشاء تقييم جديد
            $review = Review::create([
                'user_id' => Auth::id(),
                'specialist_id' => $specialist->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            $message = 'تم إضافة تقييمك بنجاح';
        }

        // تحديث متوسط التقييم للمختص
        $specialist->updateAverageRating();

        return redirect()->back()->with('success', $message);
    }

    /**
     * البحث عن المختصين (API)
     */
    public function search(Request $request)
    {
        // استعلام أساسي للمختصين
        $query = Specialist::with(['user', 'categories'])
                          ->where('is_active', true);

        // البحث
        if ($request->has('term') && !empty($request->term)) {
            $searchTerm = $request->term;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })->orWhere('specialization', 'like', "%{$searchTerm}%");
        }

        // تصفية حسب التخصص
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('service_categories.id', $request->category_id);
            });
        }

        // الحصول على المختصين
        $specialists = $query->orderBy('average_rating', 'desc')
                            ->take(10)
                            ->get()
                            ->map(function($specialist) {
                                return [
                                    'id' => $specialist->id,
                                    'name' => $specialist->user->name,
                                    'specialization' => $specialist->specialization,
                                    'avatar' => $specialist->user->avatar,
                                    'rating' => $specialist->average_rating,
                                    'categories' => $specialist->categories->pluck('name'),
                                ];
                            });

        return response()->json($specialists);
    }
}
