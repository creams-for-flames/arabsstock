@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--begin::Portlet-->
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="col-md-12">
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <form action="{{route('admin.videos.user_plans.items.export_downloads')}}" method="get">
                            <div class="row align-items-center">
                                <div class="col-md-8 order-md-1">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>من تاريخ:</label>
                                                <input type="text" class="form-control" id="kt_datepicker_1"
                                                       name="date_from"
                                                       readonly="" placeholder="اختر التاريخ">
                                            </div>
                                        </div>
                                        <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>إلى تاريخ:</label>
                                                <input type="text" class="form-control" id="kt_datepicker_2"
                                                       name="date_to"
                                                       readonly="" placeholder="اختر تاريخ ">
                                            </div>
                                        </div>
                                        <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>الدفع</label>
                                                <select name="type" id="free" class="form-control select2_folder">
                                                    <option value="all"> الكل</option>
                                                    <option value="0"> مدفوع</option>
                                                    <option value="1"> مجاني</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>{{__("views.Type")}}</label>
                                                <select name="created_by" id="created_by" class="form-control select2_folder">
                                                    @foreach ($types as $type)
                                                        <option value="{{$type}}"
                                                                @if($type == 'all') selected @endif> {{$type}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 order-md-2 kt-align-right">
                                    <button type="button" id="reset-date" class="btn btn-default ">
                                        <i class="la la-reset"></i> Reset
                                    </button>
                                </div>
                                <div class="col-md-2 order-md-2 kt-align-right">
                                    <button type="submit" class="btn btn-success ">
                                        <i class="la la-file-excel-o"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="row p-0 m-0">
                            <div class="col-md-12 p-0 m-0">
                                <div
                                    class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--begin: Datatable -->
                <div class="kt-datatable" id="kt_user_plan_downloads_list_datatable"></div>
                <!--end: Datatable -->
            </div>
        </div>
        <!--end::Portlet-->
    </div>
    <!-- end:: Content -->
@endsection

@push('css')
@endpush


@push('scripts')
    <script>
        "use strict";
        var KTUserListDatatable = function () {

// variables
            var datatable;

// init
            var init = function () {
// init the datatables. Learn more: https://keenthemes.com/metronic/?page=docs&section=datatable
                datatable = $('#kt_user_plan_downloads_list_datatable').KTDatatable({
// datasource definition
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: '{{$index_url}}',
                                params: {
                                    _token: '{{csrf_token()}}',
                                }
                            },
                        },
                        pageSize: 20, // display 20 records per page
                        serverPaging: true,
                        serverFiltering: true,
                        serverSorting: false,
                        saveState: {
                            cookie: false,
                            webstorage: false,
                        }
                    },

// layout definition
                    layout: {
                        scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                        footer: false, // display/hide footer
                    },

// column sorting
                    sortable: true,

                    pagination: true,

                    search: {
                        input: $('#generalSearch'),
                        delay: 400,
                    },

// columns definition
                    columns: [
                        {
                            field: 'id',
                            title: '#',
                            sortable: false,
                            width: 40,
                            textAlign: 'center',
                        },
                        {
                            field: 'video_id',
                            title: '# ' + '{{__('global.video')}}',
                            sortable: false,
                            width: 40,
                            textAlign: 'center',
                        },
                        {
                            field: 'user_id',
                            title: '{{__('global.user-plans.fields.user')}}',
                            template: function (row, index, datatable) {
                                return row.client && row.client.name ? row.client.name : '---'
                            },
                        },
                        {
                            field: 'contributor',
                            sortable: false,
                            title: '{{__('global.user-plans.fields.contributor')}}',
                            template: function(row, index, datatable) {
                                return (row.video)?row.video.user.name:'-'
                            },
                        },
                        {
                            field: 'ip',
                            title: '{{__('global.ip')}}',
                            template: function (r) {
                                return '<a href="https://whatismyipaddress.com/ip/{ip}" target="_blank">{ip}</a>'.replaceAll('{ip}', r.ip);
                            },
                        },
                        {
                            field: 'amount',
                            title: '{{__('global.app_amount')}}',
                            template: function (row, index, datatable) {
                                if (row.plan && row.plan.price)
                                    return '$ ' + row.plan.price / row.plan.downloads_count;
                            },
                        },
                        {
                            field: 'imageshow',
                            title: 'الصورة الرمزية',
                            template: function (row, index, datatable) {

                                return '  <img src="{{cdn('')}}/' + row.video.thumbnail_sm + '" width="200"/>';
                            },
                        },
                        {
                            field: 'image',
                            title: '{{__('global.the_video')}}',
                            template: function (row, index, datatable) {
                                if (row.video && row.video.title)
                                    return '<a target="_blank" href="' + row.video.post_link + '">' + row.video.title + '</a>';

                            },
                        },

                        {
                            field: 'date',
                            title: '{{__('global.downloaded_at')}}',
                            template: function (row, index, datatable) {
                                return moment(row.date).format("YYYY/MM/DD HH:mm");
                            },
                        },
                    ]
                });
                $("#reset-date").click(function (e) {
                    $('#kt_datepicker_1').val("").datepicker("update");
                    $('#kt_datepicker_2').val("").datepicker("update");
                    return;
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
                $('#created_by').on('change', function () {
                    if ($('#created_by').val()) {
                        datatable.search($('#created_by').val(), 'created_by');
                    }
                });
                $('#free').on('change', function () {
                    datatable.search($('#free').val(), 'free');
                });
            }

// search
            var search = function () {
                $('#kt_form_status').on('change', function () {
                    datatable.search($(this).val().toLowerCase(), 'Status');
                });
            }

// selection
            var selection = function () {
// init form controls
//$('#kt_form_status, #kt_form_type').selectpicker();

// event handler on check and uncheck on records
                datatable.on('kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated', function (e) {
                    var checkedNodes = datatable.rows('.kt-datatable__row--active').nodes(); // get selected records
                    var count = checkedNodes.length; // selected records count

                    $('#kt_subheader_group_selected_rows').html(count);

                    if (count > 0) {
                        $('#kt_subheader_search').addClass('kt-hidden');
                        $('#kt_subheader_group_actions').removeClass('kt-hidden');
                    } else {
                        $('#kt_subheader_search').removeClass('kt-hidden');
                        $('#kt_subheader_group_actions').addClass('kt-hidden');
                    }
                });
            }

// selected records delete
            var arabsSubheaderAction = function () {
                $('.arabs_subheader_action_button').on('click', function () {
// fetch selected IDs
                    var ids = datatable.rows('.kt-datatable__row--active').nodes().find('.kt-checkbox--single > [type="checkbox"]').map(function (i, chk) {
                        return $(chk).val();
                    }).toArray();

                    if (ids.length > 0) {
// learn more: https://sweetalert2.github.io/
                        var message = $(this).data('action-confirm')
                        swal.fire({
                            text: message.replace("0", ids.length),
                            type: "info",
                        }).then(function (result) {
                            if (result.value) {
                                var action_url = $(this).data('action-url').replace('0', ids.join(','))
                                post(action_url, {
                                    _token: document.querySelector('meta[name=csrf-token]').content,
                                }, $(this).data('action-method'))
                            }
                        }.bind(this));
                    }
                });

                $('.arabs_subheader_action_dropdown').on('click', "a", function () {
                    var label = $(this).find(".kt-nav__link-text").text();
                    var value = $(this).data('action-value');

// fetch selected IDs
                    var ids = datatable.rows('.kt-datatable__row--active').nodes().find('.kt-checkbox--single > [type="checkbox"]').map(function (i, chk) {
                        return $(chk).val();
                    }).toArray();

                    if (ids.length > 0) {
// learn more: https://sweetalert2.github.io/
                        var message = $(this).data('action-confirm')
                        swal.fire({
                            html: message.replace("0", ids.length).replace("ttt", label),
                            type: "info",
                        }).then(function (result) {
                            if (result.value) {
                                var action_url = $(this).data('action-url').replace('0', ids.join(','))
                                post(action_url, {
                                    ids: ids,
                                    status: value,
                                    _token: document.querySelector('meta[name=csrf-token]').content,
                                }, $(this).data('action-method'))
                            }
                        }.bind(this));
                    }
                });

            }

            var updateTotal = function () {
                datatable.on('kt-datatable--on-layout-updated', function () {
                    $('#kt_subheader_total').html('{{__('views.:number Total', ['number' => 0])}}'.replace("0", datatable.getTotalRows()));
                });
            };

            return {
// public functions
                init: function () {
                    init();
                    search();
                    selection();
                    arabsSubheaderAction();
                    updateTotal();
                },
            };
        }();

        // On document ready
        KTUtil.ready(function () {
            KTUserListDatatable.init();
        });
    </script>
@endpush
