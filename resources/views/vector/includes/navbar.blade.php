<?php
$userAuth = Auth::user();
if( Auth::check() ) {

	// Notifications
	$notifications_count = App\Models\Notifications::where('destination',Auth::user()->id)->where('status','0')->count();

}
?>
<div class="navbar navbar-inverse navBar">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

          	 <?php if( isset( $totalNotify ) ) : ?>
        	<span class="notify notifyResponsive"><?php echo $totalNotify; ?></span>
        	<?php endif; ?>

            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
			@if(is_in_segment('video'))
				<a class="navbar-brand" href="{{ route('landPage') }}">
					<img src="{{ asset('img/ARStock_dark.png') }}" class="logo" />
				</a>
			@else

				<a class="navbar-brand" href="{{ route('landPage') }}">
					<img src="{{ asset('img/ARStock_dark.png') }}" class="logo" />
				</a>
			@endif
        </div><!-- navbar-header -->



        <div class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right margin-bottom-zero">
				<li>
					<a  href="{{route('photos.home')}}" class="font-default text-uppercase">
						{{ trans('global.Pictures') }}
					</a>
				</li>
				<li>
					<a  href="{{route('video.home')}}" class="font-default text-uppercase">
						{{ trans('global.the_video') }}
					</a>
				</li>
			</ul>
        	<ul class="nav navbar-nav navbar-right margin-bottom-zero">

						@if(!Request::is('/') && !Request::is('search/*'))


						 @endif
							@if(!is_in_segment('video'))
							<li>
								<a  style="color:red" href="{{route('plans')}}" class="font-default text-uppercase">
									{{trans('misc.prices and packages')}}
								</a>
							</li>
							@endif
        		@if( Auth::check() )

        		@endif

			<li class="dropdown">
        			<a href="javascript:void(0);" class="font-default text-uppercase" data-toggle="dropdown">{{trans('misc.explore')}}
        				<i class="ion-chevron-down margin-lft5"></i>
        				</a>


				@if(is_in_segment('video'))
					<ul class="dropdown-menu arrow-up" role="menu" aria-labelledby="dropdownMenu2">
						<li><a href="{{ route('video.collection') }}"><i class="fa fa-folder-open-o myicon-right"></i> {{ trans('misc.collections') }}</a></li>
						<li><a href="{{ route('video.tags') }}"><i class="icon icon-Tag myicon-right"></i> {{ trans('misc.tags') }}</a></li>
						<li role="separator" class="divider"></li>
{{-- 						<li><a href="{{ route('video.popular') }}">{{ trans('misc.popular') }}</a></li> --}}
						<li><a href="{{ route('video.latest') }}">{{ trans('misc.latest') }}</a></li>
{{-- 					<!--<li><a href="{{ route('video.most.commented') }}">{{trans('misc.most_commented')}}</a></li>-->
						<li><a href="{{ route('video.most.viewed') }}">{{trans('misc.most_viewed')}}</a></li>
						<li><a href="{{ route('video.most.downloads') }}">{{trans('misc.most_downloads')}}</a></li> --}}
					</ul><!-- DROPDOWN MENU -->


					@else

					<ul class="dropdown-menu arrow-up" role="menu" aria-labelledby="dropdownMenu2">
					<!--<li><a href="{{ route('members') }}"><i class="icon icon-Users myicon-right"></i> {{ trans('misc.members') }}</a></li>-->
						<li><a href="{{ route('collections') }}"><i class="fa fa-folder-open-o myicon-right"></i> {{ trans('misc.collections') }}</a></li>
						<li><a href="{{ route('tags') }}"><i class="icon icon-Tag myicon-right"></i> {{ trans('misc.tags') }}</a></li>
						<li role="separator" class="divider"></li>
{{-- 						<li><a href="{{ route('featured') }}">{{ trans('misc.featured') }}</a></li>
						<li><a href="{{ route('popular') }}">{{ trans('misc.popular') }}</a></li> --}}
						<li><a href="{{ route('latest') }}">{{ trans('misc.latest') }}</a></li>
{{-- 					<!--<li><a href="{{ route('most.commented') }}">{{trans('misc.most_commented')}}</a></li>-->
						<li><a href="{{ route('most.viewed') }}">{{trans('misc.most_viewed')}}</a></li>
						<li><a href="{{ route('most.downloads') }}">{{trans('misc.most_downloads')}}</a></li> --}}
					</ul><!-- DROPDOWN MENU -->

					@endif
        				<!-- DROPDOWN MENU -->


        			</li>

        		<li class="dropdown">
        			<a href="javascript:void(0);" class="font-default text-uppercase" data-toggle="dropdown">{{trans('misc.categories')}}
        				<i class="ion-chevron-down margin-lft5"></i>
        				</a>

        				<!-- DROPDOWN MENU -->
        				<ul class="dropdown-menu arrow-up nav-session" role="menu" aria-labelledby="dropdownMenu2">
        				@foreach(  App\Models\VectorCategory::where('mode','on')->orderBy('name_en')->take(9)->get() as $category )
        					<li>
        						<a href="{{ route('video.category.show',$category->slug) }}" class="text-overflow">
									@if(App::isLocale('en'))
										{{ $category->name_en }}
									@else
										{{ $category->name_ar }}
									@endif
        							</a>
        					</li>
        					@endforeach

        					@if( App\Models\VectorCategory::count() > 9 )
			        		<li><a href="{{ route('categories') }}">
			        			<strong>{{ trans('misc.view_all') }} </strong>
			        		</a></li>
			        		@endif
        				</ul><!-- DROPDOWN MENU -->
        			</li>

							<li class="dropdown">
	        			<a href="javascript:void(0);" class="font-default text-uppercase" data-toggle="dropdown">
							@if(config('app.locale') =='ar')
							<img src="{{url('img/ar.png')}}" class="flag-select">
							@else
							<img src="{{url('img/en.png')}}" class="flag-select">

							@endif
							{{strtoupper(config('app.locale'))}}
									<span class="title-dropdown">- {{ trans('admin.languages') }}</span>
									<i class="ion-chevron-down margin-lft5"></i>
	        				</a>

	        				<!-- DROPDOWN MENU -->
	        				<ul class="dropdown-menu arrow-up nav-session" role="menu" aria-labelledby="dropdownMenu2">
	        				@foreach(  App\Models\Languages::orderBy('name')->get() as $languages )
	        					<li @if( $languages->abbreviation == config('app.locale') ) class="active" @endif>
	        						<a @if( $languages->abbreviation != config('app.locale') ) href="{{ url('lang',$languages->abbreviation) }}" @endif class="text-overflow">


										@if($languages->abbreviation =='ar')
										<img src="{{url('img/ar.png')}}" class="flag-select">
										@else
											<img src="{{url('img/en.png')}}" class="flag-select">

										@endif
	        						{{ $languages->name }}

	        							</a>
	        					</li>
	        					@endforeach
	        				</ul><!-- DROPDOWN MENU -->
	        			</li>


        		@if( Auth::check() )

        			<li class="dropdown">
			          <a href="javascript:void(0);" data-toggle="dropdown" class="userAvatar myprofile dropdown-toggle font-default text-uppercase">
			          		<img src="{{ asset('avatar').'/'.$userAuth->avatar }}" alt="{{$userAuth->name ?: $userAuth->username}}" class="img-circle avatarUser" width="21" height="21">
						  	{{$userAuth->name ?: $userAuth->username}}
			          		<span class="title-dropdown">{{ trans('users.my_profile') }}</span>
			          		<i class="ion-chevron-down margin-lft5 dropdown-ic"></i>
			          	</a>

			          <!-- DROPDOWN MENU -->
			          <ul class="dropdown-menu dd-close arrow-up nav-session" role="menu" aria-labelledby="dropdownMenu4">


	          		 @if( $userAuth->role == 'admin' )
	          		 	<li>
	          		 		<a href="{{ url('panel/admin') }}" class="text-overflow">
	          		 			<i class="icon icon-Speedometter myicon-right"></i> {{ trans('admin.admin') }}</a>
	          		 			</li>
	          		 			<li role="separator" class="divider"></li>

						  @elseif( $userAuth->role == 'admin_video')
							  <li>
							  <a href="{{ url('video/panel/admin') }}" class="text-overflow">
								  <i class="icon icon-Speedometter myicon-right"></i> {{ trans('admin.admin') }}</a>
							  </li>
							  <li role="separator" class="divider"></li>
						  @endif

	          		 	<li>
	          		 		<a href="{{ route('/video,profile',$userAuth->username) }}" class="myprofile text-overflow">
	          		 			<i class="icon icon-User myicon-right"></i> {{ trans('users.my_profile') }}
	          		 		</a>
	          		 		</li>

	          		 		<li>
	          		 			<a href="{{ route('collections',$userAuth->username) }}">
	          		 			<i class="fa fa-folder-open-o myicon-right"></i> {{ trans('misc.collections') }}
	          		 			</a></li>

	          		 		<li>
	          		 			<a href="{{ route('video.likes') }}" class="text-overflow">
	          		 				<i class="icon icon-Heart myicon-right"></i> {{ trans('users.Archives') }}
	          		 				</a>
	          		 			</li>

	          		 		<li>
	          		 			<a href="{{ route('account') }}" class="text-overflow">
	          		 				<i class="icon icon-Settings myicon-right"></i> {{ trans('users.account_settings') }}
	          		 				</a>
	          		 			</li>
							 <li>
								 <a href="{{route('me.plans')}}" class="text-overflow">
									 <i class="fa fa-th-list myicon-right"></i> {{trans('misc.My subscriptions')}}
								 </a>
							 </li>
                             <li>
								 <a href="{{route('me.invoices')}}" class="text-overflow">
									 <i class="fa fa-file myicon-right"></i> {{trans('global.invoice')}}
								 </a>
                             </li>
						 <li>
							 <a href="{{route('me.images')}}" class="text-overflow">
								 <i class="fa fa-image myicon-right"></i> {{trans('global.myImages')}}
							 </a>
						 </li>
	          		 		<li>
	          		 			<a href="{{ route('logout') }}" class="logout text-overflow">
	          		 				<i class="icon icon-Exit myicon-right"></i> {{ trans('users.logout') }}
	          		 			</a>
	          		 		</li>

	          		 	</ul><!-- DROPDOWN MENU -->

	          		</li>


        		@else

        		@if( $settings->registration_active == '1' )
        			<li>
        				<a class="log-in font-default text-uppercase" href="{{ route('register') }}">
        					<i class="glyphicon glyphicon-user"></i> {{ trans('auth.sign_up') }}
        					</a>
        			</li>
        			@endif

        			<li>
        				<a class="font-default text-uppercase @if( $settings->registration_active == 0 ) log-in @endif" href="{{ route('login') }}">
        					{{ trans('auth.login') }}
        					</a>
        			</li>
        	  @endif
          </ul>



</div><!--/.navbar-collapse -->
      </div>
    </div>
