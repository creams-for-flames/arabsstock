<style type="text/css">
    html{direction: rtl;}
    h1, h2, h3, h4,a{text-align: right !important;}
</style>

@component('mail::message')
    # تم تصدير هذه القائمة بناء على طلبك
<a href="{{ $link }}">{{ $link }}</a>
@endcomponent
