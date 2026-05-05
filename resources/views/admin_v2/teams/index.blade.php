@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--begin::Portlet-->
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-10">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="row align-items-center">
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-input-icon kt-input-icon--left">
                                        <input type="text" class="form-control" placeholder="{{__('views.Search...')}}"
                                               id="generalSearch">
                                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
<span><i class="la la-search"></i></span>
</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 order-1 order-xl-2 kt-align-right">
                            <a href="#" class="btn btn-default kt-hidden">
                                <i class="la la-cart-plus"></i> New Order
                            </a>
                            <div
                                class="kt-separator kt-separator--border-dashed kt-separator--space-lg d-xl-none"></div>
                        </div>
                    </div>
                </div>
                <!--begin: Datatable -->
                <div class="kt-datatable" id="kt_member_list_datatable"></div>
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
            var datatable;
            var init = function () {
                datatable = $('#kt_member_list_datatable').KTDatatable({
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: '{{ route('admin.teams.index') }}',
                                method: 'get',
                                params: {
                                    datatable: 1,
                                    _token: '{{csrf_token()}}',
                                }
                            },
                        },
                        pageSize: 20, // display 20 records per page
                        serverPaging: true,
                        serverFiltering: true,
                        serverSorting: true,
                        saveState: {
                            cookie: false,
                            webstorage: false,
                        }
                    },
                    layout: {
                        scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                        footer: false, // display/hide footer
                    },
                    sortable: true,
                    pagination: true,
                    search: {
                        input: $('#generalSearch'),
                        delay: 400,
                    },
                    columns: [{
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 20,
                        selector: {
                            class: 'kt-checkbox--solid'
                        },
                        textAlign: 'center',
                    }, {
                        field: 'name',
                        title: '{{__('views.Name')}}',
                        width: 200,
                    }, {
                        field: 'leader_id',
                        title: '{{__('Leader')}}',
                        template: function (row, index, datatable) {
                            return row.leader.name
                        },
                    }, {
                        field: 'created_at',
                        title: '{{__('views.Date of Creation')}}',
                        template: function (row, index, datatable) {
                            return moment(row.created_at).format("YYYY/MM/DD HH:mm");
                        },
                    }, {
                        field: 'subscriptions_count',
                        title: '{{__('Subscriptions')}}',
                        textAlign: 'center',
                    }, {
                        field: 'users_count',
                        title: '{{__('Members')}}',
                        textAlign: 'center',
                        template: function (row, index, datatable) {
                            return '<a href="' + '{{ route('admin.members.index',['team_id'=>0]) }}'.replace(0, row.id) + '">' + row.users_count + '</a>';
                        },
                    }, {
                        field: "Actions",
                        width: 80,
                        title: "{{__('views.Actions')}}",
                        sortable: false,
                        autoHide: false,
                        overflow: 'visible',
                        template: function (row) {
                            var edit_url = '{{route('admin.teams.edit',0)}}'.replace('0', row.id)
                            var delete_url = '{{route('admin.teams.destroy',0)}}'.replace('0', row.id)
                            return '\
<div class="dropdown">\
<a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
<i class="flaticon-more-1"></i>\
</a>\
<div class="dropdown-menu dropdown-menu-right">\
<ul class="kt-nav">\
<li class="kt-nav__item">\
<a href="' + edit_url + '" class="kt-nav__link">\
<i class="kt-nav__link-icon flaticon2-contract"></i>\
<span class="kt-nav__link-text">{{__('views.Edit')}}</span>\
</a>\
</li>\
</ul>\
</div>\
</div>\
';
                        },
                    }]
                });
            }

// search
            var search = function () {
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
