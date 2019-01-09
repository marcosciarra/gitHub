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
class ProformaFatturaModel extends PdaAbstractModel
{

/** @var integer */
protected $id;
/** @var integer */
protected $idContratto;
/** @var integer */
protected $idProforma;
/** @var integer */
protected $idFattura;
/** @var integer */
protected $idVersamento;
/** @var integer */
protected $idMovimentoDettaglio;
/** @var string */
protected $dataVariazione;

function __construct($pdo){parent::__construct($pdo);$this->nomeTabella = 'proforma_fattura';$this->tableName = 'proforma_fattura';}
public function findAll($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1){$distinctStr = ($distinct) ? 'DISTINCT' : '';$query = "SELECT $distinctStr * FROM $this->tableName ";if ($this->whereBase) $query .= " WHERE $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";$query .= $this->createLimitQuery($limit, $offset);return $this->createResultArray($query, null, $typeResult);}

/*--------------------------------------------------- PRIMARY ----------------------------------------------------*/
public function findByPk($id, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(PRIMARY) WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResult($query, array($id), $typeResult);}
public function deleteByPk($id){$query = "DELETE FROM $this->tableName WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResultValue($query, array($id));}

/*---------------------------------------------------- INDEX -----------------------------------------------------*/
public function findByIdxIdVersamento($idVersamento ,$typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_id_versamento) WHERE id_versamento=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($idVersamento), $typeResult);}
public function findByIdxIdFattura($idFattura ,$typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_id_fattura) WHERE id_fattura=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($idFattura), $typeResult);}
public function findByIdxIdProforma($idProforma ,$typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_id_proforma) WHERE id_proforma=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($idProforma), $typeResult);}

public function toArrayAssoc(){$arrayValue = array();if (isset($this->id)) $arrayValue['id'] = $this->id;if (isset($this->idContratto)) $arrayValue['id_contratto'] = $this->idContratto;if (isset($this->idProforma)) $arrayValue['id_proforma'] = $this->idProforma;if (isset($this->idFattura)) $arrayValue['id_fattura'] = ($this->idFattura == self::NULL_VALUE) ? null : $this->idFattura;if (isset($this->idVersamento)) $arrayValue['id_versamento'] = $this->idVersamento;if (isset($this->idMovimentoDettaglio)) $arrayValue['id_movimento_dettaglio'] = $this->idMovimentoDettaglio;if (isset($this->dataVariazione)) $arrayValue['data_variazione'] = ($this->dataVariazione == self::NULL_VALUE) ? null : $this->dataVariazione;return $arrayValue;}
public function createObjKeyArray(array $keyArray){$this->flagObjectDataValorized = false;if ((isset($keyArray['id'])) || (isset($keyArray['proforma_fattura_id']))) {$this->setId(isset($keyArray['id']) ? $keyArray['id'] : $keyArray['proforma_fattura_id']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['id_contratto'])) || (isset($keyArray['proforma_fattura_id_contratto']))) {$this->setIdContratto(isset($keyArray['id_contratto']) ? $keyArray['id_contratto'] : $keyArray['proforma_fattura_id_contratto']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['id_proforma'])) || (isset($keyArray['proforma_fattura_id_proforma']))) {$this->setIdProforma(isset($keyArray['id_proforma']) ? $keyArray['id_proforma'] : $keyArray['proforma_fattura_id_proforma']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['id_fattura'])) || (isset($keyArray['proforma_fattura_id_fattura']))) {$this->setIdFattura(isset($keyArray['id_fattura']) ? $keyArray['id_fattura'] : $keyArray['proforma_fattura_id_fattura']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['id_versamento'])) || (isset($keyArray['proforma_fattura_id_versamento']))) {$this->setIdVersamento(isset($keyArray['id_versamento']) ? $keyArray['id_versamento'] : $keyArray['proforma_fattura_id_versamento']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['id_movimento_dettaglio'])) || (isset($keyArray['proforma_fattura_id_movimento_dettaglio']))) {$this->setIdMovimentoDettaglio(isset($keyArray['id_movimento_dettaglio']) ? $keyArray['id_movimento_dettaglio'] : $keyArray['proforma_fattura_id_movimento_dettaglio']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['data_variazione'])) || (isset($keyArray['proforma_fattura_data_variazione']))) {$this->setDataVariazione(isset($keyArray['data_variazione']) ? $keyArray['data_variazione'] : $keyArray['proforma_fattura_data_variazione']);$this->flagObjectDataValorized = true;}}
public function createKeyArrayFromPositional($positionalArray){$values = array();$values['id'] = $positionalArray[0];$values['id_contratto'] = $positionalArray[1];$values['id_proforma'] = $positionalArray[2];$values['id_fattura'] = ($positionalArray[3] == self::NULL_VALUE) ? null : $positionalArray[3];$values['id_versamento'] = $positionalArray[4];$values['id_movimento_dettaglio'] = $positionalArray[5];$values['data_variazione'] = ($positionalArray[6] == self::NULL_VALUE) ? null : $positionalArray[6];return $values;}
public function getEmptyDbKeyArray(){$values = array();$values['id'] = null;$values['id_contratto'] = null;$values['id_proforma'] = null;$values['id_fattura'] = null;$values['id_versamento'] = null;$values['id_movimento_dettaglio'] = null;$values['data_variazione'] = null;return $values;}
public function getListColumns(){return 'proforma_fattura.id as proforma_fattura_id,proforma_fattura.id_contratto as proforma_fattura_id_contratto,proforma_fattura.id_proforma as proforma_fattura_id_proforma,proforma_fattura.id_fattura as proforma_fattura_id_fattura,proforma_fattura.id_versamento as proforma_fattura_id_versamento,proforma_fattura.id_movimento_dettaglio as proforma_fattura_id_movimento_dettaglio,proforma_fattura.data_variazione as proforma_fattura_data_variazione';}
public function createTable(){return $this->pdo->exec("CREATE TABLE `proforma_fattura` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_contratto` int(10) unsigned NOT NULL,
  `id_proforma` int(10) unsigned NOT NULL,
  `id_fattura` int(10) unsigned DEFAULT NULL,
  `id_versamento` int(10) unsigned NOT NULL,
  `id_movimento_dettaglio` int(10) unsigned NOT NULL,
  `data_variazione` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_versamento` (`id_versamento`),
  KEY `idx_id_fattura` (`id_fattura`),
  KEY `idx_id_proforma` (`id_proforma`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");}
/*-------------------------------------------------- GET e SET ---------------------------------------------------*/
/** @return int(10) unsigned */
public function getId($decode = false){return ($decode) ? $this->getIdValuesList()[$this->id] : $this->id;}
/** @param string $id Id
@param int $encodeType*/
public function setId($id){$this->id=$id;}

/** @return int(10) unsigned */
public function getIdContratto($decode = false){return ($decode) ? $this->getIdContrattoValuesList()[$this->idContratto] : $this->idContratto;}
/** @param string $idContratto IdContratto
@param int $encodeType*/
public function setIdContratto($idContratto){$this->idContratto=$idContratto;}

/** @return int(10) unsigned */
public function getIdProforma($decode = false){return ($decode) ? $this->getIdProformaValuesList()[$this->idProforma] : $this->idProforma;}
/** @param string $idProforma IdProforma
@param int $encodeType*/
public function setIdProforma($idProforma){$this->idProforma=$idProforma;}

/** @return int(10) unsigned */
public function getIdFattura($decode = false){return ($decode) ? $this->getIdFatturaValuesList()[$this->idFattura] : $this->idFattura;}
/** @param string $idFattura IdFattura
@param int $encodeType*/
public function setIdFattura($idFattura){$this->idFattura=$idFattura;}

/** @return int(10) unsigned */
public function getIdVersamento($decode = false){return ($decode) ? $this->getIdVersamentoValuesList()[$this->idVersamento] : $this->idVersamento;}
/** @param string $idVersamento IdVersamento
@param int $encodeType*/
public function setIdVersamento($idVersamento){$this->idVersamento=$idVersamento;}

/** @return int(10) unsigned */
public function getIdMovimentoDettaglio($decode = false){return ($decode) ? $this->getIdMovimentoDettaglioValuesList()[$this->idMovimentoDettaglio] : $this->idMovimentoDettaglio;}
/** @param string $idMovimentoDettaglio IdMovimentoDettaglio
@param int $encodeType*/
public function setIdMovimentoDettaglio($idMovimentoDettaglio){$this->idMovimentoDettaglio=$idMovimentoDettaglio;}

/** @return date */
public function getDataVariazione($decode = false){return ($decode) ? $this->getDataVariazioneValuesList()[$this->dataVariazione] : $this->dataVariazione;}
/** @param string $dataVariazione DataVariazione
@param int $encodeType*/
public function setDataVariazione($dataVariazione){$this->dataVariazione=$dataVariazione;}

}