<?php

namespace App\Http\Controllers\AdminV2;

use Illuminate\Http\Request;
use App\Models\CategoryContributor;
use App\Http\Controllers\Controller;
use App\Models\CategoryAdminsVector;
use Illuminate\Support\Facades\Validator;

class CategoryContributorVectorController extends Controller
{
    public function __construct()
    {


    }
    public function index()
    {
        $index_url = route('admin.vector.categories_contributores.datatable');
        $edit_url = route('admin.vector.categories_contributores.edit', 0);
        $destroy_url = route('admin.vector.categories_contributores.destroy', 0);
        $is_vectors_site = true;

        $object = new CategoryContributor();

        $html_breadcrumbs = [
            'title' => __('views.CategoriesContributer'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.vector.categories_contributores.create');
        $subheader_actions = [
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete All'),
                'url' => route('admin.vector.categories_contributores.destroy', 0),
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
                'object',
                'is_vectors_site'
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
        $index_url = route('admin.vector.categories_contributores.index');
        $store_url = route('admin.vector.categories_contributores.store');

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
        return redirect()->route('admin.vector.categories_contributores.index');
    }

    public function edit($id)
    {
        $categories = CategoryContributor::find($id);

        $index_url = route('admin.vector.categories_contributores.index');
        $update_url = route('admin.vector.categories_contributores.update', $id);

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
            return redirect()->route('admin.vector.categories_contributores.index');
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
        return redirect()->route('admin.vector.categories_contributores.index');
    }

    public function destroy($id)
    {
        $categories = CategoryContributor::find($id);
        $categories->delete();
        return redirect()->route('admin.vector.categories_contributores.index');
    }



}
