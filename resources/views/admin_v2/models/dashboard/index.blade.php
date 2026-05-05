@extends('admin_v2.layout.app')

@section('content')

<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
<!--Begin::Dashboard 1-->




<!--End::Dashboard 1-->
</div>
<!-- end:: Content -->

@endsection

@push('css')
    <link href="{{ asset('plugins/morris/morris.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}" rel="stylesheet" type="text/css"/>
@endpush


@push('scripts')

    <!-- Morris -->
    <script src="{{ asset('plugins/morris/raphael-min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/morris/morris.min.js')}}" type="text/javascript"></script>

    <!-- knob -->
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/knob/jquery.knob.js')}}" type="text/javascript"></script>


    <script>

 

      // Class initialization on page load

    </script>
@endpush
