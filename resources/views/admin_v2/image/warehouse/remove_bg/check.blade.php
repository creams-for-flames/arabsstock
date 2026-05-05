@if (session()->has('current_id'))
    @php
        $current_id = session()->get('current_id');
    @endphp

    <!-- Use the $data variable in your view -->
@endif
@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--begin::Portlet-->



        <div class="kt-portlet kt-portlet--mobile mb-5 kt-removebg">
            <form action="" id="removeBgForm" method="get">
                <div class="row mb-50 mt-50">

                    <div class="col">
                        <label class="" for="status">{{ __('views.Status') }}</label>
                        <select id="statusSelect" name="status" class="form-control">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>{{__("views.all")}}</option>
                            <option value="pending"
                                {{ request('status') === 'pending' || request('status') === null ? 'selected' : '' }}>
                                {{__("views.removebg_pending")}}
                            </option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{__("views.removebg_active")}}</option>
                        </select>
                    </div>
                    <div class="col">
                        <label class="" for="removebg_type">{{ __('views.removebg_type') }}</label>
                        <select id="removebgTypeSelect" name="removebg_type" class="form-control">
                            <option value="paid" {{ ($removebg_type === 'paid') ? 'selected' : '' }}>{{__("views.paid")}}</option>
                            <option value="manual"
                                {{ $removebg_type === 'manual' || $removebg_type === null ? 'selected' : '' }}>
                                {{__("views.manual")}}
                            </option>
                            <option value="free" {{ $removebg_type === 'free' ? 'selected' : '' }}>{{__("views.free")}}</option>
                        </select>
                    </div>
                    <div class="col mr-2 pl-1">
                        <div class="form-group">
                            <label for="image_id">رقم الصورة </label>
                            <input type="number" min=1 value="{{ request('image_id') }}" name="image_id" id="image_id"
                                class="form-control" placeholder="رقم الصورة">
                        </div>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger reset">reset</button>
                    </div>
                    <div class="col-2">
                        <div class="form">
                            <button type="submit" class="btn btn-dark btn-lg btn-block p-2"
                                id="searchStatus">{{ __('views.Search...') }}</button>

                        </div>

                    </div>

                </div>
            </form>
            <br />
            <div class="row mb-50 ">
                <div class="col ">
                    <fieldset>
                        <div class="row bg-white">
                            <div class="col-6 mb-2">رقم الصورة : <span class="image_id"></span> </div>
                            <div class="col-6 mb-2"> عدد الصور المعتمد/ عدد الصور : <span class="image_count">
                                    <span id="count_approve">{{ $count_approve }}</span>/
                                    <span>
                                        {{ $data->total() }}
                                    </span>

                                </span></div>
                            <div class="col mb-5">تفاصيل الصورة : <span class="image_title"></span></div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <br>
            <hr>
            <div class="row">
                <div class="col">
                    <div id="carouselExampleIndicators" class="carousel slide" data-interval="false">
                        <ol class="carousel-indicators">
                            @foreach ($data as $item)
                                <li data-target="#carouselExampleIndicators" data-slide-to="{{ $loop->index }}"
                                    class="
                                    @if (isset($current_id) && $item->id == $current_id) active 
                                    @elseif($loop->first && !isset($current_id))
                                    active @endif
                                    ">
                                    <span>
                                        {{ $loop->index + 1 }}
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach ($data as $item)
                                <div
                                    class="carousel-item  
                                    @if (isset($current_id) && $item->id == $current_id) active 
                                    @elseif($loop->first && !isset($current_id))
                                    active @endif
                            ">
                                    <img class="d-block w-100 bg-transparent" src="{{ cdn($item->removebg_image) }}"
                                        data-link="{{ $item->post_link }}" data-id="{{ $item->id }}"
                                        data-title="{{ $item->title }}" data-png="{{ cdn($item->removebg_image) }}"
                                        data-jpg="{{ cdn($item->preview) }}"
                                        data-removebg_status_disply="{{ $item->removebg_status_disply }}"
                                        data-update_status_removebg_display="{{ route('admin.images.warehouse_remove_bg.update_status_removebg_display', ['id' => $item->id]) }}"
                                        alt="...">
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>

                </div>
            </div>
            <br>
            <br>
            <div class="row bg-white">
                <div class="col">
                    <div class="row">
                        <div class="btn-group col-3" role="group" aria-label="Basic example">


                            <button type="button" class="btn btn-info float-left approve statusChange" data-edit_url="">

                            </button>


                        </div>
                        <div class="btn-group col-9" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-secondary bg-black float-right darkbg text-white">Dark
                                Background</button>
                            <button type="button" class="btn btn-secondary bg-white float-right bg-light">White
                                Background</button>
                            <button
                                type="button"class="btn btn-secondary file-jpg float-right bg-dark text-white">JPG</button>
                            @if ($check_manual)
                                <a href="javascript:0;"class="btn btn-danger file-download-jpg float-right  text-white"
                                    data-download_file="jpg"> Download JPG</a>
                            @endif
                            <button type="button"class="btn btn-secondary file-png float-right transparent-bg">PNG</button>
                            @if ($check_manual)
                                <a href="javascript:0;" class="btn btn-warning file-download-png float-right"
                                    data-download_file="png"> Download PNG</a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>


        </div>
        <!--end::Portlet-->
    </div>
    <!-- end:: Content -->

    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head"></div>
                            <div class="kt-portlet__body">
                                <form id="fileupload" action="https://jquery-file-upload.appspot.com/" method="POST"
                                    enctype="multipart/form-data">
                                    <!-- Redirect browsers with JavaScript disabled to the origin page -->

                                    <div id="alert-html" style="display:none" class="alert alert-danger fade show"
                                        role="alert">
                                        <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
                                        <div class="alert-text"></div>
                                        <div class="alert-close">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true"><i class="la la-close"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                    <div class="row fileupload-buttonbar">
                                        <div class="col-lg-10">
                                            <!-- The fileinput-button span is used to style the file input field as button -->
                                            <span class="btn btn-success fileinput-button">
                                                <i class="flaticon-plus"></i>
                                                <span>استعراض...</span>
                                                <input type="file" name="file" accept=".png" multiple />
                                                <input type="hidden" min=1 name="id" id="file_id" </span>
                                                <button type="submit" class="btn btn-primary start">
                                                    <i class="flaticon-upload-1"></i>
                                                    <span>بدأ التحميل</span>
                                                </button>
                                                <button type="reset" class="btn btn-warning cancel">
                                                    <i class="flaticon-cancel"></i>
                                                    <span>إلغاء التحميل</span>
                                                </button>
                                                {{--                                            <button type="button" class="btn btn-danger delete"> --}}
                                                {{--                                                <i class="flaticon2-trash"></i> --}}
                                                {{--                                                <span>حذف</span> --}}
                                                {{--                                            </button> --}}

                                                <!-- The global file processing state -->
                                                <span class="fileupload-process"></span>
                                        </div>
                                        <!-- The global progress state -->
                                        <div class="col-lg-2 fileupload-progress fade">
                                            <!-- The global progress bar -->
                                            <div class="progress progress-striped active" role="progressbar"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                                            </div>
                                            <!-- The extended global progress state -->
                                            <div class="progress-extended">&nbsp;</div>
                                        </div>
                                    </div>
                                    <!-- The table listing the files available for upload/download -->
                                    <table role="presentation" class="table table-striped">
                                        <tbody class="files"></tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="action-required-modal" tabindex="-1" role="dialog"
            aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('views.WhatToDo') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="uploadOnlyUniqe()">{{ __('views.UploadOnlyUniqe') }}</button>
                        <button type="button" class="btn btn-danger"
                            onclick="uploadAllWithRepeated()">{{ __('views.UploadAllWithRepeated') }}</button>
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('views.Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

        <div class="row mt-5">
            <div class="col">
                {{ $data->links('pagination::bootstrap-4') }}


            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://blueimp.github.io/Gallery/css/blueimp-gallery.min.css" />
    <link rel="stylesheet" href="{{ asset('uploader/css/jquery.fileupload.css') }}" />
    <link rel="stylesheet" href="{{ asset('uploader/css/jquery.fileupload-ui.css') }}" />
    <style>
        @if ($check_manual)

            .fade.in {
                opacity: 1;
            }

            .modal-backdrop {
                opacity: 0.4;
            }
        @endif

        .kt-removebg {

            background-color: unset !important;
        }


        .darkbg {
            background-color: #172344 !important;

        }

        .transparent-bg {
            background-color: transparent;
            border-color: transparent;
            color: #000;
        }

        ol li {
            text-indent: unset !important;
            height: unset !important;
            text-align: center;
        }

        .carousel-control-prev-icon {
            background-color: #00c3aadb;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M2.75 0l-1.5 1.5L3.75 4l-2.5 2.5L2.75 8l4-4-4-4z'/%3e%3c/svg%3e") !important;
        }

        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M5.25 0l-4 4 4 4 1.5-1.5L4.25 4l2.5-2.5L5.25 0z'/%3e%3c/svg%3e") !important;
            background-color: #00c3aadb;

        }

        .carousel-indicators .active {
            opacity: 1;
            background-color: #00c3aadb;
            color: #fff;
        }

        .bg-transparent {
            background: url({{ asset('img/no_background.jpg') }});
        }

        #searchStatus,
        .reset {
            position: absolute;
            top: 26px;
            left: 7px;
        }

        .reset {
            left: 20px;
        }
    </style>
@endpush


@push('scripts')
    @if ($check_manual)
        {{-- s:file-upload --}}
        <script>
            var myDropzone;
            var uniqueValidation = {
                file_name_conflicts: [],
                file_hash_conflicts: [],
            };
            var total = 0

            function showAlert(text) {
                var node = document.querySelector("#alert-html").cloneNode(true);
                node.removeAttribute("id");
                node.style.display = "flex";
                node.querySelector(".alert-text").innerText = text;
                document.querySelector("#alert-html").before(node);
            }

            function uploadOnlyUniqe() {
                $("#action-required-modal").modal("hide")
                total = $('.files tr').length;
                var files = uniqueValidation.file_name_conflicts;
                for (var i = 0; i < files.length; i++) {
                    $('.files tr[data-name="' + files[i] + '"] .cancel').click()
                }
                files = uniqueValidation.file_hash_conflicts;
                for (var i = 0; i < files.length; i++) {
                    $('.files tr[data-name="' + files[i] + '"] .cancel').click()
                }
                setTimeout(function() {
                    var remaining = $('.files tr').length;
                    var text = "{{ __('views.errorCancelledUpload') }}".replace(':cancelled', total - remaining)
                        .replace(':remaining', remaining)
                    showAlert(text);
                }.bind(this), 300);
            }

            function uploadAllWithRepeated() {
                $("#action-required-modal").modal("hide")
            }


            $("#folder").select2({
                tags: true
            });
            $("#categories_id").select2({});
            $("#collection_id").select2({});
        </script>
        <!-- The template to display files available for upload -->
        <script id="template-upload" type="text/x-tmpl">

{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-upload fade" data-name="{%=file.name%}">
      <td>
            <span class="preview">
              {% if (file.thumbnailUrl) { %}
                  <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
              {% } %}
          </span>
      </td>
      <td>
          {% if (window.innerWidth > 480 || !o.options.loadImageFileTypes.test(file.type)) { %}
              <p class="name">{%=file.name%}</p>
          {% } %}
          <strong class="error text-danger"></strong>
      </td>
      <td>
          <p class="size">Processing...</p>
          <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
      </td>
      <td>
          {% if (!o.options.autoUpload && o.options.edit && o.options.loadImageFileTypes.test(file.type)) { %}
            <button class="btn btn-success edit" data-index="{%=i%}" disabled>
                <i class="glyphicon glyphicon-edit"></i>
                <span>Edit</span>
            </button>
          {% } %}
          {% if (!i && !o.options.autoUpload) { %}


              <button class="btn btn-primary start" disabled>
                  <i class="flaticon-upload-1"></i>
                  <span>بدأ التحميل </span>
              </button>
          {% } %}
          {% if (!i) { %}
              <button class="btn btn-warning cancel">
<i class="flaticon-cancel"></i>
                  <span>إلغاء  </span>
              </button>
          {% } %}
      </td>
  </tr>
{% } %}



</script>
        <!-- The template to display files available for download -->
        <script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-download" fade{%=file.thumbnailUrl?' image':''%}">
      <td>
          <span class="preview">
              {% if (file.thumbnailUrl) { %}
                  <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
              {% } %}
          </span>
      </td>
      <td>
          {% if (window.innerWidth > 480 || !file.thumbnailUrl) { %}
              <p class="name">
                  {% if (file.url) { %}
                      <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                  {% } else { %}
                      <span>{%=file.name%}</span>
                  {% } %}
              </p>
          {% } %}
          {% if (file.error) { %}
              <div><span class="label label-danger">Error</span> {%=file.error%}</div>
          {% } %}
      </td>
      <td>
          <span class="size">{%=o.formatFileSize(file.size)%}</span>
      </td>
      <td>
          {% if (file.deleteUrl) { %}
              <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                  <i class="glyphicon glyphicon-trash"></i>
                  <span>Delete</span>
              </button>
              <input type="checkbox" name="delete" value="1" class="toggle">
          {% } else { %}
              <button class="btn btn-warning cancel">
                  <i class="glyphicon glyphicon-ban-circle"></i>
                  <span>Cancel</span>
              </button>
          {% } %}
      </td>
  </tr>
{% } %}



</script>
        <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
        <script src="{{ asset('uploader/js/vendor/jquery.ui.widget.js') }}"></script>
        <!-- The Templates plugin is included to render the upload/download listings -->
        <script src="https://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
        <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
        <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
        <!-- The Canvas to Blob plugin is included for image resizing functionality -->
        <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>

        <!-- blueimp Gallery script -->
        <script src="https://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
        <!-- The basic File Upload plugin -->
        <script src="{{ asset('uploader/js/jquery.fileupload.js') }}"></script>
        <!-- The File Upload processing plugin -->
        <script src="{{ asset('uploader/js/jquery.fileupload-process.js') }}"></script>
        <!-- The File Upload image preview & resize plugin -->
        <script src="{{ asset('uploader/js/jquery.fileupload-image.js') }}"></script>
        <!-- The File Upload validation plugin -->
        <script src="{{ asset('uploader/js/jquery.fileupload-validate.js') }}"></script>
        <!-- The File Upload user interface plugin -->
        <script src="{{ asset('uploader/js/jquery.fileupload-ui.js') }}"></script>
        <!-- The main application script -->
        <script src="{{ asset('plugins/select2/select2.js') }}"></script>
        <script>
            var fi = $('#fileupload'); //file input
            fi.fileupload({
                method: 'POST',
                url: '{{ env('MEDIA_URL') . '/api/admin/images/removebg/replace' }}',
                autoUpload: false,
                headers: {
                    'Authorization': 'Bearer {{ $accessToken }}',
                    'Language': '{{ config('app.locale') }}',
                },
                sequentialUploads: 1,
                limitConcurrentUploads: 1,
                minFileSize: 614400,
                maxFileSize: 52428800,
                maxNumberOfFiles: 1,

                loadVideoFileTypes: /^image\/.*$/,
                showUpload: true,
                acceptFileTypes: /(\.|\/)(png)$/i,
                success: function(data) {
                    var version = new Date().getTime();
                    var carouselItem = $('.carousel-item.active').find('img');
                    var oldImg =carouselItem.data('png');
                    var newImg =`${oldImg}?v=${version}`;
                    carouselItem.attr('data-png', newImg);
                    carouselItem.attr('src', newImg);
                    $('.carousel-item.active').removeClass('bg-white').addClass('bg-transparent darkbg');


                },
            });

            fi.on('fileuploadadd', function(e, data) {
                var dataImageName = [];

                for (var i = 0; i < data.files.length; i++) {
                    /* data.push([files[i].name, files[i].hash_md5]); */
                    dataImageName.push([data.files[i].name, 'no_hash']);
                }


            });
        </script>
        {{-- e:file-upload --}}
    @endif

    <script>
        var downloadImage = "{{ $downloadImage }}";
        $(document).ready(function() {
            var showErrorMsg = function(form, type, msg) {
                var alert = $('<div class="alert alert-' + type + ' alert-dismissible" role="alert">\
                                <div class="alert-text">' + msg + '</div>\
                                <div class="alert-close">\
                                    <i class="flaticon2-cross kt-icon-sm" data-dismiss="alert"></i>\
                                </div>\
                            </div>');

                form.find('.alert').remove();
                alert.prependTo(form);
                //alert.animateClass('fadeIn animated');
                KTUtil.animateClass(alert[0], 'fadeIn animated');
                alert.find('span').html(msg);
            }
            $('.carousel').carousel({
                interval: false,
            });
            $('.carousel').carousel('pause');

            $('.carousel').on('slid.bs.carousel', function(e) {
                callActiveImage();
            });
            callActiveImage();
            // Button click event handling
            $('.btn').on('click', function() {
                var $carouselItem = $('.carousel-item.active');

                // Change background color
                if ($(this).hasClass('bg-black')) {
                    $carouselItem.find('img').removeClass('bg-transparent bg-white').addClass('darkbg');
                } else if ($(this).hasClass('bg-white')) {
                    $carouselItem.find('img').removeClass('bg-transparent darkbg').addClass('bg-white');
                }

                var $carouselImage = $carouselItem.find('img');
                var currentImageSrc = $carouselImage.attr('src');
                // Change file type
                if ($(this).hasClass('file-jpg')) {
                    var jpgSrc = $carouselImage.data('jpg');
                    if (jpgSrc) {
                        $carouselImage.attr('src', jpgSrc);
                    }
                } else if ($(this).hasClass('file-png')) {
                    var pngSrc = $carouselImage.data('png');
                    if (pngSrc) {
                        $carouselImage.attr('src', pngSrc);
                        $carouselItem.find('img').removeClass('bg-white').addClass('bg-transparent darkbg');

                    }
                }
            });
            $('button.statusChange').on('click', function() {
                var edit_url = $(this).data('edit_url');
                var $carouselItem = $('.carousel-item.active').find('img');
                var status = $carouselItem.data('removebg_status_disply');


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
                    success: function(response) {
                        $removebg_status_disply = response.data.removebg_status_disply ??
                            $status;
                        var status = $carouselItem.data('removebg_status_disply',
                            $removebg_status_disply);
                        $('#count_approve').text(response.count_approve);
                        update_button_approve($removebg_status_disply);
                        Swal.fire(
                            "{{ __('update status') }}",
                            response.message,
                            'success'
                        )



                        // window.location.reload();
                    },
                    error: function(error) {
                        alertError("", error)
                    }
                });

            });
            $(".reset").click(function() {
                var form = $(this).closest('form');
                form.find("input").val("");
                form.find("select").prop("selectedIndex", 1);

            });

        });


        function callActiveImage() {
            var $carouselItem = $('.carousel-item.active').find('img');
            if (typeof($carouselItem) != 'undefined') {
                $id = $carouselItem.data('id');
                $('#file_id').val($id);
                $('.image_id').text($id);
                $('.statusChange').data('edit_url', $carouselItem.data('update_status_removebg_display'));
                $removebg_status_disply = $carouselItem.data('removebg_status_disply');
                $class_approve = $('.approve');
                update_button_approve($removebg_status_disply);
                @if ($check_manual)
                    $('.file-download-jpg').attr('href', downloadImage.replace('/0/', `/${$id}/`).replace(':filetype',
                        'jpg'));
                    $('.file-download-png').attr('href', downloadImage.replace('/0/', `/${$id}/`).replace(':filetype',
                        'png'));
                @endif
                if ($carouselItem.data('link') != undefined && $carouselItem.data('title') != undefined) {
                    $('.image_title').html(`
                    <a href="${$carouselItem.data('link')}" target="_blank" >${$carouselItem.data('title')}</a>
                    `);

                }
            }

        }

        function update_button_approve($removebg_status_disply) {
            $class_approve = $('.statusChange');
            $class_approve.text($removebg_status_disply === 'active' ?
                'الغاء الاعتماد' : 'اعتماد');
            if ($removebg_status_disply === 'active') {

                $class_approve.addClass('btn-danger').removeClass('btn-info');
            } else {
                $class_approve.addClass('btn-info').removeClass('btn-danger');


            }
        }
    </script>

@endpush
