@extends('app')
@section('title',$tag.' - ')
@section('meta')
    <link rel="canonical" href="{{ route('video.tags.show',$tag).(request('page')?'?page='.request('page'):'') }}"/>
@endsection
@section('content')
    @include('includes.searchbar')
    <div class="search-header jumbo-banner" data-overlay="6" style="">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ $tag }}</h1>
                        @if( $results->count() )
                            <p class="subtitle-site">{{trans('misc.tagged_videos' )}}
                                ({{$results->count()?$results->total():0}})</p>
                        @endif
                    </div>
                    @if( $results->count() )
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
    <section class="pt-50 pb-50">
        <div class="container-fluid">
            <div id="videogrid" class="flex-images">
                @if( $results->count())
                    @include('video.includes.videos',['videos'=>$results])
                @else
                    <div class="btn-block text-center">
                        <i class="icon icon-Picture ico-no-result"></i>
                    </div>
                    <h3 class="margin-top-none text-center no-result no-result-mg">
                        {{ trans('misc.no_videos_published') }}
                    </h3>
                @endif
            </div>
            @if( $results->count())
                <div class="row mt-5">
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
        </div>
    </section>
@endsection
@push('javascript_navbar')
    @include('includes.ajax_pagination',['results'=>$results,'selector'=>'#videogrid'])
@endpush
