@extends('app') @section('content')

    <style>
        header{
            box-shadow: 0 2px 4px 0 rgba(12, 18, 28, .12);}
    </style>
    @php($current_route=optional(request()->route())->getName())
    <div class="account">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-3 col-md-3 col-lg-3 pr-md-0">
                    <div class="nav flex-column nav-pills pb-50 pt-5 nav-a-d-none-xs">
                        <h4 class="acount-title"> {{ trans('auth.My-Account') }} <a
                                class="nav-link pull-bs-canvas-right d-block d-sm-none" href="#"> <i
                                    class="fal fa-list-alt"></i> </a></h4>
                        @if( Auth::check() )
                            @if( $user->role == 'admin' )
                                <a class="nav-link" href="{{ route('admin.dashboard.index') }}"><span>
                                    <i class="fal fa-solar-panel  "></i>
                                </span> {{ trans('admin.admin') }}
                                </a>
                                <div class="dropdown-divider"></div>
                            @elseif( $user->role == 'admin_video')
                                <a class="nav-link" href="{{ route('admin.videos.dashboard.index') }}"><span> <i
                                            class="fal fa-solar-panel  "></i></span> {{ trans('admin.admin') }} </a>
                                <div class="dropdown-divider"></div>
                            @elseif( $user->role == 'editor_image')
                                <a class="nav-link" href="{{ url('/panel/editor') }}"><span> <i
                                            class="fal fa-solar-panel  "></i></span>{{ trans('admin.my_collection') }}
                                </a>
                                <div class="dropdown-divider"></div>
                            @endif
                            <a class="nav-link {{ $current_route=='user.profile'?'active':'' }}"
                               href="{{ route('user.profile') }}"><span><i
                                        class="fal fa-cog  "></i></span> {{ trans('users.account_settings') }} </a>
                            @if($user->team && $user->isLeader())
                                <a class="nav-link {{ $current_route=='team'?'active':'' }}"
                                   href="{{ route('team') }}"><span><i
                                            class="fal fa-users  "></i></span> {{ __('Team') }} </a>
                            @endif
                            <a class="nav-link {{ $current_route=='account.password'?'active':'' }}"
                               href="{{ route('account.password') }}"><span><i
                                        class="fal fa-unlock-alt  "></i></span> {{ trans('auth.password') }} </a>
                        <!-- <a class="nav-link" href="{{ route('account.profile',$user->username) }}"><span><i class="fal fa-user  "></i></span> {{ trans('users.my_profile') }} </a> -->
                            <a class="nav-link {{ $current_route=='me.collections'?'active':'' }}"
                               href="{{ route('me.collections') }}"><span><i
                                        class="fal fa-album-collection"></i></span> {{ trans('misc.collections') }} </a>
                            <a class="nav-link {{ $current_route=='account.likes'?'active':'' }}"
                               href="{{ route('account.likes') }}"><span><i
                                        class="fal fa-heart"></i></span> {{ trans('users.Archives') }} </a>
                            <a class="nav-link {{ $current_route=='me.plans'?'active':'' }}"
                               href="{{route('me.plans')}}"><span><i
                                        class="fal fa-grip-horizontal"></i></span> {{trans('misc.My subscriptions')}}
                            </a>
                            <a class="nav-link {{ $current_route=='me.invoices'?'active':'' }}"
                               href="{{route('me.invoices')}}"><span><i
                                        class="fal fa-file-alt"></i></span> {{trans('global.invoice')}} </a>
                            <a class="nav-link {{ $current_route=='me.images'?'active':'' }}"
                               href="{{route('me.images')}}"><span> <i
                                        class="fal fa-camera-alt"></i> </span>{{trans('global.myImages')}} </a>
                            <a class="nav-link {{ $current_route=='me.videos'?'active':'' }}"
                               href="{{route('me.videos')}}"><span><i
                                        class="fal fa-video"></i> </span>{{trans('global.myVideos')}} </a>
                            <a class="nav-link {{ $current_route=='me.vectors'?'active':'' }}"
                               href="{{route('me.vectors')}}"><span><i
                                        class="fal fa-tilde fa-lg"></i> </span>{{trans('global.myVectors')}} </a>

                        @endif
                    </div>
                </div>
                <div
                    class="col-12 col-sm-9 col-md-9 col-lg-9 border-menu pb-50 pt-3 pt-md-5 pt-lg-5">@yield('profile_content')</div>
            </div>
        </div>
    </div>

    <div class="bs-canvas bs-canvas-right position-fixed bg-light h-100 account">
        <header class="bs-canvas-header p-35 overflow-auto">
            <button type="button" class="bs-canvas-close close" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="d-inline-block mb-0 acount-title">{{ trans('auth.My-Account') }}</h4>
        </header>
        <div class="bs-canvas-content px-3 py-3 nav-pills">
            <div class="list-group">
                @if( Auth::check() )
                    @if( $user->role == 'admin' )
                        <a href="{{ route('admin.dashboard.index') }}" class="nav-link"><span> <i
                                    class="fal fa-solar-panel  "></i></span>{{ trans('admin.admin') }} </a>
                        <div class="dropdown-divider"></div>

                    @elseif( $user->role == 'admin_video')
                        <a href="{{ route('admin.videos.dashboard.index') }}" class="nav-link"> <span> <i
                                    class="fal fa-solar-panel  "></i></span>{{ trans('admin.admin') }} </a>
                        <div class="dropdown-divider"></div>

                    @elseif( $user->role == 'editor_image')
                        <a href="{{ url('/panel/editor') }}" class="nav-link"> <span> <i
                                    class="fal fa-solar-panel  "></i></span>{{ trans('admin.my_collection') }} </a>
                        <div class="dropdown-divider"></div>
                    @endif

                    <a href="{{ route('user.profile') }}" class="nav-link"><span> <i
                                class="fal fa-cog  "></i></span>{{ trans('users.account_settings') }} </a>
                    @if($user->team && $user->isLeader())
                        <a class="nav-link {{ $current_route=='team'?'active':'' }}"
                           href="{{ route('team') }}"><span><i
                                    class="fal fa-users  "></i></span> {{ __('Team') }} </a>
                    @endif
                    <a href="{{ route('account.password') }}" class="nav-link"> <span><i class="fal fa-unlock-alt "></i></span>{{ trans('auth.password') }}
                    </a>
                    <a href="{{ route('me.collections') }}" class="nav-link"> <span><i
                                class="fal fa-album-collection  "></i></span>{{ trans('misc.collections') }} </a>
                    <a href="{{ route('account.likes') }}" class="nav-link"><span> <i
                                class="fal fa-heart  "></i></span>{{ trans('users.Archives') }} </a>
                    <a href="{{route('me.plans')}}" class="nav-link">
                        <span><i class="fal fa-grip-horizontal  "></i></span>{{trans('misc.My subscriptions')}} </a>
                    <a href="{{route('me.invoices')}}" class="nav-link"><span> <i
                                class="fal fa-file-alt  "></i></span>{{trans('global.invoice')}} </a>
                    <a href="{{route('me.images')}}" class="nav-link"><span> <i
                                class="fal fa-camera-alt  "></i></span>{{trans('global.myImages')}} </a>
                    <a href="{{route('me.videos')}}" class="nav-link"> <span><i
                                class="fal fa-video  "></i></span>{{trans('global.myVideos')}} </a>
                    <a class="nav-link" href="{{route('me.vectors')}}"><span><i
                                class="fal fa-tilde fa-lg"></i> </span>{{trans('global.myVectors')}} </a>
                    <a href="{{ route('logout') }}" class="logout nav-link"> <span><i
                                class="fal fa-sign-in  "></i></span>{{ trans('users.logout') }} </a>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('javascript')
@endsection
