<?php

require_once '../../../conf/conf.php';
require_once '../../../src/function/functionLogin.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';
require_once '../../../src/model/DrakkarTraceLog.php';
require_once '../../../src/model/Login.php';
require_once '../../../src/model/RotazioneLogin.php';
require_once '../../../lib/wsPassword/WsPassword.php';

use Click\Affitti\TblBase\Login;
use Click\Affitti\TblBase\RotazioneLogin;
use WsPassword\WsPassword;

use Drakkar\DrakkarPdo;

function caricaDati($request)
{
    $con = new \Drakkar\DrakkarDbConnector();
    $result = array();
    $utenti = new Login($con);
    if($request->id > 0){
        $result['utente'] = $utenti->findByPk($request->id,Login::FETCH_KEYARRAY);
    }else{
        $result['utente'] = $utenti->getEmptyDbKeyArray();
    }
    return json_encode($result);
}

function salvaDati($request) {
    try {
        $result = array();
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $utente = new Login($con);
        $result['eseguiLogout'] = false;

        if(!is_null($request->object->id)){
            // modifica utente
            $utente->findByPk($request->object->id);
            $utente->setNome($request->object->nome);
            $utente->setCognome($request->object->cognome);
            $utente->setEmail($request->object->email);
            $utente->setTipoUtente($request->object->tipo_utente);
        }else{
            // nuovo utente
            $utente->creaObjJson($request->object, true);
        }

        if($request->salvaPwd) {
            $utente->setPassword($request->object->password);
            $date = date_create(date("Y-m-d"));
            //se modifica aggiungo 180gg alla scadenza
            //se nuovo pwd scade oggi -> costretto a cambiarla al primo accesso
            if(!is_null($request->object->id)) {
                date_add($date, date_interval_create_from_date_string("180 days"));
            }
            $utente->setDataScadenza(date_format($date, "Y-m-d"));
        }

        $utente->saveOrUpdate();

        if($request->salvaPwd) {
            $rotazione = new RotazioneLogin($con);
            if(!is_null($request->object->id)) {
                $rotazione->setIdLogin($request->object->id);
            }else{
                $rotazione->setIdLogin($utente->getId());
            }
            $rotazione->setPassword($utente->getPassword());
            $rotazione->setDataInzio(date("Y-m-d"));
            $rotazione->saveOrUpdate();

            $result['eseguiLogout'] = true;
        }

        $result['idUtenteLoggato'] = getLoginDataFromSession('id');
        $result['tipoUtenteLoggato'] = getLoginDataFromSession('tipoUtente');

        $con->commit();
        new \Drakkar\Log\DrakkarTraceLog(getLoginDataFromSession('id',1));
        $result['status'] = 'ok';

        if(
            $request->object->id == getLoginDataFromSession('id') &&
            $request->salvaPwd
        ) {
            session_unset();
            session_regenerate_id();
            session_destroy();
        }

        return json_encode($result);
    }catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    }
    catch (\Drakkar\Exception\DrakkarIntegrityConstrainException $e){
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::integrityConstrainException($e));
    }
    catch (\Drakkar\Exception\DrakkarException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}

function controllaRotazionePwd ($request) {
    $con = new \Drakkar\DrakkarDbConnector();

    $rot = new RotazioneLogin($con);
    $rot->findByIdxIdLoginPassword($request->object->id, Login::encryptPassword($request->object->password));
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

function modificaTipo($request){
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $utenti = new Login($con);
        $utenti->findByPk($request->id);
        $utenti->setTipoUtente($request->tipoUtente);
        //$utenti->saveOrUpdate();
        $utenti->saveOrUpdateAndLog(getLoginDataFromSession('id'));
        $con->commit();

        return $utenti->getId();
    }catch(Drakkar\Exception\DrakkarException $e){
        $con->rollBack();
        return $e;
    }
}

function gestisciStatoUtente($request) {
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $utenti = new Login($con);
        $utenti->findByPk($request->id);
        $utenti->setBloccato(!$utenti->getBloccato());
        //$utenti->saveOrUpdate();
        $utenti->saveOrUpdateAndLog(getLoginDataFromSession('id'));
        $con->commit();

        return ($utenti->getId());
    }catch(Drakkar\Exception\DrakkarException $e){
        $con->rollBack();
        return $e;
    }
}

function eliminaDati($request){
    /*try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $utenti = new Login($con);
        $utenti->deleteByPk($request->id);

        $con->commit();
        return "OK";
    }catch(Drakkar\Exception\DrakkarException $e){
        $con->rollBack();
        return $e;
    }*/
    return gestisciStatoUtente($request);
}

function verificaPassword($request){
    echo WsPassword::verificaPassword($request->password);
}

function generaPassword($request)
{
    echo WsPassword::generaPassword();
}

/**/

ob_start();
session_start();$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;