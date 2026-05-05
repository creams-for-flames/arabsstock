<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title> عربستوك Arabsstock محتوى رقمي عربي خليجي – صور وفيديو وفيكتور</title>
    <link rel="canonical" href="{{ route('partners.subscriptions') }}"/>
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
        *{
            text-align: center !important;
        }
        .childTable{
            width: 100%;
            font-weight: bold;
        }
        #table tbody tr{
            height: 50px; /* Adjust the desired row height here */
        }

        #table tbody td{
            padding: 10px; /* Adjust the desired padding within each cell */
        }
        .dataTables_length{
            text-align: right !important;
        }
        .dataTables_filter{
            display: none;
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
                    <div class="col-lg-4">
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
                </div>
            </form>
        </div>
        <table id="table" class="table table-striped- table-bordered dt-responsive nowrap display" style="width:100%">
            <thead>
            <tr>
                <th>التاريخ</th>
                <th>اجمالي المبلغ</th>
                <th>عدد الاشتراكات</th>
                <th>تفاصيل</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript"
        src="https://cdn.datatables.net/v/bs4/dt-1.13.1/r-2.4.0/sc-2.0.7/sl-1.5.0/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    let plan_types = {
        "monthly": "{{ __('monthly') }}",
        "month": "{{ __('month') }}",
        "annual": "{{ __('annual') }}",
        "package": "{{ __('global.package') }}",
    }

    $(function () {
        $('#datepicker').datepicker();
    });
    var parentTable;
    $(document).ready(function () {
        parentTable = $('#table').DataTable({
            ajax: {
                url: '{{ route('partners.subscriptions',['grouped'=>1]) }}',
                data: function (d) {
                    d.date = $('input[name="date"]').val()
                },
                dataSrc: '' // The property containing the data array in the AJAX response
            },
            searchDelay: 500,
            processing: true,
            // serverSide: true,
            scrollY: '70vh',
            scrollCollapse: true,
            // "searching": false,
            "language": @json(__('datatable')),
            order: [[0, 'desc']],
            columns: [
                {data: 'date'},
                {data: 'total'},
                {data: 'count'},
                {
                    data: null,
                    render: function (data, type, row) {
                        return '<i class="fas fa-plus expand-icon" data-date="' + row.date + '"></i>';
                    },
                    className: 'expand-control',
                    orderable: false
                }
            ]
        });
        $('#table tbody').on('click', '.expand-control', function () {
            var tr = $(this).closest('tr');
            var row = parentTable.row(tr), $this = $(this);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                $this.find('i').removeClass('fa-minus').addClass('fa-plus');
            } else {
                // Open this row
                $.get('{{ route('partners.subscriptions') }}?children=1&date=' + row.data().date, function (data) {
                    row.child(formatChildTable(data)).show();
                    tr.addClass('shown');
                    $this.find('i').removeClass('fa-plus').addClass('fa-minus');
                })

            }
        });
        // setTimeout(function () {
        //     $('[data-date="' + moment().format('YYYY-MM-DD') + '"]').closest('td').click()
        // }, 2000);
        $(".search").on('keyup change', function () {
            var $val = $(this).val();
            if ($val)
                parentTable.column(0).search(moment($val, 'DD/MM/YYYY').format('YYYY-MM-DD')).draw()
            else
                parentTable.search('').columns().search('').draw()
        });

        function formatChildTable(data) {
            var childContainer = $('<div>').addClass('border border-1 border-dark')
            var childTable = $('<table>').addClass('childTable table table-striped dt-responsive p-0 mb-0')
            childTable.append('<thead><tr class="text-center"><th class="bg-slate-50 p-3 border-bottom border-slate-300">السعر</th><th class="bg-slate-50 p-3 border-bottom border-slate-300">الوقت</th><th class="bg-slate-50 p-3 border-bottom border-slate-300">الباقة</th><th class="bg-slate-50 p-3 border-bottom border-slate-300">نوع الباقة</th><th class="bg-slate-50 p-3 border-bottom border-slate-300">العميل</th></tr></thead>');

            var childTableBody = $('<tbody>');

            // Iterate over the nested data and add rows to the child table
            $.each(data, function (index, r) {
                var row = $('<tr>');
                row.append($('<td>').text(r.amount + '$'));
                row.append($('<td>').text(moment(r.created_at).format('HH:mm:ss')));
                row.append($('<td>').text(r.plan.description));
                row.append($('<td>').text(plan_types[r.plan_type]));
                row.append($('<td>').text(r.user.name));
                childTableBody.append(row);
            });

            childTable.append(childTableBody);
            childContainer.append(childTable)
            return childContainer;
        }
    });
</script>
</body>
</html>
