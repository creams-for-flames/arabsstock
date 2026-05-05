@extends('admin_v2.layout.app')
@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder">
            <div class="kt-portlet__head kt-portlet__space-x">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        المدفوعات الجديدة
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fluid">
                <div class="kt-widget20">
                    <div class="kt-widget20__chart" style="height:130px;">
                        <canvas id="kt_chart_bandwidth1"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="col-md-12">
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <form action="" method="post">
                            @csrf
                            <div class="row align-items-center">
                                <div class="col-md-8 order-md-1">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>الشهر:</label>
                                                <input type="text" class="form-control" id="date" name="date"
                                                       readonly="" placeholder="اختر الشهر" value="{{ date('m-Y') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>نوع الباقة:</label>
                                                <select name="paln_type" id="plan_type" class="form-control">
                                                    <option value="0">الكل</option>
                                                    <option value="monthly">شهري</option>
                                                    <option value="package">باقة</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row p-0 m-0">
                            <div class="col-md-12 p-0 m-0">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="kt-portlet kt-iconbox kt-iconbox--brand kt-iconbox--animate-slower">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">
                                                    <div class="kt-iconbox__icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"/>
                                                                <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"/>
                                                            </g>
                                                        </svg>
                                                    </div>
                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            <p class="kt-link">كل الاشتراكات</p>
                                                        </h3>
                                                        <div class="kt-iconbox__content" id="total_subscriptions">
                                                            -
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">
                                                    <div class="kt-iconbox__icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"/>
                                                                <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"/>
                                                            </g>
                                                        </svg>
                                                    </div>
                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            <p class="kt-link">الاشتراكات الجديدة</p>
                                                        </h3>
                                                        <div class="kt-iconbox__content" id="new_subscriptions">
                                                            -
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="kt-portlet kt-iconbox kt-iconbox--warning kt-iconbox--animate-fast">
                                            <div class="kt-portlet__body">
                                                <div class="kt-iconbox__body">
                                                    <div class="kt-iconbox__icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"/>
                                                                <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"/>
                                                            </g>
                                                        </svg>
                                                    </div>
                                                    <div class="kt-iconbox__desc">
                                                        <h3 class="kt-iconbox__title">
                                                            <p class="kt-link">اشتراكات مُجددة</p>
                                                        </h3>
                                                        <div class="kt-iconbox__content" id="renewal_subscriptions">
                                                            -
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 m-0">
                                <div
                                    class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--begin: Datatable -->
                <div class="kt-datatable" id="kt_datatable"></div>
                <!--end: Datatable -->
            </div>
        </div>
        <!--end::Portlet-->
    </div>
@endsection
@push('css')
@endpush
@push('scripts')
    <script src="{{ asset('plugins/morris/raphael-min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/morris/morris.min.js')}}" type="text/javascript"></script>

    <!-- knob -->
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/knob/jquery.knob.js')}}" type="text/javascript"></script>

    <script>
        var $plan_types = {
            'monthly': "شهري",
            'package': "باقة"
        };
        var datatable = $('#kt_datatable').KTDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        method: 'GET',
                        url: '{{ $data_url }}',
                        params: {
                            datatable: 1
                        },
                        map: function (raw) {
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
            layout: {
                scroll: false,
                footer: false,
            },
            pagination: true,
            search: {
                input: $('#generalSearch'),
            },
            columns: [
                {
                    field: 'id',
                    title: '#',
                    sortable: 'asc',
                    width: 30,
                    type: 'number',
                    selector: false,
                    textAlign: 'center'
                },
                {
                    field: 'user_id',
                    title: '{{trans('views.Username')}}',
                    template: function (row) {
                        return row.user.name;
                    },
                },
                {
                    field: 'plan_type',
                    title: '{{trans('views.Plans')}}',
                    textAlign: 'center',
                    template: function (row) {
                        return $plan_types[row.plan_type];
                    },
                },
                {
                    field: 'user_subscriptions',
                    title: '{{__('Total User Subscriptions')}}',
                    textAlign: 'center',
                    template: function (row) {
                        return row.user.subscriptions_count;
                    },
                },
                {
                    field: 'created_at',
                    title: '{{ trans('admin.date') }}',
                    textAlign: 'center',
                    template: function (r) {
                        return moment(r.created_at).format('YYYY-MM-DD')
                    }
                },
                {
                    field: 'register_date',
                    title: 'تاريخ تسجيل المستخدم',
                    sortable: false,
                    textAlign: 'center',
                    template: function (r) {
                        return moment(r.user.created_at).format('YYYY-MM-DD')
                    }
                },
            ],
        });

        $('#date').on('change', function () {
            datatable.search($('#date').val(), 'date');
            update_statistics();
        });
        $('#plan_type').on('change', function () {
            datatable.search($('#plan_type').val(), 'plan_type');
            update_statistics();
        });

        $('#date').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });

        function update_statistics() {
            $.ajax({
                type: "GET",
                url: '{{ $data_url }}',
                data: {
                    query: {
                        date: $('#date').val(),
                        plan_type: $('#plan_type').val(),
                    }
                },
                success: function ($response) {
                    for (var key in $response) {
                        if ($response.hasOwnProperty(key)) {
                            $('#' + key).html($response[key] + ' اشتراك')
                        }
                    }
                },
                dataType: 'JSON'
            });
        }

        update_statistics();
    </script>
    <script>
        var bandwidthChart1 = function () {
            if ($('#kt_chart_bandwidth1').length == 0) {
                return;
            }

            var ctx = document.getElementById("kt_chart_bandwidth1").getContext("2d");

            var gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, Chart.helpers.color('#d1f1ec').alpha(1).rgbString());
            gradient.addColorStop(1, Chart.helpers.color('#d1f1ec').alpha(0.3).rgbString());

            var config = {
                type: 'line',
                data: {
                    labels: @json(array_keys($chart_data)),
                    datasets: [{
                        label: "اشتراك جديد",
                        backgroundColor: gradient,
                        borderColor: KTApp.getStateColor('success'),
                        pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                        pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                        pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                        pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                        data: @json(array_values($chart_data)),
                    }],
                },
                options: {
                    title: {
                        display: false,
                    },
                    tooltips: {
                        mode: 'nearest',
                        intersect: false,
                        position: 'nearest',
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month',
                            },
                        }],
                        yAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Value',
                            },
                            ticks: {
                                beginAtZero: true,
                            },
                        }],
                    },
                    elements: {
                        line: {
                            tension: 0.0000001,
                        },
                        point: {
                            radius: 4,
                            borderWidth: 12,
                        },
                    },
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 10,
                            bottom: 0,
                        },
                    },
                },
            };

            var chart = new Chart(ctx, config);
        };
        $(document).ready(function () {
            bandwidthChart1()
        });
    </script>
@endpush
