<?php

/**
 * Funzione di connessione a PDO. La funzione consente di specificare il database a cui collegarsi.
 * Oltre questo, permette il passaggio dell'array indicizzato per i dati di connessione forzati composti da:
 * PWD - Password
 * USER - Username
 * SERVER - Indirizzo del server
 * ATTRIBUTE - attributi di connessione
 * CHARSET - Charset da utilizzare.
 * @param string|null $dbName
 * @param array|null
 * @return bool|PDO
 */
function connettiPdo($dbName = null,$arrayParam = null){
    if (!$dbName) $dbName = CLICK_DATABASE;
    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    $server = CLICK_SERVER;
    $user = CLICK_NAME;
    $pwd = CLICK_PWD;
    $charset = CLICK_CHARSET;
    if (is_array($arrayParam)){
        $arrayParam = array_change_key_case($arrayParam,CASE_UPPER);
        if (isset($arrayParam['PWD'])) {
            $pwd = $arrayParam['PWD'];
        }
        if (isset($arrayParam['USER'])) {
            $user = $arrayParam['USER'];
        }
        if (isset($arrayParam['SERVER'])) {
            $server = $arrayParam['SERVER'];
        }
        if (isset($arrayParam['ATTRIBUTE'])) {
            $attribute = $arrayParam['ATTRIBUTE'];
        }
        if (isset($arrayParam['CHARSET'])) {
            $charset = $arrayParam['CHARSET'];
        }
    }
    try{
        $pdo=new PDO('mysql:host='.$server.';dbname='.$dbName.';charset='.$charset,$user,$pwd, $attribute);

    }catch (PDOException $e){
        return false; //gestione errore connessione
    }
    return $pdo;
}


function connettiPdoGestaf($dbName = null,$arrayParam = null){
    if (!$dbName) $dbName = GESTAF_DATABASE;
    $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    $server = GESTAF_SERVER;
    $user = GESTAF_NAME;
    $pwd = GESTAF_PWD;
    $charset = GESTAF_CHARSET;
    if (is_array($arrayParam)){
        $arrayParam = array_change_key_case($arrayParam,CASE_UPPER);
        if (isset($arrayParam['PWD'])) {
            $pwd = $arrayParam['PWD'];
        }
        if (isset($arrayParam['USER'])) {
            $user = $arrayParam['USER'];
        }
        if (isset($arrayParam['SERVER'])) {
            $server = $arrayParam['SERVER'];
        }
        if (isset($arrayParam['ATTRIBUTE'])) {
            $attribute = $arrayParam['ATTRIBUTE'];
        }
        if (isset($arrayParam['CHARSET'])) {
            $charset = $arrayParam['CHARSET'];
        }
    }
    try{
        $pdo=new PDO('mysql:host='.$server.';dbname='.$dbName.';charset='.$charset,$user,$pwd, $attribute);

    }catch (PDOException $e){
        return false; //gestione errore connessione
    }
    return $pdo;
}

/**
 * @param $connessione PDO
 *
 */
function disconnettiPdo(&$connessione){
    $connessione=null;
}
/**
 * @param $pdo PDO
 * @param null $errore
 * @param int $codice
 */
function bloccaPdo($pdo,$errore=null,$codice=0){
    if ((isIstance($pdo)) and $pdo->inTransaction()) errorePdo($errore,$codice);
    return;
}
/**
 * @param PDO $pdo
 * @param $cercaTabella
 * @param $idTabella
 * @param int $intControllaDettagli
 */
function eliminaDettagliSenzaTestate($pdo,$cercaTabella,$idTabella,$intControllaDettagli=2){
    $arrayTabelle = array();
    $arrayCondominio = array();
    $prepareCiclaTabelle = $pdo->prepare('SELECT C.TABLE_NAME FROM information_schema.COLUMNS C where C.TABLE_SCHEMA =? AND C.COLUMN_NAME =?');
    $blobTabelle = queryPreparedPdo($pdo,$prepareCiclaTabelle,array(CLICK_DATABASE,$idTabella),'p');
    while ($tabella = $blobTabelle->fetchColumn()){
        $prepareCiclaIntrovabili= $pdo->prepare('SELECT TB.'.$idTabella.' FROM '.$tabella.' TB LEFT JOIN '.$cercaTabella.' TC ON TC.ID = TB.'.$idTabella.' WHERE TC.ID IS NULL AND TB.'.$idTabella.' != 0');
        $blobIntrovabili = queryPreparedPdo($pdo,$prepareCiclaIntrovabili,null,'p');
        while ($condominioIntrovabile = $blobIntrovabili->fetchColumn()){
            if (!in_array($condominioIntrovabile,$arrayCondominio)) $arrayCondominio[]= $condominioIntrovabile;
            if (!in_array($tabella,$arrayTabelle)) $arrayTabelle[] =$tabella;
        }
        $blobIntrovabili->closeCursor();
    }
    $blobTabelle->closeCursor();
    $condominii = implode(',',$arrayCondominio);
    foreach ($arrayTabelle as $tabella){
        if (!$condominii) break;
        $somma =delLegacy($pdo,$tabella,$idTabella.' IN ('.$condominii.')',null,true,$intControllaDettagli);
    }

}
/**
 * Funzione per l'esecuzione delle query tramite prepare/execute
 * @param PDO|null $pdo classe di gestione database
 * @param string|PDOStatement $stringPrepared la stringa preparativa. Sostituire le variabili con ?
 * @param array|matrix $arrayElementi l'array mono/bidimensionale contenente i valori
 * (NOTA: per riga, DEVONO coincidere coi punti interrogativi!!!)
 * @param char|string $chrTipoRitorno variabile 'classica' (b=blob, f=fetch/array, v=valore)
 * @param int $tipoFetch tipo di ritorno dato richiesto
 * @param bool $boolMatrice se true, $arrayElementi verra' considerato come un gruppo di righe
 * @return string|array|null|value|matrix|PDOStatement ritorna il valore, uguale alla forma di queryPdo
 * ove $boolMatrice fosse true, ne sarebbe un'array di tali elementi.
 */
function oldQueryPreparedPdo($pdo,$stringPrepared,$arrayElementi,$chrTipoRitorno='b',$tipoFetch=PDO::FETCH_BOTH,$boolMatrice=false){
    $boolConnettiLocale=(!$pdo);
    if ($boolConnettiLocale) $pdo=connettiPdo();
    if (!$stringPrepared ) bloccaPdo($pdo);
    /** @var $queryHandler PDOStatement */
    $queryHandler=($stringPrepared instanceof PDOStatement)?$stringPrepared:$pdo->prepare($stringPrepared);
    if (!$boolMatrice) $arrayElementi=array($arrayElementi);
    $arrayRitorni=array();
    foreach($arrayElementi as $rigaInCorso){
        $queryHandler->execute($rigaInCorso);
        switch (strtolower($chrTipoRitorno)){
            case 'p':
                $ritorno=$queryHandler;
                break;
            case 'b':
                $ritorno=$queryHandler->fetchAll($tipoFetch);
                break;
            case 'f':
                $ritorno=$queryHandler->fetch($tipoFetch);
                break;
            case 'v':
                $ritorno=$queryHandler->fetchColumn();
                break;
        }
        $arrayRitorni[]=$ritorno;
        if ('p'!=strtolower($chrTipoRitorno)) $queryHandler->closeCursor();
    }
    if (!$boolMatrice) $arrayRitorni=$arrayRitorni[0];
    if ($boolConnettiLocale) $pdo=null;
    return $arrayRitorni;
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
 * Semplice richiamo all'errore PDO
 * @param null $strMessaggio
 * @param int $intCodice
 * @throws PDOException
 */
function errorePdo($strMessaggio=null,$intCodice=0){
    throw new PDOException($strMessaggio,$intCodice);
}

/**
 * @param PDO $pdo
 * @param string $strTab
 * @param array  $arrayCampi
 * @param bool $boolMultipli
 * @return int|array
 */
function findIDPdo($pdo,$strTab,$arrayCampi,$boolMultipli=false){
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
/**
 * Funzione di interrogazione per le query. Le transazione NON sono gestite, MA
 * se passato l'oggetto PDO gestente la transazione, la transazione viene effettuata
 * @param $strQuery
 * @param null $pdo
 * @param string $tipoResult
 * @param int $tipoFetch
 * @return mixed|string|PDOStatement
 */
function queryPdo($strQuery,$pdo=null,$tipoResult='b',$tipoFetch=PDO::FETCH_BOTH){
    if (!$strQuery) return;
    $boolConnessione=!($pdo instanceof PDO);
    if ($boolConnessione) {
        $pdo=connettiPdo();
    }
    if (!$pdo instanceof PDO) {
        phpToAlert('Attezione! Connesione fallita!');
        die;
    }

    $queryHandler=$pdo->query($strQuery);
    switch(strtolower($tipoResult)):
        case 'p':
            $ritorno=$queryHandler;
            break;
        case 'b':
            $ritorno=$queryHandler->fetchAll($tipoFetch);
            break;
        case 'f':
            $ritorno=$queryHandler->fetch($tipoFetch);
            break;
        case 'v':
            $ritorno=$queryHandler->fetchColumn();
            break;
        case 'c':
            $ritorno=$queryHandler->rowCount();
            break;
    endswitch;
    if ('p'!=strtolower($tipoResult)) $queryHandler->closeCursor();
    if ($boolConnessione) $pdo=null;
    return $ritorno;
}

/**
 * Funzione di conto dei record dispobili in base ai parametri
 * @param $strTable
 * @param null $pdo
 * @param null $strWhere
 * @param string $strFilter
 * @return mixed
 */
function countQueryPdo($strTable,$pdo=null,$strWhere=null,$strFilter='*'){
    if (!$strTable) return;
    $boolConnessione=($pdo === null);
    if ($boolConnessione)$pdo=connettiPdo();
    $strQuery=($strWhere)?"SELECT COALESCE(COUNT({$strFilter}),0) FROM {$strTable} WHERE {$strWhere}":"SELECT COALESCE(COUNT({$strFilter}),0) FROM {$strTable}";
    $query=$pdo->query($strQuery);
    $intRighe=$query->fetchColumn();
    $query->closeCursor();
    if ($boolConnessione) $pdo=null;
    return $intRighe;
}

/**
 * @param $strTabella
 * @param $arrayCampi
 * @param $intValore
 * @param null $pdo
 * @param bool $boolTransizione
 * @param string $strNomeCampo
 * @return bool|null
 */
function duplicaRecordPdo($strTabella,$arrayCampi,$intValore,$pdo=null,$boolTransizione=true,$strNomeCampo='ID'){
    if (!is_numeric($intValore)) return null;
    $boolConnessione=($pdo === null);
    if ($boolConnessione)$pdo=connettiPdo();

    if (is_array($arrayCampi)) $arrayCampi=implode(',',$arrayCampi);
    else if (!$arrayCampi) $arrayCampi=queryPdo('SELECT GROUP_CONCAT(DISTINCT COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="'.$strTabella.'" AND COLUMN_NAME <> "'.$strNomeCampo.'" ',$pdo,'v');
    $rigaDaCopiare=  queryPdo("SELECT $arrayCampi FROM $strTabella WHERE $strNomeCampo=$intValore",$pdo,'f',PDO::FETCH_NUM);
    $arrayCampi=explode(',',$arrayCampi);
    if ($boolConnessione and $boolTransizione):
        try{
            $pdo->beginTransaction();
            $lastResult=insertPdo($strTabella,$arrayCampi,$rigaDaCopiare,$pdo,true);
            $pdo->commit();

        }catch (PDOException $e){
            $pdo->rollBack();
            $lastResult=null;
        }
    else:
        $lastResult=insertPdo($strTabella,$arrayCampi,$rigaDaCopiare,$pdo,true);
    endif;
    if ($boolConnessione) $pdo=null;
    return $lastResult;
}

/**
 * @param PDO    $pdo
 * @param        $strTabella
 * @param        $intRiga
 * @param string $escludi
 * @param string $identificatoreRiga
 * @param int    $fetchMode
 * @param null   $dbName
 * @return array
 */
function leggiValoriTranne($pdo,$strTabella,$intRiga,$escludi='ID',$identificatoreRiga='ID',$fetchMode=PDO::FETCH_ASSOC,$dbName=null){

    if (!is_array($escludi)) $escludi=explode(',',$escludi);

    if(!$dbName) $dbName = CLICK_DATABASE;
    $arrayParametri =array('tabella'=>$strTabella,'dbNome'=>$dbName);
    $arrayCampiDaTornare = array();
    $strQueryInfoSchema='SELECT DISTINCT C.COLUMN_NAME FROM information_schema.COLUMNS C WHERE C.TABLE_NAME=:tabella AND C.TABLE_SCHEMA=:dbNome ';
    $arrayEscludiCampi= array();
    $contatore = 0;
    foreach ($escludi as $id => $valore){
        if (!$valore){
            unset($escludi[$id]);
        }else{

            $arrayEscludiCampi[]=':inner'.$contatore;
            $arrayParametri['inner'.$contatore]=$valore;
            ++$contatore;
        }

    }

    if ($arrayEscludiCampi){
        $strQueryInfoSchema.=' AND C.COLUMN_NAME NOT IN (';
        $strQueryInfoSchema.=implode(',',$arrayEscludiCampi);
        $strQueryInfoSchema.=')';
    }



    $blobNomeCampi = queryPreparedPdo($pdo,$strQueryInfoSchema,$arrayParametri,'p');

    while ($nomeCampi = $blobNomeCampi->fetchColumn()){
        $arrayCampiDaTornare[]=$nomeCampi;
    }
    $blobNomeCampi->closeCursor();
    if (!$arrayCampiDaTornare){
        return;
    }

    $strCampi = implode(',',$arrayCampiDaTornare);
    $strQueryTabella = 'SELECT '.$strCampi.' FROM '.$dbName.'.'.$strTabella.' WHERE '.$identificatoreRiga.'= ?';

    return queryPreparedPdo($pdo,$strQueryTabella,array($intRiga),'f',$fetchMode);
}
/**

 * @param $strTabella
 * @param null $pdo
 * @param $intRiga
 * @param string $escludi
 * @param int $fetchMode
 * @param string $identificatore
 * @return mixed
 */
function leggiCampiTranne($strTabella,$pdo=null,$intRiga,$escludi='ID',$fetchMode=PDO::FETCH_BOTH,$identificatore='ID'){
    if ($escludi){
        if (!is_array($escludi)) $escludi=explode(',',$escludi);
        foreach ($escludi as $id => $escluso) $escludi[$id]='"'.$escluso.'"';
        $escludi=implode(',',$escludi);
        $escludi=' AND COLUMN_NAME NOT IN ('.$escludi.')';
    }
    $boolConnessione=($pdo === null);
    if ($boolConnessione)$pdo=connettiPdo();
    $arrayCampi=queryPdo('SELECT GROUP_CONCAT(DISTINCT COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="'.$strTabella.'" '.$escludi,$pdo,'v');
    $lastResult=queryPdo("SELECT {$arrayCampi} FROM {$strTabella} WHERE {$identificatore}={$intRiga}",$pdo,'f',$fetchMode);
    if ($boolConnessione) $pdo=null;
    return $lastResult;
}
/**
 * Funzione di insert con gestione di transazione, insert di più righe in un solo ciclo,
 * transazione completa del multi insert, con possibilita' di ignorare l'avvenire della transazione
 * @param string $strTabella
 * @param array|string $arrayCampi
 * @param array|matrice $arrayValori
 * @param null|PDO $pdo - oggetto connessione database. Se nullo, connetto
 * @param bool $boolUltimoInserito - se true, torno l'ultimo ID inserito
 * @param bool $boolMatrice - se true, $arrayValori verra' considerato matrice: ogni elemento e' una nuova riga di valori
 * @param bool $boolAlteraTransazione - se PDO e' nullo, ove presente, attiva la transizione
 * @return bool
 * @internal param int $intConnTrans
 */
function insertPdo($strTabella,$arrayCampi,$arrayValori,$pdo=null,$boolUltimoInserito=false,$boolMatrice=false,$boolAlteraTransazione=true){
    //verifico che siano inseriti i campi e che abbiano un nome
    $boolConnessione=($pdo === null);
    if ($boolConnessione)$pdo=connettiPdo();
    if (!$arrayCampi) bloccaPdo($pdo,'Nessun campo passato');
    $arrayRichiesti=array();
    if (is_array($arrayCampi)): //se i campi sono un'array, verifico se l'ultimo elemento e' un array
        //in caso affermativo, lo considero come gruppo di elementi che NON possano essere zero
        $arrayRichiesti=end($arrayCampi);
        if (is_array($arrayRichiesti)){
            array_pop($arrayCampi);
            foreach ($arrayRichiesti as $id => $campo) $arrayRichiesti[$id]=array_search($campo,$arrayCampi);
        } else {
            $arrayRichiesti=array();
        }
    else:
        $arrayCampi=explode(',',$arrayCampi);
    endif;
    foreach ($arrayCampi as $nomeCampo) if (!$nomeCampo) bloccaPdo($pdo,'Non possono essere passati campi da nome nullo');
    if ($boolMatrice){ //se e' una matrice, verifico che TUTTE le righe da inserire si equivalgano in numero di elementi
        $intNumValori=count($arrayValori[0]);
        foreach($arrayValori as $rigaValori){
            if ($intNumValori != count($rigaValori)) bloccaPdo($pdo,'Le righe devono essere tutte lunghe uguali');
        }
    } else $intNumValori=count($arrayValori);
    //se il numero di campi e valori non corrisponde, torno indietro
    if ($intNumValori != count($arrayCampi)) {
        bloccaPdo($pdo, 'il numero di campi e di valori non corrispondono');
        var_dump($arrayCampi);
        var_dump($arrayValori);
    }
    //creo, in base ai campi, i 'jolly' da usare
    $strPosizioni=array();
    foreach($arrayCampi as $id) $strPosizioni[]='?';
    $strPosizioni=implode(',',$strPosizioni);
    //preparo la query
    $queryHandler=$pdo->prepare("INSERT INTO {$strTabella} (".implode(',',$arrayCampi).") VALUES ({$strPosizioni})");

    //piccolo accorgimento per non ricopiare la struttura in base alla matrice di insert
    $intNumRighe=($boolMatrice)?count($arrayValori):1;
    if ($boolConnessione and $boolAlteraTransazione){
        try{ //eseguo la transazione
            $pdo->beginTransaction();
            for ($i=0;$i<$intNumRighe;$i++):
                $arrayRigaCorrente=($boolMatrice)?$arrayValori[$i]:$arrayValori;
                foreach ($arrayRichiesti as $valoreRichiesto) if (!$arrayRigaCorrente[$valoreRichiesto]) bloccaPdo($pdo,"Il {$arrayCampi[$valoreRichiesto]} non &egrave; valido!");
                foreach ($arrayRigaCorrente as $posizione => $valore) if (null===$valore) $arrayRigaCorrente[$posizione]='';
                $queryHandler->execute($arrayRigaCorrente);
            endfor;
            $ultimoId= $pdo->lastInsertId();
            $pdo->commit();
        }catch (PDOException $e){
            $ultimoId=false;
            $pdo->rollBack();

        }
    }else{
        //se non intendo effettuare una transazione (o richiamo la procedura da un'altra in corso...)
        for ($i=0;$i<$intNumRighe;$i++):
            $arrayRigaCorrente=($boolMatrice)?$arrayValori[$i]:$arrayValori;
            foreach ($arrayRichiesti as $valoreRichiesto) if (!$arrayRigaCorrente[$valoreRichiesto])  bloccaPdo($pdo,"Il {$arrayCampi[$valoreRichiesto]} non &egrave; valido!");
            foreach ($arrayRigaCorrente as $posizione => $valore) if (null===$valore) $arrayRigaCorrente[$posizione]='';
            $queryHandler->execute($arrayRigaCorrente);
        endfor;
        $ultimoId= $pdo->lastInsertId();
    }
    if ($boolConnessione) $pdo=null;
    if ($boolUltimoInserito) return $ultimoId;
}

/**
 * Funzione per l'eliminazione di determinate righe da una tabella.
 * @param string $strTabella nome tabella
 * @param mixed|array $elementi valore o valori (come array o stringa separate da virgole) da eliminare
 * @param string $chiave nome colonna da verificare, se null cancella tutti gli elementi della tabella
 * @param null $pdo oggetto di connessione preesistente
 * @param bool $boolAlteraTransazione se attivo, altera la connessione
 */
function deletePdo($strTabella,$elementi,$chiave='ID',$pdo=null,$boolAlteraTransazione=true){
    $boolConnessione=($pdo === null);
    if ($boolConnessione)$pdo=connettiPdo();
    if ((!$chiave) && !is_null($chiave)) bloccaPdo($pdo,'La chiave di ricerca DEVE essere valida.');
    if (!is_array($elementi)) $elementi=explode(',',$elementi);
    $queryDelete =  "DELETE FROM {$strTabella} WHERE {$chiave}=?";
    if ($chiave == null)  $queryDelete = "DELETE FROM {$strTabella}";
    $queryHandler=$pdo->prepare($queryDelete);
    if ($boolConnessione and $boolAlteraTransazione):
        try{ //eseguo la transazione
            $pdo->beginTransaction();
            if (is_null($chiave)){
                $queryHandler->execute();
            }else{
                foreach($elementi as $id):
                    $arrayRigaCorrente=array($id);
                    $queryHandler->execute($arrayRigaCorrente);
                endforeach;
            }
            $pdo->commit();
        }catch (PDOException $e){
            //anche una sola query fallita, genera l'errore che torna al valore precedente
            $pdo->rollBack();
        }
    else:
        //se non intendo effettuare una transazione (o richiamo la procedura da un'altra in corso...)
        if (is_null($chiave)){
            $queryHandler->execute();
        }else{
            foreach($elementi as $id):
                $arrayRigaCorrente=array($id);
                $queryHandler->execute($arrayRigaCorrente);
            endforeach;
        }
    endif;
    if ($boolConnessione) $pdo=null;
}

/**
 * Funzione di UPDATE di una tabella.
 * ATTENZIONE: La funzione permette di impostare quali parametri sono da considerarsi OBBLIGATORI, ovvero non
 * accettati con valori nulli/falsi/zeri, Affinche' questo avvenga, ricordarsi due cose: i campi ed i valori
 * DEVONO essere passati come array (anche in caso siano uno solo), INOLTRE, l'ultimo elemento dei campi,
 * DEVE essere un'array dei campi non nulli.
 *
 * Affinche' la chiamata risulti corretta, passare il riferimento all'oggetto nelle variabili. Passare null per effettuare
 * una connessione interrogativa al volo.
 * @param string $strTabella tabella in cui inserire i valori
 * @param string|array $arrayCampi campi da aggiornare
 * @param string|array $arrayValori valori da scrivere
 * @param string|array $elementi ID (elementi) da modificare.
 * @param null|PDO $pdo
 * @param string $chiave
 * @param bool $boolAlteraTransazione
 */
function updatePdo($strTabella,$arrayCampi,$arrayValori,$elementi,$pdo=null,$chiave='ID',$boolAlteraTransazione=true){
    $boolConnessione=($pdo === null);
    if ($boolConnessione)  $pdo=connettiPdo();
    $arrayRichiesti=array();
    if (is_array($arrayCampi)): //se i campi sono un'array, verifico se l'ultimo elemento e' un array
        //in caso affermativo, lo considero come gruppo di elementi che NON possano essere zero
        $arrayRichiesti=end($arrayCampi);
        if (is_array($arrayRichiesti)){
            array_pop($arrayCampi);
            foreach ($arrayRichiesti as $id => $campo) $arrayRichiesti[$id]=array_search($campo,$arrayCampi);
            if (1==count($arrayCampi)) $arrayCampi=$arrayCampi[0];
        }
        else $arrayRichiesti=array();
    endif;
    if (is_array($arrayCampi) != is_array($arrayValori)) bloccaPdo($pdo,'Verificare che gli elementi campi e valori dello stesso tipo (array o singoli)');
    if (!is_array($arrayCampi)):

        if (!$arrayCampi) bloccaPdo($pdo,'Nessun campo fornito!');
        $arrayCampi=explode(',',$arrayCampi);
        $arrayValori=explode(',',$arrayValori);
    endif;
    if (count($arrayCampi)!= count($arrayValori)) bloccaPdo($pdo,'Il rapporto campo/valore non e\' soddisfatto.');
    //controllo ogni singolo campo dell'array. Non accetto valori bianchi
    foreach ($arrayCampi as $campo) if (!$campo)bloccaPdo($pdo,'Non possono esserci nomi vuoti tra i campi');
    //recupero dall'array campi, quali DEVONO avere valore diverso da zero!
    for ($i=0;$i<count($arrayCampi);$i++) $arrayCampi[$i].='= ?';
    $intNumElementi=count($arrayValori);
    $queryHandler=$pdo->prepare("UPDATE {$strTabella} SET ".implode(',',$arrayCampi)." WHERE {$chiave}=?");
    if (!is_array($elementi)) $elementi=explode(',',$elementi);
    if ($boolConnessione and $boolAlteraTransazione){
        try{ //eseguo la transazione
            $pdo->beginTransaction();
            for ($i=0;$i<count($elementi);$i++):
                foreach ($arrayRichiesti as $valoreRichiesto) if (!$arrayValori[$valoreRichiesto]) bloccaPdo($pdo,'il campo '.str_replace('?=',null,$arrayCampi[$valoreRichiesto]).' non &egrave; valido!');
                $arrayValori[$intNumElementi]=$elementi[$i];
                foreach ($arrayValori as $posizione => $valore) if (null===$valore) $arrayValori[$posizione]='';
                $queryHandler->execute($arrayValori);
            endfor;
            $pdo->commit();

        }catch (Exception $e){
            //anche una sola query fallita, genera l'errore che torna al valore precedente
            $pdo->rollBack();
        }
    }else{
        //se non intendo effettuare una transazione (o richiamo la procedura da un'altra in corso...)
        for ($i=0;$i<count($elementi);$i++):
            foreach ($arrayRichiesti as $valoreRichiesto) if (!$arrayValori[$valoreRichiesto]) bloccaPdo($pdo,'il campo '.str_replace('?=',null,$arrayCampi[$valoreRichiesto]).' non &egrave; valido!');
            $arrayValori[$intNumElementi]=$elementi[$i];
            foreach ($arrayValori as $posizione => $valore) if (null===$valore) $arrayValori[$posizione]='';
            $queryHandler->execute($arrayValori);
        endfor;
    }
    if ($boolConnessione) $pdo=null;
}

/**
 * Funzione per effettuare l'INSERT di un array $array[<campoDB>]=>valore.
 * Nota: ogni valore giunto null verra' eliminato.
 * @param PDO $pdo
 * @param string $strTabella
 * @param array $arrayKey
 * @param bool $boolMatrice
 *
 * @return null|string
 */
function insertKeyArrayPdo($pdo,$strTabella,$arrayKey,$boolMatrice=false){
    if ( !boolArrayAssociativo($arrayKey) or !is_array($arrayKey) or !$arrayKey) return null;
    if (!$strTabella){
        bloccaPdo($pdo,'Errore grave. La tabella non e\' arrivata.');
        return 'Errore grave. La tabella non e\' arrivata.';
    }
    $boolConnetti = (!$pdo);
    if ($boolConnetti){
        $pdo=connettiPdo();
    }
    $arrayCampi=array();
    $intNumRigheMax=0;
    $arrayPosizioni=array();
    foreach($arrayKey as $key=>$value) {
        if (null===$value or ($boolMatrice and !$value)) {
            unset($arrayKey[$key]);
            continue;
        }
        $arrayCampi[]=$key;
        $arrayPosizioni[]=':'.$key;
    }
    if (!$arrayCampi) return null;
    if ($boolMatrice){
        foreach ($arrayKey as $value) if ($intNumRigheMax < count($value))$intNumRigheMax = count($value);

        foreach($arrayKey as $key => $value){
            if ($intNumRigheMax==count($value)) continue;
            for ($i=count($value);$i<$intNumRigheMax;$i++) $arrayKey[$key][$i]='';
        }
    }
    $insert=$pdo->prepare('INSERT INTO '.$strTabella.' ('.implode(',',$arrayCampi).') VALUES ('.implode(',',$arrayPosizioni).')');
    if ($boolMatrice):
        $arrayTemp=array();
        for ($i=0;$i<$intNumRigheMax;$i++):
            foreach($arrayKey as $key => $valore) $arrayTemp[$key]=(null===$valore[$i])?'':$valore[$i];
            $insert->execute($arrayTemp);
        endfor;
    else:
        $insert->execute($arrayKey);
    endif;
    $ritorno=$pdo->lastInsertId();
    if ($boolConnetti){
        $pdo = null;
    }
    return $ritorno;

}
/**
 * @param  PDO   $pdo
 * @param string  $strTabella
 * @param array  $arrayKey
 * @param mixed  $idValue
 * @param string $strIdColumn
 * @return null|string|int
 */
function updateKeyArrayPdo($pdo,$strTabella,$arrayKey,$idValue,$strIdColumn='ID'){
    $boolConnetti = (!$pdo);

    if ($boolConnetti){
        $pdo = connettiPdo();
    }
    if (!$strIdColumn or !$idValue  or !$strTabella){
        bloccaPdo($pdo,'Errore grave. L\'ID della riga, la colonna di riferimento o la tabella non sono arrivati.');
        return 'Errore grave. L\'ID della riga, la colonna di riferimento o la tabella non sono arrivati.';
    }
    $arrayCampi=array();
    foreach($arrayKey as $chiave => $valore) {
        if (null===$valore) {
            unset($arrayKey[$chiave]);
            continue;
        }
        $arrayCampi[]=$chiave.'=:'.$chiave;
    }
    if (!$arrayCampi) return;
    $update=$pdo->prepare('UPDATE '.$strTabella.' SET '.implode(',',$arrayCampi).' WHERE '.$strIdColumn.'=:ID__COLUMN');
    $arrayKey['ID__COLUMN']=$idValue;
    $update->execute($arrayKey);
    $nrecord = $update->rowCount();
    if ($boolConnetti){
        $pdo = null;
    }
    return $nrecord;

}
/******************************************************************/

/**
 * @param     $pdo        PDO
 * @param     $queryScava string
 * @param     $tabella    string
 * @param     $array      array
 * @param int $intInner
 */
function scavaDerivati($pdo,$queryScava,$tabella,&$array,$intInner=0){
    $tabella=strtoupper($tabella);
    switch ($tabella){
        case 'ESERCIZI':
            $campi='ID_ESERCIZIO';
            break;
        default: $campi='ID_'.$tabella;
    }
    if (!$array) $array[$tabella]=array($campi,'---',$intInner);
    ++$intInner;
    $blob=queryPreparedPdo($pdo,$queryScava,array(CLICK_DATABASE,$campi),'p');
    while ($derivati=$blob->fetchColumn()){
        scavaDerivati($pdo,$queryScava,$derivati,$array,$intInner);
        $temp=array($campi,$tabella,$intInner);
        if (!isset($array[$derivati]) or $array[$derivati][2] < $temp[2]) $array[$derivati]=$temp;
    }
    $blob->closeCursor();
}
/**
 * @param $pdo PDO
 * @param $strTabella string
 * @param $strWhereOrID string|int
 * @param $strJoin
 * @param bool $boolDebug
 * @return int
 */
function delLegacyUltimate($pdo,$strTabella,$strWhereOrID=null,$strJoin=null,$boolDebug=false){
    if (!$strTabella) return 0;
    $contaEliminati=($boolDebug)?'':0;
    $queryScava='select distinct Colonne.TABLE_NAME from INFORMATION_SCHEMA.COLUMNS Colonne
                            INNER JOIN INFORMATION_SCHEMA.TABLES Tabelle ON (Colonne.TABLE_NAME = Tabelle.TABLE_NAME AND Colonne.TABLE_SCHEMA=Tabelle.TABLE_SCHEMA)
                            where  Tabelle.TABLE_SCHEMA=? AND Tabelle.TABLE_TYPE = "BASE TABLE" AND Colonne.COLUMN_NAME = ? order by Colonne.COLUMN_NAME';
    $array=array();
    scavaDerivati($pdo,$queryScava,$strTabella,$array);
    $livelli=array();

    foreach ($array as $chiave => $valore){
        $livelli[$chiave]=$valore[2];
    }
    $arrayFull=array();
    foreach ($livelli as $chiave => $valore){
        $arrayDati=array($chiave);
        $i=$valore;
        $tab=$chiave;
        while ($i > 0){
            --$i;
            $tab=$array[$tab][1];
            $arrayDati[]=$tab;
        }
        $arrayDati=array_reverse($arrayDati);
        $arrayFull[$chiave]=$arrayDati;

    }
    array_multisort(array_map('count',$arrayFull),SORT_DESC,$arrayFull);
    foreach ($arrayFull as $tabella => $joins){
        unset ($joins[0]);
        foreach ($joins as $id => $tabJoin){
            $joins[$id]=' INNER JOIN '.$tabJoin.' ON '.$tabJoin.'.'.$array[$tabJoin][0].' = '.$array[$tabJoin][1].'.ID';
        }
        if (!count($joins) and !$strJoin){
            $strDelete='DELETE FROM '.$strTabella;
        }else{
            $strDelete='DELETE '.$tabella.'.* FROM '.$strTabella.' '.$strJoin.' ';
            $strDelete.=implode(' ',$joins);
        }
        $arrayDelete=null;
        if (is_numeric($strWhereOrID)){
            $strDelete.=' WHERE '.$strTabella.'.ID=?';
            $arrayDelete=array($strWhereOrID);
            $prepareElimina=$pdo->prepare($strDelete);
            $prepareElimina->execute($arrayDelete);
            ($boolDebug)?$contaEliminati.='DELETE '.$tabella.'.* eliminati '.$prepareElimina->rowCount().'<br/>'.PHP_EOL:$contaEliminati+=$prepareElimina->rowCount();
        }else {
            if ($strWhereOrID) $strDelete.=' WHERE '.$strWhereOrID;
            ($boolDebug)?$contaEliminati.='DELETE '.$strTabella.'.* eliminati '.$pdo->exec($strDelete).'<br/>'.PHP_EOL:$contaEliminati+=$pdo->exec($strDelete);
        }
    }
    /********* Cancella ritenute orfane di fattura ****************/
    $prepareControllaFatture=$pdo->query('SELECT COUNT(*) FROM FATTUREF Ritenuta LEFT JOIN FATTUREF Fattura ON Ritenuta.ID_FATTURAREFERENTE = Fattura.ID WHERE Fattura.ID IS NULL AND Ritenuta.ID_FATTURAREFERENTE <>0 AND Ritenuta.BOOLRITENUTA=1');
    $contaRitenuteOrfane=$prepareControllaFatture->fetchColumn();
    $prepareControllaFatture->closeCursor();
    if ($contaRitenuteOrfane) $contaEliminati+=delLegacyUltimate($pdo,'FATTUREF','FATTUREF.BOOLRITENUTA=1 AND FATTURA.ID IS NULL AND FATTUREF.ID_FATTURAREFERENTE <> 0','LEFT JOIN FATTUREF FATTURA ON FATTURA.ID = FATTUREF.ID_FATTURAREFERENTE');
    return $contaEliminati;
}
/**
 * Funzione per l'eliminazione degli indici riferenti ad un elemento rimosso.
 * @param PDO $pdo
 * @param string $strTabella
 * @param int $intId
 * @param resource $log
 */
function cutRiferimentiDettagli($pdo,$strTabella,$intId,$log=null){
    //$boolLogOpened=($log);
    //if (!$boolLogOpened)  $log=openLogErr('clear_keys');
    if (!(isIstance($pdo)) or !$pdo->inTransaction()) {
        bloccaPdo($pdo,'Non si puo\' procedere all\'eliminazione ereditaria senza essere in una transazione!');
        return;
    }
    if (!$strTabella or !$intId){
        bloccaPdo($pdo,'La tabella e l\'ID sono OBBLIGATORI!');
    }
    switch ($strTabella):
        case 'UNITAIMMOBILIARI':
            $arrayChiavi=array('ID_UIPROPRIETA','ID_UIRIFERIMENTO');
            break;
        case 'CONDOMINI':
            $arrayChiavi=array('ID_CONDOMINOPAGANTE');
            break;
        case 'FORNITORI':
            $arrayChiavi=array('ID_FORNITORIRIT','ID_AMMFORNITORE');
            break;
        default:
            $arrayChiavi=array('ID_'.$strTabella);
            break;
    endswitch;
    $queryScava=$pdo->prepare('select distinct Colonne.TABLE_NAME from INFORMATION_SCHEMA.COLUMNS Colonne
                                INNER JOIN INFORMATION_SCHEMA.TABLES Tabelle ON (Colonne.TABLE_NAME = Tabelle.TABLE_NAME AND Colonne.TABLE_SCHEMA=Tabelle.TABLE_SCHEMA)
                                where  Tabelle.TABLE_SCHEMA=? AND Tabelle.TABLE_TYPE = "BASE TABLE" AND Colonne.COLUMN_NAME = ? order by Colonne.COLUMN_NAME');
    foreach($arrayChiavi as $chiave):
        $queryScava->execute(array(CLICK_DATABASE,$chiave));
        while ($tabellaDaLeggere=$queryScava->fetchColumn()) {
            // logErr("Setto a 0 i valori {$chiave} ove valevano {$intId} nella tabella {$tabellaDaLeggere}",$log);
            $intRighe=$pdo->exec("UPDATE {$tabellaDaLeggere} SET {$chiave}=0 WHERE {$chiave}={$intId}");
            switch($intRighe){
                case 0:$strNumero='Nessuna riga aggiornata da '.$tabellaDaLeggere;
                    break;
                case 1:$strNumero='Aggiornata una riga da '.$tabellaDaLeggere;
                    break;
                default:$strNumero='Aggiornate '.$intRighe.' da '.$tabellaDaLeggere;
            }
            logErr($strNumero,$log);
        }
        $queryScava->closeCursor();
    endforeach;
    // if (!$boolLogOpened) closeLogErr($log);
}

/**
 * @param PDO $pdo
 * @param $tabella
 * @param $arrayElementi
 */
function ciclaGuardiani($pdo,$tabella,$arrayElementi){
    $prepareGuardiani = $pdo->prepare('SELECT DERIVATA FROM GUARDIATABELLE WHERE TABELLA =? AND BOOLGUARDIA = 1 ');
    $blobGuardiani = queryPreparedPdo($pdo,$prepareGuardiani,$tabella);
    while ($derivata = $blobGuardiani->fetchColumn()){
        ciclaQueryGuardiani($pdo,$tabella,$derivata,$arrayElementi);
    }
    $blobGuardiani->closeCursor();
}
function correggiJoinPerEliminazione($query,$join){
    return str_replace('<clickJoin>',$join,$query);
}
function impostaCondizioneWherePerElimina($query,$tabella,$where,&$arrayWhere){

    $arrayWhere = null;
    if  (is_numeric($where)) {
        //where numerico effettuato su ID
        $query.='.ID=?';
        $arrayWhere =array($where);
    }elseif (!$where){
        //where vuoto, esegue su tutto
        $query=str_replace('WHERE '.$tabella,null,$query);
    }else{
        //where generico
        $associazioni=explode('=',$where);
        if (false!==strpos($associazioni[0],'.')){
            $query = str_replace('WHERE '.$tabella,'WHERE '.$where,$query);
        }else{
            $query = $query.'.'.$where;
        }

    }
    return $query;
}

/**
 * @param PDO $pdo
 * @param $tabella
 * @param $derivata
 * @param $arrayElementi
 */
function ciclaQueryGuardiani($pdo,$tabella,$derivata,$arrayElementi){
    $prepareGuardiani = $pdo->prepare('SELECT ISTRUZIONE FROM CONTADERIVATE WHERE TABELLA =? AND DERIVATA = ? ');
    $blobGuardiani = queryPreparedPdo($pdo,$prepareGuardiani,array($tabella,$derivata));
    $arrayWhere = null;
    while ($queryGuardiana = $blobGuardiani->fetchColumn()){

        $queryGuardiana = correggiJoinPerEliminazione($queryGuardiana,$arrayElementi['Join']);
        $queryGuardiana = impostaCondizioneWherePerElimina($queryGuardiana,$arrayElementi['Tabella'],$arrayElementi['Where'],$arrayWhere);
        $contaBloccati = queryPreparedPdo($pdo,$queryGuardiana,$arrayWhere,'v');
        if (!$contaBloccati){
            continue;
        }
        bloccaPdo($pdo,'Nella tabella '.$derivata.' ci sono '.$contaBloccati.' elementi collegati. Eliminazione annullata.');
    }
    $blobGuardiani->closeCursor();

}

/**
 * @param  PDO $pdo
 * @param $arrayMarchiati
 * @param $elementiArray
 */
function marchiaEredi($pdo,&$arrayMarchiati,$elementiArray){
    ciclaGuardiani($pdo,$elementiArray['Tabella'],$elementiArray);
    ciclaEliminaTabelle($pdo,$elementiArray,$arrayMarchiati);
}

/**
 * @param $pdo PDO
 * @param $arrayMarchiati
 * @param $query
 * @param $array
 */
function marchiaEliminabili($pdo,&$arrayMarchiati,$query,$array,$derivata){
    $marchia = $pdo->prepare($query);
    $marchia->execute($array);
    $contaMarcati = $marchia->rowCount();
    if (!$contaMarcati){
        return;
    }
    if (!in_array($derivata,$arrayMarchiati)){
        $arrayMarchiati[] = $derivata;
    }
}
function marchiaIndirizziCondomino($pdo,&$arrayMarchiati){
    $query = 'UPDATE RIFERIMENTICONDOMINO RC INNER JOIN CONDOMINI C ON C.ID = RC.ID_CONDOMINO SET RC.DBDELETE = 1
             WHERE C.DBDELETE = 1';
    marchiaEliminabili($pdo,$arrayMarchiati,$query,null,'RIFERIMENTICONDOMINO');
}
/**
 * @param PDO $pdo
 * @param $elementiArray
 * @param $arrayMarchiati
 */
function ciclaEliminaTabelle($pdo,$elementiArray,&$arrayMarchiati){
    $arrayWhere = null;
    $prepareCiclaEliminaTabelle = $pdo->prepare('SELECT DERIVATA,ISTRUZIONE FROM ELIMINATABELLE WHERE TABELLA=?');
    $blobEliminaTabelle  = queryPreparedPdo($pdo,$prepareCiclaEliminaTabelle,$elementiArray['Tabella']);
    while ($rigaElimina = $blobEliminaTabelle->fetch(PDO::FETCH_ASSOC)){
        $query = $rigaElimina['ISTRUZIONE'];
        $query = correggiJoinPerEliminazione($query,$elementiArray['Join']);
        $query = impostaCondizioneWherePerElimina($query,$elementiArray['Tabella'],$elementiArray['Where'],$arrayWhere);
        $derivata = $rigaElimina['DERIVATA'];
        if (!$derivata){
            $derivata = $elementiArray['Tabella'];
        }
        marchiaEliminabili($pdo,$arrayMarchiati,$query,$arrayWhere,$derivata);
    }
    $blobEliminaTabelle->closeCursor();
}

/**
 * @param PDO        $pdo
 * @param string     $strTabella
 * @param string|int $where
 * @param null       $strJoin
 * @param bool       $boolConsideraIgnora
 * @return int|string
 */
function delLegacy($pdo,$strTabella,$where=null,$strJoin=null,$boolConsideraIgnora=false){
    if (strtoupper($strTabella)=='ELIMINATABELLE') return false;
    if ((!isIstance($pdo)) and !$pdo->inTransaction())return false;
    $boolConnetti=(!isIstance($pdo));
    if ($boolConnetti){
        $pdo=connettiPdo();
    }
    $strTabella = strtoupper($strTabella);
    if (!$strTabella or 'ELIMINATABELLE' == $strTabella){
        return false;
    }

    $prepareCercaFatturaMacchiata = $pdo->prepare('
            SELECT COUNT(*) FROM FATTUREF Fattura INNER JOIN FATTUREF Ritenuta ON Fattura.ID_FATTURAREFERENTE = Ritenuta.ID
            AND Ritenuta.BOOLRITENUTA = 1 AND Fattura.DBDELETE = 1 AND Ritenuta.DBDELETE != 1
        ');
    $arrayMarchiati = array($strTabella);
    $arrayElementi = array('Tabella' => $strTabella, 'Where' => $where, 'Join' => $strJoin, 'BoolConsideraIgnora' => $boolConsideraIgnora);
    marchiaEredi($pdo,$arrayMarchiati,$arrayElementi);
    $wherePerRitenute= 'FATTUREF.BOOLRITENUTA=1 AND FATTURA.DBDELETE = 1';
    $joinPerRitenute = 'INNER JOIN FATTUREF FATTURA ON FATTURA.ID = FATTUREF.ID_FATTURAREFERENTE';
    if (queryPreparedPdo($pdo,$prepareCercaFatturaMacchiata,null,'v')){
        $arrayElementi = array('Tabella' => 'FATTUREF', 'Where' => $wherePerRitenute, 'Join' => $joinPerRitenute);
        marchiaEredi($pdo,$arrayMarchiati,$arrayElementi);
    }
    marchiaIndirizziCondomino($pdo,$arrayMarchiati);
    return rimuoviMarchiati($pdo,$arrayMarchiati);
}

/**
 * @param $pdo PDO
 * @param $arrayMarchiati
 *
 * @return int
 */
function rimuoviMarchiati($pdo,$arrayMarchiati) {
    $contatore = 0;
    foreach ($arrayMarchiati as $tabellaSvuota) {
        if (!$tabellaSvuota) {
            continue;
        }
        $strDelete = 'DELETE FROM ' . $tabellaSvuota . ' WHERE DBDELETE = 1';
        $contatore += $pdo->exec($strDelete);
    }
    return $contatore;
}
/**
 * @param     $pdo        PDO
 * @param     $queryScava string
 * @param     $database
 * @param     $tabella    string
 * @param     $array      array
 * @param int $intInner
 */
function scavaDerivatiDel($pdo,$queryScava,$database,$tabella,&$array,$intInner=0){
    $tabella=strtoupper($tabella);
    switch ($tabella){
        case 'ESERCIZI':
            $campi='ID_ESERCIZIO';
            break;
        default: $campi='ID_'.$tabella;
    }
    if (!$array) $array[$tabella]=array($campi,'---',$intInner);
    ++$intInner;
    $blob=queryPreparedPdo($pdo,$queryScava,array($database,$campi),'p');
    while ($derivati=$blob->fetchColumn()){
        scavaDerivatiDel($pdo,$queryScava,$database,$derivati,$array,$intInner);
        $temp=array($campi,$tabella,$intInner);
        if (!isset($array[$derivati]) or $array[$derivati][2] < $temp[2]) $array[$derivati]=$temp;
    }
    $blob->closeCursor();
}

/**
 * @param PDO $pdo
 * @param $strSchema
 *
 * @return int
 *
 */
function preparaTabelleRicursive($pdo,$strSchema=null){
    if (!$strSchema) $strSchema=CLICK_DATABASE;
    $pdo->exec('TRUNCATE '.$strSchema.'.ELIMINATABELLE');
    $prepareScrivi=$pdo->prepare('INSERT INTO '.$strSchema.'.ELIMINATABELLE (TABELLA,ISTRUZIONE,ORDINE) VALUES(?,?,?)');

    $prepareLeggiTabelle=$pdo->prepare('select T.TABLE_NAME from information_schema.TABLES T where T.TABLE_SCHEMA=? and T.TABLE_TYPE = "BASE TABLE" AND T.TABLE_NAME != "ELIMINATABELLE"');
    $blobTabelle=queryPreparedPdo($pdo,$prepareLeggiTabelle,array($strSchema),'p');
    while ($tabella= $blobTabelle->fetchColumn()){
        $queryScava='select distinct Colonne.TABLE_NAME from INFORMATION_SCHEMA.COLUMNS Colonne
                            INNER JOIN INFORMATION_SCHEMA.TABLES Tabelle ON (Colonne.TABLE_NAME = Tabelle.TABLE_NAME AND Colonne.TABLE_SCHEMA=Tabelle.TABLE_SCHEMA)
                            where  Tabelle.TABLE_SCHEMA=? AND Tabelle.TABLE_TYPE = "BASE TABLE" AND Colonne.COLUMN_NAME = ? order by Colonne.COLUMN_NAME';
        $array=array();
        scavaDerivatiDel($pdo,$queryScava,$strSchema,$tabella,$array);
        $livelli=array();
        $operazione=0;
        foreach ($array as $chiave => $valore){
            $livelli[$chiave]=$valore[2];
        }
        $arrayFull=array();
        foreach ($livelli as $chiave => $valore){
            $arrayDati=array($chiave);
            $i=$valore;
            $tab=$chiave;
            while ($i > 0){
                --$i;
                $tab=$array[$tab][1];
                $arrayDati[]=$tab;
            }
            $arrayDati=array_reverse($arrayDati);
            $arrayFull[$chiave]=$arrayDati;

        }
        array_multisort(array_map('count',$arrayFull),SORT_DESC,$arrayFull);
        foreach ($arrayFull as $tabellaDerivata => $joins){
            ++$operazione;
            unset ($joins[0]);
            foreach ($joins as $id => $tabJoin){
                $joins[$id]=' INNER JOIN '.$tabJoin.' ON '.$tabJoin.'.'.$array[$tabJoin][0].' = '.$array[$tabJoin][1].'.ID';
            }
            if (!count($joins)){
                $strDelete='DELETE FROM '.$tabella.' <clickJoin> WHERE '.$tabella;

            }else{
                $strDelete='DELETE '.$tabellaDerivata.'.* FROM '.$tabella.' <clickJoin>';
                $strDelete.=implode(' ',$joins).' WHERE '.$tabella;
            }
            $prepareScrivi->execute(array($tabella,$strDelete,$operazione));

        }

    }
    $blobTabelle->closeCursor();
}
