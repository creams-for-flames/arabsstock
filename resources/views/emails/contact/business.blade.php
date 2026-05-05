<style type="text/css">
   html{direction: rtl;}
	h1,h2,h3,h4{text-align:right !important;}
</style>

@component('mail::message')
# رسالة جديدة
@component('mail::table')
    |        |          |
    | :--------- | :------------- |
    | من: | {!! $message->name !!} |
    | الجهة: | {!! $message->entity !!} |
    | البريد الإلكتروني: | {!! $message->email !!} |
    | الجوال: | {!! $message->mobile !!} |
@endcomponent
<p style="text-align: right;">
,تم الإرسال من نموذج للأعمال<br>
<a href="{{url('/')}}"> {{env('APP_NAME')}}</a>
</p>
@endcomponent
