<?php

if (!empty($_FILES)) {

    require_once '../../conf/conf.php';

    $tempPath = $_FILES['file']['tmp_name'];
    $filePath = $_FILES['file']['name'];

    //Leggo estensione
    $ext = explode('.', $filePath);
    $num=count($ext)-1;

    $fileOriginale = $ext[0];
    $ext = $ext[$num];
    sleep(1);
    //Imposto il nome come timestamp
    $nomeFile = date('Ymdhis');
    $uploadPath =
        $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . CLIENTE .
        DIRECTORY_SEPARATOR . 'upload' .
        DIRECTORY_SEPARATOR . 'temp' .
        DIRECTORY_SEPARATOR . $nomeFile . '.' . $ext;
    move_uploaded_file($tempPath, $uploadPath);

    $answer = array('answer' => 'File transfer completed', 'fileName' => $nomeFile . '.' . $ext, 'fileNameOriginale' => $fileOriginale);
    $json = json_encode($answer);

    echo $json;

}