<?php

namespace App\Http\Controllers\AdminV2;

use App\Cashier\Coupon;
use App\Jobs\CreatePromocodePaypalPlans;
use App\Models\Book;
use App\Models\Bundle;
use App\Models\Consult;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Promocode;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PromocodesController extends Controller
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
                'title' => __('views.Promocodes'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];
            $html_new_path = route('admin.promocodes.create');
            return view(
                'admin_v2.promocodes.index',
                compact(
                    'html_breadcrumbs',
                    'html_new_path',
                )
            );
        }
        $query = Promocode::withCount([
            'subscriptions as subscriptions_sum' => function ($query) {
                $query->select(\DB::raw("SUM(subscriptions.amount) as paidsum"))->where('subscriptions.status', 1);
            }
        ])->withCount([
            'subscriptions as discounts_sum' => function ($query) {
                $query->select(\DB::raw("SUM(subscriptions.plan_price) - SUM(subscriptions.amount) as paidsum"))->where('subscriptions.status', 1);
            }
        ])->withCount(['subscriptions' => function ($q) {
            $q->where('subscriptions.status', 1);
        }])->orderBy('id', 'desc');
        if ($request->input('query.id'))
            $query->where('id', '=', $request->input('query.id'));
        if ($request->status)
            $query->where('status', '=', @['shared' => 1, 'draft' => 0][$request->status]);
        if ($request->admin_id)
            $query->where('admin_id', '=', $request->admin_id);
        if ($request->q)
            $query->where(function ($q) use ($request) {
                $q->where('title_ar', 'like', "%{$request->q}%")
                    ->orWhere('title_ar', 'like', "%{$request->q}%");
            });
        $result_set = $query->paginate(20);
        return [
            'meta' => [
                "page" => $result_set->currentPage(),
                "pages" => $result_set->lastPage(),
                "perpage" => $result_set->perPage(),
                "total" => $result_set->total(),
                "sort" => "desc",
                "field" => "id"
            ],
            'data' => $result_set->items()
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin_v2.promocodes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                'title_ar' => ['required'],
                'title_en' => ['required'],
                'code' => ['required', Rule::unique('promocodes', 'code')->whereNull('deleted_at')],
                'expired_at' => ['required', 'date'],
                'type' => ['required', Rule::in(['percent', 'amount'])],
                'value' => ['required', 'numeric'],
                'max_usage' => ['required', 'numeric'],
                'plans' => ['required', 'array'],
            ], ['plans.required' => 'يجب اختيار باقة على الأقل']);
        $request->merge([
            'value' => intval($request->value),
            'max_usage' => intval($request->max_usage),
            'status' => intval($request->status),
        ]);
        $promocode = Promocode::create($request->all());
        $promocode->plans()->sync($request->input('plans', []));
        $this->dispatch(new CreatePromocodePaypalPlans($promocode));
        $stripe_coupon = \Stripe\Coupon::create([
            'duration' => 'once',
            'id' => $promocode->code,
            "{$promocode->type}_off" => $promocode->type == 'amount' ? $promocode->value * 100 : $promocode->value,
            'currency' => 'usd',
            'name' => $promocode->title_en,
        ]);
        return redirect()->route('admin.promocodes.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promocode = Promocode::with('plans')->findOrFail($id);
        return view('admin_v2.promocodes.edit', compact('promocode'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,
            [
                'title_ar' => ['required'],
                'title_en' => ['required'],
                'expired_at' => ['required', 'date'],
                'max_usage' => ['required', 'numeric'],
                'plans' => ['required', 'array'],
            ], ['plans.required' => 'يجب اختيار باقة على الأقل']);
        $promocode = Promocode::findOrFail($id);
        $request->merge([
            'max_usage' => intval($request->max_usage),
            'status' => intval($request->status),
            'max_users' => intval($request->max_users),
        ]);
        $promocode->update($request->all());
        $promocode->plans()->sync($request->input('plans', []));
        $this->dispatch(new CreatePromocodePaypalPlans($promocode));
        return redirect()->route('admin.promocodes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promocode = Promocode::findOrFail($id);
        $subscriptions_count = $promocode->subscriptions()->count();
        if ($subscriptions_count) {
            return redirect()->route('admin.promocodes.index')->with('error', trans('misc.cant_delete', ['count' => $subscriptions_count]));
        }
        try {
            $stripe_coupon = \Stripe\Coupon::retrieve($promocode->code);
            $stripe_coupon->delete();
        } catch (\Exception $exception) {
        }
        $promocode->delete();
        \Session::flash('success', trans('misc.success_delete'));
        return redirect()->route('admin.promocodes.index');
    }

    public function status($id)
    {
        $promocode = Promocode::findOrFail($id);
        $promocode->status = $promocode->status ? 0 : 1;
        $promocode->save();
        return ['status' => 'success', 'msg' => status('success.msg'), 'new_status' => $promocode->status];
    }

}
