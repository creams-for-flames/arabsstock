<?php

namespace App\Http\Controllers\AdminV2;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->datatable) {
            $html_new_path = route('admin.teams.create');
            $html_breadcrumbs = [
                'title' => __('Teams'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];
            return view(
                'admin_v2.teams.index',
                compact(
                    'html_breadcrumbs',
                    'html_new_path'
                )
            );
        }
        $query = Team::with(['leader'])->withCount('subscriptions')->withCount('users');
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            if (isset($search) && $search != '') {
                $query = $query
                    ->whereHas('leader', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })->orWhere('name', 'like', "%{$search}%");
            }

            return $query;
        });

        return $data;
    }


    public function create()
    {
        return view('admin_v2.teams.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required'],
            'leader_id' => ['required', Rule::exists('users', 'id'), Rule::unique('teams', 'leader_id')],
        ], [
            'leader_id.unique' => __('This user is on another team')
        ]);
        $user = User::find($request->leader_id);
        if ($user->team)
            throw ValidationException::withMessages(['leader_id' => __('This user is on another team')]);
        $team = Team::create($request->only('name', 'leader_id'));
        User::find($request->leader_id)->update(['team_id' => $team->id]);
        return redirect()->route('admin.teams.index')->with('success', __('admin.success_add'));
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        return view('admin_v2.teams.edit', compact('team'));
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $this->validate($request, [
            'name' => ['required'],
            'leader_id' => ['required', Rule::exists('users', 'id'), Rule::unique('teams', 'leader_id')->ignore($id)],
        ], [
            'leader_id.unique' => __('This user is on another team')
        ]);
        $team->update($request->only('name', 'leader_id'));
        return redirect()->route('admin.teams.index')->with('success', __('admin.success_update'));
    }


    public function ajax(Request $request)
    {
        $result_set = Team::select('id', 'name', 'leader_id');
        if ($request->q) {
            $result_set->where(function ($q) use ($request) {
                if (is_numeric($request->q))
                    $q->where('id', $request->q);
                else
                    $q->where('name', 'like', "%{$request->q}%");
            });


        }
        return $result_set->take(100)->get();
    }


    public function subscriptions(Request $request)
    {
        $html_new_path = route('admin.teams.create_subscription');

        if (!$request->datatable) {
            $html_breadcrumbs = [
                'title' => __('views.TeamsPlans'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];
            return view(
                'admin_v2.teams.subscriptions',
                compact(
                    'html_breadcrumbs',
                    'html_new_path'
                )
            );
        }
        $query = Subscription::with(['plan', 'user', 'country', 'city', 'promocode'])->whereHasMorph('user', [Team::class]);
        if ($request->has('query.promocode_id'))
            $query->where('promocode_id', $request->input('query.promocode_id'));
        if ($request->has('query.plan_id'))
            $query->where('plan_id', $request->input('query.plan_id'));
        if ($request->has('query.team_id'))
            $query->where('user_id', $request->input('query.team_id'));
        if ($request->has('query.status') && in_array($request->input('query.status'), [0, 1, 2, 3]) && !in_array($request->input('query.status'), ['finished', 'all']))
            $query->where('status', $request->input('query.status'));
        if ($request->input('query.status') == 'finished')
            $query->where('status', 1)->where('ends_at', '<=', now());
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

    public function create_subscription()
    {
        $html_breadcrumbs = [
            'title' => __('Subscriptions'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        return view('admin_v2.teams.create-subscription', compact('html_breadcrumbs'));
    }

    public function store_subscription(Request $request)
    {
        $this->validate($request, [
            'team_id' => ['required', Rule::exists('teams', 'id')],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('for_teams', 1)],
            'payment_id' => ['required'],
            'city_id' => ['required'],
            'transfer' => ['required', 'file'],
        ], []);
        $team = Team::find($request->team_id);
        $plan = Plan::find($request->plan_id);
        $data = $request->only('bank');
        $data['transfer'] = $request->file('transfer')->store('transfers', ['disk' => 's3']);
        $city = Cities::find($request->city_id);
        $team->subscriptions()->create([
            'plan_id' => $request->plan_id,
            'payment_method_id' => 6,//wire transfere
            'quantity' => 1,
            'remaining_credits' => $plan->credits_count,
            'plan_type' => $plan->type,
            'currency' => 'usd',
            'amount' => $plan->price,
            'plan_price' => $plan->price,
            'payment_id' => $request->payment_id,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'status' => Subscription::STATUS_ACTIVE,
            'completed' => 1,
            'renewal' => 0,
            'city_id' => optional($city)->id,
            'country_id' => optional($city)->country_id,
            'data' => json_encode($data),
        ]);
        return redirect()->route('admin.teams.subscriptions')->with('success', __('admin.success_add'));
    }


}
