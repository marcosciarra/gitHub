<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 05/01/18
 * Time: 9.10
 */

require_once '../../../conf/conf.php';
require_once '../../../src/model/DrakkarDbConnector.php';
require_once '../../../src/model/DrakkarErrorFunction.php';

require_once '../../../src/model/Anagrafiche.php';
require_once '../../../src/model/Contratti.php';
require_once '../../../src/model/ContrattiDettagli.php';
require_once '../../../src/model/Rate.php';
require_once '../../../src/model/Istat.php';
require_once '../../../src/model/PagamentoF24.php';
require_once '../../../src/model/Cauzioni.php';
require_once '../../../src/model/UnitaImmobiliariDettagli.php';
require_once '../../../src/model/Rli.php';
require_once '../../../src/model/FatturazioneTesta.php';

use Click\Affitti\TblBase\Anagrafiche;
use Click\Affitti\TblBase\Contratti;
use Click\Affitti\TblBase\Rate;
use Click\Affitti\Viste\Istat;
use Click\Affitti\Viste\PagamentoF24;
use Click\Affitti\TblBase\ContrattiDettagli;
use Click\Affitti\TblBase\Cauzioni;
use Click\Affitti\TblBase\UnitaImmobiliariDettagli;
use Click\Affitti\TblBase\Rli;
use Click\Affitti\TblBase\FatturazioneTesta;

function caricaDati()
{
    $result = array();
    try {
        $con = new \Drakkar\DrakkarDbConnector();

        $result['status'] = 'ok';

        return json_encode($result);
    } catch (\Drakkar\Exception\DrakkarConnectionException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}



/**/

ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;
