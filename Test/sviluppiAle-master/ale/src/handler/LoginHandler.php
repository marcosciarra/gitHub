<?php

/*  Alessandro Pericolo
 *  Training development
 *  Copyright @2017 Superperil
 *  "Programmers will conquer the Hello World"
*/

require_once '../../conf/conf.php';
require_once '../../lib/functions.php';
require_once '../../lib/pdo.php';

require_once '../model/login.php';

function checkLogin($request)
{
    $result = array();
    $pdo = connettiPdo();
    
    $login = new Login($pdo);
    $result = $login->findPasswordByUsername($request->username);
    //$result = $login->findAllByUsername($request->username);
    //$result = $login->findAll();
    
    return $result;
}

function insertTest($request)
{
    
    $pdo = connettiPdo();

    try{
        $pdo->beginTransaction();
        $query = $pdo->prepare("INSERT INTO login (username, password) VALUES (?,?)");
        $query->execute(array($request->username, $request->password));
        $pdo->commit();
    } catch (Exception $ex) {
        var_dump(Exception::$ex);
    }
    

}

/**/
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
