<?php

define('SYSTEM_PATH', __DIR__ . '/..');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('memory_limit', '-1');
define('PATH', __DIR__ . '/');
define('CACHE_PATH', PATH . 'thumb/');
require SYSTEM_PATH . '/vendor/autoload.php';
$quality = 75;
$allowed_ext = require SYSTEM_PATH . '/config/file.php';
$allowed_ext = $allowed_ext['image'];
$src = !empty($_GET['src']) ? htmlentities($_GET['src']) : "style/cp/assets/app/images/thumbnail.png";
$w = !empty($_GET['w']) ? intval($_GET['w']) : 0;
$h = !empty($_GET['h']) ? intval($_GET['h']) : 0;
if ($src) {
    $file_name = basename($src);
    $file_folder = str_replace($file_name, '', $src);
    $ext = pathinfo($src, PATHINFO_EXTENSION);
    if (empty($ext)) {
        die('Invalid file name.');
    }
    if (!in_array(strtolower($ext), $allowed_ext)) {
        die("\"$ext\" extension not allowed.");
    }
    $path = PATH . $src;
    if ($w or $h) {
        if ($w && $h) $save_folder = "{$w}x{$h}";
        else {
            if ($w) $save_folder = "w{$w}";
            elseif ($h) $save_folder = "h{$h}";
        }
        $save_folder = CACHE_PATH . "{$save_folder}/" . $file_folder;
        if (!file_exists($save_folder))
            @mkdir($save_folder, 0777, true);
        $thumb_path = $save_folder . $file_name;
        if (!file_exists($thumb_path)) {
            if (!file_exists($path)) {
                $path = PATH . 'style/cp/assets/app/images/thumbnail.png';
            }

            $manager = new Intervention\Image\ImageManager(array('driver' => 'gd'));
            try {
                $image = $manager->make($path);
            } catch (Exception $exception) {
                $image = $manager->make(PATH . 'style/cp/assets/app/images/thumbnail.png');
            }
            if ($w && $h)
                $image->fit($w, $h);
            elseif ($w)
                $image->widen($w);
            elseif ($h)
                $image->heighten($h);
            $image->save($thumb_path, $quality);
            $mime = $image->mime();
            $response = $image->encode($ext, $quality);
        } else {
            $response = file_get_contents($thumb_path);
            $mime = mime_content_type($thumb_path);
        }
    } else {
        if (file_exists($path)) {
            $response = file_get_contents($path);
            $mime = mime_content_type($path);
        } else
            die('Invalid file name.');
    }
    header('Content-Type: ' . $mime);
    header('Accept-Ranges: bytes');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: max-age=864000, must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('now +10 days')) . ' GMT');
    echo $response;
    exit;
}
