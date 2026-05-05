<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\ContributorImageDownloadExport;
use App\Models\Contributor;
use App\Models\Image;
use App\Models\User;
use App\Export\{ContributorDownloadExport, ImageDownloadExport, DownloadStatisticsExport};
use App\Export\VectorDownloadExport;
use App\Export\VideoDownloadExport;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ImageSubscription;
use App\Models\VideoDownload;
use App\Models\VectorDownload;
use App\Models\VideoSubscription;
use App\Models\VectorSubscription;
use App\Models\ImageDownload;
use Maatwebsite\Excel\Facades\Excel;

class UserPlanController extends Controller
{

    /**
     * Display a listing of UserPlan.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $index_url = route('admin.user_plans.datatable');
        $show_url = route('admin.user_plans.items', 0);

        $html_breadcrumbs = [
            'title' => __('views.UserPlans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $plans = \App\Models\ImagePlan::where('status', 1)->orderBy('id')->get();
        return view(
            'admin_v2.user_plan.index',
            compact(
                'html_breadcrumbs',
                'index_url',
                'show_url',
                'plans'
            )
        );
    }

    public function datatable(Request $request)
    {
        $query = ImageSubscription::with(['plan', 'user', 'country', 'city']);
        if ($request->has('free')) {
            $query = $query->whereHas('plan', function ($q) {
                $q->where('free', request('free'));
            });
        }
        if ($request->has('query.plan_id'))
            $query->where('plan_id', $request->input('query.plan_id'));
        if ($request->has('query.user_id'))
            $query->where('user_id', $request->input('query.user_id'));
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            if (isset($search) && $search != '') {
                $query = $query
                    ->whereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            }
            return $query;
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

// public function refund(){

//     $subscription = ImageSubscription::where('id',$id)->firstOrFail();
// // $sale_id = $subscription->data->transactions[0]->related_resources[0]->sale->id;

// }


    public function items($user_plan_id)
    {
        $index_url = route('admin.user_plans.items.datatable', $user_plan_id);


        $html_breadcrumbs = [
            'title' => __('views.UserPlans') . ' - ' . __('views.Downoloads'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        return view(
            'admin_v2.user_plan.items',
            compact(
                'html_breadcrumbs',
                'index_url',
// 'show_url'
            )
        );
    }

    public function itemsFree($user_plan_id)
    {
        $index_url = route('admin.user_plans.items.datatable', [$user_plan_id, 'free' => 1]);

        $html_breadcrumbs = [
            'title' => __('views.UserPlansFree') . ' - ' . __('views.DownoloadsFree'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        return view(
            'admin_v2.user_plan.items',
            compact(
                'html_breadcrumbs',
                'index_url',
// 'show_url'
            )
        );
    }

    public function datatable_items($subscription_id, Request $request)
    {
        $val = 1;
        $free = $request->get('free');
        $query = ImageDownload::with(['image', 'client', 'plan'])->where('subscription_id', $subscription_id);
        if ($request->has('free') && isset($free) && $free == 1) {
            $query = $query->whereHas('plan', function ($q) use ($val) {
                $q->where('free', $val);
            });
        } else {
            $val = 0;
            $query = $query->whereHas('plan', function ($q) use ($val) {
                $q->where('free', $val);
            });
        }
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) use ($subscription_id) {
            return $query->where(function ($query) use ($search, $subscription_id) {
            });
        });

        return $data;
    }

    public function downloads()
    {
        $index_url = route('admin.user_plans.items.datatable_downloads');
        $types = ['all', 'arabsstock', 'contributor'];
        $cases = ['percentage', 'items'];

        $html_breadcrumbs = [
            'title' => __('views.Downoloads'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        return view('admin_v2.user_plan.downloads', compact('html_breadcrumbs', 'index_url', 'types', 'cases')
        );
    }


    public function datatable_downloads(Request $request)
    {
        $query = ImageDownload::has('image')->with(['image.user', 'client', 'plan', 'subscription']);
        $free = $request->input('query.free', 'all');
        if (in_array($free, [0, 1]))
            $query = $query->whereHas('plan', function ($q) use ($free) {
                $q->where('image_plans.free', $free);
            });
        $created_by = $request->input('query.created_by');
        switch ($created_by) {
            case 'arabsstock':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', User::class);
                });
                break;
            case 'contributor':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', Contributor::class);
                });
                break;


        }
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('created_at', '>=', $from);
            $query = $query->whereDate('created_at', '<=', $to);
        }
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('created_at', '>=', $from);
            $query = $query->whereDate('created_at', '<=', $to);
        }

        $data = process_datatable_query($query, function ($query, $search) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->whereHas('client', function ($q) use ($search) {
                        $q->where('email', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');
                    });

                });
        });

        return $data;
    }

    public function export_downloads(Request $request)
    {
        $created_by = $request->get('created_by', 'all');
        $case = $request->get('case', 'items');
        if (isset($case) && $case === "percentage") {
            $data = $this->StatisticsExport($request, new ImageDownload, 1);
            $from = $request->has('date_from') && $request->input('date_from') != "" ? Carbon::parse($request->input('date_from'))->format('Y-m-d') : NULL;
            $to = $request->has('date_to') && $request->input('date_to') != "" ? Carbon::parse($request->input('date_to'))->format('Y-m-d') : NULL;
            return Excel::download(new  DownloadStatisticsExport($data, $from, $to, 'images', $created_by), "images-download-{$created_by}_{$case}_" . now() . '.xlsx');
        }
        $query = ImageDownload::has('image')->with('subscription', 'plan', 'client')->whereHas('subscription', function ($q) {
            $q->where('status', ImageSubscription::STATUS_ACTIVE);
        });
        $free = $request->input('query.free', 'all');
        if (in_array($free, [0, 1]))
            $query = $query->whereHas('plan', function ($q) use ($free) {
                $q->where('free', $free);
            })->where('user_id', '!=', 0);
        switch ($created_by) {
            case 'arabsstock':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', User::class);
                });
                break;
            case 'contributor':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', Contributor::class);
                });
                break;

        }

        if ($request->contributor_id)
            $query = $query->whereHas('image', function ($q) {
                $q->where('user_type', Contributor::class)->where('user_id', request('contributor_id'));
            });
        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        return Excel::download(new  ImageDownloadExport($query->get()), "images-download-{$created_by}_" . now() . '.xlsx');
    }

    public function delete_download(ImageDownload $download)
    {
        $purchase = Purchase::with('account_ledger')
            ->where('user_id', $download->user_id)
            ->where('purchaseable_id', $download->image_id)
            ->where('purchaseable_type', Image::class)->first();
        if ($purchase) {
            $purchase->account_ledger->delete();
            $purchase->delete();
        }
        $download->subscription->increment('download_remaining', 1);
        $download->delete();
        return ['status' => 1];
    }

    public function StatisticsExport(Request $request, $ObjOfClass, $id)
    {
        $type = $request->get('type', 'all');
        $data['total'] = $ObjOfClass->whereHas('plan', function ($q) {
            $q->where('free', 0);
        })->whereHas('image', function ($q) {
            $q->whereNull('deleted_at');
        })->where('user_id', '!=', 0);
        $data['total'] = $this->FilterDate($request, $data['total']);
        $data['total_deleted'] = $ObjOfClass->whereHas('plan', function ($q) {
            $q->where('free', 0);
        })->where('user_id', '!=', 0)->whereHas('image', function ($q) {
            $q->onlyTrashed();
        });
        $data['total_deleted'] = $this->FilterDate($request, $data['total_deleted']);

        $data['permanently_delete'] = $ObjOfClass->whereHas('plan', function ($q) {
            $q->where('free', 0);
        })->where('user_id', '!=', 0)->doesntHave('image');

        $data['permanently_delete'] = $this->FilterDate($request, $data['permanently_delete']);

        switch ($type) {
            case 'arabsstock':
                $data['arabsstock'] = $ObjOfClass->whereHas('plan', function ($q) {
                    $q->where('free', 0);
                })->where('user_id', '!=', 0)->whereHas('image', function ($q) use ($id) {
                    $q->where('user_id', $id);
                });
                $data['arabsstock'] = $this->FilterDate($request, $data['arabsstock']);
                break;
            case 'contributor':
                $data['contributors'] = $ObjOfClass->whereHas('plan', function ($q) {
                    $q->where('free', 0);
                })->where('user_id', '!=', 0)->whereHas('image', function ($q) use ($id) {
                    $q->where('user_id', '!=', $id);
                });
                $data['contributors'] = $this->FilterDate($request, $data['contributors']);
                break;

            default:
                $data['arabsstock'] = $ObjOfClass->whereHas('plan', function ($q) {
                    $q->where('free', 0);
                })->where('user_id', '!=', 0)->whereHas('image', function ($q) use ($id) {
                    $q->where('user_id', $id);
                });
                $data['arabsstock'] = $this->FilterDate($request, $data['arabsstock']);
                $data['contributors'] = $ObjOfClass->whereHas('plan', function ($q) {
                    $q->where('free', 0);
                })->where('user_id', '!=', 0)->whereHas('image', function ($q) use ($id) {
                    $q->where('user_id', '!=', $id);
                });
                $data['contributors'] = $this->FilterDate($request, $data['contributors']);
                break;
        }


        return $data;
    }

    public function FilterDate(Request $request, $query)
    {
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query = $query->whereBetween('created_at', [Carbon::parse($request->date_from), Carbon::parse($request->date_to)]);
        }
        if ($request->filled('date_from') && !$request->filled('date_to')) {
            $query = $query->where('created_at', '>', Carbon::parse($request->date_from));
        }
        if (!$request->filled('date_from') && $request->filled('date_to')) {
            $query = $query->where('created_at', '<', Carbon::parse($request->date_to));
        }

        return $query->count();
    }

    public function contributor_downloads()
    {
        $index_url = route('admin.user_plans.items.datatable_contributor_downloads');


        $html_breadcrumbs = [
            'title' => __('views.ContributorDownloads'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        return view('admin_v2.user_plan.contributor_downloads', compact('html_breadcrumbs', 'index_url')
        );
    }

    public function datatable_contributor_downloads(Request $request)
    {
        $query = Purchase::with(['purchaseable' => function ($q) {
            $q->withoutGlobalScope('not_deleted');
        }, 'user', 'image_plan', 'video_plan', 'vector_plan', 'contributor']);

        if (request('query.contributor_id'))
            $query->where('contributor_id', request('query.contributor_id'));
        if (request('query.date_range.0'))
            $query->whereDate('created_at', '>=', date('Y-m-d', strtotime(request('query.date_range.0'))));
        if (request('query.date_range.1'))
            $query->whereDate('created_at', '<=', date('Y-m-d', strtotime(request('query.date_range.1'))));

        $data = process_datatable_query($query);

        return $data;
    }

    public function export_contributor_downloads(Request $request)
    {
        $query = Purchase::with(['purchaseable' => function ($q) {
            $q->withoutGlobalScope('not_deleted');
        }, 'user', 'contributor', 'download']);
        if ($request->date_from) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
        }
        if ($request->purchaseable_type)
            $query->where('purchaseable_type', $request->purchaseable_type);
        if ($request->date_to) {
            $query = $query->whereDate('created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
        }
        if ($request->contributor_id) {
            $query = $query->where('contributor_id', $request->contributor_id);
        }
        return Excel::download(new ContributorDownloadExport($query->get()), now() . '.xlsx');
    }

//videos functions


    public function index_videos()
    {

        $index_url = route('admin.videos.user_plans.datatable');
        $show_url = route('admin.videos.user_plans.items', 0);

        $html_breadcrumbs = [
            'title' => __('views.UserPlans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_videos_site = true;
        $plans = \App\Models\VideoPlan::where('status', 1)->orderBy('id')->get();
        return view(
            'admin_v2.user_plan.index',
            compact(
                'html_breadcrumbs',
                'index_url',
                'is_videos_site',
                'show_url',
                'plans'
            )
        );
    }


    public function datatable_videos(Request $request)
    {

        $query = VideoSubscription::with(['plan', 'user', 'country', 'city']);
        if ($request->has('free')) {
            $query = $query->whereHas('plan', function ($q) {
                $q->where('free', request('free'));
            });
        }
        if ($request->has('query.plan_id'))
            $query->where('plan_id', $request->input('query.plan_id'));
        if ($request->has('query.user_id'))
            $query->where('user_id', $request->input('query.user_id'));
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            if (isset($search) && $search != '') {
                $query = $query
                    ->whereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            }
            return $query;
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


    public function items_videos($user_plan_id)
    {
// dd($user_plan_id);
//
// return VideoDownload::with(['image', 'client','plan'])->get();
        $index_url = route('admin.videos.user_plans.items.datatable', $user_plan_id);


        $html_breadcrumbs = [
            'title' => __('views.UserPlans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $is_videos_site = true;
        return view(
            'admin_v2.user_plan.items',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'index_url'
            )
        );
    }


    public function datatable_items_videos($subscription_id, Request $request)
    {
// dd($subscription_id);

        $query = VideoDownload::with(['image', 'client', 'plan']);
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query->where(function ($query) use ($search, $subscription_id) {
            });
        });

        return $data;
    }


    public function downloads_videos()
    {
        $index_url = route('admin.videos.user_plans.items.datatable_downloads');
        $types = ['all', 'arabsstock', 'contributor'];
        $cases = ['percentage', 'items'];

        $html_breadcrumbs = [
            'title' => __('views.Downoloads'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.user_plan.download_video',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'types',
                'cases',
                'index_url'
            )
        );
    }

    public function datatable_downloads_videos(Request $request)
    {
        $query = VideoDownload::has('video')->with(['video.user', 'client', 'plan', 'subscription']);
        $free = $request->input('query.free', 'all');
        if (in_array($free, [0, 1]))
            $query = $query->whereHas('plan', function ($q) use ($free) {
                $q->where('video_plans.free', $free);
            });
        $created_by = $request->input('query.created_by');
        switch ($created_by) {
            case 'arabsstock':
                $query = $query->whereHas('video', function ($q) {
                    $q->where('user_type', User::class);
                });
                break;
            case 'contributor':
                $query = $query->whereHas('video', function ($q) {
                    $q->where('user_type', Contributor::class);
                });
                break;


        }
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('date', '>=', $from);
            $query = $query->whereDate('date', '<=', $to);
        }
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('date', '>=', $from);
            $query = $query->whereDate('date', '<=', $to);
        }

        $data = process_datatable_query($query, function ($query, $search) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->whereHas('client', function ($q) use ($search) {
                        $q->where('email', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');
                    });

                });
        });

        return $data;
    }

    public function export_downloads_videos(Request $request)
    {
        $created_by = $request->get('created_by', 'all');
        $case = $request->get('case', 'items');
        if (isset($case) && $case === "percentage") {
            $data = $this->StatisticsExport($request, new ImageDownload, 1);
            $from = $request->has('date_from') && $request->input('date_from') != "" ? Carbon::parse($request->input('date_from'))->format('Y-m-d') : NULL;
            $to = $request->has('date_to') && $request->input('date_to') != "" ? Carbon::parse($request->input('date_to'))->format('Y-m-d') : NULL;
            return Excel::download(new  DownloadStatisticsExport($data, $from, $to, 'images', $created_by), "images-download-{$created_by}_{$case}_" . now() . '.xlsx');
        }
        $query = VideoDownload::has('video')->with('subscription', 'plan', 'client')->whereHas('subscription', function ($q) {
            $q->where('status', ImageSubscription::STATUS_ACTIVE);
        });
        $free = $request->input('query.free', 'all');
        if (in_array($free, [0, 1]))
            $query = $query->whereHas('plan', function ($q) use ($free) {
                $q->where('free', $free);
            })->where('user_id', '!=', 0);
        switch ($created_by) {
            case 'arabsstock':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', User::class);
                });
                break;
            case 'contributor':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', Contributor::class);
                });
                break;

        }

        if ($request->contributor_id)
            $query = $query->whereHas('video', function ($q) {
                $q->where('user_type', Contributor::class)->where('user_id', request('contributor_id'));
            });
        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('date', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('date', '<=', Carbon::parse($request->date_to));
        }
        return Excel::download(new  VideoDownloadExport($query->get()), "videos-downloads-{$created_by}-" . now() . '.xlsx');
    }


//vector fun.


    public function index_vectors()
    {
        $index_url = route('admin.vector.user_plans.datatable');
        $show_url = route('admin.vector.user_plans.items', 0);

        $html_breadcrumbs = [
            'title' => __('views.UserPlans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $is_vectors_site = true;
        $plans = \App\Models\VectorPlan::where('status', 1)->orderBy('id')->get();
        return view(
            'admin_v2.user_plan.index',
            compact(
                'html_breadcrumbs',
                'index_url',
                'is_vectors_site',
                'show_url',
                'plans'
            )
        );
    }


    public function datatable_vectors(Request $request)
    {

        $query = VectorSubscription::with(['plan', 'user', 'country', 'city']);
        if ($request->has('free')) {
            $query = $query->whereHas('plan', function ($q) {
                $q->where('free', request('free'));
            });
        }
        if ($request->has('query.plan_id'))
            $query->where('plan_id', $request->input('query.plan_id'));
        if ($request->has('query.user_id'))
            $query->where('user_id', $request->input('query.user_id'));

        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            if (isset($search) && $search != '') {
                $query = $query
                    ->whereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            }
            return $query;
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


    public function items_vectors($user_plan_id)
    {
        $index_url = route('admin.vector.user_plans.items.datatable', $user_plan_id);


        $html_breadcrumbs = [
            'title' => __('views.UserPlans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2.user_plan.items',
            compact(
                'html_breadcrumbs',
                'index_url',
                'is_vectors_site'
            )
        );
    }


    public function datatable_items_vectors($subscription_id, Request $request)
    {

        $query = VectorDownload::with(['image', 'client', 'plan'])->where('subscription_id', $subscription_id);
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query->where(function ($query) use ($search, $subscription_id) {
            });
        });

        return $data;
    }


    public function downloads_vectors()
    {
        $index_url = route('admin.vector.user_plans.items.datatable_downloads');
        $types = ['all', 'arabsstock', 'contributor'];
        $cases = ['percentage', 'items'];

        $html_breadcrumbs = [
            'title' => __('views.UserPlans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2.user_plan.download_vector',
            compact(
                'html_breadcrumbs',
                'is_vectors_site',
                'types',
                'cases',
                'index_url'
            )
        );
    }

    public function datatable_downloads_vectors(Request $request)
    {
        $query = VectorDownload::has('vector')->with(['vector.user', 'client', 'plan', 'subscription']);
        $free = $request->input('query.free', 'all');
        if (in_array($free, [0, 1]))
            $query = $query->whereHas('plan', function ($q) use ($free) {
                $q->where('vector_plans.free', $free);
            });
        $created_by = $request->input('query.created_by');
        switch ($created_by) {
            case 'arabsstock':
                $query = $query->whereHas('vector', function ($q) {
                    $q->where('user_type', User::class);
                });
                break;
            case 'contributor':
                $query = $query->whereHas('vector', function ($q) {
                    $q->where('user_type', Contributor::class);
                });
                break;


        }
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('date', '>=', $from);
            $query = $query->whereDate('date', '<=', $to);
        }
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('date', '>=', $from);
            $query = $query->whereDate('date', '<=', $to);
        }

        $data = process_datatable_query($query, function ($query, $search) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->whereHas('client', function ($q) use ($search) {
                        $q->where('email', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');
                    });

                });
        });

        return $data;
    }

    public function export_downloads_vectors(Request $request)
    {
        $created_by = $request->get('created_by', 'all');
        $query = VectorDownload::has('vector')->with('subscription', 'plan', 'client')->whereHas('subscription', function ($q) {
            $q->where('status', ImageSubscription::STATUS_ACTIVE);
        });
        $free = $request->input('query.free', 'all');
        if (in_array($free, [0, 1]))
            $query = $query->whereHas('plan', function ($q) use ($free) {
                $q->where('free', $free);
            })->where('user_id', '!=', 0);
        switch ($created_by) {
            case 'arabsstock':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', User::class);
                });
                break;
            case 'contributor':
                $query = $query->whereHas('image', function ($q) {
                    $q->where('user_type', Contributor::class);
                });
                break;

        }
        if ($request->contributor_id)
            $query = $query->whereHas('vector', function ($q) {
                $q->where('user_type', Contributor::class)->where('user_id', request('contributor_id'));
            });
        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('date', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('date', '<=', Carbon::parse($request->date_to));
        }
        return Excel::download(new  VectorDownloadExport($query->get()), "vectors-downloads-{$created_by}-" . now() . '.xlsx');

    }

}
