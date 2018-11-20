<?php
if (!defined('AP_SERVER')) {
    define('AP_SERVER', 'localhost');
    define('AP_DBUSER', 'root');
    define('AP_DBPASSWORD', 'Alessandro90');
    define('AP_DBNAME', 'alessandro');
    define('AP_DBCHARSET', 'latin1');
    define('ROOT_FOLDER', 'ale');
    define('ROOT_FOLDER_TEMPLATE', ROOT_FOLDER . '/content/template');
    define('ROOT_FOLDER_LIB', ROOT_FOLDER . '/src/css');
    define('URL_BASE', 'http://localhost/ale');
    define('SITE_NAME', '::ecommerce::');
    define('MEDIA_BASE', '/content/media');
    define('TEMPLATE_NAME', 'enmamall');

    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}