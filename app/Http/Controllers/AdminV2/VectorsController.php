<?php

namespace App\Http\Controllers\AdminV2;

use App\Helper;
use App\Jobs\StoreImgInS3;
use App\Jobs\SetTagsByComputerVision;
use App\Models\AdminCollection;
use App\Models\AdminCollectionVector;
use App\Models\ImagesReported;
use App\Models\VectorCategory;
use App\Models\VectorFolder;
use App\Models\Notifications;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoryVector;
use App\Models\Vector;
use App\Models\StockVector;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use League\ColorExtractor\Color;
use App\Contexts\Test;

class VectorsController extends Controller
{
    public function getDimension($path, $type)
    {
        if ($type == 'width') {
            $width = getWidth($path);
            return $width;
        } else {
            $height = getHeight($path);
            return $height;
        }
    }

    public function index()
    {


        $index_url = route('admin.vectors.datatable');
        $edit_url = route('admin.vectors.edit', 0);
        $destroy_url = route('admin.vectors.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.vectors'),
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
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.admin_collections.update', 0),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ];
                }),
            ],
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2_vectors.vectors.index',
            compact(
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'edit_url',
                'is_vectors_site',
                'destroy_url'
            )
        );
    }

    public function datatable(Request $request)
    {

        $query = Vector::where('status', 'active')->with(
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
                    $query->where('title_en', 'like', '%' . $search . '%')
                        ->orWhere('title_ar', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function destroy(Request $request, $id)
    {
        // TODO all multiple delete
        $id = explode(',', $id)[0];
        $image = Image::find($id);

        // Delete Notification
//        $notifications = Notifications::where('destination', $id)
//            ->where('type', '2')
//            ->orWhere('destination', $id)
//            ->where('type', '3')
//            ->orWhere('destination', $id)
//            ->where('type', '6')
//            ->get();
//
//        if (isset($notifications)) {
//            foreach ($notifications as $notification) {
//                $notification->delete();
//            }
//        }
//
//        // Collection Image
//        $collectionsImages = CollectionImage::where(
//            'image_id',
//            '=',
//            $id
//        )->get();
//        if (isset($collectionsImages)) {
//            foreach ($collectionsImages as $collectionsImage) {
//                $collectionsImage->delete();
//            }
//        }
//
//        // Image Reported
//        $imagesReporteds = ImagesReported::where('image_id', '=', $id)->get();
//        if (isset($imagesReporteds)) {
//            foreach ($imagesReporteds as $imagesReported) {
//                $imagesReported->delete();
//            }
//        }
//
//        // ALL RESOLUTIONS IMAGES
//        $stocks = Stock::where('image_id', '=', $id)->get();
//
//        foreach ($stocks as $stock) {
//            $stock_path = public_path(
//                'uploads/' . $stock->type . '/' . $stock->name
//            );
//
//            // Delete Stock
//            if (\File::exists($stock_path)) {
//                \File::delete($stock_path);
//            }
//
//            $stock->delete();
//        }
//
//        $preview_image = public_path('uploads/preview/' . $image->preview);
//        $thumbnail = public_path('uploads/thumbnail/' . $image->thumbnail);
//
//        // Delete preview
//        if (\File::exists($preview_image)) {
//            \File::delete($preview_image);
//        }
//
//        // Delete thumbnail
//        if (\File::exists($thumbnail)) {
//            \File::delete($thumbnail);
//        }

        $image->delete();

        \Artisan::call('stock:resort');
        cache()->clear();
        return redirect()->route('admin.images.index');
    }

    public function edit($id)
    {

        $data = Vector::findOrFail($id);
        $title = __('تعديل صورة');
        $categoris = CategoryVector::where('vector_id', $id)->pluck(
            'category_id'
        );
       $all_categories = \App\Models\VectorCategory::orderBy('name_en')->get();
        $tags_ar = $data->tags()->where('local', 'ar')->pluck('title');
        $tags_en = $data->tags()->where('local', 'en')->pluck('title');

        $html_breadcrumbs = [
            'title' => __('Vectors'),
            'subtitle' => __('views.Edit'),
        ];
        $is_vectors_site = true;
        return view('admin_v2_vectors.vectors.edit', compact(
            'html_breadcrumbs',
            'data',
            'is_vectors_site',
            'categoris',
            'all_categories',
            'tags_ar',
            'tags_en',
            'title'
        ));
    }

    public function update(Request $request)
    {

        $sql = Vector::with('tags')->find($request->id);

        $rules = [
            'title_ar' => 'required|min:3',
            'title_en' => 'required|min:3',
            'slug' => 'required|min:3|regex:/^[A-Za-z0-9\_-]+$/i',
            'description_ar' => 'sometimes|min:2',
            'description_en' => 'sometimes|min:2',
        ];


        if ($request->featured == 'yes' && $sql->featured == 'no') {
            $featuredDate = \Carbon\Carbon::now();
        } elseif ($request->featured == 'yes' && $sql->featured == 'yes') {
            $featuredDate = $sql->featured_date;
        } else {
            $featuredDate = '';
        }

        $this->validate($request, $rules);

        $slug = "vector-{$request->id}-" . $request->get('slug');
        if ($sql->slug !== strtolower($slug)) {
            $from_url = route('photo.show', $sql->slug);
            $to_url = route('photo.show', strtolower($slug));

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

        $sql->title_ar = $request->title_ar;
        $sql->title_en = $request->title_en;
        $sql->slug = $slug;
        $sql->description_ar = $request->description_ar;
        $sql->description_en = $request->description_en;
        $sql->in_home = $request->in_home;
        $sql->status = $request->status;
        $sql->featured = $request->featured;
        $sql->featured_date = $featuredDate;
        $sql->how_use_vector = $request->how_use_vector;
        $sql->attribution_required = $request->attribution_required;


        $sql->save();
        if ($request->categories_id) {
            $check = CategoryVector::where('vector_id', $request->id)->delete();
            for ($i = 0; $i < count($request->categories_id); $i++) {
                $categoryImages = CategoryVector::create([
                    'vector_id' => $request->id,
                    'category_id' => $request->categories_id[$i],
                ]);
            }
        }

        $this->setTags($request->id, $request->get('tag_en', []), 'en');

        $this->setTags($request->id, $request->get('tag_ar', []), 'ar');

        \Session::flash('success_message', trans('admin.success_update'));

        return redirect()->route('admin.vectors.index');
    }

    public function activate(Request $request, $id)
    {

        $id = explode(',', $id);
        Vector::whereIn('id', $id)->update(['status' => $request->get('status')]);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->back();
    }

    public function add_to_admin_collection(Request $request, $id)
    {
        $id = explode(',', $id);
        $updates = collect($id)->map(function ($item) use ($request) {
            return [
                'image_id' => $item,
                'admin_collection_id' => $request->get('status'),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ];
        })->toArray();
        // TODO remove duplicated
        AdminCollectionImage::insert($updates);

        \Session::flash('success', trans('misc.success_update'));
        return redirect()->back();
    }

    private function setTags(int $image_id, array $tag_list, string $lang)
    {
        VectorTag::where('vector_id', $image_id)
            ->where('local', $lang)
            ->delete();

        collect($tag_list)
            ->map(function ($item) use ($image_id, $lang) {
                return [
                    'vector_id' => $image_id,
                    'tag' => $item,
                    'confidence' => 0,
                    'local' => $lang,
                    'slug' => get_soundex($item),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            })
            ->when(count($tag_list) > 0, function ($data) {
                VectorTag::insert($data->toArray());
            });
    }

    public function index_pending()
    {

        $index_url = route('admin.vectors.pending.datatable');
        $edit_url = route('admin.vectors.edit', 0);
        $destroy_url = route('admin.vectors.destroy', 0);

        $html_breadcrumbs = [
            'title' => __('views.VectorsPending'),
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
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'pending',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.vectors.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'add_to_collection' => [
                'type' => 'dropdown',
                'text' => __('views.AddToCollection'),
                'options' => AdminCollection::all()->map(function ($item) {
                    return [
                        'text' => $item->title,
                        'value' => $item->id,
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.vectors.admin_collections.update', 0),
                        'method' => 'post',
                        // TODO update text
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ];
                }),
            ],
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2_vectors.vectors.pending.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url', 'is_vectors_site')
        );
    }

    public function datatable_pending(Request $request)
    {


        $query = Vector::where('status', 'pending')->with(
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
                    $query->where('title_en', 'like', '%' . $search . '%')
                        ->orWhere('title_ar', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }


    public function create_filemanger()
    {

        //     $pricesClass = new Test();
        //    $prices = $pricesClass->getPrices();
        // return $prices;
        // $path= public_path('/uploads/1603399789eyaeu.jpg');
        // return $path;

        // try {
        //         $image = new \Imagick();
        //         $image->pingImage($path);
        //         $pages = $image->getNumberImages();
        //     } catch (\Exception $e) {
        //         echo 'Caught exception: ',  $e->getMessage(), "\n";
        //     }


        $category = VectorCategory::get();
        $collection = AdminCollection::where('status', '1')->get();
        $folders = VectorFolder::withCount('vectors')->orderBy('vectors_count', 'desc')->pluck('folder');

        $html_breadcrumbs = [
            'title' => __('views.VectorFilemanger'),
            'subtitle' => __('views.New'),
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2_vectors.vectors.filemaneger.create',
            compact('html_breadcrumbs', 'category', 'collection', 'folders', 'is_vectors_site')
        );
    }

    public function store_filemanger(Request $request)
    {


        // dd($request->all());


        \Log::channel('info')->info('Vector Start1');
        $photos = [];


        if (Auth::guest()) {
            return response()->json([
                'session_null' => true,
                'success' => false,
            ]);
        }


        // PATHS
        $temp = public_path('temp/');
        $path_large = public_path('uploads/large/');
        $watermarkSource = public_path('img/watermark.png');
        $path_preview = public_path('uploads/preview/');

        $input = $request->all();

        // dd($input);

        // $path = getcwd().'uploads/Autumn14.eps';
        // $save_path =public_path("uploads/Autumn15.png");
        // $image = new \Imagick($path);
        // $image->readimage($path);
        // $image->setBackgroundColor(new ImagickPixel('transparent'));
        // $image->setResolution(300,300);
        // $image->scaleImage(600, 270);
        // $image->setImageFormat("png");
        // $image->writeImage($save_path);

        // return $image;


        if ($request->hasFile('files')) {
            $xy = $request->file('files');
            $filed_array = [];
            $total_filed = 0;
            $total_files = count($xy);
            foreach ($xy as $i => $value) {

                $extension = $value->getClientOriginalExtension();
                if ($extension != 'eps') {
                    $extension = $value->getClientOriginalExtension();
                    $originalName = $value->getClientOriginalName(); // TODO add validation
                    $type_mime_img = $value->getMimeType();
                    $sizeFile = $value->getSize();
                    $file_hash = md5_file($value->path());
                    $large = strtolower(time() . str_random(5) . '.' . $extension);
                    $thumbnail = '';
                    $preview = '';
                    $description_ar = '';

                    $preview = strtolower(
                        \Illuminate\Support\Str::slug($request->title_en, '-') .
                        '-' .
                        time() .
                        str_random(5) .
                        '.' .
                        $extension
                    );
                    $thumbnail = strtolower(
                        \Illuminate\Support\Str::slug($request->title_en, '-') .
                        '-' .
                        time() .
                        str_random(5) .
                        '.' .
                        $extension
                    );

                    $large_dimension = '';
                    $preview_dimension = '';
                    $thumbnail_dimension = '';

                    if ($value->move($temp, $large)) {
                        set_time_limit(0);

                        $original = $temp . $large;

                        $width = getWidth($original);
                        $height = getHeight($original);

                        if ($width > $height) {
                            if ($width > 1280):
                                $_scale = 1280;
                            else:
                                $_scale = 900;
                            endif;


                        } else {
                            if ($width > 1280):
                                $_scale = 960;
                            else:
                                $_scale = 800;
                            endif;


                        }

                        // PREVIEW
                        $_width = $width > $height ? 640 : 0;
                        $_height = $width > $height ? 0 : 640;
                        $uploaded = Helper::resize_image_without_scale(
                            $original,
                            $_width,
                            $_height,
                            $temp . $preview
                        );

                        // Thumbnail
                        $_width = $width > $height ? 390 : 0;
                        $_height = $width > $height ? 0 : 390;
                        Helper::resize_image_without_scale(
                            $original,
                            $_width,
                            $_height,
                            $temp . $thumbnail
                        );

                        Helper::watermark($temp . $preview, $watermarkSource);
                    } else {
                        array_push($filed_array, $originalName);
                        $total_filed += 1;
                    }

                    if (!empty($request->description_ar)) {
                        $description_ar = Helper::checkTextDb(
                            $request->description_ar
                        );
                    } else {
                        $description_ar = '';
                    }

                    if (!empty($request->description_en)) {
                        $description_en = Helper::checkTextDb(
                            $request->description_en
                        );
                    } else {
                        $description_en = '';
                    }

                    $exif_data = @exif_read_data($temp . $large, 0, true);

                    if (isset($exif_data['EXIF']['ISOSpeedRatings'][0])) {
                        $ISO = 'ISO ' . $exif_data['EXIF']['ISOSpeedRatings'][0];
                    }

                    if (isset($exif_data['EXIF']['ExposureTime'])) {
                        $ExposureTime = $exif_data['EXIF']['ExposureTime'] . 's';
                    }

                    if (isset($exif_data['EXIF']['FocalLength'])) {
                        $FocalLength =
                            round($exif_data['EXIF']['FocalLength'], 1) . 'mm';
                    }

                    if (isset($exif_data['COMPUTED']['ApertureFNumber'])) {
                        $ApertureFNumber =
                            $exif_data['COMPUTED']['ApertureFNumber'];
                    }

                    if (!isset($FocalLength)) {
                        $FocalLength = '';
                    }

                    if (!isset($ExposureTime)) {
                        $ExposureTime = '';
                    }

                    if (!isset($ISO)) {
                        $ISO = '';
                    }

                    if (!isset($ApertureFNumber)) {
                        $ApertureFNumber = '';
                    }

                    $exif =
                        $FocalLength .
                        ' ' .
                        $ApertureFNumber .
                        ' ' .
                        $ExposureTime .
                        ' ' .
                        $ISO;

                    if (isset($exif_data['IFD0']['Model'])) {
                        $camera = $exif_data['IFD0']['Model'];
                    } else {
                        $camera = '';
                    }

                    //Colors
                    $palette = Palette::fromFilename(
                        public_path('temp/' . $preview)
                    );

                    $extractor = new ColorExtractor($palette);

                    // it defines an extract method which return the most “representative” colors
                    $colors = $extractor->extract(5);

                    // $palette is an iterator on colors sorted by pixel count
                    foreach ($colors as $color) {
                        $_color[] = trim(Color::fromIntToHex($color), '#');
                    }

                    $colors_image = implode(',', $_color);

                    $large_dimension = $path_large . $large;
                    $preview_dimension = $path_preview . $preview;

                } else {
                    $extension = $value->getClientOriginalExtension();
                    $originalName_eps = $value->getClientOriginalName(); // TODO add validation
                    $file_hash_eps = md5_file($value->path());
                    $name_eps = strtolower(time() . str_random(5) . '.' . $extension);
                    $thumbnail = '';
                    $preview = '';
                    $large = '';

                    if (!empty($request->description_ar)) {
                        $description_ar = Helper::checkTextDb(
                            $request->description_ar
                        );
                    } else {
                        $description_ar = '';
                    }

                    if (!empty($request->description_en)) {
                        $description_en = Helper::checkTextDb(
                            $request->description_en
                        );
                    } else {
                        $description_en = '';
                    }

                    $colors_image = '';
                    $camera = '';
                    $exif = '';
                    $originalName = '';
                    $file_hash = '';
                    $large = '';
                    $lSize = '';

                }

                $token_id = str_random(200);

                $sql = new Vector();
                $sql->thumbnail = $thumbnail;
                $sql->preview = $preview;
                $sql->large = $large;
                $sql->title_ar = time() . str_random(10);
                $sql->title_en = time() . str_random(10);
                $sql->description_ar = trim($description_ar);
                $sql->description_en = trim($description_en);
                $sql->user_id = Auth::user()->id;
                $sql->status = 'pending';
                $sql->token_id = $token_id;
                $sql->extension = strtolower($extension);
                $sql->colors = $colors_image;
                $sql->exif = trim($exif);
                $sql->camera = $camera;
                $sql->how_use_vector = 'free';
                $sql->attribution_required = 'no';
                $sql->original_name = $originalName_eps;
                $sql->hash = $file_hash_eps;
                $sql->name_eps = $name_eps;
                $sql->is_uploaded = 1;

                $sql->original_name_eps = $originalName;
                $sql->hash_eps = $file_hash;


                if ($extension != 'eps') {

                    $imageID = $sql->id;

                    if ($request->get('categories_id')) {
                        $cateogries = $request->get('categories_id');

                        for ($i = 0; $i < count($cateogries); $i++) {
                            $categoryImages = CategoryVector::create([
                                'vector_id' => $imageID,
                                // 'vector_id' => $vectorID,
                                'category_id' => $cateogries[$i],
                            ]);
                        }
                    }

                    if ($request->get('collection_id')) {
                        AdminCollectionVector::create([
                            'vector_id' => $imageID,
                            'admin_collection_id' => $request->get('collection_id'),
                        ]);
                    }

                    if ($request->get('folder')) {
                        $folder_id = VectorFolder::firstOrCreate(['folder' => $request->get('folder')])->id;
                        $sql->folder_id = $folder_id;
                    }
                    $sql->save();

                    $lResolution = list($w, $h) = getimagesize($temp . $large);
                    $lSize = Helper::formatBytes(filesize($temp . $large), 1);

                    $stockImages = [
                        [
                            'name' => $large,
                            'type' => 'large',
                            'resolution' => $w . 'x' . $h,
                            'size' => $lSize,
                        ],
                    ];

                    foreach ($stockImages as $key) {
                        $stock = new StockVector();
                        $stock->vector_id = $imageID;
                        $stock->name = $key['name'];
                        $stock->type = $key['type'];
                        $stock->extension = $extension;
                        $stock->resolution = $key['resolution'];
                        $stock->size = $key['size'];
                        $stock->token = $token_id;
                        $stock->save();
                    }


                    \File::copy($temp . $preview, $path_preview . $preview);
                    \File::delete($temp . $preview);

                    \File::copy($temp . $large, $path_large . $large);
                    \File::delete($temp . $large);

                }


                $img = Vector::with('stock')->findOrFail($sql->id);


                if ($extension != 'eps') {

                    $sql->width_large = $this->getDimension(
                        $large_dimension,
                        'width'
                    );
                    $sql->height_large = $this->getDimension(
                        $large_dimension,
                        'height'
                    );

                    $sql->width_preview = $this->getDimension(
                        $preview_dimension,
                        'width'
                    );
                    $sql->height_preview = $this->getDimension(
                        $preview_dimension,
                        'height'
                    );


                    $sql->save();
                    \Log::channel('info')->info('count vector' . count($img->stock));
                    foreach ($img->stock as $imgItem) {
                        dispatch(new StoreImgInS3($imgItem->id))->onConnection(
                            'redis'
                        );
                    }


                    // generate tags (must start after StoreImgInS3)
                    dispatch(
                        new SetTagsByComputerVision([
                            'vector_id' => $sql->id,
                        ])
                    )->onQueue('media_default');

                    \Session::flash('success_message', trans('admin.success_add'));


                    $photo_object = new \stdClass();
                    $photo_object->name = $originalName;
                    $photo_object->textStatus = 'done';

                    $photo_object->fileID = 1;
                    $photos[] = $photo_object;

                    \Artisan::call('stock:resort');
                }


            }
            //enf foreach


            return response()->json(['files' => $photos], 200);

        }
    }


    public function check_unique(Request $request)
    {

        $files = $request->get('files');
        $file_names = collect($files)->map(function ($item) {
            return $item[0];
        });
        $file_hashes = collect($files)->map(function ($item) {
            return $item[1];
        });

        $images = Vector::query();

        $folder = $request->get('folder');
        if ($folder) {
            $folder_id = VectorFolder::firstOrCreate(['folder' => $folder])->id;
            $images->where('folder_id', $folder_id);
        }

        $file_name_conflicts = $images->whereIn('original_name', $file_names)
            ->pluck('original_name')
            ->unique()
            ->values()
            ->map(function ($filename) use ($files) {
                return collect($files)->map(function ($item) {
                    return [$item[0], $item[0]];
                })->filter(function ($item) use ($filename) {
                    return $filename === $item[1];
                })->values()->flatten()->toArray()[0];
            })
            ->toArray();

        $file_hash_conflicts = $images->whereIn('hash', $file_hashes)
            ->pluck('hash')
            ->unique()
            ->values()
            ->toArray();

        return [
            'file_name_conflicts' => $file_name_conflicts,
            'file_hash_conflicts' => $file_hash_conflicts,
        ];
    }

    public function index_warehouse(Request $request)
    {

        $routes = get_vue_image_store_routes()['images']['arabsstock'];

        $html_breadcrumbs = [
            'title' => __('views.ImagesWarehouse'),
            'subtitle' => __('views.Index'),
        ];

        $user = Auth::user()->only(
            'id',
            'email',
            'api_token'
        );
        $is_vectors_site = true;
        return view(
            'admin_v2.image.warehouse.index',
            compact(
                'html_breadcrumbs',
                'routes',
                'is_vectors_site',
                'user'
            )
        );
    }
}
