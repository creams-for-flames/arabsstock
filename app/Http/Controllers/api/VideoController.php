<?php

namespace App\Http\Controllers\api;

use App\Models\CategoryVideo;
use App\Models\AdminCollectionVideo;
use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoFolder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminCollection;
use App\Models\CategoryVideoAdmin;
use App\Models\Contributor;
use App\Models\RejectionReason;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function options($id, $slug = null)
    {


        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);

        $category_admin = CategoryVideoAdmin::where('id', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                ];
            });

        $categories = VideoCategory::query()
            ->orderBy('sort')
            ->orderBy('name_en')
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->{"name_" . $lang},
                ];
            });

        $licenses = getLicenses(["free", "editorial_only"]);

        $reasons_rejection = RejectionReason::query()
            ->where('status','active')
            ->where('category','videos')
            ->orderBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => "{$item->title} -" .__('admin.'.$item->type),
                    'description' => "{$item->description_ar} \n {$item->description_en} . ",
                ];
            });
        return [
            'options' => [
                'categories_admin' => $category_admin,
                'categories' => $categories,
                'licenses' => $licenses,
                'reasons_rejection' => $reasons_rejection,
            ],
        ];
    }

    public function filters($id, $slug = null)
    {


        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);

        $status_list = [
            'all' => trans('views.all'),
            'complete' => trans('views.complete_edit'),
            'half_edit' => trans('views.half_edit'),
            'no_edit' => trans('views.no_edit'),
            'rejected' => trans('views.rejected')
        ];

        $sort_list = [
            'updated_at' => trans('views.last_updated_at'),
            'id' => trans('views.first_created_at'),
        ];

        $folders = VideoFolder::pluck('folder', 'id')->toArray();

        $collections = AdminCollection::where('status', '1')->pluck('title', 'id');

        $category_admin = CategoryVideoAdmin::where('id', '>', 0)->pluck('name', 'id');
        $categories = VideoCategory::get()->pluck('name', 'id');
        $categories[-1] = __('global.unclassified');

        $contributor = Contributor::has('created_videos')->get()->pluck('name', 'id');


        $publisher_list = [
            'all' => trans('views.the_all'),
            'supervisor' => trans('views.supervisor'),
            'contributor' => trans('views.contributor'),
        ];
        $role = "admin_video";
        $publishers = DB::table("users")->where('role',$role)->get()->pluck('name', 'id');
        $data[] = [
            'sort_by' => ['data' => $sort_list, 'type' => "select"],
            'type' => ['data' => $status_list, 'type' => "select"],
            'categories_admin' => ['data' => $category_admin, 'type' => "select"],
            'categories' => ['data' => $categories, 'type' => "select"],
            'collection' => ['data' => $collections, 'type' => "select"],
        ];

        $data[] = [
            'folder' => ['data' => $folders, 'type' => "select"],
            'publisher_type' => ['data' => $publisher_list, 'type' => "select"],
            'publisher' => ['data' => $publishers, 'type' => "select"],
            'contributor' => ['data' => $contributor, 'type' => "select"],
            'search' => ['data' => [], 'type' => "input"],
        ];

        return response()->json(['filters'=>$data]);

    }

    public function index(Request $request)
    {


        $videos = Video::with(['tags', 'category', 'computer_vision_tags', 'category_admin', 'contributor_file.contributor','contributor_file.release_video'])->where('videos.parent_id', null);
        if ($request->has('search') and $request->get('search') != "") {
            $query = $request->get('search');
            if ((filter_var($query, FILTER_VALIDATE_INT) !== false)) {
                $videos = $videos->where('id', $query);
            }else{
                $ids = \App\Helper::search_in_elasticsearch_items('videos', $query, [], 10000)['items'];
                $videos = $videos->whereIn('id', $ids);
                if (count($ids)) {
                    $ids_ordered = implode(',', $ids);
                  $videos =   $videos->orderBy(DB::raw("FIELD(id, $ids_ordered)"));
                }
            }
        }
        if ($request->get('publisher_type')) {
            $publisher_type = $request->get('publisher_type');
            switch ($publisher_type) {
                case 'supervisor':
                      $user_type ="App\Models\User";
                      $videos->where('user_type', $user_type);
                    break;
                    case 'contributor':
                        $user_type ="App\Models\Contributor";
                        $videos->where('user_type', $user_type);
                      break;

            }
        }
        if ($request->get('type')) {

            $type = $request->get('type');
            switch ($type) {
                case 'complete':
                    $videos->where(function($query){
                        $query
                        ->doesntHave('contributor_file')
                        ->orWhereHas('contributor_file',function($q){
                            $q->where('contributor_stage',8);
                        });
                    })->where('stage_edit',2);
                    break;
                case 'half_edit':
                    $videos->where(function($query){
                        $query
                        ->doesntHave('contributor_file')
                        ->orWhereHas('contributor_file',function($q){
                            $q->where('contributor_stage',8);
                        });
                    })->where('stage_edit',1);
                    break;
                case 'no_edit':
                    $videos->where(function($query){
                        $query
                        ->doesntHave('contributor_file')
                        ->orWhereHas('contributor_file',function($q){
                            $q->where('contributor_stage',8);
                        });
                    })->where('stage_edit',0);
                    break;
                case 'rejected':
                    $videos->whereHas('contributor_file',function($q){
                        $q->whereIn('contributor_stage',[3,4]);
                    });
                    break;

            }
        }

        if ($request->get('categories_admin')) {
            $videos->where('category_admin_id', $request->get('categories_admin'));
        }

        if ($request->get('categories') ) {
            if($request->get('categories') == -1){
                $videos->doesntHave('category');
            }else{
                $videos->whereIn('id', function ($q) {
                    $q->select('video_id')->from('category_video')->where('category_id', request()->get('categories'));
                });
            }
        }

        if ($request->get('collection')) {
            $videos_ids = AdminCollectionVideo::where('admin_collection_id', $request->get('collection'))
                ->pluck('video_id')
                ->toArray();
            $videos->whereIn('id', $videos_ids);
        }

        if ($request->get('folder')) {
            $videos->where('folder_id', $request->get('folder'));
        }
        if ($request->get('contributor') && $request->get('publisher')) {
            $users_id = [$request->get('contributor'),$request->get('publisher')];
            $videos->whereIn('user_id', $users_id);
        }else{
            if ($request->get('contributor')) {
                $videos->where('user_id', $request->get('contributor'));
            }
            if ($request->get('publisher')) {
                $videos->where('user_id', $request->get('publisher'));
            }

        }

        $videos->whereDoesntHave('contributor_file',function($q){
            $q->where('contributor_stage','=',4);
        });

        $videos->orderBy($request->get('sort_by', 'id'), 'desc');

        $page = $request->get('page');
        $perpage = $request->get('perpage');

        $offset = ($page - 1) * $perpage;
        $count = $videos->count();
        $videos->offset($offset)->limit($perpage);

        $lang = app()->getLocale();
        $data = $videos
            ->get()
            ->map(function ($video) {
                $video->tags_ar = $video->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'ar';
                    })
                    ->pluck('title');
                return $video;
            })
            ->map(function ($video) {
                $video->tags_en = $video->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'en';
                    })
                    ->pluck('title');
                return $video;
            })
            ->map(function ($video) use ($lang) {
                $video->category_ids = $video->category->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->id,
                        'label' => $item->{"name_" . $lang},
                    ];
                });
                return $video;
            })
            ->map(function ($video) use ($lang) {
                if ($video->category_admin) {
                    $video->category_admin_id = [
                        'id' => $video->category_admin->id,
                        'label' => $video->category_admin->name,
                    ];
                }
                return $video;
            })
            ->map(function ($video) {
                $video->computer_vision_tags_ar = $video->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_ar,
                        'label' => $item->tag_ar . $confidence,
                    ];
                });
                $video->computer_vision_tags_en = $video->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_en,
                        'label' => $item->tag_en . $confidence,
                    ];
                });
                return $video;
            })
            ->map(function ($video)  {
                $video->release_ids = [];
                if ($video->contributor_video_id !== 0 and $video->contributor_file()->has('release_video')) {
                    $video->release_ids = $video->contributor_file->release_video->map(function ($item)  {
                        return [
                            'id' => $item->release_id,
                            'label' => $item->release->name,
                            'file' => url($item->release->file),
                        ];
                    });
                    # code...
                }
                return $video;
            })
            ->map(function ($video) {
                $status_contributor_file_lable = NULL;
                $status_contributor_file = NULL;

                if (isset($video->contributor_file)) {
                    if ($video->contributor_file->contributor_stage == 3) {
                        $status_contributor_file_lable = __('views.rejected');
                        $status_contributor_file = 'rejected';

                    }

                    if ($video->contributor_file->contributor_stage == 4){
                        $status_contributor_file_lable = __('views.hard_rejected');
                        $status_contributor_file = 'hard_rejected';

                    }
                }
                return collect([
                    'id' => $video->id,
                    'owner' => $video->contributor_file->contributor->name ?? 'admin',
                    'status_contributor_file_lable' => $status_contributor_file_lable,
                    'status_contributor_file' => $status_contributor_file,
                    'thumbnail' => cdn($video->thumbnail),
                    'height_thumbnail' => $video->height_thumbnail,
                    'width_thumbnail' => $video->width_thumbnail,
                    'preview' => $video->cut_video,
                    'height_preview' => $video->height_preview,
                    'width_preview' => $video->width_preview,
                    'title_ar' => $video->title_ar,
                    'title_en' => $video->title_en,
                    'description_ar' => $video->description_ar,
                    'description_en' => $video->description_en,
                    'status' => $video->status,
                    'license' => $video->how_use_image,
                    'license_title' => $video->how_use_image ? __("global.$video->how_use_image") : 'NONE',
                    'original_name' => $video->original_name,
                    'stage_edit' => $video->stage_edit,
                    'tags_ar' => $video->tags_ar,
                    'tags_en' => $video->tags_en,
                    'category_ids' => $video->category_ids,
                    'computer_vision_tags_ar' => $video->computer_vision_tags_ar,
                    'computer_vision_tags_en' => $video->computer_vision_tags_en,
                    'category_admin_id' => $video->category_admin_id,
                    'post_link' => $video->post_link,
                    'release_ids' => $video->release_ids

                ]);
            })
            ->values();

        return [
            'meta' => [
                'page' => $page,
                'perpage' => $perpage,
                'total' => $count,
            ],
            'data' => $data,
        ];
    }

    public function update_multi(Request $request)
    {


        // TODO fix js request
        // TODO check auth here
        $request_params = json_decode(file_get_contents('php://input'), true);
        $auth_id =  Auth::id();
        $options = $request_params['options'];
        $data = collect($request_params['data'])->map(function ($item) {
            $all = [];
            foreach ($item as $key => $value) {
                $all = array_merge($all, $item[$key]);
            }
            return $all;
        });

        $video_ids = $data->keys()->values();
        $changed_slugs = [];
        $videosChanges = $data
            ->map(function ($item,$id) use ($changed_slugs)  {
                $new = [];
                if (isset($item['title_ar'])) {
                    $new['title_ar'] = $item['title_ar'];
                }
                if (isset($item['title_en'])) {
                    $new['title_en'] = $item['title_en'];
                    $changed_slugs[] = $id;
                }
                if (isset($item['description_ar'])) {
                    $new['description_ar'] = $item['description_ar'];
                }
                if (isset($item['description_en'])) {
                    $new['description_en'] = $item['description_en'];
                }
                if (isset($item['category_admin_id'])) {
                    $new['category_admin_id'] = $item['category_admin_id'];
                }
                if (isset($item['status'])) {
                    $new['status'] = $item['status'];
                }
                // if (isset($item['stage_edit'])) {
                //     $new['stage_edit'] = $item['stage_edit'];
                // }

                if (isset($item['how_use_image'])) {
                    $new['how_use_image'] = $item['how_use_image'];
                }

                return $new;
            })
            ->filter(function ($item) {
                return count($item);
            });

        $tagsArChanges = $data
            ->map(function ($item) {
                $new = [];
                if (isset($item['tags_ar'])) {
                    $new['tags_ar'] = $item['tags_ar'];
                }
                return $new;
            })
            ->filter(function ($item) {
                return count($item);
            });

        $tagsEnChanges = $data
            ->map(function ($item) {
                $new = [];
                if (isset($item['tags_en'])) {
                    $new['tags_en'] = $item['tags_en'];
                }
                return $new;
            })
            ->filter(function ($item) {
                return count($item);
            });

        $categoriesChanges = $data
            ->map(function ($item) {
                $new = [];
                if (isset($item['category_ids'])) {
                    $new['category_ids'] = $item['category_ids'];
                }
                return $new;
            })
            ->filter(function ($item) {
                return count($item);
            });

            DB::beginTransaction();
            try {
                //code...
                // process changes
                if (count($videosChanges)) {
                    $raw_query = "";
                    foreach ($videosChanges as $id => $params) {
                        $column_string = [];
                        foreach ($params as $column => $value) {
                            $value = str_replace("'", "\'", $value);
                            $column_string[] = "`$column` = '$value'";
                        }
                        $raw_query .= "UPDATE `videos` SET" . implode(',', $column_string) . " WHERE `videos`.`id` = $id;";
                    }

                    DB::unprepared($raw_query);
                }
                $videosChangeArTags = Video::with('tags')->whereIn('id', $tagsArChanges->keys()->toArray())->get()->keyBy('id');
                $videosChangeEnTags = Video::with('tags')->whereIn('id', $tagsEnChanges->keys()->toArray())->get()->keyBy('id');

                if (count($tagsArChanges)) {
                    foreach ($tagsArChanges as $id => $tags) {
                        $video = $videosChangeArTags->get($id);
                        // dd($video->tags()->where('local','ar')->get());
                        if ($options['tags_ar_delete_old']) {
                            $tags_id = $video->tags()->where('local', 'ar')->pluck('id')->toArray();

                            if(count($tags_id))
                            $video->tags()->detach($tags_id);
                        }
                        if (count($tags['tags_ar']))
                            sync_tags($video, $tags['tags_ar'], 'ar');
                    }
                }
                if (count($tagsEnChanges)) {
                    foreach ($tagsEnChanges as $id => $tags) {
                        $video = $videosChangeEnTags->get($id);
                        if ($options['tags_en_delete_old']) {
                            $tags_id = $video->tags()->where('local', 'en')->pluck('id')->toArray();

                            if(count($tags_id))
                            $video->tags()->detach($tags_id);
                        }
                        if (count($tags['tags_en']))
                            sync_tags($video, $tags['tags_en'], 'en');
                    }
                }

                if (count($categoriesChanges)) {
                    $video_ids = $categoriesChanges->keys();
                    if ($options['category_ids_delete_old']) {
                        CategoryVideo::whereIn('video_id', $video_ids)->delete();
                    }

                    $raw_query = "INSERT INTO `category_video` (`video_id`, `category_id`) VALUES ";
                    $column_string = [];
                    foreach ($categoriesChanges as $id => $params) {
                        foreach ($params as $categories) {
                            foreach ($categories as $category_id) {
                                $column_string[] = " ($id, '$category_id')";
                            }
                        }
                    }

                    if (count($column_string)) {
                        $raw_query .= implode(',', $column_string) . ";";
                        DB::unprepared($raw_query);
                    }
                }

                $this->add_slug_for_videos();
                /* s:seovideos */
                foreach ($changed_slugs as $id)
                     dispatch(new \App\Jobs\SeoVideos($id));
                foreach ($data->keys()->values() as $id) {
                cache()->delete("video_show_{$id}_ar");
                cache()->delete("video_show_{$id}_en");
                }
                /* e:seovideos */
            DB::commit();
            $all_ids = $data->keys()->values();
            dispatch(new \App\Jobs\UpdateStageEditVideo($all_ids->toArray()));

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            $msg = "msg:tags auth:{$auth_id} msg:{$th->getMessage()} File: {$th->getFile()}  Line: {$th->getLine()} ";
            Log::error($msg);
            $exception = [
                'success' => false,
                'message' => 'Validation Errors',
                'code' => 2,
                'errors' => ['message' => __("validation.no_update_warehouse_check_data")],
            ];
            throw new HttpResponseException(response()->json($exception, 422));
        }
        return [];
    }

    public function add_slug_for_videos()
    {
        $data = DB::select("
            SELECT id, title_en FROM `videos`  where slug ='' and status='active' and title_en NOT REGEXP '[0-9]{10}.*'
            ");

        $keywords = array('video', 'videos', 'clip', 'clips');

        foreach ($data as $item) {
            $slug = updateSlug($keywords, slugify_v2($item->title_en));

            $first_id = DB::table('videos')
                ->where('id', $item->id)
                ->update(['slug' => 'clip-' . $item->id . '-' . $slug]);
        }
    }


    public function Videoshow($id, $slug = null)
    {
        // TODO move to headers and middleware

        $user_id = 0;
        if (Auth::check()) {
            $user_id = Auth::id();
        }


        $response = Video::withoutGlobalScope('reserved')->with('category')
            ->where('videos.video_fail', 0)
            ->where('status', 'active')->select('videos.*')
            ->leftJoin('collection_video', 'collection_video.video_id', '=', 'videos.id')
            ->leftJoin('video_collections', 'collection_video.collection_id', '=', 'video_collections.id')
            ->whereRaw('(( video_collections.type="public" and videos.user_id != ' . $user_id . ') or (videos.user_id = ' . $user_id . ') or (videos.id not in (select video_id from collection_video)) or (videos.id  in (select video_id from video_downloads)))')
            ->where('videos.id', $id)
            ->whereNull('videos.parent_id')
            ->firstOrFail();

        $child_ids = $response->child->pluck('id');

        return [
            'items' => $response,
        ];
    }
}
