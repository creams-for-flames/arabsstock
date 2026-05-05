<div class="col-12 mb-3">
    @if($class==\App\Models\Image::class)
        <small class="d-block font-weight-bold mb-3"> {{__('misc.select_size')}} </small>
        <div class="dropdown">
            <button class="btn btn-secondary-outline dropdown-toggle dp-btn" type="button"
                    id="dropdownMenuButton"
                    data-toggle="dropdown" aria-expanded="false">
                <input type="hidden" name="type" value="0">
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @foreach($record->stock->whereNotIn('extension',['tiff','tif'])->reverse() as $r)
                    <a class="dropdown-item" href="javascript:;">
                        <input type="hidden" name="type" value="{{ $r->type }}" disabled>
                        <p class="m-0 text-left">
                            <span class="font-weight-bold"> {{ __("global.{$r->type}") }} . </span>
                            <span class="text-muted"> {{ $r->resolution }} px </span>
                            <br>
                            <span class="text-muted">{{ $r->dpi }} DPI · JPG</span>
                        </p>
                    </a>
                @endforeach
                @if($record->psd)
                    <a class="dropdown-item" href="javascript:;">
                        <input type="hidden" name="type" value="psd" disabled>
                        <p class="m-0 text-left">
                            <span class="text-muted"> {{ $r->resolution }} px </span>
                            <span class="font-weight-bold"> PSD </span>
                            <br>
                            <span class="text-muted">{{ $r->dpi }} DPI · PSD</span>
                        </p>
                    </a>
                @endif
            </div>
        </div>
    @elseif($class==\App\Models\Video::class)
        <small class="d-block font-weight-bold mb-3"> {{__('misc.resolution')}} </small>
        <div class="dropdown">
            <button class="btn btn-secondary-outline dropdown-toggle dp-btn" type="button"
                    id="dropdownMenuButton"
                    data-toggle="dropdown" aria-expanded="false">
                <input type="hidden" name="type" value="0">
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @foreach($video->child as $r)
                    <a class="dropdown-item" href="javascript:;">
                        <input type="hidden" name="type" value="{{ $r->type }}" disabled>
                        <p class="m-0 text-left">
                            <span class="font-weight-bold"> {{ $r->type }} </span>
                            <br>
                            {{ $r->width }}px . {{ $r->height }}px
                            . {{ number_format($r->size/1024 / 1024,1) }}MB . {{ $r->extension }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif($class==\App\Models\Vector::class)
        <small class="d-block font-weight-bold mb-3"> {{__('misc.select_file_type')}} </small>
        <div class="dropdown">
            <button class="btn btn-secondary-outline dropdown-toggle dp-btn" type="button"
                    id="dropdownMenuButton"
                    data-toggle="dropdown" aria-expanded="false">
                <input type="hidden" name="type" value="0">
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="javascript:;">
                    <input type="hidden" name="type" value="vector" disabled>
                    <p class="m-0 text-left">
                        <span class="font-weight-bold"> {{ __('global.vector') }}</span>
                        <br>
                        {{ intval($vector->height_large) }}x{{ intval($vector->width_large) }} px ||
                        eps || 300 DPI
                    </p>
                </a>
                <a class="dropdown-item" href="javascript:;">
                    <input type="hidden" name="type" value="image" disabled>
                    <p class="m-0 text-left">
                        <span class="font-weight-bold"> {{ __('global.image') }}</span>
                        <br>
                        {{ $vector->height_large }}x{{ $vector->width_large }} px || jpg || 300 DPI
                    </p>
                </a>
            </div>
        </div>
    @endif
</div>
@if(auth()->user()->team)
    @php($can_download_with_team_subscription=auth()->user()->active_team_subscriptions()->has('plan')->with('plan')->sum('team_user_subscription.remaining_credits')>=$enhanced_const_credits)
    <div class="col-12 mb-3">
        <small class="d-block font-weight-bold mb-3"> {{__('Subscription Type')}} </small>
        <div class="form-check mb-3">
            <input class="form-check-input mt-3 scale-check" type="radio" name="subscription_type"
                   id="user_subscriptions" value="user_subscriptions" checked
            >
            <label class="form-check-label ml-2" for="user_subscriptions">
                <small class="d-block font-weight-bold"> {{ __('misc.My subscriptions') }}</small>
                <span
                    class="fs-11 text-muted">{{ __('It will be deducted from your subscription') }}</span>
            </label>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input mt-3 scale-check" type="radio" name="subscription_type"
                   id="team_subscriptions" value="team_subscriptions"
                   @if(!$can_download_with_team_subscription)
                   disabled
                   @endif
            >
            <label class="form-check-label ml-2" for="team_subscriptions">
                <small class="d-block font-weight-bold"> {{ __('misc.Team subscriptions') }}</small>
                <span
                    class="fs-11 text-muted">{{ __('It will be deducted from your team subscription') }}</span>
            </label>
        </div>
    </div>
@endif
