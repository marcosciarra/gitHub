<?php
/**
 * Created by PhpStorm.
 * User: Duca
 * Date: 04/05/18
 * Time: 19:05
 */

namespace WsPassword;

class WsPassword {

    public static function generaPassword()
    {
        $app = new self();
        return $app->callWs("Genera",
            "{\n\t\"lunghezza\":10,\n\t\"numeri\":true,\n\t\"maiuscole\":true,\n\t\"minuscole\":true,\n\t\"simboli\":true\n}"
        );
    }

    public static function verificaPassword($password)
    {
        $app = new self();
        return $app->callWs("Verifica",
            "{\"password\":\"$password\"}"
        );
    }

    ////////////////////////////////
    private function callWs($metodo,$request){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_PORT => "5002",
            CURLOPT_URL => "http://192.168.8.138:5002/".$metodo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}
