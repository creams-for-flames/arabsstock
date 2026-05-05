<?php

namespace App\Http\Controllers\AdminV2;


use App\Models\User;
use App\Models\Slider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
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
            'email' => 'required|email|max:255|unique:users,id,' . $id,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $index_url = route('admin.super.slider.datatable');
        $edit_url = route('admin.super.slider.edit', 0);
        $destroy_url = route('admin.super.slider.destroy', 0);

        $object = new User();

        $html_breadcrumbs = [
            'title' => __('views.slider'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.super.slider.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.super.slider.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.super.slider.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.super.slider.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];
        $is_super_site=true;
        return view(
            'admin_v2_super.slider.index',
            compact('html_breadcrumbs', 'html_new_path','is_super_site', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable(Request $request)
    {
        $data = process_datatable_query(Slider::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                });
        });

        return $data;
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

        $user = User::create(
            $request->only(['username', 'name', 'bio', 'password', 'email'])
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
        $user = Slider::findOrFail($id);

        Slider::findOrFail($id)->delete();
        return redirect()->route('admin.super.slider.index');
    }



}
