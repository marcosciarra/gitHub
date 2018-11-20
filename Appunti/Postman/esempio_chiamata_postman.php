<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8081",
  CURLOPT_URL => "http://172.16.0.196:8081/amicorent/servlet/AR_CLIFOR",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n\t\"Request\":{\n\t\t\"DatiClifor\":{\n\t\t\t\"Ditta\":1,\n\t\t\t\"Utente\":\"0002\",\n\t\t\t\"TipoConto\":\"C\",\n\t\t\t\"Op\":\"L\"\n\t\t}\n\t}\n}",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache",
    "Content-Type: application/json",
    "Postman-Token: 4e2a6f69-7730-489d-bb11-9de24e731f85"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}