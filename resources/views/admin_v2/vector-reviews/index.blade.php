@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--begin::Portlet-->
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="col-md-12">
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <div class="align-items-center">
                            <div class="row align-items-center">
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>{{ __('From Date') }}:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_1" readonly=""
                                               placeholder="{{ __('Choose Date') }}">
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>{{ __('To Date') }}:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_2" readonly=""
                                               placeholder="{{ __('Choose Date') }} ">
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>{{ __('Reviewer') }}:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_reviewer"
                                               placeholder="{{ __('Reviewer') }} ">
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>{{ __('Publisher') }}:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_publisher"
                                               placeholder="{{ __('Publisher') }} ">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 order-1 order-xl-2 kt-align-right">
                            <a href="#" class="btn btn-default kt-hidden">
                                <i class="la la-cart-plus"></i> New Order
                            </a>
                            <div
                                class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
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
                                'method': 'GET',
                                url: '{{ route('admin.vector-reviews.index') }}',
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
                            }
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
                            field: 'vector_id',
                            title: '#',
                            sortable: false,
                            width: 40,
                            textAlign: 'center',
                        }, {
                            field: 'reviewer_id',
                            title: '{{__('Reviewer')}}',
                            template: function (row, index, datatable) {
                                return row.reviewer ? row.reviewer.name : '-'
                            },
                        }, {
                            field: 'publisher_id',
                            title: '{{__('Publisher')}}',
                            template: function (row, index, datatable) {
                                return row.publisher ? row.publisher.name : '-'
                            },
                        },
                        {
                            field: 'vectorshow',
                            title: '',
                            template: function (row, index, datatable) {
                                if (row && row.thumbnail)
                                    return '<img width="100"  src="' + row.thumbnail + '" alt="image">';

                            },
                        },

                        {
                            field: 'vector',
                            title: '{{__('global.vector')}}',
                            textAlign: 'center',
                            template: function (row, index, datatable) {

                                if (row && row.title)
                                    return '<a target="_blank" href="' + row.post_link + '">' + row.title + '</a>';
                            },
                        },
                        {
                            field: 'reviewed_at',
                            title: '{{__('Reviewed At')}}',
                            align: 'center',
                            template: function (row, index, datatable) {
                                return row.reviewed_at ? moment(row.reviewed_at).format("YYYY/MM/DD HH:mm") : '-';
                            },
                        },
                        {
                            field: 'published_at',
                            title: '{{__('Published At')}}',
                            textAlign: 'center',
                            template: function (row, index, datatable) {
                                return row.published_at ? moment(row.published_at).format("YYYY/MM/DD HH:mm") : '-';
                            },
                        },
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
                $('#kt_datepicker_reviewer').on('keyup keypress change', function () {
                    datatable.search($('#kt_datepicker_reviewer').val(), 'reviewer');
                });
                $('#kt_datepicker_publisher').on('keyup keypress change', function () {
                    datatable.search($('#kt_datepicker_publisher').val(), 'publisher');
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
