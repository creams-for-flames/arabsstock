@if($videos)
@foreach( $videos as $video )
 @if($video->parent_id==null)
  @if( Auth::check() )
<?php
    $collection = new\ App\ Models\ Collection();
    $collection->setConnection( 'mysqlVideo' ); $collections = $collection->where( 'user_id', \Auth::user()->id )->orderBy( 'id', 'asc' )->get(); ?> @endif
<div class="item video-item" data-w="16" data-h="9">
        <div class="card-video border-file">
            <video   id="v{{$video->id}}" class="arabs-video" poster="{{ cdn($video->thumbnail_sm) }}">

                <source data-src="{{url($video->size_240p)}}" type="video/mp4" />
            </video>

            <div  class="loading_overlay loading_dispaly" id="loading_overlayv{{$video->id}}">
                <div data-react-toolbox="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="video_progress_bar" data-automation="VideoLoaded_loader_circleProgressBar">
                    <svg class="o_progress_bar_theme_circle" viewBox="22 22 44 44">
                        <circle class="progress_path white_stroke" cx="44" cy="44" r="20.2"></circle>
                    </svg>
                </div>
            </div>

        </div>

            <div onclick="location.href='{{$video->post_link}}'" class="over">
            <a id="click_video{{$video->id}}" href="{{$video->post_link}}" class="card-vide-a ">
            <p>{{$video->title}} </p>
            </a>
            </div>
            <div class="icon">
                    <div class="d-flex flex-row-reverse">
                        <div class="icon_save">
                            <span>
                                <i data-id="{{$video->id}}" data-like="{{trans('misc.like')}}"
                                     data-unlike="{{trans('misc.unlike')}}"
                                     data-type="{{class_basename($video)}}"
                                     class="fal fa-heart @if($video->is_like) active @endif likeButton"></i>
                            </span>
                            <span> {{ trans('global.like') }}</span>
                        </div>
                        <div class="icon_save" onclick="showModal('{{$video->id}}','{{class_basename($video)}}','{{ cdn($video->thumbnail_sm) }}','{{$video->title}}')">
                            <span> <i class="fal fa-plus-circle"></i> </span> <span>{{ trans('global.addition') }}</span>
                        </div>

                        <div class="icon-similar">
                            <a href="{{route('similar.files',['type'=>'videos','section'=>'clip','id'=>$video->id ])}}">
                                <span><i class="fal fa-th"></i></span>
                                <span>{{ __('misc.similar') }}</span>
                            </a>
                        </div>

                    </div>
                </div>



</div>
@endif
 @endforeach
  @endif
