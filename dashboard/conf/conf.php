<?php
//salt
define("SALT", "clickAffitti");

// costanti per connesioni multiple
define("CONNESSIONE_DATI", 0);
define("CONNESSIONE_COMUNE", 1);

//define("CLIENTE", "test");
define("CLIENTE", "gitHub/dashboard");

define("ROOT", $_SERVER['DOCUMENT_ROOT']);
define("UPLOAD_URL", $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . CLIENTE . DIRECTORY_SEPARATOR . 'upload');

/*CONFIGURAZIONE DB (LOCALE)*/
define("HOST", "MySql");
define("PORT", "3306");
//define("ORACLE_SID", "ALI");
define("USER", "root");
define("PWD", "Sciarra82");
define("SCHEMA", "dashboard");
define('CHARSET', 'latin1');

define("HOST1", "MySql");
define("PORT1", "3306");
define("USER1", "root");
define("PWD1", "Sciarra82");
define("SCHEMA1", "dashboard");
define('CHARSET1', 'latin1');