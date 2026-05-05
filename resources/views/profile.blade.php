@extends('includes.profile')


@section('profile_content')
<h4 class="title-site title-sm">
    {{ trans('users.account_settings') }}
</h4>
<p class="subtitle-site">
    <strong>
    </strong>
</p>
@if(count($vistits))
<div class="container margin-bottom-20">
    <!-- Col MD -->
    <div class="col-md-12 margin-top-10 margin-bottom-10 padding-bottom-30 Recently-viewed">
        <h2 class="padding-bottom-10 title-section-account">
            {{trans('global.r_viewdImage')}}
        </h2>
    </div>
    <!--  Demos -->
    <section id="demos">
        <div class="row row-owl">
            <div class="large-12 columns">
                <div class="" style="display: block;">
                    <div class="flex-images btn-block margin-bottom-40" id="imagesFlex1">
                        @foreach($vistits as $vistit)
                                    @if($vistit->images)
                        <?php $image = $vistit->
                        images;?>
                        <!-- Start Item -->
                        <a class="item hovercard" data-h="{{App\Helper::getHeight($image->thumbnail)}}" data-w="{{App\Helper::getWidth($image->thumbnail)}}" href="{{ $image->post_link }}">
                            <!-- hover-content -->
                            <span class="hover-content">
                                <h5 class="text-overflow title-hover-content" title="{{$image->title}}">
                                    @if( $image->featured == 'yes' )
                                    <i class="icon icon-Medal myicon-right" title="{{trans('misc.featured')}}">
                                    </i>
                                    @endif {{$image->title}}
                                </h5>
                                <h5 class="text-overflow author-label mg-bottom-xs" title="{{$image->user->username}}">
                                    <img alt="User" class="img-circle" src="{{ url('avatar/',$image->user->avatar) }}" style="width: 20px; height: 20px; display: inline-block; margin-right: 5px;">
                                        <em>
                                            {{$image->user->username}}
                                        </em>
                                    </img>
                                </h5>
                                <span class="timeAgo btn-block date-color text-overflow" data="{{ date('c', strtotime( $image->date )) }}">
                                </span>
                                <span class="sub-hover">
                                    <span class="myicon-right">
                                        <i class="fa fa-heart-o myicon-right">
                                        </i>
                                        {{$image->likes()->count()}}
                                    </span>
                                    <span class="myicon-right">
                                        <i class="icon icon-Download myicon-right">
                                        </i>
                                        {{$image->downloads()->count()}}
                                    </span>
                                </span>
                                <!-- Span Out -->
                            </span>
                            <!-- hover-content -->
                            <img src="{{ url($image->thumbnail) }}"/>
                        </a>
                        <!-- End Item -->
                        @endif
                                @endforeach
                    </div>
                    <!-- Image Flex -->
                </div>
            </div>
        </div>
    </section>
</div>
@endif







        @if(count($data))
<?php $userAuth = Auth::user(); ?>
<div class="container margin-bottom-20 collections-section">
    <div class="col-md-12">
        <h2 class="padding-bottom-20 title-section-account">
            {{trans('global.YourCollections')}}
            <span class="btween-slash">
                |
            </span>
            <a class="show-all" href="{{ route('user.collections',$user->username) }}">
                {{trans('global.seeall')}}
            </a>
        </h2>
    </div>
</div>
@else
<div class="container margin-bottom-20">
    <div class="rectangle-hestory">
        <h2>
            {{trans('global.no_result_found')}}
        </h2>
    </div>
</div>
@endif
<!-- Col MD -->
<div class="flex-images btn-block margin-bottom-40" id="imagesFlex2">
    @include('includes.collections')
</div>
<hr>
    <h3>
        فيديو
    </h3>
    @if(count($vistitsVideos))
    <div class="container margin-bottom-20">
        <!-- Col MD -->
        <div class="col-md-12 margin-top-10 margin-bottom-10 padding-bottom-30 Recently-viewed">
            <h5 class="padding-bottom-10">
                {{trans('global.r_viewdVideo')}}
            </h5>
            <!--  Demos -->
            <section id="demos">
                <div class="row row-owl">
                    <div class="large-12 columns">
                        <div class="" style="display: block;">
                            <div class="row all-famus more-downloading">
                                @if($vistitsVideos)

                                        @foreach( $vistitsVideos as $video )

                                            @if($video->parent_id==null)
                                                @if( Auth::check() )
                                <?php
                                                    $collection = new \App\Models\Collection();
                                                    $collection->
                                setConnection('mysqlVideo');
                                                    $collections = $collection->where('user_id', \Auth::user()->id)->orderBy('id',
                                                        'asc')->get();
                                                    ?>
                                                @endif
                                <!-- Start Item -->
                                @if( Auth::check() )
                                <div aria-hidden="true" class="modal fade" id="collectionsVideo" role="dialog" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button class="close" data-dismiss="modal" type="button">
                                                    <span aria-hidden="true">
                                                        ×
                                                    </span>
                                                    <span class="sr-only">
                                                        Close
                                                    </span>
                                                </button>
                                                <h4 class="modal-title text-center" id="myModalLabel">
                                                    <strong>
                                                        {{ trans('misc.add_collection') }}
                                                    </strong>
                                                </h4>
                                            </div>
                                            <!-- Modal header -->
                                            <div class="modal-body listWrap">
                                                <div class="collectionsData">
                                                    @if( $collections->count() != 0 )
                                                                                @foreach ( $collections as $collection )
                                                    <?php

                                                                                    $collectionImages = $collection->
                                                    collection_videos->where('video_id',
                                                                                        $video->id)->where('collections_id',
                                                                                        $collection->id)->first();
                                                                                    if (!empty($collectionImages)) {
                                                                                        $checked = 'checked="checked"';
                                                                                    } else {
                                                                                        $checked = null;
                                                                                    }
                                                                                    ?>
                                                    <div class="radio margin-bottom-15">
                                                        <label class="checkbox-inline padding-zero addVideoCollection text-overflow" data-collection-id="{{$collection->id}}" data-image-id="{{$video->id}}">
                                                            <input class="no-show" name="checked" type="checkbox" value="true" {{$checked}}="">
                                                                <span class="">
                                                                    {{$collection->title}}
                                                                </span>
                                                            </input>
                                                        </label>
                                                    </div>
                                                    @endforeach



                                                                            @else
                                                    <div class="btn-block text-center no-collections">
                                                        {{ trans('misc.no_have_collections') }}
                                                    </div>
                                                    @endif
                                                </div>
                                                <!-- collection data -->
                                                <small class="btn-block note-add @if( $collections->count() == 0 ) display-none @endif">
                                                    * {{ trans('misc.note_add_collections') }}
                                                </small>
                                                <span class="label label-success display-none btn-block response-text">
                                                </span>
                                                <!-- form start -->
                                                <form action="" enctype="multipart/form-data" id="addVideoCollectionForm" method="POST">
                                                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                                        <input name="video_id" type="hidden" value="{{ $video->id }}">
                                                            <!-- Start Form Group -->
                                                            <div class="form-group">
                                                                <label>
                                                                    {{ trans('global.title') }}
                                                                </label>
                                                                <input class="form-control" id="titleCollection" name="title" placeholder="{{ trans('global.title') }}" type="text" value="">
                                                                </input>
                                                            </div>
                                                            <!-- /.form-group-->
                                                            <!-- Start form-group -->
                                                            <div class="form-group">
                                                                <div class="radio">
                                                                    <label class="padding-zero">
                                                                        <input checked="checked" name="type" type="radio" value="public">
                                                                            {{ trans('misc.public') }}
                                                                        </input>
                                                                    </label>
                                                                </div>
                                                                <div class="radio">
                                                                    <label class="padding-zero">
                                                                        <input name="type" type="radio" value="private">
                                                                            {{ trans('misc.private') }}
                                                                        </input>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <!-- /.form-group -->
                                                            <!-- Alert -->
                                                            <div class="alert alert-danger alert-small display-none" id="dangerAlert">
                                                                <ul class="list-unstyled" id="showErrors">
                                                                </ul>
                                                            </div>
                                                            <!-- Alert -->
                                                            <div class="btn-block text-center">
                                                                <button class="btn btn-sm btn-success" id="addVideoCollection" type="submit">
                                                                    {{ trans('misc.create_collection') }}
                                                                    <i class="fa fa-plus">
                                                                    </i>
                                                                </button>
                                                            </div>
                                                        </input>
                                                    </input>
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
                                <div class="col-md-4 video11" id="video">
                                    <a class="more-famus" href="{{$video->post_link}}">
                                        <video class="H_j_h l_e_g" data-automation="VideoPlayer_video_video" loop="" muted="" playsinline="" poster="{{url($video->thumbnail)}}" style="transform: rotate(0deg); width: 100%;">
                                            <source src="{{url($video->size_240p)}}" type="video/mp4">
                                            </source>
                                        </video>
                                        <h2>
                                            {{$video->title}}
                                        </h2>
                                        <h2 class="time-video">
                                            {{$video->duration}}
                                        </h2>
                                    </a>
                                    <ul class="option-video">
                                        <li>
                                            <a class="black-tooltip" data-placement="top" data-toggle="tooltip" href="@if(count($video->category)>0){{route('video.category.show',$video->category()->first()->slug)}}@endif" title="{{ trans('global.similar') }}">
                                                <i class="fal fa-th-large">
                                                </i>
                                                {{ trans('global.similar') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="likeVideoButton" data-id="{{$video->id}}" data-like="{{trans('misc.like')}}" data-placement="top" data-toggle="tooltip" data-unlike="{{trans('misc.unlike')}}" href="#" title="{{ trans('global.like') }}">
                                                <i class="fal fa-heart">
                                                </i>
                                                {{ trans('global.like') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="btn-collection" data-placement="top" data-target="#collectionsVideo" data-toggle="tooltip" href="#" title="{{ trans('global.addition') }}">
                                                <i class="fal fa-plus-circle">
                                                </i>
                                                {{ trans('global.addition') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div id="videosList" style="display: none">
                                    <div class="video">
                                        <div class="videoListCopy">
                                            <a class="buttonMore" href="{{$video->post_link}}">
                                                <div class="breaker">
                                                    <div class="line">
                                                    </div>
                                                </div>
                                                <div class="buttonContent">
                                                    <div class="linkArrowContainer">
                                                        <div class="iconArrowRight">
                                                        </div>
                                                        <div class="iconArrowRightTwo">
                                                        </div>
                                                    </div>
                                                    <span>
                                                        {{$video->title}}
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="videoSlate">
                                            <video class="thevideo" loop="">
                                                <source src="{{url($video->size_240p)}}" type="video/ogg">
                                                    Your browser does not support the video tag.
                                                </source>
                                            </video>
                                        </div>
                                        <ul class="option-video">
                                            <li>
                                                <a class="black-tooltip" data-placement="top" data-toggle="tooltip" href="#" title="{{ trans('global.similar') }}">
                                                    <i class="fal fa-th-large">
                                                    </i>
                                                    {{ trans('global.similar') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a data-placement="top" data-toggle="tooltip" href="#" title="{{ trans('global.like') }}">
                                                    <i class="fal fa-heart">
                                                    </i>
                                                    {{ trans('global.like') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a data-placement="top" data-toggle="tooltip" href="#" title="{{ trans('global.addition') }}">
                                                    <i class="fal fa-plus-circle">
                                                    </i>
                                                    {{ trans('global.addition') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @endif
                                            @endforeach
                                        @endif
                            </div>
                            <!-- Image Flex -->
                        </div>
                    </div>
                </div>
                {{--
            </section>
        </div>
        --}}
    </div>
</hr>
@endif


@endsection
@section('javascript')
<script src="{{ asset('plugins/jquery.counterup/jquery.counterup.min.js') }}">
</script>
<script src="{{ asset('js/owl.carousel.js') }}">
</script>
<script src="{{ asset('plugins/jquery.counterup/waypoints.min.js') }}">
</script>
<script>
</script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#imagesFlex1').flexImages({ rowHeight: 220, maxRows: 8, truncate: true });
        $('#imagesFlex2').flexImages({ rowHeight: 220, maxRows: 8, truncate: true });

        $('.counter').counterUp({
            delay: 10, // the delay time in ms
            time: 1000 // the speed time in ms
        });
    });

     @if (session('success_verify'))
         swal({
             title: "{{ trans('misc.welcome') }}",
             text: "{{ trans('users.account_validated') }}",
             type: "success",
             confirmButtonText: "{{ trans('users.ok') }}"
         });
       @endif

       @if (session('error_verify'))
           swal({
               title: "{{ trans('misc.error_oops') }}",
               text: "{{ trans('users.code_not_valid') }}",
               type: "error",
               confirmButtonText: "{{ trans('users.ok') }}"
           });
       @endif

    $('.hovercard').hover(
     function () {
         $(this).find('.hover-content').fadeIn();
     },
     function () {
         $(this).find('.hover-content').fadeOut();
     }
 );

 var figure = $(".video11");
      var vid = figure.find("video");

      [].forEach.call(figure, function (item, index) {
        item.addEventListener('mouseover', hoverVideo.bind(item, index), false);
        item.addEventListener('mouseout', hideVideo.bind(item, index), false);
      });

      function hoverVideo(index, e) {
        vid[index].play();
      }

      function hideVideo(index, e) {
        vid[index].pause();
      }
</script>
@endsection
