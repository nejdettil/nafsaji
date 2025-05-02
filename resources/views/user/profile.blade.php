@extends(\"layouts.dashboard\")

@section(\"title\", \"ملفي الشخصي - نفسجي للتمكين النفسي\")

@section(\"content\")
<div class=\"container-fluid px-4 py-8\">
    <div class=\"flex justify-between items-center mb-6\">
        <h1 class=\"text-3xl font-bold text-gray-800\">ملفي الشخصي</h1>
        <a href=\"{{ route(\"user.profile.edit\") }}\" class=\"bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5 ml-2\" viewBox=\"0 0 20 20\" fill=\"currentColor\">
                <path d=\"M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z\" />
                <path fill-rule=\"evenodd\" d=\"M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z\" clip-rule=\"evenodd\" />
            </svg>
            تعديل الملف الشخصي
        </a>
    </div>

    @if(session(\"success\"))
        <div class=\"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4\" role=\"alert\">
            <span class=\"block sm:inline\">{{ session(\"success\") }}</span>
        </div>
    @endif

    <div class=\"bg-white shadow-lg rounded-lg p-6 md:p-8\">
        <div class=\"flex flex-col md:flex-row items-center\">
            {{-- Avatar --}}
            <div class=\"mb-6 md:mb-0 md:mr-8 text-center\">
                <img src=\"{{ $user->avatar ? Storage::url($user->avatar) : \"https://ui-avatars.com/api/?name=\".urlencode($user->name).\"&color=7F9CF5&background=EBF4FF\" }}\" alt=\"Avatar\" class=\"h-32 w-32 rounded-full mx-auto object-cover border-4 border-purple-200 shadow-sm\">
            </div>

            {{-- User Details --}}
            <div class=\"flex-grow w-full\">
                <div class=\"grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4\">
                    <div>
                        <label class=\"block text-sm font-medium text-gray-500\">الاسم الكامل</label>
                        <p class=\"mt-1 text-lg text-gray-900\">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class=\"block text-sm font-medium text-gray-500\">البريد الإلكتروني</label>
                        <p class=\"mt-1 text-lg text-gray-900\">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class=\"block text-sm font-medium text-gray-500\">رقم الهاتف</label>
                        <p class=\"mt-1 text-lg text-gray-900\">{{ $user->phone ?? \"لم يتم إضافته\" }}</p>
                    </div>
                    <div>
                        <label class=\"block text-sm font-medium text-gray-500\">تاريخ الانضمام</label>
                        <p class=\"mt-1 text-lg text-gray-900\">{{ $user->created_at->format(\"Y-m-d\") }}</p>
                    </div>
                    {{-- Add other profile fields here as needed --}}
                    {{-- Example:
                    <div>
                        <label class=\"block text-sm font-medium text-gray-500\">تاريخ الميلاد</label>
                        <p class=\"mt-1 text-lg text-gray-900\">{{ $user->date_of_birth ? $user->date_of_birth->format(\"Y-m-d\") : \"لم يتم إضافته\" }}</p>
                    </div>
                    --}}
                </div>
            </div>
        </div>

        {{-- Link to Change Password --}}
        <div class=\"mt-8 pt-6 border-t border-gray-200 text-right\">
            <a href=\"{{ route(\"user.profile.change-password\") }}\" class=\"text-sm text-purple-600 hover:text-purple-800 font-medium\">
                تغيير كلمة المرور
            </a>
        </div>
    </div>
</div>
@endsection

