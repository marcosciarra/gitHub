<?php

require_once '../../../conf/conf.php';
require_once '../../../src/function/functionLogin.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';
require_once '../../../src/model/DrakkarTraceLog.php';
require_once '../../../src/model/Login.php';

use Click\Affitti\TblBase\Login;

use Drakkar\DrakkarPdo;

function caricaDati($request)
{
    $con = new \Drakkar\DrakkarDbConnector();

    $result = array();

    $utenti = new Login($con);

    $tipoUtenteLoggato = getLoginDataFromSession('tipoUtente');
    $result['idUtenteLoggato'] = getLoginDataFromSession('id');

    switch ($tipoUtenteLoggato) {
        case 'A':
            $utenti->setWhereBase(" tipo_utente='U' OR tipo_utente='A' ");
            break;
        case 'U':
            $utenti->setWhereBase(" tipo_utente='U' ");
            break;
    }
    $result['elencoUtenti'] = $utenti->findAll(false,Login::FETCH_KEYARRAY);

    return json_encode($result);
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

/**/

ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;