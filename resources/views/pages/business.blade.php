@extends('app')
@section('title') {{ __('للأعمـــال') }} - @endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/business.css') }}">
<div class="business">
  <header>
    <div class="layer">
      <div class="content">
          <div class="container">
        <h1>{{ __('للأعمـــال') }}</h1>
        <p> {{ __('محتوى “عربستوك”، بخدمات وامكانات أكبر، خصيصاً لأصحاب الأعمال والشركات الكبيرة والجهات الحكومية') }} </p>
              </div>
      </div>
    </div>
  </header>
  <section class="imagine">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-12 col-md-5">
          <h3>{{ __('تخيل أكثر من') }}</h3>
          <img src="{{ asset('img/business/100k.png') }}" />
          <p> {{ __('صورة وفيديو وفيكتور تعكس هويتنا السعودية والعربية التي تلبي متطلبات السوق الإعلاني والانتاج') }} </p>
        </div>
        <div class="col-12 col-md-5">
          <h3>{{ __('ماذا تقدم لكم عربستوك') }}</h3>
          <p class="my-3 mt-4"> {{ __('محتويات رقمية سعودية وعربية جاهزة للتحميل وترخيص استخدامها في الأعمال التصميمية والإنتاجية من خلال المنصة الأكبر من نوعها عالمياً.') }} </p>
          <p> {{ __('مزايا صممت خصيصاً للأعمال تساعدك من تقديم انتاجية أكبر لفريقك الابداعي والتحكم بشكل أكبر من خلال حساب خاص لكل مستخدم.') }} </p>
          <div class="row representation-options mt-5">
            <div class="col-md-4 col-xs-12"> <a
                                class="px-3 nav-link font-weight-bold btn-block"
                                href="https://arabsstock.com/ar/photos"
                            > <i class="far fa-camera-alt mr-2"></i>{{ __('Images') }} </a> </div>
            <div class="col-md-4 col-xs-12"> <a
                                class="px-3 nav-link font-weight-bold btn-block"
                                href="https://arabsstock.com/ar/videos"
                            > <i class="far fa-video mr-2"></i>{{ __('Videos') }}</a
                            > </div>
            <div class="col-md-4 col-xs-12"> <a
                                class="px-3 nav-link font-weight-bold btn-block"
                                href="https://arabsstock.com/ar/vectors"
                            > <i class="far fa-tilde fa-lg mr-2"></i>{{ __('Vectors') }}</a
                            > </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="mediaTypes">
    <section class="mediaType d-none d-sm-block">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 content">
            <h2>{{ __('Images') }}</h2>
            <p> {{ __('حمل صور (JPG) عالية الجودة واستخدمها بدقة (300) بيكسل ومقاسات مناسبة للطباعة') }} </p>
            <ul>
              <li>{{ __('الصور الإعلانية') }}</li>
              <li>{{ __('صور الشخصيات المفرغة') }}</li>
              <li>{{ __('الصور التصميمية') }}</li>
              <li>{{ __('صور المعالم والمدن') }}</li>
              <li>{{ __('صور الأحداث') }}</li>
            </ul>
          </div>
          <div class="col-md-6 img-container"> <img
                                src="{{ asset('img/business/image-side.png') }}"
                                alt="" class="img-fluid"
                            /> </div>
        </div>
      </div>
    </section>
    <!-- Visible only on xs -->
      <section class="mediaType d-block d-sm-none">
      <div class="container">
        <div class="row justify-content-center">
                      <div class="col-md-6 img-container"> <img
                                src="{{ asset('img/business/image-side.png') }}"
                                alt="" class="img-fluid"
                            /> </div>

          <div class="col-md-6 content">
            <h2>{{ __('Images') }}</h2>
            <p> {{ __('حمل صور (JPG) عالية الجودة واستخدمها بدقة (300) بيكسل ومقاسات مناسبة للطباعة') }} </p>
            <ul>
              <li>{{ __('الصور الإعلانية') }}</li>
              <li>{{ __('صور الشخصيات المفرغة') }}</li>
              <li>{{ __('الصور التصميمية') }}</li>
              <li>{{ __('صور المعالم والمدن') }}</li>
              <li>{{ __('صور الأحداث') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <!-- End Visible only on xs -->

    <section class="mediaType mediaType-video" style="background: #f6f6f6;">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 img-container"> <img
                                src="{{ asset('img/business/video-side.png') }}"
                                alt="" class="img-fluid"
                            /> </div>
          <div class="col-md-6 content">
            <h2>{{ __('Video') }}</h2>
            <p> {{ __('حمل فيديوهات (MP4) عالية الجودة واستخدمها بدقة تصل الى (8K) وبعدد أطر يصل الى (120) فريم') }} </p>
            <ul>
              <li>{{ __('فيديوهات إعلانية') }}</li>
              <li>{{ __('فيديوهات الكروما') }}</li>
              <li>{{ __('فيديوهات 3D') }}</li>
              <li>{{ __('فيديوهات المدن والمعالم') }}</li>
              <li>{{ __('فيديوهات الأحداث') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <section class="mediaType d-none d-sm-block">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 content">
            <h2>{{ __('Vectors') }}</h2>
            <p> {{ __('حمل رسومات فيكتور (EPS) عالية الجودة واستخدمها بالحجم الذي تريد وتحكم بأجزائها لتناسب أعمالك.') }} </p>
            <ul>
              <li>{{ __('رسومات الشخصيات') }}</li>
              <li>{{ __('المخطوطات العربية') }}</li>
              <li>{{ __('الأيقونات العربية') }}</li>
              <li>{{ __('رسومات المدن والمعالم') }}</li>
              <li>{{ __('رسومات إعلانية') }}</li>
            </ul>
          </div>
          <div class="col-md-6 img-container"> <img
                                src="{{ asset('img/business/vector-side.png') }}"
                                alt="" class="img-fluid"
                            /> </div>
        </div>
      </div>
    </section>
                <!-- Visible only on xs -->
      <section class="mediaType mediaType-video d-block d-sm-none">
      <div class="container">
        <div class="row justify-content-center">
                      <div class="col-md-6 img-container"> <img
                                src="{{ asset('img/business/vector-side.png') }}"
                                alt="" class="img-fluid"
                            /> </div>

          <div class="col-md-6 content">
            <h2>{{ __('Vectors') }}</h2>
            <p> {{ __('حمل رسومات فيكتور (EPS) عالية الجودة واستخدمها بالحجم الذي تريد وتحكم بأجزائها لتناسب أعمالك.') }} </p>
            <ul>
              <li>{{ __('رسومات الشخصيات') }}</li>
              <li>{{ __('المخطوطات العربية') }}</li>
              <li>{{ __('الأيقونات العربية') }}</li>
              <li>{{ __('رسومات المدن والمعالم') }}</li>
              <li>{{ __('رسومات إعلانية') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <!-- End Visible only on xs -->


  </div>
  <div class="explore">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-11">
          <h2>{{ __('اكتشف مزايا باقات الأعمال') }}</h2>
          <p> {{ __('ساعد فريقك الإبداعي لإنتاج الأعمال بأفضل الحلول، الجميع يبحث عن محتوى عربي سعودي، الحل عند عربستوك فقط.') }} </p>
        </div>
      </div>
      <div class="row justify-content-center items">
        <div class="col-md-3"> <img src="{{ asset('img/business/muti-images.svg') }}" alt="" class="img-fluid" />
          <p>{{ __('حمل ملفات أكثر مع باقات تناسب حجم أعمالك') }}</p>
        </div>
        <div class="col-md-3"> <img src="{{ asset('img/business/multi-users.svg') }}" alt="" class="img-fluid" />
          <p>{{ __('امكانية تعدد الحسابات باحصائيات خاصة لكل موظف') }}</p>
        </div>
        <div class="col-md-3"> <img src="{{ asset('img/business/checkmark.svg') }}" alt="" class="img-fluid" />
          <p>{{ __('حمل كافة الملفات بالترخيص “المحسن” لاستخدام أكثر أمان') }}</p>
        </div>
        <div class="col-md-3"> <img src="{{ asset('img/business/gear.svg') }}" alt="" class="img-fluid" />
          <p>{{ __('دعم وتسهيلات قانونية خاصة لحسابات الأعمال') }}</p>
        </div>
      </div>
    </div>
  </div>
  <div class="request-service">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-5 content">
          <h2>{{ __('اطلب الآن باقات الأعمال') }}</h2>
          <p>{{ __('كبرى القطاعات الحكومية والخاصة تستخدم عربستوك..') }}</p>
        </div>
        <div class="col-md-5 cta">
          <button
                            class="btn btn-lg btn-outline-primary hover:btn-secondary btn-lg font-weight-bold subscribe_btn"
                            type="button"
                            onclick="send_ticket(this)"
                        > {{ __('اطلب الآن') }} </button>
        </div>
      </div>
      <form action="{{ route('business') }}" class="mt-5" method="post" id="businessForm">
        <input type="hidden" name="_recaptcha">
        @csrf
        <div class="row justify-content-center">
          <div class="col-12 row mb-4">
            <div class="col-lg-10 mx-auto">
              <div id="wrap_validation"></div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              <label> {{ __('users.name') }} * </label>
              <div class="input-with-gray">
                <input
                                        type="text"
                                        value=""
                                        name="name"
                                        title="name"
                                        required
                                        class="form-control"
                                    />
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              <label> {{ __('إسم الشركة') }} * </label>
              <div class="input-with-gray">
                <input
                                        type="text"
                                        value=""
                                        name="entity"
                                        title="requestorName"
                                        required
                                        class="form-control"
                                    />
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              <label> {{ __('global.phone') }} * </label>
              <div class="input-with-gray">
                <input
                                        type="number"
                                        value=""
                                        name="mobile"
                                        title="mobile"
                                        required
                                        class="form-control"
                                    />
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              <label> {{ __('auth.email') }} * </label>
              <div class="input-with-gray">
                <input
                                        type="email"
                                        value=""
                                        name="email"
                                        title="email"
                                        required
                                        class="form-control"
                                    />
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('javascript_navbar')
<script src="https://www.google.com/recaptcha/api.js?render={{ env('reCAPTCHA_site_key')}}"></script>
<script>
        function send_ticket(btn) {
            var form = $("#businessForm");
            var url = form.attr('action');
            var isValid = $("#businessForm")[0].checkValidity();

            if (!isValid) {
                $("#businessForm")[0].reportValidity();
                return false;
            }
            var errors = '';
            $(btn).attr('disabled',true)
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('app.recaptcha.site_key')}}', {action: 'business'}).then(function (token) {
                    $('#businessForm input[name="_recaptcha"]').val(token);
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: form.serialize(), // serializes the form's elements.
                        success: function (data) {
                            $(btn).attr('disabled',false);
                            form.trigger("reset");
                            $('#businessForm #wrap_validation').html('<p class="alert alert-success"> ' + data.message + ' </p>');
                        },
                        error: function (err) {
                            $(btn).attr('disabled',false);
                            $('#businessForm #wrap_validation').html('');
                            var errors = '<div class="alert alert-danger"><ul>';
                            if (err.status == 422) {
                                $.each(err.responseJSON.errors, function (index, value) {
                                    errors += '<li>' +
                                        '' + value[0] + '</i>' +
                                        '</li> ';
                                });
                            } else {
                                errors += " There Problem , try again ";
                            }
                            errors += '</ul></div>';
                            $('#businessForm #wrap_validation').append(errors);
                        },
                    });
                });
            });
        }
    </script>
@endpush
