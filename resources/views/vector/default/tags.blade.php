@extends('app')

@section('title'){{ trans('misc.tags').' - ' }}@endsection

@include('includes.searchbar')
@section('content')
<div class="search-header jumbo-banner" data-overlay="6" style="">
    <div class="container-fluid">
        <div class="col-lg-12 col-md-12">
            <h1>{{ trans('misc.tags') }}</h1>

            <p class="subtitle-site">{{trans('misc.tagged_videos' )}}</p>
        </div>
    </div>
</div>


<div class="container-fluid">

    <div class="mt-5 mb-5">
    @if(isset($videos)&& $videos !=null&& $videos->total() != 0 )
        <div id="videogrid" class="flex-images">
            @include('video.includes.videos')
        </div>
        <div class="col text-center load7">
            <div class="more-spinner" style="display: none;">Loading...</div>
        </div>
        <div class="col text-center" hidden id="finish">
            <p class="mt-5">{{__('global.finish content')}}</p>
        </div>
            @else
            <div class="btn-block text-center pt-5">
            <i class="fal fa-exclamation-circle"></i>
        </div>

        <h3 class="btn-block text-center no-result no-result-mg">
            {{ trans('misc.no_results_found') }}
        </h3>
            @endif
        </div>

    </div>
<!-- Col MD -->
<!-- <div class="col-md-12 margin-top-20 margin-bottom-20">

   //  <?php


	  //  $_tags = strtolower( $data{0}->tags );

	  //  $tags = array_unique( explode(',', $string) );

	///	sort($tags);

		// ?>

		@foreach( $data as $query )


			<a href="{{ route('video.tags', $query->tag ) }}" class="btn btn-danger tags font-default btn-sm">
					{{ucfirst($query->tag)}}  ( {{$query->count_video}} )
				</a>

    @endforeach

    @if( $data == ''  )
    	<div class="btn-block text-center">
	    			<i class="icon icon-Tag ico-no-result"></i>
	    		</div>

	    		<h3 class="margin-top-none text-center no-result no-result-mg">
	    		{{ trans('misc.no_results_found') }}
	    	</h3>
	    	@endif

 </div>

 </div>-->

@endsection

@section('javascript')
<script>
    var page = 2;
    var is_fetching = false;
    window.addEventListener("scroll", throttle(handleScroll, 500));
    // set masonary columns count
    function throttle(func, timeFrame) {
        var lastTime = 0;
        return function () {
            var now = new Date();
            if (now - lastTime >= timeFrame) {
                func();
                lastTime = now;
            }
        };
    }
    function handleScroll() {
        if (bottomVisible() === true) {
            loadMoreData();
        }
    }
    function bottomVisible() {
        const panel = document.querySelector("body");
        const scrollY = window.pageYOffset;
        const visible = window.screen.height;
        const pageHeight = panel.scrollHeight;
        if (visible + 1 > panel.scrollHeight) {
            return false;
        }
        var offset = 1600;
        var bottomOfPage = 1600 + visible + scrollY >= pageHeight;
        return bottomOfPage || pageHeight < visible;
    }
    function loadMoreData() {
        if (!page) {
            return false;
        }
        if (is_fetching) {
            return false;
        }
        $.ajax({
            url: "?page=" + page,
            type: "get",
            beforeSend: function () {
                $(".more-spinner").show();
            },
        })
            .done(function (data) {
                if (data.html === "") {
                    page = false;
                } else {
                    page++;
                }
                is_fetching = false;
                $(".more-spinner").hide();
                $("#imagesFlex").append(data.html);
                if(data.html==""){
                    $("#finish").removeAttr("hidden")
                }
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                return false;
            });
    }
    // if user refresh and he is bottom .. scroll to top
    var _scrollHeight = document.querySelector("body").scrollHeight;
    setTimeout(function () {
        if (_scrollHeight - window.pageYOffset < 300) {
            window.scrollTo(0, 0);
        }
    }, 2000);
</script>
@endsection
