@push('after-navbar')
    <div class="search-photo">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    {{-- <div class="border p-4 rounded mb-4">
                        <h3 class="mt-0">{{trans('views.welcome')}}</h3>
                        @if(isset($user_subscription_remaining) && $user_subscription_remaining != null && isset($user_subscription_remaining) && $title_plan=='images')
                            <p> {!! trans('views.You_have_download_left', [ 'count' =>$user_subscription_remaining->download_remaining.'/'.$user_subscription_remaining->plan->downloads_count, 'days' =>$days_left]) !!}
                            </p>
                           @elseif(isset($title_plan) && $title_plan=='images')
                            <p>  {{trans('views.You_dont_have_download_left')}}
                            </p>
                        @endif
                    </div> --}}
                </div>
            </div>
            <div class="s003">
                <form onsubmit="return false;">
                    <div class="d-flex flex-column flex-md-row full-search-form">
                        <div class="inner-form">
                            <div class="input-field first-wrap mb-0">
                                <div class="input-select">
                                    <select name="choices-single-default"
                                            data-images-ris="{{ route('ris',0) }}"
                                            data-videos-ris="{{ route('video.ris',0) }}"
                                            data-vectors-ris="{{ route('vectors.ris',0) }}"
                                    >
                                        <option value="images" {{ is_in_photos_website()?'selected':'' }}>
                                            <i class="far fa-camera-alt"></i>
                                            {{trans('views.Images')}}</option
                                        >
                                        <option value="videos" {{ is_in_video_website()?'selected':'' }}>
                                            <i class="far fa-video"></i>
                                            {{trans('views.Videos')}}</option
                                        >
                                        <option value="vectors" {{ is_in_vector_website()?'selected':'' }}>
                                            <i class="far fa-tilde fa-lg"></i>
                                            {{trans('global.vectors')}}</option
                                        >
                                    </select>
                                </div>
                            </div>
                            <div class="input-field second-wrap d-flex">
                                <?php $q = isset($q) && is_string($q) ? $q : ''; ?>
                                <?php $q = isset($tags) && is_string($tags) ? $tags : $q; ?>
                                <?php $q = isset($videos['tags']) && is_string($videos['tags']) ? $videos['tags'] : $q; ?>
                                <input
                                    onkeyup="validation()" onkeydown="validation()" onclick="validation()"
                                    onchange="validation()" onkeypress="validation()" maxlength="50"
                                    maxlength="50"
                                    id="search"
                                    type="text"
                                    placeholder="{{trans('misc.title_search_bar')}}"
                                    value="{{$q}}"
                                />

                                <button
                                    class="btn-search btn-image-search ml-md-3 ml-2 mt-md-0 d-md-none"
                                    type="button"
                                    data-toggle="tooltip" data-placement="top" title="{{ __('Search by image') }}"
                                    onclick="$('#image-search-modal').modal('show')"
                                >
                                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                        width="20" height="28" viewBox="0 0 512.000000 512.000000"
                                        preserveAspectRatio="xMidYMid meet">

                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#5c5c5c" stroke="none">
                                            <path
                                                d="M4208 4898 c-21 -5 -58 -28 -82 -50 -68 -59 -76 -96 -76 -360 l0
                                                -218 -216 0 c-125 0 -234 -5 -259 -11 -27 -7 -63 -28 -90 -51 -112 -98 -88
                                                -275 46 -342 53 -26 54 -26 285 -26 l232 0 4 -242 3 -243 28 -47 c71 -121 234
                                                -142 332 -44 59 60 65 90 65 350 l0 226 226 0 c260 0 290 6 350 65 98 98 77
                                                261 -44 332 l-47 28 -242 3 -243 4 0 233 0 233 -28 53 c-46 89 -149 134 -244
                                                107z"
                                            />
                                            <path
                                                d="M1905 4260 c-170 -26 -263 -79 -441 -252 -79 -77 -157 -144 -177
                                                -153 -29 -12 -84 -15 -267 -15 -261 0 -329 -10 -445 -65 -125 -60 -228 -162
                                                -291 -288 -73 -145 -69 -60 -69 -1362 l0 -1170 28 -82 c54 -161 169 -295 317
                                                -373 144 -74 25 -70 1785 -70 1754 0 1639 -4 1785 68 110 54 223 168 279 282
                                                72 148 71 128 71 1023 0 699 -2 802 -16 835 -33 79 -110 132 -194 132 -88 0
                                                -152 -38 -193 -115 -22 -40 -22 -45 -27 -852 l-5 -811 -30 -44 c-17 -23 -51
                                                -54 -75 -68 l-45 -25 -1551 0 -1551 0 -48 30 c-36 22 -57 45 -77 84 l-28 53 0
                                                1109 c0 1215 -3 1148 58 1212 58 61 73 64 357 70 284 5 325 12 439 69 83 41
                                                121 72 261 209 67 66 136 125 157 133 32 14 102 16 471 16 l433 0 53 26 c90
                                                45 134 140 110 238 -16 67 -85 138 -151 154 -53 13 -837 15 -923 2z"
                                            />
                                            <path
                                                d="M2165 3184 c-388 -69 -703 -341 -831 -716 -55 -164 -68 -388 -30
                                                -559 92 -418 436 -751 853 -824 656 -114 1253 383 1253 1045 0 177 -36 332
                                                -113 485 -101 202 -298 393 -500 485 -200 90 -427 121 -632 84z m334 -429 c85
                                                -20 204 -84 274 -146 71 -63 149 -179 176 -261 58 -179 49 -328 -31 -497 -57
                                                -120 -169 -232 -289 -289 -169 -80 -318 -89 -497 -31 -82 27 -198 105 -261
                                                176 -62 70 -126 189 -146 274 -19 82 -19 228 0 304 57 224 242 411 465 469 72
                                                19 229 19 309 1z"
                                            />
                                        </g>
                                    </svg>
                                </button>
                                <div id="search-alert" style="background-color: rgba(255,7,34,0.62);display: none"
                                     class="">{{__('misc.sentence_is_to_long')}}</div>
                            </div>
                            <div class="input-field third-wrap" style="display: flex">
                                <button class="btn-search btn-text-search" type="button">
                                    <i class="fal fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <button
                            class="btn-search btn-image-search ml-md-3 mt-3 mt-md-0 d-none d-md-flex"
                            type="button"
                            data-toggle="tooltip" data-placement="top" title="{{ __('Search by image') }}"
                            onclick="$('#image-search-modal').modal('show')"
                        >
                            <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                 width="20" height="28" viewBox="0 0 512.000000 512.000000"
                                 preserveAspectRatio="xMidYMid meet">

                                <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                                   fill="#5c5c5c" stroke="none">
                                    <path d="M4208 4898 c-21 -5 -58 -28 -82 -50 -68 -59 -76 -96 -76 -360 l0
                                        -218 -216 0 c-125 0 -234 -5 -259 -11 -27 -7 -63 -28 -90 -51 -112 -98 -88
                                        -275 46 -342 53 -26 54 -26 285 -26 l232 0 4 -242 3 -243 28 -47 c71 -121 234
                                        -142 332 -44 59 60 65 90 65 350 l0 226 226 0 c260 0 290 6 350 65 98 98 77
                                        261 -44 332 l-47 28 -242 3 -243 4 0 233 0 233 -28 53 c-46 89 -149 134 -244
                                        107z"
                                    />
                                    <path d="M1905 4260 c-170 -26 -263 -79 -441 -252 -79 -77 -157 -144 -177
                                        -153 -29 -12 -84 -15 -267 -15 -261 0 -329 -10 -445 -65 -125 -60 -228 -162
                                        -291 -288 -73 -145 -69 -60 -69 -1362 l0 -1170 28 -82 c54 -161 169 -295 317
                                        -373 144 -74 25 -70 1785 -70 1754 0 1639 -4 1785 68 110 54 223 168 279 282
                                        72 148 71 128 71 1023 0 699 -2 802 -16 835 -33 79 -110 132 -194 132 -88 0
                                        -152 -38 -193 -115 -22 -40 -22 -45 -27 -852 l-5 -811 -30 -44 c-17 -23 -51
                                        -54 -75 -68 l-45 -25 -1551 0 -1551 0 -48 30 c-36 22 -57 45 -77 84 l-28 53 0
                                        1109 c0 1215 -3 1148 58 1212 58 61 73 64 357 70 284 5 325 12 439 69 83 41
                                        121 72 261 209 67 66 136 125 157 133 32 14 102 16 471 16 l433 0 53 26 c90
                                        45 134 140 110 238 -16 67 -85 138 -151 154 -53 13 -837 15 -923 2z"
                                    />
                                    <path d="M2165 3184 c-388 -69 -703 -341 -831 -716 -55 -164 -68 -388 -30
                                        -559 92 -418 436 -751 853 -824 656 -114 1253 383 1253 1045 0 177 -36 332
                                        -113 485 -101 202 -298 393 -500 485 -200 90 -427 121 -632 84z m334 -429 c85
                                        -20 204 -84 274 -146 71 -63 149 -179 176 -261 58 -179 49 -328 -31 -497 -57
                                        -120 -169 -232 -289 -289 -169 -80 -318 -89 -497 -31 -82 27 -198 105 -261
                                        176 -62 70 -126 189 -146 274 -19 82 -19 228 0 304 57 224 242 411 465 469 72
                                        19 229 19 309 1z"
                                    />
                                </g>
                            </svg>
                            <p class="ml-2 mb-0 fs-14">{{ __('Search by image') }}</p>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script>
        new Choices('[name=choices-single-default]',
            {
                searchEnabled: false,
                itemSelectText: '',
            });
    </script>
@endpush
@push('header')
    <link rel="stylesheet" href="{{ asset('css/dropzone@5.9.3_dist_min_dropzone.min.css') }}" type="text/css"/>
@endpush
@push('javascript_navbar')
    <script src="{{ asset('js/dropzone@5.9.3_dist_min_dropzone.min.js') }}" defer></script>
    <div class="modal fade bd-example-modal-sm image-search-modal" id="image-search-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content p-3">
                <div class="modal-header align-items-center">
                    <h2 class="my-0">{{ __('Search similar images') }}</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" style="display: none;">
                    <span class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
  <path
      d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
  <path
      d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/>
</svg>
                    </span>
                        <span class="text"></span>
                    </div>
                    <form action="{{ route('ris',0) }}" class="dropzone ris-upload"
                    >
                        <span class="img-icon"><svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                                    width="55"  height="55" viewBox="0 0 512.000000 512.000000"
                                                    preserveAspectRatio="xMidYMid meet">

                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                                           fill="#5c5c5c" stroke="none">
                                            <path d="M4208 4898 c-21 -5 -58 -28 -82 -50 -68 -59 -76 -96 -76 -360 l0
-218 -216 0 c-125 0 -234 -5 -259 -11 -27 -7 -63 -28 -90 -51 -112 -98 -88
-275 46 -342 53 -26 54 -26 285 -26 l232 0 4 -242 3 -243 28 -47 c71 -121 234
-142 332 -44 59 60 65 90 65 350 l0 226 226 0 c260 0 290 6 350 65 98 98 77
261 -44 332 l-47 28 -242 3 -243 4 0 233 0 233 -28 53 c-46 89 -149 134 -244
107z"/>
                                            <path d="M1905 4260 c-170 -26 -263 -79 -441 -252 -79 -77 -157 -144 -177
-153 -29 -12 -84 -15 -267 -15 -261 0 -329 -10 -445 -65 -125 -60 -228 -162
-291 -288 -73 -145 -69 -60 -69 -1362 l0 -1170 28 -82 c54 -161 169 -295 317
-373 144 -74 25 -70 1785 -70 1754 0 1639 -4 1785 68 110 54 223 168 279 282
72 148 71 128 71 1023 0 699 -2 802 -16 835 -33 79 -110 132 -194 132 -88 0
-152 -38 -193 -115 -22 -40 -22 -45 -27 -852 l-5 -811 -30 -44 c-17 -23 -51
-54 -75 -68 l-45 -25 -1551 0 -1551 0 -48 30 c-36 22 -57 45 -77 84 l-28 53 0
1109 c0 1215 -3 1148 58 1212 58 61 73 64 357 70 284 5 325 12 439 69 83 41
121 72 261 209 67 66 136 125 157 133 32 14 102 16 471 16 l433 0 53 26 c90
45 134 140 110 238 -16 67 -85 138 -151 154 -53 13 -837 15 -923 2z"/>
                                            <path d="M2165 3184 c-388 -69 -703 -341 -831 -716 -55 -164 -68 -388 -30
-559 92 -418 436 -751 853 -824 656 -114 1253 383 1253 1045 0 177 -36 332
-113 485 -101 202 -298 393 -500 485 -200 90 -427 121 -632 84z m334 -429 c85
-20 204 -84 274 -146 71 -63 149 -179 176 -261 58 -179 49 -328 -31 -497 -57
-120 -169 -232 -289 -289 -169 -80 -318 -89 -497 -31 -82 27 -198 105 -261
176 -62 70 -126 189 -146 274 -19 82 -19 228 0 304 57 224 242 411 465 469 72
19 229 19 309 1z"/>
                                        </g>
                                    </svg>
                                </span>
                        <div class="dz-message" data-dz-message>
                            <p class="text-muted fs-12 mb-2">{{ __('Only support JPG and PNG images under :size MB',['size'=>20]) }}</p>
                            <p class="mb-0">{{ __('dropzone.dictDefaultMessage') }}</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-block px-3 text-center">
                    <button type="button" class="btn btn-primary btn-block mb-3 choose-file">
                        {{ __('Choose file') }}
                    </button>
                    <a href="javascript:;" data-dismiss="modal">{{ __('views.Close') }}</a>
                </div>
            </div>
        </div>
    </div>
@endpush
