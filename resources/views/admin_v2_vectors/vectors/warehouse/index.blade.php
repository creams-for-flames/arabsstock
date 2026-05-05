@extends('admin_v2.layout.app')

@section('content')

<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" id="app">
</div>
<!-- end:: Content -->

@endsection

@push('css')
    <link href="/js_apps_assets/css/chunk-vendors.css" rel="stylesheet">
    <link href="/js_apps_assets/css/image_store.css" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        var ar_routes = {!! json_encode($routes) !!}
        var user = {!! json_encode($user) !!}
        var department = "arabsstock"
        @if (isset($is_videos_site))
            var dataType = "videos"
        @else
            var dataType = "images"
        @endif
    </script>
    </script>
    <script src="/js_apps_assets/js/chunk-vendors.js"></script>
    <script src="/js_apps_assets/js/image_store.js"></script>
@endpush
