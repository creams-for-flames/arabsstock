<?php

namespace App\Http\Controllers\AdminV2;

use App\Category;
use App\Http\Middleware\ImageDownload;
use App\Models\AdminCollection;
use App\Models\AdminCollectionImage;
use App\Models\AdminCollectionVideo;
use App\Models\Image;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoriesRequest;
use App\Http\Requests\Admin\UpdateCategoriesRequest;

class AdminCollectionController extends Controller
{
    /**
     * Display a listing of Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $index_url = route('admin.admin_collections.datatable');
        $edit_url = route('admin.admin_collections.edit', 0);
        $destroy_url = route('admin.admin_collections.destroy', 0);
        $dash_link = route('admin.admin_collections.dash.index', 0);

        $object = new AdminCollection();

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.admin_collections.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => '1',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.admin_collections.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => '0',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.admin_collections.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                ],
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.admin_collections.destroy', 0),
                'method' => 'delete',
                'confirm' => __(
                    'views.Are you sure to delete :number selected records ?',
                    ['number' => 0]
                ),
            ],
        ];

        return view(
            'admin_v2.admin_collection.index',
            compact(
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'dash_link'
            )
        );
    }

    /**
     * Delete all selected Category at once.
     *
     * @param Request $request
     */

    public function datatable(Request $request)
    {
        $query = AdminCollection::withCount(['images as count' => function ($q) {
            $q->whereHas('downloads');
        }]);
        if ($request->input('query'))
            $query->where(function (
                $query,
                $search
            ) {
                return $query
                    ->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
            });
        $data = process_datatable_query($query);

        return $data;
    }

    public function select2(Request $request)
    {
        $search = $request->get('q', '');
        $data = AdminCollection::where('title', 'like', '%' . $search . '%')->paginate()->toArray();
        array_unshift($data['data'], [
            'id' => 'null',
            'title' => __('root'),
        ]);
        return $data;
    }

    /**
     * Show the form for creating new Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $index_url = route('admin.admin_collections.index');
        $store_url = route('admin.admin_collections.store');

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.New'),
        ];

        return view(
            'admin_v2.admin_collection.create',
            compact('html_breadcrumbs', 'index_url', 'store_url')
        );
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param \App\Http\Requests\StoreCategoriesRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*if (! Gate::allows('category_create')) {
            return abort(401);
        }*/

        $rules = [
            'title' => 'required',
            'description' => 'string',
            'status' => 'required',
            'username' => 'required|min:3|max:15|unique:users|',
            'email' => 'required|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'in_random_home' => 'required',
        ];

        $this->validate($request, $rules);
        $user = User::where('email', strtolower($request->get('email')))->get();
        if (count($user) > 0) {
            \Session::flash('error', trans('admin.user_found'));
            return redirect()->back()->withInput($request->input());
        } else {
            $category = AdminCollection::create($request->all());
            $token = str_random(75);

            $user = User::create([
                'username' => $request->get('username'),
                'name' => $request->get('username'),
                'bio' => '',
                'password' => bcrypt($request->get('password')),
                'email' => strtolower($request->get('email')),
                'avatar' => 'default.jpg',
                'cover' => 'cover.jpg',
                'status' => 'active',
                'type_account' => '1',
                'website' => '',
                'twitter' => '',
                'paypal_account' => '',
                'activation_code' => '',
                'oauth_uid' => '',
                'oauth_provider' => '',
                'token' => $token,
                'role' => 'editor_image',
            ]);

            $category->user_id = $user->id;
            $category->save();

            \Session::flash('success', trans('admin.success_added'));

            return redirect()->route('admin.admin_collections.index');
        }


    }

    /**
     * Show the form for editing Category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $collection = AdminCollection::with('images')->findOrFail($id);
        $user = User::findOrFail($collection->user_id);

        $index_url = route('admin.admin_collections.index');
        $update_url = route('admin.admin_collections.update', $id);
        $destroy_url = route('admin.admin_collections.delete_image', ['id' => 'id', 'image_id' => '0']);

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.edit'),
        ];

        return view(
            'admin_v2.admin_collection.edit',
            compact('html_breadcrumbs', 'collection', 'user', 'index_url', 'update_url', 'destroy_url')
        );
    }

    /**
     * Update Category in storage.
     *
     * @param \App\Http\Requests\UpdateCategoriesRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'title' => 'sometimes|required',
            'id' => 'required',
            'description' => 'sometimes|required',
            'status' => 'sometimes|required',
            'username' => 'sometimes|required|min:3|max:15|unique:users',
            'email' => 'sometimes|required|max:255|unique:users',
            'in_random_home' => 'required',
        ];

        $this->validate($request, $rules);
        $category = AdminCollection::findOrFail($request->get('id'));
        $category->update($request->all());

        $user = User::findOrFail($category->user_id);
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }
        $user->save();
        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.admin_collections.index');
    }

    public function activate(Request $request, $id)
    {
        $id = explode(',', $id);
        AdminCollection::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.admin_collections.index');
    }


    /**
     * Remove Category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = explode(',', $id);
        AdminCollection::destroy($id);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.admin_collections.index');
    }

    public function delete_image($id)
    {
        $image = AdminCollectionImage::where('image_id', $id);
        if ($image) {
            $image->delete();
        }

        return response()->json(['status' => true]);
    }

    public function index_dash(Request $request, $id)
    {
        $index_url = route('admin.admin_collections.index', $id);
        $datatable_url = route('admin.admin_collections.dash.datatable', $id);
        $edit_member_url = route('admin.members.edit', 0);

        $total_price = 0;

        $images_ids = AdminCollectionImage::where(
            'admin_collection_id',
            $id
        )->pluck('image_id');

        $images = \App\Models\ImageDownload::whereIn(
            'image_downloads.image_id',
            $images_ids->toArray()
        );
        if (
            $request->get('fillter')['Status']['fromPrice'] &&
            $request->get('fillter')['Status']['toPrice']
        ) {
            $images->whereBetween('price', [
                $request->get('fromPrice'),
                $request->get('toPrice'),
            ]);
        }

        $query = $images
            ->Join('images', 'image_downloads.image_id', '=', 'images.id')
            ->leftJoin(
                'image_subscriptions',
                'image_subscriptions.id',
                '=',
                'image_downloads.user_plan_id'
            )
            ->where('image_subscriptions.starts_at', '>=', date('Y-m-d'))
            ->where('image_subscriptions.download_remaining', '>', 0)
            ->leftJoin('image_plans', 'image_subscriptions.plan_id', '=', 'image_plans.id')
            ->select(
                \DB::raw('price/downloads_count as difference'),
                'images.*',
                'image_subscriptions.*',
                'image_plans.*'
            )
            ->get();

        foreach ($query as $queryItem) {
            $total_price +=
                ceil($queryItem->price) / $queryItem->downloads_count;

        }

        $total_price = number_format($total_price, 2, '.', '');

        // TODO change name from dash to PerformanceReports
        $html_breadcrumbs = [
            'title' => __('views.PerformanceReports'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        return view(
            'admin_v2.admin_collection.dash.index',
            compact('html_breadcrumbs', 'index_url', 'datatable_url', 'edit_member_url', 'total_price', 'id')
        );
    }

    public function datatable_dash(Request $request, $id)
    {
        $images_ids = AdminCollectionImage::where(
            'admin_collection_id',
            $id
        )->pluck('image_id');

        $images = \App\Models\ImageDownload::whereIn(
            'image_downloads.image_id',
            $images_ids->toArray()
        );

        $query = $images
            ->Join('images', 'image_downloads.image_id', '=', 'images.id')
            ->select('images.*', 'image_subscriptions.*', 'image_plans.*', 'image_downloads.*')
            ->leftJoin(
                'image_subscriptions',
                'image_subscriptions.id',
                '=',
                'image_downloads.user_plan_id'
            )
            ->where('image_subscriptions.starts_at', '>=', date('Y-m-d'))
            ->where('image_subscriptions.download_remaining', '>', 0)
            ->leftJoin('image_plans', 'image_subscriptions.plan_id', '=', 'image_plans.id');

        if ($request->get('query')) {
            if (
                isset($request->get('query')['fillter']['from_date']) &&
                isset($request->get('query')['fillter']['to_date'])
            ) {
                $query = $query
                    ->whereDate(
                        'image_downloads.date',
                        '>=',
                        Carbon::parse(
                            $request->get('query')['fillter']['from_date']
                        )
                    )
                    ->whereDate(
                        'image_downloads.date',
                        '<=',
                        Carbon::parse(
                            $request->get('query')['fillter']['to_date']
                        )
                    );
            }

            if (
                isset($request->get('query')['fillter']['fromPrice']) &&
                isset($request->get('query')['fillter']['toPrice'])
            ) {
                $query = $query->whereRaw(
                    "(price/downloads_count) between " .
                    $request->get('query')['fillter']['fromPrice'] .
                    " AND " .
                    $request->get('query')['fillter']['toPrice'] .
                    ""
                );
            }
        }

        $search_params = $request->get('query', []);
        if ($search_params === '') {
            $search_params = [];
        }

        $search_params['generalSearch'] = isset($search_params['generalSearch'])
            ? $search_params['generalSearch']
            : '';

        $total = $query->count();

        $filteredRecords = $query->count();
        $pagination = request()->get('pagination');

        $page = $pagination['page'] - 1;

        $perpage = $pagination['perpage'];
        $query->offset($page * $perpage)->limit($perpage);
        $datatable = datatables($query)
            ->order(function ($query) {
            })
            ->setTotalRecords($total)
            ->setFilteredRecords($filteredRecords)
            ->toArray();

        $datatable['meta'] = [
            "page" => $datatable['input']['pagination']['page'],
            "pages" => isset($datatable['input']['pagination']['pages'])
                ? $datatable['input']['pagination']['pages']
                : 1,
            "perpage" => $datatable['input']['pagination']['perpage'],
            "total" => $filteredRecords,
            "sort" => request()->get('sort')['sort'],
            "field" => request()->get('sort')['field'],
        ];
        return $datatable;
    }

    /**
     * Display a listing of Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_video()
    {


        $index_url = route('admin.videos.admin_collections.datatable');
        $edit_url = route('admin.videos.admin_collections.edit', 0);
        $destroy_url = route('admin.videos.admin_collections.destroy', 0);
        $dash_link = 'no_link';

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.videos.admin_collections.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => '1',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.videos.admin_collections.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => '0',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.videos.admin_collections.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                ],
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.videos.admin_collections.destroy', 0),
                'method' => 'delete',
                'confirm' => __(
                    'views.Are you sure to delete :number selected records ?',
                    ['number' => 0]
                ),
            ],
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.admin_collection.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'dash_link'
            )
        );
    }

    /**
     * Delete all selected Category at once.
     *
     * @param Request $request
     */

    public function datatable_video(Request $request)
    {
        $query = AdminCollection::withCount(['videos as count' => function ($q) {
            $q->whereHas('downloads');
        }]);
        if ($request->input('query'))
            $query->where(function (
                $query,
                $search
            ) {
                return $query
                    ->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
            });
        $data = process_datatable_query($query);

        return $data;
    }

    public function select2_video(Request $request)
    {


        $search = $request->get('q', '');
        $data = AdminCollection::where('title', 'like', '%' . $search . '%')->paginate()->toArray();
        array_unshift($data['data'], [
            'id' => 'null',
            'title' => __('root'),
        ]);
        return $data;
    }

    /**
     * Show the form for creating new Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_video()
    {


        $index_url = route('admin.videos.admin_collections.index');
        $store_url = route('admin.videos.admin_collections.store');

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.New'),
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.admin_collection.create',
            compact('is_videos_site', 'html_breadcrumbs', 'index_url', 'store_url')
        );
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param \App\Http\Requests\StoreCategoriesRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store_video(Request $request)
    {
        //


        $rules = [
            'title' => 'required',
            'description' => 'string',
            'status' => 'required',
            'username' => 'required|min:3|max:15|unique:users|',
            'email' => 'required|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);
        $user = User::where('email', strtolower($request->get('email')))->get();
        if (count($user) > 0) {
            \Session::flash('error', trans('admin.user_found'));
            return redirect()->back()->withInput($request->input());
        } else {
            $category = AdminCollection::create($request->all());
            $token = str_random(75);
            $user = User::create([
                'username' => $request->get('username'),
                'name' => $request->get('username'),
                'bio' => '',
                'password' => bcrypt($request->get('password')),
                'email' => strtolower($request->get('email')),
                'avatar' => 'default.jpg',
                'cover' => 'cover.jpg',
                'status' => 'active',
                'type_account' => '1',
                'website' => '',
                'twitter' => '',
                'paypal_account' => '',
                'activation_code' => '',
                'oauth_uid' => '',
                'oauth_provider' => '',
                'token' => $token,
                'role' => 'editor_image',
            ]);

            $category->user_id = $user->id;
            $category->save();

            \Session::flash('success', trans('admin.success_added'));

            return redirect()->route('admin.videos.admin_collections.index');
        }


    }

    /**
     * Show the form for editing Category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit_video($id)
    {


        $index_url = route('admin.videos.admin_collections.index');
        $update_url = route('admin.videos.admin_collections.update', $id);
        $destroy_url = route('admin.videos.admin_collections.delete_image', ['id' => 'id', 'image_id' => '0']);

        $collection = AdminCollection::with('videos')->findOrFail($id);
        $user = User::findOrFail($collection->user_id);

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.edit'),
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.admin_collection.edit',
            compact('is_videos_site', 'html_breadcrumbs', 'collection', 'user', 'index_url', 'update_url', 'destroy_url')
        );
    }

    /**
     * Update Category in storage.
     *
     * @param \App\Http\Requests\UpdateCategoriesRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_video(Request $request)
    {


        $rules = [
            'title' => 'sometimes|required',
            'id' => 'required',
            'description' => 'sometimes|required',
            'status' => 'sometimes|required',
            'username' => 'sometimes|required|min:3|max:15|unique:users',
            'email' => 'sometimes|required|max:255|unique:users',
        ];

        $this->validate($request, $rules);
        $category = AdminCollection::findOrFail($request->get('id'));
        $category->update($request->all());

        $user = User::findOrFail($category->user_id);
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }
        $user->save();
        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.admin_collections.index');
    }

    public function activate_video(Request $request, $id)
    {


        $id = explode(',', $id);
        AdminCollection::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.admin_collections.index');
    }


    /**
     * Remove Category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_video($id)
    {


        $id = explode(',', $id);
        AdminCollection::destroy($id);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.admin_collections.index');
    }

    public function delete_video($id)
    {


        $image = AdminCollectionImage::where('image_id', $id);
        if ($image) {
            $image->delete();
        }

        return response()->json(['status' => true]);
    }


    //vector function


    public function index_vector()
    {


        $index_url = route('admin.vectors.admin_collections.datatable');
        $edit_url = route('admin.vectors.admin_collections.edit', 0);
        $destroy_url = route('admin.vectors.admin_collections.destroy', 0);
        $dash_link = 'no_link';

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.vectors.admin_collections.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => '1',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.admin_collections.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => '0',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vectors.admin_collections.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                ],
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.vectors.admin_collections.destroy', 0),
                'method' => 'delete',
                'confirm' => __(
                    'views.Are you sure to delete :number selected records ?',
                    ['number' => 0]
                ),
            ],
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.admin_collection.index',
            compact(
                'is_vectors_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'dash_link'
            )
        );
    }

    /**
     * Delete all selected Category at once.
     *
     * @param Request $request
     */

    public function datatable_vector(Request $request)
    {
        $query = AdminCollection::withCount(['vectors as count' => function ($q) {
            $q->whereHas('downloads');
        }]);
        if ($request->input('query'))
            $query->where(function (
                $query,
                $search
            ) {
                return $query
                    ->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
            });
        $data = process_datatable_query($query);

        return $data;
    }

    public function select2_vector(Request $request)
    {


        $search = $request->get('q', '');
        $data = AdminCollection::where('title', 'like', '%' . $search . '%')->paginate()->toArray();
        array_unshift($data['data'], [
            'id' => 'null',
            'title' => __('root'),
        ]);
        return $data;
    }

    /**
     * Show the form for creating new Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_vector()
    {


        $index_url = route('admin.vectors.admin_collections.index');
        $store_url = route('admin.vectors.admin_collections.store');

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.New'),
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.admin_collection.create',
            compact('is_vectors_site', 'html_breadcrumbs', 'index_url', 'store_url')
        );
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param \App\Http\Requests\StoreCategoriesRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store_vector(Request $request)
    {
        //


        $rules = [
            'title' => 'required',
            'description' => 'string',
            'status' => 'required',
            'username' => 'required|min:3|max:15|unique:users|',
            'email' => 'required|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);
        $user = User::where('email', strtolower($request->get('email')))->get();
        if (count($user) > 0) {
            \Session::flash('error', trans('admin.user_found'));
            return redirect()->back()->withInput($request->input());
        } else {
            $category = AdminCollection::create($request->all());
            $token = str_random(75);
            $user = User::create([
                'username' => $request->get('username'),
                'name' => $request->get('username'),
                'bio' => '',
                'password' => bcrypt($request->get('password')),
                'email' => strtolower($request->get('email')),
                'avatar' => 'default.jpg',
                'cover' => 'cover.jpg',
                'status' => 'active',
                'type_account' => '1',
                'website' => '',
                'twitter' => '',
                'paypal_account' => '',
                'activation_code' => '',
                'oauth_uid' => '',
                'oauth_provider' => '',
                'token' => $token,
                'role' => 'editor_image',
            ]);

            $category->user_id = $user->id;
            $category->save();

            \Session::flash('success', trans('admin.success_added'));

            return redirect()->route('admin.videos.admin_collections.index');
        }


    }

    /**
     * Show the form for editing Category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit_vector($id)
    {


        $index_url = route('admin.vectors.admin_collections.index');
        $update_url = route('admin.vectors.admin_collections.update', $id);
        $destroy_url = route('admin.vectors.admin_collections.delete_image', ['id' => 'id', 'image_id' => '0']);

        $collection = AdminCollection::with('vectors')->findOrFail($id);
        $user = User::findOrFail($collection->user_id);

        $html_breadcrumbs = [
            'title' => __('views.Collections'),
            'subtitle' => __('views.edit'),
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.admin_collection.edit',
            compact('is_vectors_site', 'html_breadcrumbs', 'collection', 'user', 'index_url', 'update_url', 'destroy_url')
        );
    }

    /**
     * Update Category in storage.
     *
     * @param \App\Http\Requests\UpdateCategoriesRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_vector(Request $request)
    {


        $rules = [
            'title' => 'sometimes|required',
            'id' => 'required',
            'description' => 'sometimes|required',
            'status' => 'sometimes|required',
            'username' => 'sometimes|required|min:3|max:15|unique:users',
            'email' => 'sometimes|required|max:255|unique:users',
        ];

        $this->validate($request, $rules);
        $category = AdminCollection::findOrFail($request->get('id'));
        $category->update($request->all());

        $user = User::findOrFail($category->user_id);
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }
        $user->save();
        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.admin_collections.index');
    }

    public function activate_vector(Request $request, $id)
    {


        $id = explode(',', $id);
        AdminCollection::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.admin_collections.index');
    }


    /**
     * Remove Category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_vector($id)
    {


        $id = explode(',', $id);
        AdminCollection::destroy($id);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.admin_collections.index');
    }

    public function delete_vector($id)
    {


        $image = AdminCollectionImage::where('image_id', $id);
        if ($image) {
            $image->delete();
        }

        return response()->json(['status' => true]);
    }
}
