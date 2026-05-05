<?php
// ** Data User logged ** //
     $user = Auth::user();
	  ?>
@extends('app')

@section('title') {{ trans('users.account_settings') }} - @endsection

@section('content')
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h1 class="title-site title-sm">{{ trans('users.account_settings') }}</h1>
      </div>
    </div>

<div class="container margin-bottom-40  margin-top-40">

			<!-- Col MD -->
		<div class="col-md-12">

	<div class="wrap-center center-block">
			@if (Session::has('notification'))
			<div class="alert alert-success btn-sm alert-fonts" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            		{{ Session::get('notification') }}
            		</div>
            	@endif

			{{--@include('errors.errors-forms')--}}

			@include('users.navbar-edit')
				<ul>
					@foreach ($errors->all() as $error)
						<li><p style="color: red;"> {{ $error }}</p></li>
					@endforeach
				</ul>
		<!-- ***** FORM ***** -->
       <form action="{{ route('video.account') }}" method="post" name="form">

          	<input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="row">
        	<div class="col-md-6">
	           	<!-- ***** Form Group ***** -->
	            <div class="form-group has-feedback">
					<label class="font-default">{{ trans('misc.full_name_misc') }}  </label>
	              <input   oninvalid="setCustomValidity('{{trans('validation.fullname')}}')"
						   oninput="setCustomValidity('')"
						   required
						   type="text" class="form-control login-field custom-rounded" value="{{ e( $user->name ) }}" name="full_name" placeholder="{{ trans('misc.full_name_misc') }}" title="{{ trans('misc.full_name_misc') }}"  autocomplete="off">
	             </div><!-- ***** Form Group ***** -->
				@if($errors->full_name)
					<p style="color: red;" class="help-block">
						{{ trans('validation.fullname') }}
					</p>
				@endif
           </div><!-- End Col MD-->


            <div class="col-md-6">
            	<!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('auth.email') }}   </label>
              <input   type="email" oninvalid="setCustomValidity('{{trans('validation.email')}}')"
					   oninput="setCustomValidity('')"
					   required
					   class="form-control login-field custom-rounded" value="{{$user->email}}" name="email" placeholder="{{ trans('auth.email') }}" title="{{ trans('auth.email') }}" autocomplete="off">
         </div><!-- ***** Form Group ***** -->
					@if($errors->email)
						<p style="color: red;" class="help-block">
							{{ trans('validation.email') }}
						</p>
					@endif
            </div><!-- End Col MD-->

        </div><!-- End row -->

			<div class="row">

				<div class="col-md-6">
					<!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.username_misc') }}   </label>
              <input   oninvalid="setCustomValidity('{{trans('validation.username')}}')"
					   oninput="setCustomValidity('')"
					   required
					   type="text" class="form-control login-field custom-rounded" value="{{$user->username}}" name="username" placeholder="{{ trans('misc.username_misc') }}" title="{{ trans('misc.username_misc') }}" autocomplete="off">
         </div><!-- ***** Form Group ***** -->
						@if($errors->username)
							<p style="color: red;" class="help-block">
								{{ trans('validation.username') }}
							</p>
						@endif
				</div><!-- End Col MD-->

				<div class="col-md-6">
					<!-- ***** Form Group ***** -->
            <div class="form-group has-feedback">
            	<label class="font-default">{{ trans('misc.country') }}   </label>
            	<select   name="country_id" class="form-control" >
                      		<option value="">{{trans('misc.select_your_country')}}</option>
                      	@foreach(  $countries as $country )
                            <option @if( $user->country_id == $country->id ) selected="selected" @endif value="{{$country->id}}">{{ $country->country_name }}</option>
						@endforeach
                          </select>
            	    </div><!-- ***** Form Group ***** -->
						@if($errors->country_id)
							<p style="color: red;" class="help-block">
								{{ trans('validation.country') }}
							</p>
						@endif
				</div><!-- End Col MD-->

			</div><!-- End row -->

           <button type="submit" id="buttonSubmit" class="btn btn-block btn-lg btn-main custom-rounded">{{ trans('misc.save_changes') }}</button>



       </form><!-- ***** END FORM ***** -->

</div><!-- wrap center -->

		</div><!-- /COL MD -->


 </div><!-- container -->

 <!-- container wrap-ui -->

<style>
	.help-block{
		display: none;
	}
</style>
@endsection
