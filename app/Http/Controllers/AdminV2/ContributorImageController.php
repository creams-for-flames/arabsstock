<?php

namespace App\Http\Controllers\AdminV2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Validator,DB};
use App\Models\{
    CategoryContributor
    ,ImageTagContributor
    ,CategoryContributorImage
    ,ContributorImagesLegalRelease
    ,LegalRelease
    ,ContributorImage
    ,ContributorImageSubmissionItem
    ,ContributorImageSubmission
};
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helper;


class ContributorImageController extends Controller
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
            $contributor_stages = [1, 2,6];
        } else if ($stage === 3) {
            $contributor_stages = [3, 4, 5 , 8];
        }
         $images = ContributorImage::with('file')->where('contributor_id', $user_id)->whereIn('contributor_stage', $contributor_stages);

         $page = $request->get('page');
         $perpage = $request->get('perpage');
         $offset = ($page - 1) * $perpage;
         $count = $images->count();
         $images->offset($offset)->limit($perpage);

         $lang = app()->getLocale();
         $data = $images
             ->get()
            ->map(function ($image) {
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
             ->map(function ($image) {
                 return collect([
                     'id' => $image->id,
                     'thumbnail' => isset($image->olde_image_id) ? "{$image->thumbnail}":
                     ( $image->is_uploaded ? "{$image->thumbnail}":asset('img/default2.jpg')) ,
                     'height_thumbnail' => $image->height_thumbnail,
                     'width_thumbnail' => $image->width_thumbnail,
                     'preview' => $image->is_uploaded ? $image->preview:asset('img/default2.jpg'),
                     'title_ar' => $image->title_ar,
                     'title_en' => $image->title_en,
                     'status' => $image->status,
                     'license' => $image->license,
                     'license_title' => $image->license?__("global.{$image->license}"):'_',
                     'original_name' => $image->original_name,
                     'stage_edit' => $image->stage_edit,
                     'contributor_stage' => $image->contributor_stage,
                     'review_notes' => $image->review_notes,
                     'tags_ar' => $image->tags_ar,
                     'tags_en' => $image->tags_en,
                     'category_ids' => $image->category_ids,
                     'release_ids' => $image->release_ids,
                     'is_uploaded'=>$image->is_uploaded ?__('Uploaded'):__('Uploading'),
                     'post_link' =>$image->file && $image->contributor_stage === 8 ?$image->file->post_link:null,
                     'status_file_rejected_publish' =>$image->file?(($image->file->status === "pending" && \in_array($image->contributor_stage,[4,3]))?TRUE:FALSE):FALSE,
                     'action_delete'=>(!isset($image->file->id) && \in_array($image->contributor_stage,[0,1,3,4]))?TRUE:FALSE,
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
        $user_id = $request->get('user_id');
        $ids = explode(",", $ids);

        // make sure ids for current user only
        $ids = ContributorImage::where('contributor_id', $user_id)
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

        $images = ContributorImage::whereIn('id', $ids)
            ->get();

        // delete from s3
        foreach ($images as $image) {
            $orginal_path= $image->large;
            $thumbnail_path= $image->thumbnail;
            $preview_path= $image->preview;

            if (\Storage::disk('s3')->exists($orginal_path))
            \Storage::disk('s3')->delete($orginal_path);

            if (\Storage::disk('s3')->exists($thumbnail_path))
            \Storage::disk('s3')->delete($thumbnail_path);

            if (\Storage::disk('s3')->exists($preview_path))
            \Storage::disk('s3')->delete($preview_path);

        }

        ContributorImage::whereIn('id', $ids)
            ->delete();

        ImageTagContributor::whereIn('image_id', $ids)
            ->delete();

        CategoryContributorImage::whereIn('image_id', $ids)->delete();
        ContributorImagesLegalRelease::whereIn('image_id', $ids)->delete();
        ContributorImageSubmissionItem::whereIn('image_id', $ids)->delete();
        return [];
    }

    /* s:delete-all */
    public function delete_all(Request $request)
    {
        $ids = $request->get('ids');
        $ids = explode(",", $ids);
        $user_id = $request->get('user_id');

        // make sure ids for current user only
        $ids = ContributorImage::where('contributor_id', $user_id)
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

        $images = ContributorImage::with('file')->whereIn('id', $ids)
            ->get();

        // delete from s3
        foreach ($images as $image) {
            $orginal_path= $image->large;
            $thumbnail_path= $image->thumbnail;
            $preview_path= $image->preview;
            if($image->file()->exists())
             \dispatch(new \App\Jobs\DeleteImage($image->id));//->onConnection('sync');
             else{
                 if(isset($orginal_path)){
                     if (\Storage::disk('s3')->exists($orginal_path))
                     \Storage::disk('s3')->delete($orginal_path);
                 }
            }

            if(isset($thumbnail_path)){
                if (\Storage::disk('s3')->exists($thumbnail_path))
                \Storage::disk('s3')->delete($thumbnail_path);
            }
            if(isset($preview_path)){
                if (\Storage::disk('s3')->exists($preview_path))
                \Storage::disk('s3')->delete($preview_path);
            }
        }

        ContributorImage::whereIn('id', $ids)->delete();
        ImageTagContributor::whereIn('image_id', $ids)->delete();
        CategoryContributorImage::whereIn('image_id', $ids)->delete();
        ContributorImagesLegalRelease::whereIn('image_id', $ids)->delete();
        ContributorImageSubmissionItem::whereIn('image_id', $ids)->delete();
        return [];
    }
    /* e:delete-all */
    public function options($id, $slug = null)
    {
        // TODO move to headers and middleware
        $user_id = request()->get('user_id');
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);
        $categories = CategoryContributor::query()
            ->orderBy('name')
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                ];
            });

        $releases = LegalRelease::where('contributor_id', 5)
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

        $image_ids = $data->keys()->values();
        $license = $data[$image_ids[0]]['license']??'commercial';

        $imagesChanges = $data
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
        if (count($imagesChanges)) {
            $raw_query = "";
            foreach ($imagesChanges as $id => $params) {
                $column_string = [];
                foreach ($params as $column => $value) {
                    $value = str_replace("'", "\'", $value);
                    $column_string[] = "`$column` = '$value'";
                }
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

        if (count($releasesChanges) || $license === 'editorial') {
            $image_ids =$license === 'editorial'?$data->keys()->values():$releasesChanges->keys();
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

    public function submit(Request $request)
    {
        $user_id = request()->get('user_id');
        $lang = request()->get('lang');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);
        $request_params = json_decode(file_get_contents('php://input'), true);
        $contributor_id = $user_id;

        if(isset($request_params) && @$request_params['noChanges'] === false)
        $this->update_multi($request);

        $ids = explode(",", $request_params['ids']);


        $items =  collect($ids)->map(function ($id) {
            return [ 'image_id' => $id ];
        });
        ContributorImage::whereIn('id', $ids)
            ->update(['contributor_stage' => 2]);

        $submission = ContributorImageSubmission::create([
            'contributor_id' => $contributor_id,
            'type' => $request_params['type'],
            'status' => 'pending',
        ]);

        $submission->items()->createMany(
            $items->toArray()
        );

        return [];
    }

    public function resubmit(Request $request)
    {
        $user_id = request()->get('user_id');
        $contributor_stage = 2;
        $request_params = json_decode(file_get_contents('php://input'), true);
        $contributor_id = 5;

        if(isset($request_params['noChanges']) && $request_params['noChanges'] === false)
         $this->update_multi($request);

        $id = intval($request_params['id']);

        $image = ContributorImage::where('contributor_id', $user_id)
            ->where('id', $id)
            ->firstOrFail('id', $id);

        if(isset($request_params['status_rejected']) && $request_params['status_rejected'] === true)
            $contributor_stage = 6;

        $image->contributor_stage = $contributor_stage;
        $image->save();

        return [];
    }

    public function filters($id, $slug = null)
    {
        // TODO move to headers and middleware
        $user_id = request()->get('user_id');
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

///////////// edit ism
        $categories = CategoryContributor::get()->pluck('name', 'id');

        return [
            'filters' => [
                'type' => $status_list,
                'categories' => $categories,
                'sort_by' => $sort_list,
            ],
        ];
    }

    public function delete_duplicated_tags(){

    }

    public function create_release(Request $request){
        $lang = request()->get('lang', 'en');
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';
        app()->setLocale($lang);
        $extension = 'pdf,jpg,png,jpeg';
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'type' => 'required|string|max:255',
            'file' => 'file|required|mimes:'.$extension.'|min:1|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'name' => 'ValidationError',
                'description' => __('views.files_extension_allowed',['extension'=>$extension]),
                // 'description' => 'Validation error, please try again. If this error persists, please contact the site administrator.',
                'details' => $validator->errors(),
            ], 422);
        }

        $contributor_id = 5;
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
}
