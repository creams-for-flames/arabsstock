<?php

namespace App\Http\Controllers\AdminV2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
     ContributorVectorSubmissionItem
    ,ContributorVectorSubmission
    ,CategoryContributor
    ,LegalRelease
    ,ContributorVector
    ,CategoryContributorVector
    ,ContributorVectorLegalRelease
    ,VectorTagContributor

};
use Illuminate\Http\Exceptions\HttpResponseException;
class ContributorVectorController extends Controller
{
    public function index(Request $request)
    {
        $user_id = request()->get('user_id');
        $stage = intval($request->get('stage'));
        if ($stage === 1) {
            $contributor_stages = [0];
        } else if ($stage === 2) {
            $contributor_stages = [1, 2,6];
        } else if ($stage === 3) {
            $contributor_stages = [3, 4, 5,8];
        }
        $vectors = ContributorVector::where('contributor_id', $user_id)->whereIn('contributor_stage', $contributor_stages);

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
                    ->pluck('tag');
                return $vector;
            })
            ->map(function ($vector) {
                $vector->tags_en = $vector->tags
                    ->filter(function ($tag) {
                        return $tag->local === 'en';
                    })
                    ->pluck('tag');
                return $vector;
            })
            ->map(function ($vector) use ($lang) {
                $vector->category_ids = $vector->category->map(function ($item) use ($lang) {
                    return [
                        'id' => $item->id,
                        'label' => $item->name,
                    ];
                });
                return $vector;
            })

             ->map(function ($vector) {
                 return collect([
                     'id' => $vector->id,
                     'thumbnail' => $vector->is_uploaded === 1?"/{$vector->thumbnail}":asset('img/default2.jpg'),
                     'height_thumbnail' => $vector->height_thumbnail,
                     'width_thumbnail' => $vector->width_thumbnail,
                     'preview' =>$vector->is_uploaded ? $vector->preview:asset('img/default2.jpg'),
                     'title_ar' => $vector->title_ar,
                     'title_en' => $vector->title_en,
                     'status' => $vector->status,
                     'original_name' => $vector->original_name,
                     'stage_edit' => $vector->stage_edit,
                     'contributor_stage' => $vector->contributor_stage,
                     'review_notes' => $vector->review_notes,
                     'tags_ar' => $vector->tags_ar,
                     'tags_en' => $vector->tags_en,
                     'category_ids' => $vector->category_ids,
                     'release_ids' => $vector->release_ids,
                     'is_uploaded'=>$vector->is_uploaded ?__('Uploaded'):__('Uploading'),
                     'post_link' =>$vector->file && $vector->contributor_stage === 8?$vector->file->post_link:null,
                     'status_file_rejected_publish' =>$vector->file?(($vector->file->status === "pending" && \in_array($vector->contributor_stage,[4,3]))?TRUE:FALSE):FALSE,
                     'action_delete'=>(!isset($vector->file) && \in_array($vector->contributor_stage,[0,1,3,4]))?TRUE:FALSE,
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
        $user_id = request()->get('user_id');
        $ids = $request->get('ids');
        $ids = explode(",", $ids);

        // make sure ids for current user only
        $ids = ContributorVector::where('contributor_id', $user_id)
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
        $vectors = ContributorVector::whereIn('id', $ids)
            ->get();

        // delete from s3
        foreach ($vectors as $vector) {
            $orginal_path= $vector->large;
            $thumbnail_path= $vector->thumbnail;
            $preview_path= $vector->preview;

        if (\Storage::disk('s3')->exists($orginal_path))
            \Storage::disk('s3')->delete($orginal_path);

        if (\Storage::disk('s3')->exists($thumbnail_path))
            \Storage::disk('s3')->delete($thumbnail_path);

        if (\Storage::disk('s3')->exists($preview_path))
            \Storage::disk('s3')->delete($preview_path);
            
        }

        ContributorVector::whereIn('id', $ids)
            ->delete();

        VectorTagContributor::whereIn('vector_id', $ids)
            ->delete();

        CategoryContributorVector::whereIn('vector_id', $ids)->delete();
        ContributorVectorLegalRelease::whereIn('vector_id', $ids)->delete();
        ContributorVectorSubmissionItem::whereIn('vector_id', $ids)->delete();
        return [];
    }
    public function delete_all(Request $request)
    {
        $user_id = request()->get('user_id');
        $ids = $request->get('ids');
        $ids = explode(",", $ids);

        // make sure ids for current user only
        $ids = ContributorVector::where('contributor_id', $user_id)
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
        $vectors = ContributorVector::whereIn('id', $ids)
            ->get();

        // delete from s3
        foreach ($vectors as $vector) {
            $orginal_path= $vector->large;
            $thumbnail_path= $vector->thumbnail;
            $preview_path= $vector->preview;
            if($vector->file()->exists())
            \dispatch(new \App\Jobs\DeleteVector($vector->id));//->onConnection('sync');
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

        ContributorVector::whereIn('id', $ids)
            ->delete();

        VectorTagContributor::whereIn('vector_id', $ids)
            ->delete();

        CategoryContributorVector::whereIn('vector_id', $ids)->delete();
        ContributorVectorLegalRelease::whereIn('vector_id', $ids)->delete();
        ContributorVectorSubmissionItem::whereIn('vector_id', $ids)->delete();
        return [];
    }
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

        $releases = LegalRelease::where('contributor_id', $user_id)
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id' => $item->id,
                    'label' => $item->name,
                    'file' => $item->file,
                ];
            });


        return [
            'options' => [
                'categories_admin' => [],
                'categories' => $categories,
                'releases' => $releases,
            ],
        ];
    }

    public function update_multi(Request $request)
    {
        $user_id = request()->get('user_id');

            $request_params = json_decode(file_get_contents('php://input'), true);


        $options = $request_params['options'];

        $data = collect($request_params['data'])->map(function ($item) {
            $all = [];
            foreach ($item as $key => $value) {
                $all = array_merge($all, $item[$key]);
            }
            return $all;
        });

        $vector_ids = $data->keys()->values();

        $vectorsChanges = $data
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
        if (count($vectorsChanges)) {
            $raw_query = "";
            foreach ($vectorsChanges as $id => $params) {
                $column_string = [];
                foreach ($params as $column => $value) {
                    $value = str_replace("'", "\'", $value);
                    $column_string[] = "`$column` = '$value'";
                }
                $raw_query .= "UPDATE `contributor_vectors` SET" . implode(',', $column_string) . " WHERE `contributor_vectors`.`id` = $id;";
            }

            \DB::unprepared($raw_query);
        }

        if (count($tagsArChanges)) {
            $vector_ids = $tagsArChanges->keys();
            if ($options['tags_ar_delete_old']) {
                VectorTagContributor::whereIn('vector_id', $vector_ids)
                    ->where('local', 'ar')
                    ->delete();
            }

            $raw_query = "INSERT INTO `vectors_tags_contributor` (`vector_id`, `tag`, `slug`, `local`) VALUES ";
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
            $vector_ids = $tagsEnChanges->keys();
            if ($options['tags_en_delete_old']) {
                VectorTagContributor::whereIn('vector_id', $vector_ids)
                    ->where('local', 'en')
                    ->delete();
            }

            $raw_query = "INSERT INTO `vectors_tags_contributor` (`vector_id`, `tag`, `slug`, `local`) VALUES ";
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
            $vector_ids = $categoriesChanges->keys();
            if ($options['category_ids_delete_old']) {
                CategoryContributorVector::whereIn('vector_id', $vector_ids)->delete();
            }

            $raw_query = "INSERT INTO `category_contributor_vector` (`vector_id`, `category_id`) VALUES ";
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

        if (count($releasesChanges)) {
            $vector_ids = $releasesChanges->keys();
            if ($options['release_ids_delete_old']) {
                ContributorVectorLegalRelease::whereIn('vector_id', $vector_ids)->delete();
            }

            $raw_query = "INSERT INTO `contributor_vector_legal_release` (`vector_id`, `release_id`) VALUES ";
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

        $this->delete_duplicated_tags();

        return [];
    }

    public function submit(Request $request)
    {
        $user_id = request()->get('user_id');
        $request_params = json_decode(file_get_contents('php://input'), true);
        $contributor_id = $user_id;

        if(isset($request_params) && @$request_params['noChanges'] === false)
        $this->update_multi($request);

        $ids = explode(",", $request_params['ids']);

        // make sure ids for current user only
        $ids = ContributorVector::where('contributor_id', $user_id)
            ->whereIn('id', $ids)
            ->whereColumn('title_ar', '!=','title_en')
            ->pluck('id');

            if(count($ids) === 0){
            return $res = response()->json([
            'status'=>422,
            'message'=>__('views.you_must_enter_an_arabic_title_or_an_english_title_and_send_content')
            ]);
            }

        ContributorVector::whereIn('id', $ids)
            ->update(['contributor_stage' => 2]);

        $submission = ContributorVectorSubmission::create([
            'contributor_id' => $contributor_id,
            'type' => $request_params['type'],
            'status' => 'pending',
        ]);

        $items =  collect($ids)->map(function ($id) {
            return ['vector_id' => $id];
        });
        $submission->items()->createMany(
            $items->toArray()
        );


        return [];
    }

    public function resubmit(Request $request)
    {
        $user_id = request()->get('user_id');
        $request_params = json_decode(file_get_contents('php://input'), true);
        $contributor_stage = 2;

        if(isset($request_params) && $request_params['noChanges'] === false)
        $this->update_multi($request);

        $contributor_id = $user_id;

        $id = intval($request_params['id']);

        $vector = ContributorVector::where('contributor_id', $user_id)
            ->where('id', $id)
            ->firstOrFail('id', $id);

        if(isset($request_params['status_rejected']) && $request_params['status_rejected'] === true)
        $contributor_stage = 6;

        $vector->contributor_stage = $contributor_stage;
        $vector->save();

        return [];
    }

    public function filters($id, $slug = null)
    {
        $user_id = request()->get('user_id');
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

    public function delete_duplicated_tags(){

    }

    public function create_release(Request $request){
        $user_id = request()->get('user_id');

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'type' => 'required|string|max:255',
            'file' => 'file|required|min:1|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'name' => 'ValidationError',
                'description' => 'Validation error, please try again. If this error persists, please contact the site administrator.',
                'details' => $validator->errors(),
            ], 422);
        }

        $contributor_id = $user_id;
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
