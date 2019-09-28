<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 29/11/17
 * Time: 11.02
 */

$giorno = 1;
$mese = 3;
$anno = 2016;

echo date('d-m-Y', mktime(null,null, null,$mese+18, $giorno-1, $anno));
