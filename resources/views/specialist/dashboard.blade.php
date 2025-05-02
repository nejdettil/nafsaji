@extends(\'layouts.dashboard\') {{-- Assuming layouts.dashboard exists and includes specialist sidebar --}}

@section(\'title\", \"لوحة تحكم المختص - نفسجي للتمكين النفسي\")

@section(\'content\")
<div class=\"container-fluid px-4 py-8\">
    {{-- Header --}}
    <div class=\"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6\">
        <div>
            <h1 class=\"text-3xl font-bold text-gray-800\">لوحة تحكم المختص</h1>
            <p class=\"text-gray-600 mt-1\">مرحباً {{ Auth::user()->name }}، مرحباً بك في لوحة التحكم الخاصة بك.</p>
        </div>
        <div class=\"mt-4 sm:mt-0 flex space-x-2 space-x-reverse\">
            <a href=\"{{ route(\'specialist.sessions.create\") }}\" class=\"bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center\">
                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5 ml-2\" viewBox=\"0 0 20 20\" fill=\"currentColor\">
                    <path fill-rule=\"evenodd\" d=\"M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z\" clip-rule=\"evenodd\" />
                </svg>
                إضافة جلسة
            </a>
            {{-- Link to profile edit might need adjustment based on actual route name --}}
            <a href=\"{{ route(\'specialist.profile\") }}\" class=\"bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 px-4 rounded inline-flex items-center\">
                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5 ml-2\" viewBox=\"0 0 20 20\" fill=\"currentColor\">
                    <path d=\"M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z\" />
                    <path fill-rule=\"evenodd\" d=\"M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z\" clip-rule=\"evenodd\" />
                </svg>
                الملف الشخصي
            </a>
        </div>
    </div>

    {{-- Stats Overview --}}
    <div class=\"grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8\">
        <div class=\"bg-white shadow-lg rounded-lg p-5 flex items-center\">
            <div class=\"bg-blue-100 rounded-full p-3 mr-4\">
                <svg class=\"w-6 h-6 text-blue-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\"></path></svg>
            </div>
            <div>
                <p class=\"text-sm text-gray-500\">الحجوزات القادمة</p>
                <p class=\"text-2xl font-bold text-gray-800\">{{ $upcomingBookings->count() }}</p>
            </div>
        </div>
        <div class=\"bg-white shadow-lg rounded-lg p-5 flex items-center\">
            <div class=\"bg-green-100 rounded-full p-3 mr-4\">
                <svg class=\"w-6 h-6 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\"></path></svg>
            </div>
            <div>
                <p class=\"text-sm text-gray-500\">العملاء النشطين</p>
                <p class=\"text-2xl font-bold text-gray-800\">{{ $activeClients }}</p>
            </div>
        </div>
        <div class=\"bg-white shadow-lg rounded-lg p-5 flex items-center\">
            <div class=\"bg-yellow-100 rounded-full p-3 mr-4\">
                <svg class=\"w-6 h-6 text-yellow-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z\"></path></svg>
            </div>
            <div>
                <p class=\"text-sm text-gray-500\">متوسط التقييم</p>
                <p class=\"text-2xl font-bold text-gray-800\">{{ number_format($averageRating, 1) }} <span class=\"text-yellow-500 text-lg\">★</span></p>
            </div>
        </div>
        <div class=\"bg-white shadow-lg rounded-lg p-5 flex items-center\">
            <div class=\"bg-purple-100 rounded-full p-3 mr-4\">
                <svg class=\"w-6 h-6 text-purple-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0c-1.11 0-2.08-.402-2.599-1M12 16v1m-6-3a2 2 0 012-2h8a2 2 0 012 2v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z\"></path></svg>
            </div>
            <div>
                <p class=\"text-sm text-gray-500\">دخل هذا الشهر</p>
                <p class=\"text-2xl font-bold text-gray-800\">{{ number_format($monthlyIncome, 2) }} <span class=\"text-xs text-gray-500\">{{ config(\'app.currency\') ?? \'USD\') }}</span></p>
            </div>
        </div>
    </div>

    <div class=\"grid grid-cols-1 lg:grid-cols-3 gap-8\">
        {{-- Upcoming Sessions --}}
        <div class=\"lg:col-span-2 bg-white shadow-lg rounded-lg overflow-hidden\">
            <div class=\"p-6 flex justify-between items-center border-b border-gray-200\">
                <h3 class=\"text-xl font-semibold text-gray-700\">الجلسات القادمة</h3>
                <a href=\"{{ route(\'specialist.sessions.index\') }}\" class=\"text-sm text-purple-600 hover:text-purple-800 font-medium\">عرض الكل</a>
            </div>
            <div class=\"p-6\">
                @if($sessions->isNotEmpty())
                    <div class=\"overflow-x-auto\">
                        <table class=\"min-w-full leading-normal\">
                            <thead>
                                <tr>
                                    <th class=\"px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">العميل</th>
                                    <th class=\"px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الخدمة</th>
                                    <th class=\"px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">التاريخ والوقت</th>
                                    <th class=\"px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider\">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                    <tr>
                                        <td class=\"px-4 py-4 border-b border-gray-200 bg-white text-sm\">
                                            <p class=\"text-gray-900 whitespace-no-wrap\">{{ $session->user->name ?? \'غير متوفر\' }}</p>
                                        </td>
                                        <td class=\"px-4 py-4 border-b border-gray-200 bg-white text-sm\">
                                            <p class=\"text-gray-900 whitespace-no-wrap\">{{ $session->service->name ?? \'غير متوفر\' }}</p>
                                        </td>
                                        <td class=\"px-4 py-4 border-b border-gray-200 bg-white text-sm\">
                                            <p class=\"text-gray-900 whitespace-no-wrap\">{{ \Carbon\Carbon::parse($session->session_date)->format(\'Y-m-d\') }}</p>
                                            <p class=\"text-gray-600 whitespace-no-wrap text-xs\">{{ \Carbon\Carbon::parse($session->start_time)->format(\'H:i\') }} - {{ \Carbon\Carbon::parse($session->end_time)->format(\'H:i\') }}</p>
                                        </td>
                                        <td class=\"px-4 py-4 border-b border-gray-200 bg-white text-sm\">
                                            <a href=\"{{ route(\'specialist.sessions.show\". $session->id) }}\" class=\"text-indigo-600 hover:text-indigo-900\">التفاصيل</a>
                                            {{-- Add other actions like start/cancel based on status --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class=\"text-center py-10\">
                        <svg class=\"mx-auto h-12 w-12 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\" aria-hidden=\"true\">
                            <path vector-effect=\"non-scaling-stroke\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\" />
                        </svg>
                        <h3 class=\"mt-2 text-sm font-medium text-gray-900\">لا توجد جلسات قادمة</h3>
                        <p class=\"mt-1 text-sm text-gray-500\">ليس لديك أي جلسات مجدولة في الوقت الحالي.</p>
                        {{-- Optional: Add link to availability settings --}}
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Links & Recent Activity --}}
        <div class=\"space-y-8\">
            {{-- Quick Links --}}
            <div class=\"bg-white shadow-lg rounded-lg p-6\">
                <h3 class=\"text-xl font-semibold text-gray-700 mb-4\">روابط سريعة</h3>
                <div class=\"space-y-3\">
                    <a href=\"{{ route(\'specialist.schedule\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-calendar-alt ml-2\"></i>إدارة الجدول الزمني والتوفر</a>
                    <a href=\"{{ route(\'specialist.clients.index\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-users ml-2\"></i>إدارة العملاء</a>
                    <a href=\"{{ route(\'specialist.reports.index\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-chart-bar ml-2\"></i>عرض التقارير العامة</a>
                    <a href=\"{{ route(\'specialist.reports.financial\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-file-invoice-dollar ml-2\"></i>عرض التقارير المالية</a>
                    <a href=\"{{ route(\'specialist.reviews\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-star ml-2\"></i>عرض التقييمات</a> {{-- Assuming specialist.reviews route exists --}}
                </div>
            </div>

            {{-- Recent Reviews --}}
            <div class=\"bg-white shadow-lg rounded-lg p-6\">
                <div class=\"flex justify-between items-center mb-4\">
                    <h3 class=\"text-xl font-semibold text-gray-700\">آخر التقييمات</h3>
                    <a href=\"{{ route(\'specialist.reviews\") }}\" class=\"text-sm text-purple-600 hover:text-purple-800 font-medium\">عرض الكل</a> {{-- Assuming specialist.reviews route exists --}}
                </div>
                @if($reviews->isNotEmpty())
                    <ul class=\"space-y-4\">
                        @foreach($reviews as $review)
                            <li class=\"border-b border-gray-200 pb-3 last:border-b-0 last:pb-0\">
                                <div class=\"flex items-center justify-between mb-1\">
                                    <span class=\"text-sm font-medium text-gray-800\">{{ $review->user->name ?? \'مستخدم\' }}</span>
                                    <div class=\"text-yellow-500 text-xs\">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class=\"{{ $i <= $review->rating ? \'fas\': \'far\' }} fa-star\"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class=\"text-sm text-gray-600\">{{ Str::limit($review->comment, 100) }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class=\"text-sm text-gray-500\">لا توجد تقييمات جديدة.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Include Font Awesome if not already included in layout --}}
{{-- <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\" /> --}}

@endsection

