<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\VideoDownload;
use App\Models\OrderVideo;
use App\Models\OrderPayment;
use App\Models\OrderItemsVideo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsVideoController extends Controller
{
    /**
     * Display a listing of UserPlan.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $index_url = route('admin.videos.payments.datatable');
        $show_url = route('admin.videos.payments.items', 0);

        $html_breadcrumbs = [
            'title' => __('views.Payments'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_videos_site = true;

        return view(
            'admin_v2_videos.payments.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'index_url',
                'show_url'
            )
        );
    }


    public function datatable(Request $request)
    {

        $data = process_datatable_query(OrderVideo::with(['payment', 'country', 'city', 'user'])->withCount('items'), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                });
        });

        return $data;
    }


    public function order_list($order_id)
    {


        $index_url = route('admin.videos.payments.items.datatable', $order_id);
        $show_url = route('admin.videos.payments.items', 0);

        $html_breadcrumbs = [
            'title' => __('views.Payments'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_videos_site = true;

        return view(
            'admin_v2_videos.payments.items',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'index_url',
                'show_url'
            )
        );
    }


    public function datatable_items(Request $request, $order_id)
    {


        $data = process_datatable_query(OrderItemsVideo::where('order_id', $order_id)->with(['video', 'user']), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                });
        });

        return $data;
    }

    public function order_all()
    {


        // dd(VideoDownload::all());

        // return OrderItemsVideo::with(['video', 'user','order.city','order.country'])->get() ;

        $index_url = route('admin.videos.payments.items.datatable.all');

        $html_breadcrumbs = [
            'title' => __('views.Payments'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_videos_site = true;
        $show_url = route('admin.videos.videos.edit', 0);
        return view(
            'admin_v2_videos.payments.itemsAll',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'index_url',
                'show_url'
            )
        );
    }

    public function datatable_items_all(Request $request)
    {


        $data = process_datatable_query(OrderItemsVideo::with(['video', 'user', 'order.city', 'order.country']), function ($query, $searchd) {
            return $query
                ->where(function ($query) use ($search) {
                });
        });

        return $data;
    }


}
