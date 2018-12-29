<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27/12/18
 * Time: 18.24
 */

require_once '../conf/costanti.php';
require_once '../lib/pdo.php';
require_once '../class/Engine.php';


function caricaDati($request)
{
    $result = array();
    try {

        $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        $pdo = new PDO(
            'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
            USER,
            PWD,
            $attribute
        );
        /*-------------------------------------------ELENCO TABELLE-------------------------------------------------------*/
        $query = 'SHOW TABLES';

        $result['tabelle'] = $pdo->query($query)->fetchAll();
        $result['status'] = 'ok';

        return json_encode($result);
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
}


function engineTabelle($request)
{
    $result = array();
    try {

        $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        $pdo = new PDO(
            'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
            USER,
            PWD,
            $attribute
        );


        /*--------------------------------------------CLASSE----------------------------------------------------------*/
        $classe = new Engine($request->nomeTabella, false, SELECTNAMESPACE);
        $classe->apriFile();

        $classe->testata();
        $classe->createNameSpace();
        $classe->requireOnce();
        $classe->useClass();

        $classe->openClass();
        $classe->costruttore();
        $classe->closeClass();

        $classe->chiudiFile();

        /*--------------------------------------------MODELLO---------------------------------------------------------*/
        $modello = new Engine($request->nomeTabella, true, SELECTNAMESPACE);
        $modello->apriFile();

        $modello->testata();
        $modello->createNameSpace();
        $modello->requireOnce();
        $modello->useClass();

        $modello->openClass();

        $modello->costruttore();

//        $modello->metodiTabella();
//        $modello->indiciTabella();
        $modello->getSetTabella();

        $modello->closeClass();

        $modello->chiudiFile();

//        /*-------------------------------------------APRO FILE------------------------------------------------------*/
//
//
//        $tabellaModel = fopen("../output/" . $nomeTabella . "Model.php", "w");
//
//        /*-------------------------------------------ELENCO COLONNE---------------------------------------------------*/
//
//        $query = 'DESCRIBE ' . $request->nomeTabella;
//        $result = $pdo->query($query)->fetchAll();
//        foreach ($result as $res) {
//            creaMetodi($request->nomeTabella, $res);
//        }
//        /*-------------------------------------------ELENCO INDICI----------------------------------------------------*/
//
//        $query = 'SHOW INDEX FROM ' . $request->nomeTabella;
//        $result = $pdo->query($query)->fetchAll();
//        foreach ($result as $res) {
//            creaIndici($request->nomeTabella, $res);
//        }
//        /*-------------------------------------------CHIUDO FILE------------------------------------------------------*/
//
//        fclose($tabellaModel);

        $result['status'] = 'ok';

        return json_encode($result);
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
}



function creaMetodi($nomeFile, $dati)
{
    $nome = $dati['Field'];
    $tipoColonna = $dati['Type'];
    $collaction = $dati['Collation'];
    $notNull = $dati['Null'];
    $chiave = $dati['Key'];
    /*
     * La chiave può essere :
     * PRI --> primaria
     * UNI --> Unica
     * MUL --> multipla
     */
    $default = $dati['Default'];
    $tipo = $dati['Extra'];  /* INFORMAZIONI EXTRA */
    $tipo = $dati['Privilages'];
    $commenti = $dati['Comment'];

}


function creaIndici($nomeFile, $dati)
{
    $nome = $dati['Key_name'];
    $tipoColonna = $dati['Type'];
    $nomeColonna = $dati['Column_name'];

}


/**/

ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
