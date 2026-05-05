<!DOCTYPE html>
<html lang="en">
<head>
    <title>عربستوك - الممثلين</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h2>الممثلين</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>صور</th>
            <th>الاسم</th>
            <th>جوال</th>
            <th>المدينة</th>
            <th>العمر</th>
            <th>تاريخ التسجيل</th>
        </tr>
        </thead>
        <tbody>
        @foreach($results as $r)
            <tr>
                <td>
                    @foreach($r->images as $img)
                        <a target="_blank" href="{{ $img->image }}"><img src="{{ $img->image }}" alt="" height="100"></a>
                    @endforeach
                </td>
                <td>{!! $r->name !!}</td>
                <td><a href="tel:{{ $r->mobile }}">{!! $r->mobile !!}</a></td>
                <td>{!! optional($r->getRelation('city'))->name_ar !!}</td>
                <td>{!! $r->age !!}</td>
                <td>{!! $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('Y-m-d') : '#' !!}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
