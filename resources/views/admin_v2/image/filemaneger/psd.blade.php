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
                            <div class="kt-portlet__body">
                                <form id="fileupload" action="{{ env('MEDIA_URL').'/api/admin/images/psd' }}"
                                      method="POST"
                                      enctype="multipart/form-data">
                                    {{ csrf_field() }}
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
                                    <div class="row fileupload-buttonbar">
                                        <div class="col-lg-10">
<span class="btn btn-success fileinput-button">
<i class="flaticon-plus"></i>
<span>استعراض...</span>
<input type="file" name="file" multiple/>
</span>
                                            <button type="submit" class="btn btn-primary start">
                                                <i class="flaticon-upload-1"></i>
                                                <span>بدأ التحميل</span>
                                            </button>
                                            <button type="reset" class="btn btn-warning cancel">
                                                <i class="flaticon-cancel"></i>
                                                <span>إلغاء التحميل</span>
                                            </button>
                                            <button type="button" class="btn btn-danger delete">
                                                <i class="flaticon2-trash"></i>
                                                <span>حذف</span>
                                            </button>
                                            <span class="fileupload-process"></span>
                                        </div>
                                        <div class="col-lg-2 fileupload-progress fade">
                                            <div class="progress progress-striped active" role="progressbar"
                                                 aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                                            </div>
                                            <div class="progress-extended">&nbsp;</div>
                                        </div>
                                    </div>
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

<button  class="btn btn-primary start" disabled>
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
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
    </div>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
<tr class="template-download fade" data-file-name="{%=file.name%}">
<td>
<span class="preview">
{% if (file.thumbnailUrl) { %}
<span><img src="{%=file.base64%}" style="height: 60px"></span>
{% } %}
</span>
</td>
<td>
{% if (window.innerWidth > 480 || !file.thumbnailUrl) { %}
<p class="name mt-3">
{% if (file.url) { %}
<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
{% } else { %}
<span>{%=file.name%}</span>
{% } %}
</p>
{% } %}
{% if (file.error) { %}
<div class="text-danger"><span class="label label-danger"></span> {%=file.error%}</div>
{% } %}
</td>
<td>
<p class="size mt-3">{%=o.formatFileSize(file.size)%}</span>
</td>
<td data-result-image>
<div class="preview pt-3"><div class="spinner-border"></div></div>
</td>
<td data-result-title>
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
        var fi = $('#fileupload');
        var files = [];
        fi.fileupload({
            method: 'POST',
            url: '{{ env('MEDIA_URL').'/api/admin/images/psd' }}',
            autoUpload: true,
            headers: {
                'Authorization': 'Bearer {{ $accessToken }}',
                'Language': '{{config('app.locale')}}',
            },
            sequentialUploads: 1,
            limitConcurrentUploads: 1,
            loadVideoFileTypes: /^image\/.*$/,
            showUpload: true,
            allowedFileExtensions: ['psd'],
            minFileSize: 614400,  // 600 kB
// maxFileSize: 100^100, // 100 MB
            success: function (data) {
            },
            error: function (jqXHR, textStatus, errorThrown) {
                printErrorMsg(jqXHR.responseJSON.errors);
            }
        });
    </script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;
        // Initiate the Pusher JS library
        var pusher = new Pusher('e99e44458cd4d490f834', {
            cluster: 'ap2',
            encrypted: true
        });
        //

        var channel = pusher.subscribe('fe-channel');
        channel.bind('image-found', function (data) {
            console.log(data);
            if (data.image) {
                $('[data-file-name="' + data.uploaded_file + '"] [data-result-image]').html(`<span class="preview">
<a href="` + data.image.url + `"
target="_blank" >
<img src="` + data.image.thumbnail + `" style="height: 60px"></a>
</span>`);
                $('[data-file-name="' + data.uploaded_file + '"] [data-result-title]').html(`<p class="name mt-3">
<a href="` + data.image.url + `" target="_blank" >` + data.image.title + `</a></p>`)
            } else {
                $('[data-file-name="' + data.uploaded_file + '"] [data-result-image]').html(`<div class="preview pt-3" style="color: #fd397a;"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg></div>`);
            }
        });
    </script>
@endpush
