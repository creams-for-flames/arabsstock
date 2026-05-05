@extends('app_contributor')

@section('content')
<link href="/js_apps_assets/css/chunk-vendors.css" rel="stylesheet">
<link href="/js_apps_assets/css/image_store_contributor.css" rel="stylesheet">
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" id="app">
</div>
<!-- end:: Content -->
<script>
    var ar_routes = {!! json_encode($routes) !!}
</script>
<script src="/js_apps_assets/js/chunk-vendors.js"></script>
<script src="/js_apps_assets/js/image_store_contributor.js"></script>
@endsection 