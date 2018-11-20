<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 13/11/17
 * Time: 14.55
 */

require_once "../../../conf/conf.php";
require_once "../../../src/model/DrakkarDbConnector.php";
//require_once "../../../_lib/logger/Logger.php";

require_once "../../../src/model/fpcon.php";


function getDatiPagina($request){

    $conn = \Drakkar\DrakkarDbConnector::connectStatic();
    $fPcon = new \AmicoWinRent\TblBase\FPcon($conn);
    $elencoClienti = $fPcon->findElencoClienti(\AmicoWinRent\TblBase\FPconModel::FETCH_KEYARRAY);
    return json_encode($elencoClienti);
}


$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
