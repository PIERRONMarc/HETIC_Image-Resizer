<?php

// BDD

function dbConnect(){
    
    try
   {
        
   $dsn = 'mysql:dbname=image_resizer;host=localhost';
   $user = 'root';
   $password = 'root';
   
      $db = new PDO($dsn, $user, $password);
       return $db;
   }
   catch(Exception $e)
   {
       die('Erreur : '.$e->getMessage());
   }
   
}


function uploadAvatar($name,$path){

    $db = dbConnect();

    $req_sql = 'INSERT INTO `Avatar` (`Name`, `Path`, `Created_at`, `Updated_at`) VALUES ("'.$name.'", "'.$path.'", "'.date('Y-m-d').'", "'.date('Y-m-d').'")' ;
    $prepare = $db->prepare($req_sql);   
    $res = $prepare ->execute();

    if($res == true){

        $req_sql = 'SELECT id FROM Avatar WHERE name = "'.$name.'" ;';
        $req_sql = $db->query($req_sql);
        $resultat = $req_sql->fetch();
        return $resultat;

    }else{

        return false;
    }
}

function updateAvatarUser($id_user,$id_avatar){
    $db = dbConnect();
    $req_sql = 'UPDATE `User` SET `idAvatar` = "'.$id_avatar.'" ,  `Updated_at` = "'.date('Y-m-d').'" WHERE Id = "'.$id_user.'";';
    $prepare = $db->prepare($req_sql);   
    $res = $prepare ->execute();
    
    return $res;
}

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
    $imagick->readImageBlob($path_image);
    $imagick->setResolution(72,72);
    $imagick->setImageFormat('jpg');
    $imagick->cropThumbnailImage(100, 100);
    $name = kodex_random_string(7).'-'.time();

    //header("Content-Type: image/jpeg");
    $image = $imagick->getImageBlob();
    return $image;
}

function traitement_image($flux){

$flux_tab = explode(';',$flux);
$id_user = $flux_tab[0];
$image_base64 = $flux_tab[1];

$imageBlob = base64_decode($image_base64);
$imageCrop = crop_image($imageBlob);
$name = kodex_random_string(5).'-'.kodex_random_string(5).'-'.kodex_random_string(5).'-'.kodex_random_string(5).'.jpg' ;
$path = '/home/storage/avatar/';
file_put_contents($name,$imageCrop);

$idAvatar = uploadAvatar($name,$path.$name)['id'];
if(!empty($idAvatar)){
    updateAvatarUser($id_user,$idAvatar);
}

}


traitement_image(file_get_contents('rabbitmq-message.txt'));








