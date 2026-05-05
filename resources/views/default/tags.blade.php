@extends('app')
@section('title'){{ trans('misc.tags').' - ' }}@endsection
@include('includes.searchbar')
@section('content')
    <div class="jumbotron md index-header jumbotron_set jumbotron-cover">
        <div class="container wrap-jumbotron position-relative">
            <h1 class="title-site title-sm">{{ trans('misc.tags') }}</h1>
            <p class="subtitle-site"><strong>{{$settings->title}}</strong></p>
        </div>
    </div>

    <div class="container margin-bottom-40">
        <!-- Col MD -->
        <div class="col-md-12 margin-top-20 margin-bottom-20 tags-page">
            @foreach( $results as $query )
                <a href="{{ route('tags', $query->slug ) }}" class="btn btn-danger tags font-default btn-sm">
                    {{ucfirst($query->title)}}
                    ( {{$query->count_image}} )
                </a>

            @endforeach
            @if( $results == ''  )
                <div class="btn-block text-center">
                    <i class="icon icon-Tag ico-no-result"></i>
                </div>

                <h3 class="margin-top-none text-center no-result no-result-mg">
                    {{ trans('misc.no_results_found') }}
                </h3>
            @endif
        </div><!-- /COL MD -->
    </div><!-- container wrap-ui -->

@endsection

