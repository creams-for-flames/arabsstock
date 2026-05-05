@extends('app')
@push('ld_json')
    <script data-react-helmet="true"
            type="application/ld+json">{!! json_encode(["@context" => "http://schema.org","@context" => "http://schema.org","@type" => "BreadcrumbList",'itemListElement' => [["@type" => "ListItem","position" => 1,'item' => ["@id" => route('photos.home'),"name" => __('misc.images')],]]],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    <script
        type="application/ld+json">{!! json_encode(["@context"=> "http://schema.org","@type"=> "WebSite","url"=> route('photos.home'),"potentialAction"=> ["@type"=> "SearchAction","target"=> url(app()->getLocale().'/ar/photos/search/{search_term}'),"query-input"=> "required name=search_term"]]) !!}</script>
@endpush
@section('meta')
    <link rel="canonical" href="{{ route('photos.home') }}"/>
@endsection
@section('content')
    <style>@media (min-width: 992px){
            header.default-header{background: #fff0;position: absolute;top: 0;z-index: 9;width: 100%}

            header.default-header .bg-light{background-color: #fff0 !important}

            header.default-header .navbar-light .navbar-nav .nav-link{color: #fff}

            header.default-header .navbar-light .navbar-nav .nav-link:focus, header.default-header .navbar-light .navbar-nav .nav-link:hover{color: #eee}

            header.default-header .navbar-light .navbar-nav.home-pages-list li a.nav-link{color: #30354b}

            header.default-header .navbar-light .navbar-nav.home-pages-list li a.nav-link:hover{color: #20d598}

            .hero-header.h-60vh{background: url(../img/bg-img-home-2.webp);background-size: cover;background-repeat: no-repeat;background-attachment: fixed;background-position: center;height: 85vh !important}

            .logo-up-slider{display: block}

            .defult-logo{display: none}
        }
        .hero-header.h-60vh{background: url(../img/bg-img-home-2.webp);background-size: cover;background-repeat: no-repeat;background-position: center}
        header.default-header .navbar-light .navbar-nav .nav-link.color-primary{color: #20d598}</style>
    <div class="hero-header jumbo-banner landingpage h-60vh" data-overlay="5">
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
            <!--  Demos -->
        </div>
    </div>
    <section class="owl-section-category">
        <div class="container-fluid">
            <div class="owl-carousel">
                @if($top_categories)
                    @foreach($top_categories as $category )
                        <div class="item"><a href="{{ route('category.show',$category->slug) }}">
                                <span>{{$category->name}}</span>
                            </a></div>
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
                        <h2 class="text-capitalize color-primary">{{ __('Exclusively Purchase Contents') }}</h2>
                        <p class="color-secondary">
                            {{--                        {{ trans('global.most_searched') }}--}}
                            <a href="{{ route('latest') }}" class="d-block d-sm-none float-right">
                                <span class="color-primary">{{ trans('global.more') }}</span>
                            </a>
                        </p>
                    </div>
                    <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                        <div class="mt-5 pt-3 mb-0">
                            <a href="{{ route('photos.can_reserve') }}">
                                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="canReserve" class="flex-images">
                    @foreach( $canReserve as $r )
                        <div class="item card-photo" data-w="{{($r->width_thumbnail)}}px"
                             data-h="{{($r->height_thumbnail)}}px">
                            <div class="hover border-file">
                                <a href="{{ $r->post_link }}">
                                    <picture>
                                        <source
                                            data-srcset="{{ cdn($r->thumbnail) }}"/>
                                        <img
                                            data-src="{{ cdn($r->thumbnail) }}"
                                            src="{{cdn($r->thumbnail)}}"
                                            width="{{($r->width_thumbnail??300)}}" height="{{($r->height_thumbnail??300)}}"
                                            class="lazyload w-100 h-100"
                                            alt="{{ $r->img_caption }}"/>
                                    </picture>
                                    <div class="hover-overlay"></div>
                                </a>
                                <div class="card-photo-content">
                                    <h3 class="card-photo-title">{{$r->title}}</h3>
                                    <div class="icon">
                                        <div class="d-flex flex-row-reverse">
                                            <div class="icon_save">
                                            <span>
                                                <i data-id="{{$r->id}}" data-like="{{trans('misc.like')}}"
                                                   data-unlike="{{trans('misc.unlike')}}"
                                                   data-type="{{class_basename($r)}}"
                                                   class="fal fa-heart @if($r->is_like) active @endif likeButton"></i>
                                            </span>
                                                <span>{{__('misc.like')}}</span>
                                            </div>
                                            <div class="icon_save"
                                                 onclick="showModal('{{$r->id}}','{{class_basename($r)}}','{{ cdn($r->thumbnail) }}','{{$r->title}}')">
                                                <span><i class="fal fa-plus-circle"></i></span>
                                                <span>{{__('misc.save_to_collection')}}</span>
                                            </div>
                                            <div class="icon-similar">
                                                <a href="{{route('similar.files',['type'=>'photos','section'=>'image','id'=>$r->id ])}}">
                                                   <span><i class="fal fa-th"></i></span>
                                                   <span>{{__('misc.similar')}}</span>
                                               </a>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <section class="pt-50 pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h2 class="text-capitalize color-primary">{{ trans('global.Pictures_next_project') }}</h2>
                    <p class="color-secondary">{{ __('The latest :count :type',['count'=>$images->total(),'type'=>__('Image')]) }}
                        <a href="{{ route('latest') }}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0">
                        <a href="{{ route('latest') }}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div id="imageslandpage" class="flex-images">
                @foreach( $images as $r )

                    <div class="item card-photo" data-w="{{($r->width_thumbnail)}}px"
                         data-h="{{($r->height_thumbnail)}}px">
                        <div class="hover border-file h-100">
                            <a href="{{ $r->post_link }}">
                                <picture>
                                    <source
                                        data-srcset="{{ cdn($r->thumbnail) }}"/>
                                    <img
                                        data-src="{{ cdn($r->thumbnail) }}"
                                        src="{{ cdn($r->thumbnail) }}"
                                        width="{{($r->width_thumbnail??300)}}" height="{{($r->height_thumbnail??300)}}"
                                        class="lazyload w-100 h-100"
                                        alt="{{ $r->img_caption }}"/>
                                </picture>
                                <div class="hover-overlay"></div>
                            </a>
                            <div class="card-photo-content">
                                <h3 class="card-photo-title">{{$r->title}}</h3>
                                <div class="icon">
                                    <div class="d-flex flex-row-reverse">
                                        <div class="icon_save">
                                            <span>
                                                <i data-id="{{$r->id}}" data-like="{{trans('misc.like')}}"
                                                   data-unlike="{{trans('misc.unlike')}}"
                                                   data-type="{{class_basename($r)}}"
                                                   class="fal fa-heart @if($r->is_like) active @endif likeButton"></i>
                                            </span>
                                            <span>{{__('misc.like')}}</span>
                                        </div>
                                        <div class="icon_save"
                                             onclick="showModal('{{$r->id}}','{{class_basename($r)}}','{{ cdn($r->thumbnail) }}','{{$r->title}}')">
                                            <span><i class="fal fa-plus-circle"></i></span>
                                            <span>{{__('misc.save_to_collection')}}</span>
                                        </div>
                                        <div class="icon-similar">
                                            <a href="{{route('similar.files',['type'=>'photos','section'=>'image','id'=>$r->id ])}}">
                                                <span><i class="fal fa-th"></i></span>
                                                <span>{{ __('misc.similar') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>




    @if(isset($categoriesTrending) && $categoriesTrending != '' && count($categoriesTrending) > 0)
        <section class="pb-50">
            <div class="container-fluid">
                <div class="row justify-content-between">
                    <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                        <h2 class="text-capitalize color-primary">{{ trans('global.Trending Topics') }}</h2>
                        <p class="color-secondary">{{ trans('global.Content you need at this time') }}
                            <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
                                <span class="color-primary">{{ trans('global.more') }}</span>
                            </a>
                        </p>
                    </div>
                    <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                        <div class="mt-5 pt-3 mb-0"><a href="{{ route('categories') }}">
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
                                    <a href="{{ route('category.show',$categoryTrendingItem->slug) }}">
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
                    <h2 class="text-capitalize color-primary">{{ trans('global.Most_popular_topics') }}</h2>
                    <p class="color-secondary">{{ trans('global.most_searched') }}
                        <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{ route('categories') }}">
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
                                <a href="{{ route('category.show',$category->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                        <div class="card-category-content">
                                            <h3 class="card-category-title">{{ $category->name }}</h3>
                                        </div>
                                    </div>
                                </a></div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <section class="pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h2 class="text-capitalize color-primary">{{ trans('global.Cities_landmarks') }}</h2>
                    <p class="color-secondary">{{ trans('global.More_than') }}
                        <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a></p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{route('categories')}}">
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
                                <a href="{{ route('category.show',$categoryCitieItem->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                    </div>
                                </a>
                            </div>
                            <h4 class="mt-3">{{ $categoryCitieItem->name }}</h4>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="" style="clear: both">
            </div>
    </section>

    @if($categoriesPeople)
        <section class="pb-50">
            <div class="container-fluid">
                <div class="row justify-content-between">
                    <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                        <h2 class="text-capitalize color-primary">{{ trans('global.People') }}</h2>
                        <p class="color-secondary">{{ trans('global.Portrait') }}
                            <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
                                <span class="color-primary">{{ trans('global.more') }}</span>
                            </a>
                        </p>
                    </div>
                    <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                        <div class="mt-5 pt-3 mb-0">
                            <a href="{{route('categories')}}">
                                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="center-row-section row">
                    @if($categoriesPeople)
                        @foreach( $categoriesPeople as $categoryPeople )
                            <div class="col-12 col-sm-6 col-md-3 col-lg-3 mb-2 pd-l-r--3">
                                <div class="cities-category"
                                     style="background-image: url('{{ $categoryPeople->thumbnail}}')">
                                    <a href="{{ route('category.show',$categoryPeople->slug) }}">
                                        <div class="hover">
                                            <div class="hover-overlay"></div>
                                        </div>
                                    </a>
                                </div>
                                <h4 class="mt-3 mb-4">{{ $categoryPeople->name }}</h4>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="" style="clear: both;"></div>
                <!-- container wrap-ui -->
            </div>
        </section>
    @endif


    @if(count($tag)>0 && $tag[0]!="")

        <section class="trending-searches pt-50 pb-50 text-center">
            <div class="container-fluid">
                <h2 class="text-capitalize color-primary mt-0">{{__('misc.Trending_Stock_Photo_Searches')}}</h2>
                <p class="color-secondary mb-5 mt-0">  {{__('misc.tagsDescription')}}  </p>
                @foreach($tag as $key=>$value)
                    <a class="btn btn-outline-light rounded-pill"
                       href="{{ route('tags.show', preg_replace('/[[:space:]]+/', '-',($value)) ) }} "> {{$value}} </a>

                @endforeach
            </div>
        </section>
    @endif

    <section class="banner-footer">
        <div class="container">
            <h2 class="color-white mb-5">{{ trans('global.Thousands of digital content and themes, and more') }}  </h2>
            <a href="{{route('plans')}}"
               class="btn btn-primary btn-lg"> {{ trans('global.Discover-packages-prices') }} </a>
        </div>
    </section>
    @include('includes.newsletter')

    <!-- Modal -->

@endsection
@push('javascript_navbar')
    <script>
        $('#imageslandpage').flexImages({rowHeight: 300, maxRows: 2});
        $('#canReserve').flexImages({rowHeight: 300, maxRows: 2});
    </script>
@endpush
