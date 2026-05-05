<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\DownloadExport;
use App\Export\ImageDownloadExport;
use App\Http\Controllers\Controller;
use App\Models\Contributor;
use App\Models\Download;
use App\Models\FreeDownload;
use App\Models\Image;
use App\Models\Vector;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DownloadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->datatable) {
            $html_breadcrumbs = [
                'title' => __('views.Downoloads'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];
            return view('admin_v2.downloads.index', compact('html_breadcrumbs'));
        }
        $query = Download::with(['purchase.contributor', 'entity' => function ($q) {
            $q->with('user', 'contributor_file')->withoutGlobalScope('not_deleted');
        }, 'user']);
        if ($request->type)
            $query->where('entity_type', "App\\Models\\{$request->type}");
        $datatable_params = get_datatable_params(request()->all());
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
        if ($request->input('query.license_type')) {
            $query->where('license_type', $request->input('query.license_type'));
        }
        if ($request->input('query.generalSearch')) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', \request('query.generalSearch') . '%')
                        ->orWhere('email', 'like', \request('query.generalSearch') . '%');
                })->orWhereHas('purchase.contributor', function ($q) {
                    $q->where('name', 'like', \request('query.generalSearch') . '%');
                })->orWhere('entity_id', \request('query.generalSearch'));
            });
        }
        if ($request->input('query.contributor_id')) {
            $query->whereHas('purchase', function ($q) {
                $q->where('contributor_id', request('query.contributor_id'));
            });
        }
        if ($request->input('query.user_id')) {
            $query->where('user_id', request('query.user_id'));
        }
        if ($request->input('query.team_id')) {
            $query->where('team_id', request('query.team_id'));
        }
        if ($request->input('query.options')) {
            $query->where($request->input('query.options'), 1);
        }
        $data = process_datatable_query($query);
        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function free(Request $request)
    {
        if (!$request->datatable) {
            $html_breadcrumbs = [
                'title' => __('views.Downoloads'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];
            return view('admin_v2.downloads.free', compact('html_breadcrumbs'));
        }
        $query = FreeDownload::with(['entity' => function ($q) {
            $q->with('user', 'contributor_file');
        }, 'user']);
        if ($request->type)
            $query->where('entity_type', "App\\Models\\{$request->type}");
        $datatable_params = get_datatable_params(request()->all());
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
        if ($request->input('query.generalSearch')) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', \request('query.generalSearch') . '%')
                        ->orWhere('email', 'like', \request('query.generalSearch') . '%');
                })->orWhere('entity_id', \request('query.generalSearch'));
            });
        }
        if ($request->input('query.contributor_id')) {
            $query->whereHasMorph('entity', [Image::class, Video::class, Vector::class], function ($q) {
                $q->where('user_id', request('query.contributor_id'));
            });
        }
        if ($request->input('query.user_id')) {
            $query->where('user_id', request('query.user_id'));
        }
        $data = process_datatable_query($query);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Download $download
     * @return \Illuminate\Http\Response
     */
    public function show(Download $download)
    {
        $download->load('entity');
        return view('admin_v2.downloads.show', compact('download'));
    }

    /**
     * Delete the specified resource.
     *
     * @param \App\Models\Download $download
     * @return \Illuminate\Http\Response
     */
    public function destroy(Download $download)
    {
        $download->load('entity', 'purchase.account_ledger');
        optional(optional($download->purchase)->account_ledger)->delete();
        optional($download->purchase)->delete();
        foreach ($download->subscriptions as $subscription) {
            $subscription->increment('remaining_credits', $subscription->pivot->credits);
            $subscription->downloads()->detach($download->id);
        }
        $download->delete();
        return ['status' => 1];
    }


    public function export(Request $request)
    {
        $query = Download::with(['purchase.contributor', 'entity' => function ($q) {
            $q->with('user', 'contributor_file')->withoutGlobalScope('reserved');
        }, 'user']);
        if ($request->type)
            $query->where('entity_type', "App\\Models\\{$request->type}");
        if ($request->user_id)
            $query->where('user_id', $request->user_id);

        if ($request->date_from && $request->date_to) {
            $query = $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            $query = $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        if ($request->input('license_type')) {
            $query->where('license_type', $request->input('license_type'));
        }
        if ($request->contributor_id) {
            $query->whereHas('purchase', function ($q) {
                $q->where('contributor_id', request('contributor_id'));
            });
        }

        return Excel::download(new  DownloadExport($query->get()), "downloads_" . now() . '.xlsx');
    }
}
