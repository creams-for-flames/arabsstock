<!DOCTYPE html>
<html lang="{{strtolower(config('app.locale'))}}" dir="{{ config('app.locale')=='ar'?'rtl':'ltr' }}">
<head>
@include('includes.head')
@if(!in_array(request()->route()->getName(),['photo.show','video.show','vector.show']))
  <meta property="og:url" content="https://arabsstock.com">
  <meta property="og:type" content="website">
  <meta property="og:title" content="عربستوك - Arabsstock ">
  <meta property="og:description" content="أكبر مكتبة رقمية عربية، خليجية، سعودية حقيقية اكتشف آلاف الصور والفيديو و الفيكتور الحصرية التي تناسب مشروعك، خطط بأقل الأسعار واشتراكات شهرية مرنة">
  <meta property="og:image" content="{{ asset('img/og-image.png') }}">
  <meta name="twitter:card" content="summary_large_image">
  <meta property="twitter:domain" content="arabsstock.com">
  <meta property="twitter:url" content="https://arabsstock.com">
  <meta name="twitter:title" content="عربستوك - Arabsstock ">
  <meta name="twitter:description" content="أكبر مكتبة رقمية عربية، خليجية، سعودية حقيقية اكتشف آلاف الصور والفيديو و الفيكتور الحصرية التي تناسب مشروعك، خطط بأقل الأسعار واشتراكات شهرية مرنة">
  <meta name="twitter:image" content="{{ asset('img/og-image.png') }}">
    <script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "WebSite",
  "name": "عربستوك",
  "url": "https://arabsstock.com/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://arabsstock.com/ar/search/{search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Corporation",
  "name": "Arabsstock",
  "alternateName": "عربستوك",
  "url": "https://arabsstock.com/",
  "logo": "https://arabsstock.com/img/logowa.svg",
  "sameAs": [
    "https://fb.com/ArabsStock",
    "https://twitter.com/ArabsStock",
    "https://www.instagram.com/arabsstock/",
    "https://www.linkedin.com/company/arabsstock",
    "https://www.pinterest.com/arabsstock/"
  ]
}
</script>
@endif
</head>
<body class="{{ optional(request()->route())->getName()=='home'?'home':'' }}">
<div id="wrapper">
    @yield('sidebar')
    <div id="page-content-wrapper">
        <div class="layer"></div>
        @include('includes.team_invitaions')
        @include('includes.navbar')
        @stack('ld_json')
        @include('includes.ld_json')
        @yield('content')
        <div class="modal fade collection-model" id="collection-model" tabindex="-1" role="dialog"
             aria-labelledby="collection-model-label" aria-hidden="true">
            <div class="modal-dialog" role="document" style="max-width: 700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('misc.save_to_collection')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6  pt-3 pb-0 pb-sm-5 pb-md-5 pb-lg-5">
                                <div class="image-card pt-3">
                                    <img class="img-fluid p-3 p-md-0 p-lg-0" id="imageCard" src=""/>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6 pt-0 pt-md-5 pt-lg-5 content">
                                <div class="create-collection p-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="recipient-name"
                                               placeholder="{{trans('global.Create-new-collection')}}"/>
                                        <button id="create_collection" data-collection=""
                                                class="btn btn-primary">{{__('misc.Submit')}}</button>
                                    </div>
                                </div>
                                <ul id="myCollections" class="list-unstyled"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('includes.footer')
    </div>
</div>

@include('includes.javascript_general')
@yield('javascript')
@stack('javascript_navbar')
@if($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            @foreach ($errors->all() as $error)
            swal('{{ $error }}')
            @endforeach
        });
    </script>
@endif
@if(session()->has('notify'))
    <script>
        notify('{{ session()->get('notify')['message'] }}','{{ session()->get('notify')['status'] }}')
    </script>
@endif
@if(session()->has('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            notify('{{ session()->get('success') }}','success')
        });
    </script>
@endif
</body>
</html>
