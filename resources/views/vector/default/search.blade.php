@extends('app')
@section('title')
    {{ $title }}
@endsection
@push('css')
<style>
    .as-mb-100{
        margin-bottom: 100px;
    }
    .as-m-48{
        margin: 48px auto;
    }
    .as-lh-30 {line-height: 30px}
    .as-clear{
        clear: both;
    }
</style>
@endpush
@include('includes.searchbar')
@section('content')
    @if( $total )
        <div class="search-header jumbo-banner" data-overlay="6">
            <div class="container-fluid">
                <div class="col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-md-8">
                            <h1 id="searchResultSentance">{{ trans('misc.result_of') }} "{{ $q }}"</h1>
                            <p class="subtitle-site mt-4"> {{ trans('misc.Have_been_found') }} {{ trans_choice('misc.vector_plural',$total) }}</p>
                        </div>
                        <div
                            class="d-none col-md-4 d-md-flex align-items-end justify-content-end pt-4 search-pagination">
                            {!! $images->links('pagination.search-pagination') !!}
                            <p class="ml-3 pb-2">{{ __('of :number',['number'=>$images->lastPage()]) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(isset($images)&& $images !=null&& $images->total() != 0 )
        <div class="container-fluid">
            <div class="mt-5 as-mb-100" >
                <div id="imagesFlex" class="flex-images mb-5">
                    @foreach( $images as $vector )
                        <div class="item card-photo" data-w="{{($vector->width_thumbnail)}}px"
                             data-h="{{($vector->height_thumbnail)}}px">
                            <div class="hover h-100 border-file">
                                <a href="{{ $vector->post_link }}">
                                    <img class="w-100 h-100" srcset="{{ cdn($vector->thumbnail) }}"
                                         src="{{ cdn($vector->thumbnail) }}" alt="{{$vector->title}}"
                                         width="{{(int)($vector->width_thumbnail)}}"
                                         height="{{(int)($vector->height_thumbnail)}}"
                                         >
                                    <div class="hover-overlay"></div>
                                </a>
                                <div class="card-photo-content">
                                    <h3 class="card-photo-title">{{$vector->title}}</h3>
                                    <div class="icon">
                                        <div class="d-flex flex-row-reverse">
                                            <div class="icon_save">
                                                <span>
                                                    <i data-id="{{$vector->id}}" data-like="{{trans('misc.like')}}"
                                                       data-unlike="{{trans('misc.unlike')}}"
                                                       data-type="{{class_basename($vector)}}"
                                                       class="fal fa-heart @if($vector->is_like) active @endif likeButton"></i>
                                                </span>
                                                <span>{{__('misc.like')}}</span>
                                            </div>
                                            <div class="icon_save"
                                                 onclick="showModal('{{$vector->id}}','{{class_basename($vector)}}','{{ $vector->thumbnail }}','{{$vector->title}}')">
                                                <span><i class="fal fa-plus-circle"></i></span>
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
                </div>
                <div class="row">
                    <div class="col-12 col-md-7 d-flex justify-content-center justify-content-md-end">
                        <a href="javascript:;" data-page="{{ $images->currentPage()+1 }}" data-lastpage="{{$images->lastPage()}}"
                           class="btn btn-primary next-page page-link next-btn @if($images->currentPage()>=$images->lastPage()) d-none  @endif" >{{ __('Next') }}</a>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                        <div class="search-pagination d-flex">
                            {!! $images->links('pagination.search-pagination') !!}
                            <p class="ml-3 pt-2">{{ __('of :number',['number'=>$images->lastPage()]) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="grid-container my-5">
            <div class="m-auto as-m-48">
                <h1 class="bold">{{ __('Sorry, we couldnt find any matches for') }} "{{ $word }}"</h1>
                <ul class="text-left fs-16 regular text-muted as-lh-30" >
                    @if(@$term_alternative)
                        <li>{{ __('global.did_you_mean') }} <a class="color-primary"
                                                               href="{{route('vectors.search',$term_alternative)}}">{{ $term_alternative }}</a>
                        </li>
                    @endif
                    <li>{{ __('Make sure the spelling is correct') }}</li>
                    <li>{{ __('Try using a simpler search') }}</li>
                    <li>{{ __('Still having problems?') }} <a href="{{ route('technical-support') }}"
                                                              class="color-primary">{{ __('global.contact_us') }}</a>
                    </li>
                </ul>
            </div>
        </div>
        @if (isset($categories) && count($categories))
        <div class="container-fluid">
            <!-- Col MD -->
            <div class="mt-5 mb-5">
                <section>
                    <div class="row justify-content-between">
                        <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                            <h1 class="text-capitalize color-primary">{{ trans('global.Most_popular_topics') }}</h1>
                            <p class="color-secondary">{{ trans('global.most_searched_Topics') }}</p>
                        </div>
                        <div class="d-none d-sm-block col-sm-1 col-md-1 col-lg-1 text-right">
                            <div class="mt-5 pt-3 mb-0"><a href="{{ route('categories') }}">
                                    <span class="d-block color-primary"><i class="fal fa-ellipsis-h-alt"></i></span>
                                    <span class="d-block color-secondary">{{ trans('global.more') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="center-row-section row">
                        <div class="col-12 col-sm-6 col-md-8 col-lg-8 pd-l-r--3">
                            <div class="card-category"
                                 style="background-image: url('{{ @$categories[0]->thumbnail }}')">
                                <a href="{{ route('vectors.category.show',$categories[0]->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                        <div class="card-category-content">
                                            <h3 class="card-category-title">{{ $categories[0]->name }}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                            <div class="card-category"
                                 style="background-image: url('{{ @$categories[1]->thumbnail }}')">
                                <a href="{{ route('vectors.category.show',$categories[1]->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                        <div class="card-category-content">
                                            <h3 class="card-category-title">{{ $categories[1]->name }}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="card-category"
                                 style="background-image: url('{{ @$categories[2]->thumbnail }}')">
                                <a href="{{ route('vectors.category.show',$categories[2]->slug) }}">
                                    <div class="hover">
                                        <div class="hover-overlay"></div>
                                        <div class="card-category-content">
                                            <h3 class="card-category-title">{{ $categories[2]->name }}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @foreach( $categories as $key => $category )
                            <?php if (in_array($key, [0, 1, 2])) continue; ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                                <div class="card-category"
                                     style="background-image: url('{{ @$category->thumbnail }}')">
                                    <a href="{{ route('vectors.category.show',$category->slug) }}">
                                        <div class="hover">
                                            <div class="hover-overlay"></div>
                                            <div class="card-category-content">
                                                <h3 class="card-category-title">{{ $category->name }}</h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        <div class="as-clear" >
                        </div>
                    </div>
                </section>
            </div>
        </div>
        @endif
        @if(count($tags)>0 && $tags[0]!="")
            <section class="trending-searches pt-50 pb-50 text-center">
                <div class="container-fluid">
                    <h1 class="text-capitalize color-primary mt-0">{{__('misc.Trending_Stock_Photo_Searches')}}</h1>
                    <p class="color-secondary mb-5 mt-0">  {{__('misc.tagsDescription')}}  </p>
                    @foreach($tags as $tag)
                        <a class="btn btn-outline-light rounded-pill"
                           href="{{ route('vectors.tags.show', preg_replace('/[[:space:]]+/', '-',($tag)) ) }} "> {{$tag}} </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
@endsection
@push('javascript_navbar')
    @include('includes.ajax_pagination',['results'=>$images,'selector'=>'#imagesFlex'])
@endpush
