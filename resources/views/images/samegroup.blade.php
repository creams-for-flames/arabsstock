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
                        <h1>{{ __('Same Group') }}</h1>
                        @if( $total != 0 )
                            <p class="subtitle-site mt-4"> {{ trans('misc.Have_been_found') }} {{ trans_choice('misc.images_plural',$total) }}</p>
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
                <a href="{{ $image->post_link }}">
                    <img class="w-100" srcset="{{ cdn($image->thumbnail) }}"
                         src="{{ cdn($image->thumbnail) }}" alt="{{$image->title}}">
                    <div class="hover-overlay"></div>
                </a>
            </div>
            <div class="col-lg-9">
                <div class="mt-4 pb-4">
                    <p class="text-muted published mb-0">
                        <span> {!! trans('global.Stock_Id_Image',['id' => $image->id])!!} </span>
                    </p>
                    <h4><a href="{{ $image->post_link }}">{{ $image->title }}</a></h4>
                </div>
            </div>
        </div>
        <div class="mt-5 mb-5">
            @if( $results->total() != 0 )
                <div id="imagesFlex" class="flex-images">
                    @foreach( $results as $r )
                        @include('images.item',['image'=>$r])
                    @endforeach
                </div>
                @if( $total != 0 )
                    <div class="row mt-4">
                        <div class="col-12 col-md-7 d-flex justify-content-center justify-content-md-end">
                            <a href="javascript:;" data-page="{{ $results->currentPage()+1 }}"
                               class="btn btn-primary next-page page-link next-btn" {!! $results->currentPage()>=$results->lastPage()?'style="display: none;"':'' !!}>{{ __('Next') }}</a>
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
