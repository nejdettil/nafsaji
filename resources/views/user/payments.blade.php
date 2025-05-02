@extends(\"layouts.dashboard\")

@section(\"title\", \"سجل المدفوعات - نفسجي للتمكين النفسي\")

@section(\"content\")
<div class=\"container-fluid px-4 py-8\">
    <h1 class=\"text-3xl font-bold mb-6 text-gray-800\">سجل المدفوعات</h1>

    @if($payments->isNotEmpty())
        <div class=\"bg-white shadow-lg rounded-lg overflow-hidden\">
            <div class=\"overflow-x-auto\">
                <table class=\"min-w-full leading-normal\">
                    <thead>
                        <tr>
                            <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">تاريخ الدفع</th>
                            <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">المبلغ</th>
                            <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الحالة</th>
                            <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الوصف/الخدمة</th>
                            <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">رقم المعاملة</th>
                            <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                    <p class=\"text-gray-900 whitespace-no-wrap\">{{ $payment->created_at->format(\"Y-m-d\") }}</p>
                                    <p class=\"text-gray-600 whitespace-no-wrap text-xs\">{{ $payment->created_at->format(\"H:i\") }}</p>
                                </td>
                                <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                    <p class=\"text-gray-900 whitespace-no-wrap font-semibold\">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                                </td>
                                <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                    @php
                                        $statusClasses = [
                                            \"completed\" => \"bg-green-100 text-green-800\",
                                            \"pending\" => \"bg-yellow-100 text-yellow-800\",
                                            \"failed\" => \"bg-red-100 text-red-800\",
                                            \"refunded\" => \"bg-gray-100 text-gray-800\",
                                        ];
                                        $statusClass = $statusClasses[$payment->status] ?? \"bg-gray-100 text-gray-800\";
                                    @endphp
                                    <span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}\">
                                        {{ __(\"payments.status.\" . $payment->status) ?? ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                    <p class=\"text-gray-900 whitespace-no-wrap\">{{ $payment->description ?? ($payment->booking->service->name ?? \"دفعة عامة\") }}</p>
                                    @if($payment->booking)
                                    <p class=\"text-gray-600 whitespace-no-wrap text-xs\">حجز #{{ $payment->booking->id }}</p>
                                    @endif
                                </td>
                                <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                    <p class=\"text-gray-600 whitespace-no-wrap\">{{ $payment->transaction_id ?? \"N/A\" }}</p>
                                </td>
                                <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                    <a href=\"{{ route(\"user.payments.show\". $payment->id) }}\" class=\"text-indigo-600 hover:text-indigo-900\">التفاصيل</a>
                                    {{-- Add link to download invoice/receipt if available --}}
                                    {{-- Example: <a href=\"{{ route(\"payments.invoice\". $payment->id) }}\" class=\"ml-3 text-blue-600 hover:text-blue-900\">الفاتورة</a> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class=\"p-5\">
                {{ $payments->links() }} {{-- Pagination --}}
            </div>
        </div>
    @else
        <div class=\"bg-white shadow rounded-lg p-6 text-center\">
            <p class=\"text-gray-500\">لا يوجد لديك أي مدفوعات مسجلة حتى الآن.</p>
        </div>
    @endif
</div>
@endsection

