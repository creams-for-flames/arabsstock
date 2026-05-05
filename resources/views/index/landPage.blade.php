@extends('app')
@push('header')
@endpush
@section('meta')
  <link rel="canonical" href="{{ url(app()->getLocale()) }}"/>
@endsection
@section('content')
  <div class="hero-header jumbo-banner landingpage landingpage-home h-50vh" data-overlay="5"
       style="background: url('{{ $header_national_day }}');background-position-x: 0;background-position-y: 0;background-size: auto;background-position: center;background-size: cover">
    <div class="container-fluid">
      <div class="row">
        <div class="d-none d-sm-none d-md-none d-lg-block col-lg-2"></div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-8">
          <h1 class="lead">{{ trans('global.welcome_subtitle') }}</h1>
          @include('includes.search_bar')
        </div>
        <div class="d-none d-sm-none d-md-none d-log-block col-lg-2"></div>
      </div>
    </div>
  </div>
  @include('index.landPage_national_day')
  {{--    @include('index.landPage_hajj')--}}
  <section class="pt-50">
    <div class="container-fluid">
      <div class="row justify-content-between">
        <div class="col-12 col-sm-11 col-md-11 col-lg-11">
          <h2 class="text-capitalize color-primary">{{ trans('global.Pictures_next_project') }}</h2>
          <p class="color-secondary">{{ __('The latest :count :type',['count'=>$images->total(),'type'=>__('Image')]) }}
            <a href="{{ route('latest') }}" class="d-block d-sm-none float-right">
              <span class="color-primary">{{ trans('global.more') }}</span>
            </a>
          </p>
        </div>
        <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
          <div class="mt-5 pt-3 mb-0"><a href="{{ route('latest') }}">
              <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
              <span class="d-block color-secondary">{{ trans('global.more') }}</span>
            </a></div>
        </div>
      </div>
      <div id="imageslandpage" class="flex-images">
        @foreach ($images->shuffle()->slice(0, 20) as $imageItem)
          @include('images.item',['image'=>$imageItem])
        @endforeach
        @foreach ($images as $imageItem)
            @include('images.item',['image'=>$imageItem])
        @endforeach
      </div>
    </div>
  </section>
  <section class="pt-50">
    <div class="container-fluid">
      <div class="row justify-content-between">
        <div class="col-12 col-sm-11 col-md-11 col-lg-11">
          <h2 class="text-capitalize color-primary">{{ trans('global.Video_next_project') }}</h2>
          <p class="color-secondary">{{ __('The latest :count :type',['count'=>$videos->total(),'type'=>__('Video')]) }}
            <a href="{{ route('video.latest') }}" class="d-block d-sm-none float-right">
              <span class="color-primary">{{ trans('global.more') }}</span>
            </a>
          </p>
        </div>
        <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
          <div class="mt-5 pt-3 mb-0">
            <a href="{{ route('video.latest') }}">
              <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
              <span class="d-block color-secondary">{{ trans('global.more') }}</span>
            </a>
          </div>
        </div>
      </div>
      <div id="videogridlandpage" class="flex-images">
        @if ($videos->count())
          @include('video.includes.videos',['videos'=>$videos->shuffle()->slice(0, 20)])
        @else
          <div class="btn-block text-center">
            <i class="icon icon-Picture ico-no-result"></i>
          </div>

          <h3 class="margin-top-none text-center no-result no-result-mg">
            {{ trans('misc.no_videos_published') }}
          </h3>
        @endif
      </div>
    </div>
  </section>
  <section class="pt-50">
    <div class="container-fluid">
      <div class="row justify-content-between">
        <div class="col-12 col-sm-11 col-md-11 col-lg-11">
          <h2 class="text-capitalize color-primary">{{ trans('global.vectors') }}
            {{ trans('global.next_project') }}</h2>
          <p class="color-secondary">{{ __('New this week') }}
            <a href="{{ route('vectors.categories') }}" class="d-block d-sm-none float-right">
              <span class="color-primary">{{ trans('global.more') }}</span>
            </a>
          </p>
        </div>
        <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
          <div class="mt-5 pt-3 mb-0"><a href="{{ route('vectors.categories') }}">
              <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
              <span class="d-block color-secondary">{{ trans('global.more') }}</span>
            </a></div>
        </div>
      </div>
      <div id="imageslandpageVector" class="flex-images">
        @foreach ($vectors as $vector)
          <div class="item card-photo" data-w="{{ $vector->width_thumbnail }}px"
               data-h="{{ $vector->height_thumbnail }}px">
            <div class="hover h-100 border-file">
              <a href="{{ $vector->post_link }}">
                <img class="w-100 h-100" srcset="{{ cdn($vector->thumbnail) }}"
                     src="{{ cdn($vector->thumbnail) }}" alt="{{ $vector->title }}"
                     width="{{(int)($vector->width_thumbnail)}}"
                     height="{{(int)($vector->height_thumbnail)}}"
                >
                <div class="hover-overlay"></div>
              </a>
              <div class="card-photo-content">
                <h3 class="card-photo-title">{{ $vector->title }}</h3>
                <div class="icon">
                  <div class="d-flex flex-row-reverse">
                    <div class="icon_save">
                                            <span>
                                            <i data-id="{{ $vector->id }}" data-like="{{ trans('misc.like') }}"
                                               data-unlike="{{ trans('misc.unlike') }}"
                                               data-type="{{class_basename($vector)}}"
                                               class="fal fa-heart @if ($vector->is_like) active @endif likeButton"></i>
                                            </span>
                      <span>{{ __('misc.like') }}</span>
                    </div>
                    <div class="icon_save"
                         onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ $vector->thumbnail }}','{{ $vector->title }}')">
                      <span><i class="fal fa-plus-circle"></i></span>
                      <span>{{ __('misc.save_to_collection') }}</span>
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
    </div>
  </section>
  @if (0 && isset($categoriesTrending) && $categoriesTrending != '' && count($categoriesTrending) > 0)
    <section class="pb-50 pt-50">
      <div class="container-fluid">
        <div class="row justify-content-between">
          <div class="col-12 col-sm-11 col-md-11 col-lg-11">
            <h2 class="text-capitalize color-primary">{{ trans('global.Trending Topics') }}</h2>
            <p class="color-secondary">{{ trans('global.Content you need at this time') }}
              <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
                <span class="color-primary">{{ trans('global.more') }}</span>
              </a>
            </p>
          </div>
          <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
            <div class="mt-5 pt-3 mb-0"><a href="{{ route('categories') }}">
                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
              </a></div>
          </div>
        </div>
        <div class="center-row-section row">
          @if ($categoriesTrending)
            @foreach ($categoriesTrending as $categoryTrendingItem)
              <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                <div class="card-category"
                     style="background-image: url('{{ $categoryTrendingItem->thumbnail != null ? cdn('uploads/img-category/' . $categoryTrendingItem->thumbnail) : asset('img-category/default2.jpg') }}')">
                  <a href="{{ route('category.show', $categoryTrendingItem->slug) }}">
                    <div class="hover">
                      <div class="hover-overlay"></div>
                      <div class="card-category-content">
                        <h3 class="card-category-title">{{ $categoryTrendingItem->name }}</h3>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </section>
  @endif
  <section class="pb-50 pt-50">
    <div class="container-fluid">
      <h2 class="text-capitalize color-primary">{{ trans('global.welcome_subtitle') }}</h2>
      <p class="color-secondary">{{ trans('global.Thousands-of-images-videos') }}</p>
      <div class="row">
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
          <div class="card-category mb-2 mt-2"
               style="background-image: url('{!! asset('img/home-cat-photo-2.webp') !!}'); padding-bottom: 60%;width: 100%;">
            <a href="{{ route('photos.home') }}">
              <div class="hover">
                <div class="hover-overlay"></div>
                <div class="card-category-content"></div>
              </div>
              <i class="fal fa-camera-alt icon-bottom-section"></i>
            </a>
          </div>
          <div class="row justify-content-between">
            <div class="col-3 col-sm-3 col-md-2 col-lg-2">
              <h3 class="mt-2 mb-0">{{ trans('global.Images') }} </h3>
            </div>
            <div class="col-9 col-sm-9 col-md-10 col-lg-10">
              <p class="text-right mt-3 mb-0 color-secondary">{{ trans('global.Thousands-of-images') }}
              </p>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
          <div class="card-category mb-2 mt-2"
               style="background-image: url('{!! asset('img/home-cat-video-2.webp') !!}'); padding-bottom: 60%;width: 100%;">
            <a href="{{ route('video.home') }}">
              <div class="hover">
                <div class="hover-overlay"></div>
                <div class="card-category-content"></div>
              </div>
              <i class="fal fa-video icon-bottom-section"></i>
            </a>
          </div>
          <div class="row justify-content-between">
            <div class="col-3 col-sm-3 col-md-2 col-lg-2">
              <h3 class="mt-2 mb-0">{{ trans('global.videos') }}</h3>
            </div>
            <div class="col-9 col-sm-9 col-md-10 col-lg-10">
              <p class="text-right mt-3 mb-0 color-secondary">{{ trans('global.Thousands-of-videos') }}
              </p>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
          <div class="card-category mb-2 mt-2"
               style="background-image: url('{!! asset('img/home-cat-vector-2.webp') !!}'); padding-bottom: 60%;width: 100%;">
            <a href="{{ route('vectors.home') }}">
              <div class="hover">
                <div class="hover-overlay"></div>
                <div class="card-category-content"></div>
              </div>
              <i class="fas fa-tilde fa-2x icon-bottom-section"></i>
            </a>
          </div>
          <div class="row justify-content-between">
            <div class="col-3 col-sm-3 col-md-2 col-lg-2">
              <h3 class="mt-2 mb-0">{{ trans('global.vectors') }} </h3>
            </div>
            <div class="col-9 col-sm-9 col-md-10 col-lg-10">
              <p class="text-right mt-3 mb-0 color-secondary">{{ trans('global.Thousands-of-vectors') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="pt-50">
    <div class="container-fluid">
      <div class="row justify-content-between">
        <div class="col-12 col-sm-11 col-md-11 col-lg-11">
          <h2 class="text-capitalize color-primary">{{ trans('global.Most_popular_topics') }}</h2>
          <p class="color-secondary">{{ trans('global.most_searched_Topics') }}
            <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
              <span class="color-primary">{{ trans('global.more') }}</span>
            </a>
          </p>
        </div>
        <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
          <div class="mt-5 pt-3 mb-0"><a href="{{ route('categories') }}">
              <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
              <span class="d-block color-secondary">{{ trans('global.more') }}</span>
            </a></div>
        </div>
      </div>
      <div class="center-row-section row">
        @foreach ($categories as $category)
          <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
            <div class="card-category"
                 style="background-image: url('{{ $category->thumbnail != null ? cdn('uploads/img-category/' . $category->thumbnail) : asset('img-category/default2.jpg') }}')">
              <a href="{{ route('category.show', $category->slug) }}">
                <div class="hover">
                  <div class="hover-overlay"></div>
                  <div class="card-category-content">
                    <h3 class="card-category-title">{{ $category->name }}</h3>
                  </div>
                </div>
              </a>
            </div>
          </div>
        @endforeach
        <div class="" style="clear: both">
        </div>
      </div>
    </div>
  </section>
  <section class="pt-50">
    <div class="container-fluid">
      <div class="row justify-content-between">
        <div class="col-12 col-sm-11 col-md-11 col-lg-11">
          <h2 class="text-capitalize color-primary">{{ trans('global.Cities_landmarks') }}</h2>
          <p class="color-secondary">{{ trans('global.More_than') }}
            <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
              <span class="color-primary">{{ trans('global.more') }}</span>
            </a>
          </p>
        </div>
        <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
          <div class="mt-5 pt-3 mb-0"><a href="{{ route('categories') }}">
              <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
              <span class="d-block color-secondary">{{ trans('global.more') }}</span>
            </a></div>
        </div>
      </div>
      <div class="center-row-section row">
        @if ($categoriesCities)
          @foreach ($categoriesCities as $categoryCitieItem)
            <div class="col-12 col-sm-6 col-md-3 col-lg-3 mb-2 pd-l-r--3">
              <div class="cities-category"
                   style="background-image: url('{{ $categoryCitieItem->thumbnail != null ? cdn('uploads/img-category/' . $categoryCitieItem->thumbnail) : asset('img-category/default2.jpg') }}')">
                <a href="{{ route('category.show', $categoryCitieItem->slug) }}">
                  <div class="hover">
                    <div class="hover-overlay"></div>
                  </div>
                </a>
              </div>
              <h4 class="mt-3 mb-4">{{ $categoryCitieItem->name }}</h4>
            </div>
          @endforeach
        @endif
      </div>
      <div class="" style="clear: both">
      </div>
  </section>
  @if ($categoriesPeople)
    <section class="pt-50 pb-50">
      <div class="container-fluid">
        <div class="row justify-content-between">
          <div class="col-12 col-sm-11 col-md-11 col-lg-11">
            <h2 class="text-capitalize color-primary">{{ trans('global.People') }}</h2>
            <p class="color-secondary">{{ trans('global.Portrait') }}
              <a href="{{ route('categories') }}" class="d-block d-sm-none float-right">
                <span class="color-primary">{{ trans('global.more') }}</span>
              </a>
            </p>
          </div>
          <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
            <div class="mt-5 pt-3 mb-0">
              <a href="{{ route('categories') }}">
                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
              </a>
            </div>
          </div>
        </div>
        <div class="center-row-section row">
          @if ($categoriesPeople)
            @foreach ($categoriesPeople as $categoryPeople)
              <div class="col-12 col-sm-6 col-md-3 col-lg-3 mb-2 pd-l-r--3">
                <div class="cities-category"
                     style="background-image: url('{{ $categoryPeople->thumbnail != null ? cdn('uploads/img-category/' . $categoryPeople->thumbnail) : asset('img-category/default2.jpg') }}')">
                  <a href="{{ route('category.show', $categoryPeople->slug) }}">
                    <div class="hover">
                      <div class="hover-overlay"></div>
                    </div>
                  </a>
                </div>
                <h4 class="mt-3 mb-4">{{ $categoryPeople->name }}</h4>
              </div>
            @endforeach
          @endif
        </div>
        <div class="" style="clear: both;"></div>
        <!-- container wrap-ui -->
      </div>
    </section>
  @endif
  @if (count($tag) > 0 && $tag[0] != '')
    <section class="trending-searches pt-50 pb-50 text-center">
      <div class="container-fluid">
        <h2 class="text-capitalize color-primary mt-0">{{ __('misc.Trending_Stock_Photo_Searches') }}</h2>
        <p class="color-secondary mb-5 mt-0"> {{ __('misc.tagsDescription') }} </p>
        @foreach ($tag as $key => $value)
          <a class="btn btn-outline-light rounded-pill"
             href="{{ route('tags.show', preg_replace('/[[:space:]]+/', '-', $value)) }} ">
            {{ $value }} </a>

        @endforeach
      </div>
    </section>
  @endif
  <section class="banner-footer">
    <div class="container">
      <h2 class="color-white mb-5">{{ trans('global.Thousands of digital content and themes, and more') }} </h2>
      <a href="{{ route('plans') }}" class="btn btn-primary btn-lg">
        {{ trans('global.Discover-packages-prices') }} </a>
    </div>
  </section>
  @include('includes.newsletter')
@endsection
@push('javascript_navbar')
@endpush
