@extends('app')
@push('ld_json')
    <script data-react-helmet="true"
            type="application/ld+json">{!! json_encode(["@context" => "http://schema.org","@context" => "http://schema.org","@type" => "BreadcrumbList",'itemListElement' => [["@type" => "ListItem","position" => 1,'item' => ["@id" => route('video.home'),"name" => __('misc.videos')],]]],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script
        type="application/ld+json">{!! json_encode(["@context"=> "http://schema.org","@type"=> "WebSite","url"=> route('video.home'),"potentialAction"=> ["@type"=> "SearchAction","target"=> url(app()->getLocale().'/ar/videos/search/{search_term}'),"query-input"=> "required name=search_term"]]) !!}</script>
@endpush
@section('meta')
    <link rel="canonical" href="{{ route('video.home') }}"/>
@endsection
@section('content')
    <style>@media (min-width: 992px){
            header.default-header{background: #fff0;position: absolute;top: 0;z-index: 9;width: 100%}

            header.default-header .bg-light{background-color: #fff0 !important}

            header.default-header .navbar-light .navbar-nav .nav-link{color: #fff}

            header.default-header .navbar-light .navbar-nav .nav-link:focus, header.default-header .navbar-light .navbar-nav .nav-link:hover{color: #eee}

            header.default-header .navbar-light .navbar-nav.home-pages-list li a.nav-link{color: #30354b}

            header.default-header .navbar-light .navbar-nav.home-pages-list li a.nav-link:hover{color: #20d598}

            .hero-header.landingpage-video{background: url(../img/bg-video-home-2.webp);background-size: cover;background-repeat: no-repeat;background-position: center;height: 85vh !important}

            .logo-up-slider{display: block}

            .defult-logo{display: none}
        }
        header.default-header .navbar-light .navbar-nav .nav-link.color-primary{color: #20d598}</style>
    <div class="hero-header jumbo-banner landingpage landingpage-video  h-60vh" data-overlay="5">
        <video id="videoBG" class="d-none d-sm-block" autoplay loop muted playsinline>
            <source src="{{ asset('video/bg.mp4') }}" type="video/mp4"/>
        </video>
        <video id="videoBG" autoplay loop muted playsinline></video>
        <source data-src="" type="video/mp4"/>
        <video id="videoBG" class="d-block d-sm-none" autoplay loop muted playsinline>
            <source src="{{ asset('video/bg_iphone.mp4') }}" type="video/mp4"/>
            <source src="{{ asset('video/bg_iphone.webm') }}" type="video/webm">
        </video>
        <div class="container-fluid">
            <div class="row">
                <div class="d-none d-sm-none d-md-none d-lg-block col-lg-2"></div>
                <div class="col-12 col-sm-12 col-md-10 col-lg-8">
                    <h1 class="lead">{{ trans('global.welcome_subtitle') }}</h1>
                <!-- <p class="lead">{{$settings->welcome_subtitle_en}}</p> -->
                    @include('includes.search_bar')
                </div>
                <div class="d-none d-sm-none d-md-none d-log-block col-lg-2"></div>
                <!--  Demos -->
            </div>
        </div>
    </div>

    <section class="owl-section-category">
        <div class="container-fluid">
            <div class="owl-carousel">
                @if($top_categories)
                    @foreach($top_categories as $category)
                        <div class="item"><a href="{{ route('video.category.show',$category->slug) }}">
                                <span>{{$category->name}}</span>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    @if($canReserve->count())
        <section class="pt-50 pb-50">
            <div class="container-fluid">
                <div class="row justify-content-between">
                    <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                        <h1 class="text-capitalize color-primary">{{ __('Exclusively Purchase Contents') }}</h1>
                        <p class="color-secondary">
{{--                            {{ trans('global.most_searched') }}--}}
                            <a href="{{route('video.can_reserve')}}" class="d-block d-sm-none float-right">
                                <span class="color-primary">{{ trans('global.more') }}</span>
                            </a>
                        </p>
                    </div>
                    <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                        <div class="mt-5 pt-3 mb-0">
                            <a href="{{route('video.can_reserve')}}">
                                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="canReserve" class="flex-images">
                    @include('video.includes.videos',['videos'=>$canReserve])
                </div>
            </div>
        </section>
    @endif
    <section class="pt-50 pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h1 class="text-capitalize color-primary">{{ trans('global.Video_next_project') }}</h1>
                    <p class="color-secondary">{{ __('The latest :count :type',['count'=>$videos->total(),'type'=>__('Video')]) }}
                        <a href="{{route('video.latest')}}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0">
                        <a href="{{route('video.latest')}}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div id="videogrid" class="flex-images">
                @if( $videos->total() != 0 )
                    @include('video.includes.videos')
                @else
                    <div class="btn-block text-center">
                        <i class="fal fa-exclamation-circle"></i>
                    </div>

                    <h3 class="margin-top-none text-center no-result no-result-mg">
                        {{ trans('misc.no_videos_published') }}
                    </h3>
                @endif
            </div>
        </div>
    </section>
    @if (isset($categoriesTrending) && $categoriesTrending != '' && count($categoriesTrending) >0)

        <section class="pb-50">
            <div class="container-fluid">
                <div class="row justify-content-between">
                    <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                        <h1 class="text-capitalize color-primary">{{ trans('global.Trending Topics') }}</h1>
                        <p class="color-secondary">{{ trans('global.Content you need at this time') }}
                            <a href="{{ route('video.categories') }}" class="d-block d-sm-none float-right">
                                <span class="color-primary">{{ trans('global.more') }}</span>
                            </a>
                        </p>
                    </div>
                    <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                        <div class="mt-5 pt-3 mb-0"><a href="{{ route('video.categories') }}">
                                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                            </a></div>
                    </div>
                </div>
                <div class="center-row-section row">
                    @if($categoriesTrending)
                        @foreach( $categoriesTrending as $categoryTrendingItem )
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                                <div class="card-category"
                                     style="background-image: url('{{ $categoryTrendingItem->thumbnail }}')">
                                    <a href="{{ route('video.category.show',$categoryTrendingItem->slug) }}">
                                        <div class="hover">
                                            <div class="hover-overlay"></div>
                                            <div class="card-category-content">
                                                <h3 class="card-category-title">{{ $categoryTrendingItem->name }}</h3>
                                            </div>
                                        </div>
                                    </a></div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
    @endif


    <section class="pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h1 class="text-capitalize color-primary">{{ trans('global.Most_popular_topics') }}</h1>
                    <p class="color-secondary">{{ trans('global.most_searched') }}
                        <a href="{{route('video.categories')}}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{ route('video.categories') }}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a></div>
                </div>
            </div>
            <div class="center-row-section row">
                @if($categories)
                    @foreach( $categories as $category )
                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                            <div class="card-category"
                                 style="background-image: url('{{ $category->thumbnail }}')">
                                <a href="{{ route('video.category.show',$category->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                        <div class="card-category-content">
                                            <h3 class="card-category-title">{{ $category->name }}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="" style="clear: both">
                </div>
            </div>
        </div>
    </section>

    <section class="pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h1 class="text-capitalize color-primary">{{ trans('global.Cities_landmarks') }}</h1>
                    <p class="color-secondary">{{ trans('global.More_than') }}
                        <a href="{{route('video.categories')}}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{route('video.categories')}}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a></div>
                </div>
            </div>
            <div class="center-row-section row">
                @if($categoriesCities)
                    @foreach( $categoriesCities as $categoryCitieItem )
                        <div class="col-12 col-sm-6 col-md-3 col-lg-3 mb-2 pd-l-r--3">
                            <div class="cities-category"
                                 style="background-image: url('{{ $categoryCitieItem->thumbnail}}')">
                                <a href="{{ route('video.category.show',$categoryCitieItem->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                    </div>
                                </a>
                            </div>
                            <h4 class="mt-3 mb-4">{{ $categoryCitieItem->name }}</h4>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="" style="clear: both">
            </div>
        </div>
    </section>
    <section class="pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h1 class="text-capitalize color-primary">{{ trans('global.Chroma') }}</h1>
                    <p class="color-secondary">{{ trans('global.Green_background_easier_for_you') }}
                        <a href="{{ route('video.category.show','chroma') }}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{ route('video.category.show','chroma') }}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a></div>
                </div>
            </div>
            <div class="center-row-section row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2 pd-l-r--3">
                    <div class="chroma-category" style="background-image: url('/img/chroma.jpg')">
                        <a href="{{ route('video.category.show','chroma') }} ">
                            <div class="hover">
                                <div class="hover-overlay"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="" style="clear: both">
            </div>
        </div>
    </section>


    @if(count($tag)>0 && $tag[0]!="")
        <section class="trending-searches pt-50 pb-50 text-center">
            <div class="container-fluid">
                <h1 class="text-capitalize color-primary mt-0">{{__('misc.Trending_Stock_Photo_Searches')}}</h1>
                <p class="color-secondary mb-5 mt-0">  {{__('misc.tagsDescription')}}  </p>
                @foreach($tag as $key=>$value)
                    <a class="btn btn-outline-light rounded-pill"
                       href="{{ route('video.tags.show',  preg_replace('/[[:space:]]+/', '-',($value)) ) }}"> {{$value}} </a>
                @endforeach
            </div>
        </section>
    @endif
    <section class="banner-footer">
        <div class="container">
            <h2 class="color-white mb-5">{{ trans('global.Thousands of digital content and themes, and more') }}  </h2>
            <a data-toggle="modal" data-target="#signup"
               class="btn btn-primary btn-lg"> {{ trans('auth.sign_up') }} </a>
        </div>
    </section>
    @include('includes.newsletter')

@endsection

@push('javascript_navbar')
    @if($canReserve->count())
        <script>
            $('#canReserve').flexImages({object: '.arabs-video', rowHeight: 200, truncate: 1, maxRows: 2});
        </script>
    @endif
@endpush
