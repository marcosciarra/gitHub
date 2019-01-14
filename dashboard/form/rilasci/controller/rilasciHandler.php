<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 23/02/18
 * Time: 16.44
 */

require_once '../../../conf/conf.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';
require_once '../../../src/function/functionDate.php';

require_once '../../../src/model/Clienti.php';


use Click\Affitti\TblBase\Clienti;


function caricaDati($request)
{
    $result = array();
    try {
        $con = new \Drakkar\DrakkarDbConnector();

        $clienti = new Clienti($con);

        $result['clienti'] = $clienti->findAll(true, Clienti::FETCH_KEYARRAY);

        $result['status'] = 'ok';
        return json_encode($result);
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }

}


function rilascia($request)
{
    //COPIO FILE
    copia(UPLOAD_URL, ROOT . DIRECTORY_SEPARATOR . $request->ambiente);

    //DECOMPRIMO FILE
    $zip = new ZipArchive();
    if ($zip->open(ROOT . DIRECTORY_SEPARATOR . $request->ambiente . DIRECTORY_SEPARATOR . 'rilascio.zip') === TRUE) {
        $zip->extractTo(ROOT . DIRECTORY_SEPARATOR . $request->ambiente . DIRECTORY_SEPARATOR);
        $zip->close();
        echo 'ok';
    } else {
        echo 'failed';
    }

    //CAMBIO PERMESSI
    permessi(ROOT . DIRECTORY_SEPARATOR . $request->ambiente);

    //ELIMINO FILE COMPRESSO
    unlink(ROOT . DIRECTORY_SEPARATOR . $request->ambiente . DIRECTORY_SEPARATOR . 'rilascio.zip');
}


function elaboraSelezionati($request)
{
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $conExt = new \Drakkar\DrakkarDbConnector(CONNESSIONE_COMUNE);
        $con->beginTransaction();

        $fattureT = new FatturazioneTesta($con);
        $codiceGruppo = time();
        foreach ($request->documenti as $d) {
            if ($d->selezionato) {
                $fattureT->findByPk($d->id);
                //Dare in pasto la fattura al metodo che la contabilizza
                $contabilita = new Contabilita($con, $conExt);
                $contabilita->autoregistrazioneFattura($fattureT->getId(), $codiceGruppo);
                $fattureT->setContabilizzato(1);
                $fattureT->saveOrUpdate();
            }
        }

        $con->commit();

        $result['status'] = 'ok';

        return json_encode($result);

    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        $con->rollBack();
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


function copia($origine, $destinazione)
{
    foreach (scandir($origine) as $file) {
        if (!is_readable($origine . '/' . $file)) continue;
        if (is_dir($file) && ($file != ' . ') && ($file != ' ..')) {
//            mkdir($destinazione . '/' . $file);
//            copia($origine . '/' . $file, $destinazione . '/' . $file);
        } else {
            error_log($file);
            copy($origine . '/' . $file, $destinazione . '/' . $file);
        }
    }
}


function permessi($dirpath)
{
    $dirperm = 0777; // Directory Permission
    $fileperm = 0777; // File Permission

    // fix root directory
    chmod($dirpath, $dirperm);

    // then, fix child elements
    $glob = glob($dirpath . "/*");

    foreach ($glob as $ch) {
        // If is directory, else...
        $ch = (is_dir($ch)) ? chmod($ch, $dirperm) : chmod($ch, $fileperm);
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
