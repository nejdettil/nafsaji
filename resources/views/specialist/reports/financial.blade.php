@extends(\'layouts.app\')

@section(\'content\')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">التقارير المالية</h1>

    {{-- Financial Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">إجمالي الدخل</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($totalIncome, 2) }} <span class="text-sm text-gray-500">{{ config(\'app.currency\") ?? \'USD\') }}</span></p>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">متوسط الدخل الشهري (آخر 6 أشهر)</h3>
            {{-- Calculate average from monthlyIncome --}}
            @php
                $avgMonthly = $monthlyIncome->count() > 0 ? $monthlyIncome->sum() / $monthlyIncome->count() : 0;
            @endphp
            <p class="text-3xl font-bold text-blue-600">{{ number_format($avgMonthly, 2) }} <span class="text-sm text-gray-500">{{ config(\'app.currency\") ?? \'USD\') }}</span></p>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">دخل هذا الشهر</h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($monthlyIncome[now()->format(\'Y-m\")] ?? 0, 2) }} <span class="text-sm text-gray-500">{{ config(\'app.currency\") ?? \'USD\') }}</span></p>
        </div>
    </div>

    {{-- Monthly Income Chart --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">الدخل الشهري (آخر 6 أشهر)</h3>
        {{-- Placeholder for chart - Requires a charting library like Chart.js --}}
        <canvas id="monthlyIncomeChart"></canvas>
        {{-- <div class="text-center text-gray-500">[رسم بياني للدخل الشهري هنا]</div> --}}
    </div>

    {{-- Top Services by Revenue --}}
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">أكثر الخدمات تحقيقاً للدخل (أعلى 5)</h3>
        @if($topServicesByRevenue->isNotEmpty())
            <ul>
                @foreach($topServicesByRevenue as $serviceName => $revenue)
                    <li class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-800">{{ $serviceName }}</span>
                        <span class="font-semibold text-green-600">{{ number_format($revenue, 2) }} {{ config(\'app.currency\") ?? \'USD\') }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">لا توجد بيانات لعرضها.</p>
        @endif
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <h3 class="text-xl font-semibold text-gray-700 p-6">المدفوعات الأخيرة</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">تاريخ الدفع</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">العميل</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الخدمة</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المبلغ</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentPayments as $payment)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ \Carbon\Carbon::parse($payment->created_at)->format(\'Y-m-d H:i\") }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $payment->user_name }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $payment->service_name }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status == \'completed\' ? \'bg-green-100 text-green-800\' : \'bg-yellow-100 text-yellow-800\' }}">
                                    {{ __(ucfirst($payment->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                لا توجد مدفوعات لعرضها حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6">
            {{ $recentPayments->links() }} {{-- Pagination links --}}
        </div>
    </div>
</div>

@push(\'scripts\")
{{-- Include Chart.js library if you haven\'t already --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById(\'monthlyIncomeChart\').getContext(\'2d\");
    const monthlyIncomeData = @json($monthlyIncome);

    const labels = Object.keys(monthlyIncomeData).reverse(); // Reverse to show oldest first
    const data = Object.values(monthlyIncomeData).reverse();

    new Chart(ctx, {
        type: \'line\', // or \'bar\'
        data: {
            labels: labels,
            datasets: [{
                label: \'الدخل الشهري\',
                data: data,
                borderColor: \'rgb(75, 192, 192)\",
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Include currency symbol in ticks
                        callback: function(value, index, values) {
                            return value + \' {{ config(\'app.currency\") ?? \'USD\') }}\';
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
             plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || \'\';
                            if (label) {
                                label += \': \';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat(\'en-US\', { style: \'currency\', currency: \'{{ config(\'app.currency\") ?? \'USD\') }}\' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush

@endsection

