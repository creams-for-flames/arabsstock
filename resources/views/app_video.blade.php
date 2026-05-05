<?php

$body_class = "home";
if (isset($_SERVER['REQUEST_URI'])) {
    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);

    $body_class = isset($uri_segments[1]) && $uri_segments[1] ? $uri_segments[1] : '';
}
if(request()->route()->getName() == 'video.home')
$body_class = 'home';


?>
        <!DOCTYPE html>
<html lang="{{strtolower(config('app.locale'))}}">
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TSZSL8L');</script>
<!-- End Google Tag Manager -->
    @include('includes.cj')
    <meta charset="utf-8">
    <title>@section('title')@show @if( isset( $settings->title ) ){{$settings->title}}@endif</title>

    <meta name="p:domain_verify" content="31225e88f696a236711f19d8a1ed31e1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description_custom'){!! $settings->description  !!} ">
    @if( request()->route()->getName()!='photo.show' && request()->route()->getName()!='video.show')
        <meta name="keywords" content="@yield('keywords_custom'){{ $settings->keywords }}"/>
    @endif
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}"/>
    <link rel="alternate" hreflang="{{config('app.locale') === 'ar' ? 'ar': 'en'}}" href="{{ url('lang', App\Models\Languages::where('abbreviation', '!=', config('app.locale') )->value('abbreviation')) }}" />

    @include('includes.css_general')
    @yield('meta')

</head>
<body class="{{$body_class}}">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TSZSL8L"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- <div class="loader"></div> -->
@include('includes.navbar')
@stack('ld_json')
@include('includes.ld_json')
@yield('content')

<div class="modal fade collection-model" id="collection-video-model" tabindex="-1" role="dialog" aria-labelledby="collection-model-label" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 700px">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">{{__('misc.save_to_collection')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
          </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6  pt-3 pb-0 pb-sm-5 pb-md-5 pb-lg-5">

                        <div class="image-card pt-3">
                            <img class="img-fluid p-3 p-md-0 p-lg-0" id="imageCard" src="">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 pt-0 pt-md-5 pt-lg-5 content">
                        <div class="create-collection p-2">
                            <div class="form-group">
                                <input type="text" class="form-control" id="recipient-name" placeholder="{{trans('global.Create-new-collection')}}">
                                <button onclick="create_collection_video()" class="btn btn-primary">{{__('misc.Submit')}}</button>
                            </div>
                        </div>
                        <ul id="myCollections" class="list-unstyled">

                        </ul>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>



@include('includes.footer')
@include('includes.javascript_general')
<script src="{{ asset('js/lazysizes.min.js') }}"></script>
@yield('javascript')
@stack('javascript_navbar')


</body>
</html>
