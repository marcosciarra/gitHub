<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 15/12/17
 * Time: 10.38
 */

require_once '../../../conf/conf.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';

require_once '../../../src/model/IstatFoi.php';

require_once '../../../src/model/DrakkarTraceLog.php';

use Click\Affitti\TblBase\IstatFoi;


function caricaDati($request)
{
    $result = array();
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $conExt = new \Drakkar\DrakkarDbConnector(CONNESSIONE_COMUNE);
        $istat = new IstatFoi($conExt );

        // CARICO DATI
        $istat->setOrderBase(' anno DESC , mese DESC');
        $result['istat']=$istat->findAll(false,IstatFoi::FETCH_KEYARRAY);
        $result['istatNew']=$istat->getEmptyDbKeyArray();

        $result['status'] = 'ok';

        return json_encode($result);
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


function salvaDati($request){
    try {
        $conExt = new \Drakkar\DrakkarDbConnector(CONNESSIONE_COMUNE);
        $conExt->beginTransaction();

        $istat = new IstatFoi($conExt);
        $istat->creaObjJson($request->obj,true);
        $istat->saveOrUpdate();

        $conExt->commit();
        return "ok";
    }catch(Drakkar\Exception\DrakkarException $e){
        $conExt->rollBack();
        return $e;
    }
}


function eliminaDati($request){
    try {
        $conExt = new \Drakkar\DrakkarDbConnector(CONNESSIONE_COMUNE);
        $conExt->beginTransaction();

        $istat = new IstatFoi($conExt);
        $istat->deleteByPk($request->id);

        $conExt->commit();
        return "ok";
    }catch(Drakkar\Exception\DrakkarException $e){
        $conExt->rollBack();
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
