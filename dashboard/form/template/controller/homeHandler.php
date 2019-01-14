<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 22/11/17
 * Time: 15.25
 */

require_once '../../../conf/conf.php';
require_once '../../../src/function/functionLogin.php';
require_once '../../../src/function/functionDate.php';
require_once '../../../src/model/Login.php';

use Click\Affitti\TblBase\Login;

//==============================LOGIN FUNCTIONS=======================================

function getLevelUserLogged($request)
{
    return getLoginDataFromSession('tipoUtente');
}

function getUsernameUserLogged($request)
{

    if (getLoginDataFromSession('sessionId')) {
        $con = new \Drakkar\DrakkarDbConnector();
        $login = new Login($con);
        $utente = $login->findByUtentePassword(getLoginDataFromSession('username'), getLoginDataFromSession('password'));
        if ($login->getIdSessione() != getLoginDataFromSession('sessionId')) {
            if (session_destroy()) {
                return 'logout';
            }
        } else {
            $result = array();
            $result['username'] = getLoginDataFromSession('username');
            $result['tipoUtente'] = getLoginDataFromSession('tipoUtente');
            $result['sessionId'] = getLoginDataFromSession('sessionId');

            //return getLoginDataFromSession('username');
            return json_encode($result);
        }
    } else {
        effettuaLogout();
    }
}

function getIdUserLogged($request)
{
    return getLoginDataFromSession('id');
}

function controlliLogin($request)
{
    $date = new DateTime();

    if (getLoginDataFromSession('dataScadenza'))
        $dataScadenzaPwd = new DateTime(getLoginDataFromSession('dataScadenza'));
    else
        $dataScadenzaPwd = 0;

    if ($dataScadenzaPwd > $date) {
        $interval = $date->diff($dataScadenzaPwd);
        return $interval->format('%a');
    } else {
        return -1;
    }

}

function effettuaLogout($request = null)
{
    return clearSession();
}


function caricaFiltri()
{
    $result = array();
    try {
        $con = new \Drakkar\DrakkarDbConnector();

        $stabili = new Stabili($con);
        $utenti = new Login($con);

        /*------------------------------------------------AGENZIA-----------------------------------------------------*/
//        $loc=new Anagrafiche($con);
//        $loc->setWhereBase(' societa_fatturazione=1 ');
//        $result['agenzia'] = $loc->findAllPerSelect(Anagrafiche::FETCH_KEYARRAY);

        /*------------------------------------------------LOCATORI----------------------------------------------------*/
        $elencoLocatori = [];
        $contratti = new Contratti($con);
        foreach ($contratti->getElencoLocatori(Contratti::FETCH_KEYARRAY) as $elenco) {
            $elenco = $elenco['id'];

            $appElenco = explode(',', $elenco);

            foreach ($appElenco as $app) {
                $app = str_replace('[', '', $app);
                $app = str_replace(']', '', $app);
                $elencoLocatori[] = trim($app);
            }
        }
        $loc = new Anagrafiche($con);
        $result['locatori'] = $loc->findbyIdArray(implode(',', $elencoLocatori), Anagrafiche::FETCH_KEYARRAY);

        /*------------------------------------------------CONDUTTORI--------------------------------------------------*/
        $elencoConduttori = [];
        $contratti = new Contratti($con);
        foreach ($contratti->getElencoConduttori(Contratti::FETCH_KEYARRAY) as $elenco) {
            $elenco = $elenco['id'];

            $appElenco = explode(',', $elenco);

            foreach ($appElenco as $app) {
                $app = str_replace('[', '', $app);
                $app = str_replace(']', '', $app);
                $elencoConduttori[] = trim($app);
            }
        }
        $loc = new Anagrafiche($con);
        $result['conduttori'] = $loc->findbyIdArray(implode(',', $elencoConduttori), Anagrafiche::FETCH_KEYARRAY);

        /*------------------------------------------------STABILI-----------------------------------------------------*/
        $stabili->setWhereBase(' cestino=0 ');
        $stabili->setOrderBase(' descrizione ASC ');
        $result['filtriStabili'] = $stabili->findAllConCodice( Stabili::FETCH_KEYARRAY);

        /*------------------------------------------------UTENTI------------------------------------------------------*/
        $utenti->setWhereBase(' bloccato=0 AND tipo_utente!=\'SU\' ');
        $utenti->setOrderBase(' username ASC ');
        $result['filtriUtenti'] = $utenti->findPerSelect();

        $result['status'] = 'ok';

        return json_encode($result);
    } catch
    (\Drakkar\Exception\DrakkarConnectionException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


function caricaAvvisi($request)
{
    $result = array();
    try {
        $con = new \Drakkar\DrakkarDbConnector();
        $conExt = new \Drakkar\DrakkarDbConnector(CONNESSIONE_COMUNE);

        $login = new Login($con);
        $login->findByUtentePassword(getLoginDataFromSession('username'), getLoginDataFromSession('password'));
        if ($login->getTipoUtente() != 'SU') {
            $idNews = $login->getIdNewsLetta();
            if ($idNews == null)
                $idNews = 0;
            $news = new News($conExt);
            $result['avvisi'] = $news->countAvvisiNonLetti($idNews);
        } else {
            $result['avvisi'] = 0;
        }
        $result['status'] = 'ok';

        return json_encode($result);
    } catch
    (\Drakkar\Exception\DrakkarConnectionException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::connectionException($e));
    } catch (\Drakkar\Exception\DrakkarException $e) {
        return json_encode(\Drakkar\Exception\DrakkarErrorFunction::genericException($e));
    }
}


function sommaData($request)
{
    return calcolaData($request->data_inizio, 0, 1, -1);
}

function sommaDataInversa($request)
{
    return calcolaData($request->data_fine, 0, -1, 1);
}

/**/

ob_start();
session_start();
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;