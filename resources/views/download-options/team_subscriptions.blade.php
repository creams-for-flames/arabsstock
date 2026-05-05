<div
    data-url="{!! $can_download?$download_url:route('plans') !!}"
    data-btn-text="{{ $can_download?__('Download'):__('misc.prices and packages') }}"
>
    @if($active_team_subscriptions->sum('pivot.remaining_credits')>=$enhanced_const_credits)
        @if($need_to_download_team->count())
            <p class="text-center fs-12 color-primary">
                <strong>{{ __('Your current point balance: :credits credits',['credits'=>$active_team_subscriptions->sum('pivot.remaining_credits')]) }}</strong>
            </p>
            <h5 class="mb-2"> {{ __('misc.Team subscriptions') }} </h5>
            <div class="mt-3 border rounded">
                @foreach($active_team_subscriptions as $subscription)
                    <div
                        class="form-check my-2 p-3 {{ $loop->last?'':'border-bottom' }}"
                        data-credits="{{ $subscription->remaining_credits }}">
                        <label
                            class="form-check-label ml-2 d-flex justify-content-between"
                            for="subscription_{{ $subscription->id }}">
                            <div>
                                <small
                                    class="d-block font-weight-bold"> {{ $subscription->title }} </small>
                            </div>
                            <div>
                                <small
                                    class="d-block text-muted">{{ __('Credits left') }}
                                    : {{ $subscription->pivot->remaining_credits }} </small>
                                <small class="d-block">{{ __('Plan expires') }}
                                    : {{ format_date('d M,Y',$subscription->finished_at) }}</small>
                            </div>
                        </label>
                        @if($need_to_download_team->get($subscription->id))
                            <p class="text-center color-primary mb-0 mt-1 fs-11">{{ __(':credits credit will be deducted from this plan',['credits'=>$need_to_download_team->get($subscription->id)['credits']]) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif
    @if(!$can_download)
        <p class="text-center text-danger fs-12">
            <strong>{{ __("You don't have enough credits") }}</strong>
        </p>
    @endif
</div>
