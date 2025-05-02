@extends(\'layouts.dashboard\')

@section(\'title\', \'تقرير المدفوعات\')

@section(\'content\')
<div class=\"container-fluid\">

    <!-- Page Heading -->
    <h1 class=\"h3 mb-2 text-gray-800\">تقرير المدفوعات</h1>
    <p class=\"mb-4\">عرض إحصائيات و قائمة المدفوعات.</p>

    <!-- Statistics Cards -->
    <div class=\"row\">
        <div class=\"col-xl-4 col-md-6 mb-4\">
            <div class=\"card border-left-primary shadow h-100 py-2\">
                <div class=\"card-body\">
                    <div class=\"row no-gutters align-items-center\">
                        <div class=\"col mr-2\">
                            <div class=\"text-xs font-weight-bold text-primary text-uppercase mb-1\">إجمالي المبالغ</div>
                            <div class=\"h5 mb-0 font-weight-bold text-gray-800\">{{ number_format($totalAmount ?? 0, 2) }} SAR</div>
                        </div>
                        <div class=\"col-auto\">
                            <i class=\"fas fa-dollar-sign fa-2x text-gray-300\"></i>
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
                            <div class=\"text-xs font-weight-bold text-success text-uppercase mb-1\">المبالغ المدفوعة (ناجحة)</div>
                            <div class=\"h5 mb-0 font-weight-bold text-gray-800\">{{ number_format($successfulAmount ?? 0, 2) }} SAR</div>
                        </div>
                        <div class=\"col-auto\">
                            <i class=\"fas fa-check-circle fa-2x text-gray-300\"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=\"col-xl-4 col-md-6 mb-4\">
            <div class=\"card border-left-warning shadow h-100 py-2\">
                <div class=\"card-body\">
                    <div class=\"row no-gutters align-items-center\">
                        <div class=\"col mr-2\">
                            <div class=\"text-xs font-weight-bold text-warning text-uppercase mb-1\">المبالغ قيد الانتظار</div>
                            <div class=\"h5 mb-0 font-weight-bold text-gray-800\">{{ number_format($pendingAmount ?? 0, 2) }} SAR</div>
                        </div>
                        <div class=\"col-auto\">
                            <i class=\"fas fa-hourglass-half fa-2x text-gray-300\"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class=\"card shadow mb-4\">
        <div class=\"card-header py-3\">
            <h6 class=\"m-0 font-weight-bold text-primary\">قائمة المدفوعات</h6>
        </div>
        <div class=\"card-body\">
            <!-- Filter Form -->
            <form method=\"GET\" action=\"{{ route(\'admin.reports.payments\') }}\" class=\"mb-4\">
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
                        <label for=\"payment_method\">طريقة الدفع:</label>
                        <select id=\"payment_method\" name=\"payment_method\" class=\"form-control\">
                            <option value=\"all\">الكل</option>
                            <option value=\"credit_card\" {{ request(\'payment_method\') == \'credit_card\' ? \'selected\' : \'\' }}>بطاقة ائتمانية</option>
                            <option value=\"bank_transfer\" {{ request(\'payment_method\') == \'bank_transfer\' ? \'selected\' : \'\' }}>تحويل بنكي</option>
                            <option value=\"paypal\" {{ request(\'payment_method\') == \'paypal\' ? \'selected\' : \'\' }}>PayPal</option>
                            {{-- Add other payment methods as needed --}}
                        </select>
                    </div>
                    <div class=\"col-md-3 align-self-end\">
                        <button type=\"submit\" class=\"btn btn-primary\">تصفية</button>
                        <a href=\"{{ route(\'admin.reports.payments\') }}\" class=\"btn btn-secondary\">إعادة تعيين</a>
                    </div>
                </div>
            </form>

            <div class=\"table-responsive\">
                <table class=\"table table-bordered admin-table\" width=\"100%\" cellspacing=\"0\">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>الحجز #</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>طريقة الدفع</th>
                            <th>تاريخ الدفع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->user->name ?? ($payment->booking->user->name ?? \'غير متوفر\') }}</td>
                            <td>{{ $payment->booking_id ?? \'لا يوجد\' }}</td>
                            <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency ?? \'SAR\' }}</td>
                            <td>
                                @if($payment->status == \'paid\' || $payment->status == \'completed\')
                                    <span class=\"badge badge-success\">مدفوع</span>
                                @elseif($payment->status == \'pending\')
                                    <span class=\"badge badge-warning\">قيد الانتظار</span>
                                @elseif($payment->status == \'failed\')
                                    <span class=\"badge badge-danger\">فشل</span>
                                @else
                                    <span class=\"badge badge-secondary\">{{ $payment->status }}</span>
                                @endif
                            </td>
                            <td>{{ $payment->payment_method ?? \'غير محدد\' }}</td>
                            <td>{{ $payment->created_at->format(\'Y-m-d H:i\') }}</td>
                            <td>
                                <a href=\"{{ route(\'admin.payments.show\', $payment->id) }}\" class=\"btn btn-sm btn-info\"><i class=\"fas fa-eye\"></i></a>
                                {{-- Add other actions if needed --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan=\"8\" class=\"text-center\">لا يوجد مدفوعات لعرضها.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class=\"d-flex justify-content-center\">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

