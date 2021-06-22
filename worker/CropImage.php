<?php

// BDD

function dbConnect(){
    
    try
   {
        
   $dsn = 'mysql:dbname=resizer;host=db';
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

    $req_sql = 'INSERT INTO `Avatar` (`Name`, `Path`, `Created_at`) VALUES (:name, :path, :createdAt)' ;
    $prepare = $db->prepare($req_sql);   
    $res = $prepare ->execute([
        'name' => $name,
        'path' => $path,
        'createdAt' => date('Y-m-d'),
    ]);

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

function upload_on_ftp($imageName)
{
    try {
        $ftp = ftp_connect('storage');
        if (false === $ftp) {
            throw new Exception('Unable to connect');
        }

        $loggedIn = ftp_login($ftp,  'storage',  'storage');
        if (true === $loggedIn) {
            $to = $imageName;
            $from = 'tmp/'.$imageName;
            ftp_put($ftp, $to, $from);
        } else {
            throw new Exception('Unable to log in');
        }

        ftp_close($ftp);
    } catch (Exception $e) {
        echo "Failure: " . $e->getMessage();
    }
}

function traitement_image($flux){

    $message = json_decode($flux, TRUE);

    $imageBlob = base64_decode($message['image_base64']);

    $imageCrop = crop_image($imageBlob);
    $name = kodex_random_string(5).'-'.kodex_random_string(5).'-'.kodex_random_string(5).'-'.kodex_random_string(5).'.jpg' ;
    $path = '/home/storage/avatar/';
    file_put_contents("tmp/".$name,$imageCrop);
    upload_on_ftp($name);
    unlink("tmp/".$name);


    $idAvatar = uploadAvatar($name,$path.$name)['id'];
    if(!empty($idAvatar)){
        updateAvatarUser($message['user_id'],$idAvatar);
    }

}








