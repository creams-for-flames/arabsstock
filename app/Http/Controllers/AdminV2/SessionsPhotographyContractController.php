<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Models\Contract;
use App\Models\Countries;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SessionsPhotographyContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function validator(array $data, $id = null)
    {
        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        // Create Rules
        if ($id == null) {
            return Validator::make($data, [
                'title_en' => 'required',
                'title_ar' => 'required',
                'slug' => 'required|ascii_only|alpha_dash|unique:contracts',
                'content_en' => 'required',
                'content_ar' => 'required',
            ]);

            // Update Rules
        } else {
            return Validator::make($data, [
                'title_en' => 'required',
                'title_ar' => 'required',
                'slug' =>
                    'required|ascii_only|alpha_dash|unique:contracts,slug,' . $id,
                'content_en' => 'required',
                'content_ar' => 'required',
            ]);
        }
    }

    public function index($type)
    {
        $index_url = route('admin.contracts.datatable',['type'=>$type]);
        $edit_url = route('admin.contracts.edit' ,['type'=>$type,'id'=>0]);
        $destroy_url = route('admin.contracts.destroy',['type'=>$type,'id'=>0]);

        $html_breadcrumbs = [
            'title' => __('views.contracts'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.contracts.create',['type'=>$type]);
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.contracts.destroy',['type'=>$type,'id'=>0]),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.sessions_photography.contracts.index',
            compact(
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url'
            )
        );
    }
    public function datatable(Request $request,$type)
    {
        $data = process_datatable_query(Contract::query(), function (
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        $index_url = route('admin.contracts.index',['type'=>$type]);
        $store_url = route('admin.contracts.store',['type'=>$type]);

        $html_breadcrumbs = [
            'title' => __('views.contracts'),
            'subtitle' => __('views.New'),
        ];
        return view('admin_v2.sessions_photography.contracts.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$type)
    {
        $input = $request->only(['title_en','title_ar','slug','content_en','content_ar']);

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        Contract::create($input);

        Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.contracts.index',['type' => $type]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($type,$id)
    {
        $data = Contract::findOrFail($id);

        $index_url = route('admin.contracts.index',['type'=>$type]);
        $update_url = route('admin.contracts.update',['type'=>$type,'id'=> $id]);

        $html_breadcrumbs = [
            'title' => __('views.contracts'),
            'subtitle' => __('views.Edit'),
        ];

        return view('admin_v2.sessions_photography.contracts.edit', compact('html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$type, $id)
    {
        $lang = Contract::findOrFail($id);

        $input = $request->only(['title_en','title_ar','slug','content_en','content_ar']);

        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lang->fill($input)->save();

        Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.contracts.index',['type'=>$type]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($type,$id)
    {
        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $lang = Contract::findOrFail($id);

        $lang->delete();

         Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.contracts.index',['type'=>$type]);
    }
}
