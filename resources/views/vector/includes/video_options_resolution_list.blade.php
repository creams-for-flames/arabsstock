@php
    $disabled_ids = array_merge($video_in_cart_ids,$exists_in_download);
@endphp
<table class="table table-hover">
    <tbody>
    @if($response->child)
        @foreach($response->child as $childItem)
            @if(in_array($childItem->id,$disabled_ids))
                <tr>
                    <td><input type="radio" value="{{$childItem->id}}" checked disabled></td>
                    <td><span class="">{{$childItem->type}}</span></td>
                    <td colspan="2">
                        @if(in_array($childItem->id,$video_in_cart_ids))
                            {{trans('global.Clip added to cart')}}
                        @endif
                        @if(in_array($childItem->id,$exists_in_download))
                            {{trans('global.Your_item_it_was_purchased_already')}}
                        @endif
                    </td>
                    <td colspan="2">
                        @if(in_array($childItem->id,$video_in_cart_ids))
                            <a href="javascript:;" class="removeItemFromCart"
                               data-token="{{$childItem->token_id}}">{{trans('global.Remove')}}</a>
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td>
                        <div class="form-group custom-control custom-radio">
                            <input type="radio" id="{{$loop->index}}-option" name="customRadio"
                                   class="custom-control-input"
                                   value="{{$childItem->id}}" {{count($video_in_cart_ids) == 0 && $loop->first?'checked':''}} {{count($disabled_ids)? 'disabled' :''}}>
                            <label class="custom-control-label" for="{{$loop->index}}-option"></label>
                        </div>
                    </td>
                    <td>
                        <strong>{{$childItem->type}}</strong>
                    </td>
                    <td>
                        <strong>{{$childItem->extension}}</strong>
                    </td>
                    <td>
                        {{$childItem->width}} × {{$childItem->height}}
                    </td>
                    <td> {{formatBytes($childItem->size)}} </td>
                    <td>
                        <strong>
                            ${{$childItem->price}}
                        </strong>
                    </td>
                </tr>


            @endif
            @if($childItem->price > 0)
                <!-- <li data-id="{{$childItem->id}}" class="nav-item item_sdsdsd selectReslution">
        <a @if(\App\Helper::my_own_video($childItem->id)) style="border: 1px solid #333333;color: #fff !important;background-color: #333333 !important;text-align: center;" @endif  data-id="{{$childItem->id}}" class="nav-link {{$loop->first? 'active' : ''}}" id="pills-dsdsdsds-tab" data-toggle="tab" aria-selected="{{$loop->first? 'true' : 'false' }}">
        {{$childItem->type}}
                    </a>
                    </li> -->
            @endif @endforeach @endif
    </tbody>
</table>

@if(count($exists_in_download))

    <div class="alert alert-success" role="alert">
        {{ trans('global.Your_item_it_was_purchased_already') }}
    </div>

    <button type="button" class="btn btn-download large btn-lg btn-block mb-4 mt-4"
            onclick="location.href= '{{route('me.videos')}}';"><i
            class="fal fa-cloud-download-alt pr-2"></i> {{ trans('global.myVideos') }}
    </button>
@else


    @if(count($video_in_cart_ids) == 0)
        <button type="button" class="btn btn-download large btn-lg btn-block btnAddCart mb-4 mt-4"><i
                class="fal fa-cart-plus pr-2"></i>{{ trans('global.add_to_cart') }}
        </button>
    @else

        <div class="alert alert-success" role="alert">
            {{ trans('global.Your_Item_Already_In_Cart') }}
        </div>

        <button type="button" class="btn btn-download large btn-lg btn-block btnAddCart mb-4 mt-4"
                onclick="location.href = '{{route('video.cart.index')}}';"><i
                class="fal fa-cloud-download-alt pr-2"></i> {{ trans('global.Cart') }}
        </button>
    @endif
@endif
