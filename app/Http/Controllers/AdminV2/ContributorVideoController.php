<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\ContributorVideoSubmission;
use App\Models\ContributorVideoSubmissionItem;
use App\Models\LegalRelease;
use App\Models\ContributorVideo;
use App\Models\CategoryContributor;
use App\VideoTagContributor;
use Illuminate\Http\Request;
use App\CategoryContributorVideo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ContributorVideoLegalRelease;
use App\Helper;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContributorVideoController extends Controller
{
    public function index(Request $request)
    {
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        $user_id = request()->get('user_id');
        app()->setLocale($lang);
        $stage = intval($request->get('stage'));
        if ($stage === 1) {
            $contributor_stages = [0];
        } else if ($stage === 2) {
            $contributor_stages = [1, 2, 6];
        } else if ($stage === 3) {
            $contributor_stages = [3, 4, 5, 8];
        }
        $videos = ContributorVideo::with('file')->where('contributor_id', $user_id)->whereIn('contributor_stage', $contributor_stages);

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
                    ->pluck('tag');
                return $video;
            })
            ->map(function ($video) {
                $video->tags_en = $video->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'en';
                    })
                    ->pluck('tag');
                return $video;
            })
            ->map(function ($video) use ($lang) {
                $video->category_ids = $video->category->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->id,
                        'label' => $item->name,
                    ];
                });
                return $video;
            })
            ->map(function ($video) use ($lang) {
                $video->release_ids = $video->release_video->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->release_id,
                        'label' => $item->release->name,
                        'file' => url($item->release->file),
                    ];
                });
                return $video;
            })
            ->map(function ($video) {
                return collect([
                    'id' => $video->id,
                    'thumbnail' => $video->olde_video_id == null ?
                        ($video->is_uploaded ? "{$video->thumbnail}" : asset('img/default2.jpg'))
                        : $video->thumbnail,
                    'height_thumbnail' => $video->height_thumbnail,
                    'width_thumbnail' => $video->width_thumbnail,
                    'preview' => $video->is_uploaded ? $video->preview_admin : asset('img/default2.jpg'),
                    'title_ar' => $video->title_ar,
                    'title_en' => $video->title_en,
                    'status' => $video->status,
                    'license_title' => $video->license?__("global.{$video->license}"):'_',
                    'license' => $video->license,
                    'original_name' => $video->original_name,
                    'stage_edit' => $video->stage_edit,
                    'contributor_stage' => $video->contributor_stage,
                    'review_notes' => $video->review_notes,
                    'tags_ar' => $video->tags_ar,
                    'tags_en' => $video->tags_en,
                    'category_ids' => $video->category_ids,
                    'release_ids' => $video->release_ids,
                    'is_uploaded' => $video->is_uploaded ? __('Uploaded') : __('Uploading'),
                    'post_link' => $video->file && $video->contributor_stage === 8 ? $video->file->post_link : null,
                    'status_file_rejected_publish' => $video->file ? (($video->file->status === "pending" && \in_array($video->contributor_stage, [4, 3])) ? TRUE : FALSE) : FALSE,
                    'action_delete' => (!isset($video->file) && \in_array($video->contributor_stage, [0, 1, 3, 4])) ? TRUE : FALSE,
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

    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        $ids = explode(",", $ids);
        $user_id = request()->get('user_id');

        // make sure ids for current user only
        $ids = ContributorVideo::where('contributor_id', $user_id)
            ->whereIn('id', $ids)
            ->pluck('id');

        if (count($ids) === 0) {
            $exception = [
                'success' => false,
                'message' => 'Validation Errors',
                'code' => 2,
                'errors' => ['message' => __('views.can_not_delete')],
            ];
            throw new HttpResponseException(response()->json($exception, 422));
        }

        $videos = ContributorVideo::whereIn('id', $ids)->get();

        // delete from s3
        foreach ($videos as $video) {
            $orginal_path = $video->large;
            $thumbnail_path = $video->thumbnail;
            $preview_path = $video->preview_admin;

            if (\Storage::disk('s3')->exists($orginal_path))
            \Storage::disk('s3')->delete($orginal_path);

            if (\Storage::disk('s3')->exists($thumbnail_path))
            \Storage::disk('s3')->delete($thumbnail_path);

            if (\Storage::disk('s3')->exists($preview_path))
            \Storage::disk('s3')->delete($preview_path);
        }

        ContributorVideo::whereIn('id', $ids)->delete();

        VideoTagContributor::whereIn('video_id', $ids)->delete();

        CategoryContributorVideo::whereIn('video_id', $ids)->delete();
        ContributorVideoLegalRelease::whereIn('video_id', $ids)->delete();
        ContributorVideoSubmissionItem::whereIn('video_id', $ids)->delete();
        return [];
    }
    public function delete_all(Request $request)
    {
        $ids = $request->get('ids');
        $ids = explode(",", $ids);
        $user_id = request()->get('user_id');

        // make sure ids for current user only
        $ids = ContributorVideo::where('contributor_id', $user_id)
            ->whereIn('id', $ids)
            ->pluck('id');

        if (count($ids) === 0) {
            $exception = [
                'success' => false,
                'message' => 'Validation Errors',
                'code' => 2,
                'errors' => ['message' => __('views.can_not_delete')],
            ];
            throw new HttpResponseException(response()->json($exception, 422));
        }

        $videos = ContributorVideo::with('file')->whereIn('id', $ids)->get();

        // delete from s3
        foreach ($videos as $video) {
            $orginal_path = $video->large;
            $thumbnail_path = $video->thumbnail;
            $preview_path = $video->preview_admin;

            if($video->file()->exists())
            \dispatch(new \App\Jobs\DeleteVideo($video->id));//->onConnection('sync');
            else{
                if (isset($orginal_path)) {
                    if (\Storage::disk('s3')->exists($orginal_path))
                    \Storage::disk('s3')->delete($orginal_path);
                }
            }

            if (isset($thumbnail_path)) {
                if (\Storage::disk('s3')->exists($thumbnail_path))
                \Storage::disk('s3')->delete($thumbnail_path);
            }
            if (isset($preview_path)) {
                if (\Storage::disk('s3')->exists($preview_path))
                \Storage::disk('s3')->delete($preview_path);
            }
        }

        ContributorVideo::whereIn('id', $ids)->delete();

        VideoTagContributor::whereIn('video_id', $ids)->delete();

        CategoryContributorVideo::whereIn('video_id', $ids)->delete();
        ContributorVideoLegalRelease::whereIn('video_id', $ids)->delete();
        ContributorVideoSubmissionItem::whereIn('video_id', $ids)->delete();
        return [];
    }

    public function options($id, $slug = null)
    {
        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);
        $user_id = request()->get('user_id');

        $categories = CategoryContributor::query()
            ->orderBy('name')
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                ];
            });

        $releases = LegalRelease::where('contributor_id', $user_id)
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                    'file' => $item->file,
                ];
            });

        $licenses = Helper::getLicenses();

        return [
            'options' => [
                'categories_admin' => [],
                'categories' => $categories,
                'releases' => $releases,
                'licenses' => $licenses,
            ],
        ];
    }

    public function update_multi(Request $request)
    {
        $request_params = json_decode(file_get_contents('php://input'), true);
        $user_id = request()->get('user_id');

        $options = $request_params['options'];

        $data = collect($request_params['data'])->map(function ($item) {
            $all = [];
            foreach ($item as $key => $value) {
                $all = array_merge($all, $item[$key]);
            }
            return $all;
        });
        
        $video_ids = $data->keys()->values();
        $license = $data[$video_ids[0]]['license']??'commercial';
        $videosChanges = $data
            ->map(function ($item) {
                $new = [];
                if (isset($item['title_ar'])) {
                    $new['title_ar'] = $item['title_ar'];
                }
                if (isset($item['title_en'])) {
                    $new['title_en'] = $item['title_en'];
                }
                if (isset($item['stage_edit'])) {
                    $new['stage_edit'] = $item['stage_edit'];
                }

                if (isset($item['license']) && $item['license'] != '') {
                    $new['license'] = $item['license'];
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

        $releasesChanges = $data
            ->map(function ($item) {
                $new = [];
                if (isset($item['release_ids'])) {
                    $new['release_ids'] = $item['release_ids'];
                }
                return $new;
            })
            ->filter(function ($item) {
                return count($item);
            });

        // process changes
        if (count($videosChanges)) {
            $raw_query = "";
            foreach ($videosChanges as $id => $params) {
                $column_string = [];
                foreach ($params as $column => $value) {
                    $value = str_replace("'", "\'", $value);
                    $column_string[] = "`$column` = '$value'";
                }
                $raw_query .= "UPDATE `contributor_videos` SET" . implode(',', $column_string) . " WHERE `contributor_videos`.`id` = $id;";
            }

            \DB::unprepared($raw_query);
        }

        if (count($tagsArChanges)) {
            $video_ids = $tagsArChanges->keys();
            if ($options['tags_ar_delete_old']) {
                VideoTagContributor::whereIn('video_id', $video_ids)
                    ->where('local', 'ar')
                    ->delete();
            }

            $raw_query = "INSERT INTO `video_tags_contributor` (`video_id`, `tag`, `slug`, `local`) VALUES ";
            $column_string = [];
            foreach ($tagsArChanges as $id => $params) {
                foreach ($params as $tags) {
                    foreach ($tags as $value) {
                        $tag = str_replace("'", "\'", $value);
                        $slug = preg_replace('/\s+/', '', $tag);
                        $column_string[] = " ($id, '$tag', '$slug', 'ar')";
                    }
                }
            }
            if (count($column_string)) {
                $raw_query .= implode(',', $column_string) . ";";
                \DB::unprepared($raw_query);
            }
        }

        if (count($tagsEnChanges)) {
            $video_ids = $tagsEnChanges->keys();
            if ($options['tags_en_delete_old']) {
                VideoTagContributor::whereIn('video_id', $video_ids)
                    ->where('local', 'en')
                    ->delete();
            }

            $raw_query = "INSERT INTO `video_tags_contributor` (`video_id`, `tag`, `slug`, `local`) VALUES ";
            $column_string = [];
            foreach ($tagsEnChanges as $id => $params) {
                foreach ($params as $tags) {
                    foreach ($tags as $value) {
                        $tag = str_replace("'", "\'", $value);
                        $slug = preg_replace('/\s+/', '', $tag);
                        $column_string[] = " ($id, '$tag', '$slug', 'en')";
                    }
                }
            }
            if (count($column_string)) {
                $raw_query .= implode(',', $column_string) . ";";
                \DB::unprepared($raw_query);
            }
        }

        if (count($categoriesChanges)) {
            $video_ids = $categoriesChanges->keys();
            if ($options['category_ids_delete_old']) {
                CategoryContributorVideo::whereIn('video_id', $video_ids)->delete();
            }

            $raw_query = "INSERT INTO `category_contributor_video` (`video_id`, `category_id`) VALUES ";
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
                \DB::unprepared($raw_query);
            }
        }

        if (count($releasesChanges) || $license === 'editorial') {
            $video_ids =$license === 'editorial'?$data->keys()->values():$releasesChanges->keys();
            if ($options['release_ids_delete_old'] || $license === 'editorial') {
                ContributorVideoLegalRelease::whereIn('video_id', $video_ids)->delete();
            }

            if ($license != 'editorial') {
                $raw_query = "INSERT INTO `contributor_video_legal_release` (`video_id`, `release_id`) VALUES ";
                $column_string = [];
                foreach ($releasesChanges as $id => $params) {
                    foreach ($params as $releases) {
                        foreach ($releases as $release_id) {
                            $column_string[] = " ($id, '$release_id')";
                        }
                    }
                }
    
                if (count($column_string)) {
                    $raw_query .= implode(',', $column_string) . ";";
                    \DB::unprepared($raw_query);
                }
            }

        }

        $this->delete_duplicated_tags();

        return [];
    }

    /* send video to review */
    public function submit(Request $request)
    {
        $lang = request()->get('lang');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);
        $request_params = json_decode(file_get_contents('php://input'), true);
        $user_id = request()->get('user_id');


        if (isset($request_params) && @$request_params['noChanges'] === false)
            $this->update_multi($request);

        $ids = explode(",", $request_params['ids']);
             // make sure ids for current user only
             $ids = ContributorVideo::where('contributor_id', $user_id)
             ->whereIn('id', $ids)
             ->whereColumn('title_ar', '!=','title_en')
             ->pluck('id');
 
             if(count($ids) === 0){
             return $res = response()->json([
             'status'=>422,
             'message'=>__('views.you_must_enter_an_arabic_title_or_an_english_title_and_send_content')
             ]);
             }
             
    //  if(count($ids) === 0){
    //    return $res = response()->json([
    //         'status'=>422,
    //         'message'=>__('views.Please save all data befor submit.')
    //    ]);
    //  }

        //  if(count($ids) === 0){
        //    return $res = response()->json([
        //         'status'=>422,
        //         'message'=>__('views.Please save all data befor submit.')
        //    ]);
        //  }


        ContributorVideo::whereIn('id', $ids)
            ->update(['contributor_stage' => 2]);

        $submission = ContributorVideoSubmission::create([
            'contributor_id' => $user_id,
            'type' => $request_params['type'],
            'status' => 'pending',
        ]);
        
        $items =  collect($ids)->map(function ($id) {
            return ['video_id' => $id];
        });
        $submission->items()->createMany(
            $items->toArray()
        );

        return [];
    }

    public function resubmit(Request $request)
    {
        $request_params = json_decode(file_get_contents('php://input'), true);
        
        $user_id = request()->get('user_id');

        $contributor_stage = 2;

        if (isset($request_params['noChanges']) && $request_params['noChanges'] === false)
            $this->update_multi($request);

        $id = intval($request_params['id']);

        $video = ContributorVideo::where('contributor_id', $user_id)
            ->where('id', $id)
            ->firstOrFail('id', $id);

        if (isset($request_params['status_rejected']) && $request_params['status_rejected'] === true)
            $contributor_stage = 6;

        $video->contributor_stage = $contributor_stage;
        $video->save();

        return [];
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
        ];

        $sort_list = [
            'updated_at' => trans('views.last_updated_at'),
            'id' => trans('views.first_created_at'),
        ];


        $categories = CategoryContributor::get()->pluck('name', 'id');

        return [
            'filters' => [
                'type' => $status_list,
                'categories' => $categories,
                'sort_by' => $sort_list,
            ],
        ];
    }

    public function delete_duplicated_tags()
    {

    }
}
