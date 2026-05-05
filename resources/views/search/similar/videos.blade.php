@php($total=$results->total())
@extends('app')
@section('title')
{{ __('misc.similar_videos') }} -
{{ $file->title.' - '.' #'.$file->id }}
@endsection
@section('description_custom')
{{ __('misc.similar_videos') }} -
{{ $file->title.' - '.' #'.$file->id }}
@endsection
@include('includes.searchbar')
@section('meta')
    <link rel="canonical" href="{{ route('similar.files',array_filter(['type'=>'videos','section'=>$section,'id'=>$file->id,'page'=>request('page')?:''])) }}"/>
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6"
    >
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ __('misc.similar_videos') }}</h1>
                        @if( $total != 0 )
                            <p class="subtitle-site mt-4"> {{ trans('misc.Have_been_found') }} {{ trans_choice('misc.videos_plural',$total) }}</p>
                        @endif
                    </div>
                    @if( $total != 0 )
                        <div
                            class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
                            {!! $results->links('pagination.search-pagination') !!}
                            <p class="ml-3 pb-2">{{ __('of :number',['number'=>$results->lastPage()]) }}</p>
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
                <a href="{{ $file->post_link }}">
                    <img class="w-100" srcset="{{ cdn($file->thumbnail) }}"
                         src="{{ cdn($file->thumbnail) }}" alt="{{$file->title}}">
                    <div class="hover-overlay"></div>
                </a>
            </div>
            <div class="col-lg-9">
                <div class="mt-4 pb-4">
                    <p class="text-muted published mb-0">
                        <span> {!! $stock_type !!} </span>

                    </p>
                    <h4><a href="{{ $file->post_link }}">{{ $file->title }}</a></h4>
                </div>
            </div>
        </div>
        <div class="mt-5 mb-5">
            @if( $results->total() != 0 )
            <div id="videogrid_search" class="flex-images">
                @include('video.includes.videos',['videos'=>$results])
            </div>
                @if( $total != 0 )
                    <div class="row mt-4">
                        <div class="col-12 col-md-7 d-flex justify-content-center justify-content-md-end">
                            <a href="javascript:;" data-page="{{ $results->currentPage()+1 }}" data-lastpage="{{$results->lastPage()}}"
                               class="btn btn-primary next-page page-link next-btn @if($results->currentPage()>=$results->lastPage()) d-none @endif" >{{ __('Next') }}</a>
                        </div>
                        <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                            <div class="search-pagination d-flex">
                                {!! $results->links('pagination.search-pagination') !!}
                                <p class="ml-3 pt-2">{{ __('of :number',['number'=>$results->lastPage()]) }}</p>
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
    @include('includes.ajax_pagination',['results'=>$results,'selector'=>'#videogrid_search'])
@endpush
