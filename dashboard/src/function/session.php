<?php
session_start();
echo '<h1>ELEMENTI IN SESSION</h1>';
echo '<pre>';
var_dump($_SESSION);
echo '</pre>';
