@extends('video.admin.layout')

@section('css')
    <link href="{{{ asset('plugins/iCheck/all.css') }}}" rel="stylesheet" type="text/css" />

@endsection



@section('content')
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title"> تعديل تصنيف </h3>
            </div>
        </div>
        <!--begin::Form-->
        <form class="kt-form kt-form--label-right" method="post" action="{{{ url('video/panel/admin/categories/update') }}}" enctype="multipart/form-data">

            <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
            <input type="hidden" name="id" value="{{{ $categories->id }}}">
            @include('errors.errors-forms')
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>عنوان التصنيف بالعربي:</label>
                        <input type="text" value="{{{ $categories->name_en }}}" name="name_en" class="form-control" placeholder="{{{ trans('admin.name_en') }}}">
                    </div>
                    <div class="col-lg-6">
                        <label>عنوان التصنيف بالانجليزي:</label>
                        <input type="text" value="{{{ $categories->name_ar }}}" name="name_ar" class="form-control" placeholder="{{{ trans('admin.name_ar') }}}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label> الاسم الفريد</label>
                        <input type="text" value="{{{ $categories->slug }}}" name="slug" class="form-control" placeholder="{{{ trans('admin.slug') }}}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>الحالة</label>
                        <div class="kt-radio-list">
                            <label class="kt-radio">
                                <input @if( $categories->mode == 'on' ) checked @endif type="radio" name="mode" value="on" >
                                <span></span> {{{ trans('admin.active') }}}
                            </label>
                        </div>
                        <label class="kt-radio">
                            <input @if(  $categories->id == 1 ) disabled="disabled" @endif name="mode" value="off" @if( $categories->mode == 'off' ) checked @endif type="radio" >
                            <span></span> {{{ trans('admin.disabled') }}}
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>مدن ومعالم</label>
                        <div class="kt-radio-list">
                            <label class="kt-radio">
                                <input @if( $categories->cities_and_landmarks == 'on' ) checked @endif type="radio" name="cities_and_landmarks" value="on" >
                                <span></span> {{{ trans('admin.active') }}}
                            </label>
                        </div>
                        <label class="kt-radio">
                            <input @if(  $categories->id == 1 ) disabled="disabled" @endif name="cities_and_landmarks" value="off" @if( $categories->cities_and_landmarks == 'off' ) checked @endif type="radio" >
                            <span></span> {{{ trans('admin.disabled') }}}
                        </label>
                    </div>
                </div>


                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>أشخاص</label>
                        <div class="kt-radio-list">
                            <label class="kt-radio">
                                <input @if( $categories->people == 1 ) checked @endif type="radio" name="people" value="1" >
                                <span></span> {{{ trans('admin.active') }}}
                            </label>
                        </div>
                        <label class="kt-radio">
                            <input @if(  $categories->id == 1 ) disabled="disabled" @endif name="people" value="0" @if( $categories->people == 0 ) checked @endif type="radio" >
                            <span></span> {{{ trans('admin.disabled') }}}
                        </label>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>في الرئيسية</label>
                        <div class="kt-radio-list">
                            <label class="kt-radio">
                                <input type="radio" name="in_home" value="1" @if( $categories->in_home == 1 ) checked @endif>
                                <span></span> {{{ trans('admin.active') }}}
                            </label>
                        </div>
                        <label class="kt-radio">
                            <input type="radio" name="in_home" value="0" @if( $categories->in_home == 0 ) checked @endif>
                            <span></span> {{{ trans('admin.disabled') }}}
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>صورة التصنيف</label>
                        <div class="col-lg-9 col-xl-6">
                            <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1">
                                <div class="kt-avatar__holder" style="background-image: url({{ $categories->thumbnail }})"></div>
                                <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                    <i class="fa fa-pen"></i>
                                    <input type="file" onchange="readURL(this);" accept="image/*" name="thumbnail" />
                                </label>
                                <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <div class="row">
                        <div class="col-lg-6">
                            <a href="{{ url('video/panel/admin/categories') }}" class="btn btn-default">{{ trans('admin.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
@endsection
