@extends('app')
@section('title'){{ $tag.' - ' }}@endsection
@section('meta')
    <link rel="canonical" href="{{ route('tags.show',$tag).(request('page')?'?page='.request('page'):'') }}"/>
@endsection
@section('content')
    @include('includes.searchbar')
    <div class="search-header jumbo-banner" data-overlay="6" style="">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ $tag }}</h1>
                        <p class="subtitle-site">{{trans('misc.tagged_vectors' )}}
                            ({{$results->count()?$results->total():0}})</p>
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
            <div id="imagesFlex" class="flex-images">
                @if( $results->count() )
                    @foreach( $results as $vector )
                        <div class="item card-photo" data-w="{{($vector->width_thumbnail??300)}}px"
                             data-h="{{($vector->height_thumbnail??300)}}px">
                            <div class="hover h-100 border-file">
                                <a href="{{ $vector->post_link??'javascript:;' }}">
                                    <picture>
                                        <source
                                            data-srcset="{{ cdn($vector->thumbnail) }}"/>
                                        <img
                                            data-src="{{ cdn($vector->thumbnail) }}"
                                            src="{{cdn($vector->thumbnail)}}"
                                            width="{{($vector->width_thumbnail??300)}}" height="{{($vector->height_thumbnail??300)}}"
                                            class="lazyload w-100 h-100"
                                            alt="{{ $vector->img_caption }}"/>
                                    </picture>
                                    <div class="hover-overlay"></div>
                                </a>
                                <div class="card-photo-content">
                                    <h3 class="card-photo-title">{{$vector->title}}</h3>
                                    <div class="icon">
                                        <div class="d-flex flex-row-reverse">
                                            <div class="icon_save">
                                                <span> <i data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}"
                                                          data-unlike="{{trans('misc.unlike')}}"
                                                          data-type="{{class_basename($vector)}}"
                                                          class="fal fa-heart @if($vector->is_like) active @endif likeButton"></i> </span>
                                                <span>{{__('misc.like')}}</span>
                                            </div>
                                            <div class="icon_save"
                                                 onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ $vector->thumbnail }}','{{$vector->title}}')">
                                                <span> <i class="fal fa-plus-circle"></i> </span>
                                                <span>{{__('misc.save_to_collection')}}</span>
                                            </div>
                                            <div class="icon-similar">
                                                <a href="{{route('similar.files',['type'=>'vectors','section'=>'illustration','id'=>$vector->id ])}}">
                                                    <span><i class="fal fa-th"></i></span>
                                                    <span>{{ __('misc.similar') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="btn-block text-center">
                        <i class="icon icon-Picture ico-no-result"></i>
                    </div>

                    <h3 class="margin-top-none text-center no-result no-result-mg">
                        {{ trans('misc.no_vectors_published') }}
                    </h3>
                @endif
            </div>
            @if( $results->count() )
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
        </div>
    </section>
@endsection
@push('javascript_navbar')
    @include('includes.ajax_pagination',['results'=>$results,'selector'=>'#imagesFlex'])
@endpush
