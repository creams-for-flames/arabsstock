@extends('admin_v2.layout.app')

@section('content')

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
                                    <div class="form-group row">
                                        <label>{{ trans('misc.category') }}</label>
                                        <select style="width:100%" id="categories_id" name="categories_id[]"
                                                class="form-control">
                                            <option value="">اختر تصنيف</option>
                                            @foreach( $category as $categoryItem )
                                                <option value="{{$categoryItem->id}}">
                                                    {{ $categoryItem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group row">
                                        <label>{{ trans('misc.admin_collection') }}</label>
                                        <select style="width:100%" id="collection_id" name="collection_id"
                                                class="form-control">
                                            <option value="">اختر تجميعة</option>
                                            @foreach( $collection as $collectionItem )
                                                <option value="{{$collectionItem->id}}">
                                                    {{ $collectionItem->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group row">
                                        <label>{{ trans('misc.folder') }}</label>
                                        <select style="width:100%" id="folder" name="folder" class="form-control">
                                            <option value="">اختر مجلد</option>
                                            @foreach( $folders as $folder )
                                                <option value="{{$folder}}">
                                                    {{ $folder }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="alert-html" style="display:none" class="alert alert-danger fade show"
                                         role="alert">
                                        <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
                                        <div class="alert-text"></div>
                                        <div class="alert-close">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
                                                <input type="file" name="file" accept=".jpeg, .jpg, .tiff,.tif" multiple/>
                                            </span>
                                            <button type="submit" class="btn btn-primary start">
                                                <i class="flaticon-upload-1"></i>
                                                <span>بدأ التحميل</span>
                                            </button>
                                            <button type="reset" class="btn btn-warning cancel">
                                                <i class="flaticon-cancel"></i>
                                                <span>إلغاء التحميل</span>
                                            </button>
                                        {{--                                            <button type="button" class="btn btn-danger delete">--}}
                                        {{--                                                <i class="flaticon2-trash"></i>--}}
                                        {{--                                                <span>حذف</span>--}}
                                        {{--                                            </button>--}}

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
        <div class="modal" id="action-required-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __("views.WhatToDo") }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                                onclick="uploadOnlyUniqe()">{{ __("views.UploadOnlyUniqe") }}</button>
                        <button type="button" class="btn btn-danger"
                                onclick="uploadAllWithRepeated()">{{ __("views.UploadAllWithRepeated") }}</button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __("views.Close") }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->

@endsection

@push('css')
    <style>
        .fade.in{
            opacity: 1;
        }
        .modal-backdrop{
            opacity: 0.4;
        }
    </style>
    <link rel="stylesheet" href="https://blueimp.github.io/Gallery/css/blueimp-gallery.min.css"/>
    <link rel="stylesheet" href="{{ asset('uploader/css/jquery.fileupload.css')}}"/>
    <link rel="stylesheet" href="{{ asset('uploader/css/jquery.fileupload-ui.css')}}"/>
@endpush

@push('scripts')


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
            setTimeout(function () {
                var remaining = $('.files tr').length;
                var text = "{{__('views.errorCancelledUpload')}}".replace(':cancelled', total - remaining).replace(':remaining', remaining)
                showAlert(text);
            }.bind(this), 300);
        }

        function uploadAllWithRepeated() {
            $("#action-required-modal").modal("hide")
        }


        $("#folder").select2({tags: true});
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"
            integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f"
            crossorigin="anonymous"></script>
    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="{{ asset('uploader/js/vendor/jquery.ui.widget.js')}}"></script>
    <!-- The Templates plugin is included to render the upload/download listings -->
    <script src="https://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
    <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    <!-- blueimp Gallery script -->
    <script src="https://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload.js')}}"></script>
    <!-- The File Upload processing plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload-process.js')}}"></script>
    <!-- The File Upload image preview & resize plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload-image.js')}}"></script>
    <!-- The File Upload validation plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload-validate.js')}}"></script>
    <!-- The File Upload user interface plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload-ui.js')}}"></script>
    <!-- The main application script -->
    <script src="{{ asset('plugins/select2/select2.js') }}"></script>
    <script>
        var fi = $('#fileupload'); //file input
        fi.fileupload({
            method: 'POST',
            url: '{{ env('MEDIA_URL').'/api/admin/images' }}',
            autoUpload: false,
            headers: {
                'Authorization': 'Bearer {{ $accessToken }}',
                'Language': '{{config('app.locale')}}',
            },
            sequentialUploads: 1,
            limitConcurrentUploads: 1,
            minFileSize: 614400,
            maxFileSize: 52428800,
            //   maxNumberOfFiles:1,
            uploadExtraData: {
                'folder': $("#folder").val(),
                'categories_id': $("#categories_id").val(),
                'collection_id': $("#collection_id").val(),
            },
            loadVideoFileTypes: /^image\/.*$/,
            showUpload: true,
            acceptFileTypes: /(\.|\/)(jpe?g|tiff)$/i,
            success: function (data) {
            },
        });

        fi.on('fileuploadadd', function (e, data) {
            var dataImageName = [];

            for (var i = 0; i < data.files.length; i++) {
                /* data.push([files[i].name, files[i].hash_md5]); */
                dataImageName.push([data.files[i].name, 'no_hash']);
            }


        });
    </script>
@endpush
