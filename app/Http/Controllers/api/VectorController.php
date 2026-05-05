<?php

namespace App\Http\Controllers\api;

use App\Models\CategoryVector;
use App\Models\Collection;
use App\Models\AdminCollectionVector;
use App\Models\VectorCollection;
use App\Models\VectorFolder;
use App\Models\VectorLike;
use App\Models\VectorCategory;
use App\Models\VisitVector;
use App\Models\VectorPlan;
use App\Models\Vector;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminCollection;
use App\Models\Contributor;
use App\Models\RejectionReason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VectorController extends Controller
{
    public function options($id, $slug = null)
    {


        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);

        $category_admin = DB::table('category_admins_vectors')->where('id', '>', 0)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'label' => $item->name,
            ];
        });


        // return $category_admin;

        $categories = VectorCategory::query()
            ->orderBy('sort')
            ->orderBy('name_en')
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->{"name_" . $lang},
                ];
            });
        $reasons_rejection = RejectionReason::query()
            ->where('status', 'active')
            ->where('category', 'vectors')
            ->orderBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => "{$item->title} -" . __('admin.' . $item->type),
                    'description' => "{$item->description_ar} \n {$item->description_en} . ",
                ];
            });
        return [
            'options' => [
                'categories_admin' => $category_admin,
                'categories' => $categories,
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
            'rejected' => trans('views.rejected'),
        ];

        $sort_list = [
            'updated_at' => trans('views.last_updated_at'),
            'id' => trans('views.first_created_at'),
        ];

        $folders = VectorFolder::pluck('folder', 'id')->toArray();

        $collections = AdminCollection::where('status', '1')->pluck('title', 'id');

        $category_admin = DB::table('category_admins_vectors')->where('id', '>', 0)->get()->pluck('name', 'id');

        $categories = VectorCategory::get()->pluck('name', 'id');
        $categories[-1] = __('global.unclassified');

        $contributor = Contributor::has('created_vectors')->get()->pluck('name', 'id');

        $publisher_list = [
            'all' => trans('views.the_all'),
            'supervisor' => trans('views.supervisor'),
            'contributor' => trans('views.contributor'),
        ];
        $role = "admin_vector";
        $publishers = DB::table("users")->where('role', $role)->get()->pluck('name', 'id');
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

        return response()->json(['filters' => $data]);

    }

    public function index(Request $request)
    {


        $vectors = Vector::with(['tags', 'category', 'computer_vision_tags', 'contributor_file.contributor']);

        if ($request->has('search') and $request->get('search') != "") {
            $query = $request->get('search');
            if ((filter_var($query, FILTER_VALIDATE_INT) !== false)) {
                $vectors = $vectors->where('id', $query);
            } else {
                $ids = \App\Helper::search_in_elasticsearch_items('vectors', $query, [], 10000)['items'];
                $vectors = $vectors->whereIn('id', $ids);

                if (count($ids)) {
                    $ids_ordered = implode(',', $ids);
                    $vectors = $vectors->orderBy(DB::raw("FIELD(id, $ids_ordered)"));
                }
            }
        }
        if ($request->get('publisher_type')) {
            $publisher_type = $request->get('publisher_type');
            switch ($publisher_type) {
                case 'supervisor':
                    $user_type = "App\Models\User";
                    $vectors->where('user_type', $user_type);
                    break;
                case 'contributor':
                    $user_type = "App\Models\Contributor";
                    $vectors->where('user_type', $user_type);
                    break;

            }
        }
        if ($request->get('type')) {
            $type = $request->get('type');
            switch ($type) {
                case 'complete':
                    $vectors->where(function ($query) {
                        $query
                            ->doesntHave('contributor_file')
                            ->orWhereHas('contributor_file', function ($q) {
                                $q->where('contributor_stage', 8);
                            });
                    })->where('stage_edit', 2);
                    break;
                case 'half_edit':
                    $vectors->where(function ($query) {
                        $query
                            ->doesntHave('contributor_file')
                            ->orWhereHas('contributor_file', function ($q) {
                                $q->where('contributor_stage', 8);
                            });
                    })->where('stage_edit', 1);
                    break;
                case 'no_edit':
                    $vectors->where(function ($query) {
                        $query
                            ->doesntHave('contributor_file')
                            ->orWhereHas('contributor_file', function ($q) {
                                $q->where('contributor_stage', 8);
                            });
                    })->where('stage_edit', 0);
                    break;
                case 'rejected':
                    $vectors->whereHas('contributor_file', function ($q) {
                        $q->whereIn('contributor_stage', [3, 4]);
                    });
                    break;

            }
        }

        if ($request->get('categories_admin')) {
            $vectors->where('category_admin_id', $request->get('categories_admin'));
        }

        if ($request->get('categories')) {
            if ($request->get('categories') == -1) {
                $vectors->doesntHave('category');
            } else {
                $vectors->whereIn('id', function ($q) {
                    $q->select('vector_id')->from('category_vector')->where('category_id', request()->get('categories'));
                });
            }
        }

        if ($request->get('collection')) {
            $vectors_ids = AdminCollectionVector::where('admin_collection_id', $request->get('collection'))
                ->pluck('vector_id')
                ->toArray();
            $vectors->whereIn('id', $vectors_ids);
        }

        if ($request->get('folder')) {
            $vectors->where('folder_id', $request->get('folder'));
        }
        if ($request->get('contributor') && $request->get('publisher')) {
            $users_id = [$request->get('contributor'), $request->get('publisher')];
            $vectors->whereIn('user_id', $users_id);
        } else {
            if ($request->get('contributor')) {
                $vectors->where('user_id', $request->get('contributor'));
            }
            if ($request->get('publisher')) {
                $vectors->where('user_id', $request->get('publisher'));
            }

        }

        $vectors->whereDoesntHave('contributor_file', function ($q) {
            $q->where('contributor_stage', '=', 4);
        });

        $vectors->orderBy($request->get('sort_by', 'id'), 'desc');


        $page = $request->get('page');
        $perpage = $request->get('perpage');

        $offset = ($page - 1) * $perpage;
        $count = $vectors->count();
        $vectors->offset($offset)->limit($perpage);

        $lang = app()->getLocale();
        $data = $vectors
            ->get()
            ->map(function ($vector) {
                $vector->tags_ar = $vector->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'ar';
                    })
                    ->pluck('title');
                return $vector;
            })
            ->map(function ($vector) {
                $vector->tags_en = $vector->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'en';
                    })
                    ->pluck('title');
                return $vector;
            })
            ->map(function ($vector) use ($lang) {
                $vector->category_ids = $vector->category->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->id,
                        'label' => $item->{"name_" . $lang},
                    ];
                });
                return $vector;
            })
            ->map(function ($vector) use ($lang) {
                if ($vector->category_admin) {
                    $vector->category_admin_id = [
                        'id' => $vector->category_admin->id,
                        'label' => $vector->category_admin->name,
                    ];
                }
                return $vector;
            })
            ->map(function ($vector) {
                $vector->computer_vision_tags_ar = $vector->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_ar,
                        'label' => $item->tag_ar . $confidence,
                    ];
                });
                $vector->computer_vision_tags_en = $vector->computer_vision_tags->map(function ($item) {
                    $confidence = $item->confidence === "0.00" ? "" : " - " . $item->confidence . "%";

                    return [
                        'id' => $item->id,
                        'value' => $item->tag_en,
                        'label' => $item->tag_en . $confidence,
                    ];
                });
                return $vector;
            })
            ->map(function ($vector) {
                $status_contributor_file_lable = NULL;
                $status_contributor_file = NULL;

                if (isset($vector->contributor_file)) {
                    if ($vector->contributor_file->contributor_stage == 3) {
                        $status_contributor_file_lable = __('views.rejected');
                        $status_contributor_file = 'rejected';

                    }

                    if ($vector->contributor_file->contributor_stage == 4) {
                        $status_contributor_file_lable = __('views.hard_rejected');
                        $status_contributor_file = 'hard_rejected';

                    }
                }
                return collect([
                    'id' => $vector->id,
                    'owner' => $vector->contributor_file->contributor->name ?? 'admin',
                    'status_contributor_file_lable' => $status_contributor_file_lable,
                    'status_contributor_file' => $status_contributor_file,
                    'thumbnail' => $vector->thumbnail,
                    'height_thumbnail' => $vector->height_thumbnail,
                    'width_thumbnail' => $vector->width_thumbnail,
                    'preview' => $vector->preview,
                    'post_link' => $vector->post_link,
                    'height_preview' => $vector->height_preview,
                    'width_preview' => $vector->width_preview,
                    'title_ar' => $vector->title_ar,
                    'title_en' => $vector->title_en,
                    'description_ar' => $vector->description_ar,
                    'description_en' => $vector->description_en,
                    'status' => $vector->status,
                    'original_name' => $vector->original_name,
                    'stage_edit' => $vector->stage_edit,
                    'tags_ar' => $vector->tags_ar,
                    'tags_en' => $vector->tags_en,
                    'category_ids' => $vector->category_ids,
                    'computer_vision_tags_ar' => $vector->computer_vision_tags_ar,
                    'computer_vision_tags_en' => $vector->computer_vision_tags_en,
                    'category_admin_id' => $vector->category_admin_id,
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

        $vector_ids = $data->keys()->values();
        $changed_slugs = [];

        $vectorsChanges = $data
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
            //code...
            // process changes
            if (count($vectorsChanges)) {
                $raw_query = "";
                foreach ($vectorsChanges as $id => $params) {
                    $column_string = [];
                    foreach ($params as $column => $value) {
                        $value = str_replace("'", "\'", $value);
                        $column_string[] = "`$column` = '$value'";
                    }
                    $raw_query .= "UPDATE `vectors` SET" . implode(',', $column_string) . " WHERE `vectors`.`id` = $id;";
                }

                DB::unprepared($raw_query);
            }

            $vectorsChangeArTags = Vector::with('tags')->whereIn('id', $tagsArChanges->keys()->toArray())->get()->keyBy('id');
            $vectorsChangeEnTags = Vector::with('tags')->whereIn('id', $tagsEnChanges->keys()->toArray())->get()->keyBy('id');
            if (count($tagsArChanges)) {
                foreach ($tagsArChanges as $id => $tags) {
                    $vector = $vectorsChangeArTags->get($id);
                    if ($options['tags_ar_delete_old']) {
                        $tags_id = $vector->tags()->where('local', 'ar')->pluck('id')->toArray();

                        if (count($tags_id))
                            $vector->tags()->detach($tags_id);
                    }
                    if (count($tags['tags_ar']))
                        sync_tags($vector, $tags['tags_ar'], 'ar');
                }
            }
            if (count($tagsEnChanges)) {
                foreach ($tagsEnChanges as $id => $tags) {
                    $vector = $vectorsChangeEnTags->get($id);
                    if ($options['tags_en_delete_old']) {
                        $tags_id = $vector->tags()->where('local', 'en')->pluck('id')->toArray();

                        if (count($tags_id))
                            $vector->tags()->detach($tags_id);
                    }
                    if (count($tags['tags_en']))
                        sync_tags($vector, $tags['tags_en'], 'en');
                }
            }

            if (count($categoriesChanges)) {
                $vector_ids = $categoriesChanges->keys();
                if ($options['category_ids_delete_old']) {
                    CategoryVector::whereIn('vector_id', $vector_ids)->delete();
                }

                $raw_query = "INSERT INTO `category_vector` (`vector_id`, `category_id`) VALUES ";
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

            $this->add_slug_for_vectors();
            /* s:seovectors */
            foreach ($changed_slugs as $id)
                dispatch(new \App\Jobs\SeoVectors($id));
            foreach ($data->keys()->values() as $id) {
                cache()->delete("vector_show_{$id}_ar");
                cache()->delete("vector_show_{$id}_en");
            }
            /* e:seovectors */
            DB::commit();
            $all_ids = $data->keys()->values();
            dispatch(new \App\Jobs\UpdateStageEditVector($all_ids->toArray()));

        } catch (\Throwable $th) {
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

    public function add_slug_for_vectors()
    {
        $data = DB::select("
            SELECT id, title_en FROM `vectors`  where slug ='' and status='active' and title_en NOT REGEXP '[0-9]{10}.*'
            ");
        $keywords = array('vectors', 'victors', 'vector', 'victor', 'illustration', 'illustrator');
        foreach ($data as $item) {
            $slug = updateSlug($keywords, slugify_v2($item->title_en));
            $first_id = DB::table('vectors')
                ->where('id', $item->id)
                ->update(['slug' => 'illustration-' . $item->id . '-' . $slug]);
        }
    }

    public function Vectorshow($id, $slug = null)
    {
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);

        $vector = Vector::where('id', $id)->with('category')->withoutGlobalScopes(['default_loaded_relations', 'reserved', 'not_deleted'])->firstOrFail();

        $user_IP = request()->ip();
        $date = time();
        if (Auth::check()) {
            $visitCheckUser = $vector
                ->visits()
                ->where('user_id', Auth::user()->id)
                ->first();

            if (!$visitCheckUser && Auth::user()->id != @$vector->user->id) {
                $visit = new VisitVector();
                $visit->vector_id = $vector->id;
                $visit->user_id = Auth::user()->id;
                $visit->ip = $user_IP;
                $visit->save();
            }
        } else {

            $visitCheckGuest = $vector
                ->visits()
                ->where('user_id', 0)
                ->where('ip', $user_IP)
                ->orderBy('date', 'desc')
                ->first();

            if ($visitCheckGuest) {
                $dateGuest = strtotime($visitCheckGuest->date) + 7200; // 2 Hours
            }

            if (!isset($visitCheckGuest) || empty($visitCheckGuest->ip)) {
                $visit = new VisitVector();
                $visit->vector_id = $vector->id;
                $visit->user_id = 0;
                $visit->ip = $user_IP;
                $visit->save();
            } else {
                if ($dateGuest < $date) {
                    $visit = new VisitVector();
                    $visit->vector_id = $vector->id;
                    $visit->user_id = 0;
                    $visit->ip = $user_IP;
                    $visit->save();
                }
            }
        }

        return [
            'categories' => $vector->category,
        ];
        /* e */
    }


}
