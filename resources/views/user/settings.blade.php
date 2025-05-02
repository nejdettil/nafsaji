@extends(\"layouts.dashboard\")

@section(\"title\", \"الإعدادات - نفسجي للتمكين النفسي\")

@section(\"content\")
<div class=\"container-fluid px-4 py-8\">
    <h1 class=\"text-3xl font-bold mb-6 text-gray-800\">الإعدادات</h1>

    @if(session(\"success\"))
        <div class=\"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4\" role=\"alert\">
            <span class=\"block sm:inline\">{{ session(\"success\") }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class=\"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4\" role=\"alert\">
            <strong class=\"font-bold\">خطأ!</strong>
            <ul class=\"mt-2 list-disc list-inside\">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action=\"{{ route(\"user.settings.update\") }}\" method=\"POST\">
        @csrf
        {{-- No PUT method needed if route is POST --}}

        <div class=\"bg-white shadow-lg rounded-lg p-6 md:p-8 space-y-8\">

            {{-- Notification Settings --}}
            <fieldset>
                <legend class=\"text-lg font-medium text-gray-900 mb-4\">إعدادات الإشعارات</legend>
                <div class=\"space-y-4\">
                    <div class=\"relative flex items-start\">
                        <div class=\"flex items-center h-5\">
                            <input id=\"notification_email\" name=\"notification_email\" type=\"checkbox\" value=\"1\" {{ old(\"notification_email\", $user->settings[\"notifications\"]["email\"] ?? true) ? \"checked\" : \"\" }} class=\"focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded\">
                        </div>
                        <div class=\"mr-3 text-sm\">
                            <label for=\"notification_email\" class=\"font-medium text-gray-700\">إشعارات البريد الإلكتروني</label>
                            <p class=\"text-gray-500\">تلقي تحديثات هامة عبر البريد الإلكتروني.</p>
                        </div>
                    </div>
                    <div class=\"relative flex items-start\">
                        <div class=\"flex items-center h-5\">
                            <input id=\"notification_push\" name=\"notification_push\" type=\"checkbox\" value=\"1\" {{ old(\"notification_push\", $user->settings[\"notifications\"]["push\"] ?? true) ? \"checked\" : \"\" }} class=\"focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded\">
                        </div>
                        <div class=\"mr-3 text-sm\">
                            <label for=\"notification_push\" class=\"font-medium text-gray-700\">إشعارات المتصفح (Push)</label>
                            <p class=\"text-gray-500\">تلقي إشعارات فورية في متصفحك (إذا كان مدعومًا).</p>
                        </div>
                    </div>
                    {{-- Add SMS notifications if implemented --}}
                    {{-- <div class=\"relative flex items-start\">
                        <div class=\"flex items-center h-5\">
                            <input id=\"notification_sms\" name=\"notification_sms\" type=\"checkbox\" value=\"1\" {{ old(\"notification_sms\", $user->settings[\"notifications\"]["sms\"] ?? false) ? \"checked\" : \"\" }} class=\"focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded\">
                        </div>
                        <div class=\"mr-3 text-sm\">
                            <label for=\"notification_sms\" class=\"font-medium text-gray-700\">إشعارات الرسائل القصيرة (SMS)</label>
                            <p class=\"text-gray-500\">تلقي إشعارات عبر الرسائل النصية (قد يتم تطبيق رسوم).</p>
                        </div>
                    </div> --}}
                </div>
            </fieldset>

            {{-- Language and Timezone Settings --}}
            <fieldset>
                <legend class=\"text-lg font-medium text-gray-900 mb-4\">إعدادات اللغة والمنطقة الزمنية</legend>
                <div class=\"grid grid-cols-1 sm:grid-cols-2 gap-6\">
                    <div>
                        <label for=\"language\" class=\"block text-sm font-medium text-gray-700\">لغة الواجهة</label>
                        <select id=\"language\" name=\"language\" class=\"mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md\">
                            <option value=\"ar\" {{ old(\"language\", $user->settings[\"language\"] ?? config(\"app.locale\")) == \"ar\" ? \"selected\" : \"\" }}>العربية</option>
                            <option value=\"en\" {{ old(\"language\", $user->settings[\"language\"] ?? config(\"app.locale\")) == \"en\" ? \"selected\" : \"\" }}>English</option>
                        </select>
                        @error(\"language\")
                            <p class=\"text-red-500 text-xs mt-1\"></p>
                        @enderror
                    </div>
                    <div>
                        <label for=\"timezone\" class=\"block text-sm font-medium text-gray-700\">المنطقة الزمنية</label>
                        {{-- Consider using a library or helper for a comprehensive timezone list --}}
                        <select id=\"timezone\" name=\"timezone\" class=\"mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md\">
                            @php
                                $current_timezone = old(\"timezone\", $user->settings[\"timezone\"] ?? config(\"app.timezone\"));
                                $timezones = timezone_identifiers_list(); // Basic list
                            @endphp
                            @foreach($timezones as $timezone)
                                <option value=\"{{ $timezone }}\" {{ $current_timezone == $timezone ? \"selected\" : \"\" }}>{{ $timezone }}</option>
                            @endforeach
                        </select>
                        @error(\"timezone\")
                            <p class=\"text-red-500 text-xs mt-1\"></p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Security Settings (Placeholder) --}}
            <fieldset>
                <legend class=\"text-lg font-medium text-gray-900 mb-4\">الأمان</legend>
                <div class=\"space-y-4\">
                    <div>
                        <a href=\"{{ route(\"user.profile.change-password\") }}\" class=\"text-sm text-purple-600 hover:text-purple-800 font-medium\">
                            تغيير كلمة المرور
                        </a>
                    </div>
                    {{-- Add link/button for Two-Factor Authentication setup if implemented --}}
                    {{-- <div>
                        <a href=\"#\" class=\"text-sm text-purple-600 hover:text-purple-800 font-medium\">
                            إعداد المصادقة الثنائية (2FA)
                        </a>
                    </div> --}}
                </div>
            </fieldset>

            {{-- Form Actions --}}
            <div class=\"mt-8 pt-6 border-t border-gray-200 flex justify-end\">
                <button type=\"submit\" class=\"inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500\">
                    حفظ الإعدادات
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

