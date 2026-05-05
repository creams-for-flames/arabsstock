@extends('app')
@section('title'){{ $category->name.' - ' }}@endsection
@section('description_custom'){{ $category->name.' - ' }}
@endsection
@section('meta')
    <link rel="canonical" href="{{ $category->post_link.(request('page')?'?page='.request('page'):'') }}" />
@endsection
@section('content')
    @include('includes.searchbar')
    <div class="search-header jumbo-banner" data-overlay="6"
         style="background-image: url({{ str_replace('uploads/', 'uploads/', $category->cover) }});">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ $category->name }}</h1>
                        @if( $total != 0 )
                            <p>({{number_format($total)}}
                                ) {{trans_choice('misc.videos_available_category',$total )}}</p>
                        @endif
                    </div>
                    @if( $total != 0 )
                        <div
                            class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
                            {!! $videos['images']->links('pagination.search-pagination') !!}
                            <p class="ml-3 pb-2">{{ __('of :number',['number'=>$videos['images']->lastPage()]) }}</p>
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
                    @include('video.includes.videos',['videos'=>$videos['images']])
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
                        <a href="javascript:;" data-page="{{ $videos['images']->currentPage()+1 }}" data-lastpage="{{$videos['images']->lastPage()}}"
                           class="btn btn-primary next-page page-link next-btn @if($videos['images']->currentPage()>=$videos['images']->lastPage()) d-none @endif" >{{ __('Next') }}</a>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                        <div class="search-pagination d-flex">
                            {!! $videos['images']->links('pagination.search-pagination') !!}
                            <p class="ml-3 pt-2">{{ __('of :number',['number'=>$videos['images']->lastPage()]) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
@push('javascript_navbar')
    @include('includes.ajax_pagination',['results'=>$videos['images'],'selector'=>'#videogrid'])
@endpush
