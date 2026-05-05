
@if($videos)

@foreach( $videos as $video )

	@if($video->parent_id==null)
		@if( Auth::check() )
			<?php
			$collection = new \App\Models\Collection();
			$collection->setConnection('mysqlVideo');
			$collections = $collection->where('user_id', \Auth::user()->id)->orderBy('id',
					'asc')->get();
			?>
		@endif
<!-- Start Item -->

@if( Auth::check() )

@endif

<div id="videos" class="col-md-3 video10" > <a href="{{ $video->post_link }}" class="more-famus">
	<video width="100%" class=" arabs-video" id="v{{$video->id}}" data-automation="VideoPlayer_video_video" loop="" muted="" playsinline="" poster="{{ cdn($video->thumbnail_sm) }}" style="transform: rotate(0deg); width: 100%;">
      <source data-src="{{url($video->size_240p)}}" type="video/mp4">
	</video>
	<h2>{{$video->title}} </h2>
	<h2 class="time-video" >{{$video->duration}} </h2>
	</a>
	<ul class="option-video">
	  {{-- <li> <a href="@if(count($video->category)>0){{route('video.category.show',$video->category()->first()->slug))}}@endif" data-toggle="tooltip" data-placement="top" title="{{ trans('global.similar') }}" class="black-tooltip"><i class="fal fa-th-large"></i> {{ trans('global.similar') }} </a></li> --}}
	  <li> <a data-id="{{$video->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}" class="likeVideoButton" href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('global.like') }}"><i class="fal fa-heart"></i> {{ trans('global.like') }} </a></li>
	  <li> <a class="btn-collection" data-toggle="modal" data-target="#collectionsVideo" href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('global.addition') }}"><i class="fal fa-plus-circle"></i>{{ trans('global.addition') }} </a></li>
	  <li> <a class="btnAddCart" href="javascript:;" data-video-id="{{$video->id}}"  title="{{ trans('global.add_to_cart') }}"><i class="fal fa-cart-plus"></i>{{ trans('global.add_to_cart') }} </a></li>
	</ul>
	@if( Auth::check() && Auth::user()->id == $collectionData->user_id )
		<p class="subtitle-site">
			{{-- <a href="#" title="{{trans('admin.edit')}}" class="btn btn-success btn-sm myicon-right editCollection" data-toggle="modal" data-target="#collections"><i class="fa fa-pencil myicon-right "></i> {{trans('admin.edit')}}
			</a>
			<a href="#" title="{{trans('admin.edit')}}" class="btn btn-primary editCollection" image_id="{{$image->collections_images_id}}"><i class="fal fa-edit pr-1"></i>{{trans('admin.edit')}}</a> --}}

			<a href="#" title="{{trans('admin.delete')}}" data-url="{{route('collection.videoDelete',['collectionID'=>$collectionData->id,'videoID'=>$video->id])}}" class="btn btn-danger actionDelete">
				<i class="fal fa-trash-alt pr-1"></i>{{trans('admin.delete')}}
			</a>

		</p>

		<!-- Start Modal -->
		<div class="modal fade" id="collections" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content p-2">
					<div class="modal-header">
					<h5 class="modal-title">{{ trans('admin.edit') }}</h5>
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span>
							<span class="sr-only">Close</span>
						</button>

					</div><!-- Modal header -->

					<div class="modal-body listWrap">

						<!-- form start -->
						<form method="POST" action="" enctype="multipart/form-data" id="editCollectionForm">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="id" value="{{ $collectionData->id }}">

							<!-- Start Form Group -->
							<div class="form-group">
								<label>{{ trans('admin.title') }}</label>
								<input type="text" value="{{ $collectionData->title }}" name="title" id="titleCollection" class="form-control" placeholder="{{ trans('admin.title') }}">
							</div><!-- /.form-group-->

							<!-- Start form-group -->
							<div class="form-group">

								<div class="radio">
									<label class="padding-zero">
										<input type="radio" name="type" @if($collectionData->type == 'public' ) checked="checked" @endif  value="public">
										{{ trans('misc.public') }}
									</label>
								</div>

								<div class="radio">
									<label class="padding-zero">
										<input type="radio" name="type" @if($collectionData->type == 'private' ) checked="checked" @endif  value="private">
										{{ trans('misc.private') }}
									</label>
								</div>

							</div><!-- /.form-group -->


							<div class="btn-block text-center">
								<button type="submit" class="btn btn-sm btn-success" id="editCollection">{{ trans('misc.save_changes') }}</button>
							</div>

						</form>

					</div><!-- Modal body -->
				</div><!-- Modal content -->
			</div><!-- Modal dialog -->
		</div><!-- Modal -->
	@endif
  </div>




@endif
@endforeach
	@endif
	@section('javascript')
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

		<script type="text/javascript">
			$(".actionDelete").click(function(e) {
				e.preventDefault();

				var element = $(this);
				var url     = element.attr('data-url');

				element.blur();

				swal(
					{   title: "{{trans('misc.delete_confirm')}}",
						type: "warning",
						showLoaderOnConfirm: true,
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "{{trans('misc.yes_confirm')}}",
						cancelButtonText: "{{trans('misc.cancel_confirm')}}",
						closeOnConfirm: false,
					},
						function(isConfirm){
							if (isConfirm) {
								window.location.href = url;
							}
						});
			});
		</script>
	@endsection
