@extends('admin_v2.layout.app')

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content" style="margin-left:0;">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet kt-portlet--tabs">

                            <div class="kt-portlet__body">

                                <form id="myForm" method="post" action="" >
                                    @csrf
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                                            <div class="kt-form kt-form--label-right">
                                                <div class="kt-form__body">
                                                    <div class="kt-section kt-section--first">
                                                        <div class="kt-section__body">
                                                            <div class="errors kt-margin-t-20 kt-margin-b-40">
                                                                @if ($errors->any())
                                                                    <div class="alert alert-solid-danger alert-bold fade show kt-margin-t-20 kt-margin-b-40" role="alert">
                                                                        <div class="alert-icon"><i class="fa fa-exclamation-triangle"></i>
                                                                        </div>
                                                                        <div class="alert-text">{{__('views.Oops, something went wrong! Please check the errors below.')}}
                                                                            {!! implode('', $errors->all('<div>:message</div>')) !!}
                                                                        </div>
                                                                        <div class="alert-close">
                                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                                <span aria-hidden="true"><i class="la la-close"></i></span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-xl-1 col-lg-1 col-form-label">{{__('views.Title')}} </label>
                                                                <div class="col-lg-11 col-xl-11">
                                                                    <div class="input-group validated">
                                                                        <input type="text" value="{{old('title')}}" name="title" class="form-control" placeholder="{{__('views.Title')}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-xl-3 col-lg-1 col-form-label text-center">{{ __('views.To') }}</label>
                                                                <div class="col-lg-11 col-xl-8">
                                                                    <div class="input-group validated">
                                                                        <select class="form-control" name="target">
                                                                            <option
                                                                                {{old('target') === 'all' ? 'selected' : ''}} value="all">{{__('views.All Users')}}</option>
                                                                            <option
                                                                                {{old('target') === 'custom' ? 'selected' : ''}} value="custom">{{__('views.specific')}}</option>
                                                                        </select>
                                                                        <div class="invalid-feedback">{{$errors->first('target')}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row " id="custom-target" style="display: none;">
                                                                <label class="col-xl-3 col-lg-1 col-form-label text-center">{{ __('views.specific') }}</label>
                                                                <div class="col-lg-11 col-xl-8">
                                                                    <div class="input-group">
                                                                        <select name="custom_target[]" multiple id="custom_target" style="width: 100%;" data-placeholder="اختر مستخدمين"></select>
                                                                        <div class="invalid-feedback">{{$errors->first('custom_target')}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <fieldset style="border-radius:10px;padding:30px; border:2px solid rgb(192, 192, 192);">
                                                                <div class="alert alert-info" role="alert">
                                                                    <span class="pr-1 pl-1">
                                                                        {{__("admin.categories_count")}}

                                                                    </span>
                                                                    <span class="pl-2 pr-2 badge badge-light" id="categories_count"> 0 </span>
                                                                    <span class="pr-1 pl-1">
                                                                        {{__("admin.files_count")}}

                                                                    </span>
                                                                    <span class="pl-2 pr-2 badge badge-light" id="files_count"> 1 </span>

                                                                  </div>
                                                                <legend>{{__("admin.add")}}</legend>
                                                                        <button type="button" class="btn btn-primary mb-3" id="addRow">{{__("admin.add")}}</button>
                                                                <br>
                                                                <div class="rowContainer">
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="file_type[]">{{__('admin.categories')}}</label>
                                                                                <select name="file_type[]" class="form-control file_type" id="file_type">
                                                                                    @foreach ($file_type as $type)
                                                                                    <option @if ($type === 'file') selected @endif label="{{__("admin.".$type)}}">{{$type}}</option>

                                                                                    @endforeach
                                                                                </select>
                                                                              </div>
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="fileable_type[]">{{__("admin.type")}}</label>
                                                                                <select name="fileable_type[]" class="form-control" id="fileable_type[]">
                                                                                    @foreach ($fileable_type as $type)
                                                                                    <option label="{{__("admin.".$type)}}" >{{$type}}</option>

                                                                                    @endforeach
                                                                                </select>
                                                                              </div>
                                                                        </div>
                                                                        <div class="col file_title d-none">
                                                                            <div class="form-group">
                                                                                <label for="file_title[]">{{__("admin.title")}}</label>
                                                                                <input name="file_title[]" type="text" class="form-control" id="file_title[]" placeholder="{{__("admin.title")}}">
                                                                              </div>
                                                                        </div>
                                                                        <div class="col file_url d-none">
                                                                            <div class="form-group">
                                                                                <label for="file_url[]">{{__("admin.link")}}</label>
                                                                                <input name="file_url[]" type="text" class="form-control" id="file_url[]" placeholder="{{__("admin.link")}}">
                                                                              </div>
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="fileable_id[]">{{__("admin.file_number")}}</label>
                                                                                <input min="1" name="fileable_id[]" type="number" class="form-control" id="fileable_id[]" placeholder="{{__("admin.file_number")}}">
                                                                              </div>
                                                                        </div>
                                                                        <span class="delete">
                                                                            <button type="button" class="btn btn-danger deleteRow" >{{__("admin.delete")}}</button>
                                                                        </span>

                                                                    </div>

                                                                </div>
                                                            </fieldset>
                                                            <br>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="kt-separator kt-separator--space-lg kt-separator--fit kt-separator--border-solid"></div>
                                                <div class="kt-form__actions">
                                                    <div class="row">
                                                        <div class="col-xl-3"></div>
                                                        <div class="col-lg-9 col-xl-8">
                                                            <button class="btn btn-label-brand btn-bold" type="submit">حفظ</button>
                                                            <a class="btn btn-clean btn-bold" href="{{ route('admin.weekly_letters.index') }}">إلغاء</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{  asset('css/tagsinput.css') }}" rel="stylesheet"/>
@endpush
@push('scripts')
    <script src="{{ asset('js/tagsinput.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/mode/javascript/javascript.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/addon/selection/active-line.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/addon/edit/matchbrackets.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/custom/codemirror/lib/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/custom/codemirror/theme/dracula.css') }}">
    <script src="{{ asset('js/select2@4.1.0-rc.0_dist_js_select2.min.js') }}"></script>
    <script>
        const form = document.getElementById("myForm");
        const addButton = document.getElementById("addRow");
        const rowContainer = document.querySelector(".rowContainer");
        var categories_count = 0;
        var files_count = 1;

        addButton.addEventListener("click", function() {
            files_count++;
            $('#files_count').text(files_count);
            const row = document.createElement("div");
            row.classList.add("form-group","row");
            row.innerHTML = `
                                                                                    <div class="col">
                                                                                        <div class="form-group">
                                                                                            <label for="file_type[]">{{__('admin.categories')}}</label>
                                                                                            <select name="file_type[]" class="form-control file_type" id="file_type[]">
                                                                                                @foreach ($file_type as $type)
                                                                                                <option label="{{__("admin.".$type)}}">{{$type}}</option>

                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col">
                                                                                        <div class="form-group">
                                                                                            <label for="fileable_type[]">{{__("admin.type")}}</label>
                                                                                            <select name="fileable_type[]" class="form-control" id="fileable_type[]">
                                                                                                @foreach ($fileable_type as $type)
                                                                                                <option label="{{__("admin.".$type)}}" >{{$type}}</option>

                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col file_title d-none">
                                                                                        <div class="form-group">
                                                                                            <label for="file_title[]">{{__("admin.title")}}</label>
                                                                                            <input name="file_title[]" type="text" class="form-control" id="file_title[]" placeholder="{{__("admin.title")}}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col file_url d-none">
                                                                                        <div class="form-group">
                                                                                            <label for="file_url[]">{{__("admin.link")}}</label>
                                                                                            <input name="file_url[]" type="text" class="form-control" id="file_url[]" placeholder="{{__("admin.link")}}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col">
                                                                                        <div class="form-group">
                                                                                            <label for="fileable_id[]">{{__("admin.file_number")}}</label>
                                                                                            <input min="1" name="fileable_id[]" type="number" class="form-control" id="fileable_id[]" placeholder="{{__("admin.file_number")}}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <span class="delete">
                                                                                        <button type="button" class="btn btn-danger deleteRow" >{{__("admin.delete")}}</button>
                                                                                    </span>

            `;
            rowContainer.insertBefore(row, rowContainer.firstChild);

        });

        rowContainer.addEventListener("click", function(e) {
        if (e.target.classList.contains("deleteRow")) {

            e.target.closest('.row').remove();
            var file_type = $(this).closest('.rowContainer').find('.file_type').val();
            if (file_type === 'category') {
            categories_count--;
            $('#categories_count').text(categories_count);
            } else {
            files_count--;
            $('#files_count').text(files_count);
            }
        }


        });



        $(document).ready(function () {


                $('#myForm').submit(function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.weekly_letters.store') }}",
                        data: $(this).serialize(),
                        beforeSend: function( xhr ) {
                            $(".errors").html("");
                        },
                        success: function (response) {
                            if(response.success){
                                swal.fire("", response.message, "success");
                                window.location.href=response.redirect;
                            }
                        },
                        error: function (xhr) {
                            var errors = xhr.responseJSON.errors;
                            $('html, body').animate({
                            scrollTop: 0
                            }, 'slow');
                            console.log("errors");
                            console.log(errors);
                            console.log(xhr);
                            var errs = "";
                            for (const key in errors) {
                                var err = `
                                <div class="alert alert-solid-danger alert-bold fade show " role="alert">
                                                                                <div class="alert-icon"><i class="fa fa-exclamation-triangle"></i>
                                                                                </div>
                                                                                <div class="alert-text">{{__('views.Oops, something went wrong! Please check the errors below.')}}
                                                                                    ${key}:   ${errors[key][0]}
                                                                                </div>
                                                                                <div class="alert-close">
                                                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                                                                    </button>
                                                                                </div>
                                </div>

                                `;
                                errs += err;
                            }


                            $(".errors").html(errs);
                        }
                    });
                });


        });

        $(document).on('change', '.file_type', function() {
            var $fileType = $(this).val();
            var $colFileTitle = $(this).closest('.form-group.row').find('.file_title');
            var $colFileUrl = $(this).closest('.form-group.row').find('.file_url');

            if ($fileType === 'category') {
                categories_count++;
                $('#categories_count').text(categories_count);
                files_count--;
                $('#files_count').text(files_count);
                $colFileTitle.removeClass('d-none');
                $colFileUrl.removeClass('d-none');
            } else {
                files_count++;
                $('#files_count').text(files_count);
                categories_count--;
                $('#categories_count').text(categories_count);

                $colFileTitle.addClass('d-none');
                $colFileUrl.addClass('d-none');
            }
        });

    </script>
    <style>
        .CodeMirror, .CodeMirror *{
            direction: ltr;
            text-align: left;
        }

        .delete {
        position: relative;
        top: 26px;
        }
    </style>
    <script>

        $('#custom_target').select2();

        var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
            lineNumbers: true,
            styleActiveLine: true,
            matchBrackets: true,
            theme: 'dracula',
        });
    </script>
    <script>
        init_select2()
        $('[name="target"]').on('change', function () {
            if ($(this).val() == 'custom') {
                init_select2()
                $('#custom-target').show();
            } else {
                $('#custom-target').hide();
            }
        }).trigger('change');

        function init_select2() {
            var $select = $('#custom_target');
            if ($select.data('select2') !== undefined)
                $("#custom_target").select2("destroy");
            $select.select2({
                language: "ar",
                placeholder: '{{ __('views.Choose Users') }}',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('admin.members.ajax') }}',
                    dataType: 'json',
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.email,
                                    id: item.email
                                }
                            })
                        };
                    }
                }
            });
        }
    </script>
@endpush
