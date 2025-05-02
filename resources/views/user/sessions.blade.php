@extends(\'layouts.dashboard\')

@section(\'title\", \"جلساتي - نفسجي للتمكين النفسي\")

@section(\'content\")
<div class=\"container-fluid px-4 py-8\">
    <h1 class=\"text-3xl font-bold mb-6 text-gray-800\">جلساتي</h1>

    {{-- Upcoming Sessions --}}
    <div class=\"mb-10\">
        <h2 class=\"text-2xl font-semibold mb-4 text-gray-700\">الجلسات القادمة</h2>
        @if($upcomingSessions->isNotEmpty())
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-6\">
                @foreach($upcomingSessions as $session)
                    <div class=\"bg-white shadow-lg rounded-lg p-5 border-l-4 border-purple-500\">
                        <div class=\"flex justify-between items-start mb-2\">
                            <h3 class=\"text-lg font-semibold text-gray-800\">{{ $session->service->name ?? \'خدمة غير محددة\' }}</h3>
                            <span class=\"text-xs bg-purple-100 text-purple-700 font-medium py-1 px-3 rounded-full\">مجدولة</span>
                        </div>
                        <p class=\"text-sm text-gray-600 mb-3\">مع المختص: {{ $session->specialist->name ?? \'غير محدد\' }}</p>
                        <div class=\"text-sm text-gray-500 space-y-1 mb-4\">
                            <p><i class=\"fas fa-calendar-alt ml-2 text-gray-400\"></i>{{ \Carbon\Carbon::parse($session->session_date)->translatedFormat(\'l, j F Y\') }}</p>
                            <p><i class=\"fas fa-clock ml-2 text-gray-400\"></i>{{ \Carbon\Carbon::parse($session->start_time)->format(\'H:i\") }} - {{ \Carbon\Carbon::parse($session->end_time)->format(\'H:i\") }}</p>
                            <p><i class=\"fas fa-info-circle ml-2 text-gray-400\"></i>نوع الجلسة: {{ __(\'sessions.types.\' . $session->session_type) ?? $session->session_type }}</p> {{-- Assuming translation exists --}}
                        </div>
                        <div class=\"flex justify-end space-x-2 space-x-reverse\">
                            {{-- Add Join/Start button if applicable (e.g., for video sessions shortly before start time) --}}
                            {{-- <a href=\"#\" class=\"text-sm bg-green-500 hover:bg-green-600 text-white font-medium py-1 px-3 rounded-full\">بدء الجلسة</a> --}}
                            <a href=\"{{ route(\'user.sessions.show\". $session->id) }}\" class=\"text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-1 px-3 rounded-full\">التفاصيل</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class=\"bg-white shadow rounded-lg p-6 text-center\">
                <p class=\"text-gray-500\">لا توجد لديك جلسات قادمة مجدولة.</p>
                <a href=\"{{ route(\'booking.create\") }}\" class=\"mt-4 inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded\">
                    حجز جلسة جديدة
                </a>
            </div>
        @endif
    </div>

    {{-- Past Sessions --}}
    <div>
        <h2 class=\"text-2xl font-semibold mb-4 text-gray-700\">الجلسات السابقة</h2>
        @if($pastSessions->isNotEmpty())
            <div class=\"bg-white shadow-lg rounded-lg overflow-hidden\">
                <div class=\"overflow-x-auto\">
                    <table class=\"min-w-full leading-normal\">
                        <thead>
                            <tr>
                                <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">التاريخ</th>
                                <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الخدمة</th>
                                <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">المختص</th>
                                <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الحالة</th>
                                <th class=\"px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pastSessions as $session)
                                <tr>
                                    <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                        <p class=\"text-gray-900 whitespace-no-wrap\">{{ \Carbon\Carbon::parse($session->session_date)->format(\'Y-m-d\') }}</p>
                                        <p class=\"text-gray-600 whitespace-no-wrap text-xs\">{{ \Carbon\Carbon::parse($session->start_time)->format(\'H:i\") }}</p>
                                    </td>
                                    <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                        <p class=\"text-gray-900 whitespace-no-wrap\">{{ $session->service->name ?? \'N/A\' }}</p>
                                    </td>
                                    <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                        <p class=\"text-gray-900 whitespace-no-wrap\">{{ $session->specialist->name ?? \'N/A\' }}</p>
                                    </td>
                                    <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                        @php
                                            $statusClasses = [
                                                \'completed\' => \'bg-green-100 text-green-800\",
                                                \'canceled\' => \'bg-red-100 text-red-800\",
                                                \'no-show\' => \'bg-yellow-100 text-yellow-800\",
                                                \'scheduled\' => \'bg-gray-100 text-gray-800\' // For past scheduled (missed?)
                                            ];
                                            $statusClass = $statusClasses[$session->status] ?? \'bg-gray-100 text-gray-800\";
                                        @endphp
                                        <span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}\">
                                            {{ __(\'sessions.status.\' . $session->status) ?? ucfirst($session->status) }}
                                        </span>
                                    </td>
                                    <td class=\"px-5 py-5 border-b border-gray-200 bg-white text-sm\">
                                        <a href=\"{{ route(\'user.sessions.show\". $session->id) }}\" class=\"text-indigo-600 hover:text-indigo-900 mr-3\">التفاصيل</a>
                                        @if($session->status == \'completed\' && !$session->review)
                                            <a href=\"{{ route(\'user.reviews.create\". $session->id) }}\" class=\"text-yellow-600 hover:text-yellow-900\">تقييم الجلسة</a>
                                        @elseif($session->review)
                                             <span class=\"text-green-600 text-xs\">(تم التقييم <i class=\"fas fa-check\"></i>)</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=\"p-5\">
                    {{ $pastSessions->links() }} {{-- Pagination for past sessions --}}
                </div>
            </div>
        @else
             <div class=\"bg-white shadow rounded-lg p-6 text-center\">
                <p class=\"text-gray-500\">لا توجد لديك جلسات سابقة.</p>
            </div>
        @endif
    </div>
</div>

{{-- Include Font Awesome if not already included in layout --}}
{{-- <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\" /> --}}

@endsection

