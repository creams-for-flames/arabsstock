<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title> عربستوك Arabsstock محتوى رقمي عربي خليجي – صور وفيديو وفيكتور</title>
    <link rel="canonical" href="{{ route('partners') }}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('img/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg') }}" color="#20d899">
    <link rel="stylesheet" href="{{ asset('css/bootstrap@5.2.3_dist_css_bootstrap.rtl.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.13.0/css/all.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap');
        body, html{font-family: Cairo, sans-serif;font-weight: 600;color: #30354b !important;font-size: 13px !important;line-height: 1.5;letter-spacing: 0;height: 100%}
        header{
            box-shadow: 0 2px 4px 0 rgba(12, 18, 28, .12);
        }
        .logo{
            min-width: 140px;
            max-width: 150px;
            margin-top: auto;
            max-height: 24px;
        }
        @media screen and (min-width: 992px){
            header .navbar-light .navbar-nav.home-pages-list li a.nav-link{line-height: 15px;background: #eee;margin: 0 5px;border-radius: 15px;padding-left: 1rem;padding-right: 1rem}
        }
        header .navbar-light .navbar-nav .nav-link{color: #30354b;font-size: 13px;line-height: 50px}
        header .navbar-light .navbar-nav .nav-link.color-primary{color: #20d598}
        @-moz-document url-prefix(){
            header .navbar-light .navbar-nav .nav-link{display: block ruby}
            .all-browser{display: none}
            .fox-browser{display: ruby}
        }

        @media screen and (max-width: 767px){
            header .navbar-light .navbar-nav .nav-link{line-height: unset}
        }
        .page-link{
            color: #20d598;
        }
        .page-link:hover{
            color: #12a373;
        }
        .active > .page-link, .page-link.active{
            color: #ffffff;
            background-color: #20d598;
            border-color: #20d598;
        }
        .page-link:focus{
            box-shadow: none;
        }
        div.dataTables_processing > div:last-child > div{
            position: absolute;
            top: 0;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #20d598;
            animation-timing-function: cubic-bezier(0, 1, 1, 0);
        }
    </style>
</head>
<body translate="no">
<div class="p-10" dir="rtl">
    <header class="default-header">
        <div class="container-fluid px-3">
            <div class="d-flex justify-content-between">
                <div class="mt-3">
                    <a class="navbar-brand d-inline-block" href="{{ url('ar') }}">
                        <img src="{{ asset('img/logowa.svg') }}" alt="عربستوك" class="logo defult-logo">
                    </a>
                    <div class="navbar navbar-expand-lg navbar-light d-inline-block">
                        <ul class="navbar-nav ms-auto home-pages-list">
                            <li class="nav-item">
                                <a class="nav-link fw-bold" href="{{ url('ar/photos') }}">
                                    <i class="far fa-camera-alt ms-2"></i>الصور </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" href="{{ url('ar/videos') }}">
                                    <i class="far fa-video ms-2"></i>الفيديو</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" href="{{ url('ar/vectors') }}">
                                    <i class="far fa-tilde fa-lg ms-2"></i>فيكتور</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="">
                    <div class="navbar navbar-expand-lg navbar-light">
                        <div class="collapse navbar-collapse d-block" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('ar/photos/categories') }}">كل التصنيفات
                                    </a>
                                </li>
                                <li class="nav-item dropdown text-capitalize prices">
                                    <a class="nav-link color-primary" href="{{ url('ar/plans') }}">
                                        الباقات والأسعار
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="h-20 mb-5"></div>
    <div class="container pb-5">
        <div class="mb-3">
            <form action="/action_page.php" method="get" autocomplete="chrome-off">
                <div class="row">
                    <div class="col">
                        <input type="search" class="form-control search" placeholder="بحث" name="q" id="search"
                               role="presentation"
                               autocomplete="off"
                        >
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control search" placeholder="تاريخ"
                                   id="datepicker" autocomplete="off" name="date">
                            <span class="input-group-text" id="basic-addon1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-calendar3" viewBox="0 0 16 16">
  <path
      d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
  <path
      d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
</svg>
                            </span>
                        </div>
                    </div>
                    <div class="col">
                        <select name="type" id="type" class="form-control search">
                            <option value="0">الكل</option>
                            <option value="{{ \App\Models\Image::class }}">الصور</option>
                            <option value="{{ \App\Models\Video::class }}">الفيديو</option>
                            <option value="{{ \App\Models\Vector::class }}">الفكتور</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <table id="table" class="table table-striped dt-responsive nowrap" style="width:100%">
            <thead>
            <tr class="text-center">
                <th class="bg-slate-50 p-3 border-bottom border-slate-300">#</th>
                <th class="bg-slate-50 p-3 border-bottom border-slate-300">الصورة</th>
                <th class="bg-slate-50 p-3 border-bottom border-slate-300">السعر</th>
                <th class="bg-slate-50 p-3 border-bottom border-slate-300">التاريخ</th>
                <th class="bg-slate-50 p-3 border-bottom border-slate-300">عدد النقاط</th>
                <th class="bg-slate-50 p-3 border-bottom border-slate-300">الايميل</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript"
        src="https://cdn.datatables.net/v/bs4/dt-1.13.1/r-2.4.0/sc-2.0.7/sl-1.5.0/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    var datatable = $('#table').DataTable({
        searchDelay: 500,
        processing: true,
        serverSide: true,
        scrollY: '70vh',
        scrollCollapse: true,
        "searching": false,
        ajax: {
            url: "{{ route('partners') }}",
            data: function (d) {
                d.q = $('input[name="q"]').val(),
                    d.date = $('input[name="date"]').val(),
                    d.type = $('select[name="type"]').val()
            }
        },
        "language": @json(__('datatable')),
        order: [[0, 'desc']],
        columns: [
            {
                className: "text-center",
                data: 'id',
                name: 'id',
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                className: "text-center",
                data: 'id',
                name: 'id',
                render: function (data, type, full, meta) {
                    if (full.status == 'deleted')
                        return `<a href="javascript:;" target="_blank"><img height="70" src="{{ asset('img-category/default.png') }}" alt="deleted images" title="deleted image"></a>`;
                    if (full.purchaseable_type == "App\\Models\\Vector")
                        return `<a href="{{ config('app.url') }}` + ('/vectors/illustration-{id}'.replace('{id}', full.purchaseable_id)) + `" target="_blank"><img height="70" src="{{ cdn('')}}/` + full.purchaseable.preview + `" alt="img"></a>`;
                    if (full.purchaseable_type == "App\\Models\\Video")
                        return `<a href="{{ config('app.url') }}` + ('/videos/clip-{id}'.replace('{id}', full.purchaseable_id)) + `" target="_blank"><img height="70" src="{{ cdn('')}}/` + full.purchaseable.preview + `" alt="img"></a>`;
                    if (full.purchaseable_type == "App\\Models\\Image")
                        return `<a href="{{ config('app.url') }}` + ('/photos/image-{id}'.replace('{id}', full.purchaseable_id)) + `" target="_blank"><img height="70" src="{{ cdn('')}}/` + full.purchaseable.preview + `" alt="img"></a>`;
                    return '';
                }
            },
            {
                className: "text-center",
                data: 'unit_price',
                render: function (data, type, full, meta) {
                    return data + ' $';
                }
            }, {
                className: "text-center",
                data: 'created_at',
            }, {
                className: "text-center",
                data: 'id',
                render: function (data, type, full, meta) {
                    return full.download ? full.download.credits : '-';
                }
            }, {
                className: "text-center",
                data: 'user_id',
                render: function (data, type, full, meta) {
                    return full.user ? full.user.email : 'Deleted';
                }
            },
        ],
    });
    // $('#search').on( 'keypress', function () {
    //     var $val=$(this).val()
    //     datatable.search('abc').draw()
    // } );
    $(function () {
        $('#datepicker').datepicker();
    });


    $(".search").on('keyup change', function () {
        datatable.draw();
    });
</script>
</body>
</html>
