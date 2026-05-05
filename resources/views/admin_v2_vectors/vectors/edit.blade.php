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
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">تعديل فيكتور</h3>
                                </div>
                            </div>
                            <div class="alert alert-danger alert-dismissible fade show row" role="alert">
                                <strong>
                                        {{ __("admin.Notes: To modify the content status of the completed modifications") }}
                                </strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="col-12">
                                    <ul>
                                        <li>{{ __("admin.The title in English is not equal to the title in Arabic and vice versa.") }}</li>
                                        <li>{{ __("admin.The title in Arabic does not contain English letters.") }}</li>
                                        <li>{{ __("admin.The title should not contain characters except for regex and it is preferable not to use them frequently.") }}</li>
                                        <li>{{ __("admin.Preferably write the title in Arabic without movements.") }}</li>
                                        <li>{{ __("admin.The file must contain tags in English and Arabic.") }}</li>
                                        <li>{{ __("admin.The file must have a category .") }}</li>
                                    </ul>
                                </div>
                            </div>
                            <form class="kt-form kt-form--label-left" method="POST"
                                  action="{{ route('admin.vectors.update', $data->id) }}" enctype="multipart/form-data">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="id" value="{{$data->id}}">
                                @include('errors.errors-forms')
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('admin.title_ar') }}</label>
                                                <div class="col-sm-10">
                                                    <input type="text" value="{{ $data->title_ar }}" name="title_ar"
                                                           class="form-control"
                                                           placeholder="{{ trans('admin.title_ar') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('admin.title_en') }}</label>
                                                <div class="col-sm-10">
                                                    <input type="text" value="{{ $data->title_en }}" name="title_en"
                                                           class="form-control"
                                                           placeholder="{{ trans('admin.title_en') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">{{ trans('admin.slug') }}</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group" style=" direction: ltr; ">
                                                        <div class="input-group-prepend"><span class="input-group-text">illustration-{{$data->id}}-</span>
                                                        </div>
                                                        <input type="text"
                                                               value="{{ str_replace("illustration-{$data->id}-",'', $data->slug) }}"
                                                               name="slug" class="form-control"
                                                               placeholder="{{ trans('admin.slug') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label">{{ trans('misc.tags_ar') }}</label>
                                                    <div class="">
                                                        <select name="tag_ar[]" multiple class="form-control tag_ar">
                                                            @foreach ($tags_ar as $tags)

                                                                <option selected value="{{$tags}}">{{$tags}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><!-- /.box-body -->
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label class="control-label">{{ trans('misc.tags_en') }}</label>
                                                    <div class="">
                                                        <select name="tag_en[]" multiple class="form-control tag_en">
                                                            @foreach ($tags_en as $tags)

                                                                <option selected value="{{$tags}}">{{$tags}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('misc.category') }}</label>
                                                <div class="col-sm-10">
                                                    <select name="categories_id[]" class="form-control">

                                                        @if (isset($all_categories) && count($all_categories) >0)
                                                        @foreach(  $all_categories as $category )
                                                          <option @if( in_array($category->id,$categoris->toArray()) ) selected="selected" @endif value="{{$category->id}}">
                                                            @if(App::isLocale('en'))
                                                              {{ $category->name_en }}
                                                            @else
                                                              {{ $category->name_ar }}
                                                            @endif
                                                          </option>
                                                        @endforeach

                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('misc.how_use_image') }}</label>
                                                <div class="col-sm-10">
                                                    <select name="how_use_image" class="form-control">
                                                        <option value="free"
                                                                @if( $data->how_use_image == 'free' ) selected="selected" @endif>{{ trans('misc.use_free') }}</option>
                                                        <option value="free_personal"
                                                                @if( $data->how_use_vector == 'free_personal' ) selected="selected" @endif>{{ trans('misc.use_free_personal') }}</option>
                                                        <option value="editorial_only"
                                                                @if( $data->how_use_vector == 'editorial_only' ) selected="selected" @endif>{{ trans('misc.use_editorial_only') }}</option>
                                                        <option value="web_only"
                                                                @if( $data->how_use_vector == 'web_only' ) selected="selected" @endif>{{ trans('misc.use_web_only') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('admin.description_ar') }}
                                                    ({{ trans('misc.optional') }})</label>
                                                <div class="col-sm-10">
                                                    <textarea name="description_ar" rows="4" id="description_ar"
                                                              class="form-control"
                                                              placeholder="{{ trans('admin.description_ar') }}">{{ $data->description_ar }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('admin.description_en') }}
                                                    ({{ trans('misc.optional') }})</label>
                                                <div class="col-sm-10">
                                                    <textarea name="description_en" rows="4" id="description_en"
                                                              class="form-control"
                                                              placeholder="{{ trans('admin.description_en') }}">{{ $data->description_en }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('misc.featured')  }}</label>
                                                <div class="col-sm-10">
                                                    <div class="radio">
                                                        <label class="padding-zero">
                                                            <input type="radio" name="featured"
                                                                   @if( $data->featured == 'yes' ) checked="checked"
                                                                   @endif value="yes" checked>
                                                            {{ trans('misc.yes')  }}
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label class="padding-zero">
                                                            <input type="radio" name="featured"
                                                                   @if( $data->featured == 'no' ) checked="checked"
                                                                   @endif value="no">
                                                            {{ trans('misc.no')  }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-4 control-label">{{ trans('misc.attribution_required')  }}</label>
                                                <div class="col-sm-10">
                                                    <div class="radio">
                                                        <label class="padding-zero">
                                                            <input type="radio" name="attribution_required"
                                                                   @if( $data->attribution_required == 'yes' ) checked="checked"
                                                                   @endif value="yes" checked>
                                                            {{ trans('misc.yes')  }}
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label class="padding-zero">
                                                            <input type="radio" name="attribution_required"
                                                                   @if( $data->attribution_required == 'no' ) checked="checked"
                                                                   @endif value="no">
                                                            {{ trans('misc.no')  }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-2 control-label">{{ trans('admin.status') }}</label>
                                                <div class="col-sm-10">
                                                    <select name="status" class="form-control">
                                                        <option @if( $data->status == 'active' ) selected="selected"
                                                                @endif value="active">{{ trans('admin.active') }}</option>
                                                        <option @if( $data->status == 'pending' ) selected="selected"
                                                                @endif value="pending">{{ trans('admin.pending') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-2 control-label">{{{ trans('admin.in_home') }}}</label>
                                                <div class="col-sm-10">
                                                    <div class="radio">
                                                        <label class="padding-zero">
                                                            <input type="radio" name="in_home" value="1"
                                                                   @if( $data->in_home == 1 ) checked @endif>
                                                            {{{ trans('admin.active') }}}
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label class="padding-zero">
                                                            <input type="radio"
                                                                   @if(  $data->id == 1 ) disabled="disabled"
                                                                   @endif name="in_home" value="0"
                                                                   @if( $data->in_home == 0 ) checked @endif>
                                                            {{{ trans('admin.disabled') }}}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__foot">
                                    <div class="kt-form__actions">
                                        <div class="row">
                                            <div class="col-lg-9 col-xl-9">
                                                <button type="submit"
                                                        class="btn btn-success">{{ trans('admin.save') }}</button>&nbsp;
                                                <a href="{{ route('admin.vectors.index') }}"
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
            <div class="kt-grid__item kt-app__toggle kt-app__aside" id="kt_user_profile_aside">
                <div class="kt-portlet ">
                    <div class="kt-portlet__head  kt-portlet__head--noborder"></div>
                    <div class="kt-portlet__body kt-portlet__body--fit-y">
                        <div class="kt-widget kt-widget--user-profile-1">
                            <div class="kt-widget__head">
                                <div class="kt-widget__media">
                                    <img src="{{$data->thumbnail}}" alt="image">
                                </div>
                                <div class="kt-widget__content">
                                    <div class="kt-widget__section">
                  <span class="kt-widget__username">
                    Jason Muller
                    <i class="flaticon2-correct kt-font-success"></i>
                  </span>
                                        <span class="kt-widget__subtitle">
                    Head of Development
                  </span>
                                    </div>
                                    <div class="kt-widget__action">
                                        <a href="{{ $data->post_link }}" target="_blank" class="btn btn-success btn-sm"
                                           style="width:100%;">{{ trans('admin.view') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-portlet__head"></div>
                            <div class="kt-widget__body">
                                <div class="kt-widget__items">
                                    <div class="kt-widget__item ">
                  <span class="kt-widget__section">
                    <span class="kt-widget__desc">
                      {{trans('misc.uploaded_by')}}
                    </span>
                  </span>
                                        <span
                                            class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">@if(isset($data->user) && $data->user != ''){{ $data->user->username }} @endif</span>
                                    </div>
                                    <div class="kt-widget__item ">
                  <span class="kt-widget__section">
                    <span class="kt-widget__desc">
                      {{trans('misc.published')}}
                    </span>
                  </span>
                                        <strong
                                            class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatDate($data->date) }}</strong>
                                    </div>
                                    <div class="kt-widget__item ">
                  <span class="kt-widget__section">
                    <span class="kt-widget__desc">
                      {{trans('misc.downloads')}}
                    </span>
                  </span>
                                        <strong
                                            class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( $data->downloads()->count() ) }}</strong>
                                    </div>
                                    <div class="kt-widget__item ">
                  <span class="kt-widget__section">
                    <span class="kt-widget__desc">
                      {{trans('misc.views')}}
                    </span>
                  </span>
                                        <strong
                                            class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( $data->visits()->count() ) }}</strong>
                                    </div>
                                    <div class="kt-widget__item ">
                  <span class="kt-widget__section">
                    <span class="kt-widget__desc">
                      {{trans('misc.likes')}}
                    </span>
                  </span>
                                        <strong
                                            class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( $data->likes()->count() ) }}</strong>
                                    </div>
                                    <div class="kt-widget__item ">
                  <span class="kt-widget__section">
                    <span class="kt-widget__desc">
                      {{trans('misc.comments')}}
                    </span>
                  </span>
{{--                                        <strong--}}
{{--                                            class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( $data->comments()->count() ) }}</strong>--}}
                                    </div>
                                </div>
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
    <link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css"/>

    <style>
        .select2-container .select2-selection{
            max-height: 120px;
            overflow: auto;
        }
    </style>
@endpush


@push('scripts')

    <!-- icheck -->
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/select2@4.1.0-rc.0_dist_js_select2.min.js') }}"></script>
    <script type="text/javascript">
        $("#tagInput").tagsInput({

            'delimiter': [','],   // Or a string with a single delimiter. Ex: ';'
            'width': 'auto',
            'height': 'auto',
            'removeWithBackspace': true,
            'minChars': 3,
            'maxChars': 25,
            'defaultText': '{{ trans("misc.add_tag") }}',
            /*onChange: function() {
                var input = $(this).siblings('.tagsinput');
                var maxLen = 4;

               if( input.children('span.tag').length >= maxLen){
                       input.children('div').hide();
                   }
                   else{
                       input.children('div').show();
                   }
               },*/
        });

        $("#tagInput_en").tagsInput({

            'delimiter': [','],   // Or a string with a single delimiter. Ex: ';'
            'width': 'auto',
            'height': 'auto',
            'removeWithBackspace': true,
            'minChars': 3,
            'maxChars': 25,
            'defaultText': '{{ trans("misc.add_tag") }}',
            /*onChange: function() {
                var input = $(this).siblings('.tagsinput');
                var maxLen = 4;

               if( input.children('span.tag').length >= maxLen){
                       input.children('div').hide();
                   }
                   else{
                       input.children('div').show();
                   }
               },*/
        });

        $(".actionDelete").click(function (e) {
            e.preventDefault();

            var element = $(this);
            var id = element.attr('data-url');
            var form = $(element).parents('form');

            element.blur();

            swal(
                {
                    title: "{{trans('misc.delete_confirm')}}",
                    type: "warning",
                    showLoaderOnConfirm: true,
                    showCancelButton: true,
                    confirmButtonColor: "#dd6b55",
                    confirmButtonText: "{{trans('misc.yes_confirm')}}",
                    cancelButtonText: "{{trans('misc.cancel_confirm')}}",
                    closeOnConfirm: false,
                },
                function (isConfirm) {
                    if (isConfirm) {
                        form.submit();
                        //$('#form' + id).submit();
                    }
                });


        });

        //Flat red color scheme for iCheck
        $('input[type="radio"]').iCheck({
            radioClass: 'iradio_flat-red'
        });


        $(".tag_en").select2({
            tags: true,
            multiple: true,
            closeOnSelect: false,
        });
        $(".tag_ar").select2({
            tags: true,
            multiple: true,
            closeOnSelect: false,
        });
    </script>



@endpush
