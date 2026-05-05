@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--begin::Portlet-->
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="row mb-50 mt-50">

                    <div class="col m-3">
                        <label class="" for="status">{{ __('views.Status') }}</label>
                        <select id="statusSelect" class="form-control">
                            <option value="all" >{{ __('views.all') }}</option>
                            <option value="pending" selected>{{ __('views.removebg_pending') }}</option>
                            <option value="active">{{ __('views.removebg_active') }}</option>
                        </select>
                    </div>

                </div>
                <br />
                <!--begin: Datatable -->
                <div class="kt-datatable" id="kt_raw_videos_list_datatable"></div>
                <!--end: Datatable -->
            </div>
        </div>
        <!--end::Portlet-->
    </div>

    <!-- end:: Content -->
@endsection



@push('scripts')
    <script>
        "use strict";
        var datatable;

        var KTUserListDatatable = function() {

            // variables

            // init
            var init = function() {
                // init the datatables. Learn more: https://keenthemes.com/metronic/?page=docs&section=datatable
                datatable = $('#kt_raw_videos_list_datatable').KTDatatable({
                    // datasource definition
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: '{{ $index_url }}',
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
                            field: 'id',
                            title: '#',
                            sortable: false,
                            width: 20,
                            selector: {
                                class: 'kt-checkbox--solid'
                            },
                            textAlign: 'center',
                        },
                        {
                            field: 'user_id',
                            title: '{{ __('misc.uploaded_by') }}',
                            template: function(row) {
                                if (row.admin) {
                                    return row.admin ?
                                        `<span class="badge badge-info"> ${row.admin.username}</span>` :
                                        '';
                                }
                                return row.user ?
                                    `<span class="badge badge-info"> ${row.user.username}</span>` :
                                    '';
                            },
                        },
                        {
                            field: 'thumbnail',
                            title: '{{ __('misc.thumbnail') }}',
                            sortable: false,
                            template: function(row) {

                                return ` 
                              <a href="${row.post_link}" target="_blank">
                                <img src="{{ cdn('') }}/${row.thumbnail}" width="110" class="img-thumbnail bg-transparent " />
                                </a>
                              <button title="fullscreen" class=" fullscreen-video btn btn-light p-2 m-1 " data-video_file="{{ cdn('') }}/${row.size_240p}"> <i class="fa fa-expand-arrows-alt"></i></button>
                              
                              `;
                            },
                        },
                        {
                            field: 'preview',
                            title: '{{ __('misc.raw_preview') }}',
                            sortable: false,
                            template: function(row) {
                                var raw_preview = row.raw != null?row.raw.preview:row.contributor_file.raw.preview;
                                return ` 
                                <a href="${row.post_link}" target="_blank">

                                <img src="{{ cdn('') }}/${row.thumbnail}" width="110" class="img-thumbnail  " />
                                </a>
                                <button title="fullscreen" class=" fullscreen-video btn btn-light p-2 m-1 " data-video_file="{{ cdn('') }}/${raw_preview}"> <i class="fa fa-expand-arrows-alt"></i></button>

                              `;
                            },
                        },

                        {
                            field: 'action',
                            title: '{{ __('misc.file_upload_status') }}',
                            sortable: false,
                            template: function(row) {
                                var upload_status_original = '';
                                var upload_status_preview = '';
                                var note_upload_status = '';
                                if (row.raw !== null) {
                                    upload_status_preview = row.raw.is_uploaded_preview;
                                    upload_status_original = row.raw.is_uploaded_original;
                                    note_upload_status = !upload_status_original || !upload_status_preview?"{{__('misc.the_file_is_being_uploaded'). ' '. __('views.Arabsstock')}}":'';
                                    
                                } else if (row.contributor_video_id && row.contributor_file && row
                                    .contributor_file.raw !== null) {
                                    // If 'row.raw.status' is not available, check 'row.contributor_file.status'
                                    upload_status_preview = row.contributor_file.raw.is_uploaded_preview;
                                    upload_status_original = row.contributor_file.raw.is_uploaded_original;
                                    note_upload_status = !upload_status_original || !upload_status_preview?"{{__('misc.the_file_is_being_uploaded'). ' '. __('global.contributor')}}":'';
                                    
                                }
                                return `
                                <span class="badge mb-1 ${upload_status_preview?'badge-success':'badge-danger'}"> {{__('misc.raw')}}  ${upload_status_preview? '{{__("misc.active")}}': '{{__("misc.pending")}}'} </span>
                                    <span class="badge mb-1 ${upload_status_original?'badge-success':'badge-danger'}"> {{__('misc.raw_preview')}}  ${upload_status_original? '{{__("misc.active")}}': '{{__("misc.pending")}}'} </span>
                                    ${note_upload_status != ''?`<p class="alert alert-warning p-0 mt-1" role="alert">${note_upload_status}</p>`:''}
                                    
                                
                                `;
                            },
                        },
                        {
                            field: 'status',
                            title: '{{ __('views.Status') }}',
                            sortable: false,
                            autoHide: false,
                            width: 150,
                            // callback function support for column rendering
                            template: function(row) {
                                var statuses = {
                                    active: {
                                        'title': '{{ __('views.removebg_active') }}',
                                        'class': ' btn-label-info',
                                        'class_badge': ' badge-info',
                                    },
                                    pending: {
                                        'title': '{{ __('views.removebg_pending') }}',
                                        'class': ' btn-label-danger',
                                        'class_badge': ' badge-danger',
                                    },
                                };
                                var edit_url = '{{ $edit_url }}'.replace('0', row.id)
                                var status_disply = '';
                                var current_status = '';
                                var review_notes = '';
                                if (row.raw !== null) {
                                    status_disply = row.raw.status === 'active' ? 'pending' :'active';
                                    current_status = row.raw.status;
                                    review_notes = row.raw.review_notes;
                                } else if (row.contributor_video_id && row.contributor_file && row
                                    .contributor_file.raw !== null) {
                                    // If 'row.raw.status' is not available, check 'row.contributor_file.status'
                                    status_disply = row.contributor_file.raw.status === 'active' ?
                                        'pending' : 'active';
                                    current_status = row.contributor_file.raw.status;
                                    review_notes = row.contributor_file.raw.review_notes;

                                    
                                }
                                return `
                                <span class="badge ${statuses[current_status].class_badge}" > ${statuses[current_status].title} </span>
                                <button type="button" data-status="${status_disply}" data-edit_url="${edit_url}"  id="statusChange" class="btn btn-bold btn-sm btn-font-sm  statusChange
                                    ${statuses[status_disply].class}"> {{__('views.Update Status')}}</button>
                                ${
                                    current_status === 'pending' && review_notes !== ''?
                                        `
                                        <br/>
                                    <span class="alert alert-danger mt-2">{{__('views.reason')}} :  ${review_notes}</span>   
                                        `:''

                                    
                                }

                                `;
                            }
                        },
                    ]
                });
            }

            // search
            var search = function() {
                $('#statusSelect').on('change', function() {
                    datatable.search($(this).val().toLowerCase(), 'status');
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
                    var review_notes = '';

                    // fetch selected IDs
                    var ids = datatable.rows('.kt-datatable__row--active').nodes().find(
                        '.kt-checkbox--single > [type="checkbox"]').map(function(i, chk) {
                        return $(chk).val();
                    }).toArray();

                    if (ids.length > 0) {
                        var message = $(this).data('action-confirm')
                        
                        var swal_config = {
                            html: message.replace("0", ids.length).replace("ttt", label),
                            type: "info",
                         
                        }
                        if(value === 'pending'){
                        var swal_config = {
                            html: message.replace("0", ids.length).replace("ttt", label),
                            type: "info",
                            input : 'text',
                            preConfirm: (text) => {
                                review_notes = text;
                            }
                        }
                        }
                        swal.fire(swal_config).then(function(result) {
                            if (result.value) {
                                var action_url = $(this).data('action-url').replace('0', ids.join(
                                    ','))
                         
                                call_ajax_request(action_url,{
                                    ids: ids,
                                    status: value,
                                    review_notes: review_notes,
                                    _token: document.querySelector('meta[name=csrf-token]')
                                        .content,
                                });
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
            $(document).on('click', '.fullscreen-video', function() {
                var videoSrc = $(this).data('video_file');

                var fullscreenContainer = $('<div>', {
                    class: 'fullscreen-container',
                    css: {
                        position: 'fixed',
                        top: '0',
                        left: '0',
                        width: '100%',
                        height: '100%',
                        background: '#000',
                        zIndex: '9999',
                        display: 'flex',
                        justifyContent: 'center',
                        cursor: 'zoom-out',
                        objectFit: 'contain',
                        objectPosition: 'center',
                    },
                    html: `
                            <i class="close-icon" style="position: absolute; top: 10px; right: 30px; cursor: pointer; color: white; font-size: 44px;">&times;</i>
                            <video controls autoplay class="fullscreen-video bg-transparent"><source src="${videoSrc}" type="video/webm"></video>
                        `
                });

                $('body').append(fullscreenContainer);

                fullscreenContainer.find('.close-icon').on('click', function() {
                    fullscreenContainer.remove();
                });
            });
            $(document).on('click', 'button.statusChange', function() {
                update_bg_status(this);
            });

        });

        function update_bg_status(elm) {
            var edit_url = $(elm).data('edit_url');
            var status = $(elm).data('status');
            if (typeof edit_url === undefined)
                return;

            var objet = {'status':status};

            if (status === 'pending') {
                Swal.fire({
                    title: '"{{__('views.reason')}}"',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    showLoaderOnConfirm: true,
                    preConfirm: (text) => {
                        if (text) {
                            objet.review_notes =  text;
                            call_ajax_request(edit_url, objet);
                            return;


                        } else {
                            console.log('User canceled the input.');

                        }
                    },
                    allowOutsideClick: () => true
                });
                return ;
            }
            call_ajax_request(edit_url, objet);

        }

        function call_ajax_request(edit_url, objet) {
            $.ajax({
                type: "POST",
                url: edit_url,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                data: objet,
                success: function(data) {
                    Swal.fire({
                    icon: 'success',
                    title: '{{__("misc.success")}}',
                    text: data.msg,
                    })
                    datatable.search($("#statusSelect").val().toLowerCase(), 'status');


                },
                error: function(error) {
                    Swal.fire({
                    icon: 'error',
                    title: 'Oops...Something went wrong!',
                    text: error.responseJSON.msg,
                    })
                
                }
            });
        }
    </script>
@endpush
