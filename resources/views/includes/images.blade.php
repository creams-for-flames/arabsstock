@if($images)


	@if( Auth::check() )
	<?php

	$collections = App\Models\ImageCollection::where('user_id', \Auth::user()->id)->orderBy('id', 'asc')->get();
	?>
	@endif


	@foreach( $images as $image )


        <div class="item card-photo"  data-w="{{($image->width_thumbnail)}}px" data-h="{{($image->height_thumbnail)}}px">
                <div class="hover border-file h-100">
                    <a href="{{ $image->post_link }}">
                    <img class="w-100 h-100" srcset="{{ cdn($image->thumbnail) }}" src="{{ cdn($image->thumbnail) }}" alt="{{$image->title}}" width="{{($image->width_thumbnail??300)}}" height="{{($image->height_thumbnail??300)}}">
                        <div class="hover-overlay"></div>
                    </a>
                    <div class="card-photo-content">
                        <h3 class="card-photo-title">{{$image->title}}</h3>
                        <div class="icon">
                            <div class="d-flex flex-row-reverse">
                                <div class="icon_save">
                                    <span> <i data-id="{{$image->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}" data-type="{{class_basename($image)}}" class="fal fa-heart @if($image->is_like) active @endif likeButton"></i> </span>
                                    <span>{{__('misc.like')}}</span>
                                </div>
                                <div class="icon_save" onclick="showModal('{{$image->id}}','{{class_basename($image)}}','{{ cdn($image->thumbnail) }}','{{$image->title_en}}')">
                                    <span> <i class="fal fa-plus-circle"></i> </span> <span>{{__('misc.save_to_collection')}}</span>
                                </div>
                                <div class="icon-similar">
                                    <a href="{{route('similar.files',['type'=>'photos','section'=>'image','id'=>$image->id ])}}">
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

@endif
