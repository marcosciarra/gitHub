<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 22/11/17
 * Time: 15.17
 */

require_once '../../../conf/conf.php';
require_once '../../../src/function/functionLogin.php';
require_once '../../../src/model/DrakkarTraceLog.php';
require_once '../../../src/model/Login.php';
require_once '../../../src/model/RotazioneLogin.php';
require_once '../../../lib/wsPassword/WsPassword.php';

use Click\Affitti\TblBase\Login;
use Click\Affitti\TblBase\RotazioneLogin;
use WsPassword\WsPassword;

function caricaDati($request)
{
    $con = new \Drakkar\DrakkarDbConnector();
    $result = array();
    $utenti = new Login($con);
    $result['utente'] = $utenti->findByPk($request->id,Login::FETCH_KEYARRAY);

    return json_encode($result);
}

function salvaDati($request) {
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $utente = new Login($con);
        $utente->findByPk($request->object->id);
        $utente->setPassword($request->object->password);
        $date=date_create(date("Y-m-d"));
        date_add($date,date_interval_create_from_date_string("180 days"));
        $utente->setDataScadenza(date_format($date,"Y-m-d"));
        $utente->saveOrUpdate();

        $rotazione = new RotazioneLogin($con);
        $rotazione->setIdLogin($request->object->id);
        $rotazione->setPassword($utente->getPassword());
        $rotazione->setDataInzio(date("Y-m-d"));
        $rotazione->saveOrUpdate();

        $con->commit();
        new \Drakkar\Log\DrakkarTraceLog(getLoginDataFromSession('id',1));

        return json_encode(array('status' => 'ok'));
    }catch(Drakkar\Exception\DrakkarException $e){
        $con->rollBack();
        return $e;
    }
}

function controllaRotazionePwd ($request) {
    $con = new \Drakkar\DrakkarDbConnector();

    $rot = new RotazioneLogin($con);
    $rot->findByIdxIdLoginPassword($request->object->id,Login::encryptPassword($request->object->password));
    if($rot->getId()>0){
        return json_encode(array("status"=>"ko","message"=>"password già utilizzata","pwd"=>$request->object->password));
    }else{
        return json_encode(array("status"=>"ok","message"=>"password utilizzabile","pwd"=>$request->object->password));
    }
}

function verificaVecchiaPassword ($request) {
    $con = new \Drakkar\DrakkarDbConnector();

    if( strcmp (Login::encryptPassword($request->oldPassword),Login::getPasswordByIdUtenteStatic($con,$request->idUtente)) == 0){
        return json_encode(array("status"=>"ok","message"=>"password vecchia corretta"));
    }else{
        return json_encode(array("status"=>"ko","message"=>"password vecchia sbagliata"));
    }
}

function verificaPassword($request){
    echo WsPassword::verificaPassword($request->password);
}

function generaPassword($request)
{
    echo WsPassword::generaPassword();
}

ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;