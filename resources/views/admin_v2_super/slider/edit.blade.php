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
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content">
                  <div class="row">
                      <div class="col-xl-12">
                          <div class="kt-portlet">
                              <div class="kt-portlet__head">
                                  <div class="kt-portlet__head-label">
                                      <h3 class="kt-portlet__head-title">{{ trans('admin.edit') }}</h3>
                                  </div>

                              </div>
                              <form class="kt-form kt-form--label-left"method="POST" action="{{$update_url}}" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="PUT">
                                    @include('errors.errors-forms')
                                  <div class="kt-portlet__body">
                                      <div class="kt-section kt-section--first">
                                          <div class="kt-section__body">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('auth.username') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $data->username }}" disabled="disabled" name="welcome_text" class="form-control" placeholder="{{ trans('auth.username') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('auth.email') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $data->email }}" name="email" class="form-control" placeholder="{{ trans('admin.email') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('admin.description') }}</label>
                                                <div class="col-sm-12">
                                                    <textarea name="bio" rows="4" id="bio" class="form-control" placeholder="{{ trans('admin.description') }}">{{ $data->bio }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('admin.paypal_account') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $data->paypal_account }}" name="paypal_account" class="form-control" placeholder="{{ trans('admin.paypal_account') }}">
                                                    <p class="help-block">{{ trans('admin.paypal_account_donations') }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">{{ trans('misc.username_on_twitter') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $data->twitter }}" name="twitter" class="form-control" placeholder="{{ trans('misc.username_on_twitter') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('misc.website_misc') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" value="{{ $data->website }}" name="website" class="form-control" placeholder="{{ trans('misc.website_misc') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('admin.status') }}</label>
                                                <div class="col-sm-12">
                                                <select name="status" class="form-control">
                                                    <option @if( $data->status == 'pending' ) selected="selected" @endif value="pending">{{ trans('admin.pending') }}</option>
                                                    <option @if( $data->status == 'active' ) selected="selected" @endif value="active">{{ trans('admin.active') }}</option>
                                                    <option @if( $data->status == 'suspended' ) selected="suspended" @endif value="suspended">{{ trans('admin.suspended') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ trans('admin.role') }}</label>
                                                <div class="col-sm-12">
                                                    <select name="role" class="form-control">
                                                        <option @if( $data->role == 'normal' ) selected="selected" @endif value="normal">{{ trans('admin.normal') }}</option>
                                                        <option @if( $data->role == 'admin' ) selected="selected" @endif value="admin">{{ trans('admin.role_admin') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group margin-bottom-radio">
                                                <div class="col-sm-12">
                                                    <label class="control-label">{{ trans('admin.authorized_to_upload')  }}</label>
                                                    <div class="kt-radio-list"">
                                                        <label class="padding-zero kt-radio kt-radio--success">
                                                            <input type="radio" name="authorized_to_upload" @if( $data->authorized_to_upload == 'yes' ) checked="checked" @endif value="yes" checked>
                                                            <span></span> {{ trans('misc.yes')  }}
                                                        </label>
                                                    </div>
                                                    <div class="kt-radio-list"">
                                                        <label class="padding-zero kt-radio kt-radio--success">
                                                            <input type="radio" name="authorized_to_upload" @if( $data->authorized_to_upload == 'no' ) checked="checked" @endif value="no">
                                                            <span></span> {{ trans('misc.no')  }}
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

            <!--Begin:: App Aside-->
            <div class="kt-grid__item kt-app__toggle kt-app__aside" id="kt_user_profile_aside">
                <!--begin:: Widgets/Applications/User/Profile1-->
                <div class="kt-portlet ">
                    <div class="kt-portlet__head  kt-portlet__head--noborder">

                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit-y">
                        <!--begin::Widget -->
                        <div class="kt-widget kt-widget--user-profile-1">
                            <div class="kt-widget__head">
                                <div class="kt-widget__media">
                                    <img src="{{asset('avatar').'/'.$data->avatar}}" alt="image">
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
                                        <a href="{{ url($data->username) }}" target="_blank" class="btn btn-success btn-sm" style="width:100%;">{{ trans('admin.view') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="kt-portlet__head"></div>

                            <div class="kt-widget__body">
                                <div class="kt-widget__items">
                                    <div class="kt-widget__item ">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__desc">
                                                {{trans('admin.registered')}}
                                            </span>
                                        </span>
                                        <span class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatDate($data->date) }}</span>
                                    </div>

                                    <div class="kt-widget__item ">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__desc">
                                                {{trans('misc.downloads')}}
                                            </span>
                                        </span>
                                        <strong class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( \App\Models\Download::where('user_id',$data->id)->count() ) }}</strong>
                                    </div>

                                    <div class="kt-widget__item ">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__desc">
                                                {{trans('users.following')}}
                                            </span>
                                        </span>
                                        <strong class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( \App\Models\Followers::where('follower',$data->id)->count() ) }}</strong>
                                    </div>

                                    <div class="kt-widget__item ">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__desc">
                                                {{trans('users.followers')}}
                                            </span>
                                        </span>
                                        <strong class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( \App\Models\Followers::where('following',$data->id)->count() ) }}</strong>
                                    </div>

                                    <div class="kt-widget__item ">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__desc">
                                                {{trans('misc.collections')}}
                                            </span>
                                        </span>
                                        <strong class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( \App\Models\Collection::where('user_id',$data->id)->count() ) }}</strong>
                                    </div>

                                    <div class="kt-widget__item ">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__desc">
                                                {{trans('misc.likes')}}
                                            </span>
                                        </span>
                                        <strong class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( \App\Models\ImageLike::where('user_id',$data->id)->count() ) }}</strong>
                                    </div>

                                    <div class="kt-widget__item ">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__desc">
                                                {{trans('misc.comments')}}
                                            </span>
                                        </span>
                                        <strong class="kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">{{ App\Helper::formatNumber( \App\Models\Comments::where('user_id',$data->id)->count() ) }}</strong>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!--end::Widget -->
                    </div>
                </div>
                <!--end:: Widgets/Applications/User/Profile1-->
            </div>
            <!--End:: App Aside-->
        </div>
        <!--End::App-->


</div>
<!-- end:: Content -->
@endsection

@push('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
@endpush


@push('scripts')

	<!-- icheck -->
	<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>

	<script type="text/javascript">

		$(".actionDelete").click(function(e) {
   	e.preventDefault();

   	var element = $(this);
	var id     = element.attr('data-url');
	var form    = $(element).parents('form');

	element.blur();

	swal(
		{   title: "{{trans('misc.delete_confirm')}}",
		text: "{{trans('admin.delete_user_confirm')}}",
		  type: "warning",
		  showLoaderOnConfirm: true,
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		   confirmButtonText: "{{trans('misc.yes_confirm')}}",
		   cancelButtonText: "{{trans('misc.cancel_confirm')}}",
		    closeOnConfirm: false,
		    },
		    function(isConfirm){
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

	</script>



@endpush
