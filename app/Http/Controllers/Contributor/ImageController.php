<?php

namespace App\Http\Controllers\Contributor;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ContributorImage;


class ImageController extends Controller
{
    public function index()
    {

    }

    public function create()
    {
        return view('contributor.image.filemanger.create');
    }

    public function check_unique(Request $request)
    {
        // check image is not duplicate to same user
        $files = $request->get('files');
        $file_names = collect($files)->map(function ($item) {
            return $item[0];
        });
        $file_hashes = collect($files)->map(function ($item) {
            return $item[1];
        });

        $images = ContributorImage::query();

        $file_name_conflicts = $images->whereIn('original_name', $file_names)
            ->pluck('original_name')
            ->unique()
            ->values()
            ->map(function($filename) use ($files) {
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

    public function Newstore(Request $request)
    {
        \Log::channel('info')->info('Image contributor Start-1');
        $photos = [];

        if (Auth::guest()) {
            return response()->json([
                'session_null' => true,
                'success' => false,
            ]);
        }
        $path_thumbnail = public_path('uploads/thumbnail/');

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $filed_array = [];
            $total_filed= 0;
            $total_files= count($files);

            foreach ($files as $i => $value) {
                $extension = $value->getClientOriginalExtension();
                $originalName = $value->getClientOriginalName(); // TODO add validation
                $type_mime_img = $value->getMimeType();
                $sizeFile = $value->getSize();
                $file_hash = md5_file($value->path());
                $name = str_random(20) . '.' . $extension;
                $thumbnail = strtolower(
                    \Illuminate\Support\Str::slug($request->title_en, '-') .
                    '-' .
                    time() .
                    str_random(5) .
                    '.' .
                    $extension
                );

                // if ($value->move($temp, $large)) { // بتخزنها ملف ال temp باسم ال large

                //     $original = $temp . $large;
                //      $width = getWidth($original);
                //     $height = getHeight($original);

                //     $_width = $width > $height ? 390 : 0;
                //     $_height = $width > $height ? 0  : 390;
                //     Helper::resize_image_without_scale(
                //         $original,
                //         $_width,
                //         $_height,
                //         $temp . $thumbnail
                //     );
                //     $thumbnail_dimension = $path_thumbnail . $thumbnail;

                // }


                $sql=New ContributorImage();
                $sql->title_ar = time() . str_random(10);
                $sql->title_en = time() . str_random(10);
                $sql->user_id = Auth::user()->id;
                $sql->original_name =$originalName;
                $sql->thumbnail =$thumbnail;
                $sql->hash=$file_hash;
                $sql->save();

                $dir=public_path(DS . 'uploads' . DS . 'contributor_images' . DS . $sql->id );
                $orginal_folder = public_path(DS . 'uploads' . DS . 'contributor_images' . DS . $sql->id .DS. 'orginal');
                $thumbnail_folder = public_path(DS . 'uploads' . DS . 'contributor_images' . DS . $sql->id .DS. 'thumbnail');
                \Log::channel('info')->info($orginal_folder);
                \Log::channel('info')->info($thumbnail_folder);

                if (!file_exists($orginal_folder)) {
                    mkdir($orginal_folder, 0755, true);
                    mkdir($thumbnail_folder, 0755, true);
                }



                $value->move($orginal_folder, $name);

                $original=$orginal_folder . DS . $name;
                $width = getWidth($original);
                $height = getHeight($original);
                $_width = $width > $height ? 640 : 0;
                $_height = $width > $height ? 0  : 640;
                $uploaded = Helper::resize_image_without_scale(
                        $original,
                        $_width,
                        $_height,
                        $thumbnail_folder . DS . $thumbnail
                    );

                \Session::flash('success_message', trans('admin.success_add'));

                // $pathS3 = Storage::disk('s3')->put($nameFile, file_get_contents(public_path() . DS . $nameFile));// thmbmial
                // $pathS3 = Storage::disk('s3')->put($nameFile, file_get_contents(public_path() . DS . $nameFile)); // orginal

                $this->deleteDir($dir);


            $photo_object = new \stdClass();
            $photo_object->name = $originalName;
            $photo_object->textStatus = 'done';

            $photo_object->fileID = 1;
            $photos[] = $photo_object;
            return response()->json(['files' => $photos], 200);

            }
        }

    }

    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function edit()
        {
            $routes = [
                'options' => route('admin.api.images.options'),
                'filters' => route('admin.api.images.filters'),
                'index' => route('admin.api.images.index'),
                'update_multi' => route('admin.api.images.update_multi'),
            ];

            $html_breadcrumbs = [
                'title' => __('views.ImagesWarehouse'),
                'subtitle' => __('views.Index'),
            ];

            return view('contributor.image.filemanger.edit',
            compact(
                'html_breadcrumbs',
                'routes'
            ));
        }
    // public function check_unique()
    // {
    //     return [
    //         'file_name_conflicts' => [],
    //         'file_hash_conflicts' => [],
    //     ];
    // }
}
