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
                            <option value="all" selected>{{ __('views.all') }}</option>
                            <option value="pending">{{ __('views.removebg_pending') }}</option>
                            <option value="active">{{ __('views.removebg_active') }}</option>
                        </select>
                    </div>
                    <div class="col m-3">
                        <label class="" for="removebg_type">{{ __('views.removebg_type') }}</label>
                        <select id="removebgTypeSelect" name="removebg_type" class="form-control">
                            <option value="paid" selected>{{__("views.paid")}}</option>
                            <option value="manual">{{__("views.manual")}}</option>
                            <option value="free" >{{__("views.free")}}</option>
                        </select>
                    </div>
                </div>
                <br />
                <!--begin: Datatable -->
                <div class="kt-datatable" id="kt_image_removebg_list_datatable"></div>
                <!--end: Datatable -->
            </div>
        </div>
        <!--end::Portlet-->
    </div>
    <!--begin::Modal-->
    <div class="modal fade" id="kt_modal_4_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="idModalLabel">#</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body" id="removebgModal">


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('views.Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!--end::Modal-->
    <!-- end:: Content -->
@endsection

@push('css')
    <style>
        #fullscreen-image {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            z-index: 9999;
        }

        .bg-transparent {
            background: url({{ asset('img/no_background.jpg') }});
        }

        .darkbg {
            background-color: #172344 !important;

        }

        .transparent-bg {
            background-color: transparent;
            border-color: transparent;
            color: #000;
            /* Change the text color to ensure visibility */
        }
    </style>
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
                datatable = $('#kt_image_removebg_list_datatable').KTDatatable({
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
                            field: 'removebg_preview',
                            title: '{{ __('misc.removebg_preview') }}',
                            sortable: false,
                            template: function(row) {

                                return ` 
                              <a href="${row.post_link}" target="_blank">
                                <img src="{{ cdn('') }}/${row.removebg_preview}" width="110" class="img-thumbnail bg-transparent " />
                                </a>
                              <button title="fullscreen" class=" fullscreen-image btn btn-light p-2 m-1 " data-imagebg="{{ cdn('') }}/${row.removebg_watermark}"> <i class="fa fa-expand-arrows-alt"></i></button>
                              
                              `;
                            },
                        },
                        {
                            field: 'thumbnail',
                            title: '{{ __('misc.thumbnail') }}',
                            sortable: false,
                            template: function(row) {
                                return ` 
                                <a href="${row.post_link}" target="_blank">

                                <img src="{{ cdn('') }}/${row.thumbnail}" width="110" class="img-thumbnail  " />
                                </a>
                                <button title="fullscreen" class=" fullscreen-image btn btn-light p-2 m-1 " data-imagebg="{{ cdn('') }}/${row.search_large}"> <i class="fa fa-expand-arrows-alt"></i></button>

                              `;
                            },
                        },
                        {
                            field: 'removebg_status_disply',
                            title: '{{ __('views.Status') }}',
                            autoHide: false,
                            width: 150,
                            // callback function support for column rendering
                            template: function(row) {
                                var statuses = {
                                    active: {
                                        'title': '{{ __('views.removebg_active') }}',
                                        'class': ' btn-label-info'
                                    },
                                    pending: {
                                        'title': '{{ __('views.removebg_pending') }}',
                                        'class': ' btn-label-danger'
                                    },
                                };
                                var edit_url = '{{ $edit_url }}'.replace('0', row.id)
                                var removebg_status_disply = row.removebg_status_disply ===
                                    'active' ? 'pending' : 'active';
                                return ` <button type="button" data-status="${removebg_status_disply}" data-edit_url="${edit_url}"  id="statusChange" class="btn btn-bold btn-sm btn-font-sm  statusChange
                                    ${statuses[removebg_status_disply].class}"> ${statuses[removebg_status_disply].title}</button>`;
                            }
                        }, {
                            field: "Actions",
                            width: 80,
                            title: "{{ __('views.Actions') }}",
                            sortable: false,
                            autoHide: false,
                            overflow: 'visible',
                            template: function(row) {
                                var edit_url = '{{ $edit_url }}'.replace('0', row.id)

                                return `<button type="button"  class="btn btn-primary" data-toggle="modal" data-target="#kt_modal_4_2" onclick="openModal(${row.id},'{{ cdn('') }}/${row.removebg_image}','${row.removebg_status_disply}','{{ cdn('') }}/${row.search_large}','${edit_url}')">{{ __('views.check') }}</button>`;
                            },
                        }
                    ]
                });
            }

            // search
            var search = function() {
                $('#statusSelect').on('change', function() {
                    datatable.search($(this).val().toLowerCase(), 'status');
                });
                $('#removebgTypeSelect').on('change', function() {
                    datatable.search($(this).val().toLowerCase(), 'removebg_type');
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
            $('#kt_image_removebg_list_datatable').on('click', '.fullscreen-image', function() {
                var imageSrc = $(this).data('imagebg');

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
                    html: '<img src="' + imageSrc +
                        '" class="fullscreen-image bg-transparent" alt="Image">'
                });

                $('body').append(fullscreenContainer);


                fullscreenContainer.on('click', function() {
                    $(this).remove();
                });
            });
            $('#kt_image_removebg_list_datatable ').on('click', 'button.statusChange', function() {
                update_bg_status(this);
            });
            $('#removebgModal ').on('click', 'button.statusChange', function() {
                update_bg_status(this);
            });
            $('#kt_modal_4_2').on('show.bs.modal', function(event) {

                $('.btn').on('click', function() {
                    var $carouselItem = $('#removebgModal');
                    var image = $('#removebgModal img');
                    // Change background color
                    if ($(this).hasClass('bg-black')) {
                        image.addClass('darkbg').removeClass('bg-transparent bg-white');
                    } else if ($(this).hasClass('bg-white')) {
                        image.addClass('bg-white').removeClass('bg-transparent darkbg');
                    }

                    var $carouselImage = $carouselItem.find('img');
                    var currentImageSrc = $carouselImage.attr('src');
                    // Change file type
                    if ($(this).hasClass('file-jpg')) {
                        var jpgSrc = false;
                        jpgSrc = $(this).data('jpg');
                        if (jpgSrc) {
                            image.attr('src', jpgSrc);
                        }
                    } else if ($(this).hasClass('file-png')) {
                        var pngSrc = $carouselItem.find('button.file-png').data('png');
                        if (pngSrc) {
                            image.attr('src', pngSrc);
                            image.addClass('bg-transparent darkbg').removeClass('bg-white');

                        }
                    }
                });
            });
            $("#kt_modal_4_2").on("hidden.bs.modal", function() {
                $("#removebgModal").html("");
            });
        });

        function openModal(id, removebg_image, removebg_display_status, search_large, edit_url) {

            $('#removebgModal').html(
                `
                  <div>
                          <img src="" class="img-fluid bg-transparent" alt="image">
                          <div class="row">
                              <div class="col-2">
                                  <button type="button"
                                      class="btn btn-info float-left statusChange w-100">{{ __('views.removebg_active') }}</button>

                              </div>
                              <div class="btn-group col-10" role="group" aria-label="Basic example">
                                  <button type="button" class="btn btn-secondary bg-black float-right darkbg text-white">
                                      {{ __('views.DarkBackground') }}</button>
                                  <button type="button"
                                      class="btn btn-secondary bg-white float-right bg-light">{{ __('views.WhiteBackground') }}</button>
                                  <button
                                      type="button"class="btn btn-secondary file-jpg float-right bg-dark text-white">JPG</button>
                                  <button type="button"class="btn btn-secondary file-png float-right transparent-bg">PNG</button>


                              </div>

                          </div>

                        </div>
                  `
            );
            $('#removebgModal img').attr('src', removebg_image);
            $("#kt_modal_4_2 #idModalLabel").text('# ' + id);
            $('#removebgModal button.file-png').attr('data-png', removebg_image);
            $('#removebgModal button.file-jpg').attr('data-jpg', search_large);
            removebg_display_status = removebg_display_status === 'active' ? 'pending' : 'active';
            $('#removebgModal button.statusChange').attr('data-status', removebg_display_status);
            $('#removebgModal button.statusChange').attr('data-edit_url', edit_url);

            if (removebg_display_status === 'pending') {
                $('#removebgModal button.statusChange').text("{{ __('views.removebg_pending') }}");
                $('#removebgModal button.statusChange').addClass('btn-danger').removeClass('btn-info');

            }

        }

        function update_bg_status(elm) {
            var edit_url = $(elm).data('edit_url');
            var status = $(elm).data('status');
            if (typeof edit_url === undefined)
                return;


            $.ajax({
                type: "POST",
                url: edit_url,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                data: {
                    status: status
                },
                success: function(data) {
                    window.location.reload();
                },
                error: function(error) {
                    alertError("", error)
                }
            });
        }
    </script>
@endpush
