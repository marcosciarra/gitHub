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

//URL
define("BASE_URL", "http://localhost");
//define("BASE_URL", "http://172.16.0.200:83");

//CONFIGURAZIONE SMTP
define("EMAIL_USE_SMTP", true);
define("EMAIL_SMTP_HOST", "smtp-relay.sendinblue.com");
define("EMAIL_SMTP_AUTH", true);
define("EMAIL_SMTP_USERNAME", "support@clicksrl.eu");
define("EMAIL_SMTP_PASSWORD", "0vNtV2IwUnxWD9hY");
define("EMAIL_SMTP_PORT", 587);
define("EMAIL_SMTP_ENCRYPTION", "");
