<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27/12/18
 * Time: 18.24
 */

require_once '../lib/pdo.php';

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

        $result['tabelle'] =$pdo->query($query)->fetchAll();
        $result['status'] = 'ok';

        return json_encode($result);
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}

//try {
//    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
//    $pdo = new PDO(
//        'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
//        USER,
//        PWD,
//        $attribute
//    );
//    $tabellaScelta = 'login';
//    /*-------------------------------------------ELENCO TABELLE-------------------------------------------------------*/
//    $query = 'SHOW TABLES';
//    echo "<br>";
//    echo "<div class='row'>";
//    foreach ($pdo->query($query)->fetchAll() as $app) {
//        echo "<div class='btn btn-primary col-md-2'>";
//        echo $app['Tables_in_' . SCHEMA];
//        echo "</div>";
//    }
//    echo "</div>";
//
//    echo "<br><div class='text-center'>STRUTTURA TABELLA</div><br>";
//
//    $query = 'DESCRIBE ' . $tabellaScelta;
//    $result = $pdo->query($query)->fetchAll();
//    foreach ($result as $res) {
//        $nome = $res['Field'];
//        $tipoColonna = $res['Type'];
//        $collaction = $res['Collation'];
//        $notNull = $res['Null'];
//        $chiave = $res['Key'];
//        /*
//         * La chiave può essere :
//         * PRI --> primaria
//         * UNI --> Unica
//         * MUL --> multipla
//         */
//        $default = $res['Default'];
//        $tipo = $res['Extra'];  /* INFORMAZIONI EXTRA */
//        $tipo = $res['Privilages'];
//        $commenti = $res['Comment'];
//    }
//
//
//    echo "<br><div class='text-center'>INDICI</div><br>";
//
//    $query = 'SHOW INDEX FROM contratti';
//    $result = $pdo->query($query)->fetchAll();
//    foreach ($result as $res) {
//        $nome = $res['Key_name'];
//        $tipoColonna = $res['Type'];
//        $nomeColonna = $res['Column_name'];
//    }
//} catch (PDOException $e) {
//    die('Errore durante la connessione al database!: ' . $e->getMessage());
//}


/**/

ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
