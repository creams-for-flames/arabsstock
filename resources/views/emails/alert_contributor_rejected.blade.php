@component('mail::message')
# {{  __('views.welcome') }}

{{ isset($data['contributor'])? $data['contributor']->name : ' ------' }}

@component('mail::panel')
 <center>
        {{  __('views.rejected_contributor_content') }} <br/>
         " {{ $data['contributor_file']?$data['contributor_file']->original_name:'' }} "
        @if(isset($data['contributor_file']) && $data['contributor_file']->contributor_stage === 4)
         {{  __('views.hard_reject') }} 
        @endif
        
 </center>

<center>
        {{  __('views.reason') }} <br/>
</center>

 <center>
        {{ $data['contributor_file']?$data['contributor_file']->review_notes:' Error File' }} <br/>  <br/>
 </center>

@if(isset($data['contributor_file']) && $data['contributor_file']->contributor_stage != 4)
<center>
        {{  __('views.try_edit_and_resubmit') }} 
</center>
@endif

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}
@endcomponent

{{ __('views.Thanks') }},<br>
# {{ config('app.name') }}
@endcomponent
