<?php
/**
Created by Sciarra Marco
**/

namespace Click\Affitti\TblBase;
require_once 'PdaAbstractModel.php';

use Click\Affitti\TblBase\PdaAbstractModel;

/**
@property string nomeTabella
@property string tableName
*/
class AggiornamentoF24Model extends PdaAbstractModel
{

/** @var */
protected $id;
/** @var */
protected $idImpostaRegistro;
/** @var */
protected $contribuente;
/** @var */
protected $cfCoobbligato;
/** @var */
protected $codiceIdentificativo;
/** @var */
protected $codiceUfficio;
/** @var */
protected $codiceAtto;
/** @var */
protected $dettagli;
/** @var */
protected $contoCorrente;
/** @var */
protected $dataVersamento;
/** @var */
protected $codiceFlusso;

function __construct($pdo){parent::__construct($pdo);$this->nomeTabella = 'aggiornamento_f24';$this->tableName = 'aggiornamento_f24';}
public function findAll($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1){$distinctStr = ($distinct) ? 'DISTINCT' : '';$query = "SELECT $distinctStr * FROM $this->tableName ";if ($this->whereBase) $query .= " WHERE $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";$query .= $this->createLimitQuery($limit, $offset);return $this->createResultArray($query, null, $typeResult);}

/*--------------------------------------------------- PRIMARY ----------------------------------------------------*/
public function findByPk($id, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(PRIMARY) WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResult($query, array($id), $typeResult);}
public function deleteByPk($id){$query = "DELETE FROM $this->tableName WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResultValue($query, array($id));}

/*---------------------------------------------------- INDEX -----------------------------------------------------*/
public function findByIdxIdImpostaRegistro($idxIdImpostaRegistro, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_id_imposta_registro) WHERE id_imposta_registro=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($idxIdImpostaRegistro), $typeResult);}
public function findByIdxCodiceFlusso($idxCodiceFlusso, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_codice_flusso) WHERE codice_flusso=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($idxCodiceFlusso), $typeResult);}
public function findByIdxCodiceFlussoIdImpostaRegistro($idxCodiceFlussoIdImpostaRegistro, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_codice_flusso_id_imposta_registro) WHERE codice_flusso=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($idxCodiceFlussoIdImpostaRegistro), $typeResult);}
public function findByIdxCodiceFlussoIdImpostaRegistro($idxCodiceFlussoIdImpostaRegistro, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_codice_flusso_id_imposta_registro) WHERE id_imposta_registro=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($idxCodiceFlussoIdImpostaRegistro), $typeResult);}

public function toArrayAssoc(){$arrayValue = array();if (isset($this->id)) $arrayValue['id'] = $this->id;if (isset($this->idImpostaRegistro)) $arrayValue['id_imposta_registro'] = $this->idImpostaRegistro;if (isset($this->contribuente)) $arrayValue['contribuente'] = $this->jsonEncode($this->contribuente);if (isset($this->cfCoobbligato)) $arrayValue['cf_coobbligato'] = ($this->cfCoobbligato == self::NULL_VALUE) ? null : $this->cfCoobbligato;if (isset($this->codiceIdentificativo)) $arrayValue['codice_identificativo'] = ($this->codiceIdentificativo == self::NULL_VALUE) ? null : $this->codiceIdentificativo;if (isset($this->codiceUfficio)) $arrayValue['codice_ufficio'] = ($this->codiceUfficio == self::NULL_VALUE) ? null : $this->codiceUfficio;if (isset($this->codiceAtto)) $arrayValue['codice_atto'] = ($this->codiceAtto == self::NULL_VALUE) ? null : $this->codiceAtto;if (isset($this->dettagli)) $arrayValue['dettagli'] = $this->jsonEncode($this->dettagli);if (isset($this->contoCorrente)) $arrayValue['conto_corrente'] = $this->jsonEncode($this->contoCorrente);if (isset($this->dataVersamento)) $arrayValue['data_versamento'] = ($this->dataVersamento == self::NULL_VALUE) ? null : $this->dataVersamento;if (isset($this->codiceFlusso)) $arrayValue['codice_flusso'] = ($this->codiceFlusso == self::NULL_VALUE) ? null : $this->codiceFlusso;return $arrayValue;}
public function createObjKeyArray(array $keyArray){$this->flagObjectDataValorized = false;if ((isset($keyArray['id'])) || (isset($keyArray['aggiornamento_f24_id']))) {$this->setId(isset($keyArray['id']) ? $keyArray['id'] : $keyArray['aggiornamento_f24_id']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['id_imposta_registro'])) || (isset($keyArray['aggiornamento_f24_id_imposta_registro']))) {$this->setIdImpostaRegistro(isset($keyArray['id_imposta_registro']) ? $keyArray['id_imposta_registro'] : $keyArray['aggiornamento_f24_id_imposta_registro']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['contribuente'])) || (isset($keyArray['aggiornamento_f24_contribuente']))) {$this->setContribuente(isset($keyArray['contribuente']) ? $keyArray['contribuente'] : $keyArray['aggiornamento_f24_contribuente']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['cf_coobbligato'])) || (isset($keyArray['aggiornamento_f24_cf_coobbligato']))) {$this->setCfCoobbligato(isset($keyArray['cf_coobbligato']) ? $keyArray['cf_coobbligato'] : $keyArray['aggiornamento_f24_cf_coobbligato']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['codice_identificativo'])) || (isset($keyArray['aggiornamento_f24_codice_identificativo']))) {$this->setCodiceIdentificativo(isset($keyArray['codice_identificativo']) ? $keyArray['codice_identificativo'] : $keyArray['aggiornamento_f24_codice_identificativo']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['codice_ufficio'])) || (isset($keyArray['aggiornamento_f24_codice_ufficio']))) {$this->setCodiceUfficio(isset($keyArray['codice_ufficio']) ? $keyArray['codice_ufficio'] : $keyArray['aggiornamento_f24_codice_ufficio']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['codice_atto'])) || (isset($keyArray['aggiornamento_f24_codice_atto']))) {$this->setCodiceAtto(isset($keyArray['codice_atto']) ? $keyArray['codice_atto'] : $keyArray['aggiornamento_f24_codice_atto']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['dettagli'])) || (isset($keyArray['aggiornamento_f24_dettagli']))) {$this->setDettagli(isset($keyArray['dettagli']) ? $keyArray['dettagli'] : $keyArray['aggiornamento_f24_dettagli']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['conto_corrente'])) || (isset($keyArray['aggiornamento_f24_conto_corrente']))) {$this->setContoCorrente(isset($keyArray['conto_corrente']) ? $keyArray['conto_corrente'] : $keyArray['aggiornamento_f24_conto_corrente']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['data_versamento'])) || (isset($keyArray['aggiornamento_f24_data_versamento']))) {$this->setDataVersamento(isset($keyArray['data_versamento']) ? $keyArray['data_versamento'] : $keyArray['aggiornamento_f24_data_versamento']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['codice_flusso'])) || (isset($keyArray['aggiornamento_f24_codice_flusso']))) {$this->setCodiceFlusso(isset($keyArray['codice_flusso']) ? $keyArray['codice_flusso'] : $keyArray['aggiornamento_f24_codice_flusso']);$this->flagObjectDataValorized = true;}}
public function createKeyArrayFromPositional($positionalArray){$values = array();$values['id'] = $positionalArray[0];$values['id_imposta_registro'] = $positionalArray[1];$values['contribuente'] = ($positionalArray[2] == self::NULL_VALUE) ? null : $positionalArray[2];$values['cf_coobbligato'] = ($positionalArray[3] == self::NULL_VALUE) ? null : $positionalArray[3];$values['codice_identificativo'] = ($positionalArray[4] == self::NULL_VALUE) ? null : $positionalArray[4];$values['codice_ufficio'] = ($positionalArray[5] == self::NULL_VALUE) ? null : $positionalArray[5];$values['codice_atto'] = ($positionalArray[6] == self::NULL_VALUE) ? null : $positionalArray[6];$values['dettagli'] = ($positionalArray[7] == self::NULL_VALUE) ? null : $positionalArray[7];$values['conto_corrente'] = ($positionalArray[8] == self::NULL_VALUE) ? null : $positionalArray[8];$values['data_versamento'] = ($positionalArray[9] == self::NULL_VALUE) ? null : $positionalArray[9];$values['codice_flusso'] = ($positionalArray[10] == self::NULL_VALUE) ? null : $positionalArray[10];return $values;}
public function getEmptyDbKeyArray(){$values = array();$values['id'] = null;$values['id_imposta_registro'] = null;$values['contribuente'] = null;$values['cf_coobbligato'] = null;$values['codice_identificativo'] = null;$values['codice_ufficio'] = null;$values['codice_atto'] = null;$values['dettagli'] = null;$values['conto_corrente'] = null;$values['data_versamento'] = null;$values['codice_flusso'] = null;return $values;}
public function getListColumns(){return 'aggiornamento_f24.id as aggiornamento_f24_id,aggiornamento_f24.id_imposta_registro as aggiornamento_f24_id_imposta_registro,aggiornamento_f24.contribuente as aggiornamento_f24_contribuente,aggiornamento_f24.cf_coobbligato as aggiornamento_f24_cf_coobbligato,aggiornamento_f24.codice_identificativo as aggiornamento_f24_codice_identificativo,aggiornamento_f24.codice_ufficio as aggiornamento_f24_codice_ufficio,aggiornamento_f24.codice_atto as aggiornamento_f24_codice_atto,aggiornamento_f24.dettagli as aggiornamento_f24_dettagli,aggiornamento_f24.conto_corrente as aggiornamento_f24_conto_corrente,aggiornamento_f24.data_versamento as aggiornamento_f24_data_versamento,aggiornamento_f24.codice_flusso as aggiornamento_f24_codice_flusso';}
public function createTable(){return $this->pdo->exec("CREATE TABLE `aggiornamento_f24` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_imposta_registro` int(10) unsigned NOT NULL,
  `contribuente` json DEFAULT NULL,
  `cf_coobbligato` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
  `codice_identificativo` varchar(2) CHARACTER SET latin1 DEFAULT NULL,
  `codice_ufficio` varchar(3) CHARACTER SET latin1 DEFAULT NULL,
  `codice_atto` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
  `dettagli` json DEFAULT NULL,
  `conto_corrente` json DEFAULT NULL,
  `data_versamento` date DEFAULT NULL,
  `codice_flusso` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_imposta_registro` (`id_imposta_registro`),
  KEY `idx_codice_flusso` (`codice_flusso`),
  KEY `idx_codice_flusso_id_imposta_registro` (`codice_flusso`,`id_imposta_registro`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci");}
/*-------------------------------------------------- GET e SET ---------------------------------------------------*/
/** @return int(10) unsigned */
public function getId(){return $this->id;}
/** @param string $id Id
@param int $encodeType*/
public function setId($id){$this->id=$id;}

/** @return int(10) unsigned */
public function getIdImpostaRegistro(){return $this->idImpostaRegistro;}
/** @param string $idImpostaRegistro IdImpostaRegistro
@param int $encodeType*/
public function setIdImpostaRegistro($idImpostaRegistro){$this->idImpostaRegistro=$idImpostaRegistro;}

/** @return json */
public function getContribuente(){return $this->contribuente;}
/** @param string $contribuente Contribuente
@param int $encodeType*/
public function setContribuente($contribuente){$this->contribuente=$contribuente;}

/** @return varchar(16) */
public function getCfCoobbligato(){return $this->cfCoobbligato;}
/** @param string $cfCoobbligato CfCoobbligato
@param int $encodeType*/
public function setCfCoobbligato($cfCoobbligato){$this->cfCoobbligato=$cfCoobbligato;}

/** @return varchar(2) */
public function getCodiceIdentificativo(){return $this->codiceIdentificativo;}
/** @param string $codiceIdentificativo CodiceIdentificativo
@param int $encodeType*/
public function setCodiceIdentificativo($codiceIdentificativo){$this->codiceIdentificativo=$codiceIdentificativo;}

/** @return varchar(3) */
public function getCodiceUfficio(){return $this->codiceUfficio;}
/** @param string $codiceUfficio CodiceUfficio
@param int $encodeType*/
public function setCodiceUfficio($codiceUfficio){$this->codiceUfficio=$codiceUfficio;}

/** @return varchar(11) */
public function getCodiceAtto(){return $this->codiceAtto;}
/** @param string $codiceAtto CodiceAtto
@param int $encodeType*/
public function setCodiceAtto($codiceAtto){$this->codiceAtto=$codiceAtto;}

/** @return json */
public function getDettagli(){return $this->dettagli;}
/** @param string $dettagli Dettagli
@param int $encodeType*/
public function setDettagli($dettagli){$this->dettagli=$dettagli;}

/** @return json */
public function getContoCorrente(){return $this->contoCorrente;}
/** @param string $contoCorrente ContoCorrente
@param int $encodeType*/
public function setContoCorrente($contoCorrente){$this->contoCorrente=$contoCorrente;}

/** @return date */
public function getDataVersamento(){return $this->dataVersamento;}
/** @param string $dataVersamento DataVersamento
@param int $encodeType*/
public function setDataVersamento($dataVersamento){$this->dataVersamento=$dataVersamento;}

/** @return varchar(45) */
public function getCodiceFlusso(){return $this->codiceFlusso;}
/** @param string $codiceFlusso CodiceFlusso
@param int $encodeType*/
public function setCodiceFlusso($codiceFlusso){$this->codiceFlusso=$codiceFlusso;}

}