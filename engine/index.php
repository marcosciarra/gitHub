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

$attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
$pdo = new PDO(
    'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
    USER,
    PWD,
    $attribute
);

$struttura = $pdo->query("SHOW TABLE STATUS FROM " . SCHEMA);

var_dump($struttura);
