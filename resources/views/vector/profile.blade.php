@extends('app')

@section('content')
    <div class="jumbotron index-header jumbotron_set jumbotron-cover @if( Auth::check() ) session-active-cover @endif">
        <div class="container wrap-jumbotron position-relative">
        <!--<h1 class="title-site vivify driveInTop delay-500" id="titleSite">{{$settings->welcome_text}}</h1>-->
            <img src="{{ asset('img/ARStock_dark.webp') }}" class="img-responsive image-slid" width="350">
            <p class="subtitle-site vivify fadeInBottom delay-600"><strong>{{$settings->welcome_subtitle}}</strong></p>
            <div class="input-group input-group-lg searchBar">
                <input type="text" class="form-control" autocomplete="off" id="btnItems" placeholder="{{trans('misc.title_search_bar')}}">
                <span class="input-group-btn">
                    <button class="btn btn-main btn-flat" type="submit" id="btnSearch">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </span>
            </div>
        </div><!-- container wrap-jumbotron -->
    </div>
    <!-- jumbotron -->


    @if(count($videos))
        <div class="container margin-bottom-20">

            <!-- Col MD -->
            <div class="col-md-12 margin-top-10 margin-bottom-10 padding-bottom-30 Recently-viewed">
                <h5 class="padding-bottom-10">{{trans('global.r_viewdVideo')}}</h5>
                <!--  Demos -->
                <section id="demos">
                    <div class="row row-owl">
                        <div class="large-12 columns">
                            <div class="" style="display: block;">
                                <div class="row all-famus more-downloading">
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
                                                                </div><!-- Modal header -->

                                                                <div class="modal-body listWrap">

                                                                    <div class="collectionsData">
                                                                        @if( $collections->count() != 0 )
                                                                            @foreach ( $collections as $collection )

                                                                                <?php

                                                                                $collectionImages = $collection->collection_videos->where('video_id',
                                                                                    $video->id)->where('collections_id',
                                                                                    $collection->id)->first();
                                                                                if (!empty($collectionImages)) {
                                                                                    $checked = 'checked="checked"';
                                                                                } else {
                                                                                    $checked = null;
                                                                                }
                                                                                ?>
                                                                                <div class="radio margin-bottom-15">
                                                                                    <label class="checkbox-inline padding-zero addVideoCollection text-overflow" data-image-id="{{$video->id}}" data-collection-id="{{$collection->id}}">
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
                                                                    <form method="POST" action="" enctype="multipart/form-data" id="addVideoCollectionForm">
                                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                        <input type="hidden" name="video_id" value="{{ $video->id }}">

                                                                        <!-- Start Form Group -->
                                                                        <div class="form-group">
                                                                            <label>{{ trans('global.title') }}</label>
                                                                            <input type="text" value="" name="title" id="titleCollection" class="form-control" placeholder="{{ trans('global.title') }}">
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
                                                                            <button type="submit" class="btn btn-sm btn-success" id="addVideoCollection">{{ trans('misc.create_collection') }}
                                                                                <i class="fa fa-plus"></i></button>
                                                                        </div>

                                                                    </form>

                                                                </div><!-- Modal body -->
                                                            </div><!-- Modal content -->
                                                        </div><!-- Modal dialog -->
                                                    </div><!-- Modal -->
                                                @endif



                                                <div id="video" class="col-md-4 video11">

                                                    <a href="{{$video->post_link}}" class="more-famus">
                                                        <video  class="H_j_h l_e_g" data-automation="VideoPlayer_video_video" loop="" muted="" playsinline="" poster="{{url($video->thumbnail)}}" style="transform: rotate(0deg); width: 100%;">
                                                            <source src="{{url($video->size_240p)}}" type="video/mp4">
                                                        </video>
                                                        <h2>{{$video->title}} </h2>
                                                        <h2 class="time-video" >{{$video->duration}} </h2>

                                                    </a>


                                                    <ul class="option-video">
                                                        <li>
                                                            <a href="@if(count($video->category)>0){{route('video.category.show',$video->category()->first()->slug)}}@endif" data-toggle="tooltip" data-placement="top" title="{{ trans('global.similar') }}" class="black-tooltip"><i class="fal fa-th-large"></i> {{ trans('global.similar') }}
                                                            </a></li>
                                                        <li>
                                                            <a data-id="{{$video->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}" class="likeVideoButton" href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('global.like') }}"><i class="fal fa-heart"></i> {{ trans('global.like') }}
                                                            </a></li>
                                                        <li>
                                                            <a class="btn-collection" data-toggle="modal" data-target="#collectionsVideo" href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('global.addition') }}"><i class="fal fa-plus-circle"></i>{{ trans('global.addition') }}
                                                            </a></li>
                                                    </ul>
                                                </div>



                                                <div style="display: none" id="videosList">


                                <div class="video">
                             <div class="videoListCopy">
                   <a href="{{$video->post_link}}" class="buttonMore">
                                                                <div class="breaker"><div class="line"></div></div>
                                                                <div class="buttonContent">
                                                                    <div class="linkArrowContainer">
                                                                        <div class="iconArrowRight"></div>
                                                                        <div class="iconArrowRightTwo"></div>
                                                                    </div>
                                                                    <span>{{$video->title}}</span>
                                                                </div>
                                                            </a>

                                                        </div>
                                                        <div class="videoSlate">
                                                            <video class="thevideo" loop>
                                                                <source src="{{url($video->size_240p)}}" type="video/ogg">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        </div>
                                                        <ul class="option-video">
                                                            <li><a href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('global.similar') }}" class="black-tooltip"><i class="fal fa-th-large"></i>{{ trans('global.similar') }} </a></li>
                                                            <li><a href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('global.like') }}"><i class="fal fa-heart"></i>{{ trans('global.like') }} </a></li>
                                                            <li><a href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('global.addition') }}"><i class="fal fa-plus-circle"></i>{{ trans('global.addition') }}</a></li>
                                                        </ul>
                                                    </div>





                                                </div>

                                            @endif
                                        @endforeach
                                    @endif
                                </div><!-- Image Flex -->
                            </div>


                        </div>
                    </div>
                    {{--</div>--}}
                </section>
            </div>
        </div>

    @endif


@endsection

@section('javascript')
    <script src="{{ asset('plugins/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.js') }}"></script>
    <script src="{{ asset('plugins/jquery.counterup/waypoints.min.js') }}"></script>
    <script>
    </script>
    <script type="text/javascript">


      jQuery(document).ready(function () {
        $('#imagesFlex1').flexImages({rowHeight: 220, maxRows: 8, truncate: true});
        $('#imagesFlex2').flexImages({rowHeight: 220, maxRows: 8, truncate: true});

        $('.counter').counterUp({
          delay: 10, // the delay time in ms
          time: 1000, // the speed time in ms
        });
      });

      @if (session('success_verify'))
      swal({
        title: "{{ trans('misc.welcome') }}",
        text: "{{ trans('users.account_validated') }}",
        type: "success",
        confirmButtonText: "{{ trans('users.ok') }}",
      });
      @endif

      @if (session('error_verify'))
      swal({
        title: "{{ trans('misc.error_oops') }}",
        text: "{{ trans('users.code_not_valid') }}",
        type: "error",
        confirmButtonText: "{{ trans('users.ok') }}",
      });
      @endif

      $('.hovercard').hover(
        function () {
          $(this).find('.hover-content').fadeIn();
        },
        function () {
          $(this).find('.hover-content').fadeOut();
        },
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
