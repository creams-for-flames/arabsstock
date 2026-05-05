@extends('app')
@section('title')
    {{ trans('misc.categories').' - ' }}
@endsection


@section('description_custom'){{  trans('misc.categories').' - '}}
@endsection
@section('content')

    @include('includes.searchbar')

    <div class="category-header jumbo-banner" data-overlay="6" style="background-image: url('{!! url('/img/bg-footer-vector.webp') !!}');">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <h1 class="title-site title-sm">{{ trans('misc.categories') }}</h1>
                <p class="subtitle-site"><strong>{{trans('misc.browse_vectors_by_category')}}</strong></p>
            </div>
        </div>git branch -d AS-138
    </div>

    <section class="pt-50 pb-50">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                    <h1 class="text-capitalize color-primary">{{ trans('global.Main-topics') }}</h1>
                    <p class="color-secondary">{{ trans('global.More_than_topics') }}</p>
                </div>
                <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                    <div class="mt-5 pt-3 mb-0">
                        <a href="{{route('vectors.categories')}}">
                            <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                            <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="center-row-section row">
                @foreach( $data as $category )
                    @if($category->people==0 && $category->cities_and_landmarks==='off' )
                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                            <div class="card-category" style="background-image: url('{{ $category->thumbnail }}')">
                                <a href="{{ route('vectors.category.show',$category->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                        <div class="card-category-content">
                                            <h3 class="card-category-title">
                                                @if(App::isLocale('en')) {{ $category->name_en }} @else {{ $category->name_ar }} @endif ({{$category->videos()->count()}})
                                            </h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <!-- col-md-3 row-margin-20 -->
                    @endif
                @endforeach
            </div>
        </div>
    </section>


    @if($categoriesCities)
        <section class="pt-50 pb-50">
            <div class="container-fluid">
                <div class="row justify-content-between">
                    <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                        <h1 class="text-capitalize color-primary">{{ trans('global.Cities_landmarks') }}</h1>
                        <p class="color-secondary">{{ trans('global.More_than') }}</p>
                    </div>
                    <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                        <div class="mt-5 pt-3 mb-0 text-center">
                            <a href="{{route('vectors.categories')}}">
                                <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                                <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="center-row-section row">
                    @foreach($data as $categoryCity )

                        @if($categoryCity->cities_and_landmarks==='on')
                            <div class="col-12 col-sm-6 col-md-3 col-lg-3 mb-2 pd-l-r--3">
                                <div class="cities-category" style="background-image: url('{{ url($categoryCity->thumbnail)}}')">
                                    <a href="{{ route('vectors.category.show',$categoryCity->slug) }}">
                                        <div class="hover">
                                            <div class="hover-overlay"></div>
                                        </div>
                                    </a>
                                </div>
                                <h4 class="mt-3 mb-4">{{ $categoryCity->name }}</h4>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="" style="clear: both;"></div>
                <!-- container wrap-ui -->
            </div>
        </section>
    @endif


@endsection
