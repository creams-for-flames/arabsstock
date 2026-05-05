<?php

namespace App\Http\Controllers\AdminV2;

use App\Http\Controllers\Auth\RegisterController;

use App\Models\User;
use App\Rules\Mobile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    protected function validator(array $data, $id = null)
    {
        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        return Validator::make($data, [
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($id)],
//            'mobile' => ['nullable', new Mobile(), Rule::unique('users', 'mobile')->whereNull('deleted_at')->ignore($id)],
            'role' => ['required', Rule::in(array_keys(config('roles')))],
            'password' => ['nullable', 'confirmed'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $index_url = route('admin.members.datatable');
        $edit_url = route('admin.members.edit', 0);
        $destroy_url = route('admin.members.destroy', 0);

        $object = new User();

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.Index'),
//            'datatable' => true,
        ];
        $html_new_path = route('admin.members.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.members.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.member.index',
            compact('html_breadcrumbs', 'html_new_path', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable(Request $request)
    {
        $query = User::with('team')->whereNotIn('id', [1, \auth()->id()]);//->withCount('published_images');
        if ($request->input('query.role'))
            $query->where('role', $request->input('query.role'));
        $search = $request->input('query.generalSearch');
        if ($request->input('query.team_id'))
            $query->where('team_id', $request->input('query.team_id'));
        $query->where(function ($query) use ($search) {
            $query->where('username', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        });
        if ($request->sort && $query->getModel()->isFillable($request->input('sort.field')))
            $query->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $query->orderBy('id', 'desc');

        $data = $query->paginate($request->input('pagination.perpage', 20), ['*'], 'pagination.page');
        /***@var $data /Illuminate\Pagination\LengthAwarePaginator */
        return [
            'meta' => [
                "page" => $data->currentPage(),
                "pages" => $data->lastPage(),
                "perpage" => $data->perPage(),
                "total" => $data->total(),
                "sort" => "desc",
                "field" => "id"
            ],
            'data' => collect($data->items())
        ];
    }

    public function create()
    {
        $index_url = route('admin.members.index');
        $store_url = route('admin.members.store');

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.member.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $_username = explode('@', strtolower($request->email))[0];
        $_username = $_username . rand(10, 100);
        $register_cont = new RegisterController();
        while ($register_cont->user_is_exsit($_username)) {
            $_username = $_username . rand(10, 100);
        }
        $request->merge([
            'password' => $request->password ? bcrypt($request->password) : null,
            'username' => $_username
        ]);
        $user = User::create(
            array_filter($request->only(['username', 'name', 'email', 'password', 'email', 'role', 'status']))
        );

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.members.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $data = User::findOrFail($id);

        if ($data->id == 1 || $data->id == Auth::user()->id) {
            \Session::flash('success', trans('admin.user_no_edit'));
            return redirect()->route('admin.members.index');
        }

        $index_url = route('admin.members.index');
        $update_url = route('admin.members.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.Edit'),
        ];

        return view('admin_v2.member.edit', compact('html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        $user = User::findOrFail($id);

        $input = $request->all();

        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        if (@$input['password']) {
            $input['password'] = bcrypt($input['password']);
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $user->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.members.index');
    }

    public function activate(Request $request, $id)
    {
        $id = explode(',', $id);
        User::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.members.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $user = User::findOrFail($id);

        if ($user->id == 1 || $user->id == Auth::user()->id) {
            return redirect()->route('admin.members.index');
        }

        $this->deleteUser($id);
        return redirect()->route('admin.members.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index_video()
    {


        $index_url = route('admin.videos.members.datatable');
        $edit_url = route('admin.videos.members.edit', 0);
        $destroy_url = route('admin.videos.members.destroy', 0);

        $object = new User();

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.videos.members.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.videos.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.videos.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.videos.members.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.member.index',
            compact('is_videos_site', 'html_breadcrumbs', 'html_new_path', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable_video(Request $request)
    {


        $data = process_datatable_query(User::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('username', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create_video()
    {


        $index_url = route('admin.videos.members.index');
        $store_url = route('admin.videos.members.store');

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.New'),
        ];

        $is_videos_site = true;

        return view('admin_v2.member.create', compact('is_videos_site', 'html_breadcrumbs', 'index_url', 'store_url'));
    }

    public function store_video(Request $request)
    {


        $input = $request->all();

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create(
            $request->only(['username', 'name', 'bio', 'password', 'email'])
        );

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.videos.members.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit_video($id)
    {


        $data = User::findOrFail($id);

        if ($data->id == 1 || $data->id == Auth::user()->id) {
            \Session::flash('success', trans('admin.user_no_edit'));
            return redirect()->route('admin.videos.members.index');
        }

        $index_url = route('admin.videos.members.index');
        $update_url = route('admin.videos.members.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.Edit'),
        ];

        $is_videos_site = true;

        return view('admin_v2.member.edit', compact('is_videos_site', 'html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update_video($id, Request $request)
    {


        $user = User::findOrFail($id);

        $input = $request->all();

        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.videos.members.index');
    }

    public function activate_video(Request $request, $id)
    {


        $id = explode(',', $id);
        User::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.members.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy_video($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $user = User::findOrFail($id);

        if ($user->id == 1 || $user->id == Auth::user()->id) {
            return redirect()->route('admin.videos.members.index');
        }

        $this->deleteUser($id);
        return redirect()->route('admin.videos.members.index');
    }


    //vectors member

    public function index_vectors()
    {


        $index_url = route('admin.vectors.members.datatable');
        $edit_url = route('admin.vectors.members.edit', 0);
        $destroy_url = route('admin.vectors.members.destroy', 0);

        $object = new User();

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.vectors.members.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vectors.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.vectors.members.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2_vectors.member.index',
            compact('is_vectors_site', 'html_breadcrumbs', 'html_new_path', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable_vectors(Request $request)
    {


        $data = process_datatable_query(User::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('username', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create_vectors()
    {


        $index_url = route('admin.vectors.members.index');
        $store_url = route('admin.vectors.members.store');

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.New'),
        ];

        $is_vectors_site = true;

        return view('admin_v2_vectors.member.create', compact('is_vectors_site', 'html_breadcrumbs', 'index_url', 'store_url'));
    }

    public function store_vectors(Request $request)
    {


        $input = $request->all();

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create(
            $request->only(['username', 'name', 'bio', 'password', 'email'])
        );

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.vectors.members.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit_vectors($id)
    {


        $data = User::findOrFail($id);

        if ($data->id == 1 || $data->id == Auth::user()->id) {
            \Session::flash('success', trans('admin.user_no_edit'));
            return redirect()->route('admin.vectors.members.index');
        }

        $index_url = route('admin.vectors.members.index');
        $update_url = route('admin.vectors.members.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Members'),
            'subtitle' => __('views.Edit'),
        ];

        $is_vectors_site = true;

        return view('admin_v2_vectors.member.edit', compact('is_vectors_site', 'html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update_vectors($id, Request $request)
    {


        $user = User::findOrFail($id);

        $input = $request->all();

        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.vectors.members.index');
    }

    public function activate_vectors(Request $request, $id)
    {


        $id = explode(',', $id);
        User::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.vectors.members.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy_vectors($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $user = User::findOrFail($id);

        if ($user->id == 1 || $user->id == Auth::user()->id) {
            return redirect()->route('admin.vectors.members.index');
        }

        $this->deleteUser($id);
        return redirect()->route('admin.vectors.members.index');
    }

    public function ajax(Request $request)
    {
        $result_set = User::select('id', 'name', 'email')->where('role', 'normal');
        if ($request->q) {
            $result_set->where(function ($q) use ($request) {
                if (is_numeric($request->q))
                    $q->where('id', $request->q);
                else
                    $q->where('name', 'like', "%{$request->q}%")->orWhere('email', 'like', "%{$request->q}%");
            });


        }
        return $result_set->take(100)->get();
    }
}
