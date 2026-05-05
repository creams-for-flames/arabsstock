@extends('app')
@section('title',__('Latest :type',['type'=>__('Videos')]))
@section('description_custom',__('Latest :type',['type'=>__('Videos')]))
@section('meta')
    <link rel="canonical" href="{{ route('video.latest',request()->only('page')) }}" />
@endsection
@section('content')
    @include('includes.searchbar')
    <div class="search-header jumbo-banner" data-overlay="6">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ __('Latest :type',['type'=>__('Videos')]) }}</h1>
                        @if( $total != 0 )
                            <p>({{number_format($total)}}
                                ) {{trans_choice('misc.videos_available_category',$total )}}</p>
                        @endif
                    </div>
                    @if( $total != 0 )
                        <div
                            class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
                            {!! $videos->links('pagination.search-pagination') !!}
                            <p class="ml-3 pb-2">{{ __('of :number',['number'=>$videos->lastPage()]) }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <!--  Demos -->
        </div>
    </div>
    <section class="pt-50 pb-50">
        <div class="container-fluid">
            <div id="videogrid" class="flex-images">
                @if( $total != 0 )
                    @include('video.includes.videos',['videos'=>$videos])
                @else
                    <div class="btn-block text-center">
                        <i class="icon icon-Picture ico-no-result"></i>
                    </div>

                    <h3 class="margin-top-none text-center no-result no-result-mg">
                        {{ trans('misc.no_videos_published') }}
                    </h3>
                @endif
            </div>
            @if( $total != 0 )
                <div class="row mt-5">
                    <div class="col-12 col-md-7 d-flex justify-content-center justify-content-md-end">
                        <a href="javascript:;" data-page="{{ $videos->currentPage()+1 }}" data-lastpage="{{$videos->lastPage()}}"
                           class="btn btn-primary next-page page-link next-btn @if($videos->currentPage()>=$videos->lastPage()) d-none @endif" >{{ __('Next') }}</a>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                        <div class="search-pagination d-flex">
                            {!! $videos->links('pagination.search-pagination') !!}
                            <p class="ml-3 pt-2">{{ __('of :number',['number'=>$videos->lastPage()]) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
@push('javascript_navbar')
    @include('includes.ajax_pagination',['results'=>$videos,'selector'=>'#videogrid'])
@endpush
