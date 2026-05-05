<?php
if (isset($is_videos_site)) {
    $lang_route = 'admin.videos.dashboard.lang';
    $dashboard_index = route('admin.videos.dashboard.index');
    $logout_url = route('admin.videos.logout');
} elseif (isset($is_vectors_site)) {
    $lang_route = 'admin.dashboard.lang';
    $dashboard_index = route('admin.vector.dashboard.index');
    $logout_url = route('admin.vectors.logout');
} elseif (isset($is_super_site)) {
    $lang_route = 'admin.dashboard.lang';
    $dashboard_index = route('admin.super.contact.index');
    $logout_url = route('admin.super.logout');

} else {
    $lang_route = 'admin.dashboard.lang';
    $dashboard_index = route('admin.dashboard.index');
    $logout_url = route('admin.logout');
}
?>
    <!DOCTYPE html>
<html lang="{{app()->getLocale()}}" @if(app()->getLocale() === "ar")direction="rtl" dir="rtl"
      style="direction: rtl" @endif>
<!-- begin::Head -->
<head>
    <base href="/">
    <meta charset="utf-8"/>
    <title>Dashboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <!--begin::Fonts -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
    <link href="https://fonts.googleapis.com/css?family=Cairo&display=swap&subset=arabic" rel="stylesheet">
    <!--end::Fonts -->
    <!--begin::Page Vendors Styles(used by this page) -->
    <link href="{{ asset('admin_assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}"
          rel="stylesheet" type="text/css"/>
    <!--end::Page Vendors Styles -->
    <!--begin::Global Theme Styles(used by all pages) -->
    @if(app()->getLocale() === "en")
        <link href="{{ asset('admin_assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet"
              type="text/css"/>
        <link href="{{ asset('admin_assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
    @else
        <link href="{{ asset('admin_assets/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet"
              type="text/css"/>
        <link href="{{ asset('admin_assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css"/>
        <style>
            html, body{
                font-family: 'Cairo', Poppins, Helvetica, sans-serif;
            }
            /* fix bug for kt-datatable__pager-size for rtl */
            .bootstrap-select.bs-container{
                left: 0;
                right: unset;
            }
            /* fix bug for kt-datatable__pager-size for rtl */
            select.form-control{
                padding-bottom: 4px;
            }
            ul.notification-list{
                overflow-y: auto !important;
                height: 300px !important;
            }
        </style>
    @endif


<!--end::Global Theme Styles -->
    <!--begin::Layout Skins(used by all pages) -->
    <!--end::Layout Skins -->
    <link rel="shortcut icon" href="admin_assets/media/logos/favicon.png"/>
    @stack('css')
</head>
<!-- end::Head -->
<!-- begin::Body -->
<body
    class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-page--loading">
<!-- begin:: Page -->
<!-- begin:: Header Mobile -->
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        <a href="{{ route('landPage') }}" target="_blank">
            @if(app()->getLocale() === "en")
                <img alt="Logo" src="admin_assets/media/logos/logo-text-en.png" style="width: 160px;">
            @else
                <img alt="Logo" src="admin_assets/media/logos/logo-text-ar.png" style="width: 160px;">
            @endif
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">
        <button class="kt-header-mobile__toolbar-toggler kt-header-mobile__toolbar-toggler--left"
                id="kt_aside_mobile_toggler"><span></span></button>
        <button class="kt-header-mobile__toolbar-topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                class="flaticon-more"></i></button>
    </div>
</div>
<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <!-- begin:: Aside -->
        <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
        <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
             id="kt_aside">
            <!-- begin:: Aside -->
            <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
                <div class="kt-aside__brand-logo">
                    <a href="{{ route('landPage') }}" target="_blank">
                        @if(app()->getLocale() === "en")
                            <img alt="Logo" src="admin_assets/media/logos/logo-text-en.png"
                                 style="width: 160px;">
                        @else
                            <img alt="Logo" src="admin_assets/media/logos/logo-text-ar.png"
                                 style="width: 160px;">
                        @endif
                    </a>
                </div>
                <div class="kt-aside__brand-tools">
                    <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler"><span></span></button>
                </div>
            </div>
            <!-- end:: Aside -->
            <!-- begin:: Aside Menu -->
            <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1"
                     data-ktmenu-dropdown-timeout="500">
                    <ul class="kt-menu__nav ">
                        <?php
                        $role = auth()->user()->role;
                        if ($role == 'admin_image_editor') {
                            $menu_list = get_admin_image_editor_menu_list();
                        } elseif ($role == 'admin_video_editor') {
                            $menu_list = get_admin_video_editor_menu_list();
                        } elseif ($role == 'admin_vector_editor') {
                            $menu_list = get_admin_vector_editor_menu_list();
                        } elseif ($role == 'admin_video') {
                            $menu_list = get_admin_videos_menu_list();
                        } elseif ($role == 'admin_vector') {
                            $menu_list = get_admin_vectors_menu_list();
                        } elseif ($role == 'accountant') {
                            $menu_list = get_accountant_menu_list();
                        } elseif ($role == 'designer') {
                            $menu_list = get_designer_menu_list();
                        }elseif (isset($is_models_site)) {
                            $menu_list = get_admin_models_menu_list();
                        } elseif (isset($is_super_site)) {
                            $menu_list = get_super_menu_list();
                        } else {
                            $menu_list = get_admin_menu_list();
                        }
                        ?>
                        @foreach($menu_list as $menu_item )
                            @if(isset($menu_item['children']) && count($menu_item['children']))

                                <li class="kt-menu__item kt-menu__item--submenu {{ $menu_item['is_active'] ? 'kt-menu__item--open kt-menu__item--here' : '' }}"
                                    aria-haspopup="true">
                                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle"><i
                                            class="kt-menu__link-icon {{$menu_item['icon']}}"></i>
                                        <span class="kt-menu__link-text">{{$menu_item['text']}}</span><i
                                            class="kt-menu__ver-arrow la la-angle-right"></i>
                                    </a>
                                    <div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
                                        <ul class="kt-menu__subnav">
                                            @foreach($menu_item['children'] as $menu_child )
                                                <li class="kt-menu__item {{ $menu_child['is_active'] ? 'kt-menu__item--active' : '' }} {{ @$menu_child['class'] }}"
                                                    aria-haspopup="true"><a href="{{$menu_child['url']}}"
                                                                            target="{{ @$menu_child['target'] }}"
                                                                            class="kt-menu__link "><i
                                                            class="kt-menu__link-icon {{$menu_child['icon']}}"><span></span></i><span
                                                            class="kt-menu__link-text"> {{$menu_child['text']}}</span></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>

                            @else
                                <li class="kt-menu__item {{ $menu_item['is_active'] ? 'kt-menu__item--active' : '' }} {{ @$menu_item['class'] }}"
                                    aria-haspopup="true">
                                    <a href="{{$menu_item['url']}}" class="kt-menu__link "><i
                                            class="kt-menu__link-icon {{$menu_item['icon']}}"></i>
                                        <span class="kt-menu__link-text">{{$menu_item['text']}}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- end:: Aside Menu -->
        </div>
        <!-- end:: Aside -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
            <!-- begin:: Header -->
            <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">
                <!-- begin: Header Menu -->
                <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i
                        class="la la-close"></i></button>
                <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
                    <div id="kt_header_menu"
                         class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
                        <ul class="kt-menu__nav "></ul>
                    </div>
                </div>
                <!-- end: Header Menu -->
                <!-- begin:: Header Topbar -->
                <div class="kt-header__topbar">
                {{-- dddddd --}}
                @if (isset($notifications))
                    <!--begin: notifY bar -->
                        <div class="kt-header__topbar-item kt-header__topbar-item--langs">
                            {{-- not --}}
                            <div class="kt-header__topbar-wrapper" data-toggle="dropdown"
                                 data-offset="10px,0px">
<span class="kt-header__topbar-icon">
<span
    class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">

<i class="fas fa-bell text-danger">
<sup>{{ isset($notifications)?$notifications['count']:0 }}</sup>
</i>
</span>
                            </div>
                            {{-- endnot --}}
                            <div
                                class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">
                                <ul class="kt-nav kt-margin-t-10 kt-margin-b-10 notification-list ">
                                    @foreach($notifications['data'] as $item )
                                        <li class="kt-nav__item ">
                                            <a href="{{ $item['review_url'] }}" class="kt-nav__link">
                                                <span class="kt-nav__link-text">{{$item['message']}}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                @endif

                <!--end: notifY bar -->
                {{-- dddddd --}}
                <!--begin: Language bar -->
                    <div class="kt-header__topbar-item kt-header__topbar-item--langs">
                        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
<span class="kt-header__topbar-icon">
<span
    class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">
@if(app()->getLocale() === "en")
        {{__('views.English_swicher')}}
    @else
        {{__('views.Arabic_swicher')}}
    @endif
</span>
                        </div>
                        <div
                            class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">
                            <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                                <?php
                                $lang_list = [
                                    'ar' => [
                                        'code' => 'ar',
                                        'text' => __('views.Arabic_swicher'),
                                    ],
                                    'en' => [
                                        'code' => 'en',
                                        'text' => __('views.English_swicher'),
                                    ],
                                ];
                                ?>
                                @foreach($lang_list as $lang_item )
                                    <li class="kt-nav__item {{$lang_item['code'] === app()->getLocale() ? 'kt-nav__item--active' : ''}}">
                                        <a href="{{ route($lang_route, $lang_item['code']) }}"
                                           class="kt-nav__link">
                                            <span class="kt-nav__link-text">{{$lang_item['text']}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!--end: Language bar -->
                    <!--begin: User Bar -->
                    <div class="kt-header__topbar-item kt-header__topbar-item--user">
                        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                            <div class="kt-header__topbar-user">
<span
    class="kt-header__topbar-welcome kt-hidden-mobile">{{__('views.Hi,')}}</span>
                                <span
                                    class="kt-header__topbar-username kt-hidden-mobile">{{auth()->user()->name}}</span>
                                <span
                                    class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">{{mb_substr(auth()->user()->name, 0, 1)}}</span>
                            </div>
                        </div>
                        <div
                            class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
                            <!--begin: Head -->
                            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x">
                                <div class="kt-user-card__avatar">
<span
    class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">{{mb_substr(auth()->user()->name, 0, 1)}}</span>
                                </div>
                                <div class="kt-user-card__name" style="color: #595d6e;">
                                    {{auth()->user()->email}}
                                </div>
                            </div>
                            <!--end: Head -->
                            <!--begin: Navigation -->
                            <div class="kt-notification">
                                <div class="kt-notification__custom kt-space-between">
                                    &nbsp;
                                    <a href="{{ route('admin.logout') }}"
                                       rel="nofollow">
                                        {{__('views.Sign Out')}}
                                    </a>
                                </div>
                            </div>
                            <!--end: Navigation -->
                        </div>
                    </div>
                    <!--end: User Bar -->
                </div>
                <!-- end:: Header Topbar -->
            </div>
            <!-- end:: Header -->
            <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                <!-- begin:: Subheader -->
                <div class="kt-subheader   kt-grid__item" id="kt_subheader">
                    <div class="kt-container  kt-container--fluid ">
                        <div class="kt-subheader__main">
                            @if(isset($html_breadcrumbs))
                                <h3 class="kt-subheader__title">{{$html_breadcrumbs['title']}}</h3>
                                <?php
                                $icon = collect($menu_list)->filter(function ($item) {
                                    return $item['is_active'];
                                })->flatMap(function ($items) {
                                    return $items;
                                })->get('icon', 'flaticon2-shelter');
                                ?>
                                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                                @if(isset($html_breadcrumbs['subtitle']))
                                    <div class="kt-subheader__breadcrumbs">
                                        <a href="javascript:;" class="kt-subheader__breadcrumbs-home"><i
                                                class="{{$icon}}"></i></a>
                                        <a href="javascript:;" class="kt-subheader__breadcrumbs-link">
                                            {{$html_breadcrumbs['subtitle']}} </a>
                                    </div>
                                @endif
                                @if(isset($html_breadcrumbs['datatable']))
                                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                                    <div class="kt-subheader__group" id="kt_subheader_search">
<span class="kt-subheader__desc"
      id="kt_subheader_total">{{__('views.:number Total', ['number' => '-'])}}</span>
                                        <form class="kt-margin-l-20" id="kt_subheader_search_form">
                                            <div
                                                class="kt-input-icon kt-input-icon--right kt-subheader__search">
                                                <input type="text" class="form-control"
                                                       placeholder="{{__('views.Search...')}}"
                                                       id="generalSearch">
                                                <span
                                                    class="kt-input-icon__icon kt-input-icon__icon--right"><span>
<i class="flaticon2-search-1"></i>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="kt-subheader__group kt-hidden" id="kt_subheader_group_actions">
                                        <div class="kt-subheader__desc"><span
                                                id="kt_subheader_group_selected_rows"></span> {{__('views.Selected')}}
                                        </div>
                                        <div class="btn-toolbar kt-margin-l-20">
                                            @if(isset($subheader_actions))
                                                @foreach($subheader_actions as $subheader_action)
                                                    @if($subheader_action['type'] === 'button')
                                                        <button
                                                            class="btn btn-label-danger btn-bold btn-sm btn-icon-h arabs_subheader_action_button"
                                                            data-action-url="{{$subheader_action['url']}}"
                                                            data-action-method="{{$subheader_action['method']}}"
                                                            data-action-confirm="{{$subheader_action['confirm']}}">
                                                            {{$subheader_action['text']}}
                                                        </button>
                                                    @else
                                                        <div class="dropdown arabs_subheader_action_dropdown">
                                                            <button type="button"
                                                                    class="btn btn-label-brand btn-bold btn-sm dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                {{$subheader_action['text']}}
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <ul class="kt-nav">
                                                                    @foreach($subheader_action['options'] as $subheader_action_option)
                                                                        <li class="kt-nav__item">
                                                                            <a href="javascript:;"
                                                                               class="kt-nav__link"
                                                                               data-action-value="{{$subheader_action_option['value']}}"
                                                                               data-action-url="{{$subheader_action_option['url']}}"
                                                                               data-action-method="{{$subheader_action_option['method']}}"
                                                                               data-action-confirm="{{$subheader_action_option['confirm']}}">
<span class="kt-nav__link-text"><span
        class="kt-badge {{$subheader_action_option['class']}} kt-badge--inline kt-badge--bold kt-badge--rounded">{{$subheader_action_option['text']}}</span></span>
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @else
                                <h3 class="kt-subheader__title">{{__('views.Dashboard')}}</h3>
                                <span class="kt-subheader__separator kt-hidden"></span>
                                <div class="kt-subheader__breadcrumbs">
                                    <a href="javascript:;" class="kt-subheader__breadcrumbs-home"><i
                                            class="flaticon2-shelter"></i></a>
                                    <span class="kt-subheader__breadcrumbs-separator"></span>
                                    <a href="" class="kt-subheader__breadcrumbs-link">
                                        {{__('views.Application')}}</a>
                                </div>
                            @endif
                        </div>
                        <div class="kt-subheader__toolbar">
                            <div class="kt-subheader__wrapper">
                                @yield('toolbar')
                                @if(isset($html_new_path) && $html_new_path)
                                    <a href="{{$html_new_path}}"
                                       class="btn btn-label-brand btn-bold"> {{__('views.New')}} </a>
                                @else
                                    <a href="javascript:;" class="btn kt-subheader__btn-daterange"
                                       id="kt_dashboard_daterangepicker" data-toggle="kt-tooltip" title=""
                                       data-placement="left">
<span class="kt-subheader__btn-daterange-title"
      id="kt_dashboard_daterangepicker_title">{{__('views.Today')}}</span>&nbsp;
                                        <span class="kt-subheader__btn-daterange-date"
                                              id="kt_dashboard_daterangepicker_date">{{date('Y-m-d')}}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end:: Subheader -->
                @yield('content')
            </div>
            <!-- begin:: Footer -->
            <div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
                <div class="kt-container  kt-container--fluid ">
                    <div class="kt-footer__copyright">
                        2019&nbsp;&copy;&nbsp;<a href="" target="_blank"
                                                 class="kt-link">{{__('views.Arabsstock')}}</a>
                    </div>
                    <div class="kt-footer__menu">
                    </div>
                </div>
            </div>
            <!-- end:: Footer -->
        </div>
    </div>
</div>
<!-- end:: Page -->
<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>
<div class="modal fade" id="exportStatisticsModal" tabindex="-1" role="dialog"
     aria-labelledby="exportStatisticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form action="{{ route('admin.statistics.export') }}" metho="get">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportStatisticsModalLabel">تصدير الاحصائيات</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="form-control-label">من</label>
                        <input type="text" class="form-control dpicker" name="from" autocomplete="off"
                               value="01-06-2020">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="form-control-label">الى</label>
                        <input type="text" class="form-control dpicker" name="to" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                    <button type="submit" class="btn btn-primary">تنزيل</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- end::Scrolltop -->
<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
    var KTAppOptions = {
        colors: {
            state: {
                brand: "#2c77f4",
                light: "#ffffff",
                dark: "#282a3c",
                primary: "#5867dd",
                success: "#34bfa3",
                info: "#36a3f7",
                warning: "#ffb822",
                danger: "#fd3995"
            },
            base: {
                label: ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                shape: ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
</script>
<!-- end::Global Config -->
<!--begin::Global Theme Bundle(used by all pages) -->
<script src="{{ asset('admin_assets/plugins/global/plugins.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin_assets/js/scripts.bundle.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/lodash@4.17.15_lodash.min.js') }}"
        integrity="sha256-VeNaFBVDhoX3H+gJ37DpT/nTuZTdjYro9yBruHjVmoQ=" crossorigin="anonymous"></script>
<script src="{{ asset('admin_assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('admin_assets/js/global.js') }}"></script>
<script>
    "use strict";

    var locales = {
        loading: '{{__('views.Loading...')}}',
    };

    var message = "{{Session::get('error')}}";
    message && swal.fire("Alert!", message, "error");
    message = "{{Session::get('success')}}";
    message && swal.fire("", message, "success");
    $('.export-statistics').on('click', function (e) {
        e.preventDefault();
        $('#exportStatisticsModal').modal('show')
    });

</script>
<!--end::Page Vendors -->
<!--begin::Page Scripts(used by this page) -->
@stack('scripts')
<!--end::Page Scripts -->
</body>
<!-- end::Body -->
</html>
