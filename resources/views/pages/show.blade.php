@extends('app')
@section('title')
    @if(App::isLocale('en'))
        {{ $page->title_en.' - ' }}
    @else
        {{ $page->title_ar.' - ' }}
    @endif

@endsection
@section('description_custom')
    @if(App::isLocale('en'))
        {{ $page->title_en.' - ' }}
    @else
        {{ $page->title_ar.' - ' }}
    @endif
@endsection
@push('javascript_navbar')
    <style>
        .whoWeAre header {
            background-image: url('../assets/header.png');
            background-position: center;
            background-size: cover;
            height: 500px;
            display: flex;
            color: #fff;
            justify-content: center;
            position: relative;
            position: relative;
            padding: 0;
            align-items: center;
        }
        .whoWeAre header .layer {
            position: absolute;
            top: 0;
            left: 0;
            background-color: #0605058c;
            width: 100%;
            height: 100%;
        }
        .headerLead {
            font-size: 1.3rem !important;
            text-align: center;
        }
        .whoWeAre header video {
            object-fit: cover;
            height: 100%;
        }
        @media (max-width: 500px) {
            .whoWeAre header {
                object-fit: cover;
                max-height: 100%;
            }
        }
        .whoWeAre header > .container {
            position: absolute;
        }
        .whoWeAre .articles .block {
            background-position: center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 230px;
            border-radius: 10px;
            padding: 0 !important;
            margin-bottom: 20px;
            position: relative;
        }
        .whoWeAre .articles .blockCol:nth-of-type(1) .block {
            background-image: url('{{asset('about/1.png')}}');
        }
        .whoWeAre .articles .blockCol:nth-of-type(2) .block {
            background-image: url('{{asset('about/2.png')}}');
        }
        .whoWeAre .articles .blockCol:nth-of-type(3) .block {
            background-image: url('{{asset('about/3.png')}}');
        }
        .whoWeAre .articles .blockCol:nth-of-type(4) .block {
            background-image: url('{{asset('about/4.png')}}');
        }
        .whoWeAre .articles .blockCol:nth-of-type(5) .block {
            background-image: url('{{asset('about/5.png')}}');
        }
        .whoWeAre .articles .blockCol:nth-of-type(6) .block {
            background-image: url('{{asset('about/6.png')}}');
        }
        .whoWeAre .articles .block a {
            color: #fff;
            position: relative;
            padding: 10px;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .whoWeAre .articles .block a:hover {
            transition: ease all 0.3s;
            color: #20d598;
        }
        .whoWeAre .articles .block .shadow {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            border-radius: 10px;
            height: 100%;
            background-color: #00000094;
        }
        .whoWeAre .articles h2.title {
            margin-top: 80px;
            margin-bottom: 30px;
        }
    </style>
@endpush
@section('content')
    @if($page->slug == 'about')
        <div class="whoWeAre">
            <header>
                <div class="layer"></div>
                <video autoplay muted loop id="myVideo">
                    <source src="{{asset('about/240_P_9m.mp4')}}" type="video/mp4">
                </video>
                <div class="container">
                    <h3 class="font-weight-light headerLead">
                        <?php
                        if (App::isLocale('en'))
                            echo html_entity_decode($page->content_en); else echo html_entity_decode($page->content_ar); ?>
                    </h3>
                </div>
            </header>
            <section class="container articles">
                <h2 class="h1 bold title">مقالات</h2>
                <div class="row mb-5">
                    <div class="col-12 col-md-6 col-lg-4 blockCol">
                        <div class="block">
                            <div class="shadow"></div>
                            <a href="https://www.arabnews.com/node/1691981/saudi-arabia" target="_blank">
                                <h3 class="bold mt-0 mb-4">عرب نيوز</h3>
                                <h3 class="bold m-0">
                                    تقدم مكتبة الوسائط الرقمية الجديدة محتوى عربي "حقيقي" للاستخدام عبر الإنترنت
                                </h3>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 blockCol">
                        <div class="block">
                            <div class="shadow"></div>
                            <a href="https://sabq.org/saudia/ymbtgq" target="_blank">
                                <h3 class="bold mt-0 mb-4">سبق</h3>
                                <h3 class="bold m-0">
                                    عربستوك Arabsstock تفتح الفرصة لكل المصورين والمبدعين لعرض وبيع صورهم للعالم
                                </h3>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 blockCol">
                        <div class="block">
                            <div class="shadow"></div>
                            <a href="https://al-marsd.com/article/16455/آخر%20الأخبار/عربستوك-تعلن-عن-فرصة-ذهبية-للراغبين-في-التمثيل-وتكشف-عن-الهدف-من-التسجيل-بها"
                               target="_blank">
                                <h3 class="bold mt-0 mb-4">المرصد</h3>
                                <h3 class="bold m-0">
                                    "عربستوك" تعلن عن فرصة ذهبية للراغبين في التمثيل.. وتكشف عن الهدف من التسجيل بها!
                                </h3>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 blockCol">
                        <div class="block">
                            <div class="shadow"></div>
                            <a href="https://twitter.com/tafa3olcom/status/1271101752725647361?s=21&t=Bz2CNaE5PuAQXTDJRtkG5Q"
                               target="_blank">
                                <h3 class="bold mt-0 mb-4">تفاعلكم العربية</h3>
                                <h3 class="bold m-0">
                                    خليجيون ليسوا خليجيين !!
                                    @ArabsStock
                                    تحاول حل المشكلة.
                                </h3>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 blockCol">
                        <div class="block">
                            <div class="shadow"></div>
                            <a href="https://twitter.com/studioekhbariy/status/1282932620402319360?s=21&t=TjcE3HO030QSlVwmgnn1qQ"
                               target="_blank">
                                <h3 class="bold mt-0 mb-4">برنامج اليوم</h3>
                                <h3 class="bold m-0">
                                    فيديو | عربستوك.. أكبر مكتبة رقمية عربية خليجية
                                </h3>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 blockCol">
                        <div class="block">
                            <div class="shadow"></div>
                            <a href="https://www.alriyadh.com/1936035" target="_blank">
                                <h3 class="bold mt-0 mb-4">الرياض</h3>
                                <h3 class="bold m-0">
                                    عربستوك Arabsstock تفتح الفرصة لكافة المصورين والمبدعين لعرض وبيع صورهم على المنصة
                                    للعالم
                                </h3>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <section class="banner-footer">
            <div class="container">
                <h2 class="color-white mb-5">{{ trans('global.Thousands of digital content and themes, and more') }}  </h2>
                <a href="{{route('plans')}}"
                   class="btn btn-primary btn-lg"> {{ trans('global.Discover-packages-prices') }} </a>
            </div>
        </section>
        @include('includes.newsletter')
    @else
        <div class="search-header jumbo-banner" data-overlay="6" style="">
            <div class="container-fluid">
                <div class="col-lg-12 col-md-12">
                    <h1 class="title-site">
                        @if(app()->getLocale()=='en') {{ $page->title_en }} @else {{ $page->title_ar }} @endif
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <!-- Col MD -->
            <div class="mt-5 mb-5">
                <p class="text-justify content-page-p">
                    <?php
                    if (App::isLocale('en'))
                        echo html_entity_decode($page->content_en); else echo html_entity_decode($page->content_ar); ?>
                </p>
            </div>
            <!-- /COL MD -->
        </div>
    @endif
    <!-- container wrap-ui -->
@endsection
