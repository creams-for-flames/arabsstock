@component('mail::message')
# {{  __('Dear / '.$contributor_name) }}

# {{  __('This is sales statistics for ') }}

# {{  \Carbon\Carbon::today()->format('M d, Y') }}

<table>
    <tr>
        <th style="font-weight: 600;color: #000;min-width: 65px;text-align: center;font-size: 14px">{{__('File')}}</th>
        <th style="font-weight: 600;color: #000;min-width: 100px;text-align: center;font-size: 14px">{{__('File No')}}</th>
        <th style="font-weight: 600;color: #000;min-width: 120px;text-align: center;font-size: 14px">{{__('Purchase Type')}}</th>
        <th style="font-weight: 600;color: #000;min-width: 100px;text-align: center;font-size: 14px">{{__('Quantity')}}</th>
        <th style="font-weight: 600;color: #000;min-width: 100px;text-align: center;font-size: 14px">{{__('Profit')}}</th>
    </tr>
    @foreach($sales as $sale)
        <tr>
            <td style="color: #000;min-width: 65px;text-align: center">
                @if($sale['purchaseable_type'] == 'App\Models\Image')
                    <img src="{{cdn((\App\Models\Image::find($sale['purchaseable_id']))->thumbnail)}}" alt="" width="65" height="65" style="border-radius: 10px">
                @elseif($sale['purchaseable_type'] == 'App\Models\Video')
                    <img src="{{cdn((\App\Models\Video::find($sale['purchaseable_id']))->thumbnail)}}" alt="" width="65" height="65" style="border-radius: 10px">
                @elseif($sale['purchaseable_type'] == 'App\Models\Vector')
                    <img src="{{cdn((\App\Models\Vector::find($sale['purchaseable_id']))->thumbnail)}}" alt="" width="65" height="65" style="border-radius: 10px">
                @endif
            </td>
            <td style="color: #000;min-width: 100px;text-align: center">
                @if($sale['purchaseable_type'] == 'App\Models\Image')
                    <a href="{{url('en/photos/image-'.$sale['purchaseable_id'])}}">P{{$sale['purchaseable_id']}}</a>
                @elseif($sale['purchaseable_type'] == 'App\Models\Video')
                    <a href="{{url('en/videos/clip-'.$sale['purchaseable_id'])}}">V{{$sale['purchaseable_id']}}</a>
                @elseif($sale['purchaseable_type'] == 'App\Models\Vector')
                    <a href="{{url('en/vectors/illustration-'.$sale['purchaseable_id'])}}">I{{$sale['purchaseable_id']}}</a>
                @endif
            </td>
            <td style="color: #000;min-width: 120px;text-align: center">
                @if($sale['purchaseable_type'] == 'App\Models\Image')
                    {{__('Image')}}
                @elseif($sale['purchaseable_type'] == 'App\Models\Video')
                    {{__('Video')}}
                @elseif($sale['purchaseable_type'] == 'App\Models\Vector')
                    {{__('Vector')}}
                @endif
            </td>
            <td style="color: #000;min-width: 100px;text-align: center">{{$sale['quantity']}}</td>
            <td style="color: #000;min-width: 100px;text-align: center;">{{$sale['profit_value'] *$sale['quantity']}}</td>
        </tr>
    @endforeach
    <tr>
        <td style="color: #000;min-width: 65px;text-align: left"></td>
        <td style="color: #000;min-width: 100px;text-align: left"></td>
        <td style="color: #000;min-width: 100px;text-align: left"></td>
        <td style="color: #000;min-width: 100px;text-align: left;font-weight: 600"></td>
        <td style="color: #000;min-width: 100px;text-align: center;font-weight: 600">Total: <span style="color: red">{{$total_profit_value}}</span></td>
    </tr>
</table>

<br>

### {{  __('Thanks') }},
### {{  config('app.name') }}
@endcomponent
