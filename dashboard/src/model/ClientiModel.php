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
class ClientiModel extends PdaAbstractModel
{

/** @var integer */
protected $id;
/** @var string */
protected $ambiente;
/** @var string */
protected $ragioneSociale;
/** @var string */
protected $cognome;
/** @var string */
protected $nome;

function __construct($pdo){parent::__construct($pdo);$this->nomeTabella = 'clienti';$this->tableName = 'clienti';}
public function findAll($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1){$distinctStr = ($distinct) ? 'DISTINCT' : '';$query = "SELECT $distinctStr * FROM $this->tableName ";if ($this->whereBase) $query .= " WHERE $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";$query .= $this->createLimitQuery($limit, $offset);return $this->createResultArray($query, null, $typeResult);}

/*--------------------------------------------------- PRIMARY ----------------------------------------------------*/
public function findByPk($id, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(PRIMARY) WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResult($query, array($id), $typeResult);}
public function deleteByPk($id){$query = "DELETE FROM $this->tableName WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResultValue($query, array($id));}

/*---------------------------------------------------- INDEX -----------------------------------------------------*/

public function toArrayAssoc(){$arrayValue = array();if (isset($this->id)) $arrayValue['id'] = $this->id;if (isset($this->ambiente)) $arrayValue['ambiente'] = ($this->ambiente == self::NULL_VALUE) ? null : $this->ambiente;if (isset($this->ragioneSociale)) $arrayValue['ragione_sociale'] = ($this->ragioneSociale == self::NULL_VALUE) ? null : $this->ragioneSociale;if (isset($this->cognome)) $arrayValue['cognome'] = ($this->cognome == self::NULL_VALUE) ? null : $this->cognome;if (isset($this->nome)) $arrayValue['nome'] = ($this->nome == self::NULL_VALUE) ? null : $this->nome;return $arrayValue;}
public function createObjKeyArray(array $keyArray){$this->flagObjectDataValorized = false;if ((isset($keyArray['id'])) || (isset($keyArray['clienti_id']))) {$this->setId(isset($keyArray['id']) ? $keyArray['id'] : $keyArray['clienti_id']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['ambiente'])) || (isset($keyArray['clienti_ambiente']))) {$this->setAmbiente(isset($keyArray['ambiente']) ? $keyArray['ambiente'] : $keyArray['clienti_ambiente']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['ragione_sociale'])) || (isset($keyArray['clienti_ragione_sociale']))) {$this->setRagioneSociale(isset($keyArray['ragione_sociale']) ? $keyArray['ragione_sociale'] : $keyArray['clienti_ragione_sociale']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['cognome'])) || (isset($keyArray['clienti_cognome']))) {$this->setCognome(isset($keyArray['cognome']) ? $keyArray['cognome'] : $keyArray['clienti_cognome']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['nome'])) || (isset($keyArray['clienti_nome']))) {$this->setNome(isset($keyArray['nome']) ? $keyArray['nome'] : $keyArray['clienti_nome']);$this->flagObjectDataValorized = true;}}
public function createKeyArrayFromPositional($positionalArray){$values = array();$values['id'] = $positionalArray[0];$values['ambiente'] = ($positionalArray[1] == self::NULL_VALUE) ? null : $positionalArray[1];$values['ragione_sociale'] = ($positionalArray[2] == self::NULL_VALUE) ? null : $positionalArray[2];$values['cognome'] = ($positionalArray[3] == self::NULL_VALUE) ? null : $positionalArray[3];$values['nome'] = ($positionalArray[4] == self::NULL_VALUE) ? null : $positionalArray[4];return $values;}
public function getEmptyDbKeyArray(){$values = array();$values['id'] = null;$values['ambiente'] = null;$values['ragione_sociale'] = null;$values['cognome'] = null;$values['nome'] = null;return $values;}
public function getListColumns(){return 'clienti.id as clienti_id,clienti.ambiente as clienti_ambiente,clienti.ragione_sociale as clienti_ragione_sociale,clienti.cognome as clienti_cognome,clienti.nome as clienti_nome';}
public function createTable(){return $this->pdo->exec("CREATE TABLE `clienti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ambiente` varchar(45) DEFAULT NULL,
  `ragione_sociale` varchar(100) DEFAULT NULL,
  `cognome` varchar(100) DEFAULT NULL,
  `nome` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1");}
/*-------------------------------------------------- GET e SET ---------------------------------------------------*/
/** @return int(10) unsigned */
public function getId($decode = false){return ($decode) ? $this->getIdValuesList()[$this->id] : $this->id;}
/** @param string $id Id
@param int $encodeType*/
public function setId($id){$this->id=$id;}

/** @return varchar(45) */
public function getAmbiente($decode = false){return ($decode) ? $this->getAmbienteValuesList()[$this->ambiente] : $this->ambiente;}
/** @param string $ambiente Ambiente
@param int $encodeType*/
public function setAmbiente($ambiente){$this->ambiente=$ambiente;}

/** @return varchar(100) */
public function getRagioneSociale($decode = false){return ($decode) ? $this->getRagioneSocialeValuesList()[$this->ragioneSociale] : $this->ragioneSociale;}
/** @param string $ragioneSociale RagioneSociale
@param int $encodeType*/
public function setRagioneSociale($ragioneSociale){$this->ragioneSociale=$ragioneSociale;}

/** @return varchar(100) */
public function getCognome($decode = false){return ($decode) ? $this->getCognomeValuesList()[$this->cognome] : $this->cognome;}
/** @param string $cognome Cognome
@param int $encodeType*/
public function setCognome($cognome){$this->cognome=$cognome;}

/** @return varchar(100) */
public function getNome($decode = false){return ($decode) ? $this->getNomeValuesList()[$this->nome] : $this->nome;}
/** @param string $nome Nome
@param int $encodeType*/
public function setNome($nome){$this->nome=$nome;}

}