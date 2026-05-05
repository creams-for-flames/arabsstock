@extends('admin_v2.layout.app')

@section('content')

    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" id="app">
    </div>
    <!-- end:: Content -->

@endsection

@push('css')
<link href="{{ asset('/js_apps_assets/css/chunk-vendors.css') }}?v=v1.28" rel="stylesheet">
<link href="{{ asset('/js_apps_assets/css/image_store_contributor.css') }}?v=v1.28" rel="stylesheet">

@endpush

@push('scripts')
    <script>
    var type = {!! json_encode($type) !!}
    var ar_routes = {!! json_encode($routes) !!}
    var user = {!! json_encode($user) !!}
    window.lang =  "{{ $lang }}"
    </script>

<script src="{{ asset('/js_apps_assets/js/chunk-vendors.js') }}?v=v1.28"></script>
<script src="{{ asset('/js_apps_assets/js/image_store_contributor.js') }}?v=v1.28"></script>
@endpush
