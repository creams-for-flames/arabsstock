@extends('admin_v2.layout.app')
@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="form-group row ">
            <label
                class="col-xl-3 col-lg-1 col-form-label text-center">{{ __($data->check_count . '/' . $data->target_count) }}</label>
            <div class="col-lg-11 col-xl-8">
                <div class="progress" style="height: 40px;">
                    <div class="progress-bar @if ($progress < 100) progress-bar-striped progress-bar-animated @else bg-success @endif"
                        role="progressbar" style="width: {{ $progress }}%;font-size: 20px;font-weight: 600;"
                        aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
                </div>
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">

            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="col-md-12">
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <form action="" method="get" class="row align-items-center">
                            <div class="col-md-12  order-xl-1">
                                <div class="row align-items-center">
                                    <div class="col kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label for="type">{{ __('نوع الملف ') }}</label>
                                            <select name="type" id="type" class="form-control ">
                                                <option selected>{{ __('global.app_all') }}</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label for="file_status">{{ __('حالة فحص المخزن ') }}</label>
                                            <select name="file_status" id="file_status" class="form-control ">
                                                <option value="all"  >{{ __('global.app_all') }}</option>
                                                <option value="notfound" selected >{{ __('notfound') }}</option>
                                                <option value="pending">{{ __('pending') }}</option>
                                                <option value="prossing">{{ __('prossing') }}</option>
                                                <option value="done">{{ __('done') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label for="status">{{ __('حالة الملف') }}</label>
                                            <select name="status" id="status" class="form-control ">
                                                <option  value="all" selected>{{ __('global.app_all') }}</option>
                                                <option value="active" >{{ __('global.active') }}</option>
                                                <option value="pending">{{ __('global.pending') }}</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label for="folder_id">{{ __('global.select_folder') }}</label>
                                            <select name="folder_id" id="folder_id" class="form-control select2_folder">
                                                <option selected>{{ __('global.app_all') }}</option>
                                                @foreach ($folders as $folder)
                                                    <option value="{{ $folder->id }}">{{ $folder->folder }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>المساهم</label>
                                            <select name="contributor_id" id="contributor_id" style="width: 100%;"
                                                data-placeholder="اختر مساهم"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>المشرف</label>
                                            <select name="user_id" id="user_id" style="width: 100%;"
                                                data-placeholder="اختر مشرف">
                                                <option value=""></option>
                                                @foreach ($admins as $admin)
                                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="kt-datatable" id="kt_datatable"></div>
            </div>
        </div>
    </div>
@endsection
@push('css')
@endpush
@push('scripts')

    <script>
        "use strict";
        var KTUserListDatatable = function() {

            // variables
            var datatable;

            // init
            var init = function() {
                // init the datatables. Learn more: https://keenthemes.com/metronic/?page=docs&section=datatable
                datatable = $('#kt_datatable').KTDatatable({
                    // datasource definition
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: '{{ $datatable_url }}',
                                params: {
                                    _token: '{{ csrf_token() }}',
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
                    field: 'thumbnail',
                    title: '{{ __('views.Thumbnail') }}',
                    template: function(r) {
                        return `
                        <a target="_blank" href="${r.warehouseable !== null?r.warehouseable.post_link:'javascript:;'}"> <image src="{{ cdn('/') }}/${r.warehouseable !== null ?r.warehouseable.thumbnail:'#'}" width="100" heigh="100"></a>
                        `;
                    }
                },
                {
                    field: 'original_name',
                    title: '{{ __('views.original_name') }}',
                    template: function(r) {
                        return r.warehouseable !== null?r.warehouseable.original_name:'_';
                    }
                },
                {
                    field: 'user_id',
                    title: '{{ __('views.Username') }}',
                    template: function(r) {
                        return r.warehouseable?r.warehouseable.user.name:'_';
                    }
                },
                {
                    field: 'folder_id',
                    title: '{{ __('views.folder') }}',
                    template: function(r) {
                        return  r.warehouseable !== null && r.warehouseable.folder_id && (typeof r.warehouseable.folder === 'object' && r
                            .warehouseable.folder !== null) ? r.warehouseable.folder.folder : '_';
                    }
                },
                {
                    field: 'type',
                    title: '{{ __('views.Type') }}',
                },

                {
                    field: 'status',
                    title: '{{ __('views.Status') }}',
                    textAlign: 'center',
                    width: 120
                },
                {
                    field: 'date',
                    title: '{{ __('views.upload_date') }}',
                    template: function(r) {
                        if(r.warehouseable === null)
                          return '_';
                          
                        var upload_date = r.warehouseable_type;
                        if (upload_date === "App\Models\Vector") {
                            return r.warehouseable.created_at;
                        } else {
                            return r.warehouseable.date;
                        }
                    }
                },
                {
                    field: 'note',
                    title: '{{ __('views.invoice_notes') }}',
                    textAlign: 'center',
                    width: 120
                },
                {
                    field: 'created_at',
                    title: '{{ __('views.Date of Creation') }}',
                    textAlign: 'center',
                    template: function(r) {
                        return moment(r.created_at).format('LLLL');
                    }
                }, {
                    field: "Actions",
                    width: 80,
                    title: "{{ __('views.Actions') }}",
                    sortable: false,
                    autoHide: false,
                    overflow: 'visible',
                    template: function(row) {
                        var large_file = '{!! $large_file !!}';
                        var roles = ['admin', 'admin_video', 'admin_vector'];
                        if (row.status === 'notfound' && row.type === large_file && (row.warehouseable.user
                                .role && roles.includes(row.warehouseable.user.role))) {
                            var admin_file_reupload_not_found = '{{ $admin_file_reupload_not_found }}'
                                .replace('/0', '/' + row.id);
                            return `
                                <div class="dropdown">
                                  <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">
                                    <i class="flaticon-more-1"></i>
                                  </a>
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                      <li class="kt-nav__item">
                                          <a href="${admin_file_reupload_not_found}"   class="kt-nav__link">
                                          <i class="kt-nav__link-icon flaticon2-trash"></i>
                                          <span class="kt-nav__link-text">{{ __('views.reupload') }} </span>
                                        </a>
                                      </li>
                                     
                                    </ul>
                                  </div>
                                </div>
                            `;



                        } else if (row.status === 'notfound') {
                            var edit_url = '{{ $edit_url }}'.replace('/0', '/' + row.id);

                            var delete_url = '';
                            return `
                                <div class="dropdown">
                                  <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">
                                    <i class="flaticon-more-1"></i>
                                  </a>
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="kt-nav">
                                      <li class="kt-nav__item">
                                          <a data-to="${edit_url}" href="#" data-confirm="{{ __('Are you sure update?') }}" data-csrf="{{ csrf_token() }}" data-method="post" class="kt-nav__link">
                                          <i class="kt-nav__link-icon flaticon2-trash"></i>
                                          <span class="kt-nav__link-text">{{ __('views.insert_queue') }}</span>
                                        </a>
                                      </li>
                                     
                                    </ul>
                                  </div>
                                </div>
                              `;
                        } else {
                            return '';
                        }
                    },
                }
            ],
                });
            }

            // search
            var search = function() {
                $('#folder_id').on('change', function() {
                    datatable.search($('#folder_id').val(), 'folder_id');
                });
                $('#contributor_id').on('change', function() {
                    datatable.search($('#contributor_id').val(), 'contributor_id');
                });
                $('#user_id').on('change', function() {
                    datatable.search($('#user_id').val(), 'user_id');
                });
                $('#status').on('change', function() {
                    if ($(this).val() != 'undefined') {
                        datatable.search($(this).val().toLowerCase(), 'status');
                    }
                });

                $('#file_status').on('change', function() {
                    if ($(this).val() != 'undefined') {
                        datatable.search($(this).val().toLowerCase(), 'file_status');
                    }
                });
                $('#type').on('change', function() {
                    if ($(this).val() != 'undefined') {
                        datatable.search($(this).val().toLowerCase(), 'type');
                    }
                });
            }

            // selection
            var selection = function() {
                // init form controls
                //$('#kt_form_status, #kt_form_type').selectpicker();

                // event handler on check and uncheck on records
                datatable.on('kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated',
                    function(e) {
                        var checkedNodes = datatable.rows('.kt-datatable__row--active')
                            .nodes(); // get selected records
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
            var arabsSubheaderAction = function() {
                $('.arabs_subheader_action_button').on('click', function() {
                    // fetch selected IDs
                    var ids = datatable.rows('.kt-datatable__row--active').nodes().find(
                        '.kt-checkbox--single > [type="checkbox"]').map(function(i, chk) {
                        return $(chk).val();
                    }).toArray();

                    if (ids.length > 0) {
                        // learn more: https://sweetalert2.github.io/
                        var message = $(this).data('action-confirm')
                        swal.fire({
                            text: message.replace("0", ids.length),
                            type: "info",
                        }).then(function(result) {
                            if (result.value) {
                                var action_url = $(this).data('action-url').replace('0', ids.join(
                                    ','))
                                post(action_url, {
                                    _token: document.querySelector('meta[name=csrf-token]')
                                        .content,
                                }, $(this).data('action-method'))
                            }
                        }.bind(this));
                    }
                });

                $('.arabs_subheader_action_dropdown').on('click', "a", function() {
                    var label = $(this).find(".kt-nav__link-text").text();
                    var value = $(this).data('action-value');

                    // fetch selected IDs
                    var ids = datatable.rows('.kt-datatable__row--active').nodes().find(
                        '.kt-checkbox--single > [type="checkbox"]').map(function(i, chk) {
                        return $(chk).val();
                    }).toArray();

                    if (ids.length > 0) {
                        // learn more: https://sweetalert2.github.io/
                        var message = $(this).data('action-confirm')
                        swal.fire({
                            html: message.replace("0", ids.length).replace("ttt", label),
                            type: "info",
                        }).then(function(result) {
                            if (result.value) {
                                var action_url = $(this).data('action-url').replace('0', ids.join(
                                    ','))
                                post(action_url, {
                                    ids: ids,
                                    status: value,
                                    _token: document.querySelector('meta[name=csrf-token]')
                                        .content,
                                }, $(this).data('action-method'))
                            }
                        }.bind(this));
                    }
                });

            }

            var updateTotal = function() {
                datatable.on('kt-datatable--on-layout-updated', function() {
                    $('#kt_subheader_total').html('{{ __('views.:number Total', ['number' => 0]) }}'
                        .replace("0", datatable.getTotalRows()));
                });
            };

            return {
                // public functions
                init: function() {
                    init();
                    search();
                    selection();
                    arabsSubheaderAction();
                    updateTotal();
                },
            };
        }();

        // On document ready
        KTUtil.ready(function() {
            KTUserListDatatable.init();
            $('.select2_folder').select2();
            $('#file_status').select2();
            $('#type').select2();
            $('#contributor_id').select2({
                language: "ar",
                placeholder: '{{ __('views.Choose Users') }}',
                ajax: {
                    url: '{{ route('admin.contributor.datatable') }}',
                    dataType: 'json',
                    method: 'post',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            query: {
                                generalSearch: params.term
                            },
                            pagination: {
                                page: 1,
                                perpage: 10,
                            }
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });
            $('#user_id').select2();
            // $('#status').trigger('change');
        });
    </script>
@endpush
