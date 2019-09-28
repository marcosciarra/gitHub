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

function insertUser($request)
{
    
    $pdo = connettiPdo();

    try{
        
        $pdo->beginTransaction();
        $query = $pdo->prepare("INSERT INTO user (username, password, nome, cognome, data_nascita, sesso) VALUES (?,?,?,?,?,?)");
        $query->execute(array($request->user->username, $request->user->password, $request->user->nome, $request->user->cognome, $request->user->dataNascita, $request->user->sesso));
        
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

