@extends(\"layouts.dashboard\")

@section(\"title\", \"تقييم الجلسة - نفسجي للتمكين النفسي\")

@section(\"content\")
<div class=\"container-fluid px-4 py-8\">
    <h1 class=\"text-3xl font-bold mb-6 text-gray-800\">تقييم الجلسة</h1>

    <div class=\"bg-white shadow-lg rounded-lg p-6 md:p-8\">
        <div class=\"mb-6 pb-4 border-b border-gray-200\">
            <h2 class=\"text-xl font-semibold text-gray-700 mb-2\">تفاصيل الجلسة</h2>
            <p><span class=\"font-medium\">الخدمة:</span> {{ $session->service->name ?? \"N/A\" }}</p>
            <p><span class=\"font-medium\">المختص:</span> {{ $session->specialist->name ?? \"N/A\" }}</p>
            <p><span class=\"font-medium\">التاريخ:</span> {{ \Carbon\Carbon::parse($session->session_date)->format(\"Y-m-d\") }}</p>
        </div>

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

        <form action=\"{{ route(\"user.reviews.store\". $session->id) }}\" method=\"POST\">
            @csrf

            {{-- Rating --}}
            <div class=\"mb-6\">
                <label class=\"block text-sm font-medium text-gray-700 mb-2\">تقييمك للجلسة (من 1 إلى 5 نجوم) <span class=\"text-red-500\">*</span></label>
                <div class=\"flex items-center space-x-1 space-x-reverse rating-stars\">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type=\"radio\" id=\"rating-{{ $i }}\" name=\"rating\" value=\"{{ $i }}\" class=\"hidden\" {{ old(\"rating\") == $i ? \"checked\" : \"\" }} required>
                        <label for=\"rating-{{ $i }}\" class=\"cursor-pointer text-gray-300 hover:text-yellow-400 text-3xl\" title=\"{{ $i }} نجوم\"><i class=\"fas fa-star\"></i></label>
                    @endfor
                </div>
                @error(\"rating\")
                    <p class=\"text-red-500 text-xs mt-1\"></p>
                @enderror
            </div>

            {{-- Comment --}}
            <div class=\"mb-6\">
                <label for=\"comment\" class=\"block text-sm font-medium text-gray-700\">تعليقك (اختياري)</label>
                <textarea name=\"comment\" id=\"comment\" rows=\"4\" class=\"mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm\">{{ old(\"comment\") }}</textarea>
                @error(\"comment\")
                    <p class=\"text-red-500 text-xs mt-1\"></p>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class=\"mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3 space-x-reverse\">
                <a href=\"{{ route(\"user.sessions.show\". $session->id) }}\" class=\"bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500\">
                    إلغاء
                </a>
                <button type=\"submit\" class=\"inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500\">
                    إرسال التقييم
                </button>
            </div>
        </form>
    </div>
</div>

@push(\"styles\")
<style>
    .rating-stars input:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #FBBF24; /* text-yellow-400 */
    }
    .rating-stars input:checked + label {
        color: #FBBF24;
    }
</style>
@endpush

{{-- Include Font Awesome if not already included in layout --}}
{{-- <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\" /> --}}

@endsection

