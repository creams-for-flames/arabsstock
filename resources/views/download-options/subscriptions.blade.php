<div
    data-url="{!! $can_download?$download_url:route('plans') !!}"
    data-can-download="{{ intval($can_download) }}"
    data-btn-text="{{ $can_download?__('Download'):__('misc.prices and packages') }}"
>
    @if($license=='standard' && $old_subscriptions->count())
        <p class="text-center color-primary fs-12">
            <strong>{{ __('Your current downloads balance: :downloads downloads',['downloads'=>$old_subscriptions->sum('download_remaining')]) }}</strong>
        </p>
    @else
        <p class="text-center {{ ($active_subscriptions->sum('remaining_credits')>= $needed_credits)?'color-primary':'text-danger' }} fs-12">
            <strong>{{ __('Your current point balance: :credits credits',['credits'=>$active_subscriptions->sum('remaining_credits')]) }}</strong>
        </p>
    @endif
    @if(($license=='standard' && $old_subscriptions->count()) or $need_to_download->count())
        <h5 class="mb-2"> {{ __('misc.My subscriptions') }} </h5>
        @if($license=='standard' && $old_subscriptions->count())
            <div class="mt-3 border rounded">
                @foreach($old_subscriptions as $subscription)
                    <div
                        class="form-check my-2 p-3 {{ $loop->last?'':'border-bottom' }}"
                        data-credits="{{ $subscription->remaining_credits }}">
                        <label
                            class="form-check-label ml-2 d-flex justify-content-between"
                            for="subscription_{{ $subscription->id }}">
                            <div>
                                <small
                                    class="d-block font-weight-bold"> {{ $subscription->plan->title }} </small>
                                <small
                                    class="d-block font-weight-bold"> {{ number_format($subscription->plan->price) }}
                                    $</small>
                            </div>
                            <div>
                                <small
                                    class="d-block text-muted">{{ __('Downloads left') }}
                                    : {{ $subscription->download_remaining }}
                                    / {{ $subscription->plan->downloads_count }} </small>
                                <small class="d-block">{{ __('Plan expires') }}
                                    : {{ format_date('d M,Y',$subscription->finished_at) }}</small>
                            </div>
                        </label>
                        @if($loop->index==0)
                            <p class="text-center color-primary mb-0 mt-1 fs-11">{{ __('One download will be deducted from this subscription') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
        <div class="mt-3 border rounded">
            @foreach($active_subscriptions as $subscription)
                <div
                    class="form-check my-2 p-3 {{ $loop->last?'':'border-bottom' }}"
                    data-credits="{{ $subscription->remaining_credits }}">
                    <label
                        class="form-check-label ml-2 d-flex justify-content-between"
                        for="subscription_{{ $subscription->id }}">
                        <div>
                            <small
                                class="d-block font-weight-bold"> {{ $subscription->title }} </small>
                            <small
                                class="d-block font-weight-bold"> {{ number_format($subscription->amount) }}
                                $</small>
                        </div>
                        <div>
                            <small
                                class="d-block text-muted">{{ __('Credits left') }}
                                : {{ $subscription->remaining_credits }}
                                / {{ $subscription->credits }} </small>
                            <small class="d-block">{{ __('Plan expires') }}
                                : {{ format_date('d M,Y',$subscription->finished_at) }}</small>
                        </div>
                    </label>
                    @if(!(($license=='standard' && $old_subscriptions->count())) && $need_to_download->get($subscription->id))
                        <p class="text-center color-primary mb-0 mt-1 fs-11">{{ __(':credits credit will be deducted from this plan',['credits'=>$need_to_download->get($subscription->id)['credits']]) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
    @endif
</div>
