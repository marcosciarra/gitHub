<?php

if ( !empty( $_FILES ) ) {

    $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];

    $fileStructure = explode('.',$_FILES[ 'file' ][ 'name' ]);
    $name = $fileStructure[0];
    $ext = $fileStructure[1];

    $dateTime = date("YmdHis");

    $fileName = $name.'_'.$dateTime.'.'.$ext;

    //$uploadPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $fileName;

    move_uploaded_file( $_FILES["file"]["tmp_name"],"/var/www/html/upload/". $fileName);

    /*
    $answer = array( 'answer' => 'File transfer completed');
    $json = json_encode( $answer );
    echo $json;
    */

    echo $fileName;

} else {

    echo 'No files';

}

?>