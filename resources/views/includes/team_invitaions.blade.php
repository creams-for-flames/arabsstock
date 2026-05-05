@if(auth()->check())
    @php($user=auth()->user())
    @php($invitation=cache()->tags(['invitation',"user_{$user->id}"])->remember("user_invitation_{$user->id}",now()->addHour(),function()use($user){
        return collect([\App\Models\Invitation::whereIn('status',['pending','mailed'])->where('email',$user->email)->orderBy('id','desc')->first()]);
})->first())
    @if($invitation)
        <div class="py-3 h-team-invitation">
            <div class="text-center d-flex justify-content-center">
                <p class="mb-0 fs-14 bold">{{ __('You have an invitation from a team') }} ({{ $invitation->team->name }}
                    )</p>
                <a href="{{ route('accept_invitation',$invitation->uuid) }}" class="color-primary ml-3"
                   style="text-decoration: underline!important;">{{ __('Accept') }}</a>
                <a href="{{ route('decline_invitation',$invitation->uuid) }}" class="text-danger ml-3 "
                   style="text-decoration: underline!important;">{{ __('decline') }}</a>
            </div>
        </div>
    @endif
@endif
