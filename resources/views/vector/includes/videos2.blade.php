@if($vectors['vectors'])


	@if( Auth::check() )
	<?php
// 				$collection = new \App\Models\Collection();
// $collection->setConnection('mysqlVector');
// 	$collections = $collection->where('user_id', \Auth::user()->id)->orderBy('id', 'asc')->get();
	?>
	@endif


	@foreach( $vectors['vectors']->items() as $vector )


        <div class="item card-photo"  data-w="{{($vector->width_thumbnail)}}px" data-h="{{($vector->height_thumbnail)}}px">
                <div class="hover border-file h-100">
                    <a href="{{ $vector->post_link }}">
                    <img class="w-100 h-100" srcset="/{{ $vector->thumbnail }}" src="{{ $vector->thumbnail }}" alt="{{$vector->title}}">
                        <div class="hover-overlay"></div>
                    </a>
                    <div class="card-photo-content">
                        <h3 class="card-photo-title">{{$vector->title}}</h3>
                        <div class="icon">
                            <div class="d-flex flex-row-reverse">
                                <div class="icon_save">
                                    <span> <i data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}" data-type="{{class_basename($vector)}}" class="fal fa-heart @if($vector->is_like) active @endif likeButton"></i> </span>
                                    <span>{{__('misc.like')}}</span>
                                </div>
                                <div class="icon_save" onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ $vector->thumbnail }}','{{$vector->title_en}}')">
                                    <span> <i class="fal fa-plus-circle"></i> </span> <span>{{__('misc.save_to_collection')}}</span>
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

@endif
