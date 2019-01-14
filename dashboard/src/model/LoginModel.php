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
class LoginModel extends PdaAbstractModel
{

/** @var integer */
protected $id;
/** @var string */
protected $username;
/** @var string */
protected $password;
/** @var string */
protected $nome;
/** @var string */
protected $cognome;
/** @var string */
protected $email;
/** @var string (enum) */
protected $tipoUtente;
/** @var integer */
protected $bloccato;
/** @var string */
protected $ultimoAccesso;
/** @var string */
protected $idSessione;
/** @var string */
protected $dataScadenza;

function __construct($pdo){parent::__construct($pdo);$this->nomeTabella = 'login';$this->tableName = 'login';}
public function findAll($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1){$distinctStr = ($distinct) ? 'DISTINCT' : '';$query = "SELECT $distinctStr * FROM $this->tableName ";if ($this->whereBase) $query .= " WHERE $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";$query .= $this->createLimitQuery($limit, $offset);return $this->createResultArray($query, null, $typeResult);}

/*--------------------------------------------------- PRIMARY ----------------------------------------------------*/
public function findByPk($id, $typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(PRIMARY) WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResult($query, array($id), $typeResult);}
public function deleteByPk($id){$query = "DELETE FROM $this->tableName WHERE id=? ";if ($this->whereBase) $query .= " AND $this->whereBase";return $this->createResultValue($query, array($id));}

/*---------------------------------------------------- INDEX -----------------------------------------------------*/
public function findByIdxUsername($username ,$typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_username) WHERE username=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($username), $typeResult);}
public function findByIdxEmail($email ,$typeResult = self::FETCH_OBJ){$query = "SELECT * FROM $this->tableName USE INDEX(idx_email) WHERE email=? ";if ($this->whereBase) $query .= " AND $this->whereBase";if ($this->orderBase) $query .= " ORDER BY $this->orderBase";return $this->createResultArray($query, array($email), $typeResult);}

public function toArrayAssoc(){$arrayValue = array();if (isset($this->id)) $arrayValue['id'] = $this->id;if (isset($this->username)) $arrayValue['username'] = $this->username;if (isset($this->password)) $arrayValue['password'] = $this->password;if (isset($this->nome)) $arrayValue['nome'] = ($this->nome == self::NULL_VALUE) ? null : $this->nome;if (isset($this->cognome)) $arrayValue['cognome'] = ($this->cognome == self::NULL_VALUE) ? null : $this->cognome;if (isset($this->email)) $arrayValue['email'] = $this->email;if (isset($this->tipoUtente)) $arrayValue['tipo_utente'] = $this->tipoUtente;if (isset($this->bloccato)) $arrayValue['bloccato'] = $this->bloccato;if (isset($this->ultimoAccesso)) $arrayValue['ultimo_accesso'] = ($this->ultimoAccesso == self::NULL_VALUE) ? null : $this->ultimoAccesso;if (isset($this->idSessione)) $arrayValue['id_sessione'] = ($this->idSessione == self::NULL_VALUE) ? null : $this->idSessione;if (isset($this->dataScadenza)) $arrayValue['data_scadenza'] = ($this->dataScadenza == self::NULL_VALUE) ? null : $this->dataScadenza;return $arrayValue;}
public function createObjKeyArray(array $keyArray){$this->flagObjectDataValorized = false;if ((isset($keyArray['id'])) || (isset($keyArray['login_id']))) {$this->setId(isset($keyArray['id']) ? $keyArray['id'] : $keyArray['login_id']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['username'])) || (isset($keyArray['login_username']))) {$this->setUsername(isset($keyArray['username']) ? $keyArray['username'] : $keyArray['login_username']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['password'])) || (isset($keyArray['login_password']))) {$this->setPassword(isset($keyArray['password']) ? $keyArray['password'] : $keyArray['login_password']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['nome'])) || (isset($keyArray['login_nome']))) {$this->setNome(isset($keyArray['nome']) ? $keyArray['nome'] : $keyArray['login_nome']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['cognome'])) || (isset($keyArray['login_cognome']))) {$this->setCognome(isset($keyArray['cognome']) ? $keyArray['cognome'] : $keyArray['login_cognome']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['email'])) || (isset($keyArray['login_email']))) {$this->setEmail(isset($keyArray['email']) ? $keyArray['email'] : $keyArray['login_email']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['tipo_utente'])) || (isset($keyArray['login_tipo_utente']))) {$this->setTipoUtente(isset($keyArray['tipo_utente']) ? $keyArray['tipo_utente'] : $keyArray['login_tipo_utente']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['bloccato'])) || (isset($keyArray['login_bloccato']))) {$this->setBloccato(isset($keyArray['bloccato']) ? $keyArray['bloccato'] : $keyArray['login_bloccato']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['ultimo_accesso'])) || (isset($keyArray['login_ultimo_accesso']))) {$this->setUltimoAccesso(isset($keyArray['ultimo_accesso']) ? $keyArray['ultimo_accesso'] : $keyArray['login_ultimo_accesso']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['id_sessione'])) || (isset($keyArray['login_id_sessione']))) {$this->setIdSessione(isset($keyArray['id_sessione']) ? $keyArray['id_sessione'] : $keyArray['login_id_sessione']);$this->flagObjectDataValorized = true;}if ((isset($keyArray['data_scadenza'])) || (isset($keyArray['login_data_scadenza']))) {$this->setDataScadenza(isset($keyArray['data_scadenza']) ? $keyArray['data_scadenza'] : $keyArray['login_data_scadenza']);$this->flagObjectDataValorized = true;}}
public function createKeyArrayFromPositional($positionalArray){$values = array();$values['id'] = $positionalArray[0];$values['username'] = $positionalArray[1];$values['password'] = $positionalArray[2];$values['nome'] = ($positionalArray[3] == self::NULL_VALUE) ? null : $positionalArray[3];$values['cognome'] = ($positionalArray[4] == self::NULL_VALUE) ? null : $positionalArray[4];$values['email'] = $positionalArray[5];$values['tipo_utente'] = $positionalArray[6];$values['bloccato'] = $positionalArray[7];$values['ultimo_accesso'] = ($positionalArray[8] == self::NULL_VALUE) ? null : $positionalArray[8];$values['id_sessione'] = ($positionalArray[9] == self::NULL_VALUE) ? null : $positionalArray[9];$values['data_scadenza'] = ($positionalArray[10] == self::NULL_VALUE) ? null : $positionalArray[10];return $values;}
public function getEmptyDbKeyArray(){$values = array();$values['id'] = null;$values['username'] = null;$values['password'] = null;$values['nome'] = null;$values['cognome'] = null;$values['email'] = null;$values['tipo_utente'] = null;$values['bloccato'] = null;$values['ultimo_accesso'] = null;$values['id_sessione'] = null;$values['data_scadenza'] = null;return $values;}
public function getListColumns(){return 'login.id as login_id,login.username as login_username,login.password as login_password,login.nome as login_nome,login.cognome as login_cognome,login.email as login_email,login.tipo_utente as login_tipo_utente,login.bloccato as login_bloccato,login.ultimo_accesso as login_ultimo_accesso,login.id_sessione as login_id_sessione,login.data_scadenza as login_data_scadenza';}
public function createTable(){return $this->pdo->exec("CREATE TABLE `login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `cognome` varchar(45) DEFAULT NULL,
  `email` varchar(45) NOT NULL,
  `tipo_utente` enum('SU','U','A') NOT NULL DEFAULT 'U' COMMENT 'SU= Super User\nU= Utente\nA= Amministratore',
  `bloccato` tinyint(1) NOT NULL DEFAULT '0',
  `ultimo_accesso` datetime DEFAULT NULL,
  `id_sessione` varchar(255) DEFAULT NULL,
  `data_scadenza` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1");}
/*-------------------------------------------------- GET e SET ---------------------------------------------------*/
/** @return int(10) unsigned */
public function getId($decode = false){return ($decode) ? $this->getIdValuesList()[$this->id] : $this->id;}
/** @param string $id Id
@param int $encodeType*/
public function setId($id){$this->id=$id;}

/** @return varchar(45) */
public function getUsername($decode = false){return ($decode) ? $this->getUsernameValuesList()[$this->username] : $this->username;}
/** @param string $username Username
@param int $encodeType*/
public function setUsername($username){$this->username=$username;}

/** @return varchar(255) */
public function getPassword($decode = false){return ($decode) ? $this->getPasswordValuesList()[$this->password] : $this->password;}
/** @param string $password Password
@param int $encodeType*/
public function setPassword($password){$this->password=$password;}

/** @return varchar(45) */
public function getNome($decode = false){return ($decode) ? $this->getNomeValuesList()[$this->nome] : $this->nome;}
/** @param string $nome Nome
@param int $encodeType*/
public function setNome($nome){$this->nome=$nome;}

/** @return varchar(45) */
public function getCognome($decode = false){return ($decode) ? $this->getCognomeValuesList()[$this->cognome] : $this->cognome;}
/** @param string $cognome Cognome
@param int $encodeType*/
public function setCognome($cognome){$this->cognome=$cognome;}

/** @return varchar(45) */
public function getEmail($decode = false){return ($decode) ? $this->getEmailValuesList()[$this->email] : $this->email;}
/** @param string $email Email
@param int $encodeType*/
public function setEmail($email){$this->email=$email;}

/** @return enum('SU','U','A') */
public function getTipoUtente($decode = false){return ($decode) ? $this->getTipoUtenteValuesList()[$this->tipoUtente] : $this->tipoUtente;}
/** @param string $tipoUtente TipoUtente
@param int $encodeType*/
public function setTipoUtente($tipoUtente){$this->tipoUtente=$tipoUtente;}

/** @return tinyint(1) */
public function getBloccato($decode = false){return ($decode) ? $this->getBloccatoValuesList()[$this->bloccato] : $this->bloccato;}
/** @param string $bloccato Bloccato
@param int $encodeType*/
public function setBloccato($bloccato){$this->bloccato=$bloccato;}

/** @return datetime */
public function getUltimoAccesso($decode = false){return ($decode) ? $this->getUltimoAccessoValuesList()[$this->ultimoAccesso] : $this->ultimoAccesso;}
/** @param string $ultimoAccesso UltimoAccesso
@param int $encodeType*/
public function setUltimoAccesso($ultimoAccesso){$this->ultimoAccesso=$ultimoAccesso;}

/** @return varchar(255) */
public function getIdSessione($decode = false){return ($decode) ? $this->getIdSessioneValuesList()[$this->idSessione] : $this->idSessione;}
/** @param string $idSessione IdSessione
@param int $encodeType*/
public function setIdSessione($idSessione){$this->idSessione=$idSessione;}

/** @return date */
public function getDataScadenza($decode = false){return ($decode) ? $this->getDataScadenzaValuesList()[$this->dataScadenza] : $this->dataScadenza;}
/** @param string $dataScadenza DataScadenza
@param int $encodeType*/
public function setDataScadenza($dataScadenza){$this->dataScadenza=$dataScadenza;}

public function getDataScadenzaValuesList($json = false){$kv = ['SU' => 'Super User' , 'U' => 'Utente' , 'A' => 'Amministratore'];return ($json) ? $this->createJsonKeyValArray($kv) : $kv;}
}