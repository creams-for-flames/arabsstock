<?php

namespace App\Http\Controllers\AdminV2;



use App\Models\Pages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
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

        // Create Rules
        if ($id == null) {
            return Validator::make($data, [
                'title_en' => 'required',
                'title_ar' => 'required',
                'slug' => 'required|ascii_only|alpha_dash|unique:pages',
                'content_en' => 'required',
                'content_ar' => 'required',
            ]);

            // Update Rules
        } else {
            return Validator::make($data, [
                'title_en' => 'required',
                'title_ar' => 'required',
                'slug' =>
                    'required|ascii_only|alpha_dash|unique:pages,slug,' . $id,
                'content_en' => 'required',
                'content_ar' => 'required',
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $index_url = route('admin.pages.datatable');
        $edit_url = route('admin.pages.edit', 0);
        $destroy_url = route('admin.pages.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.pages.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.pages.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.page.index',
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

    public function datatable(Request $request)
    {
        $data = process_datatable_query(Pages::query(), function (
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
     * @return Response
     */
    public function create()
    {
        $index_url = route('admin.pages.index');
        $store_url = route('admin.pages.store');

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.page.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
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

        Pages::create($input);

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.pages.index');
    }

    public function edit($id)
    {
        $data = Pages::findOrFail($id);

        $index_url = route('admin.pages.index');
        $update_url = route('admin.pages.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.Edit'),
        ];

        return view('admin_v2.page.edit', compact('html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    public function update($id, Request $request)
    {
        $lang = Pages::findOrFail($id);

        $input = $request->all();
        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lang->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.pages.index');
    } //<--- End Method

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
        $lang = Pages::findOrFail($id);

        $lang->delete();

         \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.pages.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index_video()
    {


        $index_url = route('admin.videos.pages.datatable');
        $edit_url = route('admin.videos.pages.edit', 0);
        $destroy_url = route('admin.videos.pages.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.videos.pages.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.videos.pages.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.page.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url'
            )
        );
    }

    public function datatable_video(Request $request)
    {


        $data = process_datatable_query(Pages::query(), function (
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
     * @return Response
     */
    public function create_video()
    {


        $index_url = route('admin.videos.pages.index');
        $store_url = route('admin.videos.pages.store');

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.New'),
        ];

        $is_videos_site = true;

        return view('admin_v2.page.create', compact('is_videos_site', 'html_breadcrumbs', 'index_url', 'store_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
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

        Pages::create($input);

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.videos.pages.index');
    }

    public function edit_video($id)
    {


        $data = Pages::findOrFail($id);

        $index_url = route('admin.videos.pages.index');
        $update_url = route('admin.videos.pages.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.Edit'),
        ];

        $is_videos_site = true;

        return view('admin_v2.page.edit', compact('is_videos_site', 'html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    public function update_video($id, Request $request)
    {


        $lang = Pages::findOrFail($id);

        $input = $request->all();
        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lang->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.videos.pages.index');
    } //<--- End Method

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
        $lang = Pages::findOrFail($id);

        $lang->delete();

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.pages.index');
    }



    //vector


      public function index_vectors()
    {


        $index_url = route('admin.vectors.pages.datatable');
        $edit_url = route('admin.vectors.pages.edit', 0);
        $destroy_url = route('admin.vectors.pages.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.vectors.pages.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.vectors.pages.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.page.index',
            compact(
                'is_vectors_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url'
            )
        );
    }

    public function datatable_vectors(Request $request)
    {


        $data = process_datatable_query(Pages::query(), function (
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
     * @return Response
     */
    public function create_vectors()
    {


        $index_url = route('admin.vectors.pages.index');
        $store_url = route('admin.vectors.pages.store');

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.New'),
        ];

        $is_vectors_site = true;

        return view('admin_v2.page.create', compact('is_vectors_site', 'html_breadcrumbs', 'index_url', 'store_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
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

        Pages::create($input);

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.vectors.pages.index');
    }

    public function edit_vectors($id)
    {


        $data = Pages::findOrFail($id);

        $index_url = route('admin.vectors.pages.index');
        $update_url = route('admin.vectors.pages.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Pages'),
            'subtitle' => __('views.Edit'),
        ];

        $is_vectors_site = true;

        return view('admin_v2.page.edit', compact('is_vectors_site', 'html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    public function update_vectors($id, Request $request)
    {


        $lang = Pages::findOrFail($id);

        $input = $request->all();
        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lang->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.vectors.pages.index');
    } //<--- End Method

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
        $lang = Pages::findOrFail($id);

        $lang->delete();

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.vectors.pages.index');
    }
}
