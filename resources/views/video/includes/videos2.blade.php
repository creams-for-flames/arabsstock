@if($videos['images'])
    @foreach( $videos['images']->items() as $video )
        @if( Auth::check() )
            <?php
            $collection = new \App\Models\VideoCollection();
            $collections = $collection->where('user_id', \Auth::user()->id)->orderBy('id', 'asc')->get(); ?>
        @endif
        @if( Auth::check() )
            <div class="modal fade" id="collectionsVideo" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <h4 class="modal-title text-center" id="myModalLabel">
                                <strong>{{ trans('misc.add_collection') }}</strong>
                            </h4>
                        </div>
                        <!-- Modal header -->
                        <div class="modal-body listWrap">
                            <div class="collectionsData">
                                @if( $collections->count() != 0 ) @foreach ( $collections as $collection )

                                    <?php

                                    $collectionImages = $collection->collection_videos->where('video_id', $video->id)->where('collections_id', $collection->id)->first(); if (!empty($collectionImages)) {
                                        $checked = 'checked="checked"';
                                    } else {
                                        $checked = null;
                                    } ?>
                                    <div class="radio margin-bottom-15">
                                        <label class="checkbox-inline padding-zero addVideoCollection text-overflow"
                                               data-image-id="{{$video->id}}" data-collection-id="{{$collection->id}}">
                                            <input class="no-show" name="checked" {{$checked}} type="checkbox"
                                                   value="true"/>
                                            <span class="">{{$collection->title}}</span>
                                        </label>
                                    </div>

                                @endforeach @else
                                    <div
                                        class="btn-block text-center no-collections">{{ trans('misc.no_have_collections') }}</div>
                                @endif
                            </div>
                            <!-- collection data -->
                            <small
                                class="btn-block note-add @if( $collections->count() == 0 ) display-none @endif">* {{ trans('misc.note_add_collections') }}</small>
                            <span class="label label-success display-none btn-block response-text"></span>
                            <!-- form start -->
                            <form method="POST" action="" enctype="multipart/form-data" id="addVideoCollectionForm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="video_id" value="{{ $video->id }}"/>
                                <!-- Start Form Group -->
                                <div class="form-group">
                                    <label>{{ trans('global.title') }}</label>
                                    <input type="text" value="" name="title" id="titleCollection" class="form-control"
                                           placeholder="{{ trans('global.title') }}"/>
                                </div>
                                <!-- /.form-group-->
                                <!-- Start form-group -->
                                <div class="form-group">
                                    <div class="radio">
                                        <label class="padding-zero">
                                            <input type="radio" name="type" checked="checked" value="public"/>
                                            {{ trans('misc.public') }}
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label class="padding-zero">
                                            <input type="radio" name="type" value="private"/>
                                            {{ trans('misc.private') }}
                                        </label>
                                    </div>
                                </div>
                                <!-- /.form-group -->
                                <!-- Alert -->
                                <div class="alert alert-danger alert-small display-none" id="dangerAlert">
                                    <ul class="list-unstyled" id="showErrors"></ul>
                                </div>
                                <!-- Alert -->
                                <div class="btn-block text-center">
                                    <button type="submit" class="btn btn-sm btn-success"
                                            id="addVideoCollection">{{ trans('misc.create_collection') }} <i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </form>
                        </div>
                        <!-- Modal body -->
                    </div>
                    <!-- Modal content -->
                </div>
                <!-- Modal dialog -->
            </div>
            <!-- Modal -->
        @endif

        <div class="item video-item" data-w="16" data-h="9" data-mp4="{{cdn($video->size_240p)}}"
             data-thumbnail="{{ cdn($video->thumbnail_sm) }}" id="v{{$video->id}}" data-reserved="{{ $video->reserved?1:0 }}">
            <div class="card-video border-file">
                <img src="{{ cdn($video->thumbnail_sm) }}" alt="" class="img-fluid-">
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
                                <i data-id="{{$video->id}}" data-like="{{trans('misc.like')}}"
                                   data-unlike="{{trans('misc.unlike')}}"
                                   data-type="{{class_basename($video)}}"
                                   class="fal fa-heart @if($video->is_like) active @endif likeButton"></i>
                            </span>
                        <span> {{ trans('global.like') }}</span>
                    </div>
                    <div class="icon_save"
                         onclick="showModal('{{$video->id}}','{{class_basename($video)}}','{{ cdn($video->thumbnail_sm) }}','{{$video->title}}')">
                        <span> <i class="fal fa-plus-circle"></i> </span>
                        <span>{{ trans('global.addition') }}</span>
                    </div>
                    <div class="icon-similar">
                        <a href="{{route('similar.files',['type'=>'videos','section'=>'clip','id'=>$video->id ])}}">
                            <span><i class="fal fa-th"></i></span>
                            <span>{{ __('misc.similar') }}</span>
                        </a>
                    </div>
                    <div style="display: none" data-video-id="{{$video->id}}"
                         onclick="showModal('{{$video->id}}','{{class_basename($video)}}','javascript:;','{{$video->title}}')"
                         class="icon-similar btnAddCart">
                        <span><i class="fal fa-cart-plus"></i></span> <span>{{ trans('global.add_to_cart') }}</span>
                    </div>
                <!-- <li> <a class="btnAddCart" href="javascript:;" data-video-id="{{$video->id}}"  title="{{ trans('global.add_to_cart') }}"><i class="fal fa-cart-plus"></i>{{ trans('global.add_to_cart') }} </a></li> -->
                </div>
            </div>
        </div>
    @endforeach
@endif
