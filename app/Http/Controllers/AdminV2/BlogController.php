<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
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
                'image' => 'required|image',
                'content_en' => 'required',
                'content_ar' => 'required',
            ]);

            // Update Rules
        } else {
            return Validator::make($data, [
                'title_en' => 'required',
                'title_ar' => 'required',
                'image' => 'sometimes|image',
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
        $index_url = route('admin.blogs.datatable');
        $edit_url = route('admin.blogs.edit', 0);
        $destroy_url = route('admin.blogs.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.blogs'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.blogs.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.blogs.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.blog.index',
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
        $data = process_datatable_query(Blog::query(), function (
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
        $index_url = route('admin.blogs.index');
        $store_url = route('admin.blogs.store');

        $html_breadcrumbs = [
            'title' => __('views.blogs'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.blog.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
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

        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('img_blogs'), $imageName);

        $input['image']=$imageName;

        Blog::create($input);

        \Session::flash('success', trans('admin.success_add'));

        return redirect()->route('admin.blogs.index');
    }

    public function edit($id)
    {
        $data = Blog::findOrFail($id);

        $index_url = route('admin.blogs.index');
        $update_url = route('admin.blogs.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.blogs'),
            'subtitle' => __('views.Edit'),
        ];

        return view('admin_v2.blog.edit', compact('html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    public function update($id, Request $request)
    {

        $lang = Blog::findOrFail($id);

        $input = $request->all();
        $validator = $this->validator($input, $id);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        if ($request->file('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('img_blogs'), $imageName);
            $input['image']=$imageName;
        }
        else{
            $input['image']=$lang->image;
        }
        $lang->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.blogs.index');
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
        $lang = Blog::findOrFail($id);

        $lang->delete();

         \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.blogs.index');
    }


}
