<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Cities;
use App\Models\ImagePlan;
use App\Models\VideoPlan;
use App\Models\VectorPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class PlanController extends Controller
{
    private $apiContext;
    private $mode;
    private $client_id;
    private $secret;


    public function index()
    {
        $index_url = route('admin.plans.datatable');

        $show_url = route('admin.plans.show', 0);
        $destroy_url = route('admin.plans.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.Plans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.plans.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 1,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.plans.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 0,
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.plans.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                ],
            ],
        ];

        return view(
            'admin_v2.plan.index',
            compact(
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'destroy_url',
                'show_url'
            )
        );
    }

    public function datatable(Request $request)
    {
        $data = process_datatable_query(ImagePlan::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function($query) use ($search) {
                    $query->where('title_ar', 'like', '%' . $search . '%')
                        ->orWhere('title_en', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create()
    {
        $index_url = route('admin.plans.index');
        $store_url = route('admin.plans.store');

        $html_breadcrumbs = [
            'title' => __('views.Plans'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.plan.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }


    public function store(Request $request)
    {
        //        if (! Gate::allows('plan_create')) {
        //            return abort(401);
        //        }

        $data = $request->validate([
            'title_ar' => 'required|string|min:5',
            'title_en' => 'required|string|min:5',
            'price' => 'required|integer|min:5',
            'free' => 'integer',
            'downloads_count' => 'required|integer|min:5',
            'type' => 'required|in:package,monthly,annual',
            'status' => 'required|boolean',
        ]);

        $data['uuid'] = \App\Helper::uuid4();
        $data['slug'] = \Illuminate\Support\Str::slug($data['title_en']);

        $plan = ImagePlan::create($data);

        if ($plan->type == 'monthly') {
            \App\Contexts\Plans::create_paypal_plan($plan);
        }
        return redirect()->route('admin.plans.index');
    }


    public function show($id)
    {
        $user_plans = \App\Models\ImageSubscription::with('plan', 'user')->where('plan_id', $id)->get();
        // $user_payments = \App\Models\UserPayment::where('plan_id', $id)->get();

        $plan = ImagePlan::findOrFail($id);

        return view('admin_v2.plan.show', compact('plan', 'user_plans'));
    }


    public function destroy($id)
    {
        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $plan = ImagePlan::findOrFail($id);

        $plan->delete();

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.plans.index');
    }

    // video functions

    public function index_video()
    {


        $index_url = route('admin.videos.plans.datatable');

        $show_url = route('admin.videos.plans.show', 0);
        $destroy_url = route('admin.videos.plans.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.Plans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.videos.plans.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 1,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.videos.plans.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 0,
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.videos.plans.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                ],
            ],
        ];
        $is_videos_site = true;
        return view(
            'admin_v2.plan.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'destroy_url',
                'show_url'
            )
        );
    }

    public function datatable_video(Request $request)
    {


        $data = process_datatable_query(VideoPlan::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function($query) use ($search) {
                    $query->where('title_ar', 'like', '%' . $search . '%')
                        ->orWhere('title_en', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create_video()
    {

        $index_url = route('admin.videos.plans.index');
        $store_url = route('admin.videos.plans.store');

        $html_breadcrumbs = [
            'title' => __('views.Plans'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.plan.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }


    public function store_video(Request $request)
    {
        //        if (! Gate::allows('plan_create')) {
        //            return abort(401);
        //        }


        $data = $request->validate([
            // 'type_content' => 'required|in:images,videos',
            'title_ar' => 'required|string|min:5',
            'title_en' => 'required|string|min:5',
            'price' => 'required|integer|min:5',
            'downloads_count' => 'required|integer|',
            'type' => 'required|in:package,monthly',
            'status' => 'required|boolean',
        ]);

        $data['uuid'] = \App\Helper::uuid4();
        $data['slug'] = \Illuminate\Support\Str::slug($data['title_en']);

        $plan = VideoPlan::create($data);

        // if ($plan->type == 'monthly') {
        \App\Contexts\Plans::create_paypal_plan($plan);
        // }
        return redirect()->route('admin.videos.plans.index');
    }


    public function show_video($id)
    {

        $user_plans = \App\Models\VideoSubscription::with('plan','user')->where('plan_id', $id)->get();
        // $user_payments = \App\Models\UserPayment::where('plan_id', $id)->get();

        $plan = VideoPlan::findOrFail($id);

        return view('admin_v2.plan.show',compact('plan', 'user_plans'));
    }


    public function destroy_video($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $plan = VideoPlan::findOrFail($id);

        $plan->delete();

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.plans.index');
    }


    //vectors


    public function index_vectors()
    {


        $index_url = route('admin.vectors.plans.datatable');

        $show_url = route('admin.vectors.plans.show', 0);
        $destroy_url = route('admin.vectors.plans.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.Plans'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.vectors.plans.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 1,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.plans.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 0,
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vectors.plans.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                ],
            ],
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2.plan.index',
            compact(
                'is_vectors_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'destroy_url',
                'show_url'
            )
        );
    }

    public function datatable_vectors(Request $request)
    {


        $data = process_datatable_query(VectorPlan::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function($query) use ($search) {
                    $query->where('title_ar', 'like', '%' . $search . '%')
                        ->orWhere('title_en', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create_vectors()
    {

        $index_url = route('admin.vectors.plans.index');
        $store_url = route('admin.vectors.plans.store');

        $html_breadcrumbs = [
            'title' => __('views.Plans'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.plan.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }


    public function store_vectors(Request $request)
    {
        //        if (! Gate::allows('plan_create')) {
        //            return abort(401);
        //        }


        // return $request->all();

        $data = $request->validate([
            // 'type_content' => 'required|in:images,videos',
            'title_ar' => 'required|string|min:5',
            'title_en' => 'required|string|min:5',
            'price' => 'required|integer|min:5',
            'downloads_count' => 'required|integer|min:5',
            'type' => 'required|in:package,monthly',
            'status' => 'required|boolean',
        ]);

        $data['uuid'] = \App\Helper::uuid4();
        $data['slug'] = \Illuminate\Support\Str::slug($data['title_en']);


        $plan = VectorPlan::create($data);

        // return $plan;


        if ($plan->type == 'monthly') {

            \App\Contexts\Plans::create_paypal_plan($plan);
        }


        return redirect()->route('admin.vectors.plans.index');
    }


    public function show_vectors($id)
    {

        $user_plans = \App\Models\VectorSubscription::with('plan','user')->where('plan_id', $id)->get();
        // $user_payments = \App\Models\UserPayment::where('plan_id', $id)->get();

        $plan = VectorPlan::findOrFail($id);
        $is_vectors_site = true ;

        return view('admin_v2.plan.show',compact('plan', 'user_plans','is_vectors_site'));
    }


    public function destroy_vectors($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $plan = VectorPlan::findOrFail($id);

        $plan->delete();

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.vectors.plans.index');
    }

    public function cities(Request $request)
    {
        $result_set = Cities::select('id', 'name_ar', 'country_id')->has('country')->with('country');
        if ($request->q) {
            $result_set->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', "%{$request->q}%")->orWhere('name_en', 'like', "%{$request->q}%")->orWhereHas('country', function ($q) use ($request) {
                    $q->where('name_ar', 'like', "%{$request->q}%")->orWhere('name_en', 'like', "%{$request->q}%");
                });
            });
        }
        return $result_set->take(100)->get();
    }
}
