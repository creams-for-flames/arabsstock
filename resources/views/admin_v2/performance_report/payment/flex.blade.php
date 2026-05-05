@extends('admin_v2.layout.app')

@section('content')

    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-line-chart"></i>
                </span>
                    <h3 class="kt-portlet__head-title">
                        المبيعات
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <br><br>
                <div class="col-lg-12 col-xl-4=12 order-lg-1 order-xl-1">
                    <!--begin:: Widgets/Activity-->
                    <div
                        class=" kt-portlet--fit kt-portlet--head-lg kt-portlet--head-overlay kt-portlet--skin-solid kt-portlet--height-fluid">
                        <div class="kt-widget17">
                            <div class="kt-widget17__stats display-intial">
                                <div class="kt-widget17__items">
                                    <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">

                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) " x="11" y="1" width="2" height="14" rx="1"/>
        <path d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) "/>
    </g>
</svg></span>
                                        <span class="kt-widget17__subtitle">

                                        التحميلات
                                    </span>
                                        <span class="kt-widget17__desc">
                                        {{$downloads}} تحميل
                                    </span>
                                    </div>
                                    <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">

                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <rect fill="#000000" opacity="0.3" x="11.5" y="2" width="2" height="4" rx="1"/>
        <rect fill="#000000" opacity="0.3" x="11.5" y="16" width="2" height="5" rx="1"/>
        <path d="M15.493,8.044 C15.2143319,7.68933156 14.8501689,7.40750104 14.4005,7.1985 C13.9508311,6.98949895 13.5170021,6.885 13.099,6.885 C12.8836656,6.885 12.6651678,6.90399981 12.4435,6.942 C12.2218322,6.98000019 12.0223342,7.05283279 11.845,7.1605 C11.6676658,7.2681672 11.5188339,7.40749914 11.3985,7.5785 C11.2781661,7.74950085 11.218,7.96799867 11.218,8.234 C11.218,8.46200114 11.2654995,8.65199924 11.3605,8.804 C11.4555005,8.95600076 11.5948324,9.08899943 11.7785,9.203 C11.9621676,9.31700057 12.1806654,9.42149952 12.434,9.5165 C12.6873346,9.61150047 12.9723317,9.70966616 13.289,9.811 C13.7450023,9.96300076 14.2199975,10.1308324 14.714,10.3145 C15.2080025,10.4981676 15.6576646,10.7419985 16.063,11.046 C16.4683354,11.3500015 16.8039987,11.7268311 17.07,12.1765 C17.3360013,12.6261689 17.469,13.1866633 17.469,13.858 C17.469,14.6306705 17.3265014,15.2988305 17.0415,15.8625 C16.7564986,16.4261695 16.3733357,16.8916648 15.892,17.259 C15.4106643,17.6263352 14.8596698,17.8986658 14.239,18.076 C13.6183302,18.2533342 12.97867,18.342 12.32,18.342 C11.3573285,18.342 10.4263378,18.1741683 9.527,17.8385 C8.62766217,17.5028317 7.88033631,17.0246698 7.285,16.404 L9.413,14.238 C9.74233498,14.6433354 10.176164,14.9821653 10.7145,15.2545 C11.252836,15.5268347 11.7879973,15.663 12.32,15.663 C12.5606679,15.663 12.7949989,15.6376669 13.023,15.587 C13.2510011,15.5363331 13.4504991,15.4540006 13.6215,15.34 C13.7925009,15.2259994 13.9286662,15.0740009 14.03,14.884 C14.1313338,14.693999 14.182,14.4660013 14.182,14.2 C14.182,13.9466654 14.1186673,13.7313342 13.992,13.554 C13.8653327,13.3766658 13.6848345,13.2151674 13.4505,13.0695 C13.2161655,12.9238326 12.9248351,12.7908339 12.5765,12.6705 C12.2281649,12.5501661 11.8323355,12.420334 11.389,12.281 C10.9583312,12.141666 10.5371687,11.9770009 10.1255,11.787 C9.71383127,11.596999 9.34650161,11.3531682 9.0235,11.0555 C8.70049838,10.7578318 8.44083431,10.3968355 8.2445,9.9725 C8.04816568,9.54816454 7.95,9.03200304 7.95,8.424 C7.95,7.67666293 8.10199848,7.03700266 8.406,6.505 C8.71000152,5.97299734 9.10899753,5.53600171 9.603,5.194 C10.0970025,4.85199829 10.6543302,4.60183412 11.275,4.4435 C11.8956698,4.28516587 12.5226635,4.206 13.156,4.206 C13.9160038,4.206 14.6918294,4.34533194 15.4835,4.624 C16.2751706,4.90266806 16.9686637,5.31433061 17.564,5.859 L15.493,8.044 Z" fill="#000000"/>
    </g>
</svg>
                                    </span>
                                        <span class="kt-widget17__subtitle">
                                        المبلغ المحصل
                                    </span>
                                        <span class="kt-widget17__desc">
                                        {{$total_amount}} $
                                    </span>
                                    </div>
                                </div>
                                <div class="kt-widget17__items">
                                    <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                             viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--brand">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path
                                                    d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z"
                                                    fill="#000000"></path>
                                                <rect fill="#000000" opacity="0.3"
                                                      transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) "
                                                      x="16.3255682" y="2.94551858" width="3" height="18" rx="1"></rect>
                                            </g>
                                        </svg>
                                        </span>
                                        <span class="kt-widget17__subtitle">
                                        الاشتراكات
                                    </span>
                                        <span class="kt-widget17__desc">
                                        {{$total_subscription}} اشتراك
                                    </span>
                                    </div>
                                    <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">
                                        <i class="fa fa-users"></i>
                                        </span>
                                        <span class="kt-widget17__subtitle">
                                        الفِرق
                                    </span>
                                        <span class="kt-widget17__desc">
                                        {{ \App\Models\Team::count() }} فريق
                                    </span>
                                    </div>
                                </div>
                                <div class="kt-widget17__items">
                                    <div class="kt-widget17__item">
                                        <span class="kt-widget17__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <polygon points="0 0 24 0 24 24 0 24"/>
        <path d="M6.26193932,17.6476484 C5.90425297,18.0684559 5.27315905,18.1196257 4.85235158,17.7619393 C4.43154411,17.404253 4.38037434,16.773159 4.73806068,16.3523516 L13.2380607,6.35235158 C13.6013618,5.92493855 14.2451015,5.87991302 14.6643638,6.25259068 L19.1643638,10.2525907 C19.5771466,10.6195087 19.6143273,11.2515811 19.2474093,11.6643638 C18.8804913,12.0771466 18.2484189,12.1143273 17.8356362,11.7474093 L14.0997854,8.42665306 L6.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(11.999995, 12.000002) rotate(-180.000000) translate(-11.999995, -12.000002) "/>
    </g>
</svg>
                                        </span>
                                        <span class="kt-widget17__subtitle">
                                            الاشتراكات الفعالة
                                        </span>
                                        <span class="kt-widget17__desc">
                                            {{ $active_subscriptions }} اشتراك
                                        </span>
                                    </div>
                                    <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect x="0" y="7" width="16" height="2" rx="1"/>
            <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>
        </g>
    </g>
</svg>
                                        </span>
                                        <span class="kt-widget17__subtitle">
                                        الاشتراكات المنتهية
                                    </span>
                                        <span class="kt-widget17__desc">
                                        {{$total_subscription - $active_subscriptions}} اشتراك
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Widgets/Activity-->
                </div>
                <!--begin: Search Form -->
                <!--end: Search Form -->
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="col-md-12">
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <form action="{{route('admin.performance_reports.payment.export')}}" method="get">
                            <div class="row align-items-center">
                                <div class="col-xl-8  order-xl-1">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                            <div class="kt-input-icon kt-input-icon--left form-group">
                                                <label>ابحث :</label>
                                                <input type="text" class="form-control" placeholder="بحث..."
                                                       id="generalSearch">
                                                <span
                                                    class="kt-input-icon__icon kt-input-icon__icon--left kt-margin-t-15">
                                        <span><i class="la la-search"></i></span>
                                    </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>من تاريخ:</label>
                                                <input type="text" class="form-control" id="kt_datepicker_1" readonly=""
                                                       placeholder="اختر التاريخ">
                                            </div>
                                        </div>
                                        <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>إلى تاريخ:</label>
                                                <input type="text" class="form-control" id="kt_datepicker_2" readonly=""
                                                       placeholder="اختر تاريخ ">
                                            </div>
                                        </div>
                                        <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>بوابة الدفع</label>
                                                <select name="payment_method" id="payment_method" class="form-control">
                                                    <option value="0">الكل</option>
                                                    <option value="{{ \App\Models\PaymentMethod::PAYPAL }}">Paypal
                                                    </option>
                                                    <option value="{{ \App\Models\PaymentMethod::STRIPE }}">Stripe
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 order-1 order-xl-2 kt-align-right">
                                    <button type="submit" class="btn btn-default ">
                                        <i class="la la-file-excel-o"></i> Export
                                    </button>
                                    <div
                                        class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--begin: Datatable -->
            <div class="kt-datatable" id="kt_performance_report_payment_list"></div>
        </div>
    </div>
    <!-- end:: Content -->

@endsection

@push('css')
@endpush


@push('scripts')
    <script>
        var csrf_token = '{{csrf_token()}}';
        var KTDatatableRemoteAjaxDemo = function () {
            // Private functions

            // basic demo
            var demo = function () {

                var datatable = $('#kt_performance_report_payment_list').KTDatatable({
                    // datasource definition
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                method: 'GET',
                                url: '{{route('admin.performance_reports.payments')}}',
                                params: {
                                    datatable: 1
                                },
                                map: function (raw) {
                                    // sample data mapping
                                    var dataSet = raw;
                                    if (typeof raw.data !== 'undefined') {
                                        dataSet = raw.data;
                                    }
                                    return dataSet;
                                },
                            },
                        },
                        pageSize: 20,
                        serverPaging: true,
                        serverFiltering: true,
                        serverSorting: true,
                    },

                    // layout definition
                    layout: {
                        scroll: false,
                        footer: false,
                    },

                    // column sorting
                    sortable: true,

                    pagination: true,

                    search: {
                        input: $('#generalSearch'),
                    },

                    // columns definition
                    columns: [
                        {
                            field: 'id',
                            title: '#',
                            sortable: 'desc',
                            width: 30,
                            type: 'number',
                            selector: false,
                            textAlign: 'center',
                        },
                        {
                            field: 'payment_id',
                            title: 'رقم الفاتورة',
                            template: function (row) {
                                if (row.payment_id != null) {
                                    return row.payment_id;
                                } else {
                                    return row.subscription_id;
                                }
                            },
                        },

                        {
                            field: 'plan_id',
                            title: 'اسم الخطة',
                            template: function (row) {
                                if (row.plan != null) {
                                    return row.plan.title;
                                } else {
                                    return;
                                }
                            },
                        },
                        {
                            field: 'amount',
                            title: 'المبلغ',
                        },
                        {
                            field: 'plan_price',
                            title: 'سعر الخطة',
                        },
                        {
                            field: 'promocod_id',
                            title: 'الخصم',
                            template: function (r) {
                                return r.promocode ? (r.promocode.title_ar + ' - ' + r.promocode.value + (r.promocode.type == 'fixed' ? '$' : '%')) : '-'
                            }
                        },
                        {
                            field: 'payment_method_id',
                            title: 'بوابة الدفع',
                            template: function (row) {
                                if (row.payment_method != null) {
                                    return row.payment_method.title_en;
                                } else {
                                    return;
                                }
                            },
                        },
                        {
                            field: 'user_id',
                            title: 'اسم المستخدم',
                            template: function (row) {
                                if (row.user)
                                    return row.user.name;
                                return '---';
                            },
                        },
                        {
                            field: 'country_id',
                            title: 'مكان الشراء',
                            template: function (row) {
                                if (row.city)
                                    return row.country ? row.country.name_ar : '' + ' - ' + row.city.name_ar;
                                return '---';
                            },
                        },

                        {
                            field: 'created_at',
                            title: '{{ trans('admin.date') }}',
                            serverSorting: false,
                            sortable: false,
                            type: 'date',
                            format: 'MM/DD/YYYY',
                        },
                    ],

                });


                $('#kt_datepicker_2').on('change', function () {
                    if ($('#kt_datepicker_1').val()) {
                        datatable.search([$('#kt_datepicker_1').val(), $('#kt_datepicker_2').val()], 'date_range');
                    }
                });

                $('#kt_datepicker_1').on('change', function () {
                    if ($('#kt_datepicker_2').val()) {
                        datatable.search([$('#kt_datepicker_1').val(), $('#kt_datepicker_2').val()], 'date_range');
                    }
                });


                $('#kt_form_type').on('change', function () {
                    datatable.search($(this).val().toLowerCase(), 'Type');
                });

                $('#kt_form_status,#kt_form_type').selectpicker();

            };

            return {
                // public functions
                init: function () {
                    demo();
                },
            };
        }();

        jQuery(document).ready(function () {
            KTDatatableRemoteAjaxDemo.init();
        });
    </script>
@endpush

