@component('mail::message')
# {{  __('An invitation to join the Arabstock team') }}
#{{ __('Welcome :name',['name'=>$data['to']]) }}
{{ __(":name has invited you to join the team. Click\n the link below to activate your new account where you can\n search and download millions of images, vectors, and more",['name'=>$data['from']]) }}
@component('mail::button', ['url' => $data['url']])
    {{ __('Start Here') }}
@endcomponent
{{ __('views.Thanks') }},<br>
# {{ config('app.name') }}
@endcomponent
