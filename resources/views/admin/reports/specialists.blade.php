@extends(\'layouts.dashboard\')

@section(\'title\', \'تقرير المختصين\')

@section(\'content\')
<div class=\"container-fluid\">

    <!-- Page Heading -->
    <h1 class=\"h3 mb-2 text-gray-800\">تقرير المختصين</h1>
    <p class=\"mb-4\">عرض إحصائيات و قائمة المختصين.</p>

    <!-- Statistics Cards -->
    <div class=\"row\">
        <div class=\"col-xl-4 col-md-6 mb-4\">
            <div class=\"card border-left-primary shadow h-100 py-2\">
                <div class=\"card-body\">
                    <div class=\"row no-gutters align-items-center\">
                        <div class=\"col mr-2\">
                            <div class=\"text-xs font-weight-bold text-primary text-uppercase mb-1\">إجمالي المختصين</div>
                            <div class=\"h5 mb-0 font-weight-bold text-gray-800\">{{ $totalSpecialists ?? 0 }}</div>
                        </div>
                        <div class=\"col-auto\">
                            <i class=\"fas fa-user-md fa-2x text-gray-300\"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=\"col-xl-4 col-md-6 mb-4\">
            <div class=\"card border-left-success shadow h-100 py-2\">
                <div class=\"card-body\">
                    <div class=\"row no-gutters align-items-center\">
                        <div class=\"col mr-2\">
                            <div class=\"text-xs font-weight-bold text-success text-uppercase mb-1\">المختصون النشطون</div>
                            <div class=\"h5 mb-0 font-weight-bold text-gray-800\">{{ $activeSpecialists ?? 0 }}</div>
                        </div>
                        <div class=\"col-auto\">
                            <i class=\"fas fa-user-check fa-2x text-gray-300\"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=\"col-xl-4 col-md-6 mb-4\">
            <div class=\"card border-left-info shadow h-100 py-2\">
                <div class=\"card-body\">
                    <div class=\"row no-gutters align-items-center\">
                        <div class=\"col mr-2\">
                            <div class=\"text-xs font-weight-bold text-info text-uppercase mb-1\">المختصون الجدد (هذا الشهر)</div>
                            <div class=\"h5 mb-0 font-weight-bold text-gray-800\">{{ $newSpecialistsThisMonth ?? 0 }}</div>
                        </div>
                        <div class=\"col-auto\">
                            <i class=\"fas fa-user-plus fa-2x text-gray-300\"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Specialists Table -->
    <div class=\"card shadow mb-4\">
        <div class=\"card-header py-3\">
            <h6 class=\"m-0 font-weight-bold text-primary\">قائمة المختصين</h6>
        </div>
        <div class=\"card-body\">
            <!-- Filter Form -->
            <form method=\"GET\" action=\"{{ route(\'admin.reports.specialists\') }}\" class=\"mb-4\">
                <div class=\"row\">
                    <div class=\"col-md-4\">
                        <label for=\"date_from\">من تاريخ:</label>
                        <input type=\"date\" id=\"date_from\" name=\"date_from\" class=\"form-control\" value=\"{{ request(\'date_from\') }}\">
                    </div>
                    <div class=\"col-md-4\">
                        <label for=\"date_to\">إلى تاريخ:</label>
                        <input type=\"date\" id=\"date_to\" name=\"date_to\" class=\"form-control\" value=\"{{ request(\'date_to\') }}\">
                    </div>
                    <div class=\"col-md-4 align-self-end\">
                        <button type=\"submit\" class=\"btn btn-primary\">تصفية</button>
                        <a href=\"{{ route(\'admin.reports.specialists\') }}\" class=\"btn btn-secondary\">إعادة تعيين</a>
                    </div>
                </div>
            </form>

            <div class=\"table-responsive\">
                <table class=\"table table-bordered admin-table\" width=\"100%\" cellspacing=\"0\">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>التخصص</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th>عدد الحجوزات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($specialists as $specialist)
                        <tr>
                            <td>{{ $specialist->id }}</td>
                            <td>{{ $specialist->user->name ?? \'غير متوفر\' }}</td>
                            <td>{{ $specialist->user->email ?? \'غير متوفر\' }}</td>
                            <td>{{ $specialist->specialization ?? \'غير محدد\' }}</td>
                            <td>
                                @if($specialist->user && $specialist->user->status == \'active\')
                                    <span class=\"badge badge-success\">نشط</span>
                                @else
                                    <span class=\"badge badge-secondary\">{{ $specialist->user->status ?? \'غير معروف\' }}</span>
                                @endif
                            </td>
                            <td>{{ $specialist->created_at->format(\'Y-m-d\') }}</td>
                            <td>{{ $specialist->bookings_count ?? 0 }}</td>
                            <td>
                                <a href=\"#\" class=\"btn btn-sm btn-info\"><i class=\"fas fa-eye\"></i></a>
                                {{-- Add other actions like edit/delete if needed --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan=\"8\" class=\"text-center\">لا يوجد مختصون لعرضهم.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class=\"d-flex justify-content-center\">
                {{ $specialists->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

