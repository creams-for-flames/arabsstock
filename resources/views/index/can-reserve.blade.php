@extends('app') @section('title'){{ __('Exclusively Purchase Contents') }}@endsection
@section('content')

    @include('includes.searchbar')

    {{--  @include('includes.nav-pills') --}}
    <div class="category-header jumbo-banner" data-overlay="6" style="">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <h1>{{ __('Exclusively Purchase Contents') }}</h1>
                @if( $images->total() != 0 )
                    <p>({{number_format($images->total())}}
                        ) {{trans_choice('misc.images_available_category',$images->total() )}}</p>
                @endif
                <div class="breadcrumb-bar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">{{__('misc.home')}}</a></li>
                            <li class="breadcrumb-item active">
                                <a href="{{route('categories')}}">{{ trans('global.all_pictures') }}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--  Demos -->
        </div>
    </div>
    <div class="container-fluid">
        <div class="mt-5 mb-5">
            @if( $images->total() != 0 )

                <div id="imagesFlex" class="flex-images btn-block margin-bottom-40 dataResult">
                    @include('includes.images')
                </div>
                <!-- Image Flex -->

            @else
                <div class="btn-block text-center">
                    <i class="icon icon-Picture ico-no-result"></i>
                </div>

                <h3 class="margin-top-none text-center no-result no-result-mg">
                    {{ trans('misc.no_images_published') }}
                </h3>
            @endif
        </div>
        <!-- mt-5 mb-5 -->
    </div>
    <!-- container-fluid -->
@endsection
@push('javascript_navbar')
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
@endpush
