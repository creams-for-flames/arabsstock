@if(!app()->isLocal())
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js',
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-TSZSL8L');</script>
<!-- End Google Tag Manager -->
@endif
@include('includes.cj')
<meta charset="utf-8">
@if(! request()->segment(2))
    <title>@section('title')@show @if( isset( $settings->title ) ){{$settings->title}}@endif</title>
@else
    @if(request()->segment(2)=='photos')
        <title>@section('title')@show @if( isset( $settings->title_image ) ){{$settings->title_image}}@endif</title>
    @elseif(request()->segment(2)=='vectors')
        <title>@section('title')@show @if( isset( $settings->title_image ) ){{$settings->title_image}}@endif</title>
    @else
        <title>@section('title')@show @if( isset( $settings->title ) ){{$settings->title}}@endif</title>

    @endif
@endif
<meta name="p:domain_verify" content="31225e88f696a236711f19d8a1ed31e1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
@if(! request()->segment(2))
<meta name="description" content="{!! trans('global.website_description') !!}">

@else
<meta name="description" content="@yield('description_custom'){{ $settings->description }}">

@endif
@if(!in_array(optional(request()->route())->getName(),[
    'photo.show',
    'video.show',
    'video.categories',
    'categories',
    'video.category.show',
    'category.show',
    'vector.category.show',
    'vector.show'
]))
<meta name="keywords" content="@yield('keywords_custom'){{ $settings->keywords }}"/>
@endif
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('img/favicon/site.webmanifest') }}">
<link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg') }}" color="#20d899">
<meta name="msapplication-TileColor" content="#292e47">
<meta name="theme-color" content="#292e47">

<link rel="alternate" hreflang="ar" href="https://arabsstock.com/ar"/>
<link rel="alternate" hreflang="en" href="https://arabsstock.com/en"/>
@yield('meta')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap');
    body, html{font-family: Cairo, sans-serif;font-weight: 600;color: #30354b !important;font-size: 14px !important;line-height: 1.5;letter-spacing: 0;height: 100%}
    [v-cloak] > *{ display: none !important; }
    [v-cloak]::before{ content: "{{__('global.loading')}}" !important; }
</style>
@include('includes.css_general')
@yield('css')
@stack('header')
<script type="text/javascript">
    window.app_locale = '{{app()->getLocale()}}';
    window.user = {!! auth()->check() ? json_encode(\Illuminate\Support\Arr::only(auth()->user()->toArray(),['id','name','email','username'])) : 'null' !!};
</script>
<script src="{{ asset('js/choices.js') }}"></script>
<script>
    const urls = {
        vector_home: "{!! route('vectors.home') !!}",
        vector_category: "{{route('vectors.category.show',':slug')}}",
        video_category: "{{route('video.category.show',':slug')}}",
        image_category: "{{route('category.show',':slug')}}",
        images_search: '{{route('search', 'query')}}',
        videos_search: '{{route('video.search', 'query')}}',
        vectors_search: '{{route('vectors.search', 'query')}}',
        autocomplete:'{{route('autocomplete', 'type')}}'
    };
</script>
