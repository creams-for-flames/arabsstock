<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ asset('js/npm_popper.js@1.16.0_dist_umd_popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap_4.4.1_js_bootstrap.min.js') }}"></script>
<script src="{{asset('js/owl.carousel.js')}}" type="text/javascript"></script>
<script src="{{ asset('jQuery-flexImages-master/jquery.flex-images.min.js') }}"></script>
<script src="{{ asset('js/lodash@4.17.15_lodash.min.js') }}"></script>
<script src="{{ asset('js/cookie@2_src_js.cookie.min.js') }}" defer></script>
<script src="{{ asset('js/lang.'.strtolower(config('app.locale')).'.js') }}"></script>
<script src="{{ asset('js/pace-js@latest_pace.min.js') }}"></script>
<script src="{{asset('js/lazysizes.min.js')}}"></script>
<script src="{{asset('js/bootstrap-notify.js')}}" defer></script>

<script type="text/javascript">
    // URL BASE
    var URL_BASE = "{{ route('landPage') }}";
    // ReadMore
    var ReadMore = "{{ trans('misc.view_more') }}";
    var ReadLess = "{{ trans('misc.view_less') }}";
    const IMAGE_COLLECTION ="{{ route('photos.imageCollection',0)}}";
    const IMAGE_COLLECTION_STORE ="{{ route('collection_create')}}";
    const VIDEO_COLLECTION ="{{ route('video.videoCollection',0)}}";
    const VIDEO_COLLECTION_STORE ="{{ route('video.collection.store')}}";
    const VECTOR_COLLECTION ="{{ route('vectors.vectorCollection',0)}}";
    const VECTOR_COLLECTION_STORE ="{{ route('vectors.collection.store')}}";

    const IS_IN_PHOTOS_SITE = {{ is_in_photos_website()?'true':'false' }};
    const IS_IN_VECTOR_SITE = {{ is_in_vector_website()?'true':'false' }};
    const IS_IN_VIDEO_SITE = {{ is_in_video_website()?'true':'false' }};
    const url_auth = '{{ route("auth.guest") }}';
</script>
<script src="{{ asset('js/general'.(app()->environment()=='local'?'':'.min').'.js') }}?v={{ config('app.assets.version') }}"
        type="text/javascript"></script>
