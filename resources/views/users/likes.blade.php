<?php
$user = Auth::user();
?>
@extends('includes.profile') @section('profile_content') @section('title'){{ $title }}@endsection

<div class="container collections">
    <h3 class="pb-3">{{ trans('users.Archives') }}</h3>
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#images"><i
                    class="fal fa-camera-alt"></i> {{ trans('misc.images') }} ({{number_format($images->total())}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#videos"><i class="fal fa-video"></i> {{ trans('misc.videos') }}
                ({{number_format($videos->total())}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#vectors"><i
                    class="fal fa-tilde fa-lg"></i> {{ trans('misc.vectors') }} ({{number_format($vectors->total())}}
                )</a>
        </li>
    </ul>
    <hr class="mt-0"/>
    <div class="tab-content">
        <div id="images" class="tab-pane fade in show active">
            <div>
                @if( $images->total() != 0 )

                    <div id="imagesFlex" class="flex-images btn-block dataResult">
                        @include('includes.images') @if( $images->count() != 0 )
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

                    <h3 class=" btn-block text-center no-result no-result-mg">
                        {{ trans('misc.no_results_found') }}
                    </h3>
                @endif
            </div>
        </div>
        <div id="videos" class="tab-pane fade ">
            <div>
                @if( $videos->total() != 0 )

                    <div id="videogrid" class="flex-images">
                        @include('video.includes.videos') @if( $videos->count() != 0 )
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

                    <h3 class=" btn-block text-center no-result no-result-mg">
                        {{ trans('misc.no_results_found') }}
                    </h3>

                @endif
            </div>
        </div>
        <div id="vectors" class="tab-pane fade ">
            <div>
                @if( $vectors->total() != 0 )

                    <div id="imagesFlexVector" class="flex-images btn-block dataResult">
                        @include('vector.includes.vectors')
                        @if( $vectors->count() != 0 )
                            <div class="container-paginator">
                                {{ $vectors->links() }}
                            </div>
                        @endif
                    </div>
                    <!-- Image Flex -->

                @else
                    <div class="btn-block text-center pt-5">
                        <i class="fal fa-exclamation-circle"></i>
                    </div>

                    <h3 class=" btn-block text-center no-result no-result-mg">
                        {{ trans('misc.no_results_found') }}
                    </h3>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')

    <script type="text/javascript">
        $('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('#videogrid').flexImages({object: '.arabs-video', rowHeight: 300});
            $("#imagesFlexVector").flexImages({rowHeight: 200});
        })
        $("#imagesFlex").flexImages({rowHeight: 200});
        
    </script>

@endsection
