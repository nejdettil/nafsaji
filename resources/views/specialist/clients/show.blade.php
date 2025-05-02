@extends(\'layouts.app\")

@section(\'content\")
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">تفاصيل العميل: {{ $client->name }}</h1>
        <a href="{{ route(\'specialist.clients.index\") }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            العودة إلى قائمة العملاء
        </a>
    </div>

    {{-- Client Basic Info --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">المعلومات الأساسية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600"><strong>الاسم:</strong> {{ $client->name }}</p>
            </div>
            <div>
                <p class="text-gray-600"><strong>البريد الإلكتروني:</strong> {{ $client->email }}</p>
            </div>
            <div>
                <p class="text-gray-600"><strong>رقم الهاتف:</strong> {{ $client->phone ?? \'غير متوفر\\' }}</p>
            </div>
            <div>
                <p class="text-gray-600"><strong>تاريخ الانضمام:</strong> {{ $client->created_at->format(\'Y-m-d\') }}</p>
            </div>
        </div>
    </div>

    {{-- Bookings History --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">سجل الحجوزات</h2>
        @if($bookings->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">تاريخ الحجز</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الخدمة</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">تاريخ الموعد</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $booking->created_at->format(\'Y-m-d\') }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $booking->service->name }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $booking->appointment_date ? \Carbon\Carbon::parse($booking->appointment_date)->format(\'Y-m-d H:i\') : \'N/A\' }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $booking->status == \'completed\' ? \'bg-green-100 text-green-800\' : ($booking->status == \'confirmed\' ? \'bg-blue-100 text-blue-800\' : ($booking->status == \'cancelled\' ? \'bg-red-100 text-red-800\' : \'bg-yellow-100 text-yellow-800\')) }}">
                                        {{ __(ucfirst($booking->status)) }} {{-- Translate status if needed --}}
                                    </span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <a href="{{ route(\'specialist.bookings.show\", $booking->id) }}" class="text-indigo-600 hover:text-indigo-900">عرض الحجز</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">لا يوجد حجوزات سابقة لهذا العميل.</p>
        @endif
    </div>

    {{-- Sessions History --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">سجل الجلسات</h2>
        @if($sessions->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">تاريخ الجلسة</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الوقت</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الخدمة</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ \Carbon\Carbon::parse($session->session_date)->format(\'Y-m-d\') }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ \Carbon\Carbon::parse($session->start_time)->format(\'H:i\') }} - {{ \Carbon\Carbon::parse($session->end_time)->format(\'H:i\') }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $session->service->name }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $session->status == \'completed\' ? \'bg-green-100 text-green-800\' : ($session->status == \'scheduled\' ? \'bg-blue-100 text-blue-800\' : ($session->status == \'canceled\' ? \'bg-red-100 text-red-800\' : ($session->status == \'no-show\' ? \'bg-yellow-100 text-yellow-800\' : \'bg-gray-100 text-gray-800\'))) }}">
                                        {{ __(ucfirst($session->status)) }} {{-- Translate status if needed --}}
                                    </span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <a href="{{ route(\'specialist.sessions.show\", $session->id) }}" class="text-indigo-600 hover:text-indigo-900">عرض الجلسة</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">لا يوجد جلسات سابقة لهذا العميل.</p>
        @endif
    </div>

</div>
@endsection

