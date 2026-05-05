<style type="text/css">
   html{direction: rtl;}
	h1,h2,h3,h4{text-align:right !important;}
</style>

@component('mail::message')
# رسالة جديدة

### من : {!! $message->name !!}
### البريد الإلكتروني  : {!! $message->email !!}
@if($message->mobile)
### الجوال   : {!! $message->mobile !!}
@endif
### الرسالة : 
<h3>{!! $message->message !!} </h3>
 
<p style="text-align: right;">
,تم الإرسال من نموذج تواصل معنا<br>
<a href="{{url('/')}}"> {{env('APP_NAME')}}</a>
</p>
@endcomponent
