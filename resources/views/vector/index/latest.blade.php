@extends('app')
@section('title',__('Latest :type',['type'=>__('Vectors')]))
@section('description_custom',__('Latest :type',['type'=>__('Vectors')]))
@include('includes.searchbar')
@section('meta')
    <link rel="canonical" href="{{ route('latest',request()->only('page')) }}" />
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <h1>{{ __('Latest :type',['type'=>__('Vectors')]) }}</h1>
                        @if( $total != 0 )
                            <p class="subtitle-site mt-4"> ({{number_format($total)}}
                                ) {{trans_choice('misc.images_available_category',$total )}}</p>
                        @endif
                    </div>
                    @if( $total != 0 )
                        <div
                            class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
                            {!! $vectors->links('pagination.search-pagination') !!}
                            <p class="ml-3 pb-2">{{ __('of :number',['number'=>$vectors->lastPage()]) }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <!--  Demos -->
        </div>
    </div>
    <div class="container-fluid">
        <div class="mt-5 mb-5">
            @if( $vectors->total() != 0 )
                <div id="imagesFlex" class="flex-images">
                    @foreach( $vectors as $imageItem )

                        <div class="item card-photo" data-w="{{($imageItem->width_thumbnail)}}px"
                             data-h="{{($imageItem->height_thumbnail)}}px" data-reserved="{{ $imageItem->reserved?1:0 }}">
                            <div class="hover border-file h-100">
                                <a href="{{ $imageItem->post_link }}">
                                    <img class="w-100 h-100" srcset="{{ cdn($imageItem->thumbnail) }}"
                                         src="{{ cdn($imageItem->thumbnail) }}"
                                         width="{{($imageItem->width_thumbnail??300)}}" height="{{($imageItem->height_thumbnail??300)}}"
                                         alt="{{$imageItem->title}}">
                                    <div class="hover-overlay"></div>
                                </a>
                                <div class="card-photo-content">
                                    <h3 class="card-photo-title">{{$imageItem->title}}</h3>
                                    <div class="icon">
                                        <div class="d-flex flex-row-reverse">
                                            <div class="icon_save">
                                                <span> <i data-id="{{$imageItem->id}}"
                                                          data-like="{{trans('misc.like')}}"
                                                          data-unlike="{{trans('misc.unlike')}}"
                                                          data-type="{{class_basename($imageItem)}}"
                                                          class="fal fa-heart @if($imageItem->is_like) active @endif likeButton"></i> </span>
                                                <span>{{__('misc.like')}}</span>
                                            </div>
                                            <div class="icon_save"
                                                 onclick="showModal('{{$imageItem->id}}','{{class_basename($imageItem)}}','{{ cdn($imageItem->thumbnail) }}','{{$imageItem->title}}')">
                                                <span> <i class="fal fa-plus-circle"></i> </span>
                                                <span>{{__('misc.save_to_collection')}}</span>
                                            </div>
                                            <div class="icon-similar">
                                                <a href="{{route('similar.files',['type'=>'photos','section'=>'image','id'=>$imageItem->id ])}}">
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
                            <a href="javascript:;" data-page="{{ $vectors->currentPage()+1 }}" data-lastpage="{{$vectors->lastPage()}}"
                               class="btn btn-primary next-page page-link next-btn @if($vectors->currentPage()>=$vectors->lastPage()) d-none @endif" >{{ __('Next') }}</a>
                        </div>
                        <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                            <div class="search-pagination d-flex">
                                {!! $vectors->links('pagination.search-pagination') !!}
                                <p class="ml-3 pt-2">{{ __('of :number',['number'=>$vectors->lastPage()]) }}</p>
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
    @include('includes.ajax_pagination',['results'=>$vectors,'selector'=>'#imagesFlex'])
@endpush
