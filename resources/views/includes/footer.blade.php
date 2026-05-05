<footer class="page-footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-sm-12 col-md-3 col-lg-4 mt-md-0 mt-3">
        @if(app()->getLocale() == 'ar')
        <img src="{{ asset('img/logoda.svg') }}" alt="عربستوك" width="140" height="40"  class="logo-footer mb-1 pb-2">
            @else
            <img src="{{ asset('img/logode.svg') }}" alt="Arabsstock" width="140" height="40"  class="logo-footer" style="margin-bottom: 26px;">
            @endif
        <p>{{ trans('global.welcome_subtitle') }}</p>
      </div>
      <div class="col-12 col-sm-12 col-md-7 col-lg-6 mb-md-0 mb-3">
        <h5 class="text-uppercase mb-4 mt-0">{{ trans('global.quick_Links') }}</h5>
        <ul class="footer-bottom-social list-inline">
         <li class="list-inline-item mr-2">
            <a href="{{route('page.show','about')}}">{{ trans('global.about_us') }}</a>
          </li>
          <li class="list-inline-item mr-2">
            <a href="{{route('page.show','terms-of-service')}}">{{ trans('global.Terms_and_Conditions') }}</a>
          </li>
          <li class="list-inline-item mr-2">
            <a href="{{route('page.show','privacy')}}">{{ trans('global.Privacy') }}</a>
          </li>
           <li class="list-inline-item mr-2">
            <a href="{{route('page.show','license-agreement')}}">{{ trans('global.license-agreement') }}</a>
          </li>
          <li class="list-inline-item mr-2">
            <a href="{{route('technical-support')}}">{{ trans('global.Technical_support') }}</a>
          </li>
          <li class="list-inline-item mr-2">
            <a href="{{route('model-form')}}">{{ trans('global.Casting_registration') }}</a>
          </li>
          <li class="list-inline-item mr-2">
            <a href="https://contributor.arabsstock.com" target="_blank">{{ trans('global.Share_your_works') }}</a>
          </li>
        </ul>
      </div>
      <div class="col-12 col-sm-12 col-md-2 col-lg-2 mb-md-0 mb-3">
        <h5 class="text-uppercase mb-4 mt-0">{{ trans('global.Follow_us') }}</h5>
        <ul class="footer-bottom-social list-inline">
            <li class="list-inline-item"><a href="https://fb.com/ArabsStock" target="_blank"><i class="fab fa-facebook"></i></a></li>
            <li class="list-inline-item"><a href="https://twitter.com/ArabsStock" target="_blank"><i class="fab fa-twitter"></i></a></li>
            <li class="list-inline-item"><a href="https://www.instagram.com/arabsstock/" target="_blank"><i class="fab fa-instagram"></i></a></li>
            <li class="list-inline-item"><a href="https://www.linkedin.com/company/arabsstock" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
            <li class="list-inline-item"><a href="https://www.pinterest.com/arabsstock/" target="_blank"><i class="fab fa-pinterest-p"></i></a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-copyright text-center py-3"> <span> © </span> <?php echo date('Y'); ?>  {{trans('global.copy')}}</div>
</footer>
<a href="#" id="back-to-top" title="Back to top"><i class="fal fa-arrow-up"></i></a>

@push('css')

<style>
    .footer-content {
        display: -ms-grid;
        display: grid;
    }
    @media (max-width: 1199px) {
        .footer-content {
            display: -ms-grid;
            display: grid;
            -ms-grid-columns: 1fr 0px 1fr;
            grid-template-columns: repeat(2, 1fr);
            -ms-grid-rows: 1fr 0px 1fr;
            grid-template-rows: repeat(2, 1fr);
            grid-column-gap: 0px;
            grid-row-gap: 0px;
        }
        .footer-content > *:nth-child(1) {
                -ms-grid-row: 1;
                -ms-grid-column: 1;
        }
        .footer-content > *:nth-child(2) {
                -ms-grid-row: 1;
                -ms-grid-column: 3;
        }
        .footer-content > *:nth-child(3) {
                -ms-grid-row: 3;
                -ms-grid-column: 1;
        }
        .footer-content > *:nth-child(4) {
                -ms-grid-row: 3;
                -ms-grid-column: 3;
        }
        .logo-side { -ms-grid-row: 1; -ms-grid-row-span: 1; -ms-grid-column: 1; -ms-grid-column-span: 1; grid-area: 1 / 1 / 2 / 2; }
        .follow-us  {
            -ms-grid-row: 1;
            -ms-grid-row-span: 1;
            -ms-grid-column: 2;
            -ms-grid-column-span: 1;
            grid-area: 1 / 2 / 2 / 3;
            -webkit-box-ordinal-group: 3;
                -ms-flex-order: 2;
                    order: 2
        }
        .quick-links {
            -ms-grid-row: 2;
            -ms-grid-row-span: 1;
            -ms-grid-column: 1;
            -ms-grid-column-span: 2;
            grid-area: 2 / 1 / 3 / 3;
            -webkit-box-ordinal-group: 4;
                -ms-flex-order: 3;
                    order: 3
        }
    }
    @media (min-width: 1200px) {
        .footer-content {
            -ms-grid-columns: 18rem 1fr 14rem;
            grid-template-columns: 18rem 1fr 14rem;
        }
    }
    @media (min-width: 1400px) {
        .footer-content {
            -ms-grid-columns: 25rem 1fr 25rem;
            grid-template-columns: 25rem 1fr 25rem;
        }
    }

    .as-mb-26{margin-bottom: 26px;}
    .as-follow-us-container{width: 12rem; margin: 0 auto 0 0;}
</style>
@endpush