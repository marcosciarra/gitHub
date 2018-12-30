<?php
/**
 * Created by Drakkar vers. 0.1.3(Hjortspring)
 * User: P.D.A. Srl
 * Date: 2018-11-15
 * Time: 11:42:47.230103
 */

namespace Click\Affitti\TblBase;
require_once 'PdaAbstractModel.php';

use Click\Affitti\TblBase\PdaAbstractModel;

/**
 * @property string nomeTabella
 * @property string tableName
 */
class AggiornamentoF24Model extends PdaAbstractModel
{
    /** @var integer */
    protected $id;
    /** @var integer */
    protected $idImpostaRegistro;
    /** @var objext (string) */
    protected $contribuente;
    /** @var string */
    protected $cfCoobbligato;
    /** @var string */
    protected $codiceIdentificativo;
    /** @var string */
    protected $codiceUfficio;
    /** @var string */
    protected $codiceAtto;
    /** @var objext (string) */
    protected $dettagli;
    /** @var objext (string) */
    protected $contoCorrente;
    /** @var string */
    protected $dataVersamento;
    /** @var string */
    protected $codiceFlusso;

    function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->nomeTabella = 'aggiornamento_f24';
        $this->tableName = 'aggiornamento_f24';
    }

    /**
     * find by tables' Primary Key:
     * @return AggiornamentoF24|array|string|null
     */
    public function findByPk($id, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(PRIMARY) WHERE id=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResult($query, array($id), $typeResult);
    }

    /**
     * delete by tables' Primary Key:
     */
    public function deleteByPk($id)
    {
        $query = "DELETE FROM $this->tableName  WHERE id=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($id));
    }

    /**
     * Find all record of table
     * @return AggiornamentoF24[]|array|string
     */
    public function findAll($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $distinctStr = ($distinct) ? 'DISTINCT' : '';
        $query = "SELECT $distinctStr * FROM $this->tableName ";
        if ($this->whereBase) $query .= " WHERE $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        $query .= $this->createLimitQuery($limit, $offset);
        return $this->createResultArray($query, null, $typeResult);
    }

    /**
     * find by tables' Key idx_id_imposta_registro:
     * @return AggiornamentoF24[]|array|string
     */
    public function findByIdxIdImpostaRegistro($idImpostaRegistro, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(idx_id_imposta_registro) WHERE id_imposta_registro=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultArray($query, array($idImpostaRegistro), $typeResult);
    }

    /**
     * find by tables' Key idx_codice_flusso:
     * @return AggiornamentoF24[]|array|string
     */
    public function findByIdxCodiceFlusso($codiceFlusso, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(idx_codice_flusso) WHERE codice_flusso=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultArray($query, array($codiceFlusso), $typeResult);
    }

    /**
     * find by tables' Key idx_codice_flusso_id_imposta_registro:
     * @return AggiornamentoF24[]|array|string
     */
    public function findByIdxCodiceFlussoIdImpostaRegistro($codiceFlusso, $idImpostaRegistro, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(idx_codice_flusso_id_imposta_registro) WHERE codice_flusso=? AND id_imposta_registro=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultArray($query, array($codiceFlusso, $idImpostaRegistro), $typeResult);
    }

    /**
     * delete by tables' Key idx_id_imposta_registro:
     * @return boolean
     */
    public function deleteByIdxIdImpostaRegistro($idImpostaRegistro, $typeResult = self::FETCH_OBJ)
    {
        $query = "DELETE FROM $this->tableName WHERE id_imposta_registro=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($idImpostaRegistro));
    }

    /**
     * delete by tables' Key idx_codice_flusso:
     * @return boolean
     */
    public function deleteByIdxCodiceFlusso($codiceFlusso, $typeResult = self::FETCH_OBJ)
    {
        $query = "DELETE FROM $this->tableName WHERE codice_flusso=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($codiceFlusso));
    }

    /**
     * delete by tables' Key idx_codice_flusso_id_imposta_registro:
     * @return boolean
     */
    public function deleteByIdxCodiceFlussoIdImpostaRegistro($codiceFlusso, $idImpostaRegistro, $typeResult = self::FETCH_OBJ)
    {
        $query = "DELETE FROM $this->tableName WHERE codice_flusso=? AND id_imposta_registro=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($codiceFlusso, $idImpostaRegistro));
    }

    /**
     * find by id
     * @return AggiornamentoF24[]
     */
    public function findById($id, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE id=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($id), $typeResult);
    }


    /**
     * find by id_imposta_registro
     * @return AggiornamentoF24[]
     */
    public function findByIdImpostaRegistro($idImpostaRegistro, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE id_imposta_registro=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($idImpostaRegistro), $typeResult);
    }


    /**
     * find by codice_flusso
     * @return AggiornamentoF24[]
     */
    public function findByCodiceFlusso($codiceFlusso, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE codice_flusso=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($codiceFlusso), $typeResult);
    }


    /**
     * find like codice_flusso
     * @return AggiornamentoF24[]
     */
    public function findLikeCodiceFlusso($codiceFlusso, $likeMatching = self::LIKE_MATCHING_BOTH, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE codice_flusso like ?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultArray($query, array($this->prepareLikeMatching($codiceFlusso, $likeMatching)), $typeResult);
    }

    /**
     * delete by id_imposta_registro
     * @return boolean
     */
    public function deleteByIdImpostaRegistro($idImpostaRegistro)
    {
        $query = "DELETE FROM $this->tableName WHERE id_imposta_registro=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($idImpostaRegistro));
    }

    /**
     * delete by codice_flusso
     * @return boolean
     */
    public function deleteByCodiceFlusso($codiceFlusso)
    {
        $query = "DELETE FROM $this->tableName WHERE codice_flusso=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($codiceFlusso));
    }

    /**
     * Transforms the object into a key array
     * @return array
     */
    public function toArrayAssoc()
    {
        $arrayValue = array();
        if (isset($this->id)) $arrayValue['id'] = $this->id;
        if (isset($this->idImpostaRegistro)) $arrayValue['id_imposta_registro'] = $this->idImpostaRegistro;
        if (isset($this->contribuente)) $arrayValue['contribuente'] = $this->jsonEncode($this->contribuente);
        if (isset($this->cfCoobbligato)) $arrayValue['cf_coobbligato'] = ($this->cfCoobbligato == self::NULL_VALUE) ? null : $this->cfCoobbligato;
        if (isset($this->codiceIdentificativo)) $arrayValue['codice_identificativo'] = ($this->codiceIdentificativo == self::NULL_VALUE) ? null : $this->codiceIdentificativo;
        if (isset($this->codiceUfficio)) $arrayValue['codice_ufficio'] = ($this->codiceUfficio == self::NULL_VALUE) ? null : $this->codiceUfficio;
        if (isset($this->codiceAtto)) $arrayValue['codice_atto'] = ($this->codiceAtto == self::NULL_VALUE) ? null : $this->codiceAtto;
        if (isset($this->dettagli)) $arrayValue['dettagli'] = $this->jsonEncode($this->dettagli);
        if (isset($this->contoCorrente)) $arrayValue['conto_corrente'] = $this->jsonEncode($this->contoCorrente);
        if (isset($this->dataVersamento)) $arrayValue['data_versamento'] = ($this->dataVersamento == self::NULL_VALUE) ? null : $this->dataVersamento;
        if (isset($this->codiceFlusso)) $arrayValue['codice_flusso'] = ($this->codiceFlusso == self::NULL_VALUE) ? null : $this->codiceFlusso;
        return $arrayValue;
    }

    /**
     * It transforms the keyarray in an $positionalArray[%s]object
     */
    public function createObjKeyArray(array $keyArray)
    {
        $this->flagObjectDataValorized = false;
        if ((isset($keyArray['id'])) || (isset($keyArray['aggiornamento_f24_id']))) {
            $this->setId(isset($keyArray['id']) ? $keyArray['id'] : $keyArray['aggiornamento_f24_id']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['id_imposta_registro'])) || (isset($keyArray['aggiornamento_f24_id_imposta_registro']))) {
            $this->setIdImpostaRegistro(isset($keyArray['id_imposta_registro']) ? $keyArray['id_imposta_registro'] : $keyArray['aggiornamento_f24_id_imposta_registro']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['contribuente'])) || (isset($keyArray['aggiornamento_f24_contribuente']))) {
            $this->setContribuente(isset($keyArray['contribuente']) ? $keyArray['contribuente'] : $keyArray['aggiornamento_f24_contribuente']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['cf_coobbligato'])) || (isset($keyArray['aggiornamento_f24_cf_coobbligato']))) {
            $this->setCfcoobbligato(isset($keyArray['cf_coobbligato']) ? $keyArray['cf_coobbligato'] : $keyArray['aggiornamento_f24_cf_coobbligato']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['codice_identificativo'])) || (isset($keyArray['aggiornamento_f24_codice_identificativo']))) {
            $this->setCodiceidentificativo(isset($keyArray['codice_identificativo']) ? $keyArray['codice_identificativo'] : $keyArray['aggiornamento_f24_codice_identificativo']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['codice_ufficio'])) || (isset($keyArray['aggiornamento_f24_codice_ufficio']))) {
            $this->setCodiceufficio(isset($keyArray['codice_ufficio']) ? $keyArray['codice_ufficio'] : $keyArray['aggiornamento_f24_codice_ufficio']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['codice_atto'])) || (isset($keyArray['aggiornamento_f24_codice_atto']))) {
            $this->setCodiceatto(isset($keyArray['codice_atto']) ? $keyArray['codice_atto'] : $keyArray['aggiornamento_f24_codice_atto']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['dettagli'])) || (isset($keyArray['aggiornamento_f24_dettagli']))) {
            $this->setDettagli(isset($keyArray['dettagli']) ? $keyArray['dettagli'] : $keyArray['aggiornamento_f24_dettagli']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['conto_corrente'])) || (isset($keyArray['aggiornamento_f24_conto_corrente']))) {
            $this->setContocorrente(isset($keyArray['conto_corrente']) ? $keyArray['conto_corrente'] : $keyArray['aggiornamento_f24_conto_corrente']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['data_versamento'])) || (isset($keyArray['aggiornamento_f24_data_versamento']))) {
            $this->setDataversamento(isset($keyArray['data_versamento']) ? $keyArray['data_versamento'] : $keyArray['aggiornamento_f24_data_versamento']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['codice_flusso'])) || (isset($keyArray['aggiornamento_f24_codice_flusso']))) {
            $this->setCodiceflusso(isset($keyArray['codice_flusso']) ? $keyArray['codice_flusso'] : $keyArray['aggiornamento_f24_codice_flusso']);
            $this->flagObjectDataValorized = true;
        }
    }

    /**
     * @return array
     */
    public function createKeyArrayFromPositional($positionalArray)
    {
        $values = array();
        $values['id'] = $positionalArray[0];
        $values['id_imposta_registro'] = $positionalArray[1];
        $values['contribuente'] = ($positionalArray[2] == self::NULL_VALUE) ? null : $positionalArray[2];
        $values['cf_coobbligato'] = ($positionalArray[3] == self::NULL_VALUE) ? null : $positionalArray[3];
        $values['codice_identificativo'] = ($positionalArray[4] == self::NULL_VALUE) ? null : $positionalArray[4];
        $values['codice_ufficio'] = ($positionalArray[5] == self::NULL_VALUE) ? null : $positionalArray[5];
        $values['codice_atto'] = ($positionalArray[6] == self::NULL_VALUE) ? null : $positionalArray[6];
        $values['dettagli'] = ($positionalArray[7] == self::NULL_VALUE) ? null : $positionalArray[7];
        $values['conto_corrente'] = ($positionalArray[8] == self::NULL_VALUE) ? null : $positionalArray[8];
        $values['data_versamento'] = ($positionalArray[9] == self::NULL_VALUE) ? null : $positionalArray[9];
        $values['codice_flusso'] = ($positionalArray[10] == self::NULL_VALUE) ? null : $positionalArray[10];
        return $values;
    }

    /**
     * @return array
     */
    public function getEmptyDbKeyArray()
    {
        $values = array();
        $values['id'] = null;
        $values['id_imposta_registro'] = null;
        $values['contribuente'] = null;
        $values['cf_coobbligato'] = null;
        $values['codice_identificativo'] = null;
        $values['codice_ufficio'] = null;
        $values['codice_atto'] = null;
        $values['dettagli'] = null;
        $values['conto_corrente'] = null;
        $values['data_versamento'] = null;
        $values['codice_flusso'] = null;
        return $values;
    }

    /**
     * Return columns' list
     * @return string
     */
    public function getListColumns()
    {
        return 'aggiornamento_f24.id as aggiornamento_f24_id,aggiornamento_f24.id_imposta_registro as aggiornamento_f24_id_imposta_registro,aggiornamento_f24.contribuente as aggiornamento_f24_contribuente,aggiornamento_f24.cf_coobbligato as aggiornamento_f24_cf_coobbligato,aggiornamento_f24.codice_identificativo as aggiornamento_f24_codice_identificativo,aggiornamento_f24.codice_ufficio as aggiornamento_f24_codice_ufficio,aggiornamento_f24.codice_atto as aggiornamento_f24_codice_atto,aggiornamento_f24.dettagli as aggiornamento_f24_dettagli,aggiornamento_f24.conto_corrente as aggiornamento_f24_conto_corrente,aggiornamento_f24.data_versamento as aggiornamento_f24_data_versamento,aggiornamento_f24.codice_flusso as aggiornamento_f24_codice_flusso';
    }

    /**
     * DDL Table
     */
    public function createTable()
    {
        return $this->pdo->exec("CREATE TABLE `aggiornamento_f24` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_imposta_registro` int(10) unsigned NOT NULL,
  `contribuente` json DEFAULT NULL,
  `cf_coobbligato` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `codice_identificativo` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `codice_ufficio` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `codice_atto` varchar(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `dettagli` json DEFAULT NULL,
  `conto_corrente` json DEFAULT NULL,
  `data_versamento` date DEFAULT NULL,
  `codice_flusso` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_imposta_registro` (`id_imposta_registro`),
  KEY `idx_codice_flusso` (`codice_flusso`),
  KEY `idx_codice_flusso_id_imposta_registro` (`codice_flusso`,`id_imposta_registro`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ");
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id Id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getIdImpostaRegistro()
    {
        return $this->idImpostaRegistro;
    }

    /**
     * @param integer $idImpostaRegistro IdImpostaRegistro
     */
    public function setIdImpostaRegistro($idImpostaRegistro)
    {
        $this->idImpostaRegistro = $idImpostaRegistro;
    }

    /**
     * @return objext (string)
     */
    public function getContribuente()
    {
        return $this->contribuente;
    }

    /**
     * @param objext (string) $contribuente Contribuente
     */
    public function setContribuente($contribuente)
    {
        $this->contribuente = $contribuente;
    }

    /**
     * @return string
     */
    public function getCfCoobbligato()
    {
        return $this->cfCoobbligato;
    }

    /**
     * @param string $cfCoobbligato CfCoobbligato
     * @param int $encodeType
     */
    public function setCfCoobbligato($cfCoobbligato, $encodeType = self::STR_DEFAULT)
    {
        $this->cfCoobbligato = $this->decodeString($cfCoobbligato, $encodeType);
    }

    /**
     * @return string
     */
    public function getCodiceIdentificativo()
    {
        return $this->codiceIdentificativo;
    }

    /**
     * @param string $codiceIdentificativo CodiceIdentificativo
     * @param int $encodeType
     */
    public function setCodiceIdentificativo($codiceIdentificativo, $encodeType = self::STR_DEFAULT)
    {
        $this->codiceIdentificativo = $this->decodeString($codiceIdentificativo, $encodeType);
    }

    /**
     * @return string
     */
    public function getCodiceUfficio()
    {
        return $this->codiceUfficio;
    }

    /**
     * @param string $codiceUfficio CodiceUfficio
     * @param int $encodeType
     */
    public function setCodiceUfficio($codiceUfficio, $encodeType = self::STR_DEFAULT)
    {
        $this->codiceUfficio = $this->decodeString($codiceUfficio, $encodeType);
    }

    /**
     * @return string
     */
    public function getCodiceAtto()
    {
        return $this->codiceAtto;
    }

    /**
     * @param string $codiceAtto CodiceAtto
     * @param int $encodeType
     */
    public function setCodiceAtto($codiceAtto, $encodeType = self::STR_DEFAULT)
    {
        $this->codiceAtto = $this->decodeString($codiceAtto, $encodeType);
    }

    /**
     * @return objext (string)
     */
    public function getDettagli()
    {
        return $this->dettagli;
    }

    /**
     * @param objext (string) $dettagli Dettagli
     */
    public function setDettagli($dettagli)
    {
        $this->dettagli = $dettagli;
    }

    /**
     * @return objext (string)
     */
    public function getContoCorrente()
    {
        return $this->contoCorrente;
    }

    /**
     * @param objext (string) $contoCorrente ContoCorrente
     */
    public function setContoCorrente($contoCorrente)
    {
        $this->contoCorrente = $contoCorrente;
    }

    /**
     * @return string
     */
    public function getDataVersamento()
    {
        return $this->dataVersamento;
    }

    /**
     * @param string $dataVersamento DataVersamento
     * @param int $encodeType
     */
    public function setDataVersamento($dataVersamento, $encodeType = self::STR_DEFAULT)
    {
        $this->dataVersamento = $this->decodeString($dataVersamento, $encodeType);
    }

    /**
     * @return string
     */
    public function getCodiceFlusso()
    {
        return $this->codiceFlusso;
    }

    /**
     * @param string $codiceFlusso CodiceFlusso
     * @param int $encodeType
     */
    public function setCodiceFlusso($codiceFlusso, $encodeType = self::STR_DEFAULT)
    {
        $this->codiceFlusso = $this->decodeString($codiceFlusso, $encodeType);
    }
}