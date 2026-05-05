@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--begin::Portlet-->
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="col-md-12">
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <form action="{{route('admin.user_plans.items.export_contributor_downloads')}}" method="get">
                            <div class="row align-items-center">
                                <div class="col-md-8 order-md-1">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>من تاريخ:</label>
                                                <input type="text" class="form-control" id="kt_datepicker_1"
                                                       name="date_from" readonly="" placeholder="اختر التاريخ">
                                            </div>
                                        </div>
                                        <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>إلى تاريخ:</label>
                                                <input type="text" class="form-control" id="kt_datepicker_2"
                                                       name="date_to" readonly="" placeholder="اختر تاريخ ">
                                            </div>
                                        </div>
                                        <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>المساهم</label>
                                                <select name="contributor_id"
                                                        id="contributor_id" style="width: 100%;"
                                                        data-placeholder="اختر مساهم"
                                                ></select>
                                            </div>
                                        </div>
                                    </div>
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
                                method: 'get',
                                url: '{{$index_url}}',
                                params: {
                                    datatable: 1,
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
                    columns: [{
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 40,
                        textAlign: 'center',
                    },
                        {
                            field: 'imageshow',
                            title: 'الصورة الرمزية',
                            template: function (row, index, datatable) {

                                if (row.purchaseable && row.purchaseable.thumbnail)
                                    return '<a href="' + row.purchaseable.post_link + '" target="_blank"><img src="{{ cdn('') }}/' + row.purchaseable.thumbnail + '" width="50"/></a>';

                            },
                        },
                        {
                            field: 'user_id',
                            title: '{{__('global.user-plans.fields.user')}}',
                            template: function (row, index, datatable) {
                                return row.user.name
                            },
                        }, {
                            field: 'contributor_id',
                            title: '{{__('global.user-plans.fields.contributor')}}',
                            template: function (row, index, datatable) {
                                return (row.contributor) ? row.contributor.name : '-'
                            },
                        },
                        {
                            field: 'unit_price',
                            title: '{{__('global.unit_price')}}',
                        },
                        {
                            field: 'profit_ratio',
                            title: '{{__('global.profit_ratio')}}',

                        },
                        {
                            field: 'profit_value',
                            title: '{{__('global.profit_value')}}',
                        },
                        {
                            field: 'date',
                            title: '{{__('global.downloaded_at')}}',
                            template: function (row, index, datatable) {
                                return moment(row.created_at).format("YYYY/MM/DD HH:mm");
                            },
                        },
                        {
                            field: "Actions",
                            width: 80,
                            title: "{{__('views.Actions')}}",
                            sortable: false,
                            autoHide: false,
                            overflow: 'visible',
                            template: function (row) {
                                if (row.download_id) {
                                    var show_url = '{{ route('admin.downloads.show',0) }}'.replace('/0', '/' + row.download_id)
                                    return '\
              <div class="dropdown">\
                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
                  <i class="flaticon-more-1"></i>\
                </a>\
                <div class="dropdown-menu dropdown-menu-right">\
                  <ul class="kt-nav">\
                    <li class="kt-nav__item">\
                      <a href="' + show_url + '" class="kt-nav__link">\
                        <i class="kt-nav__link-icon flaticon2-contract"></i>\
                        <span class="kt-nav__link-text">{{__('views.Show')}}</span>\
                      </a>\
                    </li>\
                  </ul>\
                </div>\
              </div>\
            ';
                                }
                                return '';
                            },
                        }
                    ]
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

                $('#contributor_id').on('change', function () {
                    if ($('#contributor_id').val()) {
                        datatable.search($('#contributor_id').val(), 'contributor_id');
                    }
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
            $('#contributor_id').select2({
                language: "ar",
                placeholder: '{{ __('views.Choose Users') }}',
                ajax: {
                    url: '{{ route('admin.contributor.datatable') }}',
                    dataType: 'json',
                    method: 'post',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            query: {generalSearch: params.term},
                            pagination: {
                                page: 1,
                                perpage: 10,
                            }
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });
        });
    </script>
@endpush
