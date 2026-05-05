<?php $userAuth = Auth::user(); ?>
<header class="default-header">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="{{ route('landPage') }}">
                @if(app()->getLocale() == 'ar')
                    <img src="{{ asset('img/logowa.svg') }}" alt="عربستوك" width="33" height="33" class="logo defult-logo">
                    <img src="{{ asset('img/logoda.svg') }}" alt="عربستوك" width="33" height="33" class="logo logo-up-slider">
                @else
                    <img src="{{ asset('img/logowe.svg') }}" alt="Arabsstock" width="33" height="33" class="logo defult-logo">
                    <img src="{{ asset('img/logode.svg') }}"alt="Arabsstock"  width="33" height="33" class="logo logo-up-slider">
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto home-pages-list">
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="{{route('photos.home')}}">
                            <i class="far fa-camera-alt mr-2"></i>{{ trans('global.Pictures') }} </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="{{route('video.home')}}">
                            <i class="far fa-video mr-2"></i>{{ trans('global.the_video') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" href="{{route('vectors.home')}}">
                            <i class="far fa-tilde fa-lg mr-2"></i>{{ trans('global.vectors') }}</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    @if (is_in_video_website())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('video.categories') }}">{{trans('misc.categories')}}
                        </a>
                    </li>
                    @elseif(is_in_vector_website())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('vectors.categories') }}">{{trans('misc.categories')}}
                        </a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories') }}">{{trans('misc.categories')}}
                        </a>
                    </li>
                    @endif
                    <li class="nav-item dropdown text-capitalize prices">
                        <a class="nav-link color-primary" href="{{ route('plans') }}">
                            {{trans('misc.prices and packages')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ url('lang', cache()->rememberForever('lang_abbreviation_'.config('app.locale'),function(){
    return \DB::table('languages')->where('abbreviation', '!=', config('app.locale') )->select('abbreviation')->value('abbreviation');
})) }}">
                            <i class="fal fa-globe"></i>
                            {{config('app.locale') === 'ar' ? 'EN': 'AR'}}</a>
                    </li>
                    @if( Auth::check()  && (!config("roles.{$userAuth->role}.is_admin")))

                        <li class="nav-item">
                            <a class="nav-link pull-bs-canvas-right" href="#">
                                <i class="far fa-user"></i> {{$userAuth->name ?: $userAuth->username}} </a>
                        </li>
                    @elseif( Auth::check()  && (config("roles.{$userAuth->role}.is_admin")))
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route(config("roles.{$userAuth->role}.redirect_after_login")) }}">
                                <i class="fal fa-user-cog"></i> {{ trans('admin.admin') }}</a>
                        </li>
                        @push('css')
                        <style>a.nav-link.cart{display: none;}</style>
                        @endpush
                    @endif
                    @if(!auth()->check())
                        <li class="nav-item">
                            <a class="nav-link auth-link" data-toggle="modal" data-type="login"  data-target="#login">{{ trans('auth.login') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link auth-link" data-toggle="modal" data-type="signup"
                               data-target="#signup">{{ trans('auth.sign_up') }}</a>
                        </li>
                @endif
                </ul>
            </div>
        </nav>
    </div>
    @stack('after-navbar')
</header>
<div class="bs-canvas bs-canvas-right position-fixed bg-light h-100 account">
    <header class="bs-canvas-header p-35 overflow-auto">
        <button type="button" class="bs-canvas-close close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="d-inline-block mb-0 acount-title">{{ trans('auth.My-Account') }}</h4>
    </header>
    <div class="bs-canvas-content px-3 py-3 nav-pills">
        @if(Auth::check())
            <div class="list-group">
                @if( $userAuth->role == 'admin' )
                    <a href="{{ route('admin.dashboard.index') }}" class="nav-link">
                        <span><i class="fal fa-solar-panel  "></i></span>{{ trans('admin.admin') }} </a>
                    <div class="dropdown-divider"></div>

                @elseif( $userAuth->role == 'admin_video')
                    <a href="{{ route('admin.videos.dashboard.index') }}" class="nav-link">
                        <span><i class="fal fa-solar-panel  "></i></span>{{ trans('admin.admin') }} </a>
                    <div class="dropdown-divider"></div>

                @elseif( $userAuth->role == 'editor_image')
                    <a href="{{ url('/panel/editor') }}" class="nav-link">
                        <span><i class="fal fa-solar-panel  "></i></span>{{ trans('admin.my_collection') }} </a>
                    <div class="dropdown-divider"></div>
                @endif
                <a href="{{ route('user.profile') }}" class="nav-link">
                    <span><i class="fal fa-cog  "></i></span>{{ trans('users.account_settings') }} </a>
                @if(auth()->user()->team && auth()->user()->isLeader())
                <a href="{{ route('team') }}" class="nav-link">
                    <span><i class="fal fa-users  "></i></span>{{ __('Team') }} </a>
                @endif
                <a href="{{ route('account.password') }}" class="nav-link">
                    <span><i class="fal fa-unlock-alt "></i></span>{{ trans('auth.password') }} </a>
                <a href="{{ route('me.collections') }}" class="nav-link">
                    <span><i class="fal fa-album-collection  "></i></span>{{ trans('misc.collections') }} </a>
                <a href="{{ route('account.likes') }}" class="nav-link">
                    <span><i class="fal fa-heart  "></i></span>{{ trans('users.Archives') }} </a>
                <a href="{{route('me.plans')}}" class="nav-link">
                    <span><i class="fal fa-grip-horizontal  "></i></span>{{trans('misc.My subscriptions')}} </a>
                <a href="{{route('me.invoices')}}" class="nav-link"><span> <i class="fal fa-file-alt  "></i>
                    </span>{{trans('global.invoice')}} </a>
                <a href="{{route('me.images')}}" class="nav-link">
                    <span><i class="fal fa-camera-alt  "></i></span>{{trans('global.myImages')}} </a>
                <a href="{{route('me.videos')}}" class="nav-link">
                    <span><i class="fal fa-video  "></i></span>{{trans('global.myVideos')}} </a>
                <a class="nav-link" href="{{route('me.vectors')}}"><span><i
                            class="fal fa-tilde fa-lg"></i> </span>{{trans('global.myVectors')}} </a>
                <a href="{{ route('logout') }}" class="logout nav-link">
                    <span><i class="fal fa-sign-in  "></i></span>{{ trans('users.logout') }} </a>
            </div>
        @endif
    </div>
</div>
<div id="auth_component">

</div>
@push('javascript_navbar')
    @include('includes.check_mobile')
    @if(!$userAuth)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"
                defer></script>
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css"/>
    @endif
@endpush
