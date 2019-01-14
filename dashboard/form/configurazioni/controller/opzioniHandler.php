<?php

require_once '../../../conf/conf.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';
require_once '../../../src/model/Opzioni.php';

use Drakkar\DrakkarPdo;
use Click\Affitti\TblBase\Opzioni;

function caricaDati($request)
{
    $con = new \Drakkar\DrakkarDbConnector();

    $result = array();

    $opzioni = new Opzioni($con);

    // CARICO SELECT
    $result['elencoOpzioni'] = $opzioni->findAll(false,Opzioni::FETCH_KEYARRAY);

    return json_encode($result);
}

function salvaDati($request) {
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $opzioni = new Opzioni($con);
        $opzioni->findByPk($request->id);
        $opzioni->setValore($request->valore);
        $opzioni->saveOrUpdate();

        $con->commit();
        return json_encode(array('status' => 'ok'));
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
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

function eliminaDati($request){
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $tipiSoggetto = new TipiSoggetto($con);
        $tipiSoggetto->deleteByPk($request->id);

        $con->commit();
        return "OK";
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
