<div class="col-12 mt-3">
    <div class="mb-3 license">
        <small class="font-weight-bold "> {{ __('License type') }} </small>
        <a href="javascript:;" data-toggle="modal" data-target="#licenseDetails" class="ml-1 btn p-0 border-0">
            <i class="fal fa-info-circle m-0"></i>
        </a>
    </div>
    <div class="">
        <div class="form-check mb-3">
            <input class="form-check-input mt-3 scale-check changeable" type="radio" name="license_type"
                   id="standard_license_type" value="standard" checked
            >
            <label class="form-check-label ml-2" for="standard_license_type">
                <small class="d-block font-weight-bold"> {{ __('Standard license') }}
                    <span
                        class="color-primary ml-1"
                        license-credits-count>({{ $standard_const_credits }} {{ __('credit') }})</span></small>
                <span
                    class="fs-11 text-muted">{{ __('Limited usage in some presentation mediums.') }}</span>
            </label>
        </div>
        @if($enhanced_const_credits)
            <div class="form-check mb-3">
                <input class="form-check-input mt-3 scale-check changeable" type="radio" name="license_type"
                       id="enhanced_license_type" value="enhanced"
                >
                <label class="form-check-label ml-2" for="enhanced_license_type">
                    <small class="d-block font-weight-bold"> {{ __('Enhanced license') }}
                        <span
                            class="color-primary ml-1"
                            license-credits-count>({{ $enhanced_const_credits }} {{ __('credit') }})</span>
                    </small>
                    <span
                        class="fs-11 text-muted">
                                            @if($record->how_use === 'editorial_only')
                            {{ __('Unlimited usage in all presentation mediums.') }}
                        @else
                            {{ __('Unlimited usage in all presentation mediums.') }}
                        @endif
                                        </span>
                </label>
            </div>
        @endif

        @if($exclusive_const_credits)
            <div class="form-check">
                <input class="form-check-input mt-3 scale-check changeable" type="radio" name="license_type"
                       id="exclusive_license_type" value="exclusive"
                    {{ $record->can_reserve()?'':'disabled force-disabled' }}
                >
                <label class="form-check-label ml-2" for="exclusive_license_type">
                    <small class="d-block font-weight-bold"> {{ __('Exclusive license') }}
                        <span
                            class="color-primary ml-1"
                            license-credits-count>({{ $exclusive_const_credits }} {{ __('credit') }})</span>
                    </small>
                    <span
                        class="fs-11 text-muted">
                                            @if($record->can_reserve())
                            @if($record->how_use === 'editorial_only')
                                {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                            @else
                                {{ __("Unlimited usage in all presentation mediums, exclusive use for two years.") }}
                            @endif
                        @else
                            {{ __('This content cannot be purchased as exclusive content') }}
                        @endif
                                        </span>
                </label>
            </div>
        @endif
    </div>
</div>
