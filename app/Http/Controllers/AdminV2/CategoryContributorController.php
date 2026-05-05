<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Models\CategoryContributor;
use App\Http\Controllers\Controller;
use App\Models\CategoryAdminsVector;
use Illuminate\Support\Facades\Validator;

class CategoryContributorController extends Controller
{
    public function index()
    {
        $index_url = route('admin.categories_contributores.datatable');
        $edit_url = route('admin.categories_contributores.edit', 0);
        $destroy_url = route('admin.categories_contributores.destroy', 0);

        $object = new CategoryContributor();

        $html_breadcrumbs = [
            'title' => __('views.CategoriesContributer'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.categories_contributores.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.categories_contributores.destroy', 0),
                'method' => 'delete',
                'confirm' => __(
                    'views.Are you sure to delete :number selected records ?',
                    ['number' => 0]
                ),
            ],
        ];

        return view(
            'admin_v2.category_contributor.index',
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
        $data = process_datatable_query(CategoryContributor::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function create()
    {
        $index_url = route('admin.categories_contributores.index');
        $store_url = route('admin.categories_contributores.store');

        $html_breadcrumbs = [
            'title' => __('views.CategoriesContributer'),
            'subtitle' => __('views.New'),
        ];

        return view(
            'admin_v2.category_contributor.create',
            compact('html_breadcrumbs', 'index_url', 'store_url')
        );
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
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $sql = new CategoryContributor();
        $sql->name = trim($request->name);
        $sql->slug = preg_replace('/\s+/', '', $request->name);
        $sql->save();

        \Session::flash('success', trans('admin.success_add_category'));
        return redirect()->route('admin.categories_contributores.index');
    }

    public function edit($id)
    {
        $categories = CategoryContributor::find($id);

        $index_url = route('admin.categories_contributores.index');
        $update_url = route('admin.categories_contributores.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.CategoriesContributer'),
            'subtitle' => __('views.Edit'),
        ];

        return view(
            'admin_v2.category_contributor.edit',
            compact('html_breadcrumbs', 'categories', 'index_url', 'update_url')
        );
    }

    public function update(Request $request, $id)
    {
        $categories = CategoryContributor::find($id);

        if (!isset($categories)) {
            return redirect()->route('admin.categories_contributores.index');
        }

        Validator::extend('ascii_only', function (
            $attribute,
            $value,
            $parameters
        ) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        $rules = [
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $categories->name = $request->name;

        $categories->slug = preg_replace('/\s+/', '', $request->name);
        $categories->save();

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->route('admin.categories_contributores.index');
    }

    public function destroy($id)
    {
        $categories = CategoryContributor::find($id);
        $categories->delete();
        return redirect()->route('admin.categories_contributores.index');
    }

    // public function index_video()
    // {
    //

    //     $index_url = route('admin.videos.categories_contributor.datatable');
    //     $edit_url = route('admin.videos.categories_contributor.edit', 0);
    //     $destroy_url = route('admin.videos.categories_admin.destroy', 0);

    //     $object = new CategoryAdminsVector();

    //     $html_breadcrumbs = [
    //         'title' => __('views.CategoriesAdmin'),
    //         'subtitle' => __('views.Index'),
    //         'datatable' => true,
    //     ];
    //     $html_new_path = route('admin.videos.categories_admin.create');
    //     $subheader_actions = [
    //         'delete' => [
    //             'type' => 'button',
    //             'text' => __('views.Delete All'),
    //             'url' => route('admin.videos.categories_admin.destroy', 0),
    //             'method' => 'delete',
    //             'confirm' => __(
    //                 'views.Are you sure to delete :number selected records ?',
    //                 ['number' => 0]
    //             ),
    //         ],
    //     ];

    //     $is_videos_site = true;

    //     return view(
    //         'admin_v2.category_admin.index',
    //         compact(
    //             'is_videos_site',
    //             'html_breadcrumbs',
    //             'html_new_path',
    //             'subheader_actions',
    //             'index_url',
    //             'edit_url',
    //             'destroy_url',
    //             'object'
    //         )
    //     );
    // }

    // public function datatable_video(Request $request)
    // {
    //

    //     $data = process_datatable_query(CategoryAdminsVector::query(), function (
    //         $query,
    //         $search
    //     ) {
    //         return $query
    //             ->where(function($query) use ($search) {
    //                 $query->where('name', 'like', '%' . $search . '%');
    //             });
    //     });

    //     return $data;
    // }

    // public function create_video()
    // {
    //

    //     $index_url = route('admin.videos.categories_admin.index');
    //     $store_url = route('admin.videos.categories_admin.store');

    //     $html_breadcrumbs = [
    //         'title' => __('views.CategoriesAdmin'),
    //         'subtitle' => __('views.New'),
    //     ];

    //     $is_videos_site = true;

    //     return view(
    //         'admin_v2.category_admin.create',
    //         compact('is_videos_site', 'html_breadcrumbs', 'index_url', 'store_url')
    //     );
    // }

    // public function store_video(Request $request)
    // {
    //

    //     Validator::extend('ascii_only', function (
    //         $attribute,
    //         $value,
    //         $parameters
    //     ) {
    //         return !preg_match('/[^x00-x7F\-]/i', $value);
    //     });

    //     $rules = [
    //         'name' => 'required',
    //     ];

    //     $this->validate($request, $rules);

    //     $sql = new CategoryAdminsVector();
    //     $sql->name = trim($request->name);
    //     $sql->slug = preg_replace('/\s+/', '', $request->name);
    //     $sql->save();

    //     \Session::flash('success', trans('admin.success_add_category'));
    //     return redirect()->route('admin.videos.categories_admin.index');
    // }

    // public function edit_video($id)
    // {
    //

    //     $categories = CategoryAdminsVector::find($id);

    //     $index_url = route('admin.videos.categories_admin.index');
    //     $update_url = route('admin.videos.categories_admin.update', $id);

    //     $html_breadcrumbs = [
    //         'title' => __('views.CategoriesAdmin'),
    //         'subtitle' => __('views.Edit'),
    //     ];

    //     $is_videos_site = true;

    //     return view(
    //         'admin_v2.category_admin.edit',
    //         compact('is_videos_site', 'html_breadcrumbs', 'categories', 'index_url', 'update_url')
    //     );
    // }

    // public function update_video(Request $request, $id)
    // {
    //

    //     $categories = CategoryAdminsVector::find($id);

    //     if (!isset($categories)) {
    //         return redirect()->route('admin.videos.categories_admin.index');
    //     }

    //     Validator::extend('ascii_only', function (
    //         $attribute,
    //         $value,
    //         $parameters
    //     ) {
    //         return !preg_match('/[^x00-x7F\-]/i', $value);
    //     });

    //     $rules = [
    //         'name' => 'required',
    //     ];

    //     $this->validate($request, $rules);

    //     $categories->name = $request->name;

    //     $categories->slug = preg_replace('/\s+/', '', $request->name);
    //     $categories->save();

    //     \Session::flash('success', trans('misc.success_update'));
    //     return redirect()->route('admin.videos.categories_admin.index');
    // }

    // public function destroy_video($id)
    // {
    //

    //     $categories = CategoryAdminsVector::find($id);
    //     $categories->delete();
    //     return redirect()->route('admin.videos.categories_admin.index');
    // }


    // //here code vector


    //  public function index_vector()
    // {
    //

    //     $index_url = route('admin.vector.categories_admin.datatable');
    //     $edit_url = route('admin.vector.categories_admin.edit', 0);
    //     $destroy_url = route('admin.vector.categories_admin.destroy', 0);

    //     $object = new CategoryAdminsVector();

    //     $html_breadcrumbs = [
    //         'title' => __('views.CategoriesAdmin'),
    //         'subtitle' => __('views.Index'),
    //         'datatable' => true,
    //     ];
    //     $html_new_path = route('admin.vector.categories_admin.create');
    //     $subheader_actions = [
    //         'delete' => [
    //             'type' => 'button',
    //             'text' => __('views.Delete All'),
    //             'url' => route('admin.vector.categories_admin.destroy', 0),
    //             'method' => 'delete',
    //             'confirm' => __(
    //                 'views.Are you sure to delete :number selected records ?',
    //                 ['number' => 0]
    //             ),
    //         ],
    //     ];

    //     $is_vectors_site = true;

    //     return view(
    //         'admin_v2.category_admin.index',
    //         compact(
    //             'is_vectors_site',
    //             'html_breadcrumbs',
    //             'html_new_path',
    //             'subheader_actions',
    //             'index_url',
    //             'edit_url',
    //             'destroy_url',
    //             'object'
    //         )
    //     );
    // }

    // public function datatable_vector(Request $request)
    // {
    //

    //     $data = process_datatable_query(CategoryAdminsVector::query(), function (
    //         $query,
    //         $search
    //     ) {
    //         return $query
    //             ->where(function($query) use ($search) {
    //                 $query->where('name', 'like', '%' . $search . '%');
    //             });
    //     });

    //     return $data;
    // }

    // public function create_vector()
    // {
    //

    //     $index_url = route('admin.vector.categories_admin.index');
    //     $store_url = route('admin.vector.categories_admin.store');

    //     $html_breadcrumbs = [
    //         'title' => __('views.CategoriesAdmin'),
    //         'subtitle' => __('views.New'),
    //     ];

    //     $is_vectors_site = true;

    //     return view(
    //         'admin_v2.category_admin.create',
    //         compact('is_vectors_site', 'html_breadcrumbs', 'index_url', 'store_url')
    //     );
    // }

    // public function store_vector(Request $request)
    // {
    //

    //     Validator::extend('ascii_only', function (
    //         $attribute,
    //         $value,
    //         $parameters
    //     ) {
    //         return !preg_match('/[^x00-x7F\-]/i', $value);
    //     });

    //     $rules = [
    //         'name' => 'required',
    //     ];

    //     $this->validate($request, $rules);

    //     $sql = new CategoryAdminsVector();
    //     $sql->name = trim($request->name);
    //     $sql->slug = preg_replace('/\s+/', '', $request->name);
    //     $sql->save();

    //     \Session::flash('success', trans('admin.success_add_category'));
    //     return redirect()->route('admin.vector.categories_admin.index');
    // }

    // public function edit_vector($id)
    // {
    //

    //     $categories = CategoryAdminsVector::find($id);

    //     $index_url = route('admin.vector.categories_admin.index');
    //     $update_url = route('admin.vector.categories_admin.update', $id);

    //     $html_breadcrumbs = [
    //         'title' => __('views.CategoriesAdmin'),
    //         'subtitle' => __('views.Edit'),
    //     ];

    //     $is_vectors_site = true;

    //     return view(
    //         'admin_v2.category_admin.edit',
    //         compact('is_vectors_site', 'html_breadcrumbs', 'categories', 'index_url', 'update_url')
    //     );
    // }

    // public function update_vector(Request $request, $id)
    // {
    //

    //     $categories = CategoryAdminsVector::find($id);

    //     if (!isset($categories)) {
    //         return redirect()->route('admin.vector.categories_admin.index');
    //     }

    //     Validator::extend('ascii_only', function (
    //         $attribute,
    //         $value,
    //         $parameters
    //     ) {
    //         return !preg_match('/[^x00-x7F\-]/i', $value);
    //     });

    //     $rules = [
    //         'name' => 'required',
    //     ];

    //     $this->validate($request, $rules);

    //     $categories->name = $request->name;

    //     $categories->slug = preg_replace('/\s+/', '', $request->name);
    //     $categories->save();

    //     \Session::flash('success', trans('misc.success_update'));
    //     return redirect()->route('admin.vector.categories_admin.index');
    // }

    // public function destroy_vector($id)
    // {
    //

    //     $categories = CategoryAdminsVector::find($id);
    //     $categories->delete();
    //     return redirect()->route('admin.vector.categories_admin.index');
    // }

        /* CategoryContributerVideos */
        public function indexCategoryContributerVideos()
        {
            $index_url = route('admin.video.categories_contributores.datatable');
            $edit_url = route('admin.video.categories_contributores.edit', 0);
            $destroy_url = null;//route('admin.video.categories_contributores.destroy', 0);
            $is_videos_site = true;
            $object = new CategoryContributor();

            $html_breadcrumbs = [
                'title' => __('views.CategoriesContributer'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];
            $html_new_path = route('admin.categories_contributores.create');
            $subheader_actions = [
                'delete' => [
                    'type' => 'button',
                    'text' => __('views.Delete All'),
                    'url' => route('admin.categories_contributores.destroy', 0),
                    'method' => 'delete',
                    'confirm' => __(
                        'views.Are you sure to delete :number selected records ?',
                        ['number' => 0]
                    ),
                ],
            ];

            return view(
                'admin_v2.category_contributor.index',
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

        public function datatableCategoryContributerVideos(Request $request)
        {
            // CategoryContributorVideo extends Model

            $data = process_datatable_query(CategoryContributor::query(), function (
                $query,
                $search
            ) {
                return $query
                    ->where(function($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });

            return $data;
        }

        public function destroyCategoryContributerVideos($id)
        {
            $categories = CategoryContributor::find($id);
            $categories->delete();
            return redirect()->route('admin.categories_contributores.index');
        }
       /* CategoryContributerVideos  */

}
