<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Purchase;
use App\Models\Subscription;
use App\Models\Vector;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PartnersController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (config('partners.secured'))
                if (config('partners.enabled')) {
                    $AUTH_USER = config('partners.user');
                    $AUTH_PASS = config('partners.password');
                    header('Cache-Control: no-cache, must-revalidate, max-age=0');
                    $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
                    $is_not_authenticated = (
                        !$has_supplied_credentials ||
                        $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
                        $_SERVER['PHP_AUTH_PW'] != $AUTH_PASS
                    );
                    if ($is_not_authenticated) {
                        header('HTTP/1.1 401 Authorization Required');
                        header('WWW-Authenticate: Basic realm="Access denied"');
                        exit;
                    }
                } else
                    abort(404);
            return $next($request);
        });
    }

    public function purchases(Request $request)
    {
        if ($request->ajax()) {
            $results = Purchase::whereHasMorph('purchaseable', [Image::class, Video::class, Vector::class])->with(['download', 'user', 'purchaseable' => function ($q) {
                $q->withoutGlobalScopes(['default_loaded_relations', 'is_liked', 'reserved']);
            }]);
            if ($request->q)
                $results->where(function ($q) use ($request) {
                    $q->whereHas('user', function ($q) use ($request) {
                        $q->where('users.email', 'like', "%{$request->q}%")->orWhere('users.name', 'like', "%{$request->q}%");
                    })->orWhereHas('contributor', function ($q) use ($request) {
                        $q->where('contributors.email', 'like', "%{$request->q}%")->orWhere('contributors.name', 'like', "%{$request->q}%");
                    })->orWhereHasMorph('purchaseable', [Image::class, Video::class, Vector::class], function ($q) use ($request) {
                        $q->where('title_ar', 'like', "%{$request->q}%")->orWhere('title_en', 'like', "%{$request->q}%");
                    });
                });
            if ($request->date)
                $results->whereDate('created_at', date('Y-m-d', strtotime($request->date)));
            if ($request->type)
                $results->where('purchaseable_type', $request->type);
            $data = DataTables::of($results)
                ->make(true);

            return $data;
        }
        return view('partners.purchases');
    }

    public function subscriptions(Request $request)
    {
        if ($request->grouped) {
            return DB::table('subscriptions')
                ->where('completed', 1)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->groupBy('date')
                ->get()->filter(function ($r) {
                    return $r->count;
                })->filter(function ($r) {
                    if (\request('date'))
                        return $r->date == date('Y-m-d', strtotime(\request('date')));
                    return true;
                });
        }
        if ($request->children && $request->date) {
            $results = Subscription::select('id', 'user_id', 'user_type', 'amount', 'plan_id', 'plan_type', 'created_at')->with('user', 'plan')->whereDate('created_at', $request->date);
            if ($request->q)
                $results->where(function ($q) use ($request) {
                    $q->whereHas('user', function ($q) use ($request) {
                        $q->where('users.email', 'like', "%{$request->q}%")->orWhere('users.name', 'like', "%{$request->q}%");
                    });
                });
            if ($request->date)
                $results->whereDate('created_at', date('Y-m-d', strtotime($request->date)));
            if ($request->type)
                $results->where('plan_type', $request->type);
            return $results->get();
        }
        return view('partners.subscriptions');
    }
}
