@extends('app')
@push('ld_json')
    <script data-react-helmet="true"
            type="application/ld+json">{!! json_encode(["@context" => "http://schema.org","@context" => "http://schema.org","@type" => "BreadcrumbList",'itemListElement' => [["@type" => "ListItem","position" => 1,'item' => ["@id" => route('vectors.home'),"name" => __('misc.vectors')],]]],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    <script
        type="application/ld+json">{!! json_encode(["@context"=> "http://schema.org","@type"=> "WebSite","url"=> route('vectors.home'),"potentialAction"=> ["@type"=> "SearchAction","target"=> url(app()->getLocale().'/ar/vectors/search/{search_term}'),"query-input"=> "required name=search_term"]]) !!}</script>
@endpush
@section('meta')
    <link rel="canonical" href="{{ route('vectors.home') }}"/>
@endsection
@section('content')
    <style>@media (min-width: 992px){header.default-header{background:#fff0;position:absolute;z-index:9;width:100%}header.default-header .bg-light{background-color:#fff0!important}header.default-header .navbar-light .navbar-nav .nav-link{color:#fff}header.default-header .navbar-light .navbar-nav .nav-link:focus,header.default-header .navbar-light .navbar-nav .nav-link:hover{color:#eee}header.default-header .navbar-light .navbar-nav.home-pages-list li a.nav-link{color:#30354b}header.default-header .navbar-light .navbar-nav.home-pages-list li a.nav-link:hover{color:#20d598}.hero-header.h-60vh{background:url(../../img/bg-vector-2.webp);background-size:cover;background-repeat:no-repeat;background-attachment:fixed;background-position:center}.logo-up-slider{display:block}.defult-logo{display:none}}.hero-header.h-60vh{background:url(../../img/bg-vector-2.webp);background-size:cover;background-repeat:no-repeat;background-position:center;height:85vh!important}header.default-header .navbar-light .navbar-nav .nav-link.color-primary{color:#20d598}.banner-footer{background:url('{!! asset('img/bg-footer-vector.webp') !!}')!important;background-size:cover!important;background-attachment:fixed!important}@media screen and (max-width: 767px){.banner-footer{background-attachment:unset!important}}</style>
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
                        <div class="item"><a href="{{ route('vectors.category.show',$category->slug) }}">
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
                    <h1 class="text-capitalize color-primary">{{ __('Exclusively Purchase Contents') }}</h1>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{ route('vectors.can_reserve') }}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a></div>
                </div>
            </div>
            <div id="canReserve" class="flex-images">
                @foreach( $canReserve as $vector )
                    <div class="item card-photo" data-w="{{($vector->width_thumbnail)}}"
                         data-h="{{($vector->height_thumbnail)}}">
                        <div class="hover h-100 border-file">
                            <a href="{{$vector->post_link}}">
                                <picture>
                                    <source
                                        data-srcset="{{ cdn($vector->thumbnail) }}"/>
                                    <img
                                        data-src="{{ cdn($vector->thumbnail) }}"
                                        src="/blank.gif"
                                        class="lazyload w-100 h-100"
                                        alt="{{ $vector->img_caption }}"/>
                                </picture>
                                <div class="hover-overlay"></div>
                            </a>
                            <div class="card-photo-content">
                                <h3 class="card-photo-title">{{$vector->title}}</h3>
                                <div class="icon">
                                    <div class="d-flex flex-row-reverse">
                                        <div class="icon_save">
                                            <span>
                                                <i data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}"
                                                   data-unlike="{{trans('misc.unlike')}}"
                                                   data-type="{{class_basename($vector)}}"
                                                   class="fal fa-heart @if($vector->is_like) active @endif likeButton"></i>
                                            </span>
                                            <span>{{__('misc.like')}}</span>
                                        </div>
                                        <div class="icon_save"
                                             onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ cdn($vector->thumbnail) }}','{{$vector->title}}')">
                                            <span><i class="fal fa-plus-circle"></i></span>
                                            <span>{{__('misc.save_to_collection')}}</span>
                                        </div>
                                        <div class="icon-similar">
                                            <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$vector->id ])}}">
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
    @endif
    @if($vectors->count())
        <section class="pt-50 pb-50">
            <div class="container-fluid">
                <div class="row justify-content-between">
                    <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                        <h2 class="text-capitalize color-primary">{{ trans('global.vectors_next_project') }}</h2>
                        <p class="color-secondary">{{ __('The latest :count :type',['count'=>$vectors->total(),'type'=>__('Vector')]) }}
                            <a href="{{ route('vectors.latest') }}" class="d-block d-sm-none float-right">
                                <span class="color-primary">{{ trans('global.more') }}</span>
                            </a>
                        </p>
                    </div>
                    <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                        <div class="mt-5 pt-3 mb-0">
                            <a href="{{ route('vectors.latest') }}">
                                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="imageslandpage" class="flex-images">
                    @foreach( $vectors as $vector )
                        <div class="item card-photo" data-w="{{($vector->width_thumbnail)}}"
                             data-h="{{($vector->height_thumbnail)}}">
                            <div class="hover h-100 border-file">
                                <a href="{{$vector->post_link}}">
                                    <picture>
                                        <source
                                            data-srcset="{{ cdn($vector->thumbnail) }}"/>
                                        <img
                                            data-src="{{ cdn($vector->thumbnail) }}"
                                            src="/blank.gif"
                                            class="lazyload w-100 h-100"
                                            alt="{{ $vector->img_caption }}"/>
                                    </picture>
                                    <div class="hover-overlay"></div>
                                </a>
                                <div class="card-photo-content">
                                    <h3 class="card-photo-title">{{$vector->title}}</h3>
                                    <div class="icon">
                                        <div class="d-flex flex-row-reverse">
                                            <div class="icon_save">
                                            <span>
                                                <i data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}"
                                                   data-unlike="{{trans('misc.unlike')}}"
                                                   data-type="{{class_basename($vector)}}"
                                                   class="fal fa-heart @if($vector->is_like) active @endif likeButton"></i>
                                            </span>
                                                <span>{{__('misc.like')}}</span>
                                            </div>
                                            <div class="icon_save"
                                                 onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ cdn($vector->thumbnail) }}','{{$vector->title}}')">
                                                <span><i class="fal fa-plus-circle"></i></span>
                                                <span>{{__('misc.save_to_collection')}}</span>
                                            </div>
                                            <div class="icon-similar">
                                                <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$vector->id ])}}">
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
    @endif
    <section class="pt-50 pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h1 class="text-capitalize color-primary">{{ trans('global.vectors') }} {{ trans('global.next_project') }}</h1>
                    <p class="color-secondary">{{ trans('global.more_download') }}
                        <a href="{{ route('vectors.latest') }}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{ route('vectors.latest') }}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a></div>
                </div>
            </div>
            <div id="imageslandpage" class="flex-images">
                @foreach( $vectors as $vector )

                    <div class="item card-photo" data-w="{{($vector->width_thumbnail)}}px"
                         data-h="{{($vector->height_thumbnail)}}px">
                        <div class="hover h-100 border-file">
                            <a href="{{$vector->post_link}}">
                                <picture>
                                    <source
                                        data-srcset="{{ cdn($vector->thumbnail) }}"/>
                                    <img
                                        data-src="{{ cdn($vector->thumbnail) }}"
                                        src="/blank.gif"
                                        class="lazyload w-100 h-100"
                                        alt="{{ $vector->img_caption }}"/>
                                </picture>
                                <div class="hover-overlay"></div>
                            </a>
                            <div class="card-photo-content">
                                <h3 class="card-photo-title">{{$vector->title}}</h3>
                                <div class="icon">
                                    <div class="d-flex flex-row-reverse">
                                        <div class="icon_save">
                                            <span>
                                                <i data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}"
                                                   data-unlike="{{trans('misc.unlike')}}"
                                                   data-type="{{class_basename($vector)}}"
                                                   class="fal fa-heart @if($vector->is_like) active @endif likeButton"></i>
                                            </span>
                                            <span>{{__('misc.like')}}</span>
                                        </div>
                                        <div class="icon_save"
                                             onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ cdn($vector->thumbnail) }}','{{$vector->title}}')">
                                            <span><i class="fal fa-plus-circle"></i></span>
                                            <span>{{__('misc.save_to_collection')}}</span>
                                        </div>
                                        <div class="icon-similar">
                                            <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$vector->id ])}}">
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

    <section class="pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h1 class="text-capitalize color-primary">{{ trans('global.Most_popular_topics') }}</h1>
                    <p class="color-secondary">{{ trans('global.multiple_and_comprehensive_classifications') }}
                        <a href="{{ route('vectors.categories') }}" class="d-block d-sm-none float-right">
                            <span class="color-primary">{{ trans('global.more') }}</span>
                        </a>
                    </p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0"><a href="{{ route('vectors.categories') }}">
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
                                <a href="{{ route('vectors.category.show',$category->slug) }}">
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
    @if(count($tag)>0 && $tag[0]!="")
        <section class="trending-searches pt-50 pb-50 text-center">
            <div class="container-fluid">
                <h1 class="text-capitalize color-primary mt-0">{{__('misc.Trending_Stock_Photo_Searches')}}</h1>
                <p class="color-secondary mb-5 mt-0">  {{__('misc.tagsDescription')}}  </p>
                @foreach($tag as $key=>$value)
                    <a class="btn btn-outline-light rounded-pill"
                       href="{{ route('vectors.tags.show', preg_replace('/[[:space:]]+/', '-',($value)) ) }} "> {{$value}} </a>

                @endforeach
            </div>
        </section>
    @endif
    <section class="banner-footer">
        <div class="container">
            <h2 class="color-white ">{{ trans('global.Thousands of digital content and themes, and more') }}  </h2>
            <a href="{{route('plans')}}"
               class="btn btn-primary btn-lg"> {{ trans('global.Discover-packages-prices') }} </a>
        </div>
    </section>
    @include('includes.newsletter')
@endsection
@push('javascript_navbar')
    <script>
        $('#canReserve').flexImages({rowHeight: 300, maxRows: 2});
    </script>
@endpush
