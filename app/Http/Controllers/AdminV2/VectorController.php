<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\VectorExport;
use App\Export\VideoExport;
use App\Jobs\PublishAdminVector;
use App\Models\User;
use App\Models\VectorCategory;
use App\Models\VectorFolder;
use App\Models\VectorSearchKey;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Log;
use Image;
use App\Models\ContributorVector;
use App\Models\Vector;
use App\Models\Contributor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoryVector;
use App\Models\AdminCollection;
use App\Models\CollectionVector;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadVectors;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminCollectionVector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Maatwebsite\Excel\Facades\Excel;


class VectorController extends Controller
{
    public function getDimension($path, $type)
    {
        if ($type == 'width') {
            $width = getWidth($path);
            return $width;
        } else {
            $height = getHeight($path);
            return $height;
        }
    }

    public function index()
    {


        // return  Vector::where('status', 'active')->with(
        //     'adminCollection',
        //     'likes',
        //     'downloads',
        //     'category'
        // )->get();

        $index_url = route('admin.vectors.datatable');
        $edit_url = route('admin.vectors.edit', 0);
        $destroy_url = route('admin.vectors.destroy', 0);
        $export_url = route('admin.vectors.export');

        $html_breadcrumbs = [
            'title' => __('views.vectors'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.admin_collections.update', 0),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ];
                }),
            ],
        ];
        $is_vectors_site = true;
        $folders = VectorFolder::select('folder', 'id')->get();
        return view(
            'admin_v2_vectors.vectors.index',
            compact(
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'edit_url',
                'is_vectors_site',
                'destroy_url',
                'export_url',
                'folders'
            )
        );
    }

    public function datatable(Request $request)
    {
        $data_request_query = $request->get('query');
        $query = \App\Models\Vector::with(
            'adminCollection',
            'category'
        )->withCount('downloads', 'old_downloads', 'likes');
        if ($request->input('query.status'))
            $query->where('status', $request->input('query.status'));

        if (isset($data_request_query['folder_id']) && $data_request_query['folder_id'] !== "All") {
            $folder_id = $data_request_query['folder_id'];
            $query = $query->where('folder_id', $folder_id);
        }
        if ($request->input('query.user_id'))
            $query->where('user_id', $request->input('query.user_id'))->where('user_type', User::class);
        if ($request->input('query.contributor_id'))
            $query->where('user_id', $request->input('query.contributor_id'))->where('user_type', Contributor::class);
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                ->orderBy('date', 'DESC')
                ->where(function ($query) use ($search) {
                    if (is_numeric($search) && $search != 0) {
                        $query->where('id', $search);
                    } else {
                        $query->where('title_en', 'like', '%' . $search . '%')
                            ->orWhere('title_ar', 'like', '%' . $search . '%');
                    }
                });
        });

        return $data;
    }

    public function export(Request $request)
    {
        $query = Vector::with('user');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query = $query->whereBetween('date', [Carbon::parse($request->date_from), Carbon::parse($request->date_to)]);
        }
        if ($request->filled('date_from') && !$request->filled('date_to')) {
            $query = $query->where('date', '>', Carbon::parse($request->date_from));
        }
        if (!$request->filled('date_from') && $request->filled('date_to')) {
            $query = $query->where('date', '<', Carbon::parse($request->date_to));
        }
        if ($request->filled('status') && $request->status != -1) {
            $query = $query->where('status', $request->status);
        }
        return Excel::download(new  VectorExport($query->get()), now() . '.xlsx');
    }

    public function destroy(Request $request, $id)
    {
        // TODO all multiple delete


        $id = explode(',', $id)[0];
        $file = Vector::findOrFail($id);
        Log::channel('info')->error("Delete Vector", [
            'VectorID' => $id,
            'User' => \auth()->user()->email,
            'Ip' => $request->ip(),
        ]);

//        // Collection Vector
//        $collectionsImages = CollectionVector::where('vector_id', '=', $id)->delete();
//        $file->tags()->detach();
//        if (isset($file->preview)) {
//            $preview_image = $file->preview;
//            // Delete preview
//            if (\Storage::disk('s3')->exists($preview_image)) {
//                \Storage::disk('s3')->delete($preview_image);
//            }
//        }
//
//        // Delete thumbnail
//        if (isset($file->thumbnail)) {
//            $thumbnail = $file->thumbnail;
//            if (\Storage::disk('s3')->exists($thumbnail)) {
//                \Storage::disk('s3')->delete($thumbnail);
//            }
//        }

//        if (isset($file->search)) {
//            $search = $file->search;
//            // Delete search
//            if (\Storage::disk('s3')->exists($search)) {
//                \Storage::disk('s3')->delete($search);
//            }
//        }
//        $contributor_vector_id = $file->contributor_vector_id;
//        if ($contributor_vector_id) {
//            $contributor_file = $file->contributor_file;
//            if (isset($contributor_file->thumbnail)) {
//                $thumbnail = $contributor_file->thumbnail;
//                if (\Storage::disk('s3')->exists($thumbnail)) {
//                    \Storage::disk('s3')->delete($thumbnail);
//                }
//            }
//            if (isset($contributor_file->preview)) {
//                $preview = $contributor_file->preview;
//                if (\Storage::disk('s3')->exists($preview)) {
//                    \Storage::disk('s3')->delete($preview);
//                }
//            }
//            $contributor_file->tags()->delete();
//            $contributor_file->category()->detach();
//            $contributor_file->submmission_item()->delete();
//            $contributor_file->delete();
//
//        }
        $file->delete();

        \Artisan::call('stock:resort');

        return redirect()->route('admin.vectors.index');
    }

    public function edit($id)
    {

        $data = Vector::with('tags')->findOrFail($id);
        //  return $data;

        $title = __('تعديل صورة');
        $categoris = CategoryVector::where('vector_id', $id)->pluck(
            'category_id'
        );
        $tags_ar = $data->tags()->where('local', 'ar')->pluck('title');
        $tags_en = $data->tags()->where('local', 'en')->pluck('title');

        $html_breadcrumbs = [
            'title' => __('Vectors'),
            'subtitle' => __('views.Edit'),
        ];
        $is_vectors_site = true;
        return view('admin_v2_vectors.vectors.edit', compact(
            'html_breadcrumbs',
            'data',
            'is_vectors_site',
            'categoris',
            'tags_ar',
            'tags_en',
            'title'
        ));
    }

    public function update(Request $request)
    {


        //here storage image


        $sql = Vector::find($request->id);

        $rules = [
            'title_ar' => 'required|min:3',
            'title_en' => 'required|min:3',
            // 'slug' => 'required|min:3|regex:/^[A-Za-z0-9\_-]+$/i',
            'description_ar' => 'sometimes|min:2',
            'description_en' => 'sometimes|min:2',
        ];


        if ($request->featured == 'yes' && $sql->featured == 'no') {
            $featuredDate = \Carbon\Carbon::now();
        } elseif ($request->featured == 'yes' && $sql->featured == 'yes') {
            $featuredDate = $sql->featured_date;
        } else {
            $featuredDate = '';
        }

        $this->validate($request, $rules);
        $keywords = array('vectors', 'victors', 'vector', 'victor', 'illustration', 'illustrator');
        $slug = updateSlug($keywords, $request->get('slug'));
        $slug = "illustration-{$request->id}-" . $slug;
        if ($sql->slug !== strtolower($slug)) {
            $from_url = route('vector.show', $sql->slug);
            $to_url = route('vector.show', strtolower($slug));

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

        $sql->title_ar = $request->title_ar;
        $sql->title_en = $request->title_en;
        $sql->slug = $slug;
        $sql->description_ar = $request->description_ar;
        $sql->description_en = $request->description_en;
        $sql->in_home = $request->in_home;
        $sql->status = $request->status;
        $sql->featured = $request->featured;
        $sql->featured_date = $featuredDate;
        $sql->how_use_vector = $request->how_use_vector;
        $sql->attribution_required = $request->attribution_required;


        $sql->save();

        dispatch(new \App\Jobs\SeoVectors($sql->id));

        if ($request->categories_id) {
            $check = CategoryVector::where('vector_id', $request->id)->delete();
            for ($i = 0; $i < count($request->categories_id); $i++) {
                $categoryImages = CategoryVector::create([
                    'vector_id' => $request->id,
                    'category_id' => $request->categories_id[$i],
                ]);
            }
        }
        if ($request->get('tag_ar')) {
            sync_tags($sql, $request->get('tag_ar', []), 'ar');
        }
        if ($request->get('tag_en')) {
            sync_tags($sql, $request->get('tag_en', []), 'en');
        }

        dispatch(new \App\Jobs\UpdateStageEditVector([$sql->id]));


        \Session::flash('success_message', trans('admin.success_update'));

        return redirect()->route('admin.vectors.index');
    }

    public function activate(Request $request, $id)
    {

        $id = explode(',', $id);
        Vector::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->back();
    }

    public function add_to_admin_collection(Request $request, $id)
    {
        $id = explode(',', $id);
        $updates = collect($id)->map(function ($item) use ($request) {
            return [
                'image_id' => $item,
                'admin_collection_id' => $request->get('status'),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ];
        })->toArray();
        // TODO remove duplicated
        AdminCollectionImage::insert($updates);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->back();
    }

    private function setTags(int $image_id, array $tag_list, string $lang)
    {
        VectorTag::where('vector_id', $image_id)
            ->where('local', $lang)
            ->delete();

        collect($tag_list)
            ->map(function ($item) use ($image_id, $lang) {
                return [
                    'vector_id' => $image_id,
                    'tag' => $item,
                    'confidence' => 0,
                    'local' => $lang,
                    'slug' => get_soundex($item),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            })
            ->when(count($tag_list) > 0, function ($data) {
                VectorTag::insert($data->toArray());
            });
    }

    public function index_pending()
    {


        // return $query = Vector::where('status', 'pending')->with(
        //     'likes',
        //     'downloads',
        //     'category'
        // )->get();

        $index_url = route('admin.vectors.pending.datatable');
        $edit_url = route('admin.vectors.edit', 0);
        $destroy_url = route('admin.vectors.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.VectorsPending'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.admin_collections.update', 0),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ];
                }),
            ],
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2_vectors.vectors.pending.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url', 'is_vectors_site')
        );
    }

    public function datatable_pending(Request $request)
    {


        $query = Vector::where('status', 'pending')->with(
        // 'adminCollection',
            'likes',
            'downloads',
            'category'
        );

        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                ->orderBy('date', 'DESC')
                ->where(function ($query) use ($search) {
                    if(is_numeric($search) && $search != 0){
                        $query->where('id', $search);
                    }else{
                        $query->where('title_en', 'like', '%' . $search . '%')
                            ->orWhere('title_ar', 'like', '%' . $search . '%');
                    }

                });
        });

        return $data;
    }

    /* s:route-deleted-videos */
    public function index_deleted()
    {
        $html_breadcrumbs = [
            'title' => __('views.filesDeleted'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $index_url = route('admin.vectors.deleted.datatable');

        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.videos.videos.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.videos.videos.activate', 0),
                        'method' => 'post',
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ],
                ],
            ],

        ];

        $is_vectors_site = true;
        return view(
            'admin_v2_vectors.vectors.deleted.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'is_vectors_site')
        );

    }

    public function datatable_deleted(Request $request)
    {

        $query = Vector::onlyTrashed()->with(
            'adminCollection',
            'likes',
            'downloads',
            'category'
        );

        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('deleted_at', '>=', $from);
            $query = $query->whereDate('deleted_at', '<=', $to);
        }

        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                ->orderBy('date', 'DESC')
                ->where(function ($query) use ($search) {
                    $query->where('title_en', 'like', '%' . $search . '%')
                        ->orWhere('title_ar', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }
    /* e:route-deleted-vectors */
    /* s:route-deleted-contributor_vectors */
    public function index_deleted_contributor_vectors()
    {
        $index_url = route('admin.vectors.contributor_vectors.deleted.datatable');

        $html_breadcrumbs = [
            'title' => __('views.filesDeletedContributor'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.Activate'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.images.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.images.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],

        ];

        return view(
            'admin_v2_vectors.vectors.deleted.contributor_vectors',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url')
        );
    }

    public function datatable_deleted_contributor_vectors(Request $request)
    {
        $query = ContributorVector::onlyTrashed()->with(
            ['file' => function ($query) {
                $query->with('downloads')->withTrashed();
            },
                'user']
        );
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('deleted_at', '>=', $from);
            $query = $query->whereDate('deleted_at', '<=', $to);
        }

        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                // ->orderBy('created', 'DESC')
                ->where(function ($query) use ($search) {
                    $query->where('title_en', 'like', '%' . $search . '%')
                        ->orWhere('title_ar', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    /* e:route-deleted-contributor_vectors */

    public function create_filemanger()
    {
        $category = VectorCategory::get();
        $contributors = Contributor::select(['id', 'name'])->get();
        $folders = VectorFolder::withCount('vectors')->orderBy('vectors_count', 'desc')->pluck('folder');

        $html_breadcrumbs = [
            'title' => __('views.VectorFilemanger'),
            'subtitle' => __('views.New'),
        ];
        $is_vectors_site = true;
        $accessToken = \auth()->user()->createToken('userToken')->accessToken;
        return view(
            'admin_v2_vectors.vectors.filemaneger.create',
            compact('html_breadcrumbs', 'category', 'contributors', 'folders', 'is_vectors_site', 'accessToken')
        );
    }


    public function store_filemanger(UploadVectors $request)
    {
        \Log::channel('info')->info('Vector Admin Start-V1');
        $photos = [];
        if (Auth::guest()) {
            return response()->json([
                'session_null' => true,
                'success' => false,
            ]);
        }
        $input = $request->all();

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $status = "pending";
            $user_id = Auth::id();
            DB::beginTransaction();
            try {
                foreach ($files as $i => $file) {
                    $originalName = $file->getClientOriginalName();
                    $file_hash = hash_file('sha256', $file->path());
                    $extension = strtolower($file->getClientOriginalExtension());
                    $title = time() . Str::random(10);
                    $token_id = str_random(200);
                    $vector = new Vector();
                    $vector->title_ar = $title;
                    $vector->title_en = $title;
                    $vector->user_id = $user_id;
                    $vector->status = $status;
                    $vector->token_id = $token_id;
                    $vector->extension = strtolower($extension);
                    $vector->how_use_vector = 'free';
                    $vector->attribution_required = 'no';
                    $vector->original_name = $originalName;
                    $vector->hash = $file_hash;
                    $vector->save();
                    $original = "uploads/vectors/{$vector->id}/" . Str::random(20) . '.' . $extension;
                    $vector->vector = $original;

                    if ($request->has('folder')) {
                        \Log::channel('info')->info('Vector Start create foleder ');
                        $folder = $request->get('folder');
                        $folder_id = VectorFolder::firstOrCreate(['folder' => $folder])->id;
                        $vector->folder_id = $folder_id;
                    }
                    $vector->save();
                    if ($request->has('categories_id') && count($input['categories_id']) > 0) {
                        $vector->category()->attach($input['categories_id']);
                    }
                    if (!$file->move(dirname($original), pathinfo($original, PATHINFO_BASENAME)))
                        Log::error("ImageController cant move file. ($vector->id)");
                    chmod($original, 0777);
                    Storage::disk('s3')->put($original, file_get_contents($original));
                    Storage::disk('public')->deleteDirectory("uploads/vectors/{$vector->id}");
                    dispatch(new PublishAdminVector($vector->id))->onQueue('media_default');
                    $photos[] = ['name' => $originalName, 'textStatus' => 'done', 'fileID' => $vector->id];
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                \Log::error($th->getMessage() . ' line : ' . $th->getLine());
                $exception = [
                    'success' => false,
                    'message' => 'Exception Errors',
                    'code' => 2,
                    'errors' => ['files' => __('validation.error_contact_administrator')],
                ];
                throw new HttpResponseException(response()->json($exception, 422));
            }
            return response()->json(['files' => $photos], 200);

        }

    }

    public function check_unique(Request $request)
    {

        $files = $request->get('files');
        $file_names = collect($files)->map(function ($item) {
            return $item[0];
        });
        $file_hashes = collect($files)->map(function ($item) {
            return $item[1];
        });

        $images = Vector::query();

        $folder = $request->get('folder');
        if ($folder) {
            $folder_id = VectorFolder::firstOrCreate(['folder' => $folder])->id;
            $images->where('folder_id', $folder_id);
        }

        $file_name_conflicts = $images->whereIn('original_name', $file_names)
            ->pluck('original_name')
            ->unique()
            ->values()
            ->map(function ($filename) use ($files) {
                return collect($files)->map(function ($item) {
                    return [$item[0], $item[0]];
                })->filter(function ($item) use ($filename) {
                    return $filename === $item[1];
                })->values()->flatten()->toArray()[0];
            })
            ->toArray();

        $file_hash_conflicts = $images->whereIn('hash', $file_hashes)
            ->pluck('hash')
            ->unique()
            ->values()
            ->toArray();

        return [
            'file_name_conflicts' => $file_name_conflicts,
            'file_hash_conflicts' => $file_hash_conflicts,
        ];
    }

    public function index_warehouse(Request $request)
    {

        $routes = get_vue_image_store_routes();

        $html_breadcrumbs = [
            'title' => __('views.VectorsWarehouse'),
            'subtitle' => __('views.Index'),
        ];

        $user = Auth::user()->only(
            'id',
            'email',
            'api_token'
        );
        $is_vectors_site = true;
        return view(
            'admin_v2.image.warehouse.index',
            compact(
                'html_breadcrumbs',
                'routes',
                'is_vectors_site',
                'user'
            )
        );
    }


    public function show($id, $slug = null)
    {
        // dd('qwd');
        $array = explode('-', $id);


        if (!(isset($array[1]) && is_numeric($array[1]))) {
            abort(404);
        }


        $id = $array[1];
        header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
        header("Pragma: no-cache"); //HTTP 1.0
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        $lang = app()->getLocale();
        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }
        $response = Vector::with('category')
            ->where('status', 'active')
            ->select('vectors.*')
            ->where('vectors.id', $id)
            ->firstOrFail();


        if (Auth::check() && $response->user_id != Auth::user()->id && $response->status == 'pending' && Auth::user()->role != 'admin') {
            abort(404);
        } else {
            if (Auth::guest() && $response->status == 'pending') {
                abort(404);
            }
        }


        $tags_chunk = $response
            ->tags()
            ->where('local', app()->getLocale())
            // ->select('tag','slug')
            // ->get()
            ->pluck('tag')
            ->toArray();
        // dd($tags_chunk);


        $response->count_view = $response->count_view + 1;
        $response->save();


        $user_subscription_remaining_array = Query::user_subscription_remaining();
        $user_subscription_remaining = $user_subscription_remaining_array['user_subscription_remaining'];
        $days_left = $user_subscription_remaining_array['days_left'];
        $title_plan = $user_subscription_remaining_array['title_plan'];

        $images = \App\Helper::similar_search_in_elasticsearch('images', $response->title, ["not_in_ids" => [$response->id]]);
        $simler_images = collect($images->items())->take(10);


        return view('image.show', compact('response', 'tags_chunk', 'simler_images', 'user_subscription_remaining', 'days_left', 'title_plan'));


    }//<--- End Method



    public function search_keys(Request $request)
    {
        $data_path = route('admin.vectors_search_keys');
        $html_breadcrumbs = [
            'title' => __('Search Keys'),
            'subtitle' => __('Search Keys'),
            'datatable' => true,
        ];
        if (!$request->datatable)
            return view('admin_v2.search_keys', compact('html_breadcrumbs', 'data_path'));
        $data = process_datatable_query(VectorSearchKey::query(), function (
            $query,
            $search
        ) {
            return $query
                ->where(function ($query) use ($search) {
                    $query->where('key_word', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function replace_filemanger()
    {
        $data = Vector::select('id')->orderBy('id','desc')->pluck('id');

        $html_breadcrumbs = [
            'title' => __('views.VectorFilemangerReplace'),
            'subtitle' => __('views.New'),
        ];
        $is_vectors_site = true;
        $accessToken = \auth()->user()->createToken('userToken')->accessToken;
        return view(
            'admin_v2_vectors.vectors.filemaneger.replace',
            compact('html_breadcrumbs', 'data', 'is_vectors_site', 'accessToken')
        );
    }
}
