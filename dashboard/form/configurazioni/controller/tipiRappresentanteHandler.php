<?php

require_once '../../../conf/conf.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';
require_once '../../../src/model/TipiRappresentante.php';

use Click\Affitti\TblBase\TipiRappresentante;

use Drakkar\DrakkarPdo;

function caricaDati($request)
{
    $con = new \Drakkar\DrakkarDbConnector();

    $result = array();

    $tipiRappresentante = new TipiRappresentante($con);

    // CARICO SELECT
    $result['elencoTipiRappresentante'] = $tipiRappresentante->findAll(false,TipiRappresentante::FETCH_KEYARRAY);
    // NEW
    $result['newTipoRappresentante'] = $tipiRappresentante->getEmptyDbKeyArray();

    return json_encode($result);
}

function salvaDati($request) {
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $tipiRappresentante = new TipiRappresentante($con);
        $tipiRappresentante->creaObjJson($request->object,true);
        $k = $tipiRappresentante->saveOrUpdate();

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

        $tipiRappresentante = new TipiRappresentante($con);
        $tipiRappresentante->deleteByPk($request->id);

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
