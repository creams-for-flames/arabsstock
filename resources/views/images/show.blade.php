@php
    if(($image->width_search_large/2) > $image->height_search_large ){
        $max_height ="12%";
    }else{
        $max_height ="8%";
    }
    $is_downloaded = auth()->check() ? auth()->user()->is_downloaded($image) : false;
    $standard_const_credits = \App\Models\Image::standard_credits();
    $enhanced_const_credits = \App\Models\Image::enhanced_credits();
    $exclusive_const_credits = \App\Models\Image::exclusive_credits();
@endphp
@extends('app')
@push('ld_json')
    <script data-react-helmet="true" type="application/ld+json">
        {!! json_encode([
    "@context" => "http://schema.org",
    "@context" => "http://schema.org",
    "@type" => "BreadcrumbList",
    'itemListElement' => [
        [
            "@type" => "ListItem",
            "position" => 1,
            'item' => [
                "@id" => route('photos.home'),
                "name" => __('misc.images')
            ],
        ],
        ["@type" => "ListItem",
            "position" => 2,
            'item' => [
                "@id" => $image->post_link,
                "name" => $image->title
            ]
        ]
    ]
],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
        "@context"=>"http://schema.org",
       "@type"=>"ImageObject",
       "copyrightHolder"=>"Arabssstock",
       "name"=> $image->title,
       "description"=>$image->description,
       "contentUrl"=>cdn($image->thumbnail),
       "url"=>cdn($image->preview),
       "thumbnailUrl"=>cdn($image->preview),
       "acquireLicensePage"=>$image->post_link,
       "license"=> url(app()->getLocale().'/page/license-agreement'),
       "fileFormat"=>"image/{$image->extension}",
       "sourceOrganization"=>"Arabsstock"
    ],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script data-react-helmet="true" type="application/ld+json">
        {!! json_encode([
        "@context"=> "https://schema.org/",
          "@type"=> "Product",
          "name"=> $image->title,
          "image"=> [
            cdn($image->thumbnail)
           ],
          "description"=> $image->description,
          "Organization"=> "Arabsstock",
          'offers'=>[
            "@type"=> "Offer",
            "url"=> $image->post_link,
            "priceCurrency"=> "USD",
            "price"=> 5,
            "availability"=> "https://schema.org/OnlineOnly",
            "priceValidUntil"=>\Illuminate\Support\Carbon::parse("2040-09-06")->format('Y-m-d')
           ],
           "review" => [
        "@type" => "Review",
        "reviewRating" => [
            "@type" => "Rating",
            "ratingValue" => "5",
            "bestRating" => "5"
        ],
        "author" => [
            "@type" => "Organization",
            "name" => "Arabsstock"
        ]
    ],
    "aggregateRating" => [
        "@type" => "AggregateRating",
        "ratingValue" => "5",
        "reviewCount" => "3"
    ],
    'sku'=>'Arabsstock',
    'brand'=>[
        "@type"=> "Brand",
        'name'=>'Arabsstock'
        ],
    'mpn'=>"image-{$image->id}",
    ],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
    "@context"=> "http://schema.org",
    "@type"=> "WebSite",
    "url"=> route('photos.home'),
    "potentialAction"=> [
        "@type"=> "SearchAction",
        "target"=> url(app()->getLocale().'/ar/photos/search/{search_term}'),
        "query-input"=> "required name=search_term"
    ]
],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush
@section('title')
    {{ $image->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$image->id.' - ' }}
@endsection
@section('description_custom'){{ $image->title.' - '.trans('misc.photos_plural' ).' # '.$image->id.' - ' }}
@if( $image->description != '' ){{ App\Helper::removeLineBreak( e( $image->description ) ).' - ' }}
@endif
@endsection
@section('meta')
    <meta name="keywords" content="{{$tags->pluck('title')->implode(',',)}}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>
    <meta property="og:site_name" content="{{$settings->title}}"/>
    {{-- <meta property="og:url" content=""/> --}}
    <meta property="og:image" content="{{ cdn($image->og_image) }}"/>
    <meta property="og:title" content="{{
    (strlen($image->title) > 150) ?
        substr($image->title,0,150).'...'.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$image->id:
         $image->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$image->id
    }}"/>
    <meta property="og:description"
          content="{{ $image->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$image->id }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:image" content="{{ cdn('') }}/{{$image->preview}}"/>
    <meta name="twitter:title"
          content="{{ $image->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$image->id }}"/>
    <meta name="twitter:description"
          content="{{ $image->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$image->id }}"/>

    <meta property="og:url" content="{{$image->post_link}}">
    <link rel="canonical" href="{{ $image->post_link }}"/>
@endsection
@include('includes.searchbar')
@section('css')
@endsection
@section('content')
    <div class="modal fade bd-example-modal-lg showPreview" id="ShowImage">
        <div class="modal-dialog modal-centered modal-lg" style="max-width: 860px; position: relative;">
            <div class="modal-content">
                <button type="button" class="close" style="position: absolute;top: 15px;right:15px;font-size: 25px;"
                        data-dismiss="modal" aria-label="Close">
                    <i class="far fa-times-circle"></i>
                </button>
                <div class="text-center" data-reserved="{{ $image->reserved?1:0 }}">
                    <div class="wrap-center center-block">
                        <img class="preview_image"
                             src="{{ cdn($image->search_large) }}" alt="{{ $image->title }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="app">
        <section class="view-photo mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                        <div class="photo" data-reserved="{{ $image->reserved or $image->status=='deleted'?1:0 }}">
                            <div class="text-center position-relative w-100">
                                <picture>
                                    <img src="{{cdn(''.$image->preview)}}" alt="{{ $image->img_caption }}"
                                         data-withbg>
                                    @if($image->has_removebg)
                                        <img src="{{cdn(''.$image->removebg_preview)}}"
                                             alt="{{ $image->img_caption }}" class="d-none" data-removedbg>
                                    @endif
                                </picture>
                                @if($image->has_removebg)
                                <div class="tag">
                                    <span> PNG  </span>
                                </div>
                                @endif
                                <a href="javascript:;" data-toggle="modal" data-target="#ShowImage"
                                   class="btn btn-default preview_action_btn first"
                                   rel="tooltip" data-placement="top" title="{{ __('Zoom image') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         fill="currentColor" class="bi bi-zoom-in" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                              d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                        <path
                                            d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                                        <path fill-rule="evenodd"
                                              d="M6.5 3a.5.5 0 0 1 .5.5V6h2.5a.5.5 0 0 1 0 1H7v2.5a.5.5 0 0 1-1 0V7H3.5a.5.5 0 0 1 0-1H6V3.5a.5.5 0 0 1 .5-.5z"/>
                                    </svg>
                                </a>
                                @if($image->has_removebg)
                                    <a href="javascript:;" removebg-toggle
                                       rel="tooltip" data-placement="top" title="{{ __('Cutout image') }} PNG"
                                       class="btn btn-default preview_action_btn second">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-filetype-png" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3.76 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.506.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.82v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.067-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.348.158.48.275.133.117.238.253.314.407Zm-8.64-.706H0v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H.788v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Zm1.964 2.666V13.25h.032l1.761 2.675h.656v-3.999h-.75v2.66h-.032l-1.752-2.66h-.662v4h.747Z"></path>
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('photos.download_preview',$image->id) }}"
                                   download_preview
                                   class="btn btn-default preview_action_btn {{ $image->has_removebg?'third':'second' }}"
                                   rel="tooltip" data-placement="top" title="{{ __('Try') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         fill="currentColor"
                                         class="bi bi-download" viewBox="0 0 16 16">
                                        <path stroke-width="1" stroke="#fff"
                                              d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                        <path stroke-width="1" stroke="#fff"
                                              d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="categories-related" v-cloak>
                                <a v-if="categories" v-for="{ id, name,slug,post_link } in categories"
                                   :href="post_link"
                                   class="btn btn-sm btn-outline-secondary category rounded-pill mb-2 mt-2"> @{{ name
                                    }} </a>
                            </div>
                        </div>
                        <div class="publish-title mt-4 pb-4">
                            <p class="text-muted published mb-0">
                            <!-- {{trans('misc.published')}} <span class="color-primary"> {{App\Helper::formatDate($image->date)}}</span>
                         <span class="mx-3"> | </span> -->
                                <span> {!! trans('global.Stock_Id_Image',['id' => $image->id])!!} </span>
                            </p>
                            <h1 class="fs-18 mt-3">{{ $image->title }}</h1>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                        <div class="details-photo py-2 " v-cloak>
                            @if($image->user && auth()->check() && (auth()->user()->role!='normal' or auth()->user()->free_images==1))
                                <div class="d-flex justify-content-right">
                                    <a href="{{ route('photo.sameuser',$image) }}" class="">
                                        <img src="{{ $image->user->avatar }}" class="rounded-circle mb-3"
                                             style="width: 60px;"
                                             alt="Avatar"/>
                                    </a>
                                    <div class="px-2">
                                        <h5 class="mb-0">
                                            <a href="{{ route('photo.sameuser',$image) }}"
                                               class="bold">{{ $image->user->name }}</a>
                                        </h5>
                                        <p class="text-muted">{{ $image->user->email }} </p>
                                    </div>
                                </div>
                            @endif
                            <div class="license">
                                <h4 class="mb-3">
                                    {{trans('misc.license_and_use')}}
                                    <button data-toggle="modal" data-target="#licenseDetails" class="ml-1 btn p-0 border-0">
                                        <i class="fal fa-info-circle m-0"></i>
                                    </button>
                                </h4>
                                @if (isset($image) && ($image->model_release || $image->how_use_image === 'editorial_only' ))
                                    @if ($image->how_use_image &&  $image->how_use_image === 'editorial_only')
                                        <p>
                                            <i class="fal fa-check-circle"></i> {{trans("misc.{$image->how_use_image}")}}
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
                                            @if($image->how_use === 'editorial_only')
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
                                                class="d-block font-weight-bold fs-12 {{ $image->can_reserve()?'':'reserved' }}"> {{ __('Exclusive license') }}
                                                <span
                                                    class="color-primary ml-1"
                                                    license-credits-count>({{ $exclusive_const_credits }} {{ __('credit') }})</span>
                                            </small>
                                            <span
                                                class="fs-11 text-muted">
                                                     @if($image->how_use === 'editorial_only')
                                                    {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                                                @else
                                                    {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                                                @endif
                                        </span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div class="select-size">
                                <h4 class="mb-3">{{__('misc.file_type')}} {{ strtoupper($image->extension) }}</h4>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <b>{{ __('misc.dimensions') }}:</b>
                                        {{ dimensions_format($image->height_large ,$image->width_large) }}
                                    </div>
                                    @if(optional($image->stock)->last())
                                        <div>
                                            <b> {{ __('misc.size') }}: </b>
                                            <span>{{ $image->stock->last()->size }}</span>
                                        </div>
                                        <div>
                                            <b> {{ __('misc.resolution') }}: </b>
                                            <span>{{ $image->stock->last()->dpi }} DPI</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            @if($image->reserved)
                                <p class="mt-2 fs-12 bold text-danger">{{ __('This content was purchased as an exclusive license by another customer, you cannot purchase at this time') }}</p>
                            @else
                                <div class="row mx-0 mb-4 mt-4">
                                    @if(!optional(auth()->user())->free_images)
                                        @if($image->status!='deleted' && (is_null($image->reserved_until) || now()->gt($image->reserved_until)))
                                            <div class="col-sm{{ $is_downloaded?'-7':'' }} p-1">
                                                <button
                                                    class="btn btn-download large btn-lg btn-block {{ $is_downloaded?'btn-white':'' }}"
                                                    @click="downloadImag" type="button">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ $is_downloaded?__("Download with new license"):trans('global.Download')}}
                                                </button>
                                            </div>
                                        @endif
                                    @endif
                                    @if(auth()->check() && !auth()->user()->free_images)
                                        <div class="col-sm-5 p-1 {{ $is_downloaded?'':'d-none' }}">
                                            <div class="dropdown">
                                                <a href="javascript:;"
                                                   class="btn btn-download large btn-lg btn-block dropdown-toggle"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ trans('global.Redownload')}}
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if($image->has_removebg)
                                                        <a class="dropdown-item removebg_link {{ auth()->user()->is_downloaded($image,function($q){$q->where('downloads.removebg',1);})?'':'d-none' }}"
                                                           href="{{ route('photos.redownload',['token_id'=>$image->token_id,'removebg'=>1]) }}">
                                                            <p class="m-0 text-left">
                                                                <span
                                                                    class="font-weight-bold"> {{ __('Cutout image') }} </span>
                                                                <br>
                                                                <span class="text-muted">PNG Format</span>
                                                            </p>
                                                        </a>
                                                    @endif
                                                    @foreach($image->stock->reverse() as $r)
                                                        <a class="dropdown-item"
                                                           href="{{ route('photos.redownload',['token_id'=>$image->token_id,'type'=>$r->type]) }}">
                                                            <p class="m-0 text-left">
                                                                <span class="font-weight-bold"> {{ __("global.{$r->type}") }} . </span>
                                                                <span
                                                                    class="text-muted"> {{ $r->resolution }} px </span>
                                                                <br>
                                                                <span class="text-muted">{{ $r->dpi }} DPI · JPG</span>
                                                            </p>
                                                        </a>
                                                    @endforeach
                                                    @if($image->psd)
                                                        <a class="dropdown-item"
                                                           href="{{ route('photos.redownload',['token_id'=>$image->token_id,'type'=>'psd']) }}">
                                                            <p class="m-0 text-left">
                                                                <span
                                                                    class="text-muted"> {{ $image->resolution }} px </span>
                                                                <span class="font-weight-bold"> PSD </span>
                                                                <br>
                                                                <span
                                                                    class="text-muted">{{ $image->dpi }} DPI · PSD</span>
                                                            </p>
                                                        </a>
                                                    @endif
                                                    {{--                                                        <a class="dropdown-item" href="{{ route('photos.redownload',['token_id'=>$image->token_id]) }}">JPG</a>--}}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(auth()->check() && auth()->user()->free_images)
                                        <div class="col-sm p-1">
                                            <div class="dropdown">
                                                <a href="javascript:;"
                                                   class="btn btn-download large btn-lg btn-block dropdown-toggle"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ trans('global.Download')}}
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if($image->has_removebg)
                                                        <a class="dropdown-item"
                                                           href="{{ route('photos.download',['token_id'=>$image->token_id,'removebg'=>1]) }}">
                                                            <p class="m-0 text-left">
                                                            <span
                                                                class="font-weight-bold"> {{ __('Cutout image') }} </span>
                                                                <br>
                                                                <span class="text-muted">PNG Format</span>
                                                            </p>
                                                        </a>
                                                    @endif
                                                    @foreach($image->stock->reverse() as $r)
                                                        <a class="dropdown-item"
                                                           href="{{ route('photos.download',['token_id'=>$image->token_id,'type'=>$r->type]) }}">
                                                            <p class="m-0 text-left">
                                                                <span class="font-weight-bold"> {{ __("global.{$r->type}") }} . </span>
                                                                <span
                                                                    class="text-muted"> {{ $r->resolution }} px </span>
                                                                <br>
                                                                <span class="text-muted">{{ $r->dpi }} DPI · JPG</span>
                                                            </p>
                                                        </a>
                                                    @endforeach
                                                    @if($image->psd)
                                                        <a class="dropdown-item"
                                                           href="{{ route('photos.download',['token_id'=>$image->token_id,'type'=>'psd']) }}">
                                                            <p class="m-0 text-left">
                                                            <span
                                                                class="text-muted"> {{ $image->resolution }} px </span>
                                                                <span class="font-weight-bold"> PSD </span>
                                                                <br>
                                                                <span
                                                                    class="text-muted">{{ $image->dpi }} DPI · PSD</span>
                                                            </p>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <hr/>
                                </div>
                                <div class="like-share">
                                    <button data-id="{{$image->id}}" data-like="{{trans('misc.like')}}"
                                            data-unlike="{{trans('misc.unlike')}}"
                                            data-type="{{class_basename($image)}}" type="button"
                                            class="btn btn-outline-light likeButton @if($image->is_like) active @endif">
                                        <i class="far fa-heart    mr-1"></i>
                                        {{__('misc.like')}}
                                    </button>
                                    <button
                                        onclick='showModal("{{$image->id}}","{{ cdn($image->preview) }}","{{$image->title}}")'
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
                            <a href="{{route('tags.show', $tag['slug'])}}">
                                <span class="tag--inverse">{{$tag['title']}}</span></a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        @if($same_group->count() >= 4)
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.same_group')}}</h3>
                        <a href="{{ route('photo.samegroup',$image) }}" class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="same_group" class="flex-images sm">
                        @foreach($same_group as $r)
                            @include('images.item',['image'=>$r])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        @if(isset($simler_images) && $simler_images->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    {{-- <div class="row justify-content-between"> --}}
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_photos')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'photos','section'=>'image','id'=>$image->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    {{-- </div> --}}
                    <div id="imagesFlex" class="flex-images sm">
                        @foreach($simler_images as $r)
                            @include('images.item',['image'=>$r])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        {{-- sm:vectors --}}
        @if( isset($simler_vectors) && $simler_vectors->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_vectors')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'vectors','section'=>'image','id'=>$image->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="vectorsFlex" class="flex-images">
                        @foreach($simler_vectors as $r)
                            <div class="item card-photo" data-w="{{$r->width_thumbnail}}px"
                                 data-h="{{$r->height_thumbnail}}px">
                                <div class="hover border-file h-100">
                                    <a href="{{ $r->post_link }}">
                                        <picture>
                                            <source
                                                data-srcset="{{ cdn($r->thumbnail) }}"/>
                                            <img
                                                data-src="{{ cdn($r->thumbnail) }}"
                                                src="{{cdn($r->thumbnail)}}"
                                                width="{{($r->width_thumbnail??300)}}"
                                                height="{{($r->height_thumbnail??300)}}"
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
                                                        <i data-id="{{$r->id}}"
                                                           data-like="{{trans('misc.like')}}"
                                                           data-unlike="{{trans('misc.unlike')}}"
                                                           data-type="{{class_basename($r)}}"
                                                           class="fal fa-heart @if($r->is_like) active @endif likeButton"></i>
                                                    </span>
                                                    <span>{{__('misc.like')}}</span>
                                                </div>
                                                <div class="icon_save"
                                                     onclick="showModal('{{$r->id}}','{{class_basename($r)}}','{{ cdn($r->thumbnail) }}','{{$r->title}}')">
                                                    <span>
                                                        <i class="fal fa-plus-circle"></i>
                                                    </span>
                                                    <span>{{__('misc.save_to_collection')}}</span>
                                                </div>
                                                <div class="icon-similar">
                                                    <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$r->id ])}}">
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
        {{-- sm:vectors --}}
        {{-- sm:videos --}}
        @if(isset($simler_videos) && $simler_videos->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_videos')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'videos','section'=>'image','id'=>$image->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="videogrid" class="flex-images">
                        @include('video.includes.videos',['videos'=>$simler_videos,"type"=>"image","file"=> $image])
                    </div>
                </div>
            </section>
        @endif
        {{-- sm:videos --}}
    </div>
@endsection
@section('sidebar')
    @include('download-options.index',['record'=>$image])
@endsection
@push('javascript_navbar')
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
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>
    <script src="{{ asset('js/axios@1.4.0_dist_axios.min.js') }}"></script>
    <script>
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
                    categories: [],
                    tags: [],
                    similers_images: [],
                    img_details: null,
                    token_img: null,
                    type_img: null,
                    isActive: false,
                    notActive: true,
                    selectedTypeStock: 'large',
                };
            },
            created: function () {
                var self = this;
                axios.get('/api/Image/' + '{{$image->id}}' + '/show?lang=' + lng)
                    .then(response => {
                        self.categories = response.data.categories;
                        self.makeActiveLink(last_ext.type, last_ext.resolution, last_ext.size, last_ext.extension, last_ext.token, last_ext.dpi);
                    }).catch(error => {
                });
            },
            mounted: function () {
            },
            watch: {},
            computed: {},
            methods: {
                trans(term = "") {

                    var locals = {
                        'large': "{{trans('global.large')}}",
                        'medium': "{{trans('global.medium')}}",
                        'small': "{{trans('global.small')}}",
                    };

                    return locals[term];

                },
                makeActiveLink(type, resolution, size, extension, token, dpi) {
                    this.selectedTypeStock = type;
                    this.img_details = type + ' || ' + resolution + ' px || ' + size + ' || ' + extension + ' || ' + dpi + 'DPI';

                    this.token_img = token;
                    this.type_img = type;
                    this.isActive = true;
                    this.notActive = false;
                    $('.dropdown input[name="type"][value="' + type + '"]').closest('a').trigger('click');
                },

                downloadImag() {
                    @if(auth()->check())
                        @if(auth()->user()->free_images)
                        window.location.href = '{{route('photos.download',$image->token_id)}}?type=large';
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
                showModal(id, img, title) {
                    @if(!auth()->check())
                    $('#login').modal('show');
                    return false;
                    @endif
                    $('#myCollections').text('');
                    var link = '{{route('photos.imageCollection',':id')}}';
                    link = link.replace(':id', id);

                    $.ajax({
                        type: "POST",
                        url: link,
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token(), false); ?>',
                        },
                        success: function (data) {

                            var my_collection = '';
                            var active_class = '';

                            for (var i = 0; i < data.length; i++) {

                                if (data[i].in_collection == 1) {
                                    active_class = 'active';
                                } else {
                                    active_class = '';
                                }


                                my_collection += '<li id="li_' + data[i].id + '" onclick="addToCollection(' + id + ',' + data[i].id + ',\'' + data[i].title + '\')" class="' + active_class + '">' + data[i].title + '</li>';
                            }
                            $('#myCollections').append(my_collection);

                            $('#imageCard').attr('src', img);
                            $('#collection-model').modal('show');
                            image_id = id;
                        },
                    });
                },
                goToCategory(slug) {
                    var link = '{{route('category.show',':slug')}}';
                    link = link.replace(':slug', slug);
                    window.location.href = link;
                },
            },
        });

        $("#toggle").click(function () {
            $("#tags").toggleClass('overflow-hidden h-85');
        });
        $("[removebg-toggle]").click(function () {
            $('[download_preview]').attr('href', '{{ route('photos.download_preview',$image->id) }}' + ($('.photo [data-withbg]').hasClass('d-none') ? '' : '?removebg=1'));
            $('.photo [data-withbg]').toggleClass('d-none');
            $('.photo [data-removedbg]').toggleClass('d-none');
        });
        $("[rel=tooltip]").tooltip({html: true});
    </script>
    <script>
        @if($simler_images->count())
        $('#imagesFlex').flexImages({rowHeight: 200, maxRows: 2, truncate: 1});
        @endif
        @if($same_group->count() >= 4)
        $('#same_group').flexImages({rowHeight: 200, maxRows: 2, truncate: 1});
        @endif
        @if($simler_vectors->count())
        $('#vectorsFlex').flexImages({rowHeight: 200, maxRows: 2, truncate: 1});
        @endif
    </script>
@endpush
