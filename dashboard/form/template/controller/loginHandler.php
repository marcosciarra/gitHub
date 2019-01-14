<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 22/11/17
 * Time: 15.17
 */

require_once '../../../conf/conf.php';
require_once '../../../src/function/functionLogin.php';
require_once '../../../src/model/Login.php';
require_once '../../../src/model/DrakkarLoginLog.php';
require_once '../../../src/model/DrakkarErrorFunction.php';

use Click\Affitti\TblBase\Login;

function effettuaLogin($request)
{

    $result = array();
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $login= new Login($con);

        $result = $login->findByUtentePassword($request->login->username, Login::encryptPassword($request->login->password));

        //controllo validità password
        $oggi = new DateTime();
        $dataScadenzaPwd = new DateTime($login->getDataScadenza());
        $result = array();

        if($oggi >= $dataScadenzaPwd) {
            $result['status'] = "pwd";
            $result['idutente'] = $login->getId();
            return json_encode($result);
        }

        if ($login->getId()>0){
            setLoginElementsInSession(
                $login->getId(),$login->getUsername(),$login->getPassword(),
                $login->getEmail(),$login->getTipoUtente(),$login->getDataScadenza()
            );
            $con->beginTransaction();
            $login->setIdSessione(getElementsFromSession('login','sessionId'));
            $login->setUltimoAccesso(date("Y-m-d G:i:s"));
            $login->saveOrUpdate();
            $con->commit();

            new \Drakkar\Log\DrakkarLoginLog($login->getId());

            $result['status'] = "ok";
            return json_encode($result);
        }
        $result['status'] = "ko";
        return json_encode($result);
    }
    catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    }
    catch (\Drakkar\Exception\DrakkarException $e) {
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