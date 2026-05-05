@php($total=$results->total())
@extends('app')
@section('title'){{ __('Same Group Search') }}@endsection
@section('description_custom'){{ __('Same Group') }}
@endsection
@include('includes.searchbar')
@section('meta')
    <link rel="canonical" href="{{ route('photo.samegroup',array_filter([$image->id,'page'=>request('page')?:''])) }}"/>
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
                                    <p class="subtitle-site mt-4"> {{ trans('misc.Have_been_found') }} {{ trans_choice('misc.images_plural',$total) }}</p>
                                @endif
                            </div>
                        </div>
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
        <div class="mt-3 mb-5">
            @if( $results->total() != 0 )
                <div id="imagesFlex" class="flex-images">
                    @foreach( $results as $r )
                        @include('images.item',['image'=>$r])
                    @endforeach
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
    @include('includes.ajax_pagination',['results'=>$results,'selector'=>'#imagesFlex'])
@endpush
