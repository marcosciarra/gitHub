<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 12/12/18
 * Time: 22.23
 */

date_default_timezone_set('Europe/Rome');
header("Content-type: text/html; charset=latin1");

require_once 'conf/costanti.php';
require_once 'lib/pdo.php';

//$attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
//$pdo = new PDO(
//    'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
//    USER,
//    PWD,
//    $attribute
//);
//
//$pdo->query("select * from contratti");
//
//var_dump($pdo);

try {
    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    $pdo = new PDO(
        'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
        USER,
        PWD,
        $attribute
    );

    echo '<br><br><br>ELENCO TABELLE<br><br><br>';
    $query='SHOW TABLES';

    foreach ($pdo->query($query)->fetchAll() as $app){
        var_dump($app);
        echo '<br><br><br>';
    }

    echo '<br><br><br>STRUTTURA TABELLA<br><br><br>';

    $query='DESCRIBE contratti';

    foreach ($pdo->query($query)->fetchAll() as $app){
        var_dump($app);
        echo '<br><br><br>';
    }

    echo '<br><br><br>INDICI<br><br><br>';

    $query='SHOW INDEX FROM contratti';

    foreach ($pdo->query($query)->fetchAll() as $app){
        var_dump($app);
        echo '<br><br><br>';
    }
} catch (PDOException $e) {
    die('Errore durante la connessione al database!: ' . $e->getMessage());
}

