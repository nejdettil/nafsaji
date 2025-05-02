<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AdminServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات
     */
    public function index()
    {
        $services = Service::with('category')->paginate(10);
        
        // إحصائيات الخدمات
        $totalBookedServices = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select('services.id', DB::raw('count(*) as booking_count'))
            ->groupBy('services.id')
            ->pluck('booking_count', 'id')
            ->toArray();
            
        $topBookedServices = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(*) as booking_count'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('booking_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.services.index', compact('services', 'totalBookedServices', 'topBookedServices'));
    }

    /**
     * عرض نموذج إنشاء خدمة جديدة
     */
    public function create()
    {
        $categories = \App\Models\ServiceCategory::where('is_active', true)->get();
        
        return view('admin.services.create', compact('categories'));
    }

    /**
     * حفظ خدمة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'category_id' => 'required|exists:service_categories,id',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $validated['image'] = $imagePath;
        }

        Service::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'category_id' => $validated['category_id'],
            'is_active' => $request->has('is_active'),
            'image' => $validated['image'] ?? null,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    /**
     * عرض خدمة محددة
     */
    public function show(Service $service)
    {
        $service->load('category');
        
        // إحصائيات الحجوزات لهذه الخدمة
        $bookingsCount = Booking::where('service_id', $service->id)->count();
        $totalPayments = Payment::whereHas('booking', function($query) use ($service) {
            $query->where('service_id', $service->id);
        })->sum('amount');
        
        return view('admin.services.show', compact('service', 'bookingsCount', 'totalPayments'));
    }

    /**
     * عرض نموذج تعديل خدمة
     */
    public function edit(Service $service)
    {
        $categories = \App\Models\ServiceCategory::where('is_active', true)->get();
        
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * تحديث خدمة محددة
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'category_id' => 'required|exists:service_categories,id',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($service->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->image);
            }
            
            $imagePath = $request->file('image')->store('services', 'public');
            $validated['image'] = $imagePath;
        }

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'category_id' => $validated['category_id'],
            'is_active' => $request->has('is_active'),
            'image' => $validated['image'] ?? $service->image,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * حذف خدمة محددة
     */
    public function destroy(Service $service)
    {
        // التحقق من عدم وجود حجوزات مرتبطة بالخدمة
        if (Booking::where('service_id', $service->id)->count() > 0) {
            return redirect()->route('admin.services.index')
                ->with('error', 'لا يمكن حذف الخدمة لأنها مرتبطة بحجوزات');
        }

        // حذف الصورة إذا كانت موجودة
        if ($service->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($service->image);
        }
        
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
