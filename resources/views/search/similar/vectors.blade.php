@php($total=$results->total())
@extends('app')
@section('title')
{{ __('misc.similar_vectors') }} -
{{ $file->title.' - '.' #'.$file->id.' - ' }}
@endsection
@section('description_custom')
{{ __('misc.similar_vectors') }} -
{{ $file->title.' - '.' #'.$file->id.' - ' }}
@endsection
@include('includes.searchbar')
@section('meta')
    <link rel="canonical" href="{{ route('similar.files',array_filter(['type'=>'vectors','section'=>$section,'id'=>$file->id,'page'=>request('page')?:''])) }}"/>
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6"
         >
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ __('misc.similar_vectors') }}</h1>
                        @if( $total != 0 )
                            <p class="subtitle-site mt-4"> {{ trans('misc.Have_been_found') }} {{ trans_choice('misc.vector_plural',$total) }}</p>
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
                <div id="imagesFlex" class="flex-images">
                    @foreach( $results as $item )

                        <div class="item card-photo" data-w="{{($item->width_thumbnail)}}px"
                             data-h="{{($item->height_thumbnail)}}px">
                            <div class="hover border-file h-100">
                                <a href="{{ $item->post_link }}">
                                    <img class="w-100 h-100" srcset="{{ cdn($item->thumbnail) }}"
                                         src="{{ cdn($item->thumbnail) }}" alt="{{$item->title}}"
                                        width="{{($item->width_thumbnail)}}"
                                        height="{{($item->height_thumbnail)}}"
                                         >
                                    <div class="hover-overlay"></div>
                                </a>
                                <div class="card-photo-content">
                                    <h3 class="card-photo-title">{{$item->title}}</h3>
                                    <div class="icon">
                                        <div class="d-flex flex-row-reverse">
                                            <div class="icon_save">
                                                <span> <i data-id="{{$item->id}}"
                                                          data-like="{{trans('misc.like')}}"
                                                          data-unlike="{{trans('misc.unlike')}}"
                                                          data-type="{{class_basename($item)}}"
                                                          class="fal fa-heart @if($item->is_like) active @endif likeButton"></i> </span>
                                                <span>{{__('misc.like')}}</span>
                                            </div>
                                            <div class="icon_save"
                                                 onclick="showModal('{{$item->id}}','{{class_basename($item)}}','{{ cdn($item->thumbnail) }}','{{$item->title}}')">
                                                <span> <i class="fal fa-plus-circle"></i> </span>
                                                <span>{{__('misc.save_to_collection')}}</span>
                                            </div>
                                            <div class="icon-similar">
                                                <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$item->id ])}}">
                                                   <span><i class="fal fa-th"></i></span>
                                                   <span>{{__('misc.similar')}}</span>
                                               </a>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
