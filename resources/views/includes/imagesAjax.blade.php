@if(count($images)>0)





	@if( Auth::check() )
	<?php

	$collections = App\Models\Collection::where('user_id', \Auth::user()->id)->orderBy('id', 'asc')->get();
	?>
	@endif

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

  <div class="masonry-item animated fadeIn delay-1s">
	  <a href="{{ $image->post_link }}">
    <img src="{{ url_resizer($image->medium, '500x300') }}" alt="" class="img-fluid">
	  </a>
  </div>


	@endforeach
@endif
