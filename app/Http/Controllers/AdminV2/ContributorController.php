<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\ContributorExport;
use App\Jobs\UpdateContributorImageSubmission;
use App\Models\Contributor;
use App\Models\ContributorImageSubmissionUpdate;
use App\Rules\Mobile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\ContributorImageSubmission;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ContributorController extends Controller
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
            'email' => 'required|email|max:255|unique:contributors,id,' . $id,
            'mobile' => ['nullable', new Mobile(), Rule::unique('contributors', 'mobile')->ignore($id)],
            'profit_ratio' => 'required|regex:/^\d+(\.\d{1,2})?$/',
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
        $index_url = route('admin.contributor.datatable');
        $edit_url = route('admin.contributor.edit', 0);
        $destroy_url = route('admin.contributor.destroy', 0);
        $export_url = route('admin.contributor.export');

        $object = new Contributor();

        $html_breadcrumbs = [
            'title' => __('views.contributor'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.contributor.create');
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
            'admin_v2.contributor.index',
            compact('html_breadcrumbs', 'html_new_path', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url', 'export_url')
        );
    }

    public function datatable(Request $request)
    {
        $data = process_datatable_query(Contributor::query(), function (
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

    public function export(Request $request)
    {
        return Excel::download(new ContributorExport(Contributor::all(), $request), now() . '.xlsx');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Contributor::findOrFail($id);

        if ($data->id == 1 || $data->id == Auth::user()->id) {
            \Session::flash('success', trans('admin.user_no_edit'));
            return redirect()->route('admin.contributor.index');
        }

        $index_url = route('admin.contributor.index');
        $update_url = route('admin.contributor.update', $id);

        $html_breadcrumbs = [
            'title' => __('views.contributor'),
            'subtitle' => __('views.Edit'),
        ];

        return view('admin_v2.contributor.edit', compact('html_breadcrumbs', 'data', 'index_url', 'update_url'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update($id, Request $request)
    {

        $user = Contributor::findOrFail($id);

        $input = $request->except(['show_land_images', 'show_land_vectors', 'show_land_videos']);

        $validator = $this->validator($input, $id);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        if ($request->filled('show_land_images')) {
            $user->show_land_images = true;
        } else {
            $user->show_land_images = false;
        }
        if ($request->filled('show_land_vectors')) {
            $user->show_land_vectors = true;
        } else {
            $user->show_land_vectors = false;
        }
        if ($request->filled('show_land_videos')) {
            $user->show_land_videos = true;
        } else {
            $user->show_land_videos = false;
        }

        if ($request->filled('is_active_upload_raw')) {
            $user->is_active_upload_raw = true;
        } else {
            $user->is_active_upload_raw = false;
        }
        
        if (@$input['password'])
            $input['password'] = bcrypt($input['password']);

        $user->fill($input)->save();

        \Session::flash('success', trans('admin.success_update'));

        return redirect()->route('admin.contributor.index');
    }


    public function index_submissions(Request $request)
    {
        $status = $request->get('status');
        if ($request->has('status') and $status === "update") {
            $index_url = route('admin.contributors.datatable_submissions_update_after_publish.datatable');
            $review_url = route('admin.contributors.submissions.review', [0, 'status' => "update"]);
        } else {
            $index_url = route('admin.contributors.submissions.datatable');
            $review_url = route('admin.contributors.submissions.review', 0);
        }


        $html_breadcrumbs = [
            'title' => ($request->has('status') and $status === "update") ? __('views.update_from_contributor_after_publish') : __('views.Submissions'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [];

        return view(
            'admin_v2.submission.index',
            compact(
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'review_url'
            )
        );
    }

    public function datatable_submissions(Request $request)
    {
        $query = ContributorImageSubmission::whereHas('items.file', function ($q) {
            $q->whereIn('contributor_stage', [1, 2])
                ->doesntHave('file')
                ->orWhereHas('file', function ($q) {
                    $q->whereHas('contributor_file', function ($q) {
                        $q->where('contributor_stage', 6);
                    });
                });
        })->with('contributor')->withCount(['items' => function ($q) {
            $q->whereHas('file', function ($q) {
                $q->whereIn('contributor_stage', [1, 2])
                    ->doesntHave('file')
                    ->orWhereHas('file', function ($q) {
                        $q->whereHas('contributor_file', function ($q) {
                            $q->where('contributor_stage', 6);
                        });
                    });
            });
        }]);
        if ($request->input('query.generalSearch'))
            $query->where(function ($q) {
                $q->whereHas('contributor', function ($q) {
                    $q->where('name', 'like', \request('query.generalSearch'))
                        ->orWhere('email', 'like', \request('query.generalSearch'));
                });
            });
        if ($request->contributor)
            $query->where('contributor_id', $request->contributor);
        if ($request->date)
            $query->whereDate('created_at', $request->date);
        $query = $query->whereHas('items.image', function ($q) {
            $q->whereIn('contributor_stage', [1, 2])
                ->doesntHave('file')
                ->orWhereHas('file', function ($q) {
                    $q->whereHas('contributor_file', function ($q) {
                        $q->where('contributor_stage', 6);
                    });
                });
        });
        $result_set = $query;
        if ($request->sort)
            $result_set->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $result_set->orderBy('id', 'desc');
        $result_set = $result_set->paginate($request->input('pagination.perpage', 20), ['*'], 'pagination.page');
        return [
            'meta' => [
                "page" => $result_set->currentPage(),
                "pages" => $result_set->lastPage(),
                "perpage" => $result_set->perPage(),
                "total" => $result_set->total(),
                "sort" => "desc",
                "field" => "id"
            ],
            'data' => $result_set->items()
        ];

    }

    public function index_review(Request $request, $id)
    {
        $routes = get_vue_image_store_routes($id);

        $html_breadcrumbs = [
            'title' => __('views.review'),
            'subtitle' => __('views.Index'),
        ];

        $user = Auth::user()->only(
            'id',
            'email',
            'api_token'
        );

        return view(
            'admin_v2.submission.review.index',
            compact(
                'html_breadcrumbs',
                'routes',
                'user'
            )
        );
    }

    public function ajax(Request $request)
    {
        $result_set = Contributor::select('id', 'name', 'email');
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

    public function datatable_submissions_update_after_publish(Request $request)
    {
        $query = ContributorImageSubmission::with(['contributor', 'items.image']);
        if ($request->contributor)
            $query->where('contributor_id', $request->contributor);
        if ($request->date)
            $query->whereDate('created_at', $request->date);
        $query = $query->whereHas('items.image', function ($q) {
            $q->whereIn('contributor_stage', [8])
                ->whereHas('file', function ($q) {
                    $q->whereHas('contributor_file', function ($q) {
                        $q->where('stage_edit', 3);
                    });
                });
        });
        $result_set = $query->withCount(['items as items_count' => function ($q) {
            $q->whereHas('image', function ($q) {
                $q->whereIn('contributor_stage', [8])
                    ->whereHas('file', function ($q) {
                        $q->whereHas('contributor_file', function ($q) {
                            $q->where('stage_edit', 3);
                        });
                    });
            });
        }]);
        if ($request->sort)
            $result_set->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $result_set->orderBy('updated_at', 'desc');
        $result_set = $result_set->paginate($request->input('pagination.perpage', 20), ['*'], 'pagination.page');
        return [
            'meta' => [
                "page" => $result_set->currentPage(),
                "pages" => $result_set->lastPage(),
                "perpage" => $result_set->perPage(),
                "total" => $result_set->total(),
                "sort" => "desc",
                "field" => "id"
            ],
            'data' => $result_set->items()
        ];

    }

    public function update_images_data(Request $request)
    {
        $html_breadcrumbs = [
            'title' => __('views.update_after_publish'),
            'subtitle' => __('global.upload'),
        ];
        if ($request->method() == 'GET')
            return view('admin_v2/submission/upload_new_data', compact('html_breadcrumbs'));
        $this->validate($request, [
            'file' => ['required', 'file', 'mimes:zip'],
            'contributor_id' => ['required', Rule::exists('contributors', 'id')],
        ]);

        $contributor = Contributor::findOrFail($request->contributor_id);
        $update = ContributorImageSubmissionUpdate::create([
            'contributor_id' => $contributor->id,
        ]);
        $path = $request->file('file')->store("uploads/contributor-image-submission-updates/{$update->id}", ['disk' => 'public']);
        $update->update(['file' => $path]);
        dispatch(new UpdateContributorImageSubmission($update));
        return ['status' => 1];
    }

}
