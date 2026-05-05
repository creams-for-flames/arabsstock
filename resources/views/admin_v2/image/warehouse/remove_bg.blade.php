@extends('admin_v2.layout.app')

@section('content')

    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" id="app">
    </div>
    <!-- end:: Content -->

@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/js_apps_assets/css/image_store_remove_bg.css?v=16.35') }}">
@endpush

@push('scripts')
    <script>
        var ar_routes = {!! json_encode($routes) !!}
            var user = {!! json_encode($user) !!}
            var department = "arabsstock"
        @if (isset($is_videos_site))
        var dataType = "videos"
        @elseif(isset($is_vectors_site))
        var dataType = "vectors"
        @else
        var dataType = "images"
        @endif
    </script>
    <script src="{{ asset('/js_apps_assets/js/image_store_remove_bg.js?v=16.35') }}"></script>
@endpush
