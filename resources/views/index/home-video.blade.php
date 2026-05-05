@extends('app')

@section('content')
<div class="hero-header jumbo-banner" data-overlay="6">
	<video class="slider-video" width="100%" preload="auto" loop="" autoplay="" style="visibility: visible; width: 100%;" poster="//dl.dropbox.com/s/pjopy0mu4klisat/working-with-espresso.jpg">
              <source src="//dl.dropbox.com/s/931244iox7i0fpk/working-with-espresso.mp4" type="video/mp4">
                <source src="//dl.dropbox.com/s/g3mo3w34pb8pp2l/working-with-espresso.webm" type="video/webm">
                  <source src="//dl.dropbox.com/s/p37f0avio0x6bs8/working-with-espresso.ogv" type="video/ogg">
            </video>
  <div class="container">
    <div class="col-12 col-sm-12 col-md-10 col-lg-8">
		@if(App::isLocale('en'))
      <h1>Find your next photo</h1>
      <p class="lead">{{$settings->welcome_subtitle_en}}</p>
      @else
      <h1>أكبر مكتبة صور عربية خليجية “حقيقية”</h1>
      <p class="lead">{{$settings->welcome_subtitle_ar}}</p>
      @endif
      <div class="search-big-form search-shadow">
        <div class="row m-0">
          <div class="col-12 col-sm-9 col-md-9 col-lg-9 p-0">
            <div class="form-group"> <i class="fal fa-search"></i>
              <input type="text" class="form-control" autocomplete="on" id="btnItems" placeholder="{{trans('misc.title_search_bar')}}">
            </div>
          </div>
          <div class="col-12 col-sm-3 col-md-3 col-lg-3 p-0">
            <button class="btn btn-main btn-flat" type="submit" id="btnSearch"> Find Photo <i class="fal fa-arrow-right"></i> </button>
          </div>
        </div>
      </div>
      <div class="featured-keyword">
        <ul>
          <li>{{ trans('global.the_most_searched') }}:</li>
          @foreach($search_word as $search_wordItem)
          <li> <a href="{{route('search',$search_wordItem->key_word)}}">{{$search_wordItem->key_word}} </a> , </li>
          @endforeach
        </ul>
      </div>
    </div>
    <!--  Demos -->
  </div>

</div>



{{--
    <div class="container margin-bottom-40">

        <div class="row margin-bottom-10">
            <div class="title-of-section">
                <div class="title-sub">
                    <h2>صور لمشروعك القادم </h2>
                    <p>بناءا على الأكثر بحثا </p>

                </div>

                <a href="#" class="more-a"> <i class="fal fa-ellipsis-h-alt"></i> <br> المزيد </a>
            </div>
        </div>
        <div class="row margin-bottom-20">

            @if( $images->total() != 0 )
                {{--<div class="col-md-12 btn-block margin-bottom-40">
                    <h1 class="btn-block text-center class-montserrat margin-bottom-zero none-overflow highlight-word-color">{{trans('misc.recent')}} <span class="color-default">{{Lang::choice('misc.images_plural',0)}}</span></h1>
                    <h5 class="btn-block text-center class-montserrat text-uppercase">{{trans('misc.title_2_index')}}</h5>
                </div>--}}


                <div id="imagesFlex" class="flex-images btn-block margin-bottom-40">
                    @include('includes.images')
                </div><!-- Image Flex -->

                <div class="col-md-12 text-center margin-bottom-20">
                    <a href="{{ route('latest') }}" class="btn btn-lg btn-main custom-rounded">
                        {{ trans('misc.view_all') }}
                    </a>
                </div>

            @else
                <div class="btn-block text-center">
                    <i class="icon icon-Picture ico-no-result"></i>
                </div>

                <h3 class="margin-top-none text-center no-result no-result-mg">
                    {{ trans('misc.no_images_published') }}
                </h3>
            @endif

        </div><!-- row -->

        <div class="row margin-bottom-10">
            <div class="title-of-section">

                <div class="title-sub">
                    <h2>المواضيع الأكثر رواجا </h2>
                    <p>بناءا على الأكثر بحثا </p>
                </div>

                <a href="#" class="more-a"> <i class="fal fa-ellipsis-h-alt"></i> <br> المزيد </a>
            </div>
        </div>
        <div class="row all-famus margin-bottom-20">
            @foreach(  App\Models\VideoCategory::where('mode','on')->orderBy('name_en')->take(9)->get() as $category )

                <div class="col-md-4">
                    <div class="more-famus">
                        <img src="{{ asset('img-category/'.$category->thumbnail) }}">
                        <h2><a href="{{ route('video.category.show',$category->slug) }}">{{ $category->name }} </a></h2>
                    </div>
                </div>
            @endforeach
            <div class="col-md-12 text-center margin-bottom-20">
                <a href="#" class="btn btn-lg btn-main custom-rounded">
                    كافة المواضيع
                </a>
            </div>
        </div>
        <div class="row margin-bottom-40">

            <div class="container">

                @if( isset( $settings->google_adsense ) && $settings->google_ads_index == 'on' && $settings->google_adsense_index != '' )
                    <div class="col-md-12 margin-top-40">
                        <?php echo html_entity_decode($settings->google_adsense_index); ?>
                    </div>
                @endif

            </div><!-- row -->
        </div>

    </div><!-- container wrap-ui -->

    <div class="section-tag">
        <div class="container margin-bottom-40">


            <!-- Col MD -->
            <div class="col-md-12 margin-top-20 margin-bottom-20">


                <div class="title-of-section tags-section-home">
                    <h2>{{__('misc.tags')}} </h2>
                    <p>{{__('misc.tagsDescription')}} </p>
                </div>


                @if($tag)
                    @foreach($tag as $tagItem)
                        <a href="{{ route('video.tags.show', $tagItem->tag ) }}" class="btn btn-danger tags font-default btn-sm">
                            {{$tagItem->tag}}
                        </a>
                    @endforeach
                @endif

            </div><!-- /COL MD -->
        </div>
    </div>

    <div class="jumbotron jumbotron-bottom margin-bottom-zero jumbotron-cover">
        <div class="container wrap-jumbotron position-relative">
            <h1 class="title-site">{{trans('misc.title_2_index')}}</h1>

        <!--<p class="subtitle-site"><strong>{{$settings->welcome_subtitle}}</strong></p>-->


            @if( Auth::check() || $settings->registration_active == 0	 )

                <div class="input-group input-group-lg searchBar">
                    <input type="text" class="form-control" autocomplete="off" name="q" id="btnItems_2" placeholder="{{trans('misc.title_search_bar')}}">
                    <span class="input-group-btn">
                        <button class="btn btn-main btn-flat" type="submit" id="btnSearch_2">
                            <i class="fal fa-search"></i>
                        </button>
                    </span>
                </div>

            @else
                <div class="btn-block text-center">
                    <a href="{{ url('register') }}" class="btn btn-lg btn-main custom-rounded">
                        {{ trans('misc.signup_free') }}
                    </a>
                </div>

            @endif


        </div><!-- container wrap-jumbotron -->
    </div><!-- jumbotron -->

    <div class="wrapper">
        <div class="container">
            <div class="row margin-bottom-40">
                <div class="col-md-12 btn-block margin-bottom-40">
                    <h1 class="btn-block text-center class-montserrat margin-bottom-zero none-overflow color-white">{{trans('misc.categories')}}</h1>
                <!--<h5 class="btn-block text-center class-montserrat text-uppercase color-gray">{{trans('misc.browse_by_category')}}</h5>-->
                </div>

                @foreach(  $categories->chunk(3) as $column )

                    <div class="col-md-3 col-sm-6 col-center">
                        <ul class="list-unstyled imagesCategory">
                            @foreach ($column as $category)

                                <li>
                                    <a class="link-category" href="{{ route('video.category.show',$category->slug) }}">@if(App::isLocale('en')) {{ $category->name_en }} ( {{  $category->images()->count() }} ) @else {{ $category->name_ar }} ( {{  $category->images()->count() }} ) @endif  </a>
                                </li>

                            @endforeach

                        </ul>
                    </div>
                @endforeach

                @if( $categories->total() > 11 )
                    <div class="col-md-12 text-center margin-top-40">
                        <a href="{{ route('video.categories') }}" class="btn btn-lg btn-main custom-rounded">
                            {{ trans('misc.view_all') }}
                            {{--<i style="display: none;" class="fa fa-long-arrow-right"></i>--}}
                        </a>
                    </div>
                @endif

            </div><!-- row -->
        </div><!-- container -->
    </div><!-- wrapper -->
@endsection

@section('javascript')

    <script src="{{ asset('plugins/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery.counterup/waypoints.min.js') }}"></script>

    <script type="text/javascript">

      $('#imagesFlex').flexImages({rowHeight: 220, maxRows: 8, truncate: true});


      jQuery(document).ready(function ($) {
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

    </script>

    <!--
              <script>
                $(document).ready(function() {
                  $('.owl-carousel').owlCarousel({
                    loop: true,
                    margin: 10,
                    responsiveClass: true,
                    responsive: {
                      0: {
                        items: 4,
                        nav: true
                      },
                      600: {
                        items: 4,
                        nav: false
                      },
                      1000: {
                        items: 8,
                        nav: true,
                        loop: false,
                        margin: 5
                      }
                    }
                  })
                })
              </script> -->

	<script src="{{ asset('plugins/jquery.counterup/jquery.counterup.min.js') }}"></script>
	<script src="{{ asset('plugins/jquery.counterup/waypoints.min.js') }}"></script>

		<script type="text/javascript">

		 $('#imagesFlex').flexImages({ rowHeight: 220, maxRows: 8, truncate: true });


		jQuery(document).ready(function( $ ) {
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

		</script>

<!--
          <script>
            $(document).ready(function() {
              $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 10,
                responsiveClass: true,
                responsive: {
                  0: {
                    items: 4,
                    nav: true
                  },
                  600: {
                    items: 4,
                    nav: false
                  },
                  1000: {
                    items: 8,
                    nav: true,
                    loop: false,
                    margin: 5
                  }
                }
              })
            })
          </script> -->

@endsection
--}}
