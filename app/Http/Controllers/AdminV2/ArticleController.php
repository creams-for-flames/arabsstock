<?php

namespace App\Http\Controllers\AdminV2;



use App\Models\Articles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
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
                // 'slug' => 'required|ascii_only|alpha_dash|unique:articles',
                'content_en' => 'required',
                'content_ar' => 'required',
            ]);

            // Update Rules
        } else {
            return Validator::make($data, [
                'title_en' => 'required',
                'title_ar' => 'required',
                // 'slug' =>
                //     'required|ascii_only|alpha_dash|unique:articles,slug,' . $id,
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
        $index_url = route('admin.articles.datatable');
        $edit_url = route('admin.articles.edit', 0);
        $destroy_url = route('admin.articles.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.articles'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.articles.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.articles.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.article.index',
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
        $data = process_datatable_query(articles::query(), function (
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
        $index_url = route('admin.articles.index');
        $store_url = route('admin.articles.store');

        $html_breadcrumbs = [
            'title' => __('views.articles'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.article.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
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

        articles::create($input);

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.articles.index');
    }

    public function edit($id)
    {
        $data = articles::findOrFail($id);

        $index_url = route('admin.articles.index');
        $update_url = route('admin.articles.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.articles'),
            'subtitle' => __('views.Edit'),
        ];

        return view('admin_v2.article.edit', compact('html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    public function update($id, Request $request)
    {
        $lang = articles::findOrFail($id);

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

        return redirect()->route('admin.articles.index');
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
        $lang = articles::findOrFail($id);

        $lang->delete();

         \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.articles.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index_video()
    {


        $index_url = route('admin.videos.articles.datatable');
        $edit_url = route('admin.videos.articles.edit', 0);
        $destroy_url = route('admin.videos.articles.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.articles'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.videos.articles.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.videos.articles.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.article.index',
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


        $data = process_datatable_query(articles::query(), function (
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


        $index_url = route('admin.videos.articles.index');
        $store_url = route('admin.videos.articles.store');

        $html_breadcrumbs = [
            'title' => __('views.articles'),
            'subtitle' => __('views.New'),
        ];

        $is_videos_site = true;

        return view('admin_v2.article.create', compact('is_videos_site', 'html_breadcrumbs', 'index_url', 'store_url'));
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

        articles::create($input);

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.videos.articles.index');
    }

    public function edit_video($id)
    {


        $data = articles::findOrFail($id);

        $index_url = route('admin.videos.articles.index');
        $update_url = route('admin.videos.articles.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.articles'),
            'subtitle' => __('views.Edit'),
        ];

        $is_videos_site = true;

        return view('admin_v2.article.edit', compact('is_videos_site', 'html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    public function update_video($id, Request $request)
    {


        $lang = articles::findOrFail($id);

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

        return redirect()->route('admin.videos.articles.index');
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
        $lang = articles::findOrFail($id);

        $lang->delete();

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.articles.index');
    }
}
