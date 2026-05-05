@php($total=$ids->total())
@extends('app')
@section('title',__('Search by image'))
@section('description_custom',__('Search by image'))
@include('includes.searchbar')
@section('meta')
    <link rel="canonical" href="{{ route('ris',array_filter([$search_image->hash,'page'=>request('page')?:''])) }}"/>
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6"
    >
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ __('Search by image') }}</h1>
                        @if( $ids->count())
                            <p class="subtitle-site mt-4"> {{ trans('misc.Have_been_found') }} {{ trans_choice('misc.videos_plural',$total) }}</p>
                        @endif
                    </div>
                    @if( $total != 0 )
                        <div
                            class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
                            {!! $ids->links('pagination.search-pagination') !!}
                            <p class="ml-3 pb-2">{{ __('of :number',['number'=>$ids->lastPage()]) }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <!--  Demos -->
        </div>
    </div>
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-lg-2">
                @php($ext=pathinfo($search_image->path,PATHINFO_EXTENSION))
                <span>
                    @if(in_array($ext,['mp4','avi']))
                        <video src="{{ cdn($search_image->path) }}"></video>
                    @else
                    <img class="w-100"
                         src="{{ cdn($search_image->path) }}">
                    @endif
                    <div class="hover-overlay"></div>
                </span>
            </div>
        </div>
        <div class="mt-5 mb-5">
            @if( $total != 0 )
                <div id="videogrid_search" class="flex-images mb-5">
                    @include('video.includes.videos',['videos'=>$results])
                </div>
                @if( $total != 0 )
                    <div class="row mt-4">
                        <div class="col-12 col-md-7 d-flex justify-content-center justify-content-md-end">
                            <a href="javascript:;" data-page="{{ $ids->currentPage()+1 }}"
                               class="btn btn-primary next-page page-link next-btn" {!! $ids->currentPage()>=$ids->lastPage()?'style="display: none;"':'' !!}>{{ __('Next') }}</a>
                        </div>
                        <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                            <div class="search-pagination d-flex">
                                {!! $ids->links('pagination.search-pagination') !!}
                                <p class="ml-3 pt-2">{{ __('of :number',['number'=>$ids->lastPage()]) }}</p>
                            </div>
                        </div>
                    </div>
                @endif
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
@endsection
@push('javascript_navbar')
    <script>
    $('#videogrid_search').flexImages({object: '.arabs-video', rowHeight: 300, truncate: 1});

    </script>
    @include('includes.ajax_pagination',['results'=>$ids,'selector'=>'#videogrid_search'])
@endpush
