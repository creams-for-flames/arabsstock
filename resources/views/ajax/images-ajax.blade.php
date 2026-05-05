@foreach( $images as $imageItem )
            <div class="item card-photo" style="background-image: url('{{ url($imageItem->thumbnail) }}')" data-w="{{($imageItem->width_thumbnail)}}px" data-h="{{($imageItem->height_thumbnail)}}px">
                <div class="hover border-file">
                    <a href="{{ $imageItem->post_link }}">
                        <div class="hover-overlay"></div>
                    </a>
                    <div class="card-photo-content">
                        <h3 class="card-photo-title">{{$imageItem->title}}</h3>
                        <div class="icon">
                            <div class="d-flex flex-row-reverse">
                                <div class="icon_save">
                                    <span> <i data-id="{{$imageItem->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}" data-type="{{class_basename($imageItem)}}" class="fal fa-heart @if($imageItem->is_like) active @endif likeButton"></i> </span>
                                    <span>{{__('misc.like')}}</span>
                                </div>
                                <div class="icon_save" onclick="showModal('{{$imageItem->id}}','{{class_basename($imageItem)}}','{{ url($imageItem->thumbnail) }}','{{$imageItem->title}}')">
                                    <span> <i class="fal fa-plus-circle"></i> </span> <span>{{__('misc.save_to_collection')}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endforeach
