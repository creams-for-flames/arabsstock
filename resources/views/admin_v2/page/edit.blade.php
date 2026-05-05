@extends('admin_v2.layout.app')
@push('scripts')
    <script src="{{ asset('js/medium-editor@latest_dist_js_medium-editor.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/medium-editor@latest_dist_css_medium-editor.min.css') }}" type="text/css" media="screen" charset="utf-8">
    @endpush
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
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">تعديل صفحة</h3>
                                </div>
                            </div>
                            <form class="kt-form kt-form--label-left" method="post" action="{{$update_url}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">

                                @include('errors.errors-forms')
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.title_en') }}</label>
                                                <div class="col-sm-10">
                                                    <input type="text" value="{{ $data->title_en }}" name="title_en" class="form-control" placeholder="{{ trans('admin.title_en') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.title_ar') }}</label>
                                                <div class="col-sm-10">
                                                    <input type="text" value="{{ $data->title_ar }}" name="title_ar" class="form-control" placeholder="{{ trans('admin.title_ar') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.slug') }}</label>
                                                <div class="col-sm-10">
                                                    <input type="text" value="{{ $data->slug }}" name="slug" class="form-control" placeholder="{{ trans('admin.slug') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.content_en') }}</label>
                                                <div class="col-sm-10">

                                                    <textarea name="content_en" rows="5" cols="40" id="content_en" class=" editable" placeholder="{{ trans('admin.content_en') }}">{{ $data->content_en }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.content_ar') }}</label>
                                                <div class="col-sm-10">

                                                    <textarea name="content_ar" rows="5" cols="40" id="content_ar" class="editable" placeholder="{{ trans('admin.content_ar') }}">{{ $data->content_ar }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__foot">
                                    <div class="kt-form__actions">
                                        <div class="row">
                                            <div class="col-lg-9 col-xl-9">
                                                <button type="submit" class="btn btn-success">{{ trans('admin.save') }}</button>&nbsp;
                                                <a href="{{$index_url}}" class="btn btn-secondary">{{ trans('admin.cancel') }}</a>
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
        </div>
        <!--End::App-->

</div>
<!-- end:: Content -->
@endsection

@push('css')
@endpush

@push('scripts')
<script>
var KTSummernoteDemo = function () {
    // Private functions
    var demos = function () {
        $('.editable').summernote({
            height: 150
        });
    }

    return {
        // public functions
        init: function() {
            demos();
        }
    };
}();

// Initialization
jQuery(document).ready(function() {
    KTSummernoteDemo.init();
});
</script>
@endpush

