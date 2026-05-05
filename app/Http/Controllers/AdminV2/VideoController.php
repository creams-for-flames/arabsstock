<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\VideoExport;
use App\Jobs\PublishAdminVideo;
use App\Models\Contributor;
use App\Models\Image;
use App\Models\User;
use App\Models\VideoCategory;
use App\Models\VideoFolder;
use App\Models\ContributorVideo;
use App\Helper;
use App\Models\Video;
use App\Models\VideoSearchKey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CategoryVideo;
use App\Models\AdminCollection;
use App\Models\CollectionVideo;
use App\Jobs\StoreCategoryAndTags;
use App\Http\Requests\UploadViedio;
use App\Http\Controllers\Controller;
use App\Jobs\CopyRawVideosFromContributorToArabsstock;
use App\Models\AdminCollectionVideo;
use App\Models\RawVideo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index()
    {


        $index_url = route('admin.videos.videos.datatable');
        $price_url = route('admin.videos.videos.price.edit', 0);
        $edit_url = route('admin.videos.videos.edit', 0);
        $destroy_url = route('admin.videos.videos.destroy', 0);
        $export_url = route('admin.videos.videos.export');
        $create_raw = route('admin.videos.videos.filemanger.create_raw',0);

        $html_breadcrumbs = [
            'title' => __('views.Video'),
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
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route(
                            'admin.videos.videos.admin_collections.update',
                            0
                        ),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ];
                }),
            ],
        ];

        $is_videos_site = true;
        $folders = VideoFolder::select('folder', 'id')->get();
        return view(
            'admin_v2_videos.video.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'price_url',
                'export_url',
                'folders',
                'create_raw'
            )
        );
    }

    public function datatable(Request $request)
    {
        $data_request_query = $request->get('query');
        $query = Video::where('parent_id', null)->with(
            'adminCollection',
            'category',
            'raw',
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
        $query = Video::where('parent_id', null)->with('user');
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
        return Excel::download(new  VideoExport($query->get()), now() . '.xlsx');
    }

    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->can_delete_videos)
            return redirect()->back()->with('error', "You can't delete the video");
        $file = Video::findOrFail($request->id);
        Log::channel('info')->error("Delete Video", [
            'VideoID' => $id,
            'User' => \auth()->user()->email,
            'Ip' => $request->ip(),
        ]);
//        $collectionsImages = CollectionVideo::where('video_id', '=', $request->id)->delete();
//        $stocks = Video::where('parent_id', $request->id)->get();
//        $file->tags()->detach();
//        if (count($stocks)) {
//            foreach ($stocks as $stock) {
//                // Delete Stock
//                if (isset($stock->preview)) {
//                    $stock_path = $stock->preview;
//                    if (\Storage::disk('s3')->exists($stock_path)) {
//                        \Storage::disk('s3')->delete($stock_path);
//                    }
//                }
//                $stock->delete();
//            }
//        }
//        if (isset($file->thumbnail)) {
//            if (\Storage::disk('s3')->exists($file->thumbnail)) {
//                \Storage::disk('s3')->delete($file->thumbnail);
//            }
//        }
//        if (isset($file->search)) {
//            if (\Storage::disk('s3')->exists($file->search)) {
//                \Storage::disk('s3')->delete($file->search);
//            }
//        }
//        if (isset($file->cut_video)) {
//            if (\Storage::disk('s3')->exists($file->cut_video)) {
//                \Storage::disk('s3')->delete($file->cut_video);
//            }
//        }
//        if (isset($file->gif_video)) {
//            if (\Storage::disk('s3')->exists($file->gif_video)) {
//                \Storage::disk('s3')->delete($file->gif_video);
//            }
//        }
//
//        if (isset($file->size_240p)) {
//            if (\Storage::disk('s3')->exists($file->size_240p)) {
//                \Storage::disk('s3')->delete($file->size_240p);
//            }
//        }
//
//        $contributor_video_id = $file->contributor_video_id;
//        if ($contributor_video_id) {
//            $contributor_file = $file->contributor_file;
//            if (isset($contributor_file->thumbnail)) {
//                $thumbnail = $contributor_file->thumbnail;
//                if (\Storage::disk('s3')->exists($thumbnail)) {
//                    \Storage::disk('s3')->delete($thumbnail);
//                }
//            }
//
//            if (isset($contributor_file->preview_admin)) {
//                $preview = $contributor_file->preview_admin;
//                if (\Storage::disk('s3')->exists($preview)) {
//                    \Storage::disk('s3')->delete($preview);
//                }
//            }
//            if ($contributor_file) {
//                $contributor_file->tags()->delete();
//                $contributor_file->category()->detach();
//                $contributor_file->release_video()->delete();
//                $contributor_file->submmission_item()->delete();
//                $contributor_file->delete();
//            }
//        }
        $file->delete();
        \Session::flash('success_message', trans('admin.success_update'));
        cache()->clear();
        \Artisan::call('stock:resort');

        return redirect()->route('admin.videos.videos.index');

    }

    public function edit_price($id)
    {


        $data = Video::with('child')->findOrFail($id);

        $html_breadcrumbs = [
            'title' => __('views.Video'),
            'subtitle' => __('views.Edit'),
        ];

        $is_videos_site = true;

        return view('admin_v2_videos.video.edit_price', compact('is_videos_site', 'html_breadcrumbs', 'data'));
    }

    public function update_price(Request $request, $id)
    {


        $sql = Video::with('child')->find($id);

        $rules = [
            'price' => 'required',
            'price.*' => 'required',
        ];

        $this->validate($request, $rules);
        $i = 0;
        foreach ($sql->child as $childItem) {
            $childItem->price = $request->get('price')[$i];
            $childItem->save();
            $i++;
        }

        \Session::flash('success_message', trans('admin.success_update'));

        return redirect()->route('admin.videos.videos.index');
    }

    public function edit($id)
    {


        $data = Video::with('category')->findOrFail($id);
        $all_categories = \App\Models\VideoCategory::orderBy('name_en')->get();
        $html_breadcrumbs = [
            'title' => __('views.Video'),
            'subtitle' => __('views.Edit'),
        ];

        $is_videos_site = true;

        return view('admin_v2_videos.video.edit', compact('is_videos_site', 'html_breadcrumbs', 'data', 'all_categories'));
    }

    public function update(Request $request, $id)
    {


        $sql = Video::with('tags')->find($id);

        $rules = [
            'title_ar' => 'required|min:3',
            'title_en' => 'required|min:3',
            'slug' => 'required|min:3|regex:/^[A-Za-z0-9\_-]+$/i',
            'description_ar' => 'sometimes|min:2',
            'description_en' => 'sometimes|min:2',
//            'tags_ar' => 'required',
//            'tags_en' => 'required',
            'categories_id' => 'required',
        ];

        $this->validate($request, $rules);

        $slug = "clip-{$request->id}-" . $request->get('slug');
        if ($sql->slug !== strtolower($slug)) {
            $from_url = route('video.show', $sql->slug);
            $to_url = route('video.show', strtolower($slug));

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

        $data = $request->only([
            'title_ar',
            'title_en',
            'description_ar',
            'description_en',
            'email',
            'status',
            'in_home',
        ]);
        $data['slug'] = $slug;

        $sql->update($data);
        dispatch(new \App\Jobs\SeoVideos($sql->id));


        if ($request->get('tags_ar')) {
            $tag_ar = explode(',', $request->get('tags_ar'));
            sync_tags($sql, $tag_ar, 'ar');
        }

        if ($request->get('tags_en')) {
            $tag_en = explode(',', $request->get('tags_ar'));
            sync_tags($sql, $tag_en, 'en');
        }
        dispatch(new \App\Jobs\UpdateStageEditVideo([$sql->id]));

        if ($request->categories_id) {
            $checkCategory = CategoryVideo::where(
                'video_id',
                $sql->id
            )->delete();
            for ($i = 0; $i < count($request->categories_id); $i++) {
                $categoryImages = CategoryVideo::create([
                    'video_id' => $sql->id,
                    'category_id' => $request->categories_id[$i],
                ]);
            }
        }

        \Session::flash('success_message', trans('admin.success_update'));

        return redirect()->route('admin.videos.videos.index');
    }

    public function add_to_admin_collection(Request $request, $id)
    {


        $id = explode(',', $id);
        $updates = collect($id)->map(function ($item) use ($request) {
            return [
                'video_id' => $item,
                'admin_collection_id' => $request->get('status'),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ];
        })->toArray();
        // TODO remove duplicated
        AdminCollectionVideo::insert($updates);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->back();
    }

    public function activate(Request $request, $id)
    {


        $id = explode(',', $id);
        Video::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->back();
    }

    public function create_filemanger(Request $request)
    {

        /**@var $user User */
        $user = Auth::user();
        if ($user->authorized_to_upload == 'yes') {
            $folders = VideoFolder::withCount('videos')->orderBy('videos_count', 'desc')->pluck('folder');
            $category = VideoCategory::get();
            $collection = AdminCollection::where('status', '1')->get();

            $html_breadcrumbs = [
                'title' => __('views.VideosFilemanger'),
                'subtitle' => __('views.New'),
            ];

            $is_videos_site = true;
            $accessToken = $user->createToken('userToken')->accessToken;
            return view(
                'admin_v2_videos.video.filemaneger.create',
                compact('is_videos_site', 'html_breadcrumbs', 'category', 'collection', 'folders', 'accessToken')
            );
        } else {
            return redirect('/');
        }
    }

    public function replace_filemanger(Request $request)
    {

        /**@var $user User */
        $user = Auth::user();
        if ($user->authorized_to_upload == 'yes') {
            $html_breadcrumbs = [
                'title' => __('views.VideosFilemanger'),
                'subtitle' => __('views.New'),
            ];
            $is_videos_site = true;
            $accessToken = $user->createToken('userToken')->accessToken;
            return view(
                'admin_v2_videos.video.filemaneger.replace',
                compact('is_videos_site', 'html_breadcrumbs', 'accessToken')
            );
        } else {
            return redirect('/');
        }
    }

    public function store_filemanger(UploadViedio $request)
    {

        // TODO add validation to at least sd resolutaion

        // return $request->all();

        if (Auth::guest()) {
            return response()->json([
                'session_null' => true,
                'success' => false,
            ]);
        }

        $input = $request->all();
        $data = $request->validate([
            'files' => 'required',
        ]);

        $photos = [];
        if ($request->hasFile('files')) {
            $extension = $request
                ->file('files')[0]
                ->getClientOriginalExtension();

            $originalName = $request->file('files')[0]->getClientOriginalName(); // TODO add validation
            $file_hash = md5_file($request->file('files')[0]->path());
            $name = str_random(20) . '.' . $extension;

            $path = $request->file('files')[0]->path();
            $ffmpeg = env('FFMPEG_PATH');
            $ffprobe = env('FFPROBE_PATH');

            $cmd = "$ffprobe -v quiet -print_format compact=print_section=0:nokey=1:escape=csv -show_entries format=duration $path";
            ob_start();
            passthru($cmd, $output);
            $duration = trim(ob_get_contents());
            ob_end_clean();

            $photo_object = new \stdClass();
            $photo_object->name = $name;
            $photo_object->textStatus = 'done';

            $photo_object->fileID = 1;
            $photos[] = $photo_object;
        }

        if (!empty($request->description_ar)) {
            $description_ar = Helper::checkTextDb($request->description_ar);
        } else {
            $description_ar = '';
        }

        if (!empty($request->description_en)) {
            $description_en = Helper::checkTextDb($request->description_en);
        } else {
            $description_en = '';
        }

        /* if (AdminSettings::first()->auto_approve_images == 'on') {
             $status = 'active';
         } else {
             $status = 'pending';
         }*/
        $status = 'pending';
        $token_id = str_random(200);

        $sql = new Video();

        $sql->title_ar = trim($request->title_ar);
        $sql->title_en = trim($request->title_en);
        $sql->description_ar = trim($description_ar);
        $sql->description_en = trim($description_en);
        $sql->user_id = Auth::user()->id;
        $sql->status = $status;
        $sql->token_id = $token_id;
        $sql->extension = strtolower($extension);
        $sql->how_use_image = 'free';
        $sql->attribution_required = 'no';
        $sql->original_name = $originalName;
        $sql->hash = $file_hash;
        $sql->preview = $name;
        $sql->duration = $duration;


        if ($request->hasFile('files')) {
            $new_folder = public_path(DS . 'uploads' . DS . 'videos' . DS . $sql->id);
            \Log::channel('info')->info($new_folder);
            if (!file_exists($new_folder)) {
                mkdir($new_folder, 0755, true);
            }
            Storage::disk('s3')->put(DS . 'uploads' . DS . 'videos' . DS . $sql->id . DS . $sql->preview, $request->file('files')[0]->get());
        }

        $imageID = $sql->id;
        sync_tags($sql, $request->get('tags_en', []), 'en');
        sync_tags($sql, $request->get('tags_ar', []), 'ar');

        if ($request->get('categories_id')) {
            $cateogries = $request->get('categories_id');

            for ($i = 0; $i < count($cateogries); $i++) {
                $categoryImages = CategoryVideo::create([
                    'video_id' => $imageID,
                    'category_id' => $cateogries[$i],
                ]);
            }
        }

        if ($request->get('collection_id')) {
            AdminCollectionVideo::create([
                'video_id' => $imageID,
                'admin_collection_id' => $request->get('collection_id'),
            ]);
        }

        if ($request->get('folder')) {
            $folder_id = VideoFolder::firstOrCreate(['folder' => $request->get('folder')])->id;
            $sql->folder_id = $folder_id;
        }
        $sql->save();
        dispatch(new PublishAdminVideo($sql))->onQueue(env('VIDEO_PROCESSING_QUEUE'));


        $arry = [
            [
                'name' => true,
                'size' => "Done Successfully",
                'url' => '',
                'thumbnailUrl' => '',
                'deleteUrl' => '',
                'deleteType' => 'DELETE',
            ],
        ];

        \Artisan::call('stock:resort');

        return response()->json(['files' => $photos], 200);
    }

    public function index_warehouse(Request $request)
    {


        $routes = get_vue_image_store_routes();

        $html_breadcrumbs = [
            'title' => __('views.VideosWarehouse'),
            'subtitle' => __('views.Index'),
        ];

        $user = Auth::user()->only(
            'id',
            'email',
            'api_token'
        );

        $is_videos_site = true;

        return view(
            'admin_v2.image.warehouse.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'routes',
                'user'
            )
        );
    }


    /*  */
    public function index_pending()
    {
        $html_breadcrumbs = [
            'title' => __('views.ImagesPending'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $index_url = route('admin.videos.pending.datatable');
        $price_url = route('admin.videos.videos.price.edit', 0);
        $edit_url = route('admin.videos.videos.edit', 0);
        $destroy_url = route('admin.videos.videos.destroy', 0);

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
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route(
                            'admin.videos.videos.admin_collections.update',
                            0
                        ),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __(
                            'views.Are you sure to update :number selected records status to :text ?',
                            ['number' => 0, 'text' => 'ttt']
                        ),
                    ];
                }),
            ],
        ];

        $is_videos_site = true;

        return view(
            'admin_v2_videos.video.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'edit_url',
                'destroy_url',
                'price_url'
            )
        );

    }

    public function datatable_pending(Request $request)
    {
        $query = Video::where('status', 'pending')->with(
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
    /*  */
    /* s:route-deleted-videos */
    public function index_deleted()
    {
        $html_breadcrumbs = [
            'title' => __('views.filesDeleted'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $index_url = route('admin.videos.deleted.datatable');

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

        $is_videos_site = true;

        return view(
            'admin_v2_videos.video.deleted.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'subheader_actions',
                'index_url'
            )
        );

    }

    public function datatable_deleted(Request $request)
    {

        $query = Video::onlyTrashed()->with(
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
    /* e:route-deleted-videos */
    /* s:route-deleted-contributor_videos */
    public function index_deleted_contributor_videos()
    {
        $index_url = route('admin.videos.contributor_videos.deleted.datatable');

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
            'admin_v2_videos.video.deleted.contributor_videos',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url')
        );
    }

    public function datatable_deleted_contributor_videos(Request $request)
    {
        $query = ContributorVideo::onlyTrashed()->with(
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

    /* e:route-deleted-contributor_videos */


    public function search_keys(Request $request)
    {
        $data_path = route('admin.videos_search_keys');
        $html_breadcrumbs = [
            'title' => __('Search Keys'),
            'subtitle' => __('Search Keys'),
            'datatable' => true,
        ];
        if (!$request->datatable)
            return view('admin_v2.search_keys', compact('html_breadcrumbs', 'data_path'));
        $data = process_datatable_query(VideoSearchKey::query(), function (
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

    public function create_raw(Request $request,$id)
    {
        abort_if(Auth::user()->role !== 'admin_video',404);
       $video = Video::where('contributor_video_id',0)->doesntHave('raw')->findOrFail($id);
        /**@var $user User */
        $user = Auth::user();
        if ($user->authorized_to_upload == 'yes') {

            $html_breadcrumbs = [
                'title' => __('views.VideosFilemanger'),
                'subtitle' =>  __('views.add_raw'),
            ];

            $is_videos_site = true;
            $accessToken = $user->createToken('userToken')->accessToken;
            return view(
                'admin_v2_videos.video.filemaneger.create_raw',
                compact('is_videos_site', 'html_breadcrumbs','video', 'accessToken')
            );
        } else {
            return redirect('/');
        }
    }

    public function raw(Request $request)
    {
        $index_url = route('admin.videos.raw_datatable');
        $edit_url = route('admin.videos.raw.edit',0);

        $html_breadcrumbs = [
            'title' => __('views.raw_videos'),
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
                        'url' => $edit_url,
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.removebg_pending'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => $edit_url,
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],

        ];


        return view(
            'admin_v2_videos.video.raw',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url','edit_url')

        );
        # code...
    }

    public function raw_datatable(Request $request)
    {
        $data_request_query = $request->get('query');
        $status = $request->input('query.status','pending');

        $all_status = ['pending','active'];

        $query = Video::with([
            'raw',
            'contributor_file.raw',
        ]
        )->where(function($query){
            $query->whereHas('contributor_file.raw')
            ->orWhereHas('raw');
        })->where('parent_id', null);
 
        $query =    $query->when(in_array($status,$all_status) , function ($query) use ($status){
                $query->where(function($query) use($status){
                    $query->whereHas('contributor_file.raw',function($query) use($status){
                        $query->where('contributor_raw_videos.status',$status);
                    })
                    ->orWhereHas('raw',function($query) use($status){
                        $query->where('raw_videos.status',$status);
                    });
                });
            });



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

    public function raw_edit(Request $request,$id)
    {
        $ids = explode(',', $id);
        $videos = Video::where(function($query){
            $query->whereHas('contributor_file.raw')
            ->orWhereHas('raw');
        })->where('parent_id', null)->with(
            'contributor_file.raw',
            'raw'
        )->whereIn('id',$ids)->get();

        if (count($videos) === 0) {
            if ($request->ajax()) {
                return  response()->json([
                    'msg'=>__('misc.no_results_found'),
                ],422);
            }
            abort(404);
            # code...
        }
        abort_if(count($videos) === 0,404);
        DB::beginTransaction();
        try {
            $status = $request->get('status');
            if (in_array($status,['active','pending'])) {
                foreach ($videos as $key => $video) {
                    if ($video->raw()->exists()) {
                        $raw = $video->raw;
                        $data['status'] = $status;
                        if($request->has('review_notes'))
                            $data['review_notes'] = $request->get('review_notes');
                        $raw->update($data);
                        if ($video->contributor_file()->exists() && $video->contributor_file->raw()->exists()) {
                            $data_contributor_have_video = ['status' => $status];
                            $data_contributor_have_video['contributor_stage'] = $status === 'active'?8:4;
                            $contributor_file = $video->contributor_file;
                            $contributor_raw =$contributor_file->raw;
                            $contributor_raw->update($data_contributor_have_video);

                        }
                    }elseif($video->contributor_file->raw()->exists() ){
                        if ($status === 'active' && !$video->raw()->exists()) {
                            $contributor_file = $video->contributor_file;
                            $raw =$contributor_file->raw;
                            $raw_copy = new RawVideo();
                            $raw_copy->video_id = $video->id;
                            $raw_copy->original_name = $raw->original_name;
                            $raw_copy->extension = $raw->extension;
                            $raw_copy->hash = $raw->hash;
                            $raw_copy->status = $status;
                            $raw_copy->created_at =  Carbon::now();
                            // $raw_copy->has_noise = $raw->has_noise;
                            $raw_copy->duration = $raw->duration;
                            $raw_copy->save();
                            $name = Str::random(20).".$raw_copy->extension";
                            $preview_name = "preview-".Str::random(20).".".pathinfo($raw->preview,PATHINFO_EXTENSION);
                            $original = "/uploads/raw_videos/$raw_copy->id/original/$name";
                            $preview = "/uploads/raw_videos/$raw_copy->id/$preview_name";
                            $raw_copy->original = $original;
                            $raw_copy->preview = $preview;
                            $raw_copy->save();

                            dispatch(new CopyRawVideosFromContributorToArabsstock($raw->id,$raw_copy->id));
                            $raw_copy->updated_at =  Carbon::now();
                            $raw_copy->save();
                            # code...
                            $data_contributor = ['status' => $status,'contributor_stage'=> 5];
                        }else{
                            
                            $data_contributor = ['status' => $status,'contributor_stage'=> 4];
                            if($request->has('review_notes'))
                                 $data_contributor['review_notes'] = $request->get('review_notes');
                        }
                        $raw->update($data_contributor);
        
                    }
                }
    
            }
            DB::commit();
            return response()->json([
                'msg' =>__("misc.success_update"),
            ]);
            //code...
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        
    }
}
