<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 15/12/17
 * Time: 10.38
 */

require_once 'conf/conf.php';
require_once 'src/model/DrakkarDbConnector.php';
require_once 'src/model/DrakkarErrorFunction.php';

require_once 'src/function/functionDate.php';

require_once 'src/model/ElaboraContratto.php';
require_once 'src/model/AggiornaContratto.php';


require_once 'src/model/MovimentiTesta.php';
require_once 'src/model/MovimentiDettagli.php';
require_once 'src/model/MovimentiBancaTesta.php';
require_once 'src/model/MovimentiBancaDettagli.php';

require_once 'src/model/PeriodiContrattuali.php';
require_once 'src/model/Gestioni.php';
require_once 'src/model/PianoRateTesta.php';
require_once 'src/model/Rli.php';

require_once 'src/flussi/CreaF24Elide.php';

use Click\Affitti\Viste\ElaboraContratto;
use Click\Affitti\Viste\AggiornaContratto;
use Click\Flussi\CreaF24Elide;
use Click\Affitti\TblBase\MovimentiTesta;
use Click\Affitti\TblBase\MovimentiDettagli;
use Click\Affitti\TblBase\MovimentiBancaTesta;
use Click\Affitti\TblBase\MovimentiBancaDettagli;


try {
    $con = new \Drakkar\DrakkarDbConnector();
    $con->beginTransaction();

    $rli = new \Click\Affitti\TblBase\Rli($con);
    /** @var \Click\Affitti\TblBase\Rli $rli */
    foreach ($rli->findAll() as $rli) {

        $periodoC = new \Click\Affitti\TblBase\PeriodiContrattuali($con);
        $periodoC->findByPk($rli->getIdPeriodoContrattuale());

        $gestione = new \Click\Affitti\TblBase\Gestioni($con);
        $idGestione = $gestione->getIdGestioneByData($rli->getIdContratto(), $periodoC->getDataInizio());

        $pianoRateT = new \Click\Affitti\TblBase\PianoRateTesta($con);
        $canone = $pianoRateT->getImportoTotaleByIdGestione($idGestione, 'F');
        $rli->setCanone($canone);

        $rli->saveOrUpdate();
    }

    $con->commit();
    return;
} catch (Drakkar\Exception\DrakkarException $e) {

    $con->rollBack();
    var_dump($e);

} catch (Exception $e) {
    var_dump($e);
}
