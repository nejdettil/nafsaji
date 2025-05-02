@extends(\'layouts.dashboard\') {{-- Assuming layouts.dashboard exists and includes user sidebar --}}

@section(\'title\', \"لوحة تحكم المستخدم - نفسجي للتمكين النفسي\")

@section(\'content\")
<div class=\"container-fluid px-4 py-8\">
    {{-- Header --}}
    <div class=\"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6\">
        <div>
            <h1 class=\"text-3xl font-bold text-gray-800\">لوحة التحكم</h1>
            <p class=\"text-gray-600 mt-1\">مرحباً {{ $user->name }}، أهلاً بك مجدداً.</p>
        </div>
        <div class=\"mt-4 sm:mt-0 flex space-x-2 space-x-reverse\">
            <a href=\"{{ route(\'booking.create\") }}\" class=\"bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center\">
                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5 ml-2\" viewBox=\"0 0 20 20\" fill=\"currentColor\">
                    <path fill-rule=\"evenodd\" d=\"M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z\" clip-rule=\"evenodd\" />
                </svg>
                حجز جلسة جديدة
            </a>
             <a href=\"{{ route(\'user.notifications\") }}\" class=\"relative bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 px-4 rounded inline-flex items-center\">
                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5 ml-2\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                  <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9\" />
                </svg>
                الإشعارات
                @if($unreadNotificationsCount > 0)
                    <span class=\"absolute top-0 right-0 block h-4 w-4 transform -translate-y-1/2 translate-x-1/2 rounded-full text-white bg-red-500 text-xs flex items-center justify-center\">{{ $unreadNotificationsCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Stats Overview --}}
    <div class=\"grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8\">
        <div class=\"bg-white shadow-lg rounded-lg p-5 flex items-center\">
            <div class=\"bg-blue-100 rounded-full p-3 mr-4\">
                <svg class=\"w-6 h-6 text-blue-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\"></path></svg>
            </div>
            <div>
                <p class=\"text-sm text-gray-500\">الجلسات القادمة</p>
                <p class=\"text-2xl font-bold text-gray-800\">{{ $upcomingSessions->count() }}</p>
            </div>
        </div>
        <div class=\"bg-white shadow-lg rounded-lg p-5 flex items-center\">
            <div class=\"bg-green-100 rounded-full p-3 mr-4\">
                <svg class=\"w-6 h-6 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\"></path></svg>
            </div>
            <div>
                <p class=\"text-sm text-gray-500\">الحجوزات المكتملة</p>
                <p class=\"text-2xl font-bold text-gray-800\">{{ $completedBookings }}</p>
            </div>
        </div>
        <div class=\"bg-white shadow-lg rounded-lg p-5 flex items-center\">
            <div class=\"bg-purple-100 rounded-full p-3 mr-4\">
                <svg class=\"w-6 h-6 text-purple-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z\"></path></svg>
            </div>
            <div>
                <p class=\"text-sm text-gray-500\">إجمالي الحجوزات</p>
                <p class=\"text-2xl font-bold text-gray-800\">{{ $totalBookings }}</p>
            </div>
        </div>
    </div>

    <div class=\"grid grid-cols-1 lg:grid-cols-3 gap-8\">
        {{-- Upcoming Sessions List --}}
        <div class=\"lg:col-span-2 bg-white shadow-lg rounded-lg overflow-hidden\">
            <div class=\"p-6 flex justify-between items-center border-b border-gray-200\">
                <h3 class=\"text-xl font-semibold text-gray-700\">جلساتك القادمة</h3>
                <a href=\"{{ route(\'user.sessions\') }}\" class=\"text-sm text-purple-600 hover:text-purple-800 font-medium\">عرض كل الجلسات</a>
            </div>
            <div class=\"p-6\">
                @if($upcomingSessions->isNotEmpty())
                    <ul class=\"space-y-4\">
                        @foreach($upcomingSessions as $session)
                            <li class=\"flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200\">
                                <div class=\"mb-3 sm:mb-0 sm:mr-4 flex-grow\">
                                    <p class=\"font-semibold text-gray-800\">{{ $session->service->name ?? \'خدمة غير محددة\' }}</p>
                                    <p class=\"text-sm text-gray-600\">مع المختص: {{ $session->specialist->name ?? \'غير محدد\' }}</p>
                                    <p class=\"text-sm text-gray-500 mt-1\">
                                        <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-4 w-4 inline-block mr-1 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\" /></svg>
                                        {{ \Carbon\Carbon::parse($session->session_date)->translatedFormat(\'l, j F Y\") }} {{-- Display formatted date --}}
                                    </p>
                                    <p class=\"text-sm text-gray-500\">
                                        <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-4 w-4 inline-block mr-1 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\" /></svg>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format(\'H:i\") }} - {{ \Carbon\Carbon::parse($session->end_time)->format(\'H:i\") }} ({{ \Carbon\Carbon::parse($session->start_time)->diffForHumans(null, true) }} من الآن)
                                    </p>
                                </div>
                                <a href=\"{{ route(\'user.sessions.show\', $session->id) }}\" class=\"text-sm bg-purple-100 text-purple-700 hover:bg-purple-200 font-medium py-1 px-3 rounded-full whitespace-nowrap\">
                                    عرض التفاصيل
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class=\"text-center py-10\">
                        <svg class=\"mx-auto h-12 w-12 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\" aria-hidden=\"true\">
                            <path vector-effect=\"non-scaling-stroke\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\" />
                        </svg>
                        <h3 class=\"mt-2 text-sm font-medium text-gray-900\">لا توجد جلسات قادمة</h3>
                        <p class=\"mt-1 text-sm text-gray-500\">ليس لديك أي جلسات مجدولة في الوقت الحالي.</p>
                        <div class=\"mt-6\">
                            <a href=\"{{ route(\'booking.create\") }}\" class=\"inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500\">
                                حجز جلسة جديدة
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Payments & Quick Links --}}
        <div class=\"space-y-8\">
            {{-- Recent Payments --}}
            <div class=\"bg-white shadow-lg rounded-lg p-6\">
                <div class=\"flex justify-between items-center mb-4\">
                    <h3 class=\"text-xl font-semibold text-gray-700\">آخر المدفوعات</h3>
                    <a href=\"{{ route(\'user.payments\') }}\" class=\"text-sm text-purple-600 hover:text-purple-800 font-medium\">عرض الكل</a>
                </div>
                @if($recentPayments->isNotEmpty())
                    <ul class=\"space-y-3\">
                        @foreach($recentPayments as $payment)
                            <li class=\"flex items-center justify-between border-b border-gray-200 pb-2 last:border-b-0 last:pb-0\">
                                <div>
                                    <p class=\"text-sm font-medium text-gray-800\">{{ $payment->booking->service->name ?? \'دفعة\' }}</p>
                                    <p class=\"text-xs text-gray-500\">{{ $payment->created_at->format(\'Y-m-d\") }}</p>
                                </div>
                                <span class=\"text-sm font-semibold {{ $payment->status == \'completed\' ? \'text-green-600\' : \'text-yellow-600\' }}\">
                                    {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class=\"text-sm text-gray-500\">لا توجد مدفوعات حديثة.</p>
                @endif
            </div>

            {{-- Quick Links --}}
            <div class=\"bg-white shadow-lg rounded-lg p-6\">
                <h3 class=\"text-xl font-semibold text-gray-700 mb-4\">روابط سريعة</h3>
                <div class=\"space-y-3\">
                    <a href=\"{{ route(\'user.bookings\') }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-calendar-check ml-2\"></i>إدارة حجوزاتي</a>
                    <a href=\"{{ route(\'user.sessions\') }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-video ml-2\"></i>جلساتي القادمة والسابقة</a>
                    <a href=\"{{ route(\'user.profile\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-user-circle ml-2\"></i>ملفي الشخصي</a>
                    <a href=\"{{ route(\'user.settings\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-cog ml-2\"></i>الإعدادات</a>
                    <a href=\"{{ route(\'user.paymentMethods\") }}\" class=\"block text-purple-600 hover:text-purple-800 font-medium\"><i class=\"fas fa-credit-card ml-2\"></i>طرق الدفع</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Font Awesome if not already included in layout --}}
{{-- <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\" /> --}}

@endsection

