@php
$is_downloaded = auth()->check() ? auth()->user()->is_downloaded($vector) : false;
if(($vector->width_search_large/2) > $vector->height_search_large ){
    $max_height ="12%";
}else{
    $max_height ="8%";

}
$standard_const_credits = \App\Models\Vector::standard_credits();
$enhanced_const_credits = \App\Models\Vector::enhanced_credits();
$exclusive_const_credits = \App\Models\Vector::exclusive_credits();
@endphp
@extends('app')
@section('title')
    {{ $vector->title.' - '.trans_choice('misc.vectors_plural', 1 ).' #'.$vector->id.' - ' }}
@endsection
@section('description_custom'){{ $vector->title.' - '.trans('misc.vectors_plural' ).' # '.$vector->id.' - ' }}
@if( $vector->description != '' ){{ App\Helper::removeLineBreak( e( $vector->description ) ).' - ' }}
@endif
@endsection
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
                "@id" => $vector->post_link,
                "name" => $vector->title
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
       "name"=> $vector->title,
       "description"=>$vector->description,
       "contentUrl"=>cdn($vector->thumbnail),
       "url"=>cdn($vector->preview),
       "thumbnailUrl"=>cdn($vector->preview),
       "acquireLicensePage"=>$vector->post_link,
       "license"=> url(app()->getLocale().'/page/license-agreement'),
       "fileFormat"=>"image/{$vector->extension}",
       "sourceOrganization"=>"Arabsstock"
    ],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script data-react-helmet="true" type="application/ld+json">
        {!! json_encode([
    "@context" => "https://schema.org/",
    "@type" => "Product",
    "name" => $vector->title,
    "image" => [
        cdn($vector->thumbnail)
    ],
    "description" => $vector->description,
    "Organization" => "Arabsstock",
    'offers' => [
        "@type" => "Offer",
        "url" => $vector->post_link,
        "priceCurrency" => "USD",
        "price" => 4,
        "availability" => "https://schema.org/OnlineOnly",
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
    'mpn'=>"vector-{$vector->id}",
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
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
@section('meta')
    <meta name="keywords" content="{{$tags->pluck('title')->implode(',',)}}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>
    <meta property="og:site_name" content="{{$settings->title}}"/>
    {{-- <meta property="og:image" content="{{ cdn('uploads2/thumbnail/'.$vector->thumbnail) }}"/> --}}
    <meta property="og:title" content="{{
        (strlen($vector->title) > 150) ?
        substr($vector->title,0,150).'...'.' - '.trans_choice('misc.vectors_plural', 1 ).' #'.$vector->id:
         $vector->title.' - '.trans_choice('misc.vectors_plural', 1 ).' #'.$vector->id
    }}"/>
    <meta property="og:description"
          content="{{ $vector->title.' - '.trans_choice('misc.vectors_plural', 1 ).' #'.$vector->id }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:image" content="{{ cdn($vector->thumbnail) }}"/>
    <meta name="twitter:title"
          content="{{ $vector->title.' - '.trans_choice('misc.vectors_plural', 1 ).' #'.$vector->id }}"/>
    <meta name="twitter:description" content="{{ App\Helper::removeLineBreak( e( $vector->description ) ) }}"/>

    <meta property="og:url" content="{{$vector->post_link}}">
    <meta property="og:image" content="{{ cdn($vector->og_image) }}">

    <link rel="canonical" href="{{ $vector->post_link }}"/>
@endsection
@section('css')
    <style>
        .photo img{
            object-fit: contain !important;
        }
        .photo{
            margin: auto !important;
        }

        .publish-title{
            border-top: 1px solid rgba(12, 18, 28, .12);
            border-bottom: unset !important;
        }
        .images-box{
            border: unset !important;
        }
        .image_search_btn{
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: darkgray;
            border: 1px solid gray;
        }
        .image_search_btn:hover{
            background: whitesmoke;
            border: 1px solid #20d598;
            color: #20d598;
        }

        .large-view{
            position: absolute;
            bottom: 0;
            margin: 0 auto;
            height: {!! $max_height !!};
            background: white;
            width: 100%;
        }
        #ShowImage img{
            max-width: 100%;
            max-height: calc(100vh - 200px);
        }

        @media only screen and (max-width: 768px){
            #ShowImage .modal-content{
                padding: 20px;
            }
        }
        .handlePos{
            position: relative;
            display: flex;
            justify-content: center;
        }
        #ShowImage .modal-content{
            background: #fff;
            padding: 50px 50px calc(50px - 5%) 50px;
        }
    </style>
@endsection
@include('includes.searchbar')
@section('content')

    <div class="modal fade bd-example-modal-lg showPreview" id="ShowImage">
        <div class="modal-dialog modal-centered modal-lg" style="max-width: 860px; position: relative;">
            <div class="modal-content">
                <button type="button" class="close" style="position: absolute;top: 15px;right:15px;font-size: 25px;"
                        data-dismiss="modal" aria-label="Close">
                    <i class="far fa-times-circle"></i>
                </button>
                <div class="handlePos">
                    <div class="large-view"></div>
                    <img src="{{ cdn($vector->search_large) }}" class="preview_image"
                         alt="{{ $vector->title }}">
                </div>
            </div>
        </div>
    </div>

    <div id="app">
        <section class="view-photo mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-7 col-lg-7 border-file">
                        <div class="photo"
                             data-reserved="{{ $vector->reserved or $vector->status=='deleted'?1:0 }}"
                             style="{!! 'max-width:448px;max-height:'.(((int)$vector->height_preview * (int)$vector->width_preview)/488).'px;' !!}">
                            <div class="text-center images-box w-100 position-relative">
                                <picture>
                                    <source
                                        data-srcset="{{ cdn($vector->preview) }}"/>
                                    <img
                                        data-src="{{ cdn($vector->preview) }}"
                                        src="{{ cdn($vector->preview) }}"
                                        style="{!! 'max-width:448px;max-height:'.(((int)$vector->height_preview * (int)$vector->width_preview)/488).'px;' !!}"
                                        class="lazyload w-100 h-100"
                                        alt="{{ $vector->img_caption }}"/>
                                </picture>
                                <a href="javascript:;" data-toggle="modal" data-target="#ShowImage"
                                   class="btn btn-default preview_action_btn first" id="preview_action_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-zoom-in" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                              d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                        <path
                                            d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                                        <path fill-rule="evenodd"
                                              d="M6.5 3a.5.5 0 0 1 .5.5V6h2.5a.5.5 0 0 1 0 1H7v2.5a.5.5 0 0 1-1 0V7H3.5a.5.5 0 0 1 0-1H6V3.5a.5.5 0 0 1 .5-.5z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('vectors.download_preview',$vector->id) }}"
                                   class="btn btn-default preview_action_btn second">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-download" viewBox="0 0 16 16">
                                        <path stroke-width="1" stroke="#fff"
                                              d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                        <path stroke-width="1" stroke="#fff"
                                              d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="publish-title mt-4 pb-4">
                            <div class="categories-related" v-cloak>
                                <a v-if="categories" v-for="{ id, name,slug,post_link_vector } in categories"
                                   :href="post_link_vector"
                                   class="btn btn-sm btn-outline-secondary category rounded-pill mb-2 mt-2"> @{{ name
                                    }} </a>
                            </div>
                            <p class="text-muted published mb-0">
                                <span> {!! trans('global.Stock_Id_Vector',['id' => $vector->id])!!} </span>
                            </p>
                            <h1 class="fs-18 mt-3">{{ $vector->title }}</h1>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                        @if($vector->user && auth()->check() && (auth()->user()->role!='normal' or auth()->user()->free_vectors==1))
                            <div class="d-flex justify-content-right">
                                <a href="{{ route('vector.sameuser',$vector) }}" class="">
                                    <img src="{{ $vector->user->avatar }}" class="rounded-circle mb-3"
                                         style="width: 60px;"
                                    />
                                </a>
                                <div class="px-2">
                                    <h5 class="mb-0">
                                        <a href="{{ route('vector.sameuser',$vector) }}"
                                           class="bold">{{ $vector->user->name }}</a>
                                    </h5>
                                    <p class="text-muted">{{ $vector->user->email }} </p>
                                </div>
                            </div>
                        @endif
                        <div class="details-photo py-2 " v-cloak>
                            <div class="license">
                                <h4 class="mb-3">
                                    {{trans('misc.license_and_use')}}
                                    <button data-toggle="modal" data-target="#licenseDetails" class="ml-1 btn p-0 border-0">
                                        <i class="fal fa-info-circle m-0"></i>
                                    </button>
                                </h4>
                                <div>
                                    <i class="fal fa-check-circle"></i>{{trans('misc.use_it_at_any_size_without_losing_resolution')}}
                                </div>
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
                                            @if($vector->how_use === 'editorial_only')
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
                                                class="d-block font-weight-bold fs-12 {{ $vector->can_reserve()?'':'reserved' }}"> {{ __('Exclusive license') }}
                                                <span
                                                    class="color-primary ml-1"
                                                    license-credits-count>({{ $exclusive_const_credits }} {{ __('credit') }})</span>
                                            </small>
                                            <span
                                                class="fs-11 text-muted">
                                                        @if($vector->how_use === 'editorial_only')
                                                    {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                                                @else
                                                    {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                                                @endif
                                        </span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div>
                                    <h4 class="mb-3">{{__('misc.file_type')}} {{ strtoupper($vector->extension) }}</h4>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <b>{{ __('misc.dimensions') }}:</b>
                                            {{ dimensions_format($vector->height_vector ,$vector->width_vector) }}
                                        </div>
{{--                                        @if(\Illuminate\Support\Facades\Storage::disk('s3')->exists($vector->vector))--}}
{{--                                            <div>--}}
{{--                                                <b>{{ __('misc.size') }}:</b>--}}
{{--                                                {{ size_format(\Illuminate\Support\Facades\Storage::disk('s3')->size($vector->vector)) }}--}}
{{--                                            </div>--}}
{{--                                        @endif--}}
                                        <div>
                                            <b>{{ __('misc.resolution') }}:</b>
                                            300 DPI
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                @if($vector->reserved)
                                    <p class="mt-2 fs-12 bold text-danger">{{ __('This content was purchased as an exclusive license by another customer, you cannot purchase at this time') }}</p>
                                @else
                                    <div class="row mx-0 mb-4 mt-4">
                                        @if($vector->status!='deleted' && (is_null($vector->reserved_until) || now()->gt($vector->reserved_until)))
                                            <div class="col-sm{{ $is_downloaded?'-7':'' }} p-1">
                                                <button
                                                    class="btn btn-download large btn-lg btn-block {{ $is_downloaded?'btn-white':'' }}"
                                                    @click="downloadImag" type="button">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ $is_downloaded?__("Download with new license"):trans('global.Download')}}
                                                </button>
                                            </div>
                                        @endif
                                        @if(auth()->check() && !auth()->user()->free_images)
                                            <div class="col-sm-5 p-1 {{ $is_downloaded?'':'d-none' }}">
                                                <a
                                                    class="btn btn-download large btn-lg btn-block"
                                                    href="{{ route('vectors.redownload',['token_id'=>$vector->token_id]) }}"
                                                    target="_blank" rel="nofollow">
                                                    <i class="fal fa-cloud-download-alt pr-2"></i>{{ trans('global.Redownload')}}
                                                </a>
                                            </div>
                                        @endif
                                        <hr/>
                                    </div>
                                    <div class="like-share">
                                        <button data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}"
                                                data-unlike="{{trans('misc.unlike')}}" type="button"
                                                class="btn btn-outline-light @if($vector->is_like) active @endif save_vector">
                                            <i class="far fa-heart   mr-1"></i>
                                            {{__('misc.like')}}
                                        </button>
                                        <button
                                            onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ cdn($vector->thumbnail) }}','{{$vector->title}}')"
                                            type="button" class="btn btn-outline-light">
                                            <i class="far fa-plus-circle mr-1"></i>
                                            {{__('misc.save_to_collection')}}
                                        </button>
                                        <button type="button" class="btn btn-outline-light" id="share_vector"
                                                data-toggle="dropdown">
                                            <i class="far fa-share-alt mr-1"></i>
                                            {{__('misc.share')}}
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="share_vector">
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
                            <a href="{{route('vectors.tags.show', $tag['slug'])}}">
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
                        <a href="{{ route('vector.samegroup',$vector) }}" class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="same_group" class="flex-images">
                        @foreach($same_group as $r)
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
                                                           class="fal fa-heart @if($r->is_like) active @endif save_vector"></i>
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
        @if(isset($simler_vectors) && $simler_vectors->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_vectors')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$vector->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="vectorsFlex" class="flex-images">
                        @if($simler_vectors)
                            @foreach($simler_vectors as $r)
                                <div class="item card-photo " data-w="{{(int)$r->width_thumbnail}}px"
                                     data-h="{{(int)$r->height_thumbnail}}px">
                                    <div class="hover border-file m-1 h-100">
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
                                                         onclick="showModal('{{$r->id}}','{{class_basename($r)}}','{{ cdn('/uploads2/thumbnail/'.$r->thumbnail) }}','{{$r->title}}')">
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
                        @endif
                    </div>
                </div>
            </section>
        @endif
        {{-- sm:images --}}
        @if(isset($simler_images) &&  $simler_images->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_photos')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'photos','section'=>'illustration','id'=>$vector->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="imagesFlex" class="flex-images">
                        @foreach($simler_images as $r)
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
        {{-- sm:images --}}

        {{-- sm:videos --}}
        @if(isset($simler_videos) && $simler_videos->count())
            <section class="simler-images mt-5 mb-5">
                <div class="container">
                    <div class="d-flex justify-content-between p-2">
                        <h3 class="text-capitalize">{{trans('misc.similar_videos')}}</h3>
                        <a href="{{ route('similar.files',['type'=>'videos','section'=>'illustration','id'=>$vector->id])}}"
                           class="text-right">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                    <div id="videogrid" class="flex-images">
                        @include('video.includes.videos',['videos'=>$simler_videos])
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
@section('sidebar')
    @include('download-options.index',['record'=>$vector])
@endsection

@push('javascript_navbar')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>
    <script src="{{ asset('js/axios@1.4.0_dist_axios.min.js') }}"></script>
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
                    categories: [],
                    tags_chunk: [],
                    tags: [],
                    similers_images: [],
                    img_details: null,
                    token_img: null,
                    type_img: null,
                    isActive: false,
                    notActive: true,
                    selectedTypeStock: 'vector',
                };
            },
            created: function () {
                // var vector = "{{ json_encode('$vector') }}";
                // console.log("vector,,,,,,,,,,,");
                // var machien_data = JSON.parse(vector.replace(/&quot;/g,'"'));

                // console.log(machien_data);
                var self = this;
                axios.get('/api/Vector/' + '{{$vector->id}}' + '/show?lang=' + lng)
                    .then(response => {
                        self.categories = response.data.categories;
                    }).catch(error => {
                    console.log(error);

                });

            },
            mounted: function () {

            },
            watch: {},

            computed: {},
            methods: {
                trans(term = "") {

                    var locals = {
                        'thumbnail': "{{trans('global.image')}}",
                        'vector': "{{trans('global.vector')}}",
                        // 'medium': "{{trans('global.medium')}}",
                        // 'small': "{{trans('global.small')}}",
                    };

                    return locals[term];

                },

                gotoCategoryPage(slug) {

                    var link = '{{route('vectors.category.show',':slug')}}';
                    link = link.replace(':slug', slug);
                    $(location).attr('href', link);

                },

                gotoImage(post_link) {

                    window.location.href = post_link;

                },

                downloadImag() {
                    @if(auth()->check())
                        @if(auth()->user()->free_vectors)
                        window.location.href = '{{route('vectors.download',$vector->token_id)}}?type=vector';
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
                showModale(id, img, title) {

                    <?php if(!auth()->check()): ?>

                    $('#login').modal('show');

                    return false;

                    <?php endif; ?>

                    $('#myCollections').text('');

                    var link = '{{route('vectors.vectorCollection',':id')}}';
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
                            $('#collection-model-vector').modal('show');
                            image_id = id;
                        },
                    });


                },
                goToCategoryVector(slug) {
                    var link = "{{route('vectors.category.show',':slug')}}";
                    link = link.replace(':slug', slug);
                    window.location.href = link;
                },
            },
        });

        $("#toggle").click(function () {
            $("#tags").toggleClass('overflow-hidden h-85');
        });
    </script>

    <script>
        $(document).on("click", '.save_vector', function (e) {
            let element = $(this);
            console.log(element.text());
            let id = element.attr("data-id");
            let like = element.attr('data-like');
            let like_active = element.attr('data-unlike');
            let data = 'id=' + id;
            e.preventDefault();
            element.blur();
            element.find('i').addClass('fa-heart');
            let msg = '';
            if (element.hasClass('active')) {
                element.removeClass('active');
                element.find('i').removeClass('far fa-heart').addClass('fas fa-heart');
                msg = "{{trans('misc.unlike_photo_video')}}";
            } else {
                element.addClass('active');
                element.find('i').removeClass('fas fa-heart').addClass('far fa-heart');
                msg = "{{trans('misc.like_photo_video')}}";
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: "POST",
                url: '{{url('/:locale/vectors/ajax/like')}}'.replace(':locale', window.app_locale),
                data: data,
                success: function (result) {
                    jQuery.notify({
                        title: "<strong>" + msg + "</strong>",
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
                    if (result == '') {
                        window.location.reload();
                        element.removeClass('active');
                    } else {
                        element.find('i').removeClass('icon-spinner2 fa-spin');
                    }
                },
            });
        })
    </script>
    <script>
        @if($simler_vectors->count())
        $('#vectorsFlex').flexImages({rowHeight: 200, maxRows: 2});
        @endif
        @if($simler_images->count())
        $('#imagesFlex').flexImages({rowHeight: 200, maxRows: 2});
        @endif
        @if($same_group->count() >= 4)
        $('#same_group').flexImages({rowHeight: 200, maxRows: 2, truncate: 1});
        @endif
    </script>
@endpush
