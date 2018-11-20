<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 07/11/17
 * Time: 9.55
 */

require_once "../../../conf/conf.php";
require_once "../../../src/model/DrakkarDbConnector.php";
//require_once "../../../_lib/logger/Logger.php";

require_once "../../../src/model/ftab.php";
require_once "../../../src/model/fpcon.php";

function getElenchiPerSelect($request){

    try{
        $conn = \Drakkar\DrakkarDbConnector::connectStatic();
    }catch (\Drakkar\Exception\DrakkarException  $e){
        return gestisciErroreGenerico($e);
    }

    $result = array();

    $fTab = new \AmicoWinRent\TblBase\FTab($conn);
    $result['modalitaPagamento'] = $fTab->findElencoModlitaPagamento(\AmicoWinRent\TblBase\FTab::FETCH_KEYARRAY);
    $result['listino'] = $fTab->findElencoListino(\AmicoWinRent\TblBase\FTab::FETCH_KEYARRAY);
    $result['categoriaSconto'] = $fTab->findElencoCategoriaSconto(\AmicoWinRent\TblBase\FTab::FETCH_KEYARRAY);
    $result['agente'] = $fTab->findElencoAgente(\AmicoWinRent\TblBase\FTab::FETCH_KEYARRAY);
    $result['zona'] = $fTab->findElencoZona(\AmicoWinRent\TblBase\FTab::FETCH_KEYARRAY);
    $result['settoreMerciologico'] = $fTab->findElencoSettoreMerciologico(\AmicoWinRent\TblBase\FTab::FETCH_KEYARRAY);

    return json_encode($result);
}

function salvaNuovoCliente($request){

    try{
        $conn = \Drakkar\DrakkarDbConnector::connectStatic();
    }catch (\Drakkar\Exception\DrakkarConnectionException $e){
        return json_encode($e->getMessage());
    }catch (\Drakkar\Exception\DrakkarException $e){
        return json_encode($e->getMessage());
    }


    try{

        $conn->beginTransaction();

        $cliente = new \AmicoWinRent\TblBase\FPcon($conn);

        //setto i campi di default a " " / 0
        $cliente->presetCampi();
        //setto i campi con un valore predefinito
        $cliente->presetCampiFissi();
        //setto i campi che mi arrivano da maschera
        $cliente->setPNome($request->nuovoCliente->intestazione->nomeRagioneSociale.' '.$request->nuovoCliente->intestazione->cognomeRagioneSocialeEst);
        $cliente->setPNomeEst('');
        $cliente->setPInd($request->nuovoCliente->indirizzo->indirizzo);
        $cliente->setPIndEst($request->nuovoCliente->indirizzo->indirizzoEst);
        $cliente->setPCap($request->nuovoCliente->indirizzo->cap);
        $cliente->setPCitta($request->nuovoCliente->indirizzo->citta);
        $cliente->setPProvin($request->nuovoCliente->indirizzo->provincia);
        $cliente->setPTelefono($request->nuovoCliente->contatti->telefono);
        $cliente->setPCell($request->nuovoCliente->contatti->cellulare);
        $cliente->setPTelefax($request->nuovoCliente->contatti->fax);
        $cliente->setPEmail($request->nuovoCliente->contatti->email);
        $cliente->setPContatto($request->nuovoCliente->contatti->referenteAziendale);
        $cliente->setPPec($request->nuovoCliente->contatti->pec);
        $cliente->setPSito($request->nuovoCliente->contatti->sito);
        $cliente->setPFisc($request->nuovoCliente->datiFiscali->codiceFiscale);
        $cliente->setPCopiva($request->nuovoCliente->datiFiscali->partitaIva);
        $cliente->setPPf($request->nuovoCliente->tipologia);
        $cliente->setPRappr($request->nuovoCliente->altriDati->agente);
        $cliente->setPSetmer($request->nuovoCliente->altriDati->settoreMerciologico);
        $cliente->setPZona($request->nuovoCliente->altriDati->areaGeografica);
        $cliente->setPCodPag($request->nuovoCliente->altriDati->modalitaPagamento);
        $cliente->setPCatsco($request->nuovoCliente->altriDati->categoriaSconto);
        if($request->nuovoCliente->altriDati->listino == ""){
            $cliente->setPTipoList(1);
        }else{
            $cliente->setPTipoList($request->nuovoCliente->altriDati->listino);
        }
        $cliente->setPSesso($request->nuovoCliente->datiPrivati->sesso);
        if($request->nuovoCliente->tipologia == "C"){
            $cliente->setPCognome($request->nuovoCliente->intestazione->cognomeRagioneSocialeEst);
        }else{
            $cliente->setPCognome($request->nuovoCliente->datiPrivati->cognome);
        }
        if($request->nuovoCliente->tipologia == "C"){
            $cliente->setPNomePf($request->nuovoCliente->intestazione->nomeRagioneSociale);
        }else{
            $cliente->setPNomePf($request->nuovoCliente->datiPrivati->nome);
        }
        $cliente->setPDataNas($request->nuovoCliente->datiPrivati->dataNascita);
        $cliente->setPLuogoNas($request->nuovoCliente->datiPrivati->luogoNascita);
        $cliente->setPProvNas($request->nuovoCliente->datiPrivati->provinciaNascita);
        $cliente->setPNazione($request->nuovoCliente->indirizzo->stato);

        $cliente->saveOrUpdate();

        $conn->commit();


        return 'OK';

    }catch (Exception $e){

        //var_dump($e);
        $conn->rollBack();

        return 'K0';
    }

}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$function = $request->function;
$r = $function($request);
echo $r;


