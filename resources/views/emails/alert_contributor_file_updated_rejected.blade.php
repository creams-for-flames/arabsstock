@component('mail::message')
# {{  __('views.welcome') }}

{{ isset($data->contributor)? $data->contributor->name : ' ------' }}

# {{  __('views.alert_contributor_updated_rejected') }}


<br>

@component('mail::button', ['url' => $data->file->post_link])
{{ __("views.click_here_follow_updates") }}- {{$data->id}}
@endcomponent

{{ __('views.Thanks') }},<br>
# {{ config('app.name') }}
@endcomponent
