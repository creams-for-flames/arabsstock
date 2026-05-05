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
                            <div class="kt-portlet__head">
                                <div class="alert">
                                    {{ __('views.Username') }} : {{ $file->warehouseable->user->name??'_' }}
                                    <br> {{ __('مجلد') }} : {{ $file->warehouseable->folder->folder??'_' }}
                                    <br> {{ __('الاسم الاساسي للملف') }} : {{ $file->warehouseable->original_name??'_' }}
                                    <br> {{ __('تاريخ الرفع') }} : {{ $file->warehouseable->{$date_name}??'_' }}

                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <form id="fileupload" action="{{url('')}}" method="POST" enctype="multipart/form-data">
                                    <!-- Redirect browsers with JavaScript disabled to the origin page -->

                                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                    <div class="row fileupload-buttonbar">
                                        <div class="col-lg-10">
                                            <!-- The fileinput-button span is used to style the file input field as button -->
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

                                            <!-- The global file processing state -->
                                            <span class="fileupload-process"></span>
                                        </div>
                                        <!-- The global progress state -->
                                        <div class="col-lg-2 fileupload-progress fade">
                                            <!-- The global progress bar -->
                                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
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

    </div>
    <!-- end:: Content -->

@endsection

@push('css')
    <style>
        .fade.in {
            opacity: 1;
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
                $('.files tr[data-name="'+files[i]+'"] .cancel').click()
            }
            files = uniqueValidation.file_hash_conflicts;
            for (var i = 0; i < files.length; i++) {
                $('.files tr[data-name="'+files[i]+'"] .cancel').click()
            }
            setTimeout(function(){
                var remaining = $('.files tr').length;
                var text = "{{__('views.errorCancelledUpload')}}".replace(':cancelled', total - remaining).replace(':remaining', remaining)
                showAlert(text);
            }.bind(this), 300);
        }

        function uploadAllWithRepeated() {
            $("#action-required-modal").modal("hide")
        }



    </script>
    <script type="text/javascript">
      $(document).on('click', '#avatar_file', function () {

        var _this = $(this);
        $("#uploadAvatar").trigger('click');
        _this.blur();
      });

      //Flat red color scheme for iCheck


      $('#removePhoto').click(function () {
        $('#filePhoto').val('');
        $('#title').val('');
        $('.preview').css({backgroundImage: 'none'}).hide();
        $('.filer-input-dragDrop').removeClass('hoverClass');
      });

      //================== START FILE IMAGE FILE READER
      $("#filePhoto").on('change', function () {

        var loaded = false;
        if (window.File && window.FileReader && window.FileList && window.Blob) {
          if ($(this).val()) { //check empty input filed
            oFReader = new FileReader(), rFilter = /^(?:image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/png|image)$/i;
            if ($(this)[0].files.length === 0) {
              return;
            }


            var oFile = $(this)[0].files[0];
            var fsize = $(this)[0].files[0].size; //get file size
            var ftype = $(this)[0].files[0].type; // get file type


            if (!rFilter.test(oFile.type)) {
              $('#filePhoto').val('');
              $('.popout').addClass('popout-error').html("{{ trans('misc.formats_available') }}").fadeIn(500).delay(5000).fadeOut();
              return false;
            }

            var allowed_file_size = {{$settings->file_size_allowed * 1024}};

            if (fsize > allowed_file_size) {
              $('#filePhoto').val('');
              $('.popout').addClass('popout-error').html("{{trans('misc.max_size').': '.App\Helper::formatBytes($settings->file_size_allowed * 1024)}}").fadeIn(500).delay(5000).fadeOut();
              return false;
            }
              <?php $dimensions = explode('x', $settings->min_width_height_image); ?>

                oFReader.onload = function (e) {

                var image = new Image();
                image.src = oFReader.result;

                image.onload = function () {

                  if (image.width < {{ $dimensions[0] }}) {
                    $('#filePhoto').val('');
                    $('.popout').addClass('popout-error').html("{{trans('misc.width_min',['data' => $dimensions[0]])}}").fadeIn(500).delay(5000).fadeOut();
                    return false;
                  }

                  if (image.height < {{ $dimensions[1] }} ) {
                    $('#filePhoto').val('');
                    $('.popout').addClass('popout-error').html("{{trans('misc.height_min',['data' => $dimensions[1]])}}").fadeIn(500).delay(5000).fadeOut();
                    return false;
                  }


                  $('.preview').css({backgroundImage: 'url(' + e.target.result + ')'}).show();
                  $('.filer-input-dragDrop').addClass('hoverClass');
                  var _filname = oFile.name;
                  var fileName = _filname.substr(0, _filname.lastIndexOf('.'));
                  $('#title').val(fileName);
                };// <<--- image.onload


              };

            oFReader.readAsDataURL($(this)[0].files[0]);

          }
        } else {
          $('.popout').html('Can\'t upload! Your browser does not support File API! Try again with modern browsers like Chrome or Firefox.').fadeIn(500).delay(5000).fadeOut();
          return false;
        }
      });


      $('input[type="file"]').attr('title', window.URL ? ' ' : '');


    </script>

    <script>
      $(document).on("change", ".file_multi_video", function (evt) {
        var $source = $("#video_here");
        $source[0].src = URL.createObjectURL(this.files[0]);
        $source.parent()[0].load();
      });
    </script>
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
        <div class="slides"></div>
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>
    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">
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
        <tr class="template-download fade">
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
                    <div class="text-danger"><span class="label label-danger"></span> {%=file.error%}</div>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>
    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="{{ asset('uploader/js/vendor/jquery.ui.widget.js')}}"></script>
    <!-- The Templates plugin is included to render the upload/download listings -->
    <script src="https://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
    <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <!-- blueimp Gallery script -->
    <script src="https://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload.js')}}"></script>
    <!-- The File Upload processing plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload-process.js')}}"></script>

    <!-- The File Upload video preview plugin -->
    <script src="{{ asset('uploader/js/jquery.fileupload-video.js')}}"></script>
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
            maxChunkSize: 30000000, // 10 MB
            timeout: 300000000,
            url: '{{ env('MEDIA_URL').'/api/admin/videos/' . $file->warehouseable_id . '/admin_file_reupload_not_found' }}',
            autoUpload: false,
            headers: {
                'Authorization': 'Bearer {{ $accessToken }}',
                'Language': '{{config('app.locale')}}',
            },
            acceptFileTypes: /(\.|\/)(mp4|mov|mpeg|flv|mkv)$/i,
            minFileSize: 4194304, // 4Mb,
            sequentialUploads: 1,
            limitConcurrentUploads: 1,
            maxNumberOfFiles:1,
            //  maxFileSize:  20971520, // 20 MB = 20971520 byte
            loadVideoFileTypes: /^video\/.*$/,
            showUpload: true,
            success: function (data) {
                window.files_count_success--
                window.files_count_success_arabsstock = +1;
                if (window.files_count_success === 0 && window.files_count_success_arabsstock > 0) {
                    var element = document.getElementById("next");
                    element.classList.toggle("isDisabled");
                    $('#one').removeClass('doUpload');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                window.files_count_success--
                if (window.files_count_success === 0 && window.files_count_success_arabsstock > 0) {
                    var element = document.getElementById("next");
                    element.classList.toggle("isDisabled");
                    $('#one').removeClass('doUpload');
                }
            }
        });
        fi.on('fileuploadadd', function (e, data) {
            $('#one').prop("disabled", false);
        });
        fi.on('fileuploadchunkdone', function (e, data) {
            console.log(data)
        })
    </script>

@endpush
