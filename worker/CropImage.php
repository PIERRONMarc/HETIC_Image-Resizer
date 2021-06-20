<?php

function kodex_random_string($length){
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for($i=0; $i<$length; $i++){
        $string .= $chars[rand(0, strlen($chars)-1)];
    }
    return $string;
}


function crop_image($path_image){

    $imagick = new Imagick();   
    $imagick->readImage($path_image);
    $imagick->setResolution(72,72);
    $imagick->setImageFormat('jpg');
    $imagick->cropThumbnailImage(100, 100);
    $name = kodex_random_string(7).'-'.time();

    header("Content-Type: image/jpeg");
    $image = $imagick->getImageBlob();
    echo $image;
}


