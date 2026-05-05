<!-- Bootstrap core CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.css')}}" rel="stylesheet" type="text/css" />
    @if(app()->getLocale() == 'ar')
    <link href="{{ asset('bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet" type="text/css" />
    @endif
    <link href="{{ asset('css/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    @if(app()->getLocale() == 'en')
    <link href="{{ asset('css/main.css') }}?v={{ config('app.assets.version') }}" rel="stylesheet">
    @else
    <link href="{{ asset('css/main-rtl.css') }}?v=3" rel="stylesheet">

    @endif

    <!-- FONT Awesome CSS -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

     <!-- IcoMoon CSS -->
    <link href="{{ asset('css/icomoon.css') }}" rel="stylesheet">

    <!-- stroke icons CSS -->
    <link href="{{ asset('css/strokeicons.css') }}" rel="stylesheet">

    <!-- IcoMoon CSS -->
    @if(app()->getLocale() == 'en')
    <link href="{{ asset('plugins/fleximages/jquery.flex-images.css') }}" rel="stylesheet">
    @else
    <link href="{{ asset('plugins/fleximages/jquery.flex-images-rtl.css') }}" rel="stylesheet">
    @endif
    <!-- Ionicons -->
    <link href="{{ asset('fonts/ionicons/css/ionicons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="{{ asset('js/ie-emulation-modes-warning.js') }}"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('js/ie10-viewport-bug-workaround.js') }}"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Cairo:400,700" rel="stylesheet"/>

    <!-- Sweet Alert -->
    <link href="{{ asset('plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" type="text/css" />

<link href="{{ asset('plugins/select2/select2.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('css/vivify.min.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
<![endif]-->

<script type="text/javascript">

    // URL BASE
    var URL_BASE = "{{ route('landPage') }}";
    // ReadMore
    var ReadMore = "{{ trans('misc.view_more') }}";
    var ReadLess = "{{ trans('misc.view_less') }}";

 </script>
