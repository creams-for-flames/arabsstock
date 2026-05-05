@if(auth()->check())
    @php($class=get_class($record))
    @php($type=class_basename(get_class($record)))
    <link rel="stylesheet" href="{{ asset('css/download-options.css') }}?v=1.3">
    <div id="sidebar-wrapper"
         data-download-options="{{ route('download_options',['type'=>strtolower($type),'id'=>$record->id]) }}">
        <form class="content container--fluid" method="get"
              id="downloadForm"
              action=""
        >
            <div class="row no-gutters">
                <div class="scrollable-content">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <h4 class="my-3">{{ __("Download {$type}") }}</h4>
                            <div class="">
                                <h2 class="credits-count">2</h2>
                                <span class="font-weight-bold">{{ __('credit') }}</span>
                            </div>
                        </div>
                    </div>
                    @include('download-options.download-type')
                    @include('download-options.license-type')
                    <div class="col-12 mt-3">
                        @if($class==\App\Models\Image::class && $record->has_removebg)
                            <small
                                class="d-block font-weight-bold mb-3 mt-4"> {{ __('Removed Background PNG') }} </small>
                            <div class="form-check my-3 ml-3- removebg">
                                <div class="d-flex justify-content-between">
                                    <div class="">
                                        <input class="form-check-input scale-check changeable" type="checkbox" name="removebg"
                                               id="removebg" value="1">
                                        <label class="form-check-label ml-2" for="removebg">
                                            <small
                                                class="d-block font-weight-bold"> {{ __('Add one credit and save your time') }}
                                                <span class="color-primary ml-1">(+1 {{ __('credit') }})</span>
                                            </small>
                                        </label>
                                    </div>
                                    <div class="color-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23"
                                             fill="currentColor" class="bi bi-filetype-png" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                  d="M14 4.5V14a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3.76 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.506.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.82v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.067-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.348.158.48.275.133.117.238.253.314.407Zm-8.64-.706H0v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H.788v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Zm1.964 2.666V13.25h.032l1.761 2.675h.656v-3.999h-.75v2.66h-.032l-1.752-2.66h-.662v4h.747Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @elseif($class==\App\Models\Video::class && $record->has_raw())
                            <small
                                class="d-block font-weight-bold mb-3 mt-4"> {{ __('Download with open features') }} </small>
                            <div class="form-check my-3 ml-3- removebg">
                                <div class="d-flex justify-content-between">
                                    <div class="">
                                        <input class="form-check-input scale-check changeable" type="checkbox" name="raw"
                                               id="download_raw" value="1">
                                        <label class="form-check-label ml-2" for="download_raw">
                                            <p
                                                class="font-weight-bold fs-11 p-0 m-0"> {{ __('Download ProRes file') }}
                                                <span class="color-primary ml-1">(+30 {{ __('credit') }})</span>
                                            </p>
                                            <p class="fs-11 p-0 m-0">
                                                {{ $record->width }}px . {{ $record->height }}px
                                                . {{ size_format($record->raw->size) }} . {{ $record->raw->extension }}
                                            </p>
                                        </label>
                                    </div>
                                    <div class="color-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-filetype-raw" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.597 11.85H0v3.999h.782v-1.491h.71l.7 1.491h1.651l.313-1.028h1.336l.314 1.028h.84L5.31 11.85h-.925l-1.329 3.96-.783-1.572A1.18 1.18 0 0 0 3 13.116c0-.256-.056-.479-.167-.668a1.098 1.098 0 0 0-.478-.44 1.669 1.669 0 0 0-.758-.158Zm-.815 1.913v-1.292h.7a.74.74 0 0 1 .507.17c.13.113.194.276.194.49 0 .21-.065.368-.194.474-.127.105-.3.158-.518.158H.782Zm4.063-1.148.489 1.617H4.32l.49-1.617h.035Zm4.006.445-.74 2.789h-.73L6.326 11.85h.855l.601 2.903h.038l.706-2.903h.683l.706 2.903h.04l.596-2.903h.858l-1.055 3.999h-.73l-.74-2.789H8.85Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <hr class="my-4">
                    <div id="subscriptions" class="ml-3"></div>
                </div>
                <div class="fixedfooter col-12 px-4">
                    <button
                        class="btn btn-primary btn-block downloadBtn mt-3">{{ __('Download') }}</button>
                    <a
                        href="javascript:;"
                        onclick="$('#wrapper').toggleClass('toggled');"
                        class="d-block my-2"> {{ __('Cancel') }} </a>
                </div>
            </div>
        </form>
    </div>
    @push('javascript_navbar')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" defer></script>
        <script>
            @if(session()->has('open_download_options'))
            @php($open_download_options=session()->pull('open_download_options'))
            @if(@$open_download_options['license_type'])
            $('[name="license_type"][value="{{ $open_download_options['license_type'] }}"]').click();
            @endif
            $("#wrapper").toggleClass("toggled");
            $('#downloadForm [name="license_type"]:checked').trigger('change');
            @else
            $('[name="license_type"][value="standard"]').click();
            $('#downloadForm [name="license_type"]:checked').trigger('change');
            @endif
            @if(session()->has('message'))
            swal('{{ session()->get('message') }}');
            @endif
        </script>
    @endpush
@endif
