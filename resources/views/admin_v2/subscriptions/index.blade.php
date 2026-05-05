@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--begin::Portlet-->
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="col-md-12">
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <div class="row align-items-center">
                            <div class="col-xl-12  order-xl-1">
                                <div class="row align-items-center">
                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>من تاريخ:</label>
                                            <input type="text" class="form-control" id="kt_datepicker_1" readonly=""
                                                   placeholder="اختر التاريخ">
                                        </div>
                                    </div>
                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>إلى تاريخ:</label>
                                            <input type="text" class="form-control" id="kt_datepicker_2" readonly=""
                                                   placeholder="اختر تاريخ ">
                                        </div>
                                    </div>
                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>الخطة:</label>
                                            <select class="form-control select2-input" id="plan_id"
                                                    name="plan_id">
                                                <option></option>
                                                @foreach(\App\Models\Plan::where('status',1)->orderBy('id')->get() as $r)
                                                    <option
                                                        value="{{$r->id}}" {{ request('plan_id')==$r->id?'selected':'' }}>{{$r->title_ar}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>العميل:</label>
                                            <select name="user_id"
                                                    id="user_id" style="width: 100%;"
                                                    data-placeholder="اختر عميل"
                                            ></select>
                                        </div>
                                    </div>
                                    @php($status=[
                                        0=>['title'=>__('global.status.pending'),'class'=>' btn-label-danger',],
                                        1=>['title'=>__('global.status.active'),'class'=>' btn-label-success',],
                                        2=>['title'=>__('global.status.notactive'),'class'=>' btn-label-danger',],
                                        3=>['title'=>__('Refunded'),'class'=>' btn-label-danger',],
                                        'finished'=>['title'=>__('global.status.finished'),'class'=>' btn-label-warning',],
                                        ])
                                    {{--                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">--}}
                                    {{--                                        <div class="form-group">--}}
                                    {{--                                            <label>الحالة:</label>--}}
                                    {{--                                            <select class="form-control select2-input" id="status"--}}
                                    {{--                                                    name="status">--}}
                                    {{--                                                <option value="all">الكل</option>--}}
                                    {{--                                                @foreach($status as $k=>$r)--}}
                                    {{--                                                    <option--}}
                                    {{--                                                        value="{{ $k }}" {{ request()->has('status')&&request('status')==$k?'selected':'' }}>{{$r['title']}}</option>--}}
                                    {{--                                                @endforeach--}}
                                    {{--                                            </select>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                    @php($promocodes=\App\Models\Promocode::all())
                                    @if($promocodes->count())
                                        <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                            <div class="form-group">
                                                <label>كود خصم:</label>
                                                <select class="form-control select2-input" id="promocode_id"
                                                        name="promocode_id">
                                                    <option></option>
                                                    @foreach(\App\Models\Promocode::all() as $r)
                                                        <option
                                                            value="{{$r->id}}" {{ request('promocode_id')==$r->id?'selected':'' }}>{{$r->title_ar}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>حالة الدفع:</label>
                                            <select class="form-control select2-input" id="paid"
                                                    name="paid">
                                                <option></option>
                                                <option value="1">مدفوع</option>
                                                <option value="0">غير مدفوع</option>
                                            </select>
                                        </div>
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
                <div class="kt-datatable" id="kt_user_plan_list_datatable"></div>
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
                datatable = $('#kt_user_plan_list_datatable').KTDatatable({
                    // datasource definition
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                method: 'get',
                                url: '{{ route('admin.subscriptions.index') }}',
                                params: {
                                    datatable: 1,
                                    @if(request()->all())
                                    query:@json(request()->all())
                                    @endif
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
                        width: 50,
                        textAlign: 'center',
                    }, {
                        field: 'plan_id',
                        title: 'وصف',
                        template: function (row, index, datatable) {
                            return row.title
                        },
                    },
                        {
                            field: 'user_id',
                            title: '{{__('global.user-plans.fields.user')}}',
                            template: function (row, index, datatable) {
                                if (row.user)
                                    return row.user.name;
                                return "";
                            },
                        },
                        {
                            field: 'country_id',
                            title: 'مكان الشراء',
                            template: function (row) {
                                var address = '';
                                if (row.country)
                                    address = row.country.name_ar;
                                if (row.city)
                                    address += ' - ' + row.city.name_ar;

                                return address;
                            },
                        },
                        {
                            field: 'starts_at',
                            title: '{{__('global.user-plans.fields.date-start')}}',
                            width:70,
                            textAlign: 'center',
                            template: function (row, index, datatable) {
                                return row.starts_at !== null ? moment(row.starts_at).format("YYYY/MM/DD HH:mm") : '-';
                            },
                        }, {
                            field: 'ends_at',
                            title: '{{__('global.user-plans.fields.date-end')}}',
                            width:70,
                            textAlign: 'center',
                            template: function (row, index, datatable) {
                                return row.ends_at !== null ? moment(row.ends_at).format("YYYY/MM/DD HH:mm") : '-';
                            },
                        }, {
                            field: 'days_remaining',
                            width: 50,
                            title: '{{__('global.user-plans.fields.days-remaining')}}',
                            sortable: false,
                            textAlign: 'center',
                            template: function (row, index, datatable) {
                                var date1 = '{{now()->format('Y-m-d H:i:s')}}';
                                var date2 = moment(row.ends_at);
                                return (date2.diff(date1, 'days')) <= 0 ? "0" : (date2.diff(date1, 'days'));
                            },
                        }, {
                            field: 'remaining_credits',
                            width: 50,
                            title: '{{__('global.user-plans.fields.remaining-credits')}}',
                            textAlign: 'center',
                            sortable: false,
                        },
                        {
                            field: 'amount',
                            title: 'المبلغ المدفوع',
                            textAlign: 'center',
                            template: function (row, index, datatable) {
                                return '$ ' + parseInt(row.amount);
                            },
                        },
                        {
                            field: 'promocode_id',
                            title: '{{__('Promocode')}}',
                            textAlign: 'center',
                            template: function (row, index, datatable) {
                                if (row.promocode)
                                    return '-' + (row.plan.price - row.amount) + `$ (<a href="` + '{{ route('admin.promocodes.index',['id'=>0]) }}'.replace(0, row.promocode.id) + `">` + row.promocode.code + `</a>)`;
                                return '-';
                            },
                        },
                        {
                            field: 'status',
                            title: '{{__('views.Status')}}',
                            width: 90,
                            textAlign: 'center',
                            // callback function support for column rendering
                            template: function (row) {
                                var statuses = @json($status);
                                var status = row.status;
                                if (row.remaining_credits == 0 || moment(row.ends_at) < 0) {
                                    return '<span class="btn btn-bold btn-sm btn-font-sm ' + statuses['finished'].class + '">' + statuses['finished'].title + '</span>';
                                }
                                return '<span class="btn btn-bold btn-sm btn-font-sm ' + statuses[status].class + '">' + statuses[status].title + '</span>';
                            }
                        },
                        {
                            field: 'paid',
                            title: '{{__('حالة الدفع')}}',
                            width: 80,
                            textAlign: 'center',
                            // callback function support for column rendering
                            template: function (row) {
                                if (!row.paid) {
                                    return '<span class="btn btn-bold btn-sm btn-font-sm btn-label-danger">{{ __('Unpaid') }}</span>';
                                }
                                return '';
                            }
                        },
                        {{--{--}}
                        {{--    field: 'renewal',--}}
                        {{--    title: '{{__('Auto renewal')}}',--}}
                        {{--    width: 100,--}}
                        {{--    textAlign: 'center',--}}
                        {{--    // callback function support for column rendering--}}
                        {{--    template: function (r) {--}}
                        {{--        if (r.plan_type == 'package')--}}
                        {{--            return '-';--}}
                        {{--        if (r.renewal)--}}
                        {{--            return '<span class="btn btn-bold btn-sm btn-font-sm btn-label-success">{{__('misc.active')}}</span>';--}}
                        {{--        return '<span class="btn btn-bold btn-sm btn-font-sm btn-label-danger">{{__('misc.stopped')}}</span>';--}}
                        {{--    }--}}
                        {{--},--}}
                        {
                            field: "Actions",
                            width: 80,
                            title: "{{__('views.Actions')}}",
                            sortable: false,
                            autoHide: false,
                            overflow: 'visible',
                            template: function (row, index, datatable) {
                                var show_url = '{{ route('admin.subscriptions.show',0) }}'.replace('/0', '/' + row.id)
                                var eidt_url = '{{ route('admin.subscriptions.edit',0) }}'.replace('/0', '/' + row.id)
                                var status_url = '{{ route('admin.subscriptions.status',0) }}'.replace('/0', '/' + row.id)
                                var menu = '';
                                if (!row.paid) {
                                    menu += '<li class="kt-nav__item">\
                      <a href="' + eidt_url + '" class="kt-nav__link">\
                        <i class="kt-nav__link-icon fa fa-dollar-sign"></i>\
                        <span class="kt-nav__link-text">{{__('views.pay')}}</span>\
                      </a>\
                    </li><li class="kt-nav__item">\
                      <a href="' + status_url + '" class="kt-nav__link update-status" >\
                        <i class="kt-nav__link-icon la la-leaf"></i>\
                        <span class="kt-nav__link-text">' + (row.status ? '{{__('admin.disable')}}' : '{{__('admin.enable')}}') + '</span>\
                      </a>\
                    </li>';
                                }
                                menu += '<li class="kt-nav__item">\
                      <a href="' + show_url + '" class="kt-nav__link" target="_blank" data-fancybox data-type="iframe" data-preload="false" data-height="' + document.body.scrollHeight + '">\
                        <i class="kt-nav__link-icon flaticon2-contract"></i>\
                        <span class="kt-nav__link-text">{{__('views.Show')}}</span>\
                      </a>\
                    </li>';
                                return '\
              <div class="dropdown">\
                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
                  <i class="flaticon-more-1"></i>\
                </a>\
                <div class="dropdown-menu dropdown-menu-right">\
                  <ul class="kt-nav">' + menu + '</ul>\
                </div>\
              </div>\
            ';
                            },
                        }]
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
                $(document).on('change', '#promocode_id', function () {
                    datatable.search($('#promocode_id').val(), 'promocode_id');
                });
                $(document).on('change', '#paid', function () {
                    datatable.search($('#paid').val(), 'paid');
                });
                $(document).on('change', '#plan_id', function () {
                    datatable.search($('#plan_id').val(), 'plan_id');
                });
                $(document).on('change', '#user_id', function () {
                    datatable.search($('#user_id').val(), 'user_id');
                });
                $(document).on('change', '#status', function () {
                    datatable.search($('#status').val(), 'status');
                });
                $(document).on('click', '.update-status', function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    $.ajax({
                        type: "POST",
                        url: $this.attr('href'),
                        data: {},
                        success: function () {
                            datatable.reload()
                        },
                        dataType: 'json'
                    });
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
            $('#user_id').select2({
                language: "ar",
                placeholder: '{{ __('views.Choose Users') }}',
                ajax: {
                    url: '{{ route('admin.members.ajax') }}',
                    dataType: 'json',
                    method: 'get',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
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
    <script src="{{ asset('js/fancyapps_ui@4.0_dist_fancybox.umd.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/fancyapps_ui@4.0_dist_fancybox.css') }}"/>

@endpush
