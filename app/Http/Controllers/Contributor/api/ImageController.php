<?php

namespace App\Http\Controllers\Contributor\api;

use App\Models\ImageCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ContributorImage;
use App\Models\ImageTagContributor;
use App\Models\CategoryContributorImage;
class ImageController extends Controller
{

    public function index(Request $request)
   {
      $images = ContributorImage::where('contributor_id','50')->where('contributor_stage','0');

        $page = $request->get('page');
        $perpage = $request->get('perpage');
        $offset = ($page - 1) * $perpage;
        $count = $images->count();
        $images->offset($offset)->limit($perpage);

        $lang = app()->getLocale();
        $data = $images
            ->get()
            ->map(function ($image) {
                return collect([
                    'id' => $image->id,
                    'thumbnail' => "/uploads/contributor_images/".$image->id."/". $image->thumbnail,
                    'height_thumbnail' => $image->height_thumbnail,
                    'width_thumbnail' => $image->width_thumbnail,
                    'preview' => "/uploads/preview/" . $image->preview,
                    'title_ar' => $image->title_ar,
                    'title_en' => $image->title_en,
                    'original_name' => $image->original_name,
                    'stage_edit' => $image->stage_edit,
                    'contributor_stage' => $image->contributor_stage,

                    'tags_ar' => [],
                    'tags_en' => [],
                    'category_ids' => [],

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

       return [
           'filters' => [
               'type' => $status_list,
               'categories' => $categories,
            //    'collection' => $collections,
            //    'folder' => $folders,
               'sort_by' => $sort_list,
           ],
       ];
   }

   public function options($id, $slug = null)
   {
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

       return [
           'options' => [
               'categories' => $categories,
           ],
       ];
   }

   public function update_multi(Request $request)
    {

        $request_params = json_decode(file_get_contents('php://input'), true);


        $options = $request_params['options'];

        $data = collect($request_params['data'])->map(function ($item) {
            $all = [];
            foreach ($item as $key => $value) {
                $all = array_merge($all, $item[$key]);
            }
            return $all;
        });

        if(count($data)){
            foreach($data as $id => $item){
                $contributorImages=ContributorImage::findOrFail($id);
                $contributorImages->title_en=$item['title_en'];
                $contributorImages->title_ar=$item['title_ar'];
                $contributorImages->contributor_stage=1;
                $contributorImages->save();
            }
        }

        $image_ids = $data->keys()->values();






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
                \DB::unprepared($raw_query);
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
                \DB::unprepared($raw_query);
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
                \DB::unprepared($raw_query);
            }
        }

        $this->delete_duplicated_tags();

        return [];
    }

    public function delete_duplicated_tags()
    {
        $data = \DB::select("
            select image_id, tag from ( SELECT image_id, tag, count(*) as c FROM `image_tags_contributor` GROUP by image_id, tag HAVING c > 1  ) as t1
            ");

        foreach ($data as $item) {
            $first_id = \DB::table('image_tags_contributor')
                ->where('image_id', $item->image_id)
                ->where('tag', $item->tag)
                ->first()->id;

            \DB::table('image_tags_contributor')
                ->where('id', $first_id)
                ->delete();
        }
    }



}
