<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\VectorCategory;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $index_url = route('admin.categories.datatable');
        $edit_url = route('admin.categories.edit', 0);
        $destroy_url = route('admin.categories.destroy', 0);

        $object = new ImageCategory();

        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.categories.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'on',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.categories.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'off',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.categories.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.categories.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.category.index',
            compact(
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'object'
            )
        );
    }

    public function datatable(Request $request)
    {
        $data = process_datatable_query(ImageCategory::query()->orderBy('sort'), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('name_ar', 'like', '%' . $search . '%')
                        ->orWhere('name_en', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create()
    {
        $index_url = route('admin.categories.index');
        $store_url = route('admin.categories.store');

        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.New'),
        ];

        return view('admin_v2.category.create', compact('html_breadcrumbs', 'index_url', 'store_url'));
    }

    public function store(Request $request)
    {
        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        $rules = [
            'name_en' => 'required',
            'name_ar' => 'required',
            'cities_and_landmarks' => 'required',
            'people' => 'required',
            'slug' => 'required|ascii_only|unique:image_categories',
            'in_random_home_image' => 'required',
            'sort' => 'required|integer'
        ];

        $this->validate($request, $rules);

        if ($request->hasFile('thumbnail')) {
            $extension = $request
                ->file('thumbnail')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('thumbnail')->getMimeType();
            $sizeFile = $request->file('thumbnail')->getSize();
            $thumbnail =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('thumbnail')->storeAs('uploads/img-category/', $thumbnail, 's3');
        } else {
            $thumbnail = '';
        }


        if ($request->hasFile('cover')) {
            $extension = $request
                ->file('cover')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('cover')->getMimeType();
            $sizeFile = $request->file('cover')->getSize();
            $cover =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('cover')->storeAs('uploads/img-category/', $cover, 's3');
        } else {
            $cover = '';
        }
        $sql = new ImageCategory();
        $sql->name_en = trim($request->name_en);
        $sql->name_ar = trim($request->name_ar);
        $sql->slug = strtolower($request->slug);
        $sql->thumbnail = $thumbnail;
        $sql->cover = $cover;
        $sql->sort = $request->sort;
        $sql->mode = $request->mode;
        $sql->cities_and_landmarks = $request->cities_and_landmarks;
        $sql->in_random_home_image = $request->get('in_random_home_image');
        $sql->people = $request->get('people');
        $sql->created_at = date('Y-m-d H:i:s');
        $sql->updated_at = date('Y-m-d H:i:s');
        $sql->save();

        \Session::flash('success', trans('admin.success_add_category'));
        return redirect()->route('admin.categories.index');
    }

    public function edit($id)
    {
        $categories = ImageCategory::find($id);
        // dd($categories->toArray());
        $index_url = route('admin.categories.index');
        $update_url = route('admin.categories.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.Edit'),
        ];

        return view(
            'admin_v2.category.edit',
            compact('html_breadcrumbs', 'index_url', 'update_url', 'categories')
        );
    }

    public function update(Request $request, $id)
    {


        // dd($request->all());
        $categories = ImageCategory::find($id);

        if (!isset($categories)) {
            return redirect()->route('admin.categories.index');
        }

        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        $rules = [
            'name_en' => 'required',
            'name_ar' => 'required',
            'cities_and_landmarks' => 'required',
            'people' => 'required',
            'slug' => 'required|ascii_only|unique:image_categories,slug,' . $id,
            'in_home' => 'required',
            'show_in_trending_list' => 'required|boolean',
            'sort' => 'required|integer'
        ];

        $this->validate($request, $rules);

        if ($request->hasFile('thumbnail')) {
            $extension = $request
                ->file('thumbnail')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('thumbnail')->getMimeType();
            $sizeFile = $request->file('thumbnail')->getSize();
            $thumbnail =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('thumbnail')->storeAs('uploads/img-category/', $thumbnail, 's3');
            // delete old
            \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['thumbnail']);

            $categories->thumbnail = $thumbnail;
        }


        if ($request->hasFile('cover')) {
            $extension = $request
                ->file('cover')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('cover')->getMimeType();
            $sizeFile = $request->file('cover')->getSize();
            $cover =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('cover')->storeAs('uploads/img-category/', $cover, 's3');
            // delete old
            \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['cover']);

            $categories->cover = $cover;
        }

        if ($categories->slug !== strtolower($request->slug)) {
            $from_url = route('category.show', $categories->slug);
            $to_url = route('category.show', strtolower($request->slug));

            // in admin route generate url without language prefix
            $domain_name = url('/');
            $from_url = '/ar' . str_replace($domain_name, '', $from_url);
            $to_url = '/ar' . str_replace($domain_name, '', $to_url);

            // arabic_url
            add_to_redirect_url_list($from_url, $to_url);

            // english_url
            $from_url = str_replace('/ar/', '/en/', $from_url);
            $to_url = str_replace('/ar/', '/en/', $to_url);
            add_to_redirect_url_list($from_url, $to_url);

            dump_rewrite_rules_to_file();
        }

        $categories->name_en = $request->name_en;
        $categories->name_ar = $request->name_ar;
        $categories->slug = strtolower($request->slug);
        $categories->mode = $request->mode;
        $categories->in_home = $request->in_home;
        $categories->show_in_trending_list = $request->show_in_trending_list;
        $categories->cities_and_landmarks = $request->cities_and_landmarks;
        $categories->in_random_home_image = $request->in_random_home_image;
        $categories->people = $request->get('people');
        $categories->sort = $request->sort;
        $categories->save();

        \Session::flash('success', trans('misc.success_update'));

        return redirect()->route('admin.categories.index');
    }

    public function activate(Request $request, $id)
    {
        $id = explode(',', $id);
        ImageCategory::whereIn('id', $id)->update(['mode' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.categories.index');
    }


    public function destroy($id)
    {
        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $categories = ImageCategory::find($id);

        if (!isset($categories) || $categories->id == 1) {
            return redirect()->route('admin.categories.index');
        } else {
            $thumbnail = public_path('img-category/' . $categories->thumbnail);
            $cover = public_path('img-category/' . $categories->cover);
            $images_category = Image::where('categories_id', $id)->get();

            $categories->delete();

            if ($categories->getAttributes()['thumbnail']) {
                \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['thumbnail']);
            }

            if ($categories->getAttributes()['cover']) {
                \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['cover']);
            }

            if (isset($images_category)) {
                foreach ($images_category as $key) {
                    $key->categories_id = 1;
                    $key->save();
                }
            }
            \Session::flash('success', trans('misc.success_update'));
            return redirect()->route('admin.categories.index');
        }
    }

    public function index_video()
    {


        $index_url = route('admin.videos.categories.datatable');
        $edit_url = route('admin.videos.categories.edit', 0);
        $destroy_url = route('admin.videos.categories.destroy', 0);

        $object = new ImageCategory();

        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.videos.categories.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'on',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.videos.categories.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'off',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.videos.categories.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.videos.categories.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_videos_site = true;

        return view(
            'admin_v2.category.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'object'
            )
        );
    }

    public function datatable_video(Request $request)
    {


        $data = process_datatable_query(VideoCategory::query()->orderBy('sort'), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('name_ar', 'like', '%' . $search . '%')
                        ->orWhere('name_en', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create_video()
    {

        $index_url = route('admin.videos.categories.index');
        $store_url = route('admin.videos.categories.store');
        $is_videos_site = true;
        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.New'),
        ];

        $is_videos_site = true;

        return view('admin_v2.category.create', compact('html_breadcrumbs', 'index_url', 'store_url', 'is_videos_site'));
    }

    public function store_video(Request $request)
    {


        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        $rules = [
            'name_en' => 'required',
            'name_ar' => 'required',
            'cities_and_landmarks' => 'required',
            'people' => 'required',
            'slug' => 'required|ascii_only|unique:video_categories',
            'sort' => 'required|integer'
        ];

        $this->validate($request, $rules);

        if ($request->hasFile('thumbnail')) {
            $extension = $request
                ->file('thumbnail')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('thumbnail')->getMimeType();
            $sizeFile = $request->file('thumbnail')->getSize();
            $thumbnail =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('thumbnail')->storeAs('uploads/img-category/', $thumbnail, 's3');
        } else {
            $thumbnail = '';
        }


        if ($request->hasFile('cover')) {
            $extension = $request
                ->file('cover')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('cover')->getMimeType();
            $sizeFile = $request->file('cover')->getSize();
            $cover =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('cover')->storeAs('uploads/img-category/', $cover, 's3');
        } else {
            $cover = '';
        }


        $sql = new VideoCategory();
        $sql->name_en = trim($request->name_en);
        $sql->name_ar = trim($request->name_ar);
        $sql->slug = strtolower($request->slug);
        $sql->thumbnail = $thumbnail;
        $sql->cover = $cover;
        $sql->mode = $request->mode;
        $sql->sort = $request->sort;
        $sql->cities_and_landmarks = $request->cities_and_landmarks;
        $sql->in_random_home_video = $request->in_random_home_video;
        $sql->people = $request->get('people');
        $sql->save();

        \Session::flash('success', trans('admin.success_add_category'));

        return redirect()->route('admin.videos.categories.index');
    }

    public function edit_video($id)
    {


        $categories = VideoCategory::find($id);
        $is_vectors_site = true;
        $index_url = route('admin.videos.categories.index');
        $update_url = route('admin.videos.categories.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.Edit'),
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.category.edit',
            compact('is_vectors_site', 'html_breadcrumbs', 'index_url', 'update_url', 'categories', 'is_vectors_site')
        );
    }

    public function update_video(Request $request, $id)
    {


        $categories = VideoCategory::find($id);

        if (!isset($categories)) {
            return redirect()->route('admin.videos.categories.index');
        }

        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        $rules = [
            'name_en' => 'required',
            'name_ar' => 'required',
            'cities_and_landmarks' => 'required',
            'people' => 'required',
            'slug' =>
                'required|ascii_only|unique:video_categories,slug,' . $id,
            'in_home' => 'required',
            'sort' => 'required|integer'
        ];

        $this->validate($request, $rules);

        if ($request->hasFile('thumbnail')) {
            $extension = $request
                ->file('thumbnail')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('thumbnail')->getMimeType();
            $sizeFile = $request->file('thumbnail')->getSize();
            $thumbnail =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('thumbnail')->storeAs('uploads/img-category/', $thumbnail, 's3');
            // delete old
            \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['thumbnail']);

            $categories->thumbnail = $thumbnail;
        }


        if ($request->hasFile('cover')) {
            $extension = $request
                ->file('cover')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('cover')->getMimeType();
            $sizeFile = $request->file('cover')->getSize();
            $cover =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('cover')->storeAs('uploads/img-category/', $cover, 's3');
            // delete old
            \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['cover']);

            $categories->cover = $cover;
        }

        if ($categories->slug !== strtolower($request->slug)) {
            $from_url = route('video.category.show', $categories->slug);
            $to_url = route('video.category.show', strtolower($request->slug));

            // in admin route generate url without language prefix
            $domain_name = url('/');
            $from_url = '/ar' . str_replace($domain_name, '', $from_url);
            $to_url = '/ar' . str_replace($domain_name, '', $to_url);

            // arabic_url
            add_to_redirect_url_list($from_url, $to_url);

            // english_url
            $from_url = str_replace('/ar/', '/en/', $from_url);
            $to_url = str_replace('/ar/', '/en/', $to_url);
            add_to_redirect_url_list($from_url, $to_url);

            dump_rewrite_rules_to_file();
        }

        $categories->name_en = $request->name_en;
        $categories->name_ar = $request->name_ar;
        $categories->slug = strtolower($request->slug);
        $categories->mode = $request->mode;
        $categories->in_home = $request->in_home;
        $categories->cities_and_landmarks = $request->cities_and_landmarks;
        $categories->in_random_home_video = $request->in_random_home_video;
        $categories->people = $request->people;
        $categories->sort = $request->sort;
        $categories->show_in_trending_list = $request->show_in_trending_list;
        $categories->save();

        \Session::flash('success', trans('misc.success_update'));

        return redirect()->route('admin.videos.categories.index');
    }

    public function activate_video(Request $request, $id)
    {


        $id = explode(',', $id);
        VideoCategory::whereIn('id', $id)->update(['mode' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.videos.categories.index');
    }


    public function destroy_video($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $categories = VideoCategory::find($id);

        if (!isset($categories) || $categories->id == 1) {
            return redirect()->route('admin.videos.categories.index');
        } else {
            $thumbnail = public_path('img-category/' . $categories->thumbnail);
            $images_category = Image::where('categories_id', $id)->get();

            $categories->delete();

            if ($categories->getAttributes()['thumbnail']) {
                \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['thumbnail']);
            }

            if ($categories->getAttributes()['cover']) {
                \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['cover']);
            }

            if (isset($images_category)) {
                foreach ($images_category as $key) {
                    $key->categories_id = 1;
                    $key->save();
                }
            }
            \Session::flash('success', trans('misc.success_update'));
            return redirect()->route('admin.videos.categories.index');
        }
    }


    //here code vectors


    public function index_vector()
    {


        $index_url = route('admin.vector.categories.datatable');
        $edit_url = route('admin.vector.categories.edit', 0);
        $destroy_url = route('admin.vector.categories.destroy', 0);

        $object = new VectorCategory();

        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.vector.categories.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'on',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.videos.categories.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'off',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vector.categories.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.videos.categories.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.category.index',
            compact(
                'is_vectors_site',
                'html_breadcrumbs',
                'html_new_path',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'object'
            )
        );
    }

    public function datatable_vector(Request $request)
    {


        $data = process_datatable_query(VectorCategory::query()->orderBy('sort'), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('name_ar', 'like', '%' . $search . '%')
                        ->orWhere('name_en', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create_vector()
    {

        $index_url = route('admin.vector.categories.index');
        $store_url = route('admin.vector.categories.store');
        $is_vectors_site = true;
        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.New'),
        ];

        $is_vectors_site = true;

        return view('admin_v2.category.create', compact('html_breadcrumbs', 'index_url', 'store_url', 'is_vectors_site'));
    }

    public function store_vector(Request $request)
    {


        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        $rules = [
            'name_en' => 'required',
            'name_ar' => 'required',
            'cities_and_landmarks' => 'required',
            'people' => 'required',
            'slug' => 'required|ascii_only|unique:vector_categories',
            'sort' => 'required|integer'
        ];

        $this->validate($request, $rules);

        if ($request->hasFile('thumbnail')) {
            $extension = $request
                ->file('thumbnail')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('thumbnail')->getMimeType();
            $sizeFile = $request->file('thumbnail')->getSize();
            $thumbnail =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('thumbnail')->storeAs('uploads/img-category/', $thumbnail, 's3');
        } else {
            $thumbnail = '';
        }


        if ($request->hasFile('cover')) {
            $extension = $request
                ->file('cover')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('cover')->getMimeType();
            $sizeFile = $request->file('cover')->getSize();
            $cover =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('cover')->storeAs('uploads/img-category/', $cover, 's3');
        } else {
            $cover = '';
        }


        $sql = new VectorCategory();
        $sql->name_en = trim($request->name_en);
        $sql->name_ar = trim($request->name_ar);
        $sql->slug = strtolower($request->slug);
        $sql->thumbnail = $thumbnail;
        $sql->cover = $cover;
        $sql->mode = $request->mode;
        $sql->sort = $request->sort;
        $sql->cities_and_landmarks = $request->cities_and_landmarks;
        $sql->in_random_home_vector = $request->in_random_home_vector;
        $sql->people = $request->get('people');
        $sql->save();

        \Session::flash('success', trans('admin.success_add_category'));

        return redirect()->route('admin.vectors.categories.index');
    }

    public function edit_vector($id)
    {


        $categories = VectorCategory::find($id);
        $is_vectors_site = true;
        $index_url = route('admin.vector.categories.index');
        $update_url = route('admin.vector.categories.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.Categories'),
            'subtitle' => __('views.Edit'),
        ];

        $is_vectors_site = true;

        return view(
            'admin_v2.category.edit',
            compact('is_vectors_site', 'html_breadcrumbs', 'index_url', 'update_url', 'categories', 'is_vectors_site')
        );
    }

    public function update_vector(Request $request, $id)
    {


        $categories = VectorCategory::find($id);

        if (!isset($categories)) {
            return redirect()->route('admin.vector.categories.index');
        }

        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        $rules = [
            'name_en' => 'required',
            'name_ar' => 'required',
            'cities_and_landmarks' => 'required',
            'people' => 'required',
            'slug' =>
                'required|ascii_only|unique:vector_categories,slug,' . $id,
            'in_home' => 'required',
            'sort' => 'required|integer'
        ];

        $this->validate($request, $rules);

        if ($request->hasFile('thumbnail')) {
            $extension = $request
                ->file('thumbnail')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('thumbnail')->getMimeType();
            $sizeFile = $request->file('thumbnail')->getSize();
            $thumbnail =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('thumbnail')->storeAs('uploads/img-category/', $thumbnail, 's3');
            // delete old
            \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['thumbnail']);

            $categories->thumbnail = $thumbnail;
        }


        if ($request->hasFile('cover')) {
            $extension = $request
                ->file('cover')
                ->getClientOriginalExtension();
            $type_mime_shot = $request->file('cover')->getMimeType();
            $sizeFile = $request->file('cover')->getSize();
            $cover =
                $request->slug . '-' . str_random(32) . '.' . $extension;

            $request->file('cover')->storeAs('uploads/img-category/', $cover, 's3');
            // delete old
            \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['cover']);

            $categories->cover = $cover;
        }

        if ($categories->slug !== strtolower($request->slug)) {
            $from_url = route('video.category.show', $categories->slug);
            $to_url = route('video.category.show', strtolower($request->slug));

            // in admin route generate url without language prefix
            $domain_name = url('/');
            $from_url = '/ar' . str_replace($domain_name, '', $from_url);
            $to_url = '/ar' . str_replace($domain_name, '', $to_url);

            // arabic_url
            add_to_redirect_url_list($from_url, $to_url);

            // english_url
            $from_url = str_replace('/ar/', '/en/', $from_url);
            $to_url = str_replace('/ar/', '/en/', $to_url);
            add_to_redirect_url_list($from_url, $to_url);

            dump_rewrite_rules_to_file();
        }

        $categories->name_en = $request->name_en;
        $categories->name_ar = $request->name_ar;
        $categories->slug = strtolower($request->slug);
        $categories->mode = $request->mode;
        $categories->in_home = $request->in_home;
        $categories->cities_and_landmarks = $request->cities_and_landmarks;
        $categories->in_random_home_vector = $request->in_random_home_vector;
        $categories->people = $request->people;
        $categories->sort = $request->sort;
        $categories->show_in_trending_list = $request->show_in_trending_list;
        $categories->save();

        \Session::flash('success', trans('misc.success_update'));

        return redirect()->route('admin.vector.categories.index');
    }

    public function activate_vector(Request $request, $id)
    {


        $id = explode(',', $id);
        VectorCategory::whereIn('id', $id)->update(['mode' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.vector.categories.index');
    }


    public function destroy_vector($id)
    {


        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $categories = VectorCategory::find($id);

        if (!isset($categories) || $categories->id == 1) {
            return redirect()->route('admin.vector.categories.index');
        } else {
            $thumbnail = public_path('img-category/' . $categories->thumbnail);
            $images_category = Image::where('categories_id', $id)->get();

            $categories->delete();

            if ($categories->getAttributes()['thumbnail']) {
                \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['thumbnail']);
            }

            if ($categories->getAttributes()['cover']) {
                \Storage::disk('s3')->delete('uploads/img-category/' . $categories->getAttributes()['cover']);
            }

            if (isset($images_category)) {
                foreach ($images_category as $key) {
                    $key->categories_id = 1;
                    $key->save();
                }
            }
            \Session::flash('success', trans('misc.success_update'));
            return redirect()->route('admin.vector.categories.index');
        }
    }


}
