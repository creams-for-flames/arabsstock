@if($images)


	@if( Auth::check() )
	<?php

	$collections = App\Models\Collection::where('user_id', \Auth::user()->id)->orderBy('id', 'asc')->get();
	?>
	@endif
	<div>

	@foreach( $images as $image )

		<!-- Start Item -->
		@if( Auth::check() )
			<div class="modal fade" id="collections" tabindex="-1" role="dialog" aria-hidden="true">
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
						</div><!-- Modal header -->

						<div class="modal-body listWrap">

							<div class="collectionsData">
								@if( $collections->count() != 0 )
									@foreach ( $collections as $collection )

										<?php

										$collectionImages = $collection->collection_images->where('images_id', $image->id)->where('collections_id', $collection->id)->first();
										if (!empty($collectionImages)) {
											$checked = 'checked="checked"';
										} else {
											$checked = null;
										}
										?>
										<div class="radio margin-bottom-15">
											<label class="checkbox-inline padding-zero addImageCollection text-overflow" data-image-id="{{$image->id}}" data-collection-id="{{$collection->id}}">
												<input class="no-show" name="checked" {{$checked}} type="checkbox" value="true">
												<span class="">{{$collection->title}}</span>
											</label>
										</div>

									@endforeach



								@else
									<div class="btn-block text-center no-collections">{{ trans('misc.no_have_collections') }}</div>
								@endif

							</div><!-- collection data -->

							<small class="btn-block note-add @if( $collections->count() == 0 ) display-none @endif">* {{ trans('misc.note_add_collections') }}</small>

							<span class="label label-success display-none btn-block response-text"></span>

							<!-- form start -->
							<form method="POST" action="" enctype="multipart/form-data" id="addCollectionForm">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="image_id" value="{{ $image->id }}">

								<!-- Start Form Group -->
								<div class="form-group">
									<label>{{ trans('global.title') }}</label>
									<input type="text" value="" name="title" id="titleCollection{{$loop->index}}" class="form-control" placeholder="{{ trans('global.title') }}">
								</div><!-- /.form-group-->

								<!-- Start form-group -->
								<div class="form-group">

									<div class="radio">
										<label class="padding-zero">
											<input type="radio" name="type" checked="checked" value="public">
											{{ trans('misc.public') }}
										</label>
									</div>

									<div class="radio">
										<label class="padding-zero">
											<input type="radio" name="type" value="private">
											{{ trans('misc.private') }}
										</label>
									</div>

								</div><!-- /.form-group -->

								<!-- Alert -->
								<div class="alert alert-danger alert-small display-none" id="dangerAlert">
									<ul class="list-unstyled" id="showErrors"></ul>
								</div><!-- Alert -->

								<div class="btn-block text-center">
									<button type="submit" class="btn btn-sm btn-success" id="addCollection{{$loop->index}}">{{ trans('misc.create_collection') }}
										<i class="fa fa-plus"></i></button>
								</div>

							</form>

						</div><!-- Modal body -->
					</div><!-- Modal content -->
				</div><!-- Modal dialog -->
			</div><!-- Modal -->
		@endif


        <div class="item card-photo"  data-w="{{($image->width_thumbnail)}}px" data-h="{{($image->height_thumbnail)}}px">
                <div class="hover border-file h-100">
                    <a href="{{ $image->post_link }}">
                    <img class="w-100 h-100" srcset="{{ url($image->thumbnail) }}" src="{{ url($image->thumbnail) }}" alt="{{$image->title}}">
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
                                <div class="icon_save" onclick="showModal('{{$image->id}}','{{class_basename($image)}}','{{ url($image->thumbnail) }}','{{$image->title_en}}')">
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
	</div>
@endif
