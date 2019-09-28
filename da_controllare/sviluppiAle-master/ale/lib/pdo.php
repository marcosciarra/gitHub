<?php
if (file_exists('../conf/conf.php')){
    require_once '../conf/conf.php';
}
/**
 * Funzione di connessione a PDO. La funzione consente di specificare il database a cui collegarsi.
 *
 * @param string|null $dbName Nome del database
 *
 * @return bool|PDO
 */
function connettiPdo($dbName = null)
{
    if (!$dbName) $dbName = AP_DBNAME;
    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    try {
        $pdo = new PDO('mysql:host=' . AP_SERVER . ';dbname=' . $dbName . ';charset=' . AP_DBCHARSET, AP_DBUSER, AP_DBPASSWORD, $attribute);
    }
    catch (PDOException $e) {
        echo $e->getMessage() . '<br/>';
        var_dump($e);

        return false; //gestione errore connessione
    }

    return $pdo;
}


/**
 * Funzione per l'esecuzione delle query tramite prepare/execute
 *
 * @param PDOStatement $stringPrepared la stringa preparativa. Sostituire le variabili con ?
 * @param array        $arrayElementi  l'array mono/bidimensionale contenente i valori
 *                                     (NOTA: per riga, DEVONO coincidere coi punti interrogativi!!!)
 * @param char         $chrTipoRitorno variabile 'classica' (p=PDOStatement [default], b=blob(matrice di array),
 *                                     f=fetch/array, v=valore singolo)
 *
 * @return PDOStatement|array|string ritorna il valore
 */
function queryPreparedPdoOld(PDOStatement $stringPrepared, $arrayElementi = null, $chrTipoRitorno = 'p')
{
    if (!is_null($arrayElementi) and !is_array($arrayElementi)) {
        $arrayElementi = array($arrayElementi);
    }
    //echo $stringPrepared->queryString,'<br/>';
    $stringPrepared->execute($arrayElementi);
    switch (strtolower($chrTipoRitorno)) {
        case 'p':
            return $stringPrepared;
        case 'b':
            $ritorno = $stringPrepared->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'f':
            $ritorno = $stringPrepared->fetch(PDO::FETCH_ASSOC);
            break;
        case 'v':
            $ritorno = $stringPrepared->fetchColumn();
            break;
    }
    $stringPrepared->closeCursor();

    return $ritorno;
}

/**
 * Funzione per l'esecuzione delle query tramite prepare/execute
 * @param PDO|null $pdo classe di gestione database
 * @param string|PDOStatement $stringPrepared la stringa preparativa. Sostituire le variabili con ?
 * @param array $arrayElementi l'array mono/bidimensionale contenente i valori
 * (NOTA: per riga, DEVONO coincidere coi punti interrogativi!!!)
 * @param char|string $chrTipoRitorno variabile 'classica' (p=PDOStatement [default], b=blob, f=fetch/array, v=valore)
 * @param int $tipoFetch tipo di ritorno dato richiesto
 * @return PDOStatement|array|string ritorna il valore, uguale alla forma di queryPdo
 * ove $boolMatrice fosse true, ne sarebbe un'array di tali elementi.
 */
function queryPreparedPdo($pdo,$stringPrepared,$arrayElementi=null,$chrTipoRitorno='p',$tipoFetch=null){
    $boolConnettiLocale=(!($pdo instanceof PDO));
    if ($boolConnettiLocale) {
        $pdo=connettiPdo();
	}
    if (!$pdo instanceof PDO) {
        phpToAlert('Attezione! Connesione fallita!');
        die;
    }
    if (!$stringPrepared ){
        bloccaPdo($pdo);
	}
    /** @var $queryHandler PDOStatement */
    $queryHandler=($stringPrepared instanceof PDOStatement)?$stringPrepared:$pdo->prepare($stringPrepared);
    if (!is_null($arrayElementi) and !is_array($arrayElementi)){
        $arrayElementi = array($arrayElementi);
    }

    $queryHandler->execute($arrayElementi);
    if ('p' == strtolower($chrTipoRitorno)){
        return $queryHandler;
    }
    $fetching = (is_null($tipoFetch))?PDO::FETCH_ASSOC:$tipoFetch;
    switch (strtolower($chrTipoRitorno)){
        case 'b':
            $ritorno=$queryHandler->fetchAll($fetching);
            break;
        case 'f':
            $ritorno=$queryHandler->fetch($fetching);
            break;
        case 'v':
            $fetching = (is_null($tipoFetch))?0:$tipoFetch;
            $ritorno=$queryHandler->fetchColumn($fetching);
            break;
    }
    $queryHandler->closeCursor();
    if ($boolConnettiLocale) {
        $pdo=null;
    }
    return $ritorno;
}

/**
 * Funzione per effettuare l'INSERT di un array $array[<campoDB>]=>valore.
 *
 * @param PDO    $pdo        Connessione al db
 * @param string $strTabella nome della tabella da aggiornare
 * @param array  $arrayKey   array dei valori da aggiornare (CHIAVE=>VALORE)
 *
 * @return int l'id dell'ultima riga inserita
 */
function insertKeyArrayPdo(PDO $pdo, $strTabella, $arrayKey)
{
    $arrayCampi = array();
    $arrayPosizioni = array();
    foreach ($arrayKey as $key => $value) {
        if (null === $value or !$value) {
            unset($arrayKey[$key]);
            continue;
        }
        $arrayCampi[] = $key;
        $arrayPosizioni[] = ':' . $key;
    }
    $insert = $pdo->prepare('INSERT INTO ' . $strTabella . ' (' . implode(',', $arrayCampi) . ') VALUES (' . implode(',', $arrayPosizioni) . ')');
    $insert->execute($arrayKey);

    return $pdo->lastInsertId();
}

/**
 * @param PDO    $pdo         Connessione al db
 * @param string $strTabella  nome della tabella da aggiornare
 * @param array  $arrayKey    array dei valori da aggiornare (CHIAVE=>VALORE)
 * @param mixed  $idValue     l'id della riga da aggiornare
 * @param string $strIdColumn il nome della colonna da utilizzare come chiave per l'aggiornamento
 *
 * @return null|int il numero di righe aggiornate
 */
function updateKeyArrayPdo(PDO $pdo, $strTabella, $arrayKey, $idValue, $strIdColumn = 'ID')
{
    $arrayCampi = array();
    foreach ($arrayKey as $chiave => $valore) {
        if (null === $valore) {
            unset($arrayKey[$chiave]);
            continue;
        }
        $arrayCampi[] = $chiave . '=:' . $chiave;
    }
    $update = $pdo->prepare('UPDATE ' . $strTabella . ' SET ' . implode(',', $arrayCampi) . ' WHERE ' . $strIdColumn . '=:ID_COLUMN');
    $arrayKey['ID_COLUMN'] = $idValue;
    $update->execute($arrayKey);

    return $update->rowCount();

}

function connettiPdoAdvanced($dbName = null, $arrayParamGave = null)
{
    if (!$dbName) $dbName = PDA_DBNAME;
    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    $charset = PDA_DBCHARSET;
    $arrayParam = array_change_key_case($arrayParamGave, CASE_UPPER);
    if (isset($arrayParam['PWD'])) {
        $pwd = $arrayParam['PWD'];
    }
    else {
        $pwd = PDA_DBPASSWORD;
    }
    if (isset($arrayParam['USER'])) {
        $user = $arrayParam['USER'];
    }
    else {
        $user = PDA_DBNAME;
    }
    if (isset($arrayParam['SERVER'])) {
        $server = $arrayParam['SERVER'];
    }
    else {
        $server = PDA_SERVER;
    }
    try {
        $pdo = new PDO('mysql:host=' . $server . ';dbname=' . $dbName . ';charset=' . $charset, $user, $pwd, $attribute);
    }
    catch (PDOException $e) {
        echo $e->getMessage() . '<br/>';
        var_dump($e);

        return false; //gestione errore connessione
    }

    return $pdo;
}
/**
 * Tronca dal DB click tutte le tabelle che non hanno righe nel DB preset
 * se il DB preset non c'e', scrive una truncate fissa
 * @param PDO $pdoClick
 */
function dropTabelleNonPreset(PDO $pdoClick){
    $ckExists = $pdoClick->query('select SCHEMA_NAME from INFORMATION_SCHEMA.SCHEMATA where SCHEMA_NAME="preset"');
    $dbPreset = $ckExists->fetchColumn();
    $ckExists->closeCursor();
    if (!$dbPreset){
        /*
        $queryTruncate = 'truncate table allegatifaldoni;truncate table amministrazione;truncate table ascensoriscale;'
                . 'truncate table assemblea;truncate table assembleac;truncate table assembleacd;truncate table assembleag;'
                . 'truncate table assembleauic;truncate table assembleav;truncate table assembleavc;truncate table backnforw;'
                . 'truncate table banca;truncate table bancapagamenti;truncate table bonifici_creati;truncate table cancellazionefilews;'
                . 'truncate table causaliabicon;truncate table certificazioni;truncate table certificazioni770_condominio;'
                . 'truncate table certificazioni770_dettaglio;truncate table certificazioni770_rappresentante;truncate table certificazionif24;'
                . 'truncate table checkimportate;truncate table codicefiscalec;truncate table codicefiscaled;truncate table codificafornitori;'
                . 'truncate table colonneriepilogo;truncate table compensarate;truncate table condomini;truncate table condominio;'
                . 'truncate table condominiofornitori;truncate table condominioimpiantiunita;truncate table condominioposta;'
                . 'truncate table condominiosituazione;truncate table condominisubentro;truncate table consumid;truncate table consumit;'
                . 'truncate table consuntivod;truncate table consuntivodbanca;truncate table consuntivodetraibile;'
                . 'truncate table controlliquadratura;truncate table convocaunita;truncate table convocazioni;truncate table detrazionifiscali;'
                . 'truncate table dettagliocompensa;truncate table dettagliocredito;truncate table dettagliomora;truncate table dettaglioriepilogo;'
                . 'truncate table dettagliprevisione;truncate table dettaglirecupero;truncate table dettaglisubentro;'
                . 'truncate table dettcontabilizzapersonali;truncate table dettgiroimporti;truncate table dettgruppo;'
                . 'truncate table dettinsolvenze;truncate table dilazionirate;truncate table documenti;truncate table documentifaldoni;'
                . 'truncate table erroreflussi;truncate table errorifatture;truncate table esercizi;truncate table esercizi_insolvenze;'
                . 'truncate table esercizidetraibili;truncate table eserciziounitai;truncate table esiti;truncate table faldoni;'
                . 'truncate table fascicolastampe;truncate table fattprofessionisti;truncate table fatturef;truncate table fatturefconsuntivi;'
                . 'truncate table fatturefpagamenti;truncate table fatturefscadenze;truncate table filecaricati;'
                . 'truncate table fornitori;truncate table fornitoripagamenti;truncate table fornitoririepilogo;'
                . 'truncate table fornitoritipof;truncate table girorate;truncate table gruppospese;truncate table import_consumi;'
                . 'truncate table insolventi;truncate table insolvenze;truncate table intestazioneindirizzo;truncate table intestazionevoci;'
                . 'truncate table legali;truncate table letteredetrazioni;truncate table logmail;truncate table logmailallegati;'
                . 'truncate table logoperazioni;truncate table mavaccrediti;truncate table mod770_ad_regionale;truncate table mod770_dettaglio;'
                . 'truncate table mod770_erario;truncate table mod770_frontespizio;truncate table mod770_rappresentante;'
                . 'truncate table mod770_riassuntivi;truncate table mod770_riepilogo;truncate table mod770_riepilogo_tabella;'
                . 'truncate table moraesercizio;truncate table movimentibanca;truncate table movimentibancad;truncate table movimentibancam;'
                . 'truncate table movimenticcall;truncate table ordinegiorno;truncate table palazzi;truncate table parametri_insolvenze;'
                . 'truncate table pianoconti;truncate table pianocontisotto;truncate table pianorate;truncate table pianospesa;'
                . 'truncate table pianospesamillesimi;truncate table pianospesasotto;truncate table polizzeassicurative;'
                . 'truncate table polizzedati;truncate table polizzelocali;truncate table polizzevalori;truncate table posta;'
                . 'truncate table postaallegati;truncate table postaindirizzi;truncate table predisponibonifici;truncate table preparapianimav;'
                . 'truncate table prepararatemav;truncate table prepararitenute;truncate table presenze;truncate table presenzeunita;'
                . 'truncate table previsionestr;truncate table previsionestrx;truncate table previsioni;truncate table progressividocumentale;'
                . 'truncate table progressivocb;truncate table promemoria;truncate table promemoriae;truncate table promemoriaggr;'
                . 'truncate table promemoriar;truncate table quadroac_dettaglio;truncate table quadroac_testata;truncate table quadroacf;'
                . 'truncate table ratealteratesubentro;truncate table ratecredito;truncate table rated;truncate table ratedgiro;'
                . 'truncate table ratet;truncate table ratetgiro;truncate table ratetprecedenti;truncate table recuperorate;'
                . 'truncate table riassuntobonifici;truncate table riepilogo;truncate table riepilogostd;truncate table riepilogostg;'
                . 'truncate table riferimenticondominio;truncate table riferimenticondomino;truncate table ripartizioneautom;'
                . 'truncate table ripartizioneautomd;truncate table ripartizioni;truncate table ripartospese;truncate table ritenute;'
                . 'truncate table ritenuteall;truncate table ritenuteerrate;truncate table ritenutef24;truncate table scadenzariof;'
                . 'truncate table scadenzariofd;truncate table scadenzariorate;truncate table scadenzariorated;truncate table segnalazioni;'
                . 'truncate table sinistri;truncate table sinistrogaranzie;truncate table spesegenerali;truncate table statistiche;'
                . 'truncate table statistiched;truncate table statopatrimoniale;truncate table storicoscadenzefornitori;'
                . 'truncate table storicosubentri;truncate table subentri;truncate table subentriunita;truncate table tecnici;'
                . 'truncate table tipocsv;truncate table tipofaldoni;truncate table tipoflussi;truncate table unitaImmobiliare;'
                . 'truncate table unitaimmobiliaricondomini;truncate table unitaimmobiliariimpianti;truncate table unitaimmobiliarirspese;'
                . 'truncate table valori_presubentro;truncate table verbali;truncate table versamenti;truncate table versamentid;'
                . 'truncate table votazioni';
         * 
         */
            $queryTruncate = 'TRUNCATE TABLE ALLEGATIFALDONI;TRUNCATE TABLE AMMINISTRAZIONE;TRUNCATE TABLE ASCENSORISCALE;'
                . 'TRUNCATE TABLE ASSEMBLEA;TRUNCATE TABLE ASSEMBLEAC;TRUNCATE TABLE ASSEMBLEACD;TRUNCATE TABLE ASSEMBLEAG;'
                . 'TRUNCATE TABLE ASSEMBLEAUIC;TRUNCATE TABLE ASSEMBLEAV;TRUNCATE TABLE ASSEMBLEAVC;TRUNCATE TABLE BACKNFORW;'
                . 'TRUNCATE TABLE BANCA;TRUNCATE TABLE BANCAPAGAMENTI;TRUNCATE TABLE BONIFICI_CREATI;TRUNCATE TABLE CANCELLAZIONEFILEWS;'
                . 'TRUNCATE TABLE CAUSALIABICON;TRUNCATE TABLE CERTIFICAZIONI;TRUNCATE TABLE CERTIFICAZIONI770_CONDOMINIO;'
                . 'TRUNCATE TABLE CERTIFICAZIONI770_DETTAGLIO;TRUNCATE TABLE CERTIFICAZIONI770_RAPPRESENTANTE;TRUNCATE TABLE CERTIFICAZIONIF24;'
                . 'TRUNCATE TABLE CHECKIMPORTATE;TRUNCATE TABLE CODICEFISCALEC;TRUNCATE TABLE CODICEFISCALED;TRUNCATE TABLE CODIFICAFORNITORI;'
                . 'TRUNCATE TABLE COLONNERIEPILOGO;TRUNCATE TABLE COMPENSARATE;TRUNCATE TABLE CONDOMINI;TRUNCATE TABLE CONDOMINIO;'
                . 'TRUNCATE TABLE CONDOMINIOFORNITORI;TRUNCATE TABLE CONDOMINIOIMPIANTIUNITA;TRUNCATE TABLE CONDOMINIOPOSTA;'
                . 'TRUNCATE TABLE CONDOMINIOSITUAZIONE;TRUNCATE TABLE CONDOMINISUBENTRO;TRUNCATE TABLE CONSUMID;TRUNCATE TABLE CONSUMIT;'
                . 'TRUNCATE TABLE CONSUNTIVOD;TRUNCATE TABLE CONSUNTIVODBANCA;TRUNCATE TABLE CONSUNTIVODETRAIBILE;'
                . 'TRUNCATE TABLE CONTROLLIQUADRATURA;TRUNCATE TABLE CONVOCAUNITA;TRUNCATE TABLE CONVOCAZIONI;TRUNCATE TABLE DETRAZIONIFISCALI;'
                . 'TRUNCATE TABLE DETTAGLIOCOMPENSA;TRUNCATE TABLE DETTAGLIOCREDITO;TRUNCATE TABLE DETTAGLIOMORA;TRUNCATE TABLE DETTAGLIORIEPILOGO;'
                . 'TRUNCATE TABLE DETTAGLIPREVISIONE;TRUNCATE TABLE DETTAGLIRECUPERO;TRUNCATE TABLE DETTAGLISUBENTRO;'
                . 'TRUNCATE TABLE DETTCONTABILIZZAPERSONALI;TRUNCATE TABLE DETTGIROIMPORTI;TRUNCATE TABLE DETTGRUPPO;'
                . 'TRUNCATE TABLE DETTINSOLVENZE;TRUNCATE TABLE DILAZIONIRATE;TRUNCATE TABLE DOCUMENTI;TRUNCATE TABLE DOCUMENTIFALDONI;'
				. 'TRUNCATE TABLE ECCEZIONISOTTOCONTI;'
                . 'TRUNCATE TABLE ERROREFLUSSI;TRUNCATE TABLE ERRORIFATTURE;TRUNCATE TABLE ESERCIZI;TRUNCATE TABLE ESERCIZI_INSOLVENZE;'
                . 'TRUNCATE TABLE ESERCIZIDETRAIBILI;TRUNCATE TABLE ESERCIZIOUNITAI;TRUNCATE TABLE ESITI;TRUNCATE TABLE FALDONI;'
                . 'TRUNCATE TABLE FASCICOLASTAMPE;TRUNCATE TABLE FATTPROFESSIONISTI;TRUNCATE TABLE FATTUREF;TRUNCATE TABLE FATTUREFCONSUNTIVI;'
                . 'TRUNCATE TABLE FATTUREFPAGAMENTI;TRUNCATE TABLE FATTUREFSCADENZE;TRUNCATE TABLE FILECARICATI;'
                . 'TRUNCATE TABLE FORNITORI;TRUNCATE TABLE FORNITORIPAGAMENTI;TRUNCATE TABLE FORNITORIRIEPILOGO;'
                . 'TRUNCATE TABLE FORNITORITIPOF;TRUNCATE TABLE GIRORATE;TRUNCATE TABLE GRUPPOSPESE;TRUNCATE TABLE IMPORT_CONSUMI;'
                . 'TRUNCATE TABLE INSOLVENTI;TRUNCATE TABLE INSOLVENZE;TRUNCATE TABLE INTESTAZIONEINDIRIZZO;TRUNCATE TABLE INTESTAZIONEVOCI;'
                . 'TRUNCATE TABLE LEGALI;TRUNCATE TABLE LETTEREDETRAZIONI;TRUNCATE TABLE LOGMAIL;TRUNCATE TABLE LOGMAILALLEGATI;'
                . 'TRUNCATE TABLE LOGOPERAZIONI;TRUNCATE TABLE MAVACCREDITI;TRUNCATE TABLE MOD770_AD_REGIONALE;TRUNCATE TABLE MOD770_DETTAGLIO;'
                . 'TRUNCATE TABLE MOD770_ERARIO;TRUNCATE TABLE MOD770_FRONTESPIZIO;TRUNCATE TABLE MOD770_RAPPRESENTANTE;'
                . 'TRUNCATE TABLE MOD770_RIASSUNTIVI;TRUNCATE TABLE MOD770_RIEPILOGO;TRUNCATE TABLE MOD770_RIEPILOGO_TABELLA;'
                . 'TRUNCATE TABLE MORAESERCIZIO;TRUNCATE TABLE MOVIMENTIBANCA;TRUNCATE TABLE MOVIMENTIBANCAD;TRUNCATE TABLE MOVIMENTIBANCAM;'
                . 'TRUNCATE TABLE MOVIMENTICCALL;TRUNCATE TABLE ORDINEGIORNO;TRUNCATE TABLE PALAZZI;TRUNCATE TABLE PARAMETRI_INSOLVENZE;'
                . 'TRUNCATE TABLE PIANOCONTI;TRUNCATE TABLE PIANOCONTISOTTO;TRUNCATE TABLE PIANORATE;TRUNCATE TABLE PIANOSPESA;'
                . 'TRUNCATE TABLE PIANOSPESAMILLESIMI;TRUNCATE TABLE PIANOSPESASOTTO;TRUNCATE TABLE POLIZZEASSICURATIVE;'
                . 'TRUNCATE TABLE POLIZZEDATI;TRUNCATE TABLE POLIZZELOCALI;TRUNCATE TABLE POLIZZEVALORI;TRUNCATE TABLE POSTA;'
                . 'TRUNCATE TABLE POSTAALLEGATI;TRUNCATE TABLE POSTAINDIRIZZI;TRUNCATE TABLE PREDISPONIBONIFICI;TRUNCATE TABLE PREPARAPIANIMAV;'
                . 'TRUNCATE TABLE PREPARARATEMAV;TRUNCATE TABLE PREPARARITENUTE;TRUNCATE TABLE PRESENZE;TRUNCATE TABLE PRESENZEUNITA;'
                . 'TRUNCATE TABLE PREVISIONESTR;TRUNCATE TABLE PREVISIONESTRX;TRUNCATE TABLE PREVISIONI;TRUNCATE TABLE PROGRESSIVIDOCUMENTALE;'
                . 'TRUNCATE TABLE PROGRESSIVOCB;TRUNCATE TABLE PROMEMORIA;TRUNCATE TABLE PROMEMORIAE;TRUNCATE TABLE PROMEMORIAGGR;'
                . 'TRUNCATE TABLE PROMEMORIAR;TRUNCATE TABLE QUADROAC_DETTAGLIO;TRUNCATE TABLE QUADROAC_TESTATA;TRUNCATE TABLE QUADROACF;'
                . 'TRUNCATE TABLE RATEALTERATESUBENTRO;TRUNCATE TABLE RATECREDITO;TRUNCATE TABLE RATED;TRUNCATE TABLE RATEDGIRO;'
                . 'TRUNCATE TABLE RATET;TRUNCATE TABLE RATETGIRO;TRUNCATE TABLE RATETPRECEDENTI;TRUNCATE TABLE RECUPERORATE;'
                . 'TRUNCATE TABLE RIASSUNTOBONIFICI;TRUNCATE TABLE RIEPILOGO;TRUNCATE TABLE RIEPILOGOSTD;TRUNCATE TABLE RIEPILOGOSTG;'
                . 'TRUNCATE TABLE RIFERIMENTICONDOMINIO;TRUNCATE TABLE RIFERIMENTICONDOMINO;TRUNCATE TABLE RIPARTIZIONEAUTOM;'
                . 'TRUNCATE TABLE RIPARTIZIONEAUTOMD;TRUNCATE TABLE RIPARTIZIONI;TRUNCATE TABLE RIPARTOSPESE;TRUNCATE TABLE RITENUTE;'
                . 'TRUNCATE TABLE RITENUTEALL;TRUNCATE TABLE RITENUTEERRATE;TRUNCATE TABLE RITENUTEF24;TRUNCATE TABLE SCADENZARIOF;'
                . 'TRUNCATE TABLE SCADENZARIOFD;TRUNCATE TABLE SCADENZARIORATE;TRUNCATE TABLE SCADENZARIORATED;TRUNCATE TABLE SEGNALAZIONI;'
                . 'TRUNCATE TABLE SINISTRI;TRUNCATE TABLE SINISTROGARANZIE;TRUNCATE TABLE SPESEGENERALI;TRUNCATE TABLE STATISTICHE;'
                . 'TRUNCATE TABLE STATISTICHED;TRUNCATE TABLE STATOPATRIMONIALE;TRUNCATE TABLE STORICOSCADENZEFORNITORI;'
                . 'TRUNCATE TABLE STORICOSUBENTRI;TRUNCATE TABLE SUBENTRI;TRUNCATE TABLE SUBENTRIUNITA;TRUNCATE TABLE TECNICI;'
                . 'TRUNCATE TABLE TIPOCSV;TRUNCATE TABLE TIPOFALDONI;TRUNCATE TABLE TIPOFLUSSI;TRUNCATE TABLE UNITAIMMOBILIARI;'
                . 'TRUNCATE TABLE UNITAIMMOBILIARICONDOMINI;TRUNCATE TABLE UNITAIMMOBILIARIIMPIANTI;TRUNCATE TABLE UNITAIMMOBILIARIRSPESE;'
                . 'TRUNCATE TABLE VALORI_PRESUBENTRO;TRUNCATE TABLE VERBALI;TRUNCATE TABLE VERSAMENTI;TRUNCATE TABLE VERSAMENTID;'
                . 'TRUNCATE TABLE VOTAZIONI';
    }else{
        $pdoClick->query('SET group_concat_max_len = 1500000');
        $queryString = $pdoClick->query('select group_concat(TruncateQuery separator ";")
        from (
            select concat("truncate table ", TABLE_NAME) TruncateQuery
            from INFORMATION_SCHEMA.TABLES
            where
                TABLE_SCHEMA = "preset"
                and TABLE_ROWS=0)TruncateTables');
        $queryTruncate = $queryString->fetchColumn();
        //echo $queryTruncate;
        $queryString->closeCursor();
    }
    $arrayDelete = explode(';', $queryTruncate);
    foreach($arrayDelete as $query){
        $pdoClick->exec($query);
    }
}

/**
 * @param PDO $pdo
 * @param string $strTab
 * @param array  $arrayCampi
 * @param bool $boolMultipli
 * @return int|array
 */
function findIDPdo($pdo,$strTab,$arrayCampi,$boolMultipli=false){
    if (!is_array($arrayCampi)){
        return false;
    }
    $alias = '';
    if (stripos($strTab,'join')){
        $strTab = trim($strTab);
        $separaSpazi = explode(' ',$strTab);
        foreach ($separaSpazi as $indice => $valore){
            if (!strlen($valore)){
                unset($separaSpazi[$indice]);
            }

        }
        $separaSpazi = array_values($separaSpazi);
        $chiaveJoin = array_search(strtolower('join'), array_map('strtolower', $separaSpazi));
        $chiaveJoin-=2;
        $alias = $separaSpazi[$chiaveJoin].'.';
    }
    $strQuery='SELECT '.$alias.'ID FROM '.$strTab.' WHERE ';
    $arrayBlocchi=array();
    foreach ($arrayCampi as $campo=>$valore){
        $confronto = '=';
        if (is_array($valore)){
            $confronto = $valore[1];
            $arrayCampi[$campo] = $valore[0];
        }
        $campoCorretto = $campo;
        if (strpos($campo,'.')){
            $campoCorretto = str_replace('.','',$campo);
            $arrayCampi[$campoCorretto] = $arrayCampi[$campo];
            unset($arrayCampi[$campo]);
        }
        $arrayBlocchi[]=$campo.' '.$confronto.':'.$campoCorretto;
    }
    $arrayRisultati=array();
    $strQuery.=implode(' AND ',$arrayBlocchi);
    $prepareLeggiID=$pdo->prepare($strQuery);
    $prepareLeggiID->execute($arrayCampi);
    while ($idLetta=$prepareLeggiID->fetchColumn()) $arrayRisultati[]=$idLetta;
    $prepareLeggiID->closeCursor();
    if (!count($arrayRisultati)) $arrayRisultati=false;
    if (!$boolMultipli and is_array($arrayRisultati)) $arrayRisultati=$arrayRisultati[0];
    return $arrayRisultati;
}