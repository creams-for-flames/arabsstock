@extends('includes.profile')

@section('title'){{ $title }}@endsection

@section('css')
  <link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('profile_content')
<div class="container">
    <h3 class="pb-3">{{ trans('misc.collection') }}</h3>
    <p class="mb-2 color-primary"><strong>{{ $collectionData->title  }}  ({{number_format($images->total())}})</strong></p>


<hr>

    @if( $images->total() != 0 )
    <div id="imagesFlex" class="flex-images my-3 mx-out-3 profile-control-option-grid grid-mycollection">
        @if($images)
          @foreach( $images as $image )
        <div class="item card-photo" style="background-image: url('{{ cdn($image->thumbnail) }}');" data-w="16" data-h="9">
            <div class="hover border-file">
                <a href="{{ $image->post_link }}">
                    <div class="hover-overlay"></div>
                </a>
                <div class="card-photo-content">
                    <h3 class="card-photo-title pr-title"><a href="{{ $image->post_link }}">{{$image->title}} {{ date('c', strtotime( $image->date )) }}  </a>
                    </h3>

                </div>


            </div>
            <a href="#" title="{{trans('admin.edit')}}"  class="downloade-btn btn downloadImag  editCollection" image_id="{{$image->collections_images_id}}"><i class="far fa-edit"></i></a>
            <a href="#" title="{{trans('admin.delete')}}"  class="downloade-btn btn downloadImag  actionDelete" data-url="{{route('collection.imageDelete',['collectionID'=>$collectionData->id,'imageID'=>$image->id])}}"><i class="far fa-trash-alt"></i></a>

        </div>
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
                        <form method="put" action="" id="editCollectionForm">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="id" id="editId" value="{{ $image->id }}" />

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
        @endforeach

        @endif

              @if( $images->count() != 0  )
                  <div class="container-paginator">
                      {{ $images->links() }}
                  </div>
        @endif

    </div>
    <!-- Image Flex -->
    @else
                <div class="btn-block text-center pt-5">
                    <i class="fal fa-exclamation-circle"></i>
                </div>

    <h3 class=" btn-block text-center no-result no-result-mg">{{ trans('misc.collection_empty') }}
    </h3>
    @endif
</div>

</div>
 </div>
@endsection
@section('javascript')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script type="text/javascript">
    $("#imagesFlex").flexImages({ rowHeight: 200 });
</script>
<script type="text/javascript">


    $('#imagesFlex').flexImages({ rowHeight: 220 });

     @if( Auth::check() && Auth::user()->id == $collectionData->user_id )

    $(".editCollection").click(function(){
        $("#collections").modal("show");
        $("#editId").val($(this).attr("image_id"));
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
