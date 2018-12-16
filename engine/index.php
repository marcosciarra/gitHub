<!DOCTYPE html>
<html ng-app="affittiApp" ng-controller="homeController">

<head>
    <link href="./lib/bootstrap.css" rel="stylesheet" type="text/css" media="all">

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
    ?>
</head>

<body class="bgColor">
<?php
try {
    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    $pdo = new PDO(
        'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
        USER,
        PWD,
        $attribute
    );
    $tabellaScelta = 'login';
    /*-------------------------------------------ELENCO TABELLE-------------------------------------------------------*/
    $query = 'SHOW TABLES';
    echo "<br>";
    echo "<div class='row'>";
    foreach ($pdo->query($query)->fetchAll() as $app) {
        echo "<div class='btn btn-primary col-md-2'>";
        echo $app['Tables_in_' . SCHEMA];
        echo "</div>";
    }
    echo "</div>";

    echo "<br><div class='text-center'>STRUTTURA TABELLA</div><br>";

    $query = 'DESCRIBE ' . $tabellaScelta;
    $result = $pdo->query($query)->fetchAll();
    foreach ($result as $res) {
        $nome = $res['Field'];
        $tipoColonna = $res['Type'];
        $collaction = $res['Collation'];
        $notNull = $res['Null'];
        $chiave = $res['Key'];
        /*
         * La chiave può essere :
         * PRI --> primaria
         * UNI --> Unica
         * MUL --> multipla
         */
        $default = $res['Default'];
        $tipo = $res['Extra'];  /* INFORMAZIONI EXTRA */
        $tipo = $res['Privilages'];
        $commenti = $res['Comment'];
    }


    echo "<br><div class='text-center'>INDICI</div><br>";

    $query = 'SHOW INDEX FROM contratti';
    $result = $pdo->query($query)->fetchAll();
    foreach ($result as $res) {
        $nome = $res['Key_name'];
        $tipoColonna = $res['Type'];
        $nomeColonna = $res['Column_name'];
    }
} catch (PDOException $e) {
    die('Errore durante la connessione al database!: ' . $e->getMessage());
}
?>

</body>
