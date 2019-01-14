<?php

require_once '../../../conf/conf.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';
require_once '../../../src/model/GruppiFatturazione.php';
require_once '../../../src/model/Anagrafiche.php';
require_once '../../../src/model/Contratti.php';

use Drakkar\DrakkarPdo;
use Click\Affitti\TblBase\GruppiFatturazione;
use Click\Affitti\TblBase\Anagrafiche;
use Click\Affitti\TblBase\Contratti;

function caricaDati($request)
{
    $con = new \Drakkar\DrakkarDbConnector();
    $result = array();

    $gruppiFatturazione = new GruppiFatturazione($con);
    foreach ($gruppiFatturazione->findElencoUltimiGruppi(false, GruppiFatturazione::FETCH_KEYARRAY) as $app) {
        $contr=new Contratti($con);
        $app['numeroContratti'] = $contr->getNumeroContrattiPerGruppoFatturazione($app['id']);
        $result['elencoGruppiFatturazione'][] = $app;
    }
    // NEW
    $result['newGruppoFatturazione'] = $gruppiFatturazione->getEmptyDbKeyArray();
    $anagrafica = new Anagrafiche($con);
    $result['elencoAnagrafichePiva'] = $anagrafica->findAllPerSelectPiva(Anagrafiche::FETCH_KEYARRAY);

    return json_encode($result);
}


function salvaDati($request)
{
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $gruppiFatturazione = new GruppiFatturazione($con);

        $gruppiFatturazione->creaObjJson($request->object, true);
        $gruppiFatturazione->saveOrUpdate();

        $con->commit();
        return json_encode(array('status' => 'ok'));
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarIntegrityConstrainException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::integrityConstrainException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


function eliminaGruppo($request)
{
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $gruppiFatturazione = new GruppiFatturazione($con);

        $gruppiFatturazione->deleteByPk($request->id);

        $con->commit();
        return json_encode(array('status' => 'ok'));
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarIntegrityConstrainException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::integrityConstrainException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


function modificaGruppo($request)
{
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $gruppiFatturazione = new GruppiFatturazione($con);

        $gruppiFatturazione->findByPk($request->id);
        $gruppiFatturazione->setFlagFattura($request->flagFattura);
        $gruppiFatturazione->saveOrUpdate();

        $con->commit();
        return json_encode(array('status' => 'ok'));
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarIntegrityConstrainException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::integrityConstrainException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


function modificaNumerazioneZero($request)
{
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $gruppiFatturazione = new GruppiFatturazione($con);

        $gruppiFatturazione->findByPk($request->id);
        $gruppiFatturazione->setUltimoNumero(0);
        $gruppiFatturazione->setFlagNumeroZero($request->flagNumeroZero);
        $gruppiFatturazione->saveOrUpdate();

        $con->commit();
        return json_encode(array('status' => 'ok'));
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarIntegrityConstrainException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::integrityConstrainException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
