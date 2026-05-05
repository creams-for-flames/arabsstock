<?php

$images = [
  'https://arabsstock.fra1.digitaloceanspaces.com/uploads/small/1586974011v9oti.jpg',
];

foreach($images as $image) {

    $seqments = explode('/', $image);
    $file_name = "./small/" . array_pop($seqments);
    file_put_contents($file_name, file_get_contents($image));

    // path to image
    $image = $file_name;
    list($imagewidth, $imageheight, $imageType) = getimagesize($image);
    $imageType = image_type_to_mime_type($imageType);
    switch($imageType) {
        case "image/gif":
            $source=imagecreatefromgif($image);
            break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
            $source=imagecreatefromjpeg($image);
            break;
        case "image/png":
        case "image/x-png":
            $source=imagecreatefrompng($image);
            break;
      }
    imageresolution($source, 300);
    switch($imageType) {
        case "image/gif":
              imagegif( $source, $image );
            break;
          case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
              imagejpeg( $source, $image ,100 );
            break;
        case "image/png":
        case "image/x-png":
            imagepng( $source, $image );
            break;
    }

}

// then in shell run
// # s3cmd setacl s3://arabsstock/uploads/small --acl-public --recursive
