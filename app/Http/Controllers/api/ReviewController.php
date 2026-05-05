<?php

namespace App\Http\Controllers\api;

use App\Models\LegalRelease;
use App\Models\CategoryContributorImage;
use App\Models\Contributor;
use App\Models\Image;
use App\Jobs\PublishImage;
use App\ImageTagContributor;
use App\Models\ImageCategory;
use Illuminate\Http\Request;
use App\Models\ContributorImageSubmission;
use App\Models\ContributorImageSubmissionItem;
use App\Models\ContributorImage;
use App\Models\CategoryContributor;
use App\Http\Controllers\Controller;
use App\Models\ContributorImagesLegalRelease;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailAlertContributorFileRejected;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\CheckUniqueContributorFilesPublished;
use App\Jobs\SendEmailAlertContributorFileUpdatedJob;
use App\Jobs\SendEmailAlertContributorFileUpdatedRejectedJob;
use App\Models\RejectionReason;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function index(Request $request, $id)
    {
        $status = $request->get('status');
        $update =($request->has('status') and $status === "update");
        $image_ids = ContributorImageSubmissionItem::whereHas('image', function ($q) use($update){
            if($update)
            $q = $q->where('contributor_stage',8);
            else
            $q = $q->whereIn('contributor_stage', [1, 2, 6]);


            $q =  $q->doesntHave('file')
                ->orWhereHas('file', function ($q) use($update) {
                    $q->whereHas('contributor_file', function ($q) use($update) {
                        if($update)
                          $q->where('stage_edit',3)
                          ->where('contributor_stage',8);
                        else
                          $q->where('contributor_stage', 6);
                    });
                });
        })
            ->whereHas('submmission', function ($q) use ($id) {
                $q->where('id', $id);
            })->pluck('image_id');

        $images = ContributorImage::whereIn('contributor_images.id', $image_ids);
        //$images = $images->doesntHave('file');
        // $images = $images->orWhereHas('file',function($q)use($contributor_id,$id) {
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
                $images->where('stage_edit', 2);
            } elseif ($request->get('type') == 'half_edit') {
                $images->where('stage_edit', 1);
            } elseif ($request->get('type') == 'no_edit') {
                $images->where('stage_edit', 0);
            }
        }

        if ($request->get('categories')) {
            $images
                ->select('contributor_images.*')
                ->join('category_contributor_image', 'category_contributor_image.image_id', '=', 'contributor_images.id')
                ->where('category_contributor_image.category_id', $request->get('categories'));
        }

        $images->orderBy('contributor_images.' . $request->get('sort_by', 'id'), 'desc');

        $page = $request->get('page');
        $perpage = $request->get('perpage');
        $offset = ($page - 1) * $perpage;
        $count = $images->count();
        $images->offset($offset)->limit($perpage);

        $lang = app()->getLocale();
        $data = $images
            ->get();
        $data = $data->map(function ($image) {
            $image->tags_ar = $image->tags_img
                ->filter(function ($tag) {
                    return $tag->local === 'ar';
                })
                ->pluck('tag');
            return $image;
        })
            ->map(function ($image) {
                $image->tags_en = $image->tags_img
                    ->filter(function ($tag) {
                        return $tag->local === 'en';
                    })
                    ->pluck('tag');
                return $image;
            })
            ->map(function ($image) use ($lang) {
                $image->category_ids = $image->category->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->id,
                        'label' => $item->name,
                    ];
                });
                return $image;
            })
            ->map(function ($image) use ($lang) {
                $image->release_ids = $image->release_image->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->release_id,
                        'label' => $item->release->name,
                        'file' => url($item->release->file),
                    ];
                });
                return $image;
            })
            ->map(function ($image) use($update) {
                $status_file = __('views.new');
                if ($image->contributor_stage === 5)
                    $status_file = __('views.error_publish');
                elseif ($image->contributor_stage === 6)
                    $status_file = __('views.wating_after_rejected');
                elseif ($image->contributor_stage === 8 and $image->stage_edit === 3)
                    $status_file = __('views.update_from_contributor_after_publish');


                return collect([
                    'id' => $image->id,
                    'thumbnail' => $image->thumbnail,
                    'height_thumbnail' => $image->height_thumbnail,
                    'width_thumbnail' => $image->width_thumbnail,
                    'preview' => $image->preview ?$image->preview : NULL,
                    'title_ar' => $image->title_ar,
                    'title_en' => $image->title_en,
                    'status' => $image->status,
                    'license' => $image->license,
                    'license_title' => $image->license ? __("global.$image->license") : 'NONE',
                    'original_name' => $image->original_name,
                    'stage_edit' => $image->stage_edit,
                    'contributor_stage' => $image->contributor_stage,
                    'status_file' => $status_file,
                    'user_id' => $image->user_id,
                    'tags_ar' => $image->tags_ar,
                    'tags_en' => $image->tags_en,
                    'category_ids' => $image->category_ids,
                    'release_ids' => $image->release_ids,
                    'post_link' => $image->file && $image->contributor_stage === 8 ? $image->file->post_link : null,
                    'file_title_en' => ($image->file && $image->contributor_stage === 8 && $update) ? $image->file->title_en : null,
                    'file_title_ar' => ($image->file && $image->contributor_stage === 8 && $update) ? $image->file->title_ar : null,
                    'file_tags_en' => ($image->file && $image->contributor_stage === 8 && $update) ? $image->file->tags->where('local','en')->pluck('title') : null,
                    'file_tags_ar' => ($image->file && $image->contributor_stage === 8 && $update) ? $image->file->tags->where('local','ar')->pluck('title') : null,
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

    public function submit(CheckUniqueContributorFilesPublished $request, $id)
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
                $contributor_submission = ContributorImageSubmission::find($id);
                $image_ids = $contributor_submission->items()->whereIn('image_id', $ids)->pluck('image_id');
                $contributor_id = $contributor_submission->contributor_id;

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
                    $ids = ContributorImage::whereIn('contributor_images.id', $image_ids)
                        ->whereIn('contributor_stage', [1, 2, 5])
                        // ->whereIn('id', $ids)
                        ->doesntHave('file')
                        ->pluck('id');
                } elseif ($request_params['type'] === 'publish') {

                    $validator = Validator::make($data, [
                        'categories' => 'required|array',
                        'categories.*' => 'exists:image_categories,id',
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

                    $ids = ContributorImage::whereHas('tags_img')
                        ->whereHas('category')
                        ->whereIn('contributor_images.id', $image_ids)
                        ->whereIn('contributor_stage', [1, 2, 5])
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

            ContributorImage::whereIn('id', $image_ids)->update([
                'contributor_stage' => $contributor_stage,
                'review_notes' => $request_params['notes'],
            ]);

            if ($contributor_stage === 5) {
                $contributor_images = ContributorImage::with('contributor')->whereIn('id', $image_ids)->get();
                foreach ($contributor_images as $contributor_image) {
                    $data = [
                        'contributor_submission_id' => $contributor_submission->id,
                        'contributor_image_id' => $contributor_image->id,
                        'original_name' => $contributor_image->original_name,
                        'extension' => $contributor_image->extension,
                        'hash' => $contributor_image->hash,
                        'large' => $contributor_image->large,
                        'title_en' => $contributor_image->title_en,
                        'title_ar' => $contributor_image->title_ar,
                        'user_id' => $contributor_image->contributor_id,
                        'user_type' => Contributor::class,
                        'tags_en' => ImageTagContributor::where('image_id', $contributor_image->id)->where('local', 'en')->pluck('tag')->toArray(),
                        'tags_ar' => ImageTagContributor::where('image_id', $contributor_image->id)->where('local', 'ar')->pluck('tag')->toArray(),
                        'category_ids' => $request_params['categories'] ?? NULL,
                        'contributor' => $contributor_image->contributor,
                        'reviewer_id' => $contributor_image->reviewer_id,
                        'reviewed_at' => $contributor_image->reviewed_at,
                        'how_use_image' => $contributor_image->license === 'editorial' ? 'editorial_only' : "free",
                        'publisher_id' => auth()->id(),
                        'published_at' => date('Y-m-d H:i:s'),
                    ];
                    $this->publish_image($data);
                }

            } elseif (isset($request_params['notes']) && \in_array($contributor_stage, [3, 4])) {
                foreach ($ids as $key => $value) {
                    $id = $value;
                    $type = 'images';
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

    private function publish_image($params)
    {
        dispatch(new PublishImage($params))->onQueue('image');
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

        $categories = ImageCategory::get()->pluck('name', 'id');
        $data[] = [
            'type' => ['data'=>$status_list,'type'=>"select"],
            'categories' => ['data'=>$categories,'type'=>"select"],
            'sort_by' => ['data'=>$sort_list,'type'=>"select"],
        ];

        return response()->json(['filters'=>$data]);
    }

    public function options($id)
    {
        $contributor_submission = ContributorImageSubmission::find($id);
        $contributor_id = $contributor_submission->contributor_id;
        // TODO move to headers and middleware
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);
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

        $categories_contributor = CategoryContributor::query()
            ->orderBy('slug')
            ->get()
            ->map(function ($item) {
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
        $licenses = getLicenses(["commercial", "editorial"]);
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
                'categories_admin' => [],
                'categories' => $categories,
                'categories_contributor' => $categories_contributor,
                'releases' => $releases,
                'reasons_rejection' => $reasons_rejection,
                'licenses' => $licenses,
            ],
        ];
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

        $image_ids = $data->keys()->values();
        $license = $data[$image_ids[0]]['license'] ?? '';

        $imagesChanges = $data
            ->map(function ($item) use($status_update) {
                $new = [];
                if (isset($item['title_ar'])) {
                    $new['title_ar'] = $item['title_ar'];
                }
                if (isset($item['title_en'])) {
                    $new['title_en'] = $item['title_en'];
                }
                if($status_update){
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
        if (count($imagesChanges)) {
            $raw_query = "";
            foreach ($imagesChanges as $id => $params) {
                $column_string = [];
                foreach ($params as $column => $value) {
                    $value = str_replace("'", "\'", $value);
                    $column_string[] = "`$column` = '$value'";
                }
                $column_string[] = "`reviewer_id` = '" . auth()->id() . "'";
                $column_string[] = "`reviewed_at` = '" . date('Y-m-d H:i:s') . "'";
                $raw_query .= "UPDATE `contributor_images` SET" . implode(',', $column_string) . " WHERE `contributor_images`.`id` = $id;";
            }

            DB::unprepared($raw_query);
        }

        if (count($tagsArChanges)) {
            $image_ids = $tagsArChanges->keys();
            if ($options['tags_ar_delete_old']) {
                ImageTagContributor::whereIn('image_id', $image_ids)
                    ->where('local', 'ar')
                    ->delete();
            }

            $raw_query = "INSERT INTO `image_tags_contributor` (`image_id`, `tag`, `slug`, `local`) VALUES ";
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
            $image_ids = $tagsEnChanges->keys();
            if ($options['tags_en_delete_old']) {
                ImageTagContributor::whereIn('image_id', $image_ids)
                    ->where('local', 'en')
                    ->delete();
            }

            $raw_query = "INSERT INTO `image_tags_contributor` (`image_id`, `tag`, `slug`, `local`) VALUES ";
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
            $image_ids = $categoriesChanges->keys();
            if ($options['category_ids_delete_old']) {
                CategoryContributorImage::whereIn('image_id', $image_ids)->delete();
            }

            $raw_query = "INSERT INTO `category_contributor_image` (`image_id`, `category_id`) VALUES ";
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

        if (count($releasesChanges) || $license === 'editorial' && $status_update) {
            $image_ids = $license === 'editorial' ? $data->keys()->values() : $releasesChanges->keys();

            if ($options['release_ids_delete_old'] || $license === 'editorial') {
                ContributorImagesLegalRelease::whereIn('image_id', $image_ids)->delete();
            }

            if ($license != 'editorial') {
                $raw_query = "INSERT INTO `contributor_image_legal_release` (`image_id`, `release_id`) VALUES ";
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

    public function create_release(Request $request, $id)
    {
        $contributor_submission = ContributorImageSubmission::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'type' => 'required|string|max:255',
            'file' => 'file|required|min:1|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 422,
                    'name' => 'ValidationError',
                    'description' => 'Validation error, please try again. If this error persists, please contact the site administrator.',
                    'details' => $validator->errors(),
                ],
                422
            );
        }

        $contributor_id = $contributor_submission->contributor_id;
        $folder_path = "uploads/contributor_releases/{$contributor_id}";
        $path = \Storage::disk('s3')->putFile($folder_path, $request->file('file'));

        $releases = LegalRelease::create([
            'contributor_id' => $contributor_id,
            'name' => $request->name,
            'type' => $request->type,
            'file' => $path,
            'ethnicity' => "",
            'age' => "",
            'gender' => "",
        ]);

        return $releases;
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
                $images = Image::where('contributor_image_id', '>', 0)
                    ->whereIn('id', $ids)
                    ->select('id', 'contributor_image_id')
                    ->get();
                if (count($images) === 0) {
                    $exception = [
                        'success' => false,
                        'message' => 'Validation Errors',
                        'code' => 2,
                        'errors' => ['message' => __('views.You cannot reject this content.')],
                    ];
                    throw new HttpResponseException(response()->json($exception, 422));
                }
                $ids_images = Arr::pluck($images, 'id');
                $ids_contributor_images = Arr::pluck($images, 'contributor_image_id');

            } elseif ($request_params['type'] === 'publish') {

                $images = Image::whereIn('contributor_image_id', $ids)
                    ->select('id', 'contributor_image_id')
                    ->get();
                if (count($images) === 0) {
                    $exception = [
                        'success' => false,
                        'message' => 'Validation Errors',
                        'code' => 2,
                        'errors' => ['message' => __('views.You cannot reject this content.')],
                    ];
                    throw new HttpResponseException(response()->json($exception, 422));
                }
                $ids_images = Arr::pluck($images, 'id');
                $ids_contributor_images = Arr::pluck($images, 'contributor_image_id');


                $ids = ContributorImage::whereHas('tags_img')
                    ->whereHas('category')
                    ->whereIn('contributor_images.id', $ids_contributor_images)
                    ->whereIn('contributor_stage', [3,4, 6])
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
        ContributorImage::whereIn('id', $ids_contributor_images)->update([
            'contributor_stage' => $contributor_stage,
            'review_notes' => $request_params['notes'],
        ]);

        if (isset($request_params['notes']) && \in_array($contributor_stage, [3, 4])) {
            Image::whereIn('id', $ids_images)->update(['status' => 'pending']);
            foreach ($ids_contributor_images as $key => $value) {
                $id = $value;
                $type = 'images';
                $review_notes = $request_params['notes'];
                SendEmailAlertContributorFileRejected::dispatch($id, $type, $review_notes);

            }
        }

        if ($contributor_stage === 8)
            Image::whereIn('id', $ids_images)->update(['status' => 'active']);

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
            $data = ContributorImage::with(['tags_img','category','file'])->whereIn('id',$ids)->get();
            if ($status_update === 'agree') {
                foreach ($data as $key => $item) {
                    $file = $item->file()->where('contributor_image_id',$item->id)->first();
                    $file->title_ar = $item->title_ar;
                    $file->title_en = $item->title_en;
                    $slug = 'image-' . $file->id . '-' . slugify_v2($item->title_en);
                    $file->slug = $slug;
                    $file->save();
                    $tags_ar = $item->tags_img->where('local','ar')->pluck('tag')->toArray();
                    $tags_en = $item->tags_img->where('local','en')->pluck('tag')->toArray();
                    if($tags_ar)
                    sync_tags($file, $tags_ar, 'ar');
                    if($tags_en)
                    sync_tags($file, $tags_en, 'en');

                    $item->stage_edit = 2;
                    $item->save();
                    dispatch(new \App\Jobs\SeoImages($file->id));
                    SendEmailAlertContributorFileUpdatedJob::dispatch($item->id, class_basename($item));



                }
            }elseif($status_update === 'reject'){
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
