@php($v=config('app.assets.version'))
@if(app()->getLocale() == 'en')
<link rel="stylesheet" href="{{ asset('css/bootstrap_4.4.1_css_bootstrap.min.css') }}"  media="all">
@endif
@if(app()->getLocale() == 'ar')
<link rel="stylesheet" href="{{ asset('css/bootstrap-4.2.1-rtl.min.css') }}" >
@endif
<link rel="stylesheet" href="{{ asset('css/pace-theme-default.min.css') }}" media="all">
<link href="{{ asset('css/main.css') }}?v={{ $v }}" rel="stylesheet" media="all">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.0/css/all.css"  media="all">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" media="all"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" media="all"/>
