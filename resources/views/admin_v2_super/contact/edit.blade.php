@extends('admin_v2.layout.app')

@section('content')
    <style>
        .thumbnail .options{
            position: absolute;
            top: -10px;
            right: 8px;
            -webkit-transform: scale(0);
            -moz-transform: scale(0);
            -o-transform: scale(0);
            -ms-transform: scale(0);
            transform: scale(0);
            -webkit-transform: scale(0,);
            -ms-transform: scale(0,);
            transform: scale(0,);
            -webkit-opacity: 0;
            -moz-opacity: 0;
            opacity: 0;
            filter: alpha(opacity=0);
            -o-transform-origin: 50% 0%;
            -ms-transform-origin: 50% 0%;
            -webkit-transform-origin: 50% 0%;
            -moz-transform-origin: 50% 0%;
            transform-origin: 50% 0%;
            -moz-transition: all 300ms ease-in-out;
            -o-transition: all 300ms ease-in-out;
            -webkit-transition: all 300ms ease-in-out;
            transition: all 300ms ease-in-out;
        }
        .thumbnail:hover .options{
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -o-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
            -webkit-transform: scale(1,);
            -ms-transform: scale(1,);
            transform: scale(1,);
            -webkit-opacity: 1;
            -moz-opacity: 1;
            opacity: 1;
            filter: alpha(opacity=100);
        }
        .thumbnail .options a{
            display: inline-block;
            line-height: 1;
            margin-left: 2px;
            background: #737881;
            color: #FFF;
            width: 24px;
            height: 24px;
            line-height: 24px;
            -webkit-border-radius: 12px;
            -webkit-background-clip: padding-box;
            -moz-border-radius: 12px;
            -moz-background-clip: padding;
            border-radius: 12px;
            background-clip: padding-box;
            text-align: center;
            -moz-box-shadow: 0 2px 5px rgba(0, 0, 0, .2);
            -webkit-box-shadow: 0 2px 5px rgb(0 0 0 / 20%);
            box-shadow: 0 2px 5px rgb(0 0 0 / 20%);
        }
        .thumbnail .options a.delete{
            background: #dd1f26;
        }
    </style>
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::App-->
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
            <!--Begin:: App Aside Mobile Toggle-->
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>
            <!--End:: App Aside Mobile Toggle-->
            <!--Begin:: App Content-->
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">{{ trans('admin.edit') }}</h3>
                                </div>
                            </div>
                            <form class="kt-form kt-form--label-left" method="POST"
                                  action="{{ route('admin.super.contact.update',$contact) }}">
                                @csrf
                                @method('PUT')
                                @include('errors.errors-forms')
                                @if(request('return_page'))
                                    <input type="hidden" name="return_page" value="{{ request('return_page') }}">
                                @endif
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('auth.name') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $contact->name }}" name="name"
                                                           class="form-control" placeholder="{{ trans('auth.name') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('auth.email') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $contact->email }}" name="email"
                                                           class="form-control" placeholder="{{ trans('auth.email') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-3 control-label">{{ trans('global.mobile') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $contact->mobile }}" name="mobile"
                                                           class="form-control"
                                                           placeholder="{{ trans('auth.mobile') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-3 control-label">{{ trans('global.nationality') }}</label>
                                                <div class="col-sm-12">
                                                    <select class="form-control select2-input" name="nationality"
                                                            oninvalid="setCustomValidity('The Name Is Requerd')"
                                                            required>
                                                        <option
                                                            value="">  {{trans('global.select')}} {{trans('global.nationality' )}}</option>
                                                        @foreach(\App\Models\Countries::all() as $country)
                                                            <option
                                                                value="{{$country->id}}" {{ $contact->nationality == $country->id ? "selected" : "" }}>
                                                                @if(app()->getLocale() == 'en') {{$country->name_en}} @else {{$country->name_ar}} @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('global.city') }}</label>
                                                <div class="col-sm-12">
                                                    <select class="form-control select2-input" name="city"
                                                            required>
                                                        <option
                                                            value="">  {{trans('global.select')}} {{trans('global.city' )}}</option>
                                                        @foreach(\App\Models\Cities::all() as $city)
                                                            <option
                                                                value="{{$city->id}}" {{ $contact->city == $city->id ? "selected" : "" }}>
                                                                @if(app()->getLocale() == 'en') {{$city->name_en}} @else {{$city->name_ar}} @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('global.sex') }}</label>
                                                <div class="col-sm-12">
                                                    <select id="sex" required class="form-control" name="sex"
                                                            required>
                                                        <option
                                                            value="">  {{trans('global.select')}} {{trans('global.sex' )}}</option>
                                                        <option
                                                            value="mail" {{ $contact->sex == 'mail' ? "selected" : "" }} >  {{trans('global.mail')}}</option>
                                                        <option
                                                            value="femail" {{ $contact->sex == 'femail' ? "selected" : "" }} >  {{trans('global.femail')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-3 control-label">{{ trans('global.birth_date') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $contact->birth_date }}"
                                                           name="birth_date" class="form-control"
                                                           placeholder="{{ trans('global.birth_date') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-3 control-label">{{ trans('global.length') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $contact->length }}" name="length"
                                                           class="form-control"
                                                           placeholder="{{ trans('global.length') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-3 control-label">{{ trans('global.weight') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $contact->weight }}" name="weight"
                                                           class="form-control"
                                                           placeholder="{{ trans('global.weight') }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label
                                                        id="sss">{{trans('global.skills' )  .' (' . __('global.optional').' )'}}</label>
                                                    <div id="sss2"></div>
                                                    <input class="form-select" value="{{ $contact->skill }}"
                                                           name="skill"
                                                           data-role="tagsinput">
                                                </div>
                                            </div>
                                            <div id="attachments_upload" class="mb-4">
                                                <div class="dropzone dropzone-default dropzone-success"
                                                     id="attachments">
                                                    <div class="dropzone-msg dz-message needsclick">
                                                        <h3 class="dropzone-msg-title">Drop files here or click to
                                                            upload.</h3>
                                                        <span class="dropzone-msg-desc">Only image files are allowed for upload</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="media_items" class="row">
                                                @foreach($contact->images as $r)
                                                    <div class="col-md-3">
                                                        <div class="thumbnail mb-3">
                                                            <div class="options">
                                                                <a href="javascript:;"
                                                                   data-href="{{ route('admin.super.contact.delete_image',$r) }}"
                                                                   class="delete" tooltip-placement="top"
                                                                   uib-tooltip="حذف"><i class="fa fa-times"></i></a>
                                                            </div>
                                                            <a href="{{ cdn($r->image) }}" data-fancybox
                                                               style="height: 400px;overflow: hidden!important;display: block;">
                                                                <img src="{{ cdn($r->image) }}"
                                                                     style="width:100%;">
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__foot">
                                    <div class="kt-form__actions">
                                        <div class="row">
                                            <div class="col-lg-9 col-xl-9">
                                                <button type="submit"
                                                        class="btn btn-success">{{ trans('admin.save') }}</button>
                                                &nbsp;
                                                <a href="{{$index_url}}"
                                                   class="btn btn-secondary">{{ trans('admin.cancel') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End:: App Content-->
            <!--Begin:: App Aside-->
            <!--End:: App Aside-->
        </div>
        <!--End::App-->
    </div>
    <!-- end:: Content -->
@endsection
@push('css')
    <link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{  asset('css/tagsinput.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css"/>
@endpush
@push('scripts')
    <script src="{{ asset('js/tagsinput.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;
        if ($('#attachments').length) {
            var attachmentsDropzone = new Dropzone("#attachments", {
                paramName: "file",
                dictDefaultMessage: "اسقط الملفات لرفعها",
                maxFilesize: 1000,
                url: "{{ route('admin.super.contact.upload',$contact) }}",
                acceptedFiles: ".jpg,.jpeg,.png"
            });
            attachmentsDropzone.on("sending", function (file, xhr, formData) {
                formData.append("_token", '{{ csrf_token() }}');
            });
            attachmentsDropzone.on("success", function (file, $response) {
                if ($response.status) {
                    $('#media_items').prepend(`<div class="col-md-3">
                                                        <div class="thumbnail mb-3">
                                                        <div class="options">
                                                                <a href="javascript:;"
                                                                   data-href="` + ('{{ route('admin.super.contact.delete_image',0) }}'.replace(0, $response.id)) + `"
                                                                   class="delete" tooltip-placement="top"
                                                                   uib-tooltip="حذف"><i class="fa fa-times"></i></a>
                                                            </div>
                                                            <a href="` + $response.path + `"  data-fancybox style="height: 400px;overflow: hidden!important;display: block;">
                                                                <img src="` + $response.path + `" alt="Lights" style="width:100%;">
                                                            </a>
                                                        </div>
                                                    </div>`)
                    attachmentsDropzone.removeFile(file);
                }
            });
            attachmentsDropzone.on("complete", function () {
            });
        }
        $(document).on('click', '.delete[data-href]', function ($e) {
            $e.preventDefault();
            var $this = $(this),
                $item = $this.closest('.thumbnail').parent(),
                $url = $this.attr('data-href');
            Swal.fire({
                title: "تأكيد الحذف",
                text: 'هل تود حذف الصورة',
                type: "error",
                showCancelButton: true,
                confirmButtonText: "تأكيد",
                cancelButtonText: "الغاء",
                reverseButtons: true
            }).then(function (e) {
                if (e.value) {
                    $.ajax({
                        type: "DELETE",
                        data: {'_token': '{{ csrf_token() }}'},
                        url: $url,
                        success: function ($response) {
                            $item.fadeOut(300, function () {
                                $(this).remove();
                            })
                            $('[data-toggle="m-tooltip"]').tooltip('hide');
                        }
                    });
                }
            });
        });
    </script>
@endpush
