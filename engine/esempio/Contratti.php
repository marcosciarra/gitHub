<?php
/**
 * Created by Drakkar vers. 0.0.22(Hjortspring)
 * User: P.D.A. Srl
 * Date: 2017-11-27
 * Time: 18:25:51.915629
 */

namespace Click\Affitti\TblBase;
require_once 'ContrattiModel.php';

use Click\Affitti\TblBase\ContrattiModel;
use Drakkar\DrakkarDbConnector;

class  Contratti extends ContrattiModel
{
    /**
     * Contratti constructor.
     * @param \Drakkar\DrakkarDbConnector|null $pdo
     * @param string $statoCestino A = Attivo(default) <br> C = Cestiono <br> T = Tutti
     *
     */
    function __construct($pdo, $statoCestino = 'A')
    {

        switch ($statoCestino) {
            case 'A':
                $this->whereBase = " contratti.cestino=0 ";
                break;
            case 'C':
                $this->whereBase = " contratti.cestino=1 ";
                break;
            default:
                $this->whereBase = "";
        }
        parent::__construct($pdo);
    }


    public function getElencoLocatori($typeResult = self::FETCH_OBJ)
    {
        $query =
            "
            SELECT distinct proprietari->'$[*].id' as id FROM contratti
            ";
        return $this->createResultArray($query, null, $typeResult);
    }

    public function getElencoConduttori($typeResult = self::FETCH_OBJ)
    {
        $query =
            "
            SELECT distinct conduttori->'$[*].id' as id FROM contratti
            ";
        return $this->createResultArray($query, null, $typeResult);
    }

    /**
     * Metodo che restituisce l'elenco dei contratti senza codice identificativo
     * @param int $typeResult
     * @return Contratti[]|array|string
     */
    public function getContrattiSenzaRegistrazione($typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT contratti.* from contratti 
                    inner join rli on contratti.id=rli.id_contratto
                    where (rli.codice_identificativo IS NULL OR LENGTH(rli.codice_identificativo)<16)
                    and contratti.elaborato=1
                    and contratti.cestino=0
                    group by contratti.id";
        return $this->createResultArray($query, null, $typeResult);
    }


    /*
     * Metodo che restituisce l'elenco dei dei contratti con le relative UI collegate
     */
    public function getElencoContratti($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $distinctStr = ($distinct) ? 'DISTINCT' : '';
        $query = "  SELECT 
                        contratti.id,
                        contratti.id_tipo_contratto, 
                        DATE_FORMAT(contratti.data_inizio,'%d/%m/%Y') AS data_inizio_formattata,
                        contratti.data_inizio,
                        contratti.elaborato,
                        contratti.cestino,
                        contratti.proprietari as proprietari,
                        contratti.conduttori as conduttori,
                        contratti.garanti as garanti,
                        contratti.stato_contratto as stato_contratto,
                        contratti.id_utente_riferimento,
                        sum(canoni_oneri.importo) as importo,
                        rli.codice_identificativo
                    FROM contratti
                    LEFT JOIN canoni_oneri ON 
                        contratti.id=canoni_oneri.id_contratto
                    LEFT JOIN rli ON 
                        contratti.id=rli.id_contratto
        ";

        if ($this->whereBase) $query .= " WHERE $this->whereBase";
        $query .= " group by contratti.id";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        $query .= $this->createLimitQuery($limit, $offset);
        return $this->createResultArray($query, null, $typeResult);
    }

    /*
     * Metodo che restituisce l'elenco dei dei contratti con tabella RLI collegata
     */
    public function elencoRinnoviProroghe($dataInizio, $dataFine, $typeResult = self::FETCH_OBJ)
    {
        $query = "
            SELECT 
                contratti.id,
                contratti.descrizione,
                contratti.proprietari,
                imposta_registro.data_scadenza
            FROM
                contratti
                    INNER JOIN
                rli ON contratti.id = rli.id_contratto
                    INNER JOIN
                imposta_registro ON rli.id = imposta_registro.id_rli
            WHERE
                contratti.cestino = 0
                    AND contratti.stato_contratto = 'A'
                    AND contratti.elaborato = 1
                    AND imposta_registro.data_scadenza BETWEEN '$dataInizio' AND '$dataFine'
        ";
        return $this->createResultArray($query, null, $typeResult);
    }


    /*
     * Metodo che restituisce il canone massimo e minimo
     */
    public function getCanoneMaxMin($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $distinctStr = ($distinct) ? 'DISTINCT' : '';
        $query = "SELECT MIN(canoni_oneri.importo) as minCanone, MAX(canoni_oneri.importo) as maxCanone
            from contratti
            LEFT JOIN canoni_oneri on contratti.id=canoni_oneri.id_contratto ";
        if ($this->whereBase) $query .= " WHERE $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        $query .= $this->createLimitQuery($limit, $offset);
        return $this->createResultArray($query, null, $typeResult);
    }

    /**
     *
     * @param bool $distinct
     * @param int $typeResult
     * @param int $limit
     * @param int $offset
     * @return array|string
     */
    public function getElencoContrattiCruscotto($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $distinctStr = ($distinct) ? 'DISTINCT' : '';
        $query = "
            select 
                contratti.id,
                contratti.id_tipo_contratto,
                contratti.data_inizio,
                contratti.proprietari as proprietari,
                contratti.conduttori as conduttori,
                sum(canoni_oneri.importo) as importo
            from contratti
            LEFT JOIN canoni_oneri on contratti.id=canoni_oneri.id_contratto
            WHERE contratti.cestino=0 AND contratti.elaborato=1
            group by contratti.id
            ";

        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        $query .= $this->createLimitQuery($limit, $offset);
        return $this->createResultArray($query, null, $typeResult);
    }

    public function findByContaContratti($elaborato, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE elaborato=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResult($query, array($elaborato), $typeResult);
    }


    /**
     * @param int $typeResult
     * @return array|string
     *
     * Elenco dei contratti disdettabili
     */
    public function getElencoContrattiDisdettabili($stato, $escludiPreavviso, $dataInizio, $dataFine, $typeResult = self::FETCH_OBJ)
    {
        $query = "
            SELECT 
                contratti.id,
                contratti.id_tipo_contratto,
                contratti.descrizione,
                contratti.id_utente_riferimento,
                contratti.mesi_preavviso_locatore,
                contratti.proprietari,
                contratti.conduttori,
                tipo_contratto.secondo_rinnovo,
                    MAX(periodi_contrattuali.data_fine) AS data_fine
                <variable>
            FROM
                contratti
                    INNER JOIN
                periodi_contrattuali ON contratti.id = periodi_contrattuali.id_contratto
                    INNER JOIN
                tipo_contratto ON contratti.id_tipo_contratto = tipo_contratto.id
            WHERE
                contratti.cestino = 0
                    AND contratti.elaborato = 1
                    AND contratti.stato_contratto = ?
            GROUP BY contratti.id
            HAVING data_preavviso BETWEEN ? AND ?";
        if (!$escludiPreavviso) {
            $query = str_replace("<variable>", ", DATE_SUB(MAX(periodi_contrattuali.data_fine),
                        INTERVAL contratti.mesi_preavviso_locatore MONTH) AS data_preavviso", $query);
        } else {
            $query = str_replace("<variable>", ", MAX(periodi_contrattuali.data_fine) AS data_preavviso", $query);
        }
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($stato, $dataInizio, $dataFine), $typeResult);
    }

    public function getScadenzaContratto()
    {
        $query = "
            SELECT 
                MAX(periodi_contrattuali.data_fine) AS data_fine
            FROM
                contratti
                    INNER JOIN
                periodi_contrattuali ON contratti.id = periodi_contrattuali.id_contratto
            WHERE contratti.id = ?";

        return $this->createResultValue($query, array($this->getId()));
    }


    public function getNumeroContrattiPerGruppoFatturazione($idGruppoFatturazione)
    {
        $query =
            "
            SELECT 
                COUNT(contratti.id) AS totale
            FROM
                contratti
                    INNER JOIN
                contratti_dettagli ON contratti.id = contratti_dettagli.id
            WHERE
                contratti_dettagli.id_gruppo_fatturazione = ?
                    AND contratti.cestino = 0
            ";
        return $this->createResultValue($query, array($idGruppoFatturazione));
    }


    /**
     * @param DrakkarDbConnector $con
     * @param $id
     * @param int $cestino
     */
    public static function cestinaContratto($idUtente, $con, $id, $cestino = 1)
    {
        $query = 'update contratti set cestino=? where id=?';
        $con->execQuery($query, array($cestino, $id));
        new \Drakkar\Log\DrakkarTraceLog($idUtente);
    }


    public function getProprietari($indice = null, $key = null)
    {
        if (is_null($indice)) {
            return parent::getProprietari();
        }

        $app = json_decode(parent::getProprietari());

        if (is_null($key)) {
            return $app[$indice];
        }

        return isset($app[$indice]->$key) ? $app[$indice]->$key : null;
    }

    public function getConduttori($indice = null, $key = null)
    {
        if (is_null($indice)) {
            return parent::getConduttori();
        }

        $app = json_decode(parent::getConduttori());

        if (is_null($key)) {
            return $app[$indice];
        }

        return isset($app[$indice]->$key) ? $app[$indice]->$key : null;
    }

}