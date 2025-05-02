@extends(\"layouts.dashboard\")

@section(\"title\", \"تعديل الملف الشخصي - نفسجي للتمكين النفسي\")

@section(\"content\")
<div class=\"container-fluid px-4 py-8\">
    <h1 class=\"text-3xl font-bold mb-6 text-gray-800\">تعديل الملف الشخصي</h1>

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

    <form action=\"{{ route(\"user.profile.update\") }}\" method=\"POST\" enctype=\"multipart/form-data\">
        @csrf
        @method(\"PUT\")

        <div class=\"bg-white shadow-lg rounded-lg p-6 md:p-8\">
            <div class=\"flex flex-col md:flex-row items-start\">
                {{-- Avatar Upload --}}
                <div class=\"mb-6 md:mb-0 md:mr-8 text-center flex-shrink-0\">
                    <img id=\"avatarPreview\" src=\"{{ $user->avatar ? Storage::url($user->avatar) : \"https://ui-avatars.com/api/?name=\".urlencode($user->name).\"&color=7F9CF5&background=EBF4FF\" }}\" alt=\"Avatar\" class=\"h-32 w-32 rounded-full mx-auto object-cover border-4 border-purple-200 shadow-sm mb-4\">
                    <label for=\"avatar\" class=\"cursor-pointer bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded text-sm\"><i class=\"fas fa-upload ml-1\"></i> تغيير الصورة</label>
                    <input type=\"file\" name=\"avatar\" id=\"avatar\" class=\"hidden\" accept=\"image/*\" onchange=\"previewAvatar(event)\">
                    @error(\"avatar\")
                        <p class=\"text-red-500 text-xs mt-1\"></p>
                    @enderror
                </div>

                {{-- User Details Form --}}
                <div class=\"flex-grow w-full\">
                    <div class=\"grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4\">
                        {{-- Name --}}
                        <div>
                            <label for=\"name\" class=\"block text-sm font-medium text-gray-700\">الاسم الكامل <span class=\"text-red-500\">*</span></label>
                            <input type=\"text\" name=\"name\" id=\"name\" value=\"{{ old(\"name\", $user->name) }}\" required class=\"mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm\">
                            @error(\"name\")
                                <p class=\"text-red-500 text-xs mt-1\"></p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for=\"email\" class=\"block text-sm font-medium text-gray-700\">البريد الإلكتروني <span class=\"text-red-500\">*</span></label>
                            <input type=\"email\" name=\"email\" id=\"email\" value=\"{{ old(\"email\", $user->email) }}\" required class=\"mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm\">
                            @error(\"email\")
                                <p class=\"text-red-500 text-xs mt-1\"></p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for=\"phone\" class=\"block text-sm font-medium text-gray-700\">رقم الهاتف</label>
                            <input type=\"tel\" name=\"phone\" id=\"phone\" value=\"{{ old(\"phone\", $user->phone) }}\" class=\"mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm\">
                            @error(\"phone\")
                                <p class=\"text-red-500 text-xs mt-1\"></p>
                            @enderror
                        </div>

                        {{-- Add other editable profile fields here --}}
                        {{-- Example:
                        <div>
                            <label for=\"date_of_birth\" class=\"block text-sm font-medium text-gray-700\">تاريخ الميلاد</label>
                            <input type=\"date\" name=\"date_of_birth\" id=\"date_of_birth\" value=\"{{ old(\"date_of_birth\", $user->date_of_birth ? $user->date_of_birth->format(\"Y-m-d\") : \"\") }}\" class=\"mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm\">
                            @error(\"date_of_birth\")
                                <p class=\"text-red-500 text-xs mt-1\"></p>
                            @enderror
                        </div>
                        --}}
                    </div>

                    {{-- Form Actions --}}
                    <div class=\"mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3 space-x-reverse\">
                        <a href=\"{{ route(\"user.profile\") }}\" class=\"bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500\">
                            إلغاء
                        </a>
                        <button type=\"submit\" class=\"inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500\">
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push(\"scripts\")
<script>
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById(\"avatarPreview\");
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    };
</script>
@endpush

{{-- Include Font Awesome if not already included in layout --}}
{{-- <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\" /> --}}

@endsection

