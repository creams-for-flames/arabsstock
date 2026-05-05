<?php

namespace App\Http\Controllers\api;

use App\Jobs\SeoImages;
use App\Models\CategoryImage;
use App\Models\Collection;
use App\Models\AdminCollectionImage;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\ImageCollection;
use App\Models\ImageFolder;
use App\Models\ImageLike;
use App\Models\ImagePlan;
use App\Models\VisitImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ImagesResource;
use App\Jobs\RemoveBgImage;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminCollection;
use App\Models\CategoryAdmin;
use App\Models\Contributor;
use App\Models\RejectionReason;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function getImages(Request $request)
    {
        $images = Image::where('id', '>', 0);

        if (($request->get('status') || $request->get('status') == "0") && $request->get('status') !== "00") {
            $images->where('stage_edit', $request->get('status'));
        }
        if ($request->get('admin_categories') && $request->get('admin_categories') != '0') {
            $images->where('category_admin_id', $request->get('admin_categories'));
        }

        $images = ImagesResource::collection($images->paginate(10));
        return $images;
    }

    public function getAdminCategories()
    {
        $categories = \App\Models\CategoryAdmin::paginate(10);

        return $categories;
    }

    public function getCategories(Request $request)
    {
        $categories = \App\Models\ImageCategory::query();
        if ($request->get('ids')) {
            $categories
                ->join('category_image', 'image_categories.id', '=', 'category_image.category_id')
                ->leftJoin('images', 'category_image.image_id', '=', 'images.id')
                ->whereIn('images.id', $request->get('ids'))
                ->select('category_image.category_id as id_cat', 'images.*');
        }
        $categories = $categories->get();
        return $categories;
    }

    public function Imageshow($id, $slug = null)
    {
        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);

        $response = Image::where('id', $id)->with('category')->withoutGlobalScopes(['default_loaded_relations', 'reserved', 'not_deleted'])->firstOrFail();

        $user_IP = request()->ip();
        $date = time();

        if (Auth::check()) {
            $visitCheckUser = $response
                ->visits()
                ->where('user_id', Auth::user()->id)
                ->first();

            if (!$visitCheckUser && Auth::user()->id != $response->user->id) {
                $visit = new VisitImage();
                $visit->image_id = $response->id;
                $visit->user_id = Auth::user()->id;
                $visit->ip = $user_IP;
                $visit->save();
            }
        } else {
            $visitCheckGuest = $response
                ->visits()
                ->where('user_id', 0)
                ->where('ip', $user_IP)
                ->orderBy('date', 'desc')
                ->first();

            if ($visitCheckGuest) {
                $dateGuest = strtotime($visitCheckGuest->date) + 7200; // 2 Hours
            }

            if (empty($visitCheckGuest->ip)) {
                // $visit = new VisitImage();
                // $visit->image_id = $response->id;
                // $visit->user_id = 0;
                // $visit->ip = $user_IP;
                // $visit->save();
            } else {
                if ($dateGuest < $date) {
                    $visit = new VisitImage();
                    $visit->image_id = $response->id;
                    $visit->user_id = 0;
                    $visit->ip = $user_IP;
                    $visit->save();
                }
            }
        }

        $categories = CategoryImage::where('image_id', $id)->pluck('category_id');
        $categoriesItem = ImageCategory::whereIn('id', $categories->toArray())->get();

        return [
            'categories' => $categoriesItem,
        ];
    }

    public function options($id, $slug = null)
    {
        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);

        $category_admin = CategoryAdmin::where('id', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                ];
            });

        $categories = ImageCategory::query()
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
        $removebg = ['queue','processing','done'];
        $removebg = collect($removebg)->map(function($item){

            $license = new \stdClass();
            $license->value = $item;
            $license->title = __("global.{$item}");
            return $license;
        });
        $reasons_rejection = RejectionReason::query()
        ->where('status','active')
        ->where('category','images')
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
        $removebg = request()->get('removebg',0);
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);

        $status_list = [
            'all' => trans('views.all'),
            'complete' => trans('views.complete_edit'),
            'half_edit' => trans('views.half_edit'),
            'no_edit' => trans('views.no_edit'),
            'rejected' => trans('views.rejected'),
        ];

        $sort_list = [
            'updated_at' => trans('views.last_updated_at'),
            'id' => trans('views.first_created_at'),
        ];

        $folders = ImageFolder::pluck('folder', 'id')->toArray();

        $collections = AdminCollection::where('status', '1')->pluck('title', 'id');

        $category_admin = CategoryAdmin::where('id', '>', 0)->pluck('name', 'id');

        $categories = ImageCategory::get()->pluck('name', 'id');
        $categories[-1] = __('global.unclassified');

        $contributor = Contributor::has('created_images')->get()->pluck('name', 'id');

        $publisher_list = [
            'all' => trans('views.the_all'),
            'supervisor' => trans('views.supervisor'),
            'contributor' => trans('views.contributor'),
        ];
        $removebg_status = [
            'all' => trans('views.the_all'),
            'processing' => trans('global.processing'),
            'done' => trans('global.done'),
            'un_removebg' => trans('global.un_removebg'),
        ];
        $removebg_type = [
            'all' => trans('views.the_all'),
            'paid' => trans('views.invoice_paid'),
            'free' => trans('views.free'),
        ];
        $removebg_status_disply = [
            'all' => trans('views.the_all'),
            'pending' => trans('global.pending'),
            'active' => trans('global.active'),
        ];
        $role = "admin";
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
        ];
        if ($removebg) {
            $data[1]['removebg_status'] = ['data' => $removebg_status, 'type' => "select"];
        }
        if ($removebg) {
            $data[2]['removebg_type'] = ['data' => $removebg_type, 'type' => "select"];
            $data[2]['removebg_status_disply'] = ['data' => $removebg_status_disply, 'type' => "select"];
        }
        $data[2]['no_file'] = ['data' => [], 'type' => "input"];
        return response()->json(['filters'=>$data]);

    }

    public function index(Request $request)
    {
        $images = Image::with(['tags', 'category', 'computer_vision_tags', 'category_admin', 'contributor_file.contributor', 'contributor_file.release_image']);
        if ($request->has('no_file') and $request->get('no_file') != "") {
            $query = $request->get('no_file');
            if ((filter_var($query, FILTER_VALIDATE_INT) !== false)) {
                $images = $images->where('id', $query);
            }else{
                $ids = \App\Helper::search_in_elasticsearch_items('images', $query, [], 10000)['items'];
                $images = $images->whereIn('id', $ids);
                if (count($ids)) {
                    $ids_ordered = implode(',', $ids);
                  $images =   $images->orderBy(DB::raw("FIELD(id, $ids_ordered)"));
                }
            }
        }
        if ($request->get('publisher_type')) {
            $publisher_type = $request->get('publisher_type');
            switch ($publisher_type) {
                case 'supervisor':
                      $user_type ="App\Models\User";
                      $images->where('user_type', $user_type);
                    break;
                    case 'contributor':
                        $user_type ="App\Models\Contributor";
                        $images->where('user_type', $user_type);
                      break;

            }
        }
        if ($request->get('type')) {
            $type = $request->get('type');
            switch ($type) {
                case 'complete':
                    $images->where(function($query){
                        $query
                        ->doesntHave('contributor_file')
                        ->orWhereHas('contributor_file',function($q){
                            $q->where('contributor_stage',8);
                        });
                    })->where('stage_edit',2);
                    break;
                case 'half_edit':
                    $images->where(function($query){
                        $query
                        ->doesntHave('contributor_file')
                        ->orWhereHas('contributor_file',function($q){
                            $q->where('contributor_stage',8);
                        });
                    })->where('stage_edit',1);
                    break;
                case 'no_edit':
                    $images->where(function($query){
                        $query
                        ->doesntHave('contributor_file')
                        ->orWhereHas('contributor_file',function($q){
                            $q->where('contributor_stage',8);
                        });
                    })->where('stage_edit',0);
                    break;
                case 'rejected':
                    $images->whereHas('contributor_file',function($q){
                        $q->whereIn('contributor_stage',[3,4]);
                    });
                    break;

            }
        }

        if ($request->get('categories_admin')) {
            $images->where('category_admin_id', $request->get('categories_admin'));
        }

        if ($request->get('categories')) {
            if ($request->get('categories') == -1) {
                $images->doesntHave('category');
            } else {
                $images->whereIn('id', function ($q) {
                    $q->select('image_id')->from('category_image')->where('category_id', request()->get('categories'));
                });

            }
        }


        if ($request->get('collection')) {
            $images_ids = AdminCollectionImage::where('admin_collection_id', $request->get('collection'))
                ->pluck('image_id')
                ->toArray();
            $images->whereIn('id', $images_ids);
        }

        if ($request->get('folder')) {
            $images->where('folder_id', $request->get('folder'));
        }

        if ($request->get('contributor') && $request->get('publisher')) {
            $users_id = [$request->get('contributor'),$request->get('publisher')];
            $images->whereIn('user_id', $users_id);
        }else{
            if ($request->get('contributor')) {
                $images->where('user_id', $request->get('contributor'));
            }
            if ($request->get('publisher')) {
                $images->where('user_id', $request->get('publisher'));
            }

        }

        $removebg_status_list = [
            'processing',
            'done'
        ];
        $removebg_status = $request->get('removebg_status');
        if ($removebg_status && in_array($removebg_status ,$removebg_status_list )  )
        {
            $images->where('removebg_status', $removebg_status);
        }else if($removebg_status and $removebg_status === 'un_removebg'){
            $images->whereNull('removebg_status');

        }

        $removebg_type_list = [
            'free',
            'paid'
        ];
        $removebg_type = $request->get('removebg_type');
        if ($removebg_type && in_array($removebg_type ,$removebg_type_list )  )
        {
            $images->where('removebg_type', $removebg_type);
        }

        $removebg_status_disply_list = [
            'active',
            'pending'
        ];
        $removebg_status_disply = $request->get('removebg_status_disply');
        if ($removebg_status_disply && in_array($removebg_status_disply ,$removebg_status_disply_list )  )
        {
            $images->where('removebg_status_disply', $removebg_status_disply);
        }

        if ($removebg_status)
        $images->orderBy('removebg_created_at', 'desc');
        else

        $images->whereDoesntHave('contributor_file',function($q){
            $q->where('contributor_stage','=',4);
        });

        $images->orderBy($request->get('sort_by', 'id'), 'desc');


        $page = $request->get('page');
        $perpage = $request->get('perpage');

        $offset = ($page - 1) * $perpage;
        $count = $images->count();
        $images->offset($offset)->limit($perpage);

        $lang = app()->getLocale();
        $data = $images
            ->get()
            ->map(function ($image) {
                $image->tags_ar = $image->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'ar';
                    })
                    ->pluck('title');
                return $image;
            })
            ->map(function ($image) {
                $image->tags_en = $image->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'en';
                    })
                    ->pluck('title');
                return $image;
            })
            ->map(function ($image) use ($lang) {
                $image->category_ids = $image->category->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->id,
                        'label' => $item->{"name_" . $lang},
                    ];
                });
                return $image;
            })
            ->map(function ($image) use ($lang) {
                if ($image->category_admin) {
                    $image->category_admin_id = [
                        'id' => $image->category_admin->id,
                        'label' => $image->category_admin->name,
                    ];
                }
                return $image;
            })
            ->map(function ($image) {
                $image->computer_vision_tags_ar = $image->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_ar,
                        'label' => $item->tag_ar . $confidence,
                    ];
                });
                $image->computer_vision_tags_en = $image->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_en,
                        'label' => $item->tag_en . $confidence,
                    ];
                });
                return $image;
            })
            ->map(function ($image) {
                $image->release_ids = [];
                if ($image->contributor_image_id !== 0 and $image->contributor_file()->has('release_image')) {
                    $image->release_ids = optional(optional($image->contributor_file)->release_image)->map(function ($item) {
                        return [
                            'id' => $item->release_id,
                            'label' => $item->release->name,
                            'file' => url($item->release->file),
                        ];
                    });
                }
                return $image;
            })
            ->map(function ($image) {
                $status_contributor_file_lable = NULL;
                $status_contributor_file = NULL;

                if (isset($image->contributor_file)) {
                    if ($image->contributor_file->contributor_stage == 3) {
                        $status_contributor_file_lable = __('views.rejected');
                        $status_contributor_file = 'rejected';

                    }

                    if ($image->contributor_file->contributor_stage == 4) {
                        $status_contributor_file_lable = __('views.hard_rejected');
                        $status_contributor_file = 'hard_rejected';

                    }
                }
                // $querys = ['اشخاص','أشخاص','شخص','بورتريه'];
                // foreach ($querys as $key => $value) {
                //     $removebg_can = (fnmatch("*{$value}*","{$image->title_ar}")
                //     || in_array($value, $image->tags_ar->toArray()));
                //     if($removebg_can)
                //     break;
                // }
                $removebg_status = NULL;
                if ($image->removebg_status) {
                    $removebg_status = new \stdClass();
                    $removebg_status->value = $image->removebg_status;
                    $removebg_status->title = __("global.{$image->removebg_status}");
                }

                return collect([
                    'id' => $image->id,
                    'owner' => $image->contributor_file->contributor->name ?? 'admin',
                    'status_contributor_file_lable' => $status_contributor_file_lable,
                    'status_contributor_file' => $status_contributor_file,
                    'thumbnail' => cdn($image->thumbnail),
                    'height_thumbnail' => $image->height_thumbnail,
                    'width_thumbnail' => $image->width_thumbnail,
                    'preview' => $image->preview,
                    'post_link' => $image->post_link,
                    'height_preview' => $image->height_preview,
                    'width_preview' => $image->width_preview,
                    'title_ar' => $image->title_ar,
                    'title_en' => $image->title_en,
                    "removebg_can"=>"disabled",
                    'removebg_status'=> $image->removebg_status?$removebg_status:'',
                    'removebg_watermark'=>isset($image->removebg_image)?cdn($image->removebg_image):'',
                    'removebg_status_disply'=>$image->removebg_status_disply,
                    'removebg_type'=>$image->removebg_type,
                    'description_ar' => $image->description_ar,
                    'description_en' => $image->description_en,
                    'status' => $image->status,
                    'license' => $image->how_use_image,
                    'license_title' => $image->how_use_image ? __("global.$image->how_use_image") : 'NONE',
                    'original_name' => $image->original_name,
                    'stage_edit' => $image->stage_edit,
                    'tags_ar' => $image->tags_ar,
                    'tags_en' => $image->tags_en,
                    'category_ids' => $image->category_ids,
                    'computer_vision_tags_ar' => $image->computer_vision_tags_ar,
                    'computer_vision_tags_en' => $image->computer_vision_tags_en,
                    'release_ids' => $image->release_ids
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
        $auth_id = Auth::id();
        $options = $request_params['options'];
        $data = collect($request_params['data'])->map(function ($item) {
            $all = [];
            foreach ($item as $key => $value) {
                $all = array_merge($all, $item[$key]);
            }
            return $all;
        });

        $image_ids = $data->keys()->values();
        $changed_slugs = [];
        $imagesChanges = $data
            ->map(function ($item, $id) use (&$changed_slugs) {
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
                if (isset($item['how_use_image'])) {
                    $new['how_use_image'] = $item['how_use_image'];
                }
                // if (isset($item['stage_edit'])) {
                //     $new['stage_edit'] = $item['stage_edit'];
                // }

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
            // process changes
            if (count($imagesChanges)) {
                $raw_query = "";
                foreach ($imagesChanges as $id => $params) {
                    $column_string = [];
                    $params = ['updated_at' => now()->format('Y-m-d H:i:s')];
                    foreach ($params as $column => $value) {
                        $value = str_replace("'", "\'", $value);
                        $column_string[] = "`$column` = '$value'";
                    }
                    $raw_query .= "UPDATE `images` SET" . implode(',', $column_string) . " WHERE `images`.`id` = $id;";
                }

                DB::unprepared($raw_query);
            }

            if (count($imagesChanges)) {
                $raw_query = "";
                foreach ($imagesChanges as $id => $params) {
                    $column_string = [];
                    foreach ($params as $column => $value) {
                        $value = str_replace("'", "\'", $value);
                        $column_string[] = "`$column` = '$value'";
                    }
                    $raw_query .= "UPDATE `images` SET" . implode(',', $column_string) . " WHERE `images`.`id` = $id;";
                }

                DB::unprepared($raw_query);
            }
            $imagesChangeArTags = Image::with('tags')->whereIn('id', $tagsArChanges->keys()->toArray())->get()->keyBy('id');
            $imagesChangeEnTags = Image::with('tags')->whereIn('id', $tagsEnChanges->keys()->toArray())->get()->keyBy('id');
            if (count($tagsArChanges)) {
                foreach ($tagsArChanges as $id => $tags) {
                    $image = $imagesChangeArTags->get($id);
                    if ($options['tags_ar_delete_old']) {
                        $tags_id = $image->tags()->where('local', 'ar')->pluck('id')->toArray();

                        if (count($tags_id))
                            $image->tags()->detach($tags_id);

                    }
                    if (count($tags['tags_ar']))
                        sync_tags($image, $tags['tags_ar'], 'ar');
                }
            }
            if (count($tagsEnChanges)) {
                foreach ($tagsEnChanges as $id => $tags) {
                    $image = $imagesChangeEnTags->get($id);
                    if ($options['tags_en_delete_old']) {
                        $tags_id = $image->tags()->where('local', 'en')->pluck('id')->toArray();

                        if (count($tags_id))
                            $image->tags()->detach($tags_id);

                    }
                    if (count($tags['tags_en']))
                        sync_tags($image, $tags['tags_en'], 'en');
                }
            }

            if (count($categoriesChanges)) {
                $image_ids = $categoriesChanges->keys();
                if ($options['category_ids_delete_old']) {
                    CategoryImage::whereIn('image_id', $image_ids)->delete();
                }

                $raw_query = "INSERT INTO `category_image` (`image_id`, `category_id`) VALUES ";
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

            //        $this->delete_duplicated_tags();
            $this->add_slug_for_images();
            foreach ($changed_slugs as $id)
                dispatch(new SeoImages($id));
            foreach ($data->keys()->values() as $id) {
                cache()->delete("image_show_{$id}_ar");
                cache()->delete("image_show_{$id}_en");
            }
            DB::commit();
            $all_ids = $data->keys()->values();
            dispatch(new \App\Jobs\UpdateStageEditImage($all_ids->toArray()));

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

    public function add_slug_for_images()
    {
        // exclude auto generated titles
        $data = DB::select("
            SELECT id, title_en FROM `images`  where slug ='' and status='active' and title_en NOT REGEXP '[0-9]{10}.*'
            ");

        $keywords = array('images', 'image', 'photo', 'photos');
        foreach ($data as $item) {
            $slug = updateSlug($keywords, slugify_v2($item->title_en));
            $first_id = DB::table('images')
                ->where('id', $item->id)
                ->update(['slug' => 'image-' . $item->id . '-' . $slug]);
        }
    }

    //go to app/Console/Kernel.php
//    public function delete_duplicated_tags()
//    {
//      go to app/Console/Kernel.php
//    }

    public function indexReview(Request $request)
    {
        $images = Image::with(['tags', 'category', 'computer_vision_tags', 'category_admin'])
            ->where('is_contributor', '1');

        if ($request->get('type')) {
            if ($request->get('type') == 'complete') {
                $images->where('stage_edit', 2);
            } elseif ($request->get('type') == 'half_edit') {
                $images->where('stage_edit', 1);
            } elseif ($request->get('type') == 'no_edit') {
                $images->where('stage_edit', 0);
            }
        }

        if ($request->get('categories_admin')) {
            $images->where('category_admin_id', $request->get('categories_admin'));
        }

        if ($request->get('categories')) {
            $images->whereIn('id', function ($q) {
                $q->select('image_id')->from('category_image')->where('category_id', request()->get('categories'));
            });
        }


        if ($request->get('collection')) {
            $images_ids = AdminCollectionImage::where('admin_collection_id', $request->get('collection'))
                ->pluck('image_id')
                ->toArray();
            $images->whereIn('id', $images_ids);
        }

        if ($request->get('folder')) {
            $images->where('folder_id', $request->get('folder'));
        }
        $images->orderBy($request->get('sort_by'), 'desc');

        $page = $request->get('page');
        $perpage = $request->get('perpage');

        $offset = ($page - 1) * $perpage;
        $count = $images->count();
        $images->offset($offset)->limit($perpage);

        $lang = app()->getLocale();
        $data = $images
            ->get()
            ->map(function ($image) {
                $image->tags_ar = $image->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'ar';
                    })
                    ->pluck('title');
                return $image;
            })
            ->map(function ($image) {
                $image->tags_en = $image->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'en';
                    })
                    ->pluck('title');
                return $image;
            })
            ->map(function ($image) use ($lang) {
                $image->category_ids = $image->category->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->id,
                        'label' => $item->{"name_" . $lang},
                    ];
                });
                return $image;
            })
            ->map(function ($image) use ($lang) {
                if ($image->category_admin) {
                    $image->category_admin_id = [
                        'id' => $image->category_admin->id,
                        'label' => $image->category_admin->name,
                    ];
                }
                return $image;
            })
            ->map(function ($image) {
                $image->computer_vision_tags_ar = $image->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_ar,
                        'label' => $item->tag_ar . $confidence,
                    ];
                });
                $image->computer_vision_tags_en = $image->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_en,
                        'label' => $item->tag_en . $confidence,
                    ];
                });
                return $image;
            })
            ->map(function ($image) {
                return collect([
                    'id' => $image->id,
                    'thumbnail' => $image->thumbnail,
                    'height_thumbnail' => $image->height_thumbnail,
                    'width_thumbnail' => $image->width_thumbnail,
                    'preview' => $image->preview,
                    'height_preview' => $image->height_preview,
                    'width_preview' => $image->width_preview,
                    'title_ar' => $image->title_ar,
                    'title_en' => $image->title_en,
                    'description_ar' => $image->description_ar,
                    'description_en' => $image->description_en,
                    'status' => $image->status,
                    'original_name' => $image->original_name,
                    'stage_edit' => $image->stage_edit,
                    'tags_ar' => $image->tags_ar,
                    'tags_en' => $image->tags_en,
                    'category_ids' => $image->category_ids,
                    'computer_vision_tags_ar' => $image->computer_vision_tags_ar,
                    'computer_vision_tags_en' => $image->computer_vision_tags_en,
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

    public function filtersReview($id, $slug = null)
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
        ];

        $sort_list = [
            'updated_at' => trans('views.last_updated_at'),
            'id' => trans('views.first_created_at'),
        ];

        $folders = ImageFolder::pluck('folder', 'id')->toArray();

        $collections = AdminCollection::where('status', '1')->pluck('title', 'id');

        $category_admin = CategoryAdmin::where('id', '>', 0)->pluck('name', 'id');

        $categories = ImageCategory::get()->pluck('name', 'id');

        return [
            'filters' => [
                'type' => ['data' => $status_list, 'type' => "select"],
                'categories_admin' => ['data' => $category_admin, 'type' => "select"],
                'categories' => ['data' => $categories, 'type' => "select"],
                'collection' => ['data' => $collections, 'type' => "select"],
                'folder' => ['data' => $folders, 'type' => "select"],
                'sort_by' => ['data' => $sort_list, 'type' => "select"],
            ],
        ];
    }

    public function update_multi_remove_bg(Request $request)
    {

        $request_params = json_decode(file_get_contents('php://input'), true);
        $removebg_status = "queue";
        $check_removebg_status_disply = Image::where(function($q) use($removebg_status){
            $q->where('removebg_status','!=',$removebg_status)
            ->orWhereNull('removebg_status');

        })->whereIn('id',$request_params['data']);
        $check_removebg_status_disply = $check_removebg_status_disply->where('removebg_status_disply' , 'active')->count();
        if ($check_removebg_status_disply) {
            $exception = [
                'success' => false,
                'message' => __("validation.Sorry_previously_approved_images_cannot_be_remove_background"),
                'code' => 2,
                'errors' => ['message' => 'Validation Errors'],
            ];
            throw new HttpResponseException(response()->json($exception, 422));
        }

        $images = Image::where(function($q) use($removebg_status){
            $q->where('removebg_status','!=',$removebg_status)
            ->orWhereNull('removebg_status');

        })->whereIn('id',$request_params['data'])->get();
        $removebg_type = $request_params['removebg_type'];
        foreach ($images as $key => $image) {
            dispatch(new RemoveBgImage($image->id,$removebg_type))->onConnection('media');//->onQueue('vector');
            $image->removebg_type = $removebg_type;
            $image->removebg_status = $removebg_status;
            $image->save();
        }


        return [];
    }

    public function update_multi_remove_bg_display(Request $request)
    {


        $request_params = json_decode(file_get_contents('php://input'), true);
        $display = $request_params['display'];
        $check_removebg = Image::where(function($q){
            $q->where('removebg_status','!=',"done")
            ->orWhereNull('removebg_status');
        })
        ->whereIn('id',$request_params['data'])->count();
        if ($check_removebg) {
            $exception = [
                'success' => false,
                'message' => __("validation.Sorry_all_files_must_be_without_background"),
                'code' => 2,
                'errors' => ['message' => 'Validation Errors'],
            ];
            throw new HttpResponseException(response()->json($exception, 422));
        }
         $images = Image::whereIn('id',$request_params['data'])->update(['removebg_status_disply'=>$display]);

        return [];
    }
}
