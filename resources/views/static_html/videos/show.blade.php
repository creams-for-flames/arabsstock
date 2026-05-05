@extends('app')
@section('title')
    {{ $response->title.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$response->id.' - ' }}@endsection
@section('description_custom')
{{ $response->title.' - '.trans_choice('misc.videos_plural', 1 ).'#'.$response->id.' - ' }} @if( $response->description != '' ){{ App\Helper::removeLineBreak( e( $response->description ) ).' - ' }}
@endif @endsection @section('keywords_custom'){{implode(',',$tags_chunk)}}@endsection @section('meta')

<meta property="og:type" content="website" />
<meta property="og:image:width" content="{{$response->thumbnail_width}}" />
<meta property="og:image:height" content="{{$response->thumbnail_height}}" />
<meta property="og:site_name" content="{{$settings->title}}" />
<meta property="og:url" content="{{$response->post_link}}"/>
<meta property="og:image" content="{{ cdn($response->thumbnail) }}" />
<meta property="og:title" content="{{ $response->title.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$response->id }}" />
<meta property="og:description" content="{{ App\Helper::removeLineBreak( e( $response->description ) ) }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="{{ cdn($response->thumbnail) }}" />
<meta name="twitter:title" content="{{ $response->title.' - '.trans_choice('misc.videos_plural', 1 ).' #'.$response->id }}" />
<meta name="twitter:description" content="{{ App\Helper::removeLineBreak( e( $response->description ) ) }}" />

@endsection @section('content')

<section class="view-video mt-50">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-7 col-lg-7">
                <div class="photo">
                    <div class="text-center">
                        <video controls autoplay loop muted poster="{{url($response->thumbnail)}}" style="object-fit: contain; width: 100%;">
                            <source src="{{url($response->cut_video)}}" type="video/mp4" />
                        </video>
                    </div>
                    <div class="categories-related">
                        @if($response->category) @foreach($response->category as $responseCategory)
                        <a href="{{route('video.category.show',$responseCategory->slug)}}" title="{{$responseCategory->name_en}}" class="btn btn-sm btn-outline-secondary category rounded-pill mb-2 mt-2">
                            @if(App::isLocale('en')) {{str_limit($responseCategory->name_en, 18, '...') }} @else {{str_limit($responseCategory->name_ar, 18, '...') }} @endif
                        </a>
                    @endforeach @endif
                </div>
                </div>
                <div class="publish-title mt-4 pb-4">
                    <p class="text-muted published mb-0">
                        <!-- {{trans('misc.published')}} <span class="color-primary"> {{App\Helper::formatDate($response->date)}}</span>
                         <span class="mx-3"> | </span> -->
                         <span> {!! trans('global.Stock_Id_Video',['id' => $response->id])!!} </span></p>
                    <h4>{{ $response->title }}</h4>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                <div class="details-photo py-2">
                    <div class="license">
                        <h4 class="mb-3">{{trans('misc.license_and_use')}}</h4>
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6"><i class="fal fa-check-circle"></i> {{trans('misc.no_attribution_required')}}</div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6"><i class="fal fa-check-circle"></i> {{trans('misc.Signed-model-release')}}</div>
                        </div>
                    </div>
                    <hr />
                    <div class="videofile-information">
                        <p>
                            <b>{{__('views.Video clip length:')}}</b> {{$response->duration}} <b class="pl-2">{{__('views.FPS')}}:</b> {{$response->fps}} <b class="pl-2">{{__('views.Aspect ratio:')}}</b> {{get_aspect_ratio($response->width,
                            $response->height)}}
                        </p>
                    </div>

                    <div id="videos-radio-list">
                        @include('video.includes.video_options_resolution_list')
                    </div>
                    <hr />
                    <div class="like-share">
                    <button type="button" class="btn btn-outline-light">
                            <i data-id="{{$response->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}"
                               data-type="{{class_basename($response)}}"
                               class="far fa-heart @if($response->is_like) active @endif likeButton mr-1"></i>
                            {{__('misc.like')}}
                        </button>
                        <button type="button" class="btn btn-outline-light" data-toggle="modal" data-target="#sharemodel">
                            <i class="far fa-share-alt mr-1"></i>
                            {{__('misc.share')}}
                        </button>

                        <button onclick="showModal('{{$response->id}}','{{class_basename($response)}}','{{ cdn($response->thumbnail_sm) }}','{{$response->title}}')" type="button" class="btn btn-outline-light">
                            <i class="far fa-plus-circle mr-1"></i>
                            {{__('misc.save_to_collection')}}
                        </button>
                        <div class="modal fade" id="sharemodel" tabindex="-1" role="dialog" aria-labelledby="sharemodel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="sharemodel">{{__('misc.share')}}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="share"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(count($tags))
<section class="related-keywords mt-5 mb-5">
    <div class="container">
        <h3 class="mb-4">
            {{__('global.keywords')}}
            <button id="toggle" data-slide="open">{{trans('global.Show-all')}}</button>
        </h3>
       @foreach($tags[0] as $tag)
        <span><a href="{{route('video.tags.show', $tag)}}" class="btn btn-outline-secondary rounded-pill mb-2">{{$tag}}</a></span>
        @endforeach
       @foreach($tags[1] as $tag)
        <span class="moretag" style="display: none;"><a href="{{route('video.tags.show', $tag)}}" class="btn btn-outline-secondary rounded-pill mb-2">{{$tag}}</a></span>
        @endforeach
    </div>
</section>
@endif

<section class="simler-images mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-between">
            @if(count($simler_video)>0)
            <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                <h3 class="text-capitalize">{{trans('misc.similar_videos')}}</h3>
            </div>
        </div>
        @endif
                    <div id="videogrid" class="flex-images">
                        @if(count($simler_video) > 0)
                        @foreach($simler_video as $videosItem)
                        <div class="item" data-w="16" data-h="9">
                            <div class="card-video border-file">
                                <a href="{{ $videosItem->post_link }}" class="card-vide-a">
                                    <video id="v{{$videosItem->id}}" class="arabs-video" poster="{{ cdn($videosItem->thumbnail_sm) }}">
                                        <source data-src="{{url($videosItem->size_240p)}}" type="video/mp4" />
                                    </video>

                                    <div class="loading_overlay loading_dispaly" id="loading_overlayv{{$videosItem->id}}">
                                        <div data-react-toolbox="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="video_progress_bar" data-automation="VideoLoaded_loader_circleProgressBar">
                                            <svg class="o_progress_bar_theme_circle" viewBox="22 22 44 44">
                                                <circle class="progress_path white_stroke" cx="44" cy="44" r="20.2"></circle>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="over">
                                        <p>{{$videosItem->title}}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endforeach @endif
                    </div>
                    <!-- Image Flex -->
    </div>
</section>

@if( Auth::check() && $response->user->id != Auth::user()->id && $response->user->paypal_account != '' || Auth::guest() && $response->user->paypal_account != '' )
<form id="form_pp" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post" style="display: none;">
    <input type="hidden" name="cmd" value="_donations" />
    <input type="hidden" name="return" value="{{$response->post_link}}" />
    <input type="hidden" name="cancel_return" value="{{$response->post_link}}" />
    <input type="hidden" name="currency_code" value="USD" />
    <input type="hidden" name="item_name" value="{{trans('misc.support').' @'.$response->user->username}} - {{$settings->title}}" />
    <input type="hidden" name="business" value="{{$response->user->paypal_account}}" />
    <input type="submit" />
</form>

@endif

@endsection
