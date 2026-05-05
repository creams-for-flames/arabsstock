<?php

namespace App\Http\Controllers\AdminV2;

use App\Http\Controllers\Controller;
use App\Jobs\CreateInvoicePDF;
use App\Models\Cities;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $html_new_path = route('admin.subscriptions.create');
        if (!$request->datatable) {
            $html_breadcrumbs = [
                'title' => __('views.UserPlans'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];
            return view(
                'admin_v2.subscriptions.index',
                compact(
                    'html_breadcrumbs',
                    'html_new_path'
                )
            );
        }
        $query = Subscription::with(['plan', 'user', 'country', 'city', 'promocode'])->whereHasMorph('user', [User::class]);

        if ($request->has('query.promocode_id'))
            $query->where('promocode_id', $request->input('query.promocode_id'));
        if ($request->has('query.plan_id'))
            $query->where('plan_id', $request->input('query.plan_id'));
        if ($request->has('query.user_id'))
            $query->where('user_id', $request->input('query.user_id'));
        if ($request->has('query.status') && in_array($request->input('query.status'), [0, 1, 2, 3]) && !in_array($request->input('query.status'), ['finished', 'all']))
            $query->where('status', $request->input('query.status'));
        if ($request->input('query.status') == 'finished')
            $query->where('status', 1)->where('ends_at', '<=', now());
        if ($request->has('query.paid')) {
            $query->where('paid', $request->input('query.paid'));
        }
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            if (isset($search) && $search != '') {
                $query = $query
                    ->whereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('stripe_id', 'like', '%' . $search . '%');
                    })->orWhere('data', 'like', "%{$search}%");
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

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['downloads' => function ($q) {
            $q->orderBy('id', 'desc');
        }, 'plan', 'user', 'payment_method', 'renewals']);
        return view('admin_v2.subscriptions.show', compact('subscription'));
    }

    public function create()
    {
        $html_breadcrumbs = [
            'title' => __('Subscriptions'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        return view('admin_v2.subscriptions.create', compact('html_breadcrumbs'));
    }

    public function store(Request $request)
    {
        $plan = Plan::findOrFail($request->plan_id);
        $this->validate($request, [
            'user_id' => ['required', Rule::exists('users', 'id')],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('for_teams', 0)],
            'payment_id' => ['nullable'],
            'city_id' => ['required'],
            'transfer' => ['nullable', 'file'],
            'credits' => [Rule::requiredIf(function () use ($request, $plan) {
                return $plan->on_demand;
            }), 'min:1'],
            'amount' => [Rule::requiredIf(function () use ($request, $plan) {
                return $plan->on_demand;
            }), 'min:0'],
        ], []);
        $user = User::find($request->user_id);

        $data = $request->only('bank');
        $data['transfer'] = $request->file('transfer') ? $request->file('transfer')->store('transfers', ['disk' => 's3']) : '';
        $city = Cities::find($request->city_id);
        $credit_price = $plan->credit_price;
        if ($plan->on_demand)
            $credit_price = number_format($request->amount / $request->credits, 2);

        $subscription = $user->subscriptions()->create([
            'plan_id' => $request->plan_id,
            'payment_method_id' => PaymentMethod::BANK,
            'quantity' => 1,
            'credit_price' => $credit_price,
            'credits' => $plan->on_demand ? $request->credits : $plan->credits_count,
            'remaining_credits' => $plan->on_demand ? $request->credits : $plan->credits_count,
            'plan_type' => $plan->type,
            'currency' => 'usd',
            'amount' => $plan->on_demand ? $request->amount : $plan->price,
            'plan_price' => $plan->on_demand ? $request->amount : $plan->price,
            'payment_id' => $request->payment_id,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'status' => Subscription::STATUS_ACTIVE,
            'completed' => 1,
            'renewal' => 0,
            'city_id' => optional($city)->id,
            'country_id' => optional($city)->country_id,
            'data' => json_encode(array_filter($data)),
            'paid' => $request->payment_id ? 1 : 0
        ]);
        dispatch(new CreateInvoicePDF($subscription));
        return redirect()->route('admin.subscriptions.index')->with('success', __('admin.success_add'));
    }

    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);
        if (!$subscription->paid)
            return view('admin_v2.subscriptions.pay', compact('subscription'));
        return view('admin_v2.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        $this->validate($request, [
            'payment_id' => ['required'],
            'city_id' => ['required'],
            'transfer' => [Rule::requiredIf(function () use ($request, $subscription) {
                return !$subscription->paid;
            }), 'file'],
        ], [
            'transfer.required' => __('views.upload_transfer_photo')
        ]);
        $data = json_decode($subscription->data, true);
        if ($request->bank)
            $data['bank'] = $request->bank;
        if ($request->file('transfer'))
            $data['transfer'] = $request->file('transfer')->store('transfers', ['disk' => 's3']);
        $city = Cities::find($request->city_id);
        $data = [
            'payment_id' => $request->payment_id,
            'city_id' => optional($city)->id,
            'country_id' => optional($city)->country_id,
            'data' => json_encode($data),
        ];
        if ($request->payment_id)
            $data['paid'] = 1;
        $subscription->update($data);
        return redirect()->route('admin.subscriptions.index')->with('success', __('admin.success_update'));
    }

    public function status(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->status = !$subscription->status;
        $subscription->save();
        return ['status' => 1, 'new_status' => $subscription->status];
    }
}
