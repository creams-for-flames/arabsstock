<?php

namespace App;

use App\Models\AdminImageSettings;
use App\Models\AdminVectorSettings;
use App\Models\AdminVideoSettings;
use http\Exception;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Input as Input;
use App\Models\OrderVideo;
use App\Models\OrderItemsVideo;
use Elasticsearch\ClientBuilder;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

DEFINE('DS', DIRECTORY_SEPARATOR);

class Helper
{
    public static function wp_normalize_path($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('|(?<=.)/+|', '/', $path);
        if (':' === substr($path, 1, 1)) {
            $path = ucfirst($path);
        }
        return $path;
    }

    public static function array_equal($a, $b)
    {
        return is_array($a) && is_array($b) && count($a) == count($b) && array_diff($a, $b) === array_diff($b, $a);
    }

    public static function uuid4()
    {
        $uuid4 = Uuid::uuid4();
        return $uuid4->toString();
    }

    public static function mainResponse($status, $message, $data, $code, $key, $validator = null)
    {
        try {
            $result['status'] = $status;
            $result['code'] = $code;
            $result['message'] = $message;

            if ($validator && $validator->fails()) {
                $errors = $validator->errors();
                $errors = $errors->toArray();
                $message = '';
                foreach ($errors as $key => $value) {
                    $message = $value[0] . ',';
                }
                $result['message'] = $message;
                return response()->json($result, $code);
            } elseif (!is_null($data)) {
                if ($status) {
                    if ($data != null && array_key_exists('data', $data)) {
                        $result[$key] = $data['data'];
                    } else {
                        $result[$key] = $data;
                    }
                } else {
                    $result[$key] = $data;
                }
            }
            return response()->json($result, $code);
        } catch (Exception $ex) {
            return response()->json(
                [
                    'line' => $ex->getLine(),
                    'message' => $ex->getMessage(),
                    'getFile' => $ex->getFile(),
                    'getTrace' => $ex->getTrace(),
                    'getTraceAsString' => $ex->getTraceAsString(),
                ],
                $code
            );
        }
    }


    // spaces
    public static function spacesUrlFiles($string)
    {
        return preg_replace('/(\s+)/u', '_', $string);
    }

    public static function spacesUrl($string)
    {
        return preg_replace('/(\s+)/u', '+', trim($string));
    }

    public static function removeLineBreak($string)
    {
        return str_replace(["\r\n", "\r"], "", $string);
    }

    public static function hyphenated($url)
    {
        $url = strtolower($url);
        //Rememplazamos caracteres especiales latinos
        $find = ['á', 'é', 'í', 'ó', 'ú', 'ñ'];
        $repl = ['a', 'e', 'i', 'o', 'u', 'n'];
        $url = str_replace($find, $repl, $url);
        // Añaadimos los guiones
        $find = [' ', '&', '\r\n', '\n', '+'];
        $url = str_replace($find, '-', $url);
        // Eliminamos y Reemplazamos demás caracteres especiales
        $find = ['/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/'];
        $repl = ['', '-', ''];
        $url = preg_replace($find, $repl, $url);
        //$palabra=trim($palabra);
        //$palabra=str_replace(" ","-",$palabra);
        return $url;
    }

    // Text With (2) line break
    public static function checkTextDb($str)
    {
        //$str = trim( self::spaces( $str ) );
        if (mb_strlen($str, 'utf8') < 1) {
            return false;
        }
        $str = preg_replace('/(?:(?:\r\n|\r|\n)\s*){3}/s', "\r\n\r\n", $str);
        $str = trim($str, "\r\n");

        return $str;
    }

    public static function checkText($str)
    {
        //$str = trim( self::spaces( $str ) );
        if (mb_strlen($str, 'utf8') < 1) {
            return false;
        }

        $str = nl2br(e($str));
        $str = str_replace([chr(10), chr(13)], '', $str);

        $str = stripslashes($str);

        return $str;
    }

    public static function formatNumber($number)
    {
        if ($number >= 1000 && $number < 1000000) {
            return number_format($number / 1000, 1) . "k";
        } elseif ($number >= 1000000) {
            return number_format($number / 1000000, 1) . "M";
        } else {
            return $number;
        }
    } //<<<<--- End Function

    public static function formatNumbersStats($number)
    {
        if ($number >= 100000000) {
            return '<span class=".numbers-with-commas counter">' . number_format($number / 1000000, 0) . "</span>M";
        } else {
            return '<span class=".numbers-with-commas counter">' . number_format($number) . '</span>';
        }
    } //<<<<--- End Function

    public static function spaces($string)
    {
        return preg_replace('/(\s+)/u', ' ', $string);
    }

    public static function resizeImage($image, $width, $height, $scale, $imageNew = null, $dpi = false, $quality = 90)
    {
        ini_set('memory_limit', '10000M');

        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($image);
                imagefill($newImage, 0, 0, imagecolorallocate($newImage, 255, 255, 255));
                imagealphablending($newImage, true);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);

                //imagefill( $newImage, 0, 0, imagecolorallocate( $newImage, 255, 255, 255 ) );
                //imagealphablending( $newImage, TRUE );
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);
        // kill source

        imagedestroy($source);

        if ($dpi) {
            imageresolution($newImage, $dpi);
        }
        switch ($imageType) {
            case "image/gif":
                imagegif($newImage, $imageNew);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage, $imageNew, $dpi ? 100 : $quality);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage, $imageNew);
                break;
        }

        chmod($image, 0777);
        return $image;
    }

    public static function resize_image_without_scale($image, $width, $height = 0, $imageNew, $dpi = false, $quality = 90)
    {
        ini_set('memory_limit', '10000M');

        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $newImageWidth = $width;
        $newImageHeight = $height;
        if (!$width && !$height) {
            Log::error('set at least one of $width , $height');
            die('1');
        }

        if ($imageheight == 0) {
            $imageheight = 1;
        }
        if (!$width) {
            $newImageWidth = floor(round($height * $imagewidth / $imageheight, 2));
        }
        if (!$height) {
            $newImageHeight = floor(round($width * $imageheight / $imagewidth, 2));
        }
        $imageType = image_type_to_mime_type($imageType);

        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($image);
                imagefill($newImage, 0, 0, imagecolorallocate($newImage, 255, 255, 255));
                imagealphablending($newImage, true);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);

                //imagefill( $newImage, 0, 0, imagecolorallocate( $newImage, 255, 255, 255 ) );
                //imagealphablending( $newImage, TRUE );
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imagewidth, $imageheight);
        // kill source

        imagedestroy($source);

        if ($dpi) {
            imageresolution($newImage, $dpi);
        }
        switch ($imageType) {
            case "image/gif":
                imagegif($newImage, $imageNew);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage, $imageNew, $dpi ? 100 : $quality);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage, $imageNew);
                break;
        }

        chmod($image, 0777);
        return $image;
    }

    public static function resizeImageFixed($image, $width, $height, $imageNew = null)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        $newImage = imagecreatetruecolor($width, $height);

        switch ($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($image);
                imagefill($newImage, 0, 0, imagecolorallocate($newImage, 255, 255, 255));
                imagealphablending($newImage, true);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);

                /*imagefill( $newImage, 0, 0, imagecolorallocate( $newImage, 255, 255, 255 ) );
                 imagealphablending( $newImage, TRUE );*/
                break;
        }
        if ($width / $imagewidth > $height / $imageheight) {
            $nw = $width;
            $nh = ($imageheight * $nw) / $imagewidth;
            $px = 0;
            $py = ($height - $nh) / 2;
        } else {
            $nh = $height;
            $nw = ($imagewidth * $nh) / $imageheight;
            $py = 0;
            $px = ($width - $nw) / 2;
        }

        imagecopyresampled($newImage, $source, $px, $py, 0, 0, $nw, $nh, $imagewidth, $imageheight);

        switch ($imageType) {
            case "image/gif":
                imagegif($newImage, $imageNew);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage, $imageNew, 90);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage, $imageNew);
                break;
        }

        chmod($image, 0777);
        return $image;
    }


    public static function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['', 'kB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    public static function removeHTPP($string)
    {
        $string = preg_replace('#^https?://#', '', $string);
        return $string;
    }

    public static function Array2Str($kvsep, $entrysep, $a)
    {
        $str = "";
        foreach ($a as $k => $v) {
            $str .= "{$k}{$kvsep}{$v}{$entrysep}";
        }
        return $str;
    }

    public static function removeBR($string)
    {
        $html = preg_replace('[^(<br( \/)?>)*|(<br( \/)?>)*$]', '', $string);
        $output = preg_replace('~(?:<br\b[^>]*>|\R){3,}~i', '<br /><br />', $html);
        return $output;
    }

    public static function removeTagScript($html)
    {
        //parsing begins here:
        $doc = new \DOMDocument();
        @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $nodes = $doc->getElementsByTagName('script');

        $remove = [];

        foreach ($nodes as $item) {
            $remove[] = $item;
        }

        foreach ($remove as $item) {
            $item->parentNode->removeChild($item);
        }

        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html>', '</html>', '<body>', '</body>', '<head>', '</head>', '<p>', '</p>', '&nbsp;'], ['', '', '', '', '', ' '], $doc->saveHtml()));
    } // End Method

    public static function removeTagIframe($html)
    {
        //parsing begins here:
        $doc = new \DOMDocument();
        @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $nodes = $doc->getElementsByTagName('iframe');

        $remove = [];

        foreach ($nodes as $item) {
            $remove[] = $item;
        }

        foreach ($remove as $item) {
            $item->parentNode->removeChild($item);
        }

        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html>', '</html>', '<body>', '</body>', '<head>', '</head>', '<p>', '</p>', '&nbsp;'], ['', '', '', '', '', ' '], $doc->saveHtml()));
    } // End Method

    public static function fileNameOriginal($string)
    {
        return pathinfo($string, PATHINFO_FILENAME);
    }

    public static function formatDate($date)
    {
        $day = date('d', strtotime($date));
        //		$_month = date('m', strtotime($date));
        $month = date('m', strtotime($date)); //trans("months.$_month");
        $year = date('Y', strtotime($date));

        $dateFormat = $day . ' / ' . $month . ' / ' . $year;

        return $dateFormat;
    }

    public static function watermark($name, $watermarkSource)
    {
        $thumbnail = \Image::make($name);
        $watermark = \Image::make($watermarkSource);
        if ($thumbnail->height() > $thumbnail->width()) {
            $watermark_width = $thumbnail->width();
            $watermark_height = round(($watermark_width * $watermark->height()) / $watermark->width(), 2);
        } else {
            $watermark_height = $thumbnail->height();
            $watermark_width = round(($watermark_height * $watermark->width()) / $watermark->height(), 2);
        }
        $watermark->resize($watermark_width, $watermark_height);
        $thumbnail->insert($watermark, 'center');
        $thumbnail->save($name)->destroy();
        $watermark->destroy();
    }

    public static function init_elasticsearch()
    {
        // $logger = new \Monolog\Logger('elasticsearch');
        // $log_level = config('app.debug') == true ? \Monolog\Logger::DEBUG : \Monolog\Logger::WARNING;
        // $logger->pushHandler(new \Monolog\Handler\StreamHandler(base_path('storage/logs/elasticsearch.log'), $log_level));
        $config = config('services.elasticsearch');
        $client = ClientBuilder::create()
            ->setHosts([$config['endpoint']])
            ->setSSLVerification(false)
            ->setBasicAuthentication($config['user'], $config['password'])
            ->build();
        return $client;
    }

    public static function search_in_elasticsearch($type, $query, $filters = [], $size = null)
    {
//        try {
        $options = Helper::select_type($type);
        $class_name = $options['class_name'];
        $page = intval(Input::get('page', 1)) ?: 1;
        $settings = $options['settings'];
        // paginations
        if (is_null($size)) {
            $from = ($page - 1) * $settings->result_request;
            $size = $settings->result_request;
        } else {
            $from = ($page - 1) * $size;
        }
        $from = intval($from);

        if (isset($filters['sort_categories_at_last'])) {
            $results = self::search_in_elasticsearch_items($type, $query, [], $size, $page);
            $total = $results['total'];
            $not_in_categories = self::search_in_elasticsearch_items($type, $query, ['not_in_categories' => $filters['sort_categories_at_last']], $size, $page);
            $not_in_categories_pages_count = ceil($not_in_categories['total'] / $size);
            $items = $not_in_categories['items'];
            if ($page > (ceil($not_in_categories['total'] / $size))) {
                $items = self::search_in_elasticsearch_items($type, $query, ['in_categories' => $filters['sort_categories_at_last']], $size, $page - $not_in_categories_pages_count)['items'];
            }
        } else {
            $items = self::search_in_elasticsearch_items($type, $query, $filters, $size, $page);
            $total = $items['total'];
            $items = $items['items'];
        }
        if (count($items)) {
            $ids = array_unique($items);
            $ids_ordered = implode(',', $ids);
            $items = $class_name::tinySelection()->withoutGlobalScope('default_loaded_relations')->whereIn('id', $ids)->orderBy(\DB::raw("FIELD(id, $ids_ordered)"))->with('category')->get();
            $items = collect(array_fill(0, $from, 0))
                ->toBase()
                ->merge($items);
            // we use the sortBy method from collection class
            $paginated = \App\CollectionHelper::paginate($items, $total, $size);

        } else {
            $paginated = \App\CollectionHelper::paginate(new Collection([]), 0, $size);
        }

        return $paginated;
//        } catch (\Throwable $th) {
//            \Log::error("search_in_elasticsearch: {$th}");
//            if ($search_type) {
//                return [];
//            }
//            return  $paginated = \App\CollectionHelper::paginate(new Collection([]), 0, $size);
//
//        }


    }

    public static function search_in_elasticsearch_items($type, $query, $filters = [], $size = null, $page = 1)
    {
        $query = mb_strtolower($query);
        if (!isset($filters['not_in_ids'])) {
            $filters['not_in_ids'] = [];
        } else {
            $temp_array1 = array_map(function ($item) {
                return $item . "ar";
            }, $filters['not_in_ids']);
            $temp_array2 = array_map(function ($item) {
                return $item . "en";
            }, $filters['not_in_ids']);
            $filters['not_in_ids'] = array_merge($temp_array1, $temp_array2);
        }
        $options = Helper::select_type($type);
        $elasticsearch_index = $options['elasticsearch_index'];
        $elasticsearch_filter = $options['elasticsearch_filter'];

        $settings = $options['settings'];
        $client = Helper::init_elasticsearch();
        // paginations
        if (is_null($size)) {
            $from = ($page - 1) * $settings->result_request;
            $size = $settings->result_request;
        } else {
            $from = ($page - 1) * $size;
        }
        $from = intval($from);
        $options_s =
            [
                'match' => [
                    "full_text_boosted" => [
                        "query" => $query,
                        "operator" => "and",
                        // "fuzziness"=> 1,
                        // "minimum_should_match"=> 3

                    ],
                    "full_text_boosted" => [
                        "query" => $query,
                        "operator" => "or",
                        // "fuzziness"=> 1,
                        // "minimum_should_match"=> 3

                    ]
                ]
            ];

        $filters_s = [
            "match" => ["type" => $elasticsearch_filter],
            "match" => ["full_text" => $query],
        ];

        // search
        $params = [
            'index' => $elasticsearch_index,
            'body' => [
                'from' => $from,
                'size' => $size,
                "track_total_hits" => true,
                '_source' => ['full_text', 'search_result_data.*'],
                'query' => [
                    'function_score' => [
                        'query' => [
                            "bool" => [
                                "must" => $options_s,
                                "filter" => [
                                    "bool" => [
                                        "must" => $filters_s

                                    ]
                                ],
                                "must_not" => [
                                    "ids" => [
                                        "values" => $filters['not_in_ids'],
                                    ],
                                ],
                            ],
                        ],
                        'random_score' => ["seed" => crc32(\Session::getId())],
                    ]
                ],
            ],
        ];
        if (isset($filters['not_in_categories']))
            $params['body']['post_filter'] = [
                'bool' => [
                    'must_not' => [
                        [
                            'terms' => [
                                'categories' => is_array($filters['not_in_categories']) ? $filters['not_in_categories'] : [$filters['not_in_categories']]
                            ]
                        ]
                    ]
                ]
            ];
        if (isset($filters['sort_categories_at_last']))
            $params['body']['post_filter'] = [
                'bool' => [
                    'must_not' => [
                        [
                            'terms' => [
                                'categories' => is_array($filters['sort_categories_at_last']) ? $filters['sort_categories_at_last'] : [$filters['sort_categories_at_last']]
                            ]
                        ]
                    ]
                ]
            ];
        if (isset($filters['in_categories']))
            $params['body']['post_filter'] = [
                'bool' => [
                    'must' => [
                        [
                            'terms' => [
                                'categories' => is_array($filters['in_categories']) ? $filters['in_categories'] : [$filters['in_categories']]
                            ]
                        ]
                    ]
                ]
            ];
        $results = $client->search($params);
        $items = collect($results['hits']['hits'])
            ->pluck('_source.search_result_data')
            ->map(function ($item) {
                return $item['id'];
            })
            ->toArray();
        return [
            'total' => $results['hits']['total']['value'],
            'items' => $items,
        ];
    }

    public static function similar_search_in_elasticsearch($type, $query, $filters = [], $size = null)
    {
        try {
            $query = mb_strtolower($query);
            // prepare filters
            if (!isset($filters['not_in_ids'])) {
                $filters['not_in_ids'] = [];
            } else {
                $temp_array1 = array_map(function ($item) {
                    return $item . "ar";
                }, $filters['not_in_ids']);
                $temp_array2 = array_map(function ($item) {
                    return $item . "en";
                }, $filters['not_in_ids']);
                $filters['not_in_ids'] = array_merge($temp_array1, $temp_array2);
            }

            $options = Helper::select_type($type);
            $elasticsearch_index = $options['elasticsearch_index'];
            $elasticsearch_filter = $options['elasticsearch_filter'];
            $class_name = $options['class_name'];
            $settings = $options['settings'];

            $client = Helper::init_elasticsearch();

            $page = intval(Input::get('page', 1)) ?: 1;
            // paginations
            if (is_null($size)) {
                $from = ($page - 1) * $settings->result_request;
                $size = $settings->result_request;
            } else {
                $from = ($page - 1) * $size;
            }
            $from = intval($from);


            // search
            $params = [
                'index' => $elasticsearch_index,
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    "track_total_hits" => true,
                    'query' => [
                        'function_score' => [
                            'query' => [
                                "bool" => [
                                    "must" => [

                                        'multi_match' => [
                                            'fields' => ['full_text_boosted'],
                                            'type' => 'most_fields',
                                            'query' => $query,
                                            // "analyzer"=> "rebuilt_arabic"

                                        ],
                                    ],
                                    "filter" => [
                                        ["match" => [
                                            "type" => $elasticsearch_filter,
                                        ]],
                                        ["terms" => ["completion_terms" => explode(" ", $query)]]
                                    ],
                                    "must_not" => [
                                        "ids" => [
                                            "values" => $filters['not_in_ids'],
                                        ],
                                    ],
                                ],
                            ],
                            //  'random_score' => ["seed" => crc32(\Session::getId())],
                        ]
                    ],

                ],
            ];
            $results = $client->search($params);
            /* s:min_score */
            $params = [
                'index' => $elasticsearch_index,
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    "track_total_hits" => true,
                    'query' => [
                        'function_score' => [
                            'query' => [
                                "bool" => [
                                    "must" => [

                                        'multi_match' => [
                                            'fields' => ['full_text_boosted'],
                                            'type' => 'most_fields',
                                            'query' => $query,

                                        ],
                                    ],
                                    "filter" => [
                                        ["match" => [
                                            "type" => $elasticsearch_filter,
                                        ]],
                                        ["terms" => ["completion_terms" => explode(" ", $query)]]
                                    ],
                                    "must_not" => [
                                        "ids" => [
                                            "values" => $filters['not_in_ids'],
                                        ],
                                    ],
                                ],
                            ],
                            "score_mode" => "max",
                            "min_score" => $results['hits']['max_score'] / 5,

                        ]
                    ],
                    "sort" => [
                        "_score" => ["order" => "desc"],
                        "search_result_data.id" => ["order" => "desc"],
                    ],
                ],
            ];
            $results = $client->search($params);
            $items = collect($results['hits']['hits'])
                ->pluck('_source.search_result_data')
                ->map(function ($item) {
                    return $item['id'];
                })
                ->toArray();
            $ids_ordered = implode(',', $items);
            if (count($items)) {
                $dataHaveCategoryEM = $class_name::whereIn('id', $items);
                switch ($type) {
                    case 'images':
                        $category_id = 84;
                        $dataHaveCategoryEM = $dataHaveCategoryEM->whereHas('category', function ($query) use ($category_id) {
                            $query->whereIn('image_categories.id', [$category_id]);
                        });
                        break;
                    case 'vectors':
                        $category_id = 76;
                        $dataHaveCategoryEM = $dataHaveCategoryEM->whereHas('category', function ($query) use ($category_id) {
                            $query->whereIn('vector_categories.id', [$category_id]);
                        });
                        break;
                    case 'videos':
                        $category_id = 71;
                        $dataHaveCategoryEM = $dataHaveCategoryEM->whereHas('category', function ($query) use ($category_id) {
                            $query->whereIn('video_categories.id', [$category_id]);
                        });
                        break;
                }
                $dataHaveCategoryEM = $dataHaveCategoryEM->get()->pluck('id')->toArray();
                if (count($dataHaveCategoryEM)) {
                    $flattened_array = array_diff($items, $dataHaveCategoryEM);
                    $result = array_merge($flattened_array, $dataHaveCategoryEM);
                    $ids_ordered = implode(',', $result);

                    $items = $class_name::whereIn('id', $result)->orderBy(DB::raw("FIELD(id, $ids_ordered)"))
                        ->with('category');
                } else {

                    $items = $class_name::whereIn('id', $items)->orderBy(DB::raw("FIELD(id, $ids_ordered)"))->with('category');
                }

                $items = $items->get();
                $items = collect(array_fill(0, $from, 1))
                    ->toBase()
                    ->merge($items);
                $total = $results['hits']['total']['value'];
                // we use the sortBy method from collection class
                $paginated = \App\CollectionHelper::paginate($items, $total, $size);

            } else {
                $paginated = \App\CollectionHelper::paginate(new Collection([]), 0, $size);
            }

            return $paginated;

        } catch (\Throwable $th) {
            \Log::error("similar_search_in_elasticsearch: {$th}");
            return $paginated = \App\CollectionHelper::paginate(new Collection([]), 0, $size);

        }

    }

    public static function select_type($type)
    {
        $data['settings'] = image_settings();

        switch ($type) {
            case 'images':
                $data['elasticsearch_index'] = $type;
                $data['elasticsearch_filter'] = 'image';
                $data['class_name'] = '\App\Models\Image';
                break;
            case 'videos':
                $data['elasticsearch_index'] = $type;
                $data['elasticsearch_filter'] = 'video';
                $data['class_name'] = '\App\Models\Video';
                break;
            case 'vectors':
                $data['elasticsearch_index'] = $type;
                $data['elasticsearch_filter'] = 'vector';
                $data['class_name'] = '\App\Models\Vector';
                break;
            default:
                $data['elasticsearch_index'] = "images";
                $data['elasticsearch_filter'] = 'image';
                $data['class_name'] = '\App\Models\Image';
                break;
        }
        return $data;
    }

    public static function check_spelling_in_elasticsearch($type, $query)
    {
        $query = strtolower($query);
        if ($type === 'images') {
            $elasticsearch_index = $type;
            $elasticsearch_filter = 'image';
            $settings = image_settings();
        } elseif ($type === 'videos') {
            $elasticsearch_index = $type;
            $elasticsearch_filter = 'video';
            $settings = video_settings();
        } elseif ($type === 'vectors') {
            $elasticsearch_index = $type;
            $elasticsearch_filter = 'vector';
            $settings = vector_settings();
        }

        $client = Helper::init_elasticsearch();

        // paginations
        $page = intval(Input::get('page', 1));

        $from = ($page - 1) * $settings->result_request;
        $size = $settings->result_request;

        // spell suggest
        $params = [
            'index' => $elasticsearch_index,
            'body' => [
                'suggest' => [
                    'spelling-suggest' => [
                        'text' => $query,
                        'term' => [
                            'field' => 'suggestion_terms',
                            'prefix_length' => 0,
                            /* 'analyzer' => 'LanguageAnalyzers.Arabic', */
                        ],
                    ],
                ],
            ],
        ];

        $results = $client->search($params);
        $term_alternative = "";
        $items = collect($results['suggest']['spelling-suggest'])
            ->keyBy('text')
            ->filter(function ($item) {
                return count($item['options']) > 0;
            })
            ->map(function ($item) use ($query, $term_alternative) {
                return $item['options'][0]['text'];
            })
            ->toArray();

        $term_alternative = str_replace(array_keys($items), array_values($items), $query);

        return $term_alternative;
    }

    public static function autocomplete_in_elasticsearch($type, $query)
    {
        if ($type === 'images') {
            $elasticsearch_index = $type;
            $elasticsearch_filter = 'image';
            $settings = image_settings();
        } elseif ($type === 'videos') {
            $elasticsearch_index = $type;
            $elasticsearch_filter = 'video';
            $settings = video_settings();
        } elseif ($type === 'vectors') {
            $elasticsearch_index = $type;
            $elasticsearch_filter = 'vector';
            $settings = vector_settings();
        }

        $client = Helper::init_elasticsearch();

        // paginations
        $page = Input::get('page', 1);

        $from = ($page - 1) * $settings->result_request;
        $size = $settings->result_request;

        // auto complete
        $params = [
            'index' => $elasticsearch_index,
            'body' => [
                '_source' => 'no_source',
                'suggest' => [
                    'auto-complete-suggest' => [
                        'prefix' => $query,
                        'completion' => [
                            'field' => 'completion_terms',
                            'fuzzy' => [
                                'fuzziness' => 3,
                            ],
                            'size' => 10,
                            'skip_duplicates' => true,
                        ],
                    ],
                ],
            ],
        ];
        $results = $client->search($params);

        $items = collect($results['suggest']['auto-complete-suggest'][0]['options'])
            ->pluck('text')
            ->toArray();

        // if no autocomplete, try for spell checking
        if (count($items) === 0) {
            $term_alternative = \App\Helper::check_spelling_in_elasticsearch($type, $query);
            if ($term_alternative !== $query) {
                $items[] = $term_alternative;
            }
        }

        return $items;
    }

    //resize and crop image by center by ahed
    public static function resize_crop_image($source_file, $width, $height, $dst_dir)
    {
        try {
            $imgsize = getimagesize($source_file);
            $w = $imgsize[0];
            $h = $imgsize[1];
            $mime = $imgsize['mime'];
        } catch (\Throwable $th) {
            //throw $th;
            \Log::error("error getimagesize msg: {$th->getMessage()} , File: {$th->getFile()} Line:{$th->getLine()}  file_path: {$source_file} ");

        }

        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image_out = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image_out = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image_out = "imagejpeg";
                $quality = 80;
                break;

            default:
                return false;
                break;
        }
        $image = $image_create($source_file);
        $w = @imagesx($image); //current width
        $h = @imagesy($image); //current height
        if (!$w || !$h) {
            $GLOBALS['errors'][] = 'Image could not be resized because it was not a valid image.';
            return false;
        }
        if ($w == $width && $h == $height) {
            return $image;
        } //no resizing needed

        //try max width first...
        $ratio = $width / $w;
        $new_w = $width;
        $new_h = $h * $ratio;

        //if that created an image smaller than what we wanted, try the other way
        if ($new_h < $height) {
            $ratio = $height / $h;
            $new_h = $height;
            $new_w = $w * $ratio;
        }

        $image2 = imagecreatetruecolor($new_w, $new_h);
        imagecopyresampled($image2, $image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

        //check to see if cropping needs to happen
        if ($new_h != $height || $new_w != $width) {
            $image3 = imagecreatetruecolor($width, $height);
            if ($new_h > $height) {
                //crop vertically
                $extra = $new_h - $height;
                $x = 0; //source x
                $y = round($extra / 2); //source y
                imagecopyresampled($image3, $image2, 0, 0, $x, $y, $width, $height, $width, $height);
            } else {
                $extra = $new_w - $width;
                $x = round($extra / 2); //source x
                $y = 0; //source y
                imagecopyresampled($image3, $image2, 0, 0, $x, $y, $width, $height, $width, $height);
            }
            imagedestroy($image2);
            $image_out($image3, $dst_dir);
            return true;
        } else {
            $image_out($image2, $dst_dir);
            return true;
        }
    }

    public static function isValidTelephoneNumber(string $telephone, int $minDigits = 9, int $maxDigits = 14): bool
    {
        if (preg_match('/^[+][0-9]/', $telephone)) { //is the first character + followed by a digit
            $count = 1;
            $telephone = str_replace(['+'], '', $telephone, $count); //remove +
        }

        //remove white space, dots, hyphens and brackets
        $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone);

        //are we left with digits only?
        return self::isDigits($telephone, $minDigits, $maxDigits);
    }

    private static function isDigits(string $s, int $minDigits = 9, int $maxDigits = 14): bool
    {
        return preg_match('/^[0-9]{' . $minDigits . ',' . $maxDigits . '}\z/', $s);
    }

    public static function getHeight($image)
    {
        $size = getimagesize($image);
        $height = intval($size[1]);
        return $height;
    }

    public static function getWidth($image)
    {
        $size = getimagesize($image);
        $width = intval($size[0]);
        return $width;
    }


    public static function getLicenses()
    {
        $licenses_key = ["commercial", "editorial"];
        $licenses = [];
        foreach ($licenses_key as $key => $value) {
            $license = new \stdClass();
            $license->name = $value;
            $license->title = __("global.{$value}");
            $licenses[$value] = $license;
        }

        return $licenses;

    }

    public static function ElasticSearchSql($index_name, $q)
    {
        try {
            $client = ClientBuilder::create()->build();
            $sql = "SELECT search_result_data.id FROM $index_name WHERE MATCH(full_text, '*{$q}*')
               LIMIT 10000";
            $params = [
                'body' => [
                    'query' => $sql
                ]
            ];
            // send the request to the _sql endpoint
            return $response = $client->sql()->query($params);
            $flattened_array = call_user_func_array('array_merge', $response['rows']);
            return $flattened_array;
        } catch (\Throwable $th) {
            return [];
            //throw $th;
        }

    }
} //<--- End Class

class CollectionHelper
{
    public static function paginate(Collection $results, $total, $pageSize)
    {
        $page = Paginator::resolveCurrentPage('page');

        return self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param \Illuminate\Support\Collection $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected static function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact('items', 'total', 'perPage', 'currentPage', 'options'));
    }
}

function cdn_path($path = '')
{
    if ($path && mb_substr($path, 0, 1) !== "/") {
        $path = "/" . $path;
    }
    return 'https://arabsstock.fra1.digitaloceanspaces.com' . $path;
}

function create_chunk_by_strlen($len)
{
    return function ($data) use ($len) {
        for ($i = 0; $i <= count($data); $i++) {
            if (mb_strlen(implode('', array_slice($data->toArray(), 0, $i))) > $len) {
                return $data->chunk($i);
            }
        }

        return collect([$data->toArray(), []]);
    };
}
