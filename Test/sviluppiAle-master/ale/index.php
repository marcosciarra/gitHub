<?php

require_once 'conf/conf.php';

$pageurl = "http://" . $_SERVER['HTTP_HOST'] .'/SviluppiAle/'.ROOT_FOLDER . '/form/login.php';
header("location: $pageurl");