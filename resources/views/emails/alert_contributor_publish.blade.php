@component('mail::message')
# {{  __('views.welcome') }}


# {{  __('views.alert_contributor_publish') }}

{{ isset($data['contributor'])? $data['contributor']->name : ' ------' }}

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

{{ __('views.Thanks') }},<br>
# {{ config('app.name') }}
@endcomponent
