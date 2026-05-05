@extends('includes.profile')

@section('title') {{ $title }} @endsection

@section('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('profile_content')
<div class="container">
    <h3 class="pb-3">{{ trans('misc.collection') }}</h3>
    <p class="mb-2 color-primary"><strong>{{ $collectionData->title }} ({{number_format($videos->total())}})</strong></p>


    @if( Auth::check() )
    <?php
        $collection = new \App\Models\VideoCollection();
        $collections = $collection->where( 'user_id', \Auth::user()->id )->orderBy( 'id', 'asc' )->get(); ?> @endif

<hr>
            @if( $videos->total() != 0 )
            <div id="videogrid" class="flex-images my-3 mx-out-3 profile-control-option-grid grid-mycollection">
            @if($videos)
@foreach( $videos as $video )
 @if($video->parent_id==null)

<div  class="item" data-w="16" data-h="9">
    <div class="card-video border-file">
           <a id="click_video{{$video->id}}" href="{{$video->post_link}}" class="card-vide-a">

            <video id="v{{$video->id}}" class="arabs-video" poster="{{ cdn($video->thumbnail_sm) }}">
                <source data-src="{{url($video->size_240p)}}" type="video/mp4" />
            </video>

            <div class="loading_overlay loading_dispaly" id="loading_overlayv{{$video->id}}">
                <div data-react-toolbox="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="video_progress_bar" data-automation="VideoLoaded_loader_circleProgressBar">
                    <svg class="o_progress_bar_theme_circle" viewBox="22 22 44 44">
                        <circle class="progress_path white_stroke" cx="44" cy="44" r="20.2"></circle>
                    </svg>
                </div>
            </div>
            <div class="over">
              <p class="card-photo-title pr-title"> {{$video->title}} </p>


            </div>
        <a href="#" title="{{trans('admin.edit')}}"  class="downloade-btn btn downloadImag  editCollection" video_id="{{$video->collections_video_id}}"><i class="far fa-edit"></i></a>
            <a href="" title="{{trans('admin.delete')}}"  class="downloade-btn btn downloadImag  actionDelete" data-url="{{route('collection.videoDelete',['collectionID'=>$collectionData->id,'imageID'=>$video->id])}}"><i class="far fa-trash-alt"></i></a>

        </a>
    </div>
</div>

@endif
 @endforeach
  @endif

                @if( $videos->count() != 0 )
                <div class="container-paginator">
                    {{ $videos->links() }}
                </div>
                @endif
            </div>
            <!-- Image Flex -->

            @else
            <div class="btn-block text-center pt-5">
                    <i class="fal fa-exclamation-circle"></i>
                </div>

            <h3 class=" btn-block text-center no-result no-result-mg">{{ trans('misc.collection_empty') }}</h3>
            @endif
            <div class="modal fade" id="collections" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content p-2">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ trans('admin.edit') }}</h5>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                        </div>
                        <!-- Modal header -->

                        <div class="modal-body listWrap">
                            <!-- form start -->
                            <form method="put" action="" id="editCollectionVideos">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input type="hidden" name="id" id="editId" value="" />

                                <!-- Start Form Group -->

                                <!-- /.form-group-->

                                <!-- Start form-group -->


                                <div class="form-group">
                                    @foreach ($collections as $collection)
                                  <div class="radio">
                                      <label class="padding-zero"><input type="radio" name="collection_id" @if($collectionData->id==$collection->id)checked="checked" @endif value="{{$collection->id}}" > {{$collection->title }} </label>
                                  </div>
                                    @endforeach



                                </div>
                                <!-- /.form-group -->



                                <div class="btn-block text-center">
                                    <button type="submit" class="btn btn-primary" id="editCollection">{{ trans('misc.save_changes') }}</button>
                                </div>
                            </form>
                        </div>
                        <!-- Modal body -->
                    </div>
                    <!-- Modal content -->
                </div>
                <!-- Modal dialog -->
            </div>

    <!-- container wrap-ui -->
    @endsection @section('javascript')
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script type="text/javascript">


        $('#videogrid').flexImages({ rowHeight: 300 });

         @if( Auth::check() && Auth::user()->id == $collectionData->user_id )
         $(".editCollection").click(function(){
                    $("#collections").modal("show");
                    $("#editId").val($(this).attr("video_id"));
                });

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
            @endif
    </script>
    @endsection
</div>
