<?php

namespace App\Http\Controllers\AdminV2;


use App\Export\PaymentsExport;
use App\Export\StatisticsExport;
use App\Export\SubscriptionsExport;
use App\Models\Download;
use App\Models\ImageSubscription;
use App\Models\Subscription;
use App\Models\VideoSubscription;
use App\Models\VectorSubscription;
use App\Models\ImageDownload;
use App\Models\VideoDownload;
use App\Models\VectorDownload;
use App\Models\PaymentsLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PerformanceReportController extends Controller
{
    public function payments(Request $request)
    {
        if (!$request->datatable) {
            $html_new_path = '';
            $downloads = Download::count();
            $total_amount = Subscription::whereNotIn('status', [Subscription::STATUS_PENDING, Subscription::STATUS_REFUND])->sum('amount');
            $total_subscription = Subscription::whereNotIn('status', [Subscription::STATUS_PENDING, Subscription::STATUS_REFUND])->count();
            $active_subscriptions = Subscription::whereNotIn('status', [Subscription::STATUS_PENDING, Subscription::STATUS_REFUND])->where([
                ['subscriptions.remaining_credits', '>', 0],
                ['subscriptions.ends_at', '>=', date('Y-m-d H:i:s')],
            ])->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereIn('subscriptions.status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_CANCEL])->whereIn('subscriptions.plan_type', ['monthly', 'annual']);
                })->orWhere(function ($q) {
                    $q->whereIn('subscriptions.status', [Subscription::STATUS_ACTIVE])->where('subscriptions.plan_type', 'package');
                });
            })->count();
            return view('admin_v2.performance_report.payment.flex',
                compact('html_new_path', 'downloads', 'total_amount', 'total_subscription', 'active_subscriptions'));
        }
        $query = Subscription::whereNotIn('status', [Subscription::STATUS_PENDING, Subscription::STATUS_REFUND])->with('user', 'plan', 'country', 'city', 'promocode', 'payment_method');
        $data = process_datatable_query($query, function ($query, $search) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('payment_id', 'like', '%' . $search . '%')
                        ->orWhere('subscription_id', 'like', '%' . $search . '%')
                        ->orWhere('data', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');


                        });

                });

            // TODO check if range is sent $search_params['date_range']
        }, function ($query) {
            $datatable_params = get_datatable_params(request()->all());
            if (isset($datatable_params['date_range'][1])) {
                $query = $query->filter(function ($item) use ($datatable_params) {
                    return (date('d-m-Y', strtotime(data_get($item, 'starts_at'))) >= date('d-m-Y', strtotime($datatable_params['date_range'][0])) && date('d-m-Y', strtotime(data_get($item, 'starts_at'))) <= date('d-m-Y', strtotime($datatable_params['date_range'][1])));

                });
            }
            return $query;

        });

        return $data;
    }

    public function index_payment()
    {
        $index_url = route('admin.performance_reports.payment.datatable');
        $export_url = route('admin.performance_reports.image_payment.export');
        $html_new_path = '';
        $image_download = ImageDownload::count();
        $total_amount = ImageSubscription::sum('amount');
        $toaol_subscription = ImageSubscription::count();
        $html_breadcrumbs = [
            'title' => __('views.PerformanceReports'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        return view('admin_v2.performance_report.payment.index',
            compact('html_new_path', 'index_url', 'image_download', 'total_amount', 'toaol_subscription', 'export_url'));
    }

    public function datatable_payment(Request $request)
    {
        $query = ImageSubscription::with('user', 'plan', 'country', 'city');
        if ($request->input('query.payment_method'))
            $query->where('payment_method_id', $request->input('query.payment_method'));
        $search = request('query.q');
        if ($search) {
            $query->where('payment_id', 'like', '%' . $search . '%')
                ->orWhere('subscription_id', 'like', '%' . $search . '%')
                ->orWhere('data', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        }
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('created_at', '>=', $from);
            $query = $query->whereDate('created_at', '<=', $to);
        }
        $data = process_datatable_query($query);
        return $data;
    }

    public function export_payments(Request $request)
    {
        $query = Subscription::with('user', 'plan', 'country', 'city');
        if ($request->input('payment_method'))
            $query->where('payment_method_id', $request->input('payment_method'));
        $search = request('q');
        if ($search) {
            $query->where('payment_id', 'like', '%' . $search . '%')
                ->orWhere('subscription_id', 'like', '%' . $search . '%')
                ->orWhere('data', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        }
        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        return Excel::download(new  PaymentsExport($query->get()), "payments_" . now() . '.xlsx');
    }

    public function export_image_payments(Request $request)
    {
        $query = ImageSubscription::where('status', ImageSubscription::STATUS_ACTIVE)->with('user', 'plan', 'country', 'city');
        if ($request->input('payment_method'))
            $query->where('payment_method_id', $request->input('payment_method'));
        $search = request('q');
        if ($search) {
            $query->where('payment_id', 'like', '%' . $search . '%')
                ->orWhere('subscription_id', 'like', '%' . $search . '%')
                ->orWhere('data', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        }
        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        return Excel::download(new  SubscriptionsExport($query->get()), "image_payments_" . now() . '.xlsx');
    }

    public function export_video_payments(Request $request)
    {
        $query = VideoSubscription::where('status', VideoSubscription::STATUS_ACTIVE)->with('user', 'plan', 'country', 'city');
        if ($request->input('payment_method'))
            $query->where('payment_method_id', $request->input('payment_method'));
        $search = request('q');
        if ($search) {
            $query->where('payment_id', 'like', '%' . $search . '%')
                ->orWhere('subscription_id', 'like', '%' . $search . '%')
                ->orWhere('data', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        }

        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        return Excel::download(new  SubscriptionsExport($query->get()), "video_payments_" . now() . '.xlsx');
    }

    public function export_vector_payments(Request $request)
    {
        $query = VectorSubscription::where('status', VectorSubscription::STATUS_ACTIVE)->with('user', 'plan', 'country', 'city');
        if ($request->input('payment_method'))
            $query->where('payment_method_id', $request->input('payment_method'));
        $search = request('q');
        if ($search) {
            $query->where('payment_id', 'like', '%' . $search . '%')
                ->orWhere('subscription_id', 'like', '%' . $search . '%')
                ->orWhere('data', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        }
        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        return Excel::download(new  SubscriptionsExport($query->get()), "vector_payments_" . now() . '.xlsx');
    }

    public function index_payments_logs()
    {
        $index_url = route('admin.payments_log.datatable');
        $html_new_path = route('admin.payments_log.datatable');

        $image_download = ImageDownload::count();
        $total_amount = ImageSubscription::sum('amount');
        $toaol_subscription = ImageSubscription::count();

        $html_breadcrumbs = [
            'title' => __('views.PerformanceReports'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        return view('admin_v2.performance_report.payments_logs.index',
            compact('html_new_path', 'index_url', 'image_download', 'total_amount', 'toaol_subscription'));
    }

    public function datatable_payments_logs(Request $request)
    {
        $model = PaymentsLog::with('user');
        $data = process_datatable_query($model, function ($query, $search) {
            return $query;
        });
        return $data;
    }


    public function index_payment_videos()
    {
        $index_url = route('admin.videos.reports.payment.datatable');
        $export_url = route('admin.performance_reports.video_payment.export');
        $html_new_path = '';
        $image_download = VideoDownload::count();
        $total_amount = VideoSubscription::sum('amount');
        $toaol_subscription = VideoSubscription::count();
        $html_breadcrumbs = [
            'title' => __('views.PerformanceReports'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $title = 'video';
        $is_videos_site = true;
        return view('admin_v2.performance_report.payment.index',
            compact('html_new_path', 'index_url', 'image_download', 'total_amount', 'toaol_subscription', 'title', 'is_videos_site', 'export_url'));
    }

    public function datatable_payment_videos(Request $request)
    {
        $query = VideoSubscription::with('user', 'plan', 'country', 'city');
        if ($request->input('query.payment_method'))
            $query->where('payment_method_id', $request->input('query.payment_method'));
        $search = request('query.q');
        if ($search) {
            $query->where('payment_id', 'like', '%' . $search . '%')
                ->orWhere('subscription_id', 'like', '%' . $search . '%')
                ->orWhere('data', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        }
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('created_at', '>=', $from);
            $query = $query->whereDate('created_at', '<=', $to);
        }
        $data = process_datatable_query($query);
        return $data;
    }


    public function index_payments_logs_videos()
    {
        // dd('wefe');
        // return PaymentsLog::with('user')->get();


        $index_url = route('admin.videos.reports.payments_log.datatable');
        $html_new_path = '';

        $image_download = VideoDownload::count();
        $total_amount = VideoSubscription::sum('amount');
        $toaol_subscription = VideoSubscription::count();

        $html_breadcrumbs = [
            'title' => __('views.PerformanceReports'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_videos_site = true;
        return view('admin_v2.performance_report.payments_logs.index',
            compact('html_new_path', 'index_url', 'image_download', 'total_amount', 'toaol_subscription', 'is_videos_site'));
    }

    public function datatable_payments_logs_videos(Request $request)
    {
        $model = PaymentsLog::with('user');

        $search = '';
        $data = process_datatable_query($model, function ($query, $search) {

            // $from_date = $to_date = null;
            // $query_params = request()->get('query',[]);
            // if(isset($query_params['date_range'])){

            // if(isset($query_params['date_range'][0]))
            // $from_date = $query_params['date_range'][0];

            // if(isset($query_params['date_range'][1]))
            // $to_date = $query_params['date_range'][1];
            // }

            return $query;
            // ->where(function($q) use ($search) {
            //   $q->where('id', 'like', '%' . $search . '%');
            // })->where(function($q) use ($from_date,$to_date) {
            //     $q->when(!is_null($from_date),function($q)use($from_date){
            //     $q->where('created_at', '=', $from_date . '%');
            //       })->when(!is_null($to_date),function($q)use($to_date){
            //     $q->where('created_at', '<=', $to_date . '%');
            //       });
            // });

        });

        return $data;
    }


    //vectors


    public function index_payment_vectors()
    {
        $index_url = route('admin.vector.reports.payment.datatable');
        $export_url = route('admin.performance_reports.vector_payment.export');
        $html_new_path = '';
        $image_download = VectorDownload::count();
        $total_amount = VectorSubscription::sum('amount');
        $toaol_subscription = VectorSubscription::count();
        $html_breadcrumbs = [
            'title' => __('views.PerformanceReports'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $title = 'vector';
        $is_vectors_site = true;
        return view('admin_v2.performance_report.payment.index',
            compact('html_new_path', 'index_url', 'image_download', 'total_amount', 'toaol_subscription', 'title', 'is_vectors_site', 'export_url'));
    }

    public function datatable_payment_vectors(Request $request)
    {
        $query = VectorSubscription::with('user', 'plan', 'country', 'city');
        if ($request->input('query.payment_method'))
            $query->where('payment_method_id', $request->input('query.payment_method'));
        $search = request('query.q');
        if ($search) {
            $query->where('payment_id', 'like', '%' . $search . '%')
                ->orWhere('subscription_id', 'like', '%' . $search . '%')
                ->orWhere('data', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        }
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('created_at', '>=', $from);
            $query = $query->whereDate('created_at', '<=', $to);
        }
        $data = process_datatable_query($query);
        return $data;
    }


    public function index_payments_logs_vectors()
    {


        $index_url = route('admin.vector.reports.payments_log.datatable');
        $html_new_path = '';

        $image_download = VectorDownload::count();
        $total_amount = VectorSubscription::sum('amount');
        $toaol_subscription = VectorSubscription::count();

        $html_breadcrumbs = [
            'title' => __('views.PerformanceReports'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_vectors_site = true;
        return view('admin_v2.performance_report.payments_logs.index',
            compact('html_new_path', 'index_url', 'image_download', 'total_amount', 'toaol_subscription', 'is_vectors_site'));
    }

    public function datatable_payments_logs_vectors(Request $request)
    {
        $model = PaymentsLog::where('category', 'vector')->with('user');

        $search = '';
        $data = process_datatable_query($model, function ($query, $search) {

            // $from_date = $to_date = null;
            // $query_params = request()->get('query',[]);
            // if(isset($query_params['date_range'])){

            // if(isset($query_params['date_range'][0]))
            // $from_date = $query_params['date_range'][0];

            // if(isset($query_params['date_range'][1]))
            // $to_date = $query_params['date_range'][1];
            // }

            return $query;
            // ->where(function($q) use ($search) {
            //   $q->where('id', 'like', '%' . $search . '%');
            // })->where(function($q) use ($from_date,$to_date) {
            //     $q->when(!is_null($from_date),function($q)use($from_date){
            //     $q->where('created_at', '=', $from_date . '%');
            //       })->when(!is_null($to_date),function($q)use($to_date){
            //     $q->where('created_at', '<=', $to_date . '%');
            //       });
            // });

        });

        return $data;
    }

    public function monthly_new_image_payments(Request $request)
    {
        if (!$request->ajax()) {
            $dates = DB::select("select distinct year(created_at) as y,month(created_at) as m from image_subscriptions");
            $chart_data = [];
            foreach ($dates as $date) {
                if ($date->y && $date->m)
                    $chart_data["{$date->y}-{$date->m}"] = ImageSubscription::has('user')
                        ->where('plan_type', 'monthly')
                        ->where('status', ImageSubscription::STATUS_ACTIVE)->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('image_subscriptions as t')
                                ->whereRaw('t.created_at < image_subscriptions.created_at and t.user_id=image_subscriptions.user_id');
                        })
                        ->whereMonth('created_at', $date->m)
                        ->whereYear('created_at', $date->y)
                        ->count();
            }
            $data_url = route('admin.performance_reports.monthly_new_image_payments');
            return view('admin_v2.performance_report.monthly_new_payments', compact('chart_data', 'data_url'));
        }
        $date = $request->input('query.date') ? Carbon::createFromFormat('m-Y', $request->input('query.date')) : Carbon::now();
        $query = ImageSubscription::has('user')
            ->where('plan_type', 'monthly')
            ->where('status', ImageSubscription::STATUS_ACTIVE)
            ->whereMonth('created_at', $date->format('m'))
            ->whereYear('created_at', $date->format('Y'));
        $result_set = clone $query;
        $result_set = $result_set->with(['user' => function ($q) {
            $q->withCount(['image_subscriptions' => function ($q) {
                $q->where('status', ImageSubscription::STATUS_ACTIVE);
            }]);
        }])->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('image_subscriptions as t')
                ->whereRaw('t.created_at < image_subscriptions.created_at and t.user_id=image_subscriptions.user_id');
        });
        if ($request->input('query.plan_type'))
            $result_set->where('plan_type', request('query.plan_type'));
        if ($request->sort && in_array($request->input('sort.field'), ['id', 'user_id', 'created_at']))
            $result_set->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $result_set->orderBy('id', 'desc');

        $result_set = $result_set->paginate($request->input('pagination.perpage', 30), ['*'], 'pagination.page');

        if ($request->datatable)
            return [
                'meta' => [
                    "page" => $result_set->currentPage(),
                    "pages" => $result_set->lastPage(),
                    "perpage" => $result_set->perPage(),
                    "total" => $result_set->total(),
                    "sort" => "desc",
                    "field" => "id"
                ],
                'data' => $result_set->items(),
            ];
        return [
            'total_subscriptions' => $query->count(),
            'renewal_subscriptions' => $query->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('image_subscriptions as t')
                    ->whereRaw('t.created_at < image_subscriptions.created_at and t.user_id=image_subscriptions.user_id');
            })->count(),
            'new_subscriptions' => $result_set->total(),
        ];
    }

    public function monthly_new_video_payments(Request $request)
    {
        if (!$request->ajax()) {
            $dates = DB::select("select distinct year(created_at) as y,month(created_at) as m from video_subscriptions");
            $chart_data = [];
            foreach ($dates as $date) {
                if ($date->y && $date->m)
                    $chart_data["{$date->y}-{$date->m}"] = VideoSubscription::has('user')
                        ->where('plan_type', 'monthly')
                        ->where('status', VideoSubscription::STATUS_ACTIVE)->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('video_subscriptions as t')
                                ->whereRaw('t.created_at < video_subscriptions.created_at and t.user_id=video_subscriptions.user_id');
                        })
                        ->whereMonth('created_at', $date->m)
                        ->whereYear('created_at', $date->y)
                        ->count();
            }
            $data_url = route('admin.performance_reports.monthly_new_video_payments');
            return view('admin_v2.performance_report.monthly_new_payments', compact('chart_data', 'data_url'));
        }
        $date = $request->input('query.date') ? Carbon::createFromFormat('m-Y', $request->input('query.date')) : Carbon::now();
        $query = VideoSubscription::has('user')->where('status', VideoSubscription::STATUS_ACTIVE)
            ->where('plan_type', 'monthly')
            ->whereMonth('created_at', $date->format('m'))
            ->whereYear('created_at', $date->format('Y'));
        $result_set = clone $query;
        $result_set = $result_set->with(['user' => function ($q) {
            $q->withCount(['video_subscriptions as subscriptions_count' => function ($q) {
                $q->where('status', VideoSubscription::STATUS_ACTIVE);
            }]);
        }])->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('video_subscriptions as t')
                ->whereRaw('t.created_at < video_subscriptions.created_at and t.user_id=video_subscriptions.user_id');
        });
        if ($request->input('query.plan_type'))
            $result_set->where('plan_type', request('query.plan_type'));
        if ($request->sort && in_array($request->input('sort.field'), ['id', 'user_id', 'created_at']))
            $result_set->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $result_set->orderBy('id', 'desc');

        $result_set = $result_set->paginate($request->input('pagination.perpage', 30), ['*'], 'pagination.page');

        if ($request->datatable)
            return [
                'meta' => [
                    "page" => $result_set->currentPage(),
                    "pages" => $result_set->lastPage(),
                    "perpage" => $result_set->perPage(),
                    "total" => $result_set->total(),
                    "sort" => "desc",
                    "field" => "id"
                ],
                'data' => $result_set->items(),
            ];
        return [
            'total_subscriptions' => $query->count(),
            'renewal_subscriptions' => $query->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('video_subscriptions as t')
                    ->whereRaw('t.created_at < video_subscriptions.created_at and t.user_id=video_subscriptions.user_id');
            })->count(),
            'new_subscriptions' => $result_set->total(),
        ];
    }

    public function monthly_new_vector_payments(Request $request)
    {
        if (!$request->ajax()) {
            $dates = DB::select("select distinct year(created_at) as y,month(created_at) as m from vector_subscriptions");
            $chart_data = [];
            foreach ($dates as $date) {
                if ($date->y && $date->m)
                    $chart_data["{$date->y}-{$date->m}"] = VectorSubscription::has('user')
                        ->where('plan_type', 'monthly')
                        ->where('status', VectorSubscription::STATUS_ACTIVE)->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('vector_subscriptions as t')
                                ->whereRaw('t.created_at < vector_subscriptions.created_at and t.user_id=vector_subscriptions.user_id');
                        })
                        ->whereMonth('created_at', $date->m)
                        ->whereYear('created_at', $date->y)
                        ->count();
            }
            $data_url = route('admin.performance_reports.monthly_new_vector_payments');
            return view('admin_v2.performance_report.monthly_new_payments', compact('chart_data', 'data_url'));
        }
        $date = $request->input('query.date') ? Carbon::createFromFormat('m-Y', $request->input('query.date')) : Carbon::now();
        $query = VectorSubscription::has('user')->where('status', VectorSubscription::STATUS_ACTIVE)
            ->where('plan_type', 'monthly')
            ->whereMonth('created_at', $date->format('m'))
            ->whereYear('created_at', $date->format('Y'));
        $result_set = clone $query;
        $result_set = $result_set->with(['user' => function ($q) {
            $q->withCount(['vector_subscriptions as subscriptions_count' => function ($q) {
                $q->where('status', VectorSubscription::STATUS_ACTIVE);
            }]);
        }])->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('vector_subscriptions as t')
                ->whereRaw('t.created_at < vector_subscriptions.created_at and t.user_id=vector_subscriptions.user_id');
        });
        if ($request->input('query.plan_type'))
            $result_set->where('plan_type', request('query.plan_type'));
        if ($request->sort && in_array($request->input('sort.field'), ['id', 'user_id', 'created_at']))
            $result_set->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $result_set->orderBy('id', 'desc');

        $result_set = $result_set->paginate($request->input('pagination.perpage', 30), ['*'], 'pagination.page');

        if ($request->datatable)
            return [
                'meta' => [
                    "page" => $result_set->currentPage(),
                    "pages" => $result_set->lastPage(),
                    "perpage" => $result_set->perPage(),
                    "total" => $result_set->total(),
                    "sort" => "desc",
                    "field" => "id"
                ],
                'data' => $result_set->items(),
            ];
        return [
            'total_subscriptions' => $query->count(),
            'renewal_subscriptions' => $query->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('vector_subscriptions as t')
                    ->whereRaw('t.created_at < vector_subscriptions.created_at and t.user_id=vector_subscriptions.user_id');
            })->count(),
            'new_subscriptions' => $result_set->total(),
        ];
    }

    public function export_statistics(Request $request)
    {
        $from = Carbon::parse($request->input('from', '2020-06-01'))->format('Y-m-d');
        $to = Carbon::parse($request->input('to', '2022-06-31'))->format('Y-m-d');
        $export = new StatisticsExport($from, $to);
        return \Maatwebsite\Excel\Facades\Excel::download($export, 'احصائيات محتوى عربستوك.xlsx');
    }

}
