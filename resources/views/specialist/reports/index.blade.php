@extends(\'layouts.app\')

@section(\'content\')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">التقارير العامة</h1>

    {{-- Booking Statistics --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">إحصائيات الحجوزات</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-blue-100 p-4 rounded-lg text-center">
                <p class="text-sm text-blue-700 font-semibold">إجمالي الحجوزات</p>
                <p class="text-2xl font-bold text-blue-900">{{ $totalBookings }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg text-center">
                <p class="text-sm text-yellow-700 font-semibold">حجوزات قيد الانتظار</p>
                <p class="text-2xl font-bold text-yellow-900">{{ $bookingsByStatus[\'pending\'] ?? 0 }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg text-center">
                <p class="text-sm text-green-700 font-semibold">حجوزات مكتملة</p>
                <p class="text-2xl font-bold text-green-900">{{ $bookingsByStatus[\'completed\'] ?? 0 }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded-lg text-center">
                <p class="text-sm text-red-700 font-semibold">حجوزات ملغاة</p>
                <p class="text-2xl font-bold text-red-900">{{ $bookingsByStatus[\'cancelled\'] ?? 0 }}</p>
            </div>
        </div>
        {{-- Monthly Bookings Chart --}}
        <div>
            <h4 class="text-lg font-semibold text-gray-600 mb-2">الحجوزات الشهرية (آخر 6 أشهر)</h4>
            <canvas id="monthlyBookingsChart"></canvas>
            {{-- <div class="text-center text-gray-500">[رسم بياني للحجوزات الشهرية هنا]</div> --}}
        </div>
    </div>

    {{-- Session Statistics --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">إحصائيات الجلسات</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
             <div class="bg-indigo-100 p-4 rounded-lg text-center">
                <p class="text-sm text-indigo-700 font-semibold">إجمالي الجلسات</p>
                <p class="text-2xl font-bold text-indigo-900">{{ $totalSessions }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-lg text-center">
                <p class="text-sm text-blue-700 font-semibold">جلسات مجدولة</p>
                <p class="text-2xl font-bold text-blue-900">{{ $sessionsByStatus[\'scheduled\'] ?? 0 }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg text-center">
                <p class="text-sm text-green-700 font-semibold">جلسات مكتملة</p>
                <p class="text-2xl font-bold text-green-900">{{ $sessionsByStatus[\'completed\'] ?? 0 }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded-lg text-center">
                <p class="text-sm text-red-700 font-semibold">جلسات ملغاة</p>
                <p class="text-2xl font-bold text-red-900">{{ $sessionsByStatus[\'canceled\'] ?? 0 }}</p>
            </div>
             <div class="bg-yellow-100 p-4 rounded-lg text-center">
                <p class="text-sm text-yellow-700 font-semibold">لم يحضر</p>
                <p class="text-2xl font-bold text-yellow-900">{{ $sessionsByStatus[\'no-show\'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Client Statistics --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">إحصائيات العملاء</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-purple-100 p-4 rounded-lg text-center">
                <p class="text-sm text-purple-700 font-semibold">إجمالي العملاء</p>
                <p class="text-2xl font-bold text-purple-900">{{ $totalClients }}</p>
            </div>
            <div class="bg-teal-100 p-4 rounded-lg text-center">
                <p class="text-sm text-teal-700 font-semibold">عملاء جدد (آخر 30 يوم)</p>
                <p class="text-2xl font-bold text-teal-900">{{ $newClientsLast30Days }}</p>
            </div>
            <div class="bg-orange-100 p-4 rounded-lg text-center">
                <p class="text-sm text-orange-700 font-semibold">متوسط التقييم</p>
                <p class="text-2xl font-bold text-orange-900">{{ number_format($averageRating, 1) }} <span class="text-yellow-500">★</span></p>
            </div>
        </div>
    </div>

     {{-- Top Booked Services --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">أكثر الخدمات حجزاً (أعلى 5)</h3>
        @if($topBookedServicesCount->isNotEmpty())
            <ul>
                @foreach($topBookedServicesCount as $serviceName => $count)
                    <li class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-800">{{ $serviceName }}</span>
                        <span class="font-semibold text-blue-600">{{ $count }} حجوزات</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">لا توجد بيانات لعرضها.</p>
        @endif
    </div>

</div>

@push(\'scripts\')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Bookings Chart
    const bookingsCtx = document.getElementById(\'monthlyBookingsChart\').getContext(\'2d\');
    const monthlyBookingsData = @json($monthlyBookings);

    const bookingLabels = Object.keys(monthlyBookingsData).reverse();
    const bookingData = Object.values(monthlyBookingsData).reverse();

    new Chart(bookingsCtx, {
        type: \'bar\', // or \'line\'
        data: {
            labels: bookingLabels,
            datasets: [{
                label: \'عدد الحجوزات\
                data: bookingData,
                backgroundColor: \'rgba(54, 162, 235, 0.6)\", // Blue color
                borderColor: \'rgba(54, 162, 235, 1)\",
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1 // Ensure integer steps for counts
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
             plugins: {
                legend: {
                    display: false // Hide legend for single dataset
                }
            }
        }
    });
</script>
@endpush

@endsection

