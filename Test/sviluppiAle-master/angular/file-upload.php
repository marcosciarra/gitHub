<?php
/**
 * Created by PhpStorm.
 * User: clickale
 * Date: 14/10/16
 * Time: 12.31
 */

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

    var_dump($_FILES['file']);

    // uploads file in the folder 
    $temp = explode(".", $_FILES["file"]["name"]);
    $newfilename = substr(md5(time()), 0, 10) . '.' . end($temp);
    move_uploaded_file($_FILES['file']['tmp_name'], 'fileUpload/' . $newfilename);

// give callback to your angular code with the image src name
    echo json_encode($newfilename);
}
?>