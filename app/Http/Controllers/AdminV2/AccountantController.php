<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\ContributorContentExport;
use App\Export\ContributorExport;
use App\Models\Contributor;
use App\Models\ContributorImage;
use App\Models\ContributorVector;
use App\Models\ContributorVideo;
use App\Models\Image;
use App\Models\User;
use App\Models\Vector;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AccountantController extends Controller
{
    public function downloads(Request $request)
    {
        return view('admin_v2.accountant.downloads');
    }

    public function payments(Request $request)
    {
        return view('admin_v2.accountant.payments');
    }

    public function contributor_downloads(Request $request)
    {
        return view('admin_v2.accountant.contributor_downloads');
    }

    public function contributor_downloads_statistics(Request $request)
    {
        if ($request->contributor_id)
            $query->where('user_id', $request->contributor_id)->where('user_type', Contributor::class);
        if ($request->user_id)
            $query->where('user_id', $request->user_id)->where('user_type', User::class);
        if ($request->date_from && $request->date_from) {
            $from = date('Y-m-d', strtotime($request->date_from));
            $to = date('Y-m-d', strtotime($request->date_to));
            $query = $query->whereDate('date', '>=', $from);
            $query = $query->whereDate('date', '<=', $to);
        }
        return Excel::download(new ContributorContentExport($query->get()), Str::slug(basename($type)) . 's-' . now() . '.xlsx');
    }

    public function payouts(Request $request)
    {
        return view('admin_v2.accountant.payouts');
    }

    public function contents(Request $request)
    {
        return view('admin_v2.accountant.contents');
    }

    public function export_contents(Request $request)
    {
        $this->validate($request, [
            'type' => ['required', Rule::in(Image::class, Video::class, Vector::class)]
        ]);
        $type = $request->type;
        $query = $type::withoutGlobalScopes()->with(
            ['user']
        );
        if ($request->contributor_id)
            $query->where('user_id', $request->contributor_id)->where('user_type', Contributor::class);
        if ($request->user_id)
            $query->where('user_id', $request->user_id)->where('user_type', User::class);
        if ($request->date_from && $request->date_from) {
            $from = date('Y-m-d', strtotime($request->date_from));
            $to = date('Y-m-d', strtotime($request->date_to));
            $query = $query->whereDate('date', '>=', $from);
            $query = $query->whereDate('date', '<=', $to);
        }
        return Excel::download(new ContributorContentExport($query->get()), Str::slug(basename($type)) . 's-' . now() . '.xlsx');
    }
}
