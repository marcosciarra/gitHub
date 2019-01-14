<?php

require_once '../../../conf/conf.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';
require_once '../../../src/model/DrakkarTraceLog.php';
require_once '../../../src/model/TestiLettere.php';


use Drakkar\DrakkarDbConnector;
use Click\Affitti\TblBase\TestiLettere;


function caricaDati($request)
{
    $con = new \Drakkar\DrakkarDbConnector();

    $result = array();

    $testi= new TestiLettere($con);

    // CARICO SELECT
    $result['elencoTipiTestiLettere'] = $testi->getCategoriaValuesList(true,TestiLettere::FETCH_KEYARRAY);
    // CARICO TESTI E LETTERE
    $testi->setOrderBase(' categoria ASC, sottocategoria ASC ');
    $result['elencoTestiLettere'] = $testi->findAll(false,TestiLettere::FETCH_KEYARRAY);
    $result['status']='ok';

    return json_encode($result);
}


function salvaDati($request) {
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $con->beginTransaction();

        $testi= new TestiLettere($con);
        $testi->findByPk($request->id);
        $testi->setOggetto($request->oggetto);
        $testi->setTesto($request->testo);
        $testi->saveOrUpdate();

        $con->commit();
        return json_encode(array('status' => 'ok'));
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


/**/

ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
