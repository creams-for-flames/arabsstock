@extends('admin_v2.layout.app')

@section('content')
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
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content" style="margin-left:0;">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet kt-portlet--tabs">
                            <div class="kt-portlet__body">
                                <form method="post" action="{{ route('admin.newsletter.store') }}">
                                    @csrf
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                                            <div class="kt-form kt-form--label-right">
                                                <div class="kt-form__body">
                                                    <div class="kt-section kt-section--first">
                                                        <div class="kt-section__body">
                                                            @if ($errors->any())
                                                                <div
                                                                    class="alert alert-solid-danger alert-bold fade show kt-margin-t-20 kt-margin-b-40"
                                                                    role="alert">
                                                                    <div class="alert-icon"><i
                                                                            class="fa fa-exclamation-triangle"></i>
                                                                    </div>
                                                                    <div
                                                                        class="alert-text">{{__('views.Oops, something went wrong! Please check the errors below.')}}
                                                                        {!! implode('', $errors->all('<div>:message</div>')) !!}
                                                                    </div>
                                                                    <div class="alert-close">
                                                                        <button type="button" class="close"
                                                                                data-dismiss="alert" aria-label="Close">
                                                                            <span aria-hidden="true"><i
                                                                                    class="la la-close"></i></span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">{{ __('views.Title') }}</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        <input type="text" value="{{ old('subject') }}"
                                                                               name="subject"
                                                                               class="form-control"
                                                                               placeholder="{{ __('views.Title') }}"
                                                                               required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">{{ __('views.To') }}</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group validated">
                                                                        <select class="form-control"
                                                                                name="receivers">
                                                                            <option
                                                                                {{old('receivers') === 'all' ? 'selected' : ''}} value="all">{{__('views.All Users')}}</option>
                                                                            <option
                                                                                {{old('receivers') === 'specific' ? 'selected' : ''}} value="specific">{{__('views.specific')}}</option>
                                                                        </select>
                                                                        <div
                                                                            class="invalid-feedback">{{$errors->first('receivers')}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row " id="specific-users"
                                                                 style="display: none;">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">{{ __('views.specific') }}</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        {{--                                                                        <input type="text"--}}
                                                                        {{--                                                                               value="{{ old('specific_users') }}"--}}
                                                                        {{--                                                                               name="specific_users"--}}
                                                                        {{--                                                                               class="form-control"--}}
                                                                        {{--                                                                               data-role=tagsinput--}}
                                                                        {{--                                                                               placeholder="{{ __('views.specific') }}"--}}
                                                                        {{--                                                                               required>--}}
                                                                        <select name="specific_users[]" multiple
                                                                                id="specific_users" style="width: 100%;"
                                                                                data-placeholder="اختر مستخدمين"
                                                                        ></select>
                                                                        <div
                                                                            class="invalid-feedback">{{$errors->first('receivers')}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">Html</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <textarea id="code" name="html" direction="ltr"
                                                                              dir="ltr">{!! old('html') !!}</textarea>
                                                                    <p class="mt-4">اسم المستخدم: <span
                                                                            class="bg-success text-white px-2 rounded"
                                                                            id="name_var">{name}</span></p>
                                                                    <p class="mt-4">الغاء الاشتراك: <span
                                                                            class="bg-success text-white px-2 rounded"
                                                                            id="unsubscribe_var">{unsubscribe}</span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="kt-separator kt-separator--space-lg kt-separator--fit kt-separator--border-solid"></div>
                                                <div class="kt-form__actions">
                                                    <div class="row">
                                                        <div class="col-xl-3"></div>
                                                        <div class="col-lg-9 col-xl-8">
                                                            <button class="btn btn-label-brand btn-bold" type="submit">
                                                                ارسال
                                                            </button>
                                                            <a class="btn btn-clean btn-bold"
                                                               href="{{ route('admin.newsletter.index') }}">إلغاء</a>
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
        <!--End::App-->
    </div>
    <!-- end:: Content -->
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
    {{--    <link href="{{ asset('css/select2@4.1.0-rc.0_dist_css_select2.min.css') }}" rel="stylesheet" />--}}
    <script src="{{ asset('js/select2@4.1.0-rc.0_dist_js_select2.min.js') }}"></script>

    <style>
        .CodeMirror, .CodeMirror *{
            direction: ltr;
            text-align: left;
        }
    </style>
    <script>
        $('#specific_users').select2();

        var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
            lineNumbers: true,
            styleActiveLine: true,
            matchBrackets: true,
            theme: 'dracula',
        });
    </script>
    <script>
        init_select2()
        $('[name="receivers"]').on('change', function () {
            if ($(this).val() == 'specific') {
                init_select2()
                $('#specific-users').show();
            } else {
                $('#specific-users').hide();
            }
        }).trigger('change');

        function init_select2() {
            var $select = $('#specific_users');
            if ($select.data('select2') !== undefined)
                $("#specific_users").select2("destroy");
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
