<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" media="all" href="{{asset('css/pdf.css')}}" />
</head>
<div class="container invoice">
    <div class="head-title">
        <img src="{{isset($data['logo'])?$data['logo']:asset('img/logowe.png')}}" width="300">
        <div class="mt-10">
            @isset($data['logo_text'])
                {{$data['logo_text']}}
            @endisset
    </div>

    <div class="add-detail mt-50">
        <div class="w-50 float-left logo mt-10">
            <div class="from">
                <strong>{{$data['from_label']}}</strong>
                <p class="m-0 p-0">
                    {{$data['company']}}
                </p>
                <p class="m-0 p-0">
                    {{$data['company_address_line1']}}
                </p>
            </div>

            <div class="for">
                <strong>{{$data['for_label']}}</strong>
                @if(@$data['client_is_business'])
                    @if($data['client_company_name'])
                        <p class="m-0 p-0">
                            {{$data['client_company_name']}}
                        </p>
                    @endif
                    @if(@$data['client_company_contact'])
                        <p class="m-0 p-5">
                            {{$data['client_company_contact']}}
                        </p>
                    @endif
                    @if(@$data['client_company_address'])
                        <p class="m-0 p-5">
                            {{$data['client_company_address']}}
                        </p>
                    @endif
                @else
                <p class="m-0 p-0">
                    {{$data['client']}}
                </p>
                <p class="m-0 p-5">
                    {{$data['client_contact_name']}}
                </p>
                @endif
                @if ($data['client_company_tax_id'] && $data['client_company_tax_id'] != '')
                <p class="m-0 p-5">
                    {{$data['tax_number_label']}} : {{$data['client_company_tax_id']}}
                </p>
                @endif
            </div>
        </div>
        <div class="w-50 float-left mt-10">
            <strong> {{$data['details_label']}} </strong>
            <div class="table-section bill-tbl w-100 mt-10 details">
                <table class="table w-100 mt-0">
                    <tr class="b-none">
                        <td>{{$data['invoice_label']}}</td>
                        <td style="text-align: right;">{{$data['invoice_id']}}</td>
                    </tr>
                    <tr class="b-none">
                        <td>{{$data['invoice_date_label']}}</td>
                        <td style="text-align: right;">{{$data['invoice_date']}}</td>
                    </tr>
                    <tr class="b-none">
                        <td style="width: 120px">{{$data['payment_method_label']}}</td>
                        <td style="text-align: right;">{{$data['payment_method']}}</td>
                    </tr>
                    <tr class="b-none">
                        <td>{{$data['payment_status_label']}}</td>
                        <td style="text-align: right;">{{$data['payment_status']}}</td>
                    </tr>
                    <tr class="b-none amount">
                        <td class="text-bold">{{$data['amount_due_label']}}</td>
                        <td class="text-bold" style="text-align: right;">{{$data['amount_due']}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50 text-left">{{$data['item_label']}}</th>
                <th class="w-50 text-right">{{$data['price_label']}}</th>
            </tr>
            @foreach ($data['items'] as $item)
            <tr>
                <td>{{$item['title']}}</td>
                <td style="text-align: right;">{{$item['amount']}}</td>
            </tr>

            @endforeach
        </table>
    </div>

    <div>
        <div style="margin: 10px 10px 10px auto; width: 30%">
            <table class="table w-100 mt-0">
                <tbody><tr class="b-none">
                    <td> <b>{{$data['total_label']}} </b> </td>
                    <td style="text-align: right;"> {{$data['total']}} </td>
                </tr>
                <tr class="b-none">
                    <td> <b>{{$data['amount_paid_label']}} </b></td>
                    <td style="text-align: right;">{{$data['amount_paid']}} </td>
                </tr>
            </tbody></table>
        </div>
    </div>
    <div class="head-title">
        <div class="w-50 float-left logo mt-10">
            <p class="text-bold" style="margin-bottom: 5px">{{$data['terms_label']}}</p>
            <p class="m-0 p-0">
                {{$data['terms_value']}}
            </p>

            <p class="text-bold" style="margin-bottom: 5px">{{$data['notes_label']}}</p>
            <p class="m-0 p-5">
                {{$data['notes_value']}}
            </p>
        </div>
    </div>

</html>
