<?php

namespace App\Http\Controllers\api;

use App\CategoryContributorVideo;
use App\Helper;
use App\Models\LegalRelease;
use App\Models\Contributor;
use App\Models\ContributorVideo;
use App\Models\ContributorVideoSubmission;
use App\Models\ContributorVideoSubmissionItem;
use App\Models\VideoCategory;
use App\Models\Video;
use App\Jobs\PublishVideo;
use App\VideoTagContributor;
use Illuminate\Http\Request;
use App\Models\CategoryContributor;
use App\Http\Controllers\Controller;
use App\Models\ContributorVideoLegalRelease;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailAlertContributorFileRejected;
use App\Jobs\SendEmailAlertContributorFileUpdatedJob;
use App\Jobs\SendEmailAlertContributorFileUpdatedRejectedJob;
use App\Models\RejectionReason;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoReviewController extends Controller
{
    public function index(Request $request, $id)
    {
        $status = $request->get('status');
        $update =($request->has('status') and $status === "update");
        $video_ids = ContributorVideoSubmissionItem::whereHas('File', function ($q) use($update) {
                if($update)
                $q = $q->where('contributor_stage',8);
                else
                $q = $q->whereIn('contributor_stage', [1, 2, 6]);

                $q = $q->doesntHave('file')
                ->orWhereHas('file', function ($q) use($update){
                    $q->whereHas('contributor_file', function ($q) use($update) {
                        if ($update)
                          $q->where('stage_edit',3)
                          ->where('contributor_stage',8);
                        else
                          $q->where('contributor_stage', 6);
                    });
                });
        })
            ->whereHas('submmission', function ($q) use ($id) {
                $q->where('id', $id);
            })->pluck('video_id');
        /* e:edit query */
        $videos = ContributorVideo::whereIn('contributor_videos.id', $video_ids);
        // $videos = $videos->doesntHave('file');
        // $videos = $videos->orWhereHas('file',function($q)use($contributor_id,$id) {
        //     $q->whereHas('contributor_file',function($q) use($contributor_id,$id){
        //         $q->where('contributor_stage',6)
        //         ->where('user_id',$contributor_id)
        //         ->whereHas('submmission_item',function($q)use($id){
        //             $q->whereHas('submmission',function($q)use($id){
        //                 $q->where('id',$id);
        //             });
        //         });
        //     });
        // });

        if ($request->get('type')) {
            if ($request->get('type') == 'complete') {
                $videos->where('stage_edit', 2);
            } elseif ($request->get('type') == 'half_edit') {
                $videos->where('stage_edit', 1);
            } elseif ($request->get('type') == 'no_edit') {
                $videos->where('stage_edit', 0);
            }
        }

        if ($request->get('categories')) {
            $videos
                ->select('contributor_videos.*')
                ->join('category_videos_contributor', 'category_videos_contributor.video_id', '=', 'contributor_videos.id')
                ->where('category_videos_contributor.category_id', $request->get('categories'));
        }

        $videos->orderBy('contributor_videos.' . $request->get('sort_by', 'id'), 'desc');

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
            ->map(function ($video) use($update){
                $status_file = __('views.new');
                if ($video->contributor_stage === 8)
                    $status_file = __('views.error_publish');
                elseif ($video->contributor_stage === 6)
                    $status_file = __('views.wating_after_rejected');
                elseif ($video->contributor_stage === 8 and $video->stage_edit === 3)
                    $status_file = __('views.update_from_contributor_after_publish');

                return collect([
                    'id' => $video->id,
                    'thumbnail' => $video->thumbnail,
                    'height_thumbnail' => $video->height_thumbnail,
                    'width_thumbnail' => $video->width_thumbnail,
                    'preview' => $video->preview_admin ? $video->preview_admin : NULL,
                    'title_ar' => $video->title_ar,
                    'title_en' => $video->title_en,
                    'status' => $video->status,
                    'license' => $video->license,
                    'license_title' =>$video->license?__("global.$video->license"):'NONE',
                    'original_name' => $video->original_name,
                    'stage_edit' => $video->stage_edit,
                    'contributor_stage' => $video->contributor_stage,
                    'status_file' => $status_file,
                    'tags_ar' => $video->tags_ar,
                    'tags_en' => $video->tags_en,
                    'category_ids' => $video->category_ids,
                    'release_ids' => $video->release_ids,
                    'post_link' => $video->file && $video->contributor_stage === 8 ? $video->file->post_link : null,
                    'file_title_en' => ($video->file && $video->contributor_stage === 8 && $update) ? $video->file->title_en : null,
                    'file_title_ar' => ($video->file && $video->contributor_stage === 8 && $update) ? $video->file->title_ar : null,
                    'file_tags_en' => ($video->file && $video->contributor_stage === 8 && $update) ? $video->file->tags->where('local','en')->pluck('title') : null,
                    'file_tags_ar' => ($video->file && $video->contributor_stage === 8 && $update) ? $video->file->tags->where('local','ar')->pluck('title') : null,

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

    public function submit(Request $request, $id)
    {

        $data = $request->all();

        DB::beginTransaction();

        try {
            if ($request->has('type') && $data['type'] != '') {
                $request_params = json_decode(file_get_contents('php://input'), true);
                if (!isset($request_params['ids']) || $request_params['ids'] == "") {
                    $exception = [
                        'success' => false,
                        'message' => 'Validation Errors',
                        'code' => 2,
                        'errors' => ['message' => __('views.Please Select files')],
                    ];
                    return response()->json($exception, 422);
                }
                $ids = explode(",", $request_params['ids']);
                $contributor_submission = ContributorVideoSubmission::find($id);
                $video_ids = $contributor_submission->items->whereIn('video_id', $ids)->pluck('video_id');
                $contributor_id = $contributor_submission->contributor_id;

                $instans_of_class = new ContributorVideo();

                if (in_array($request_params['type'], ['reject', 'hard_reject'])) {
                    if ($request_params['type'] === 'reject')
                        $contributor_stage = 3;
                    elseif ($request_params['type'] === 'hard_reject')
                        $contributor_stage = 4;

                    if (!isset($request_params['notes']) || $request_params['notes'] == '') {

                        $exception = [
                            'success' => false,
                            'message' => 'Validation Errors',
                            'code' => 2,
                            'errors' => ['message' => __('views.Please enter the Note field required.')],
                        ];
                        return response()->json($exception, 422);
                    }
                    $ids = $instans_of_class
                        ->whereIn('contributor_videos.id', $video_ids)
                        ->whereIn('contributor_stage', [1, 2, 8])
                        // ->whereIn('id', $ids)
                        ->doesntHave('file')
                        ->pluck('id');
                } elseif ($request_params['type'] === 'publish') {

                    $validator = Validator::make($data, [
                        'categories' => 'required|array',
                        'categories.*' => 'exists:video_categories,id',
                    ]);

                    if ($validator->fails()) {
                        $errors_keys = $validator->errors()->keys();
                        $request_errors = $validator->errors()->all();
                        $errors = [];
                        for ($i = 0; $i < sizeof($errors_keys); $i++) {
                            $val = $request_errors[$i];
                            array_push($errors, $val);
                        }
                        $exception = [
                            'success' => false,
                            'message' => 'Validation Errors',
                            'code' => 2,
                            'errors' => ['message' => collect($errors)->map(function ($item) {
                                return $item . ' <br/>';
                            })],
                        ];
                        return response()->json($exception, 422);

                    }

                    $ids = $instans_of_class->whereHas('tags')
                        ->whereHas('category')
                        ->whereIn('contributor_videos.id', $video_ids)
                        // ->whereIn('id', $ids)
                        ->whereIn('contributor_stage', [1, 2, 8])
                        ->where('title_ar', '!=', '')
                        ->where('title_en', '!=', '')
                        ->doesntHave('file')
                        ->pluck('id');
                    if (count($ids) === 0) {
                        $exception = [
                            'success' => false,
                            'message' => 'Validation Errors',
                            'code' => 2,
                            'errors' => ['message' => __('views.Please save all data befor submit.')],
                        ];
                        return response()->json($exception, 422);
                    }
                    $contributor_stage = 5;
                }

            } else {
                $exception = [
                    'success' => false,
                    'message' => 'Validation Errors',
                    'code' => 2,
                    'errors' => ['message' => __('views.Somthing Error')],
                ];
                return response()->json($exception, 422);
            }

            $instans_of_class->whereIn('id', $video_ids)->update([
                'contributor_stage' => $contributor_stage,
                'review_notes' => $request_params['notes'],
            ]);


            if ($contributor_stage === 5) {
                $contributor_videos = ContributorVideo::with('contributor')->whereIn('id', $ids)->get();
                foreach ($contributor_videos as $contributor_video) {
                    $this->publish_video([
                        'contributor_video_id' => $contributor_video->id,
                        'original_name' => $contributor_video->original_name,
                        'original_type' => $contributor_video->original_type,
                        'extension' => $contributor_video->extension,
                        'hash' => $contributor_video->hash,
                        'preview' => $contributor_video->preview,
                        'title_en' => $contributor_video->title_en,
                        'title_ar' => $contributor_video->title_ar,
                        'user_id' => $contributor_video->contributor_id,
                        'user_type' => Contributor::class,
                        'duration' => $contributor_video->duration,
                        'tags_en' => VideoTagContributor::where('video_id', $contributor_video->id)->where('local', 'en')->pluck('tag')->toArray(),
                        'tags_ar' => VideoTagContributor::where('video_id', $contributor_video->id)->where('local', 'ar')->pluck('tag')->toArray(),
                        'category_ids' => $request_params['categories'] ?? NULL,
                        'contributor' => $contributor_video->contributor,
                        'reviewer_id' => $contributor_video->reviewer_id,
                        'reviewed_at' => $contributor_video->reviewed_at,
                        'how_use_image' => $contributor_video->license === 'editorial' ? 'editorial_only' : "free",
                        'publisher_id' => auth()->id(),
                        'published_at' => date('Y-m-d H:i:s'),

                    ]);
                }
            } elseif (isset($request_params['notes']) && \in_array($contributor_stage, [3, 4])) {

                foreach ($ids as $key => $value) {
                    $id = $value;
                    $type = 'videos';
                    $review_notes = $request_params['notes'];

                    SendEmailAlertContributorFileRejected::dispatch($id, $type, $review_notes);

                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error("msg: ". $th->getMessage() ."file:  ". $th->getFile(). ' line : ' . $th->getLine(). "  user_id: " .auth()->id());
            $exception = [
                'success' => false,
                'message' => 'Exception Errors',
                'code' => 2,
                'errors' => ['files' => __('validation.error_contact_administrator')],
            ];
            return response()->json($exception, 422);
        }


        return [];
    }

    private function publish_video($params)
    {
        dispatch(new PublishVideo($params))->onQueue('video');
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

        $categories = VideoCategory::get()->pluck('name', 'id');

        $data[] = [
            'type' => ['data'=>$status_list,'type'=>"select"],
            'categories' => ['data'=>$categories,'type'=>"select"],
            'sort_by' => ['data'=>$sort_list,'type'=>"select"],
        ];

        return response()->json(['filters'=>$data]);
    }

    public function options($id)
    {
        $contributor_submission = ContributorVideoSubmission::find($id);
        $contributor_id = $contributor_submission->contributor_id;
        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);
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

        $categories_contributor = CategoryContributor::query()
            ->orderBy('name')
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                ];
            });

        $releases = LegalRelease::where('contributor_id', $contributor_id)
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                ];
            });

            $licenses = getLicenses(["commercial","editorial"]);
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
                'categories_admin' => [],
                'categories_contributor' => $categories_contributor,
                'categories' => $categories,
                'releases' => $releases,
                'reasons_rejection' => $reasons_rejection,
                'licenses' => $licenses,
            ],
        ];
    }


    public function getDimension($path, $type)
    {
        if ($type == 'width') {
            $width = Helper::getWidth($path);
            return $width;
        } else {
            $height = Helper::getHeight($path);
            return $height;
        }
    }

    public function update_multi(Request $request)
    {
        $request_params = json_decode(file_get_contents('php://input'), true);

        $options = $request_params['options'];
        $status_update = $request_params['status_update']??true;

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
            ->map(function ($item) use($status_update) {
                $new = [];
                if (isset($item['title_ar'])) {
                    $new['title_ar'] = $item['title_ar'];
                }
                if (isset($item['title_en'])) {
                    $new['title_en'] = $item['title_en'];
                }
                if ($status_update) {
                    if (isset($item['stage_edit'])) {
                        $new['stage_edit'] = $item['stage_edit'];
                    }
                    if (isset($item['license']) && $item['license'] != '') {
                        $new['license'] = $item['license'];
                    }
                }
                $new['updated_at'] = date('Y-m-d H:i:s');

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
                $column_string[] = "`reviewer_id` = '" . auth()->id() . "'";
                $column_string[] = "`reviewed_at` = '" . date('Y-m-d H:i:s') . "'";
                $raw_query .= "UPDATE `contributor_videos` SET" . implode(',', $column_string) . " WHERE `contributor_videos`.`id` = $id;";
            }

            DB::unprepared($raw_query);
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
                DB::unprepared($raw_query);
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
                DB::unprepared($raw_query);
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
                DB::unprepared($raw_query);
            }
        }

        if (count($releasesChanges) || $license === 'editorial' && $status_update ) {
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
                    DB::unprepared($raw_query);
                }
            }
        }

        $this->delete_duplicated_tags();

        return [];
    }

    public function delete_duplicated_tags()
    {
    }

    public function ChangeStatusContributorFileAfterPublished(Request $request)
    {
        /*
            ContributorFile
            3 => Rejrect ,
            4 => Haed Rejrect ,
            8 => Publish ,
            6  => After Contributor update data file and resubmit to admin ,
        */
        $request_params = json_decode(file_get_contents('php://input'), true);
        if (!isset($request_params['ids']) || $request_params['ids'] == '') {
            $exception = [
                'success' => false,
                'message' => 'Validation Errors',
                'code' => 2,
                'errors' => ['message' => __('views.Please select files.')],
            ];
            throw new HttpResponseException(response()->json($exception, 422));
        }
        if ($request->has('type') && $request_params['type'] != '') {
            $ids = explode(",", $request_params['ids']);
            $instans_of_class = new Video();

            if (in_array($request_params['type'], ['reject', 'hard_reject'])) {
                if ($request_params['type'] === 'reject')
                    $contributor_stage = 3;
                elseif ($request_params['type'] === 'hard_reject')
                    $contributor_stage = 4;

                if (!isset($request_params['notes']) || $request_params['notes'] == '') {
                    $exception = [
                        'success' => false,
                        'message' => 'Validation Errors',
                        'code' => 2,
                        'errors' => ['message' => __('views.Please enter the Note field required.')],
                    ];
                    throw new HttpResponseException(response()->json($exception, 422));
                }
                $videos = $instans_of_class
                    ->where('contributor_video_id', '>', 0)
                    ->whereIn('id', $ids)
                    ->select('id', 'contributor_video_id')
                    ->get();
                if (count($videos) === 0) {
                    $exception = [
                        'success' => false,
                        'message' => 'Validation Errors',
                        'code' => 2,
                        'errors' => ['message' => __('views.You cannot reject this content.')],
                    ];
                    throw new HttpResponseException(response()->json($exception, 422));
                }
                $ids_videos = Arr::pluck($videos, 'id');
                $ids_contributor_videos = Arr::pluck($videos, 'contributor_video_id');

            } elseif ($request_params['type'] === 'publish') {
                $videos = $instans_of_class
                ->whereIn('id', $ids)
                    ->whereHas('contributor_file', function ($query) {
                        $query->whereIn('contributor_stage', [3,4,6]);
                    })
                    ->select('id', 'contributor_video_id')
                    ->get();
                if (count($videos) === 0) {
                    $exception = [
                        'success' => false,
                        'message' => 'Validation Errors',
                        'code' => 2,
                        'errors' => ['message' => __('views.You cannot reject this content.')],
                    ];
                    throw new HttpResponseException(response()->json($exception, 422));
                }
                $ids_videos = Arr::pluck($videos, 'id');
                $ids_contributor_videos = Arr::pluck($videos, 'contributor_video_id');


                $ids = ContributorVideo::whereHas('tags')
                    ->whereHas('category')
                    ->whereIn('id', $ids_contributor_videos)
                    ->whereIn('contributor_stage', [3,4,6])
                    ->where('title_ar', '!=', '')
                    ->where('title_en', '!=', '')
                    ->has('file')
                    ->pluck('id');
                if (count($ids) === 0) {
                    $exception = [
                        'success' => false,
                        'message' => 'Validation Errors',
                        'code' => 2,
                        'errors' => ['message' => __('views.All fields are required.')],
                    ];
                    throw new HttpResponseException(response()->json($exception, 422));
                }
                $contributor_stage = 8;
            }

        } else {
            $exception = [
                'success' => false,
                'message' => 'Validation Errors',
                'code' => 2,
                'errors' => ['message' => __('views.Somthing Error')],
            ];
            throw new HttpResponseException(response()->json($exception, 422));
        }
        ContributorVideo::whereIn('id', $ids_contributor_videos)->update([
            'contributor_stage' => $contributor_stage,
            'review_notes' => $request_params['notes'],
        ]);

        if (isset($request_params['notes']) && \in_array($contributor_stage, [3, 4])) {
            $instans_of_class->whereIn('id', $ids_videos)->update(['status' => 'pending']);
            foreach ($ids_contributor_videos as $key => $value) {
                $id = $value;
                $type = 'videos';
                $review_notes = $request_params['notes'];
                SendEmailAlertContributorFileRejected::dispatch($id, $type, $review_notes);

            }
        }

        if ($contributor_stage === 8)
            $instans_of_class->whereIn('id', $ids_videos)->update(['status' => 'active']);

        $response = [
            'success' => true,
            'message' => 'Successfuly',
            'code' => 201,
            'errors' => NULL,
        ];
        throw new HttpResponseException(response()->json($response, 201));

    }

    public function update_after_publish(Request $request)
    {
        $ids = explode(",",$request->get('ids'));
        $status_update = $request->get('status_update');

        DB::beginTransaction();
        try {
            $data = ContributorVideo::with(['tags','file'])->whereIn('id',$ids)->get();
            if ($status_update === 'agree') {
                foreach ($data as $key => $item) {
                    $file = $item->file()->where('contributor_video_id',$item->id)->first();
                    $file->title_ar = $item->title_ar;
                    $file->title_en = $item->title_en;
                    $slug = 'clip-' . $file->id . '-' . slugify_v2($item->title_en);
                    $file->slug = $slug;
                    $file->save();
                    $tags_ar = $item->tags->where('local','ar')->pluck('tag')->toArray();
                    $tags_en = $item->tags->where('local','en')->pluck('tag')->toArray();
                    if($tags_ar)
                    sync_tags($file, $tags_ar, 'ar');
                    if($tags_en)
                    sync_tags($file, $tags_en, 'en');


                    $item->stage_edit = 2;
                    $item->save();
                    dispatch(new \App\Jobs\SeoVideos($file->id));
                    SendEmailAlertContributorFileUpdatedJob::dispatch($item->id, class_basename($item));



                }
            }elseif ($status_update === 'reject') {
                foreach ($data as $key => $item) {
                    $item->stage_edit = 2;
                    $item->save();
                    SendEmailAlertContributorFileUpdatedRejectedJob::dispatch($item->id, class_basename($item));
                }

            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = [
                'success' => true,
                'message' => 'Successfuly',
                'code' => 500,
                'errors' => $th->getMessage(),
                'line' => $th->getLine(),
            ];
            throw new HttpResponseException(response()->json($response, 500));
            throw $th;
        }
        $response = [
            'success' => true,
            'message' => 'Successfuly',
            'code' => 201,
            'errors' => NULL,
        ];
        throw new HttpResponseException(response()->json($response, 201));
    }
}
