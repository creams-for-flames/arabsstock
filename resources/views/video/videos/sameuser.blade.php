@php($total=$videos['images']->total())
@extends('app')
@section('title'){{ "{$user->name} ".__('Videos') }}@endsection
@section('description_custom'){{ "{$user->name} ".__('Videos') }}
@endsection
@include('includes.searchbar')
@section('meta')
    <link rel="canonical" href="{{ route('photo.sameuser',$video->id) }}"/>
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6"
    >
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex justify-content-right pt-4">
                            <div class="">
                                <img src="{{ $user->avatar }}" class="rounded-circle mb-3"
                                     style="width: 60px;"
                                     alt="Avatar"/>
                            </div>
                            <div class="px-2">
                                <h5 class="mb-0">
                                    <span class="bold text-white fs-22">{{ $user->name }}</span>
                                </h5>
                                <p class="text-light">{{ $user->email }} </p>
                                @if( $total != 0 )
                                    <p class="subtitle-site mt-4"> {{ trans('misc.Have_been_found') }} {{ trans_choice('misc.videos_plural',$total) }}</p>
                                @endif
                            </div>
                        </div>
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
    <div class="container-fluid">
        <section class="pt-50 pb-50">
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
                           class="btn btn-primary next-page page-link next-btn @if( $videos['images']->currentPage()>=$videos['images']->lastPage()) d-none @endif" >{{ __('Next') }}</a>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                        <div class="search-pagination d-flex">
                            {!! $videos['images']->links('pagination.search-pagination') !!}
                            <p class="ml-3 pt-2">{{ __('of :number',['number'=>$videos['images']->lastPage()]) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
@push('javascript_navbar')
    @include('includes.ajax_pagination',['results'=>$videos['images'],'selector'=>'#videogrid'])
@endpush
