@php
    /**@var $video \App\Models\Video*/
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0 "); // Proxies.
    $is_downloaded = auth()->check() ? auth()->user()->is_downloaded($video) : false;
    $standard_const_credits = \App\Models\Video::standard_credits();
    $enhanced_const_credits = \App\Models\Video::enhanced_credits();
    $exclusive_const_credits = \App\Models\Video::exclusive_credits();
@endphp
@extends('app')
@section('title',$video->title.' - '.trans_choice('misc.video', 1 ).' #'.$video->id.' - ')
@section('description_custom')
    {{ $video->title.' - '.trans('misc.video' ).' # '.$video->id.' - ' }}
    @if( $video->description != '' )
        {{ App\Helper::removeLineBreak( e( $video->description ) ).' - ' }}
    @endif
@endsection
@push('ld_json')
    <script data-react-helmet="true" type="application/ld+json">
        {!! json_encode(["@context" => "http://schema.org","@context" => "http://schema.org","@type" => "BreadcrumbList",'itemListElement' => [["@type" => "ListItem","position" => 1,'item' => ["@id" => route('video.home'),"name" => __('misc.videos')],],["@type" => "ListItem","position" => 2,'item' => ["@id" => $video->post_link,"name" => $video->title]]]],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script data-react-helmet="true" type="application/ld+json">
        {!! json_encode(["@context"=> "https://schema.org/","@type"=> "Product","name"=> $video->title,"image"=> [cdn($video->thumbnail_sm)],"description"=> $video->description,"Organization"=> "Arabsstock",'offers'=>["@type"=> "Offer","url"=> $video->post_link,"priceCurrency"=> "USD","price"=> 65,"availability"=> "https://schema.org/OnlineOnly","priceValidUntil"=>\Illuminate\Support\Carbon::parse("2040-09-06")->format('Y-m-d')],"review" => ["@type" => "Review","reviewRating" => ["@type" => "Rating","ratingValue" => "5","bestRating" => "5"],"author" => ["@type" => "Organization","name" => "Arabsstock"]],"aggregateRating" => ["@type" => "AggregateRating","ratingValue" => "5","reviewCount" => "3"],'sku'=>'Arabsstock','brand'=>["@type"=> "Brand",'name'=>'Arabsstock'],'mpn'=>"video-{$video->id}",],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    @if($video->cut_video)
        <script data-react-helmet="true" type="application/ld+json">
            {!! json_encode(["@context"=> "http://schema.org","@type"=> "VideoObject","name"=> $video->title,"description"=> $video->description,"url"=> $video->post_link,"thumbnailUrl"=> cdn($video->thumbnail_sm),"playerType"=> "HTML5","videoQuality"=> $video->type,"duration"=> iso8601_duration($video->getOriginal('duration')),"contentUrl"=> cdn($video->cut_video),"uploadDate"=> $video->date],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
    <script type="application/ld+json">
        {!! json_encode(["@context"=> "http://schema.org","@type"=> "WebSite","url"=> route('video.home'),"potentialAction"=> ["@type"=> "SearchAction","target"=> url(app()->getLocale().'/ar/videos/search/{search_term}'),"query-input"=> "required name=search_term"]],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush
@section('meta')
    <meta name="keywords" content="{{$tags->pluck('title')->implode(',',)}}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>
    <meta property="og:site_name" content="{{$settings->title}}"/>
    <meta property="og:url" content="{{$video->post_link}}"/>
    <meta property="og:image" content="{{ cdn($video->og_image) }}"/>
    <meta property="og:title" content="{{
        (strlen($video->title) > 150) ?
        substr($video->title,0,150).'...'.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$video->id:
         $video->title.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$video->id
          }}"/>
    <meta property="og:description"
          content="{{$video->description? App\Helper::removeLineBreak( e( $video->description ) ):$video->title.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$video->id }}"/>

    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:image" content="{{ cdn($video->thumbnail_sm) }}"/>
    <meta name="twitter:title"
          content="{{ $video->title.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$video->id }}"/>
    <meta name="twitter:description"
          content="{{$video->description? App\Helper::removeLineBreak( e( $video->description ) ):$video->title.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$video->id }}"/>

    <link rel="canonical" href="{{ $video->post_link }}"/>
@endsection
@include('includes.searchbar')
@section('content')
    <div id="app">
        <section class="view-video mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                        <div class="photo" data-reserved="{{ $video->reserved or $video->status=='deleted'?1:0 }}">
                            <div class="text-center position-relative">
                                <video controls autoplay loop muted poster="{{cdn($video->thumbnail_sm??'')}}"
                                       preload="none"
                                       style="object-fit: contain; width: 100%;max-height:450px">
                                    <source src="{{cdn($video->cut_video??'')}}" type="video/mp4"/>
                                </video>
                                <a href="{{ route('video.download_preview',$video->id) }}"
                                   class="btn btn-default preview_action_btn first">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-download" viewBox="0 0 16 16">
                                        <path stroke-width="1" stroke="#fff"
                                              d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                        <path stroke-width="1" stroke="#fff"
                                              d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                    </svg>
                                </a>
                                @if($video->has_raw())
                                    <div class="tag">
                                        <span> ProRes  </span>
                                    </div>
                                @endif
                            </div>
                            <div class="categories-related">
                                @if(isset($video->category) && $video->category)
                                    @foreach($video->category as $videoCategory)
                                        <a href="{{route('video.category.show',$videoCategory->slug)}}"
                                           title="{{$videoCategory->name_en}}"
                                           class="btn btn-sm btn-outline-secondary category rounded-pill mb-2 mt-2">
                                            @if(App::isLocale('en'))
                                                {{isset($videoCategory->name_en)?str_limit($videoCategory->name_en, 18, '...'):'' }}
                                            @else
                                                {{ isset($videoCategory->name_ar)?str_limit($videoCategory->name_ar, 18, '...') :''}}
                                            @endif
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="publish-title mt-4 pb-4">
                            <p class="text-muted published mb-0">
                                <span> {!! trans('global.Stock_Id_Video',['id' => $video->id ?? 0])!!} </span>
                            </p>
                            <h1 class="fs-18 mt-3">{{ $video->title }}</h1>
                        </div>
                    </div>
                    <!-- box left -->
                    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                        @if($video->user && auth()->check() && (auth()->user()->role!='normal' or auth()->user()->free_videos==1))
                            <div class="d-flex justify-content-right">
                                <a href="{{ route('photo.sameuser',$video) }}" class="">
                                    <img src="{{ $video->user->avatar }}" class="rounded-circle mb-3"
                                         style="width: 60px;"
                                         alt="Avatar"/>
                                </a>
                                <div class="px-2">
                                    <h5 class="mb-0">
                                        <a href="{{ route('video.sameuser',$video) }}"
                                           class="bold">{{ $video->user->name }}</a>
                                    </h5>
                                    <p class="text-muted">{{ $video->user->email }} </p>
                                </div>
                            </div>
                        @endif
                        <div class="details-photo py-2" v-cloak>
                            <div class="license">
                                <h4 class="mb-3">
                                    {{trans('misc.license_and_use')}}
                                    <button data-toggle="modal" data-target="#licenseDetails"
                                            class="ml-1 btn p-0 border-0">
                                        <i class="fal fa-info-circle m-0"></i>
                                    </button>
                                </h4>
                                @if (isset($video) && ($video->model_release || $video->how_use_image === 'editorial_only' ))
                                    @if ($video->how_use_image &&  $video->how_use_image === 'editorial_only')
                                        <p>
                                            <i class="fal fa-check-circle"></i> {{trans("misc.{$video->how_use_image}")}}
                                        </p>
                                    @else
                                        <p>
                                            <i class="fal fa-check-circle"></i>{{trans('misc.Signed-model-release')}}
                                        </p>
                                    @endif
                                @endif
                            </div>
                            <hr/>
                            @include('license_modal')
                            <div class="">
                                <div class="form-check mb-3 pl-0">
                                    <label class="form-check-label ml-2" for="standard_license_type">
                                        <small class="d-block font-weight-bold fs-12"> {{ __('Standard license') }}
                                            <span
                                                class="color-primary ml-1"
                                                license-credits-count>({{ $standard_const_credits }} {{ __('credit') }})</span></small>
                                        <span
                                            class="fs-11 text-muted">{{ __('Limited usage in some presentation mediums.') }}</span>
                                    </label>
                                </div>
                                @if($enhanced_const_credits)
                                    <div class="form-check mb-3 pl-0">
                                        <label class="form-check-label ml-2" for="enhanced_license_type">
                                            <small
                                                class="d-block font-weight-bold fs-12"> {{ __('Enhanced license') }}
                                                <span
                                                    class="color-primary ml-1"
                                                    license-credits-count>({{ $enhanced_const_credits }} {{ __('credit') }})</span>
                                            </small>
                                            <span
                                                class="fs-11 text-muted">
                                            @if($video->how_use === 'editorial_only')
                                                    {{ __('Unlimited usage in all presentation mediums.') }}
                                                @else
                                                    {{ __('Unlimited usage in all presentation mediums.') }}
                                                @endif
                                        </span>
                                        </label>
                                    </div>
                                @endif
                                @if($exclusive_const_credits)
                                    <div class="form-check pl-0">
                                        <label class="form-check-label ml-2" for="exclusive_license_type">
                                            <small
                                                class="d-block font-weight-bold fs-12 {{ $video->can_reserve()?'':'reserved' }}"> {{ __('Exclusive license') }}
                                                <span
                                                    class="color-primary ml-1"
                                                    license-credits-count>({{ $exclusive_const_credits }} {{ __('credit') }})</span>
                                            </small>
                                            <span
                                                class="fs-11 text-muted">
                                                    @if($video->how_use === 'editorial_only')
                                                    {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                                                @else
                                                    {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                                                @endif
                                        </span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div id="videos-radio-list">
                                <div class="select-size">
                                    <h4 class="mb-3">{{__('misc.file_type')}} {{ strtoupper($video->extension) }}</h4>
                                    @if(optional($video->child)->last())
                                        @php($child=$video->child->sortByDesc('width')->first())
                                        <div class="d-flex justify-content-between">
                                            <div>{{ __('misc.dimensions') }}:
                                                <span>{{  dimensions_format($video->height , $video->width) }} </span>
                                            </div>
                                            <div>{{ __('misc.size') }}:
                                                <span>{{ size_format($child->size) }}</span></div>
                                            <div>{{ __('misc.resolution') }}:
                                                <span>{{ $child->type }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <hr>
                                <div class="videofile-information d-flex justify-content-between">
                                    <div>
                                        <b>{{__('views.Video clip length:')}}</b> {{$video->duration ?? ''}}
                                    </div>
                                    <div>
                                        <b class="pl-2">{{__('views.FPS')}}:</b> {{$video->fps ?? ''}}
                                    </div>
                                    <div>
                                        <b class="pl-2">{{__('views.Aspect ratio:')}}</b> {{get_aspect_ratio($video->width?? 0, $video->height?? 0)}}
                                    </div>
                                </div>
                                <hr>
                            </div>
                            @if($video->reserved)
                                <p class="mt-2 fs-12 bold text-danger">{{ __('This content was purchased as an exclusive license by another customer, you cannot purchase at this time') }}</p>
                            @else
                                <div class="row mx-0 mb-4 mt-4">
                                    @if(!optional(auth()->user())->free_videos)
                                        @if($video->status!='deleted' && (is_null($video->reserved_until) || now()->gt($video->reserved_until)))
                                            <div class="col-sm{{ $is_downloaded?'-7':'' }} p-1">
                                                <button
                                                    class="btn btn-download large btn-lg btn-block {{ $is_downloaded?'btn-white':'' }}"
                                                    @click="downloadVideo" type="button">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ $is_downloaded?__("Download with new license"):trans('global.Download')}}
                                                </button>
                                            </div>
                                        @endif
                                    @endif
                                    @if(auth()->check() && !auth()->user()->free_videos)
                                        <div class="col-sm-5 p-1 {{ $is_downloaded?'':'d-none' }}">
                                            <div class="dropdown">
                                                <a href="javascript:;"
                                                   class="btn btn-download large btn-lg btn-block dropdown-toggle"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ trans('global.Redownload')}}
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if($video->has_raw())
                                                        <a class="dropdown-item {{ auth()->user()->is_downloaded($video,function($q){$q->where('downloads.raw',1);})?'':'d-none' }}"
                                                           target="_blank"
                                                           href="{{ route('video.redownload',['token_id'=>$video->token_id,'raw'=>1]) }}">
                                                            <p class="m-0 text-left">
                                                                <span
                                                                    class="font-weight-bold"> {{ __('Raw') }} </span>
                                                                <br>
                                                                @if($video->raw->size)
                                                                    <span
                                                                        class="text-muted">{{ size_format($video->raw->size)  }}</span>
                                                                @endif
                                                            </p>
                                                        </a>
                                                    @endif
                                                    @foreach($video->child as $r)
                                                        <a class="dropdown-item"
                                                           href="{{ route('video.redownload',['token_id'=>$video->token_id,'type'=>$r->type]) }}"
                                                           target="_blank">
                                                            <p class="m-0 text-left">
                                                                <span
                                                                    class="font-weight-bold text-left"> {{ $r->type }} </span>
                                                                <span class="text-muted text-left"> {{ $r->width }}px . {{ $r->height }}px </span>
                                                                <br>
                                                                {{ number_format($r->size/1024 / 1024,1) }}MB
                                                                . {{ $r->extension }}
                                                            </p>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(auth()->check() && auth()->user()->free_videos)
                                        <div class="col-sm p-1">
                                            <div class="dropdown">
                                                <a href="javascript:;"
                                                   class="btn btn-download large btn-lg btn-block dropdown-toggle"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ trans('global.Download')}}
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if($video->has_raw())
                                                        <a class="dropdown-item "
                                                           target="_blank"
                                                           href="{{ route('video.download',['token_id'=>$video->token_id,'raw'=>1]) }}">
                                                            <p class="m-0 text-left">
                                                                <span
                                                                    class="font-weight-bold"> {{ __('Raw') }} </span>
                                                                <br>
                                                                @if($video->raw->size)
                                                                    <span class="text-muted">{{ number_format($video->raw->size/1024 / 1024,1) }}MB</span>
                                                                @endif
                                                            </p>
                                                        </a>
                                                    @endif
                                                    @foreach($video->child->reverse() as $r)
                                                        <a class="dropdown-item"
                                                           href="{{ route('video.download',['token_id'=>$video->token_id,'type'=>$r->type]) }}"
                                                           target="_blank">
                                                            <p class="m-0 text-left">
                                                                <span
                                                                    class="font-weight-bold text-left"> {{ $r->type }} </span>
                                                                <span class="text-muted text-left"> {{ $r->width }}px . {{ $r->height }}px </span>
                                                                <br>
                                                                {{ number_format($r->size/1024 / 1024,1) }}MB
                                                                . {{ $r->extension }}
                                                            </p>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <hr/>
                                </div>
                                <div class="like-share">
                                    <button data-id="{{$video->id}}" data-like="{{trans('misc.like')}}"
                                            data-unlike="{{trans('misc.unlike')}}"
                                            data-type="{{class_basename($video)}}" type="button"
                                            class="btn btn-outline-light likeButton @if($video->is_like) active @endif">
                                        <i class="far fa-heart    mr-1"></i>
                                        {{__('misc.like')}}
                                    </button>
                                    <button
                                        onclick='showModal("{{$video->id}}","{{ cdn($video->preview) }}","{{$video->title}}")'
                                        type="button" class="btn btn-outline-light">
                                        <i class="far fa-plus-circle mr-1"></i>
                                        {{__('misc.save_to_collection')}}
                                    </button>
                                    <button type="button" class="btn btn-outline-light" id="share_image"
                                            data-toggle="dropdown">
                                        <i class="far fa-share-alt mr-1"></i>
                                        {{__('misc.share')}}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="share_image">
                                        <a class="dropdown-item"
                                           href="https://www.facebook.com/sharer/sharer.php?u={{url()->current()}}"
                                           target="_blank">{{trans('global.admin-settings.fields.facebook')}}</a>
                                        <a class="dropdown-item"
                                           href="https://twitter.com/intent/tweet?url={{url()->current()}}"
                                           target="_blank">{{trans('global.admin-settings.fields.twitter')}}</a>
                                        <a class="dropdown-item clipboard" href="javascript:;"
                                           data-clipboard-text="{{url()->current()}}">{{trans('global.app_copy_url')}}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- end box left -->
                </div>
            </div>
        </section>
        @if($tags->count())
            <section class="related-keywords mt-5 mb-5">
                <div class="container">
                    <h3 class="mb-4">
                        {{__('global.keywords')}}
                        <button id="toggle" data-slide="open">{{trans('global.Show-all')}}</button>
                    </h3>
                    <div id="tags" class="overflow-hidden h-85">
                        @foreach($tags as $tag)
                            <a href="{{route('video.tags.show', $tag['slug'])}}">
                                <span class="tag--inverse">{{$tag['title']}}</span></a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <section class="simler-images mt-5 mb-5">
            <div class="container">
                @if($same_group->count() >= 4)
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.same_group')}}</h3>
                        <a href="{{ route('video.samegroup',$video) }}" class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="same_group" class="flex-images">
                        @include('video.includes.videos',['videos'=>$same_group])
                    </div>
                @endif
                @if(count($simler_videos) > 0)
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_videos')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'videos','section'=>'clip','id'=>$video->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="videogrid" class="flex-images">
                        @include('video.includes.videos',['videos'=>$simler_videos])
                    </div>
                @endif
            </div>
        </section>
        {{-- sm:images --}}
        @if(isset($simler_images) && $simler_images->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_photos')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'photos','section'=>'clip','id'=>$video->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="imagesFlex" class="flex-images">
                        @foreach($simler_images as $simler_imagesItem)
                            <div class="item card-photo" data-w="{{$simler_imagesItem->width_thumbnail??300}}px"
                                 data-h="{{$simler_imagesItem->height_thumbnail??170}}px">
                                <div class="hover border-file h-100">
                                    <a href="{{ $simler_imagesItem->post_link }}">
                                        <img class="w-100 h-100" srcset="{{ cdn($simler_imagesItem->thumbnail) }}"
                                             src="{{ cdn($simler_imagesItem->thumbnail) }}"
                                             width="{{($simler_imagesItem->width_thumbnail??300)}}"
                                             height="{{($simler_imagesItem->height_thumbnail??300)}}"
                                             alt="{{$simler_imagesItem->title}}">
                                        <div class="hover-overlay"></div>
                                    </a>
                                    <div class="card-photo-content">
                                        <h3 class="card-photo-title">{{$simler_imagesItem->title}}</h3>
                                        <div class="icon">
                                            <div class="d-flex flex-row-reverse">
                                                <div class="icon_save">
                                                <span>
                                                    <i data-id="{{$simler_imagesItem->id}}"
                                                       data-like="{{trans('misc.like')}}"
                                                       data-unlike="{{trans('misc.unlike')}}"
                                                       data-type="{{class_basename($simler_imagesItem)}}"
                                                       class="fal fa-heart @if($simler_imagesItem->is_like) active @endif likeButton"></i>
                                                </span>
                                                    <span>{{__('misc.like')}}</span>
                                                </div>
                                                <div class="icon_save"
                                                     onclick="showModal('{{$simler_imagesItem->id}}','{{ cdn($simler_imagesItem->thumbnail) }}','{{$simler_imagesItem->title}}')">
                                                <span>
                                                    <i class="fal fa-plus-circle"></i>
                                                </span>
                                                    <span>{{__('misc.save_to_collection')}}</span>
                                                </div>
                                                <div class="icon-similar">
                                                    <a href="{{route('similar.files',['type'=>'photos','section'=>'image','id'=>$simler_imagesItem->id ])}}">
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
        {{-- sm:images --}}
        {{-- sm:vectors --}}
        @if(isset($simler_vectors) && $simler_vectors->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_vectors')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'vectors','section'=>'clip','id'=>$video->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="vectorsFlex" class="flex-images">
                        @foreach($simler_vectors as $simler_vector)
                            <div class="item card-photo" data-w="{{$simler_vector->width_thumbnail}}px"
                                 data-h="{{$simler_vector->height_thumbnail}}px">
                                <div class="hover border-file h-100">
                                    <a href="{{ $simler_vector->post_link }}">
                                        <img class="w-100 h-100" srcset="{{ cdn($simler_vector->thumbnail) }}"
                                             src="{{ cdn($simler_vector->thumbnail) }}"
                                             width="{{($simler_vector->width_thumbnail??300)}}"
                                             height="{{($simler_vector->height_thumbnail??300)}}"
                                             alt="{{$simler_vector->title}}">
                                        <div class="hover-overlay"></div>
                                    </a>
                                    <div class="card-photo-content">
                                        <h3 class="card-photo-title">{{$simler_vector->title}}</h3>
                                        <div class="icon">
                                            <div class="d-flex flex-row-reverse">
                                                <div class="icon_save">
                                                    <span>
                                                        <i data-id="{{$simler_vector->id}}"
                                                           data-like="{{trans('misc.like')}}"
                                                           data-unlike="{{trans('misc.unlike')}}"
                                                           data-type="{{class_basename($simler_vector)}}"
                                                           class="fal fa-heart @if($simler_vector->is_like) active @endif likeButton"></i>
                                                    </span>
                                                    <span>{{__('misc.like')}}</span>
                                                </div>
                                                <div class="icon_save"
                                                     onclick="showModal('{{$simler_vector->id}}','{{ cdn($simler_vector->thumbnail) }}','{{$simler_vector->title}}')">
                                                    <span>
                                                        <i class="fal fa-plus-circle"></i>
                                                    </span>
                                                    <span>{{__('misc.save_to_collection')}}</span>
                                                </div>
                                                <div class="icon-similar">
                                                    <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$simler_vector->id ])}}">
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
        {{-- sm:vectors --}}


        @if(
            Auth::check() &&
            (isset($video->user) && $video->user->id != Auth::user()->id) &&
             ( isset($video->user)  && $video->user->paypal_account != '' )
             || Auth::guest() &&
             (isset($video->user)  &&  $video->user->paypal_account != '')
               )
            <form id="form_pp" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post"
                  style="display: none;">
                <input type="hidden" name="cmd" value="_donations"/>
                <input type="hidden" name="return" value="{{$video->post_link}}"/>
                <input type="hidden" name="cancel_return" value="{{$video->post_link}}"/>
                <input type="hidden" name="currency_code" value="USD"/>
                <input type="hidden" name="item_name"
                       value="{{trans('misc.support').' @'.$video->user->username}} - {{$settings->title}}"/>
                <input type="hidden" name="business" value="{{$video->user->paypal_account}}"/>
                <input type="submit"/>
            </form>

        @endif
    </div>

@endsection
@section('sidebar')
    @include('download-options.index',['record'=>$video])
@endsection
@push('javascript_navbar')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>
    <script src="{{ asset('js/clipboard@2.0.10_dist_clipboard.min.js') }}"></script>
    <script>
        let clipboard = new ClipboardJS('.clipboard');
        clipboard.on('success', function (e) {
            jQuery.notify({
                title: '<strong>{{trans('global.app_copied')}}</strong>',
                icon: 'glyphicon glyphicon-star',
                message: "",
            }, {
                type: 'info',
                animate: {
                    enter: 'animated fadeInUp',
                    exit: 'animated fadeOutRight',
                },
                placement: {
                    from: "bottom",
                    align: "center",
                },
                offset: 40,
                spacing: 30,
                z_index: 10000000000000000,
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
            });
            e.clearSelection();
        });

        var lng = "{{app()->getLocale()}}";
        Vue.config.devtools = false;
        Vue.config.debug = false;
        Vue.config.silent = false;
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    image: null,
                    stock: [],
                    img_details: null,
                    token_img: null,
                    type_img: null,
                    isActive: false,
                    notActive: true,
                    selectedTypeStock: '',
                };
            },
            created: function () {
            },
            mounted: function () {

            },
            watch: {},

            computed: {},
            methods: {
                trans(term = "") {

                    // var locals = {
                    //   'large': "{{trans('global.large')}}",
                    //   'medium': "{{trans('global.medium')}}",
                    //   'small': "{{trans('global.small')}}",
                    // };

                    // return locals[term];

                },

                downloadVideo() {
                    @if(auth()->check())
                        @if(auth()->user()->free_videos)
                        window.location.href = '{{route('video.download',$video->token_id)}}?type={{ $video->child->first()->type }}';
                    return 0;
                    @endif
                    $("#wrapper").toggleClass("toggled");
                    return 0;
                    @else
                    if (!auth_render) {
                        call_auth('login');
                        return;
                    }
                    $('#login').modal('show');
                    return 0;
                    @endif
                },
            },
        });

        $("#toggle").click(function () {
            $("#tags").toggleClass('overflow-hidden h-85');
        });
        @if($same_group->count() >= 4)
        $('#same_group').flexImages({object: '.arabs-video', rowHeight: 200, truncate: 1});
        @endif
        @if(isset($simler_vectors) && $simler_vectors->count())
        $('#vectorsFlex').flexImages({rowHeight: 200, maxRows: 2, truncate: 1});
        @endif
        @if(isset($simler_images) && $simler_images->count())
        $('#imagesFlex').flexImages({rowHeight: 200, maxRows: 2, truncate: 1});
        @endif
    </script>
@endpush
