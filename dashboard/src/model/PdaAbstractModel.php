<?php

namespace Click\Affitti\TblBase;

use Drakkar\DrakkarDbConnector;
use Drakkar\Exception\DrakkarDuplicatePrimaryException;
use Drakkar\Exception\DrakkarException;
use Drakkar\Exception\DrakkarIntegrityConstrainException;
use Drakkar\Exception\DrakkarJsonException;

use Drakkar\Log\DrakkarTraceLog;


require_once 'DrakkarDbConnector.php';
require_once 'PdaInterfaceModel.php';

abstract class PdaAbstractModel implements PdaInterfaceModel
{

    //private $logger;

    // Default Encode/Decode String
    const STR_DEFAULT = self::STR_UTF8;


    /** @var  DrakkarDbConnector */
    protected $conn;

    protected $flagMultiKey = false;
    /** @var  int */
    protected $id;

    /** @var  string */
    protected $nomeTabella;
    /** @var  string */
    protected $tableName;

    /** Condizioni Where aggiuntive per query base */
    protected $whereBase = "";

    /** @var string */
    protected $orderBase = "";

    /** @var bool */
    protected $flagObjectDataValorized = false;

    /**
     * protected $flagMultiKey = false;
     *
     * /**
     *se settato esegue la limit con gli attributi passati<br/>
     *                           es: - Per ottenere i primi 10 elementi della query basterà passare '10'<br/>
     *                               - Per ottenere 10 elementi partendo dal terzo si passerà '3,10'
     *
     * @var integer
     */
    protected $limitBase = -1;
    /**
     * @var integer
     */
    protected $offsetBase = -1;

    /**
     * PdaAbstractModel constructor.
     *
     * @param DrakkarDbConnector|null $conn
     */
    function __construct($conn = null)
    {
        $this->conn = new DrakkarDbConnector($conn);
        /*
        \Logger::configure('/var/www/html/conf/log.properties');
        $this->logger = \Logger::getLogger('PdaAbstractModel');
        */
    }

    /**
     * Save the object into database
     *
     * @param bool $forcedInsert if true, save the object using the primary key that has been enhanced
     *
     * @return int|null|string
     */
    public function saveOrUpdate($forcedInsert = false)
    {
        return $this->saveKeyArray(null, $forcedInsert);
    }

    /**
     * Save the object into database and save the operation log
     *
     * @param $idUser
     * @param bool $forcedInsert if true, save the object using the primary key that has been enhanced
     *
     * @return int|null|string
     */
    public function saveOrUpdateAndLog($idUser, $forcedInsert = false)
    {
        $save = $this->saveOrUpdate($forcedInsert);
        new DrakkarTraceLog($idUser);
        return $save;
    }

    /**
     * Truncate table's class
     *
     * @return  boolean
     */
    public final function truncateTable()
    {
        return $this->conn->exec('TRUNCATE TABLE ' . $this->nomeTabella);
    }

    /**
     * Delete all data
     *
     * @return  boolean
     */
    public final function deleteTable()
    {
        return $this->conn->exec('DELETE FROM  ' . $this->nomeTabella);
    }

    /**
     * @inheritDoc
     */
    public final function deleteTableAndLog($idUser)
    {
        new DrakkarTraceLog($idUser);
        $this->deleteTable();
    }


    /**
     * Truncate table's class
     *
     * @param DrakkarDbConnector $conn
     *
     * @return  boolean
     */
    public final static function truncateTableStatic($conn)
    {
        return self::truncateTable();
        //return $conn->exec('TRUNCATE TABLE ' . self::getNomeTabella());
    }

    /**
     * Funzione per la cancellazione di tutto il contenuto della tabella
     *
     * @param DrakkarDbConnector $conn
     *
     * @return  boolean
     */
    public final static function deleteTableStatic($conn)
    {
        return self::deleteTable();
        //return $conn->exec('DELETE FROM  ' . self::getNomeTabella());
    }

    /**
     * Ritorna il nome Java Stile di una variabile
     *
     * @param $variabile
     *
     * @return string
     */
    protected final function creaNomeVariabile($variabile)
    {
        $variabile = ucwords(strtolower(str_replace("_", " ", $variabile)));
        $variabile = strtolower(substr($variabile, 0, 1)) . substr($variabile, 1, strlen($variabile));

        return str_replace(" ", "", $variabile);
    }


    /**
     * @param array $arrayPosizionale
     *
     * @return null|string
     */
    public function saveOrUpdatePosizionale($arrayPosizionale)
    {
        $arrayValori = $this->createKeyArrayFromPositional($arrayPosizionale);

        return $this->saveKeyArray($arrayValori);
    }

    //TODO: Risolere problema update chiavi multiple

    /**
     * @param array $arrayValori
     * @param bool $insertForced ser settato a true forza il salvataggio del nuovo recor anche se è presnete la
     *                            chiave primaria
     *
     * @return null|string|int
     */
    public function saveKeyArray($arrayValori = null, $insertForced = false)
    {
        if (!$arrayValori) $arrayValori = $this->toArrayAssoc();
        if ($insertForced) {
            try {
                $this->insertDb($arrayValori);
            } catch (DrakkarIntegrityConstrainException $e) {
                $this->updateDb($arrayValori);
            }
            // $this->insertForcedDb($arrayValori);
        } else {
            if ($this->checkPk()) {
                $this->updateDb($arrayValori);
            } else {
                $this->insertDb($arrayValori);
            }
        }


        return $this->id;
    }

    /**
     * Exec Update data on DB
     *
     * @param array $arrayValori
     *
     * @return int rowCount
     * @throws \Drakkar\Exception\DrakkarConnectionException
     * @throws \Drakkar\Exception\DrakkarException
     */
    public function updateDb($arrayValori)
    {
        if ($this->flagMultiKey) {
            $c =
                $this->conn->updateKeyArray($this->nomeTabella, $arrayValori, $this->getArrayPk(), $this->wherePK());
        } else {
            $c = $this->conn->updateKeyArray($this->nomeTabella, $arrayValori, $this->id);
        }

        return $c;
    }

    /**
     * Exec Insert data on DB and set id
     *
     * @param $arrayValori
     *
     * @throws \Drakkar\Exception\DrakkarConnectionException
     * @throws \Drakkar\Exception\DrakkarException
     */
    public function insertDb($arrayValori)
    {
        $this->id = $this->conn->insertKeyArray($this->nomeTabella, $arrayValori);
    }

    /**
     * Exec Insert data on DB and set id
     *
     * @param $arrayValori
     *
     * @throws \Drakkar\Exception\DrakkarConnectionException
     * @throws \Drakkar\Exception\DrakkarIntegrityConstrainException
     * @throws \Drakkar\Exception\DrakkarException
     */
    protected function insertForcedDb($arrayValori)
    {
        $id = $this->conn->insertForcedKeyArray($this->nomeTabella, $arrayValori);
        if ($id != 'OK')
            $this->id = $id;
    }


    /**
     * @param string $query
     * @param array|null parameters
     *
     * @return int|string
     */
    protected function createResultValue($query, $parameters = null)
    {
        //return queryPreparedPdo($this->conn, $this->conn->prepare($query), $parameters, "v");
        try {
            return $this->conn->execQuery($query, $parameters, DrakkarDbConnector::FETCH_VALUE);
        } catch (\Exception $e) {

            //$this->logger->error("createResultValue: $e->getMessage() ");
            return null;
        }
    }

    protected function returnResult($typeResult, $value = null)
    {


        if (is_null($value)) {
            switch ($typeResult) {
                case self::FETCH_OBJ:
                    return $typeResult;
                    break;
                case self::FETCH_KEYARRAY:
                case self::FETCH_ASSOC:
                    return $this->toArrayAssoc();
                    break;
                default:
                    return false;  //TODO da finire e sistemare
            }
        } else {
            switch ($typeResult) {
                case self::FETCH_OBJ:
                    return $value;
                    break;
                case self::FETCH_KEYARRAY:
                case self::FETCH_ASSOC:
                    $app = [];
                    foreach ($value as $v) {
                        $app[] = $v->toArrayAssoc();
                    }
                    return $app;
                    break;
                default:
                    return false;  //TODO da finire e sistemare
            }
        }

    }

    //TODO sistemare livello accesso

    /**
     * @param string $query
     * @param null $parametri
     * @param int $tipoRisultato
     * @param int $encodeType
     *
     * @return array|string
     */
    public function createResultArray($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ,
                                      $encodeType = self::STR_DEFAULT)
    {
        try {
            $nomeClasse = get_class($this);
            $arrayObj = array();
            switch ($tipoRisultato) {
                case self::FETCH_OBJ:
                    $valoriPdo = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_ASSOC);
                    foreach ($valoriPdo as $valori) {
                        $app = new $nomeClasse($this->conn);
                        $app->createObjKeyArray($valori);
                        $arrayObj[] = $app;
                    }


                    return $arrayObj;
                    break;
                case self::FETCH_JSON:
                    $valoriPdo = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_ASSOC);
                    foreach ($valoriPdo as $valori) {
                        $arrayObj[] = $this->encodeString($valori, $encodeType);
                    }

                    // $valoriPdo->closeCursor()

                    return json_encode($arrayObj);
                    break;
                case self::FETCH_KEYARRAY:

                    $valoriPdo = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_ASSOC);
                    foreach ($valoriPdo as $valori) {
                        $arrayObj[] = $this->encodeArray($valori, $encodeType);
                    }

                    // // $valoriPdo->closeCursor()
                    return $arrayObj;
                    break;
                case self::FETCH_KEYVALUEARRAY:
                    $valoriPdo = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_NUM);
                    foreach ($valoriPdo as $valori) {
                        $arrayObj[$valori[0]] = $this->encodeString($valori[1], $encodeType);
                    }

                    // $valoriPdo->closeCursor()
                    return $arrayObj;
                    break;
                case self::FETCH_VALUEARRAY:
                    $valoriPdo = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_NUM);
                    foreach ($valoriPdo as $valori) {
                        $arrayObj[] = $valori[0]; //TODO sistemare encoding
                    }

                    // $valoriPdo->closeCursor()
                    return $arrayObj;
                    break;
                case self::FETCH_NUMARRAY:
                    $valoriPdo = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_NUM);
                    foreach ($valoriPdo as $valori) {
                        $arrayObj[] = $this->encodeArray($valori, $encodeType);
                    }

                    // $valoriPdo->closeCursor()

                    return $arrayObj;
                    break;
                case self::FETCH_XML:
                    $xml = '<obj>';
                    $valoriPdo = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_ASSOC);
                    foreach ($valoriPdo as $valori) {
                        $xml .= "<$this->nomeTabella>";
                        foreach ($valori as $chiave => $valore) {
                            $xml .= "<$chiave>$valore</$chiave>";
                        }
                        $xml .= "</$this->nomeTabella>";
                    }
                    $xml = '</obj>';

                    return $xml;
                    break;
            }

            return null;
        } catch (\Exception $e) {

            //$this->logger->error("createResultArray: $e->getMessage() ");
            return null;
        }
    }

    /**
     * @param      $query
     * @param null $parametri
     * @param int $tipoRisultato
     * @param int $encodeType
     *
     * @return  $this|array|null|string
     */
    protected function createResult($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ,
                                    $encodeType = self::STR_DEFAULT)
    {
        try {
            if ($tipoRisultato == self::FETCH_NUMARRAY) {
                $valori = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_NUM);
            } else {
                $valori = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_SINGLE);

            }

            if (!$valori) {
                return null;
            }

            switch ($tipoRisultato) {
                case self::FETCH_OBJ:
                    foreach ($valori as $chiave => $valore) {
                        $variabile = $this->creaNomeVariabile($chiave);
                        $this->$variabile = $this->encodeString($valore, $encodeType);
                    }
                    return $this;
                    break;
                case self::FETCH_JSON:
                    return json_encode($this->encodeArray($valori, $encodeType));
                    break;
                case self::FETCH_KEYARRAY:
                    return $this->encodeArray($valori, $encodeType);
                    break;
                case self::FETCH_NUMARRAY:
                    return $valori;
                    break;
                case self::FETCH_XML:
                    $xml = "<$this->nomeTabella>";
                    foreach ($valori as $chiave => $valore) {
                        $xml .= "<$chiave>$valore</$chiave>";
                    }
                    $xml .= "</$this->nomeTabella>";

                    return $xml;
                    break;
            }

            return null;
        } catch (\Exception $e) {

            //$this->logger->error("createResult: $e->getMessage() ");
            return null;
        }
    }

    /**
     * Dato un oggetto Json istanzia la classe e la popola con i valori
     *
     * @param      $json
     * @param bool $flgObjJson
     */
    public function creaObjJson($json, $flgObjJson = false)
    {
        if ($flgObjJson) {
            $json = json_encode($json);
        }
        $json = json_decode($json, true);
        $this->createObjKeyArray($json);
    }

    /**
     * Restituisce la rappresentazione della classe in formato Json
     *
     * @return string
     */
    public function getEmptyObjJson()
    {
        return json_encode(get_object_vars($this));
    }

    public function getEmptyDbJson()
    {
        return json_encode($this->getEmptyDbKeyArray());
    }

    /**
     * Restituisce la rappresentazione della classe in formato array
     *
     * @return array
     */
    public function getEmptyObjKeyArray()
    {
        return get_object_vars($this);
    }

    public function getJsonValue($metodo, $indice = null, $key = null)
    {
        if (is_null($indice)) {
            return $this->$metodo();
        }

        $app = json_decode($this->$metodo());

        if (is_null($key)) {
            return $app[$indice];
        }

        return isset($app[$indice]->$key) ? $app[$indice]->$key : null;

    }

    /**
     * @param $input
     * @param $typeEncode
     *
     * @return string
     */
    protected function encodeString($input, $typeEncode)
    {
        if (is_string($input)) {
            switch ($typeEncode) {
                case self::STR_UTF8:
                    return utf8_encode($input);
                    break;
                default:
                    return $input;
            }
        } else {
            return $input;
        }
    }

    /**
     * @param $input
     * @param $typeEncode
     *
     * @return string
     */
    protected function decodeString($input, $typeEncode)
    {
        if (is_string($input))
            switch ($typeEncode) {
                case self::STR_UTF8:
                    return utf8_decode($input);
                    break;
                default:
                    return $input;
            }
        else
            return $input;
    }

    /**
     * @param $input
     * @param $typeEncode
     *
     * @return array
     */
    protected function encodeArray($input, $typeEncode)
    {
        if (is_array($input)) {
            $app = array();
            foreach ($input as $key => $value) {
                $app[$key] = $this->encodeString($value, $typeEncode);
            }

            return $app;
        } else
            return $input;
    }


    /**
     * @param $input
     * @param $typeEncode
     *
     * @return array
     */
    protected function decodeArray($input, $typeEncode)
    {
        if (is_array($input)) {
            $app = array();
            foreach ($input as $key => $value) {
                $app[$key] = $this->decodeString($value, $typeEncode);
            }

            return $app;
        } else
            return $input;
    }

    /**
     * @param $input
     *
     * @return string|int
     */
    public function encodeObj($input)
    {
        if (is_object($input)) {
            return $input;
            //todo sistempare
            /* $vars = array_keys(get_object_vars($input));

               foreach ($vars as $var) {
                   utf8_encode_deep($input->$var,$typeEncode);
               }*/
        } else
            return $input;
    }


    /**
     * @param string $string search string
     * @param int $likeMatching pattern for like matching
     */
    protected function prepareLikeMatching($string, $likeMatching)
    {
        switch ($likeMatching) {
            case self::LIKE_MATCHING_LEFT :
                return '%' . $string;
            case self::LIKE_MATCHING_RIGHT :
                return $string . '%';
            case self::LIKE_MATCHING_BOTH :
                return '%' . $string . '%';
            default:
                return $string;
        }
    }

    public function checkPk()
    {
        return ($this->id) ? true : false;
    }


    protected function createLimitQuery($limit = -1, $offset = -1)
    {
        $s = '';
        if ($limit > -1)
            $s .= ' LIMIT ' . $limit;
        elseif ($this->limitBase > -1)
            $s .= ' LIMIT ' . $this->limitBase;

        if ($offset > -1)
            $s .= ' OFFSET ' . $offset;
        elseif ($this->offsetBase > -1)
            $s .= ' OFFSET ' . $this->offsetBase;

        return $s;
    }

    protected function createJsonKeyValArray($array, $key = 'key', $val = 'val')
    {
        $output = array();
        foreach ($array as $k => $v) {
            $output[] = [$key => $k, $val => $v];
        }
        return $output;
    }

    //------------------------------
    // Getter & Setter
    //------------------------------

    /**
     * @return DrakkarDbConnector
     * @deprecated
     */
    public function getPdo()
    {
        return $this->getConn();
    }


    /**
     * @return DrakkarDbConnector
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @param DrakkarDbConnector $conn
     *
     * @deprecated
     */
    public function setPdo($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param DrakkarDbConnector $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return string
     */
    public function getWhereBase()
    {
        return $this->whereBase;
    }

    /**
     * @param string $whereBase
     */
    public function setWhereBase($whereBase)
    {
        $this->whereBase = $whereBase;
    }

    /**
     * @return string
     */
    public function getOrderBase()
    {
        return $this->orderBase;
    }

    /**
     * @param string $orderBase
     */
    public function setOrderBase($orderBase)
    {
        $this->orderBase = $orderBase;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getNomeTabella()
    {
        return $this->nomeTabella;
    }

    /**
     * @param string $nomeTabella
     *
     * @deprecated
     */
    public function setNomeTabella($nomeTabella)
    {
        $this->setTableName($nomeTabella);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        //TODO rimuovere per deprecato
        $this->nomeTabella = $tableName;

    }


    /**
     * @return integer
     */
    public function getLimitBase()
    {
        return $this->limitBase;
    }

    /**
     * @param integer $limitBase
     */
    // public function setLimitBase(Integer $limitBase)
    public function setLimitBase($limitBase)
    {
        $this->limitBase = $limitBase;
    }

    /**
     * @return integer
     */
    public function getOffsetBase()
    {
        return $this->offsetBase;
    }

    /**
     * @param integer $offsetBase
     */
    public function setOffsetBase($offsetBase)
    {
        $this->offsetBase = $offsetBase;
    }

    /**
     * @return bool
     */
    public function isFlagObjectDataValorized()
    {
        return $this->flagObjectDataValorized;
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public function isJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    /**
     * @param $string
     *
     * @return string
     * @throws DrakkarJsonException
     */
    public function jsonEncode($string)
    {
        if ($string == null) return null;

        if ($this->isJson($string))
            return $string;
        else {
            $result = json_encode($string);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $result;
            }
            throw new DrakkarJsonException(json_last_error());
        }

    }

    /**
     * @param $string
     *
     * @return string
     * @throws DrakkarJsonException
     */
    public function jsonDecode($string)
    {
        $result = json_decode($string);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new DrakkarJsonException(json_last_error());
    }

    //------------------------------
    // Abstract
    //------------------------------

    /**
    * @return array
    * @deprecated
    */
    public function createKeyArray(){
    	return $this->toArrayAssoc();
    }

    /**
     * Transforms the object into a key array
     *
     * @return array
     */
    public abstract function toArrayAssoc();

    /**
     * It transforms the keyarray in an object
     *
     * @param array $keyArray
     */
    public abstract function createObjKeyArray(array $keyArray);

    /**
     * @param array $positionalArray
     *
     * @return array
     */
    public abstract function createKeyArrayFromPositional($positionalArray);

    /**
     * Return columns' list
     *
     * @return string
     */
    public abstract function getListColumns();

    /**
     * DDL Table
     */
    public abstract function createTable();


    public abstract function getEmptyDbKeyArray();

    //------------------------------
    // Overrided
    //------------------------------

    /**
     * @param $id
     *
     * @return int
     */
    protected function issetPk($id)
    {
        return $this->createResultValue("SELECT ID FROM $this->tableName WHERE ID = ?", array($id));
    }


    public function wherePk()
    {
        return '';
    }


}