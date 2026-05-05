@extends('app')
@section('title',$tag.' - ')
@include('includes.searchbar')
@section('meta')
    <link rel="canonical" href="{{ route('tags.show',$tag).(request('page')?'?page='.request('page'):'') }}"/>
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6" style="">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ $tag }}</h1>
                        <p class="subtitle-site">{{trans('misc.tagged_images' )}}
                            ({{$results->count()?$results->total():0}})</p>
                    </div>
                    @if( $results->count() && $results->total())
                        <div
                            class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
                            {!! $results->links('pagination.search-pagination') !!}
                            <p class="ml-3 pb-2">{{ __('of :number',['number'=>$results->lastPage()]) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Col MD -->
        <div class="mt-5 mb-5">
            @if($results->count())
                <div id="imagesFlex" class="flex-images">
                    @include('includes.images',['images'=>$results])
                </div>
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
            @else
                <div class="btn-block text-center pt-5">
                    <i class="fal fa-exclamation-circle"></i>
                </div>
                <h3 class="btn-block text-center no-result no-result-mg">
                    {{ trans('misc.no_results_found') }}
                </h3>
            @endif
        </div>
        <!-- /COL MD -->
    </div>
@endsection
@push('javascript_navbar')
    @include('includes.ajax_pagination',['results'=>$results,'selector'=>'#imagesFlex'])
@endpush
