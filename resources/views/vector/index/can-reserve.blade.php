@extends('app') @section('title'){{ __('Exclusively Purchase Contents') }}@endsection
@section('content')
    @include('includes.searchbar')
    <div class="category-header jumbo-banner" data-overlay="6" style="">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <h1>{{ __('Exclusively Purchase Contents') }}</h1>
                @if( $vectors->total() != 0 )
                    <p>({{number_format($vectors->total())}}
                        ) {{trans_choice('misc.vectors_available_category',$vectors->total() )}}</p>
                @endif
                <div class="breadcrumb-bar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('landPage') }}">{{__('misc.home')}}</a></li>
                            <li class="breadcrumb-item active">
                                <a href="{{route('categories')}}">{{ trans('global.all_vectors') }}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="mt-5 mb-5">
            @if( $vectors->total() != 0 )
                <div id="imagesFlex" class="flex-images">
                    @foreach( $vectors as $vector )
                        <div class="item card-photo" data-w="{{($vector->width_thumbnail??300)}}px"
                             data-h="{{($vector->height_thumbnail??300)}}px">
                            <div class="hover h-100 border-file">
                                <a href="{{ $vector->post_link??'javascript:;' }}">
                                    <img class="w-100 h-100" srcset="{{ cdn($vector->thumbnail) }}"
                                         src="{{ cdn($vector->thumbnail) }}"
                                         width="{{($vector->width_thumbnail??300)}}" height="{{($vector->height_thumbnail??300)}}"
                                         alt="{{$vector->title}}"
                                         >
                                    <div class="hover-overlay"></div>
                                </a>
                                <div class="card-photo-content">
                                    <h3 class="card-photo-title">{{$vector->title}}</h3>
                                    <div class="icon">
                                        <div class="d-flex flex-row-reverse">
                                            <div class="icon_save">
                                                <span> <i data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}"
                                                          data-unlike="{{trans('misc.unlike')}}"
                                                          data-type="{{class_basename($vector)}}"
                                                          class="fal fa-heart @if($vector->is_like) active @endif likeButton"></i> </span>
                                                <span>{{__('misc.like')}}</span>
                                            </div>
                                            <div class="icon_save"
                                                 onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ $vector->thumbnail }}','{{$vector->title}}')">
                                                <span> <i class="fal fa-plus-circle"></i> </span>
                                                <span>{{__('misc.save_to_collection')}}</span>
                                            </div>
                                            <div class="icon-similar">
                                                <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$vector->id ])}}">
                                                    <span><i class="fal fa-th"></i></span>
                                                    <span>{{ __('misc.similar') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="btn-block text-center">
                    <i class="icon icon-Picture ico-no-result"></i>
                </div>

                <h3 class="margin-top-none text-center no-result no-result-mg">
                    {{ trans('misc.no_vectors_published') }}
                </h3>
            @endif
        </div>
    </div>
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
