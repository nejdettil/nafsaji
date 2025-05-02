@extends(\'layouts.dashboard\')

@section(\'title\', \'تقرير الحجوزات\')

@section(\'content\')
<div class=\"container-fluid\">

    <!-- Page Heading -->
    <h1 class=\"h3 mb-2 text-gray-800\">تقرير الحجوزات</h1>
    <p class=\"mb-4\">عرض قائمة الحجوزات مع خيارات التصفية.</p>

    <!-- Bookings Table -->
    <div class=\"card shadow mb-4\">
        <div class=\"card-header py-3\">
            <h6 class=\"m-0 font-weight-bold text-primary\">قائمة الحجوزات</h6>
        </div>
        <div class=\"card-body\">
            <!-- Filter Form -->
            <form method=\"GET\" action=\"{{ route(\'admin.reports.bookings\') }}\" class=\"mb-4\">
                <div class=\"row\">
                    <div class=\"col-md-3\">
                        <label for=\"date_from\">من تاريخ:</label>
                        <input type=\"date\" id=\"date_from\" name=\"date_from\" class=\"form-control\" value=\"{{ request(\'date_from\') }}\">
                    </div>
                    <div class=\"col-md-3\">
                        <label for=\"date_to\">إلى تاريخ:</label>
                        <input type=\"date\" id=\"date_to\" name=\"date_to\" class=\"form-control\" value=\"{{ request(\'date_to\') }}\">
                    </div>
                    <div class=\"col-md-3\">
                        <label for=\"specialist_id\">المختص:</label>
                        <select id=\"specialist_id\" name=\"specialist_id\" class=\"form-control admin-select\">
                            <option value=\"all\">الكل</option>
                            @foreach($specialists as $specialist)
                                <option value=\"{{ $specialist->id }}\" {{ request(\'specialist_id\') == $specialist->id ? \'selected\' : \'\' }}>{{ $specialist->user->name ?? \'غير متوفر\' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class=\"col-md-3 align-self-end\">
                        <button type=\"submit\" class=\"btn btn-primary\">تصفية</button>
                        <a href=\"{{ route(\'admin.reports.bookings\') }}\" class=\"btn btn-secondary\">إعادة تعيين</a>
                    </div>
                </div>
            </form>

            <div class=\"table-responsive\">
                <table class=\"table table-bordered admin-table\" width=\"100%\" cellspacing=\"0\">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>المختص</th>
                            <th>الخدمة</th>
                            <th>تاريخ الحجز</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id }}</td>
                            <td>{{ $booking->user->name ?? \'غير متوفر\' }}</td>
                            <td>{{ $booking->specialist->user->name ?? \'غير متوفر\' }}</td>
                            <td>{{ $booking->service->name ?? \'غير متوفر\' }}</td>
                            <td>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format(\'Y-m-d H:i\') : \'غير محدد\' }}</td>
                            <td>
                                @if($booking->status == \'confirmed\')
                                    <span class=\"badge badge-success\">مؤكد</span>
                                @elseif($booking->status == \'pending\')
                                    <span class=\"badge badge-warning\">قيد الانتظار</span>
                                @elseif($booking->status == \'cancelled\')
                                    <span class=\"badge badge-danger\">ملغى</span>
                                @else
                                    <span class=\"badge badge-secondary\">{{ $booking->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href=\"{{ route(\'admin.bookings.show\', $booking->id) }}\" class=\"btn btn-sm btn-info\"><i class=\"fas fa-eye\"></i></a>
                                {{-- Add other actions like edit/delete if needed --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan=\"7\" class=\"text-center\">لا يوجد حجوزات لعرضها.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class=\"d-flex justify-content-center\">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

