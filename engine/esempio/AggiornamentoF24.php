<?php
/**
 * Created by Drakkar vers. 0.1.2(Hjortspring)
 * User: P.D.A. Srl
 * Date: 2018-05-30
 * Time: 18:53:51.942191
 */

namespace Click\Affitti\TblBase;
require_once 'AggiornamentoF24Model.php';

use Click\Affitti\TblBase\AggiornamentoF24Model;

class  AggiornamentoF24 extends AggiornamentoF24Model
{
    function __construct($pdo)
    {
        parent::__construct($pdo);
    }


    public function getArrayCodiceFlusso($codiceFlusso, $typeResult = self::FETCH_KEYARRAY)
    {
        $query = "SELECT DISTINCT codice_flusso FROM $this->tableName WHERE codice_flusso LIKE ? ";
        return $this->createResultArray($query, array($codiceFlusso), $typeResult);
    }


    public function getElencoOperazioni($limit = -1, $offset = -1)
    {
        $query = "
            SELECT
                DISTINCT codice_flusso
            FROM $this->tableName 
            ORDER BY codice_flusso desc 
            ";
        $query .= $this->createLimitQuery($limit, $offset);
        return $this->createResultArray($query, null,self::FETCH_KEYARRAY);
    }


    public function findIdContrattoByIdAggiornamento($id)
    {
        $query =
            "
            SELECT 
                DISTINCT contratti.id
            FROM
                aggiornamento_f24
                    INNER JOIN
                imposta_registro ON aggiornamento_f24.id_imposta_registro = imposta_registro.id
                    INNER JOIN
                rli ON imposta_registro.id_rli = rli.id
                    INNER JOIN
                contratti ON rli.id_contratto = contratti.id
            WHERE
                aggiornamento_f24.id = ?            
            ";
        return $this->createResultValue($query, array($id));
    }


}