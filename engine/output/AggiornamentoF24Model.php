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