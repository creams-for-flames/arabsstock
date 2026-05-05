@extends('app')

@section('title'){{ __('global.myImages').' - ' }}@endsection

@push('css')

@endpush
@section('content')
    <style>
        .history .title-services > a {
            float: right;
            font-size: 14px;
            color: #111;
        }

        .history .title-services > span {
            float: left;
            font-size: 12px;
            color: #aaa;
        }

        h1.download-btn-id {
            text-align: center;
            display: none;
        }

        .img-downloaded-past:hover h1.download-btn-id {
            display: block;
            margin: -.65em 0;
        }

        .img-downloaded-past:hover .title-services {
            display: none;
        }

        .img-downloaded-past {
            max-height: 240px;
            height: 240px;
        }
    </style>
    <div class="jumbotron md index-header jumbotron_set jumbotron-cover">
        <div class="container wrap-jumbotron position-relative">
            <h1 class="title-site title-sm">{{__('global.myImages')}}</h1>
            <p class="subtitle-site"><strong>{{__('global.myDownloadImages')}} ({{count($images)}})</strong></p>
        </div>
    </div>
    <div class="container margin-bottom-40">

        <!-- Col MD -->
        <div class="col-md-12 margin-top-20 margin-bottom-20 history">

            @foreach($images as $imagesItem)


                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 img-downloaded-past row-margin-20">
                    <a href="{{$imagesItem->post_link}}">
                        <img class="img-responsive btn-block custom-rounded" src="{{ url($imagesItem->thumbnail) }}" alt="Business / Finance">
                    </a>
                    <h1 class="download-btn-id"><a href="{{ $imagesItem->post_link }}" class="btn btn-md btn-main custom-rounded" style="
"><i class="fa fa-cloud-download"> </i> {{__('global.download')}}</a></h1>

                    <h1 class="title-services">
                        <a href="{{ $imagesItem->post_link }}">{{$imagesItem->images_id}}</a>
                        <span>{{$imagesItem->date}}</span>
                    </h1>
                </div>
            @endforeach
        </div><!-- /COL MD -->

    </div>

@endsection

@section('javascript')




@endsection
