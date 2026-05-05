<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\ImageExport;
use App\Jobs\PublishAdminImage;
use App\Models\Contributor;
use App\Models\ImageCategory;
use App\Models\ImageFolder;
use App\Models\ImageSearchKey;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helper;
use App\Models\Stock;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\CategoryImage;
use App\Models\Notifications;
use App\Models\ImagesReported;
use App\Models\AdminCollection;
use App\Models\CollectionImage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ImagesController;
use App\Models\AdminCollectionImage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SaveMultipleImages;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ContributorImage;
use App\Models\Vector;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{


    public function index()
    {
        $index_url = route('admin.images.datatable');
        $edit_url = route('admin.images.edit', 0);
        $destroy_url = route('admin.images.destroy', 0);
        $export_url = route('admin.images.export');
        $folders = ImageFolder::select('folder', 'id')->get();
        $html_breadcrumbs = [
            'title' => __('views.Images'),
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
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.images.admin_collections.update', 0),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ];
                }),
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Delete Selected'),
                'url' => route('admin.images.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ]
        ];

        return view(
            'admin_v2.image.index',
            compact(
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'export_url',
                'folders',
            )
        );
    }

    public function datatable(Request $request)
    {
        $data_request_query = $request->get('query');
        $query = Image::with(
            'adminCollection',
            'category'
        )->withCount('downloads', 'old_downloads', 'likes');
        if (isset($data_request_query['folder_id']) && $data_request_query['folder_id'] !== "All") {
            $folder_id = $data_request_query['folder_id'];
            $query = $query->where('folder_id', $folder_id);
        }
        if ($request->input('query.status'))
            $query->where('status', $request->input('query.status'));
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
        $query = Image::with('user');
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
        return Excel::download(new  ImageExport($query->get()), now() . '.xlsx');
    }

    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->can_delete_images)
            return redirect()->back()->with('error', "You can't delete the image");
        // TODO all multiple delete
        $ids = explode(',', $id);
        foreach ($ids as $key => $id) {
            $file = Image::find($id);
            Log::channel('info')->error("Delete Image", [
                'ImageID' => $id,
                'User' => \auth()->user()->email,
                'Ip' => $request->ip(),
            ]);
//            $contributor_image_id = $file->contributor_image_id;
//            // Delete Notification
//            $notifications = Notifications::where('destination', $id)
//                ->whereIn('type', [2, 3, 6])
//                ->delete();
//            // Collection Image
//            $collectionsImages = CollectionImage::where('image_id', '=', $id)->delete();
//            // Image Reported
//            $imagesReporteds = ImagesReported::where('image_id', '=', $id)->delete();
//            // ALL RESOLUTIONS IMAGES
//            $stocks = Stock::where('image_id', '=', $id)->delete();
//            if (isset($file->medium) && $file->medium != '') {
//                $medium = $file->medium;
//                // Delete medium
//                if (\Storage::disk('s3')->exists($medium)) {
//                    \Storage::disk('s3')->delete($medium);
//                }
//            }
//            if (isset($file->small) && $file->small != '') {
//                $small = $file->small;
//                // Delete small
//                if (\Storage::disk('s3')->exists($small)) {
//                    \Storage::disk('s3')->delete($small);
//                }
//            }
//
//            if (isset($file->preview)) {
//                $preview_image = $file->preview;
//                // Delete preview
//                if (\Storage::disk('s3')->exists($preview_image)) {
//                    \Storage::disk('s3')->delete($preview_image);
//                }
//            }
//
//            if (isset($file->thumbnail)) {
//                $thumbnail = $file->thumbnail;
//                // Delete thumbnail
//                if (\Storage::disk('s3')->exists($thumbnail)) {
//                    \Storage::disk('s3')->delete($thumbnail);
//                }
//            }
//
//            if (isset($file->search)) {
//                $search = $file->search;
//                // Delete search
//                if (\Storage::disk('s3')->exists($search)) {
//                    \Storage::disk('s3')->delete($search);
//                }
//            }
//
//
//            if ($contributor_image_id) {
//                $contributor_file = $file->contributor_file;
//                if (isset($contributor_file->thumbnail)) {
//                    $thumbnail = $contributor_file->thumbnail;
//                    if (\Storage::disk('s3')->exists($thumbnail)) {
//                        \Storage::disk('s3')->delete($thumbnail);
//                    }
//                }
//
//                if (isset($contributor_file->preview)) {
//                    $preview = $contributor_file->preview;
//                    if (\Storage::disk('s3')->exists($preview)) {
//                        \Storage::disk('s3')->delete($preview);
//                    }
//                }
//                $contributor_file->tags_img()->delete();
//                $contributor_file->category()->detach();
//                $contributor_file->release_image()->delete();
//                $contributor_file->submmission_item()->delete();
//                $contributor_file->delete();
//
//            }

            $file->delete();
        }

        cache()->clear();
        \Artisan::call('stock:resort');

        return redirect()->route('admin.images.index');
    }

    public function edit($id)
    {
        $image = Image::findOrFail($id);
        $title = __('تعديل صورة');
        $categoris = CategoryImage::where('image_id', $id)->pluck(
            'category_id'
        );
        $all_categories = \App\Models\ImageCategory::orderBy('name_en')->get();

        $tags_ar = $image->tags()->where('local', 'ar')->pluck('title');
        $tags_en = $image->tags()->where('local', 'en')->pluck('title');

        $html_breadcrumbs = [
            'title' => __('views.Images'),
            'subtitle' => __('views.Edit'),
        ];

        return view('admin_v2.image.edit', compact(
            'html_breadcrumbs',
            'image',
            'all_categories',
            'categoris',
            'tags_ar',
            'tags_en',
            'title'
        ));
    }

    public function update(Request $request)
    {
        $sql = Image::with('tags')->find($request->id);

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

        $slug = "image-{$request->id}-" . $request->get('slug');
        if ($sql->slug !== strtolower($slug)) {
            $from_url = route('photo.show', $sql->slug);
            $to_url = route('photo.show', strtolower($slug));

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
        $sql->how_use_image = $request->how_use_image;
        $sql->attribution_required = $request->attribution_required;


        $sql->save();

        dispatch(new \App\Jobs\SeoImages($sql->id));

        if ($request->categories_id) {
            $check = CategoryImage::where('image_id', $request->id)->delete();
            for ($i = 0; $i < count($request->categories_id); $i++) {
                $categoryImages = CategoryImage::create([
                    'image_id' => $request->id,
                    'category_id' => $request->categories_id[$i],
                ]);
            }
        }

        if ($request->get('tags_en')) {
            sync_tags($sql, $request->get('tag_en', []), 'en');
        }
        if ($request->get('tag_ar')) {
            sync_tags($sql, $request->get('tag_ar', []), 'ar');
        }
        dispatch(new \App\Jobs\UpdateStageEditImage([$sql->id]));

        \Session::flash('success_message', trans('admin.success_update'));

        return redirect()->route('admin.images.index');
    }

    public function activate(Request $request, $id)
    {
        $id = explode(',', $id);
        Image::whereIn('id', $id)->update(['status' => $request->get('status')]);

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

    public function index_pending()
    {
        $index_url = route('admin.images.pending.datatable');
        $edit_url = route('admin.images.edit', 0);
        $destroy_url = route('admin.images.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.ImagesPending'),
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
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.images.admin_collections.update', 0),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ];
                }),
            ],
        ];

        return view(
            'admin_v2.image.pending.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable_pending(Request $request)
    {
        $query = Image::where('status', 'pending')->with(
            'adminCollection',
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

    /* s:route-deleted-imges */
    public function index_deleted()
    {
        $index_url = route('admin.images.deleted.datatable');
        $edit_url = route('admin.images.edit', 0);
        $destroy_url = route('admin.images.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.filesDeleted'),
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
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.images.admin_collections.update', 0),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ];
                }),
            ],
        ];

        return view(
            'admin_v2.image.deleted.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable_deleted(Request $request)
    {
        $query = Image::onlyTrashed()->with(
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
    /* e:route-deleted-imges */
    /* s:route-deleted-contributor_images */
    public function index_deleted_contributor_images()
    {
        $index_url = route('admin.contributor_images.deleted.datatable');

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
            'admin_v2.image.deleted.contributor_images',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url')
        );
    }

    public function datatable_deleted_contributor_images(Request $request)
    {
        $query = ContributorImage::onlyTrashed()->with(
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

    /* e:route-deleted-contributor_images */
    public function create_filemanger()
    {
        $category = ImageCategory::get();
        $collection = AdminCollection::where('status', '1')->get();
        $folders = ImageFolder::withCount('images')->orderBy('images_count', 'desc')->pluck('folder');

        $html_breadcrumbs = [
            'title' => __('views.ImagesFilemanger'),
            'subtitle' => __('views.New'),
        ];
        $accessToken = \auth()->user()->createToken('userToken')->accessToken;
        return view(
            'admin_v2.image.filemaneger.create',
            compact('html_breadcrumbs', 'category', 'collection', 'folders', 'accessToken')
        );
    }

    /* e:route-deleted-contributor_images */
    public function replace_filemanger()
    {
        $html_breadcrumbs = [
            'title' => __('views.ImagesFilemanger'),
            'subtitle' => __('Replace'),
        ];
        $accessToken = \auth()->user()->createToken('userToken')->accessToken;
        return view(
            'admin_v2.image.filemaneger.replace',
            compact('html_breadcrumbs', 'accessToken')
        );
    }
    //set psd files to uploaded images
    public function psd_filemanger()
    {
        $html_breadcrumbs = [
            'title' => __('views.ImagesFilemanger'),
            'subtitle' => __('PSD'),
        ];
        $accessToken = \auth()->user()->createToken('userToken')->accessToken;
        return view(
            'admin_v2.image.filemaneger.psd',
            compact('html_breadcrumbs', 'accessToken')
        );
    }

    public function store_filemanger(SaveMultipleImages $request)
    {

        \Log::channel('info')->info('Image Start1');
        $photos = [];


        if (Auth::guest()) {
            return response()->json([
                'session_null' => true,
                'success' => false,
            ]);
        }
        $files = [];
        $inputs = $request->all();
        if ($request->hasFile('files')) {
            $xy = $request->file('files');
            foreach ($xy as $i => $value) {
                $originalName = $value->getClientOriginalName();
                $type_mime_img = $value->getMimeType();
                $sizeFile = $value->getSize();
                $file_hash = hash_file('sha256', $value->path());
                $extension = strtolower($value->getClientOriginalExtension());

                if (!empty($inputs['description_ar'])) {
                    $description_ar = Helper::checkTextDb(
                        $inputs['description_ar']
                    );
                } else {
                    $description_ar = '';
                }
                if (!empty($inputs['description_en'])) {
                    $description_en = Helper::checkTextDb(
                        $inputs['description_en']
                    );
                } else {
                    $description_en = '';
                }

                $image = new Image();
                $image->title_ar = time() . Str::random(10);
                $image->title_en = time() . Str::random(10);
                $image->description_ar = trim($description_ar);
                $image->description_en = trim($description_en);
                $image->user_id = Auth::id();
                $image->status = 'pending';
                $token_id = Str::random(200);
                $image->token_id = $token_id;
                $image->save();
                $large = "uploads/images/{$image->id}/large/" . strtolower(time() . Str::random(5) . '.' . $extension);
                $image->save();
                $original = $large;
                if (!$value->move(dirname($original), pathinfo($large, PATHINFO_BASENAME)))
                    Log::error("ImageController cant move file. ($image->id)");
                chmod($original, 0777);
                $exif_data = @exif_read_data($original, 0, true);

                if (isset($exif_data['EXIF']['ISOSpeedRatings'][0])) {
                    $ISO = 'ISO ' . $exif_data['EXIF']['ISOSpeedRatings'][0];
                }

                if (isset($exif_data['EXIF']['ExposureTime'])) {
                    $ExposureTime = $exif_data['EXIF']['ExposureTime'] . 's';
                }

                if (isset($exif_data['EXIF']['FocalLength'])) {
                    $FocalLength =
                        round($exif_data['EXIF']['FocalLength'], 1) . 'mm';
                }

                if (isset($exif_data['COMPUTED']['ApertureFNumber'])) {
                    $ApertureFNumber =
                        $exif_data['COMPUTED']['ApertureFNumber'];
                }

                if (!isset($FocalLength)) {
                    $FocalLength = '';
                }

                if (!isset($ExposureTime)) {
                    $ExposureTime = '';
                }

                if (!isset($ISO)) {
                    $ISO = '';
                }

                if (!isset($ApertureFNumber)) {
                    $ApertureFNumber = '';
                }

                $exif =
                    $FocalLength .
                    ' ' .
                    $ApertureFNumber .
                    ' ' .
                    $ExposureTime .
                    ' ' .
                    $ISO;

                if (isset($exif_data['IFD0']['Model'])) {
                    $camera = $exif_data['IFD0']['Model'];
                } else {
                    $camera = '';
                }


                $image->extension = $extension;
                $image->exif = trim($exif);
                $image->camera = $camera;
                $image->how_use_image = 'free';
                $image->attribution_required = 'no';
                $image->original_name = $originalName;
                $image->hash = $file_hash;
                $image->save();
                $image->large = $large;


                $stock = new Stock();
                $stock->image_id = $image->id;
                $stock->name = pathinfo($large, PATHINFO_BASENAME);
                $stock->type = 'large';
                $stock->extension = $extension;
                $stock->token = $token_id;
                $stock->save();


                if (@$inputs['categories_id']) {
                    $cateogries = $inputs['categories_id'];

                    for ($i = 0; $i < count($cateogries); $i++) {
                        $categoryImages = CategoryImage::create([
                            'image_id' => $image->id,
                            'category_id' => $cateogries[$i],
                        ]);
                    }
                }

                $files[] = [
                    'path' => $large,
                    'type_mime_img' => $type_mime_img,
                    'sizeFile' => $sizeFile,
                    'name' => $originalName,
                    'textStatus' => "done",
                ];

                if (@$inputs['collection_id']) {
                    AdminCollectionImage::create([
                        'image_id' => $image->id,
                        'admin_collection_id' => $inputs['collection_id'],
                    ]);
                }

                if (@$inputs['folder']) {
                    $folder_id = ImageFolder::firstOrCreate(['folder' => $inputs['folder']])->id;
                    $image->folder_id = $folder_id;
                }
                $image->save();
                Storage::disk('s3')->put($image->large, file_get_contents($original));
                Storage::disk('public')->deleteDirectory("uploads/images/{$image->id}");
                dispatch(new PublishAdminImage($image->id))->onQueue('media_default');
            }
        }
        return ['files' => $files];
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

        $images = Image::query();

        $folder = $request->get('folder');
        if ($folder) {
            $folder_id = ImageFolder::firstOrCreate(['folder' => $folder])->id;
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

        // return $routes;

        $html_breadcrumbs = [
            'title' => __('views.ImagesWarehouse'),
            'subtitle' => __('views.Index'),
        ];

        $user = Auth::user()->only(
            'id',
            'email',
            'api_token'
        );

        return view(
            'admin_v2.image.warehouse.index',
            compact(
                'html_breadcrumbs',
                'routes',
                'user'
            )
        );
    }

    public function search_keys(Request $request)
    {
        $data_path = route('admin.images_search_keys');
        $html_breadcrumbs = [
            'title' => __('Search Keys'),
            'subtitle' => __('Search Keys'),
            'datatable' => true,
        ];
        if (!$request->datatable)
            return view('admin_v2.search_keys', compact('html_breadcrumbs', 'data_path'));
        $data = process_datatable_query(ImageSearchKey::query(), function (
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


    public function warehouse_remove_bg(Request $request)
    {
        $routes = get_vue_image_store_remove_bg_routes();


        $html_breadcrumbs = [
            'title' => __('views.ImagesWarehouseRemoveBg'),
            'subtitle' => __('views.Index'),
        ];

        $user = Auth::user()->only(
            'id',
            'email',
            'api_token'
        );

        return view(
            'admin_v2.image.warehouse.remove_bg',
            compact(
                'html_breadcrumbs',
                'routes',
                'user'
            )
        );
    }


    public function rejected($type)
    {
        $index_url = route('admin.files.datatable.rejected',['type' => $type]);
        $users = new User();
        $html_breadcrumbs = [
            'title' => __('views.'.ucfirst($type)) . __('views.hard_rejected'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        switch ($type) {
            case 'images':
                $users = $users->where('role','admin_image_editor');
                break;
            case 'videos':
                $users = $users->where('role','admin_video_editor');
                break;
            case 'vectors':
                $users = $users->where('role','admin_vector_editor');
                break;

            default:
                $post_link = route('photo.show',':slug');
                $users = $users->where('role','admin_image_editor')->get();
                break;
        }
        $users = $users->get();
        $subheader_actions = [

        ];

        return view(
            'admin_v2.image.rejected',
            compact(
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'users',
            )
        );
    }

    public function datatable_rejected(Request $request,$type)
    {
        $data_request_query = $request->get('query');
        switch ($type) {
            case 'images':
                $class_name = new Image();
        $felde_name = 'date';

                break;
            case 'videos':
                $class_name = new Video();
        $felde_name = 'date';

                break;
            case 'vectors':
                $class_name = new Vector();
        $felde_name = 'created_at';

                break;
            default:
                $class_name = new Image();
                $felde_name = 'date';

                break;
        }
        $query = $class_name->with(
           [ 'category','likes' => function ($query) {
            $query->select('id');
        },'downloads' => function ($query) {
            $query->select('id');
        },
         'old_downloads'=> function ($query) {
            $query->select('id');
        },
        ]
        )->withCount('downloads','old_downloads', 'likes');

       $query = $query->whereHas('contributor_file',function($q){
            $q->where('contributor_stage',4);
        });

        if ($request->input('query.user_id'))
            $query->where('reviewer_id', $request->input('query.user_id'));
        if ($request->input('query.contributor_id'))
            $query->where('user_id', $request->input('query.contributor_id'))->where('user_type', Contributor::class);

            if($request->has('query.date_from')){
                $from = date('Y-m-d', strtotime($request->input('query.date_from')));
                $query = $query->whereDate($felde_name, '>=', $from);
            }

            if($request->has('query.date_to')){
                $to = date('Y-m-d', strtotime($request->input('query.date_to')));
                $query = $query->whereDate($felde_name, '<=', $to);
            }

            $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                ->orderBy('date', 'DESC')
                ->where(function ($query) use ($search) {
                    if(isset($search)){
                        if (is_numeric($search) && $search != 0) {
                            $query->where('id', $search);
                        } else {
                            $query->where('title_en', 'like', '%' . $search . '%')
                                ->orWhere('title_ar', 'like', '%' . $search . '%');
                        }
                    }
                });
        });

        return $data;
    }

    public function warehouse_remove_bg_check(Request $request)
    {
        $check_manual = FALSE;
        $accessToken = NULL;
        $downloadImage = NULL;
        if ($request->route()->getName() === "admin.images.warehouse_remove_bg.check_manual") {
            $check_manual = TRUE;
            $accessToken = \auth()->user()->createToken('userToken')->accessToken;
            $downloadImage = route('admin.images.downloadImage',['id'=>0,'type'=>':filetype']);

        }
        $status = $request->get('status','pending');
        $removebg_type = $request->get('removebg_type','paid');
        $image_id = $request->get('image_id');
        if (in_array($status,['all','pending','active'])) {
            $data = Image::whereNotNull('removebg_image')->where('removebg_status','done')->select('id','preview','removebg_image','title_ar','slug','removebg_status_disply');
            $count_approve =  Image::whereNotNull('removebg_image')->where('removebg_status','done')->where('removebg_type',"paid")->where('removebg_status_disply','active');
            if (in_array($status,['pending','active'])) {
                $data = $data->where('removebg_status_disply',$status);
                $count_approve = $count_approve->where('removebg_status_disply',$status);
            }

            if (isset($removebg_type)) {
                $data = $data->where('removebg_type',$removebg_type);
            }

            if(isset($image_id) && is_numeric($image_id))
                $data->where('id',$image_id);


            $count_approve = $count_approve->count();
            $data = $data->paginate(20);
            $data->appends(['status' =>$status,'image_id'=>$image_id,'removebg_type'=>$removebg_type]);


            
            $html_breadcrumbs = [
                'title' => __('views.ImagesWarehouseRemoveBg'),
                'subtitle' => __('views.ImagesWarehouseRemoveBgCheck'),
            ];
            return view(
                'admin_v2.image.warehouse.remove_bg.check',
                compact('html_breadcrumbs','data','count_approve','check_manual','accessToken','downloadImage','removebg_type')
            );
        }
    }

    public function update_status_removebg_display(Request $request,$id)
    {
        if ($request->ajax()) {
            $status = $request->get('status');
            $data = Image::whereNotNull('removebg_image')->where('removebg_status','done')->findOrFail($id);
            $current_status = $status === 'active'?'pending':'active';
            $data->removebg_status_disply = $current_status;
            $data->save();

            $count_approve =  DB::table('images')->whereNotNull('removebg_image')->where('removebg_status','done')->where('removebg_type',"paid")->where('removebg_status_disply','active')->count();

            return  response()->json([
                'message' =>  trans('admin.success_update'),
                'count_approve'=> $count_approve,
                'data' =>  $data,
            ]);
        }
        abort(404);
    }



    public function warehouse_remove_bg_check_admin()
    {
        $index_url = route('admin.images.warehouse_remove_bg.check.admin.datatable');
        $edit_url = route('admin.images.warehouse_remove_bg.update_status_removebg_display_admin', 0);

        $html_breadcrumbs = [
            'title' => __('views.ImagesWarehouseRemoveBgCheck'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views.removebg_active'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-info',
                        'url' => route('admin.images.warehouse_remove_bg.update_status_removebg_display_admin', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.removebg_pending'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.images.warehouse_remove_bg.update_status_removebg_display_admin', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],

        ];

        return view(
            'admin_v2.image.warehouse.remove_bg.admin',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url','edit_url')
        );
    }

    public function datatable_warehouse_remove_bg_check_admin(Request $request)
    {
        $status = $request->input('query.status','pending');
        $removebg_type = $request->input('query.removebg_type','paid');

        $all_status = ['pending','active'];
        $all_removebg_type = ['paid','free','manual'];
        $query = Image::whereNotNull('removebg_image')->where('removebg_status','done')
        ->when(in_array($status,$all_status) , function ($query) use ($status){
            $query->where('removebg_status_disply',$status);
        })
        ->when(in_array($removebg_type,$all_removebg_type) , function ($query) use ($removebg_type){
            $query->where('removebg_type',$removebg_type);
        });
        
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
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

    public function update_status_removebg_display_admin(Request $request,$id)
    {
        $id = explode(',', $id);
        $status = $request->get('status');
        if (in_array($status,['active','pending'])) {
            $data = Image::whereNotNull('removebg_image')->where('removebg_status','done')->where('removebg_type','paid')->whereIn('id',$id)->update(['removebg_status_disply'=>$status]);

            \Session::flash('success', trans('admin.success_update'));
            # code...
        }else{
            \Session::flash('error', trans('admin.status not exist'));

        }

        return redirect()->back();
    }

    public function downloadImage($id,$type)
    {
        $image = Image::findOrFail($id);

        try {
            //code...
            switch ($type) {
                case 'jpg':
                    $size = "large";
                    break;
                case 'png':
                    $size = "removebg_image";
                    break;
    
                }        
                // Call the downloadFile function with appropriate arguments;
        
                cache()->forget("image_show_{$image->id}_ar");
                cache()->forget("image_show_{$image->id}_en");
        
                    $path = $image->{$size};
                    $path = str_replace('\\', '/', $path);
                    $path = trim($path, '/');
        
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    $url = Storage::disk('s3')->temporaryUrl(
                        $path, now()->addHours(1),
                        [
                            'url' => 'https://cdn.arabsstock.com',
                            'ResponseContentType' => 'application/octet-stream',
                            'ResponseContentDisposition' => 'attachment; filename="' . "arabsstock_P{$image->id}_removebg.{$extension}" . '"',
                            'Expires' => '0',
                            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                            'Pragma' => 'public',
                        ]
                    );
                    if (app()->isLocal())
                        return redirect()->to($url);
                    return redirect()->to(str_replace(parse_url($url)['host'], parse_url(config('filesystems.disks.s3.url'))['host'], $url));

        } catch (\Throwable $th) {
            Log::error("msg: ". $th->getMessage() ."file:  ". $th->getFile(). ' line : ' . $th->getLine(). "  user_id: " .auth()->id());

            return redirect()->back();
            
        }    

    }

}
