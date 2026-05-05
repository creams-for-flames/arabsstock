@if($videos)
@foreach( $videos as $video )
@if($video->parent_id==null)
<div class="item video-item" data-w="{{ $video->width }}" data-h="{{ $video->height }}" data-thumbnail="{{ cdn($video->thumbnail_sm) }}" id="v{{$video->id}}">
    <div class="tag">
        <span> ProRes </span>
    </div>
    <div class="card-video border-file">
        <video width="100%" height="100%" preload="none" muted="" loop="true" class="d-none">
            <source type="video/webm" src="{{cdn($video->size_240p)}}">
        </video>
        <img src="{{ cdn($video->thumbnail) }}" alt="{{$video->title}}" class="img-fluid-">
    </div>
    <a class="over" href="{{$video->post_link}}">
        <span>
            <p>{{$video->title}} </p>
        </span>
    </a>
    <div class="icon">
        <div class="d-flex flex-row-reverse">
            <div class="icon_save">
                <span>
                    <i data-id="{{$video->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}" data-type="{{class_basename($video)}}" class="fal fa-heart @if($video->is_like) active @endif likeButton"></i>
                </span>
                <span> {{ trans('global.like') }}</span>
            </div>
            <div class="icon_save" onclick="showModal('{{$video->id}}','{{class_basename($video)}}','{{ cdn($video->thumbnail_sm) }}','{{$video->title}}')">
                <span> <i class="fal fa-plus-circle"></i> </span>
                <span>{{ trans('global.addition') }}</span>
            </div>
            @if($video->category->count())
            <div class="icon-similar">
                <a href="{{route('similar.files',['type'=>'videos','section'=>'clip','id'=>$video->id ])}}">
                    <span><i class="fal fa-th"></i></span> <span>{{ trans('global.similar') }}</span>
                </a>
            </div>
            @endif
            <div style="display: none" data-video-id="{{$video->id}}" onclick="showModal('{{$video->id}}','{{class_basename($video)}}','javascript:;','{{$video->title}}')" class="icon-similar btnAddCart">
                <span><i class="fal fa-cart-plus"></i></span> <span>{{ trans('global.add_to_cart') }}</span>
            </div>
            <!-- <li> <a class="btnAddCart" href="javascript:;" data-video-id="{{$video->id}}"  title="{{ trans('global.add_to_cart') }}"><i class="fal fa-cart-plus"></i>{{ trans('global.add_to_cart') }} </a></li> -->
        </div>
    </div>
</div>
@endif
@endforeach
@endif
