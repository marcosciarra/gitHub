<?php

$curl = curl_init();
//curl_setopt($curl, CURLOPT_URL, 'http://192.168.8.146:8080/testHTML/servlet/ARTICOLI?Ditta=1&Op=L&SubOp=T');
curl_setopt($curl, CURLOPT_URL, 'https://www.w3schools.com/angular/customers.php');

$result = curl_exec($curl);

return $result;

