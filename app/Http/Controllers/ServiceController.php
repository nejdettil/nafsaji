<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Specialist;
use App\Models\Specialization;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات
     */
    public function index(Request $request)
    {
        // الخدمات
        $services = Service::where('is_package', false)
            ->where('is_active', true);

        // تصفية حسب الفئة
        if ($request->has('category')) {
            $services->whereHas('categories', function($q) {
                $q->where('slug', request('category'));
            });
        }

        // تصفية حسب الخدمة
        if ($request->has('service')) {
            $services->whereHas('services', function($q) {
                $q->where('slug', request('service'));
            });
        }

        $services = $services->orderBy('rating', 'desc')
            ->paginate(12);

        // فئات الخدمات
        $categories = ServiceCategory::withCount('services')
            ->orderBy('name')
            ->get();

        // الأسئلة الشائعة
        $faqs = DB::table('faqs')
            ->where('service_id', 'services') // تعديل هنا: استخدام service_id بدلاً من category
            ->take(5)
            ->get();

        // الباقات للعرض في قسم الباقات
        $packages = Service::where('is_package', true)
            ->where('is_active', true)
            ->get();

        return view('services.index', compact('services', 'categories', 'faqs', 'packages'));
    }

    /**
     * عرض تفاصيل خدمة معينة
     */
    public function show(Service $service)
    {
        // التأكد من أن الخدمة نشطة
        if (!$service->is_active) {
            abort(404);
        }

        // الخدمات المشابهة
        $relatedServices = Service::where('id', '!=', $service->id)
            ->where('is_active', true)
            ->where('is_package', $service->is_package)
            ->whereHas('categories', function($q) use ($service) {
                $q->whereIn('categories.id', $service->categories->pluck('id'));
            })
            ->take(3)
            ->get();

        // المختصين المرتبطين بالخدمة
        $specialists = Specialist::whereHas('services', function($q) use ($service) {
            $q->where('services.id', $service->id);
        })
            ->where('is_active', true)
            ->take(4)
            ->get();

        // الأسئلة الشائعة المرتبطة بالخدمة
        $faqs = Faq::where('service_id', $service->id)
            ->orWhere('service_id', null)
            ->take(5)
            ->get();

        return view('services.show', compact('service', 'relatedServices', 'specialists', 'faqs'));
    }

    /**
     * عرض قائمة الباقات
     */
    public function packages()
    {
        // الباقات
        $packages = Service::where('is_package', true)
            ->where('is_active', true)
            ->get();

        // التأكد من أن كل باقة لديها مصفوفة features
        foreach ($packages as $package) {
            if (!isset($package->features) || !is_array($package->features)) {
                $package->features = [];
            }
        }

        return view('services.packages', compact('packages'));
    }

    /**
     * عرض تفاصيل باقة معينة
     */
    public function packageShow(Service $package)
    {
        // التأكد من أن الباقة نشطة وهي فعلاً باقة
        if (!$package->is_active || !$package->is_package) {
            abort(404);
        }

        // الباقات الأخرى
        $otherPackages = Service::where('id', '!=', $package->id)
            ->where('is_active', true)
            ->where('is_package', true)
            ->take(3)
            ->get();

        return view('services.package-show', compact('package', 'otherPackages'));
    }

    /**
     * عرض صفحة حجز خدمة
     */
    public function book(Service $service)
    {
        // التأكد من أن الخدمة نشطة
        if (!$service->is_active) {
            abort(404);
        }

        // المختصين المرتبطين بالخدمة
        $specialists = Specialist::whereHas('services', function($q) use ($service) {
            $q->where('services.id', $service->id);
        })
            ->where('is_active', true)
            ->get();

        return view('services.book', compact('service', 'specialists'));
    }

    /**
     * عرض صفحة حجز باقة
     */
    public function packageBook(Service $package)
    {
        // التأكد من أن الباقة نشطة وهي فعلاً باقة
        if (!$package->is_active || !$package->is_package) {
            abort(404);
        }

        // المختصين المرتبطين بالباقة
        $specialists = Specialist::whereHas('services', function($q) use ($package) {
            $q->where('services.id', $package->id);
        })
            ->where('is_active', true)
            ->get();

        return view('services.package-book', compact('package', 'specialists'));
    }

    /**
     * عرض قائمة المختصين
     */
    public function specialists(Request $request)
    {
        // المختصين
        $specialists = Specialist::where('is_active', true);

        // تصفية حسب الخدمة
        if ($request->has('service')) {
            $specialists->whereHas('services', function($sq) {
                $sq->where('slug', request('service'));
            });
        }

        // تصفية حسب الفئة
        if ($request->has('category')) {
            $specialists->whereHas('categories', function($sq) {
                $sq->where('slug', request('category'));
            });
        }

        $specialists = $specialists->orderBy('rating', 'desc')
            ->paginate(12);

        // الفئات للتصفية
        $categories = ServiceCategory::withCount('specialists')
            ->orderBy('name')
            ->get();

        // التخصصات للتصفية
        $specializations = Specialization::orderBy('name')->get();

        // الخدمات للتصفية
        $services = Service::where('is_active', true)
            ->where('is_package', false)
            ->orderBy('name')
            ->get();

        // الشهادات للعرض في قسم التقييمات
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('specialists.index', compact('specialists', 'categories', 'specializations', 'services', 'testimonials'));
    }

    /**
     * عرض تفاصيل مختص معين
     */
    public function specialistShow(Specialist $specialist)
    {
        // التأكد من أن المختص نشط
        if (!$specialist->is_active) {
            abort(404);
        }

        // الخدمات التي يقدمها المختص
        $services = $specialist->services()
            ->where('is_active', true)
            ->where('is_package', false)
            ->get();

        // الباقات التي يقدمها المختص
        $packages = $specialist->services()
            ->where('is_active', true)
            ->where('is_package', true)
            ->get();

        // المختصين المشابهين
        $similarSpecialists = Specialist::where('id', '!=', $specialist->id)
            ->where('is_active', true)
            ->whereHas('categories', function($q) use ($specialist) {
                $q->whereIn('categories.id', $specialist->categories->pluck('id'));
            })
            ->take(3)
            ->get();

        return view('specialists.show', compact('specialist', 'services', 'packages', 'similarSpecialists'));
    }

    /**
     * عرض صفحة حجز مع مختص معين
     */
    public function specialistBook(Specialist $specialist)
    {
        // التأكد من أن المختص نشط
        if (!$specialist->is_active) {
            abort(404);
        }

        // الخدمات التي يقدمها المختص
        $services = $specialist->services()
            ->where('is_active', true)
            ->where('is_package', false)
            ->get();

        // الباقات التي يقدمها المختص
        $packages = $specialist->services()
            ->where('is_active', true)
            ->where('is_package', true)
            ->get();

        return view('specialists.book', compact('specialist', 'services', 'packages'));
    }
}
