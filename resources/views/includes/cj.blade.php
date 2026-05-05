@php($cj_config=config('cj'))
@if(request()->hasCookie('cje') && key_exists(optional(request()->route())->getName(),$cj_config))
    <!-- BEGIN CJ TRACKING CODE -->
    <script type='text/javascript'>
        if (!window.cj) window.cj = {};
        cj.sitePage = {
            enterpriseId: 1563613,
            pageType: '{{ $cj_config[request()->route()->getName()] }}',
            @if(auth()->check())
            userId: '{{ auth()->id() }}',
            emailHash: '{{ hash('sha256', auth()->user()->email) }}',
            @endif
            referringChannel: 'Affiliate',
            cartSubtotal: 0,
        };
    </script>
    <script type='text/javascript'>
        (function(a,b,c,d){
            a='{{ url('proxydirectory/tags/12363/tag.js') }}';
            b=document;c='script';d=b.createElement(c);d.src=a;
            d.type='text/java'+c;d.async=true;
            d.id='cjapitag';
            a=b.getElementsByTagName(c)[0];a.parentNode.insertBefore(d,a)
        })();
    </script>
    <!-- END CJ TRACKING CODE -->
@endif
@yield('cj')
