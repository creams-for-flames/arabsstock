@php($total=$images->total())
@extends('app')
@section('title')
  {{ $category->name.' - ' }}
@endsection
@section('description_custom')
  {{ $category->name.' - ' }}
@endsection
@include('includes.searchbar')
@section('meta')
  <link rel="canonical" href="{{ $category->post_link.(request('page')?'?page='.request('page'):'') }}"/>
@endsection
@section('content')
  <div class="search-header jumbo-banner" data-overlay="6"
       style="background-image: url({{ str_replace('uploads/', 'uploads/', $category->cover) }});">
    <div class="container-fluid">
      <div class="col-lg-12 col-md-12">
        <div class="row">
          <div class="col-md-8">
            <h1>{{ $category->name }}</h1>
            @if( $total != 0 )
              <p class="subtitle-site mt-4"> ({{number_format($total)}}
                ) {{trans_choice('misc.images_available_category',$total )}}</p>
            @endif
          </div>
          @if( $total != 0 )
            <div
                    class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
              {!! $images->links('pagination.search-pagination') !!}
              <p class="ml-3 pb-2">{{ __('of :number',['number'=>$images->lastPage()]) }}</p>
            </div>
          @endif
        </div>
      </div>
      <!--  Demos -->
    </div>
  </div>
  <div class="container-fluid">
    <div class="mt-5 mb-5">
      @if( $images->total() != 0 )
        <div id="imagesFlex" class="flex-images">
          @foreach( $images as $imageItem )
            @include('images.item',['image'=>$imageItem])
          @endforeach
        </div>
        @if( $total != 0 )
          <div class="row mt-4">
            <div class="col-12 col-md-7 d-flex justify-content-center justify-content-md-end">
              <a href="javascript:;" data-page="{{ $images->currentPage()+1 }}" data-lastpage="{{$images->lastPage()}}"
                 class="btn btn-primary next-page page-link next-btn @if ($images->currentPage()>=$images->lastPage()) d-none @endif">{{ __('Next') }}</a>
            </div>
            <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
              <div class="search-pagination d-flex">
                {!! $images->links('pagination.search-pagination') !!}
                <p class="ml-3 pt-2">{{ __('of :number',['number'=>$images->lastPage()]) }}</p>
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
  @include('includes.ajax_pagination',['results'=>$images,'selector'=>'#imagesFlex'])
@endpush
