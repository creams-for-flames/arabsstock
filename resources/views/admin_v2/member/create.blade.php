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
                    <div class="col-lg-8 mx-auto">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">{{ trans('admin.edit') }}</h3>
                                </div>
                            </div>
                            <form class="kt-form kt-form--label-left" method="post" action="{{$store_url}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                @include('errors.errors-forms')
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.name') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="name" class="form-control"
                                                           placeholder="{{ trans('admin.name') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('auth.email') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="email" class="form-control"
                                                           placeholder="{{ trans('admin.email') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-2 control-label">{{ trans('auth.password') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="password" name="password" class="form-control"
                                                           placeholder="{{ trans('auth.password') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-2 control-label">{{ trans('auth.password_confirmation') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="password" name="password_confirmation"
                                                           class="form-control"
                                                           placeholder="{{ trans('auth.password_confirmation') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-sm-2 control-label">{{ trans('admin.status') }}</label>
                                                <div class="col-sm-12">
                                                    <select name="status" class="form-control">
                                                        <option value="active">{{ trans('admin.active') }}</option>
                                                        <option value="pending">{{ trans('admin.pending') }}</option>
                                                        <option
                                                            value="suspended">{{ trans('admin.suspended') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.role') }}</label>
                                                <div class="col-sm-12">
                                                    <select name="role" class="form-control">
                                                        @foreach(array_keys(config('roles')) as $r)
                                                            <option
                                                                value="{{ $r }}">{{ trans("admin.roles.{$r}") }}</option>
                                                        @endforeach
                                                    </select>
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
        </div>
        <!--End::App-->
    </div>
    <!-- end:: Content -->
@endsection
@push('css')
@endpush

@push('javascript')

@endpush

