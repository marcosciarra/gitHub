<?php
//require_once 'PdaInterfaceModel.php';

/**
 * User: Claudio COLOMBO - P.D.A. Srl
 * Date: <date>
 * Version 0.0.1
 */

namespace Drakkar;

require_once 'DrakkarDbConnector.php';

abstract class _PdaAbstractModel //implements PdaInterfaceModel
{

    private $logger;

    // Fetch Constant
    const FETCH_OBJ = 1;
    const FETCH_JSON = 2;
    const FETCH_KEYARRAY = 3;
    const FETCH_NUMARRAY = 4;
    const FETCH_XML = 5;
    const FETCH_KEYVALUEARRAY = 6;

    // Like String Constant
    const LIKE_MATCHING_LEFT = 0;
    const LIKE_MATCHING_RIGHT = 1;
    const LIKE_MATCHING_BOTH = 2;
    const LIKE_MATCHING_PATTERN = 3;

    // Encode/Decode String
    const STR_NORMAL = 0;
    const STR_UTF8 = 1;

    // Default Encode/Decode String
    const STR_DEFAULT = 1;


    /** @var  PDO */
    protected $conn;

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
    /**
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

        \Logger::configure('/var/www/html/conf/log.properties');
        $this->logger = \Logger::getLogger('PdaAbstractModel');
    }

    /**
     * Salvataggio dell'oggetto
     *
     * @param bool $forzaInsert se true salva l'oggetto settando anche la chiave primaria
     *
     * @return int
     */
    public function saveOrUpdate($forzaInsert = false)
    {
        if ($forzaInsert) {
            return $this->saveKeyArray(null, true);
        } else {
            return $this->saveKeyArray();
        }
    }

    /**
     * Funzione per la truncate della tabella
     *
     * @return  boolean
     */
    public final function truncateTable()
    {
        return $this->conn->exec('TRUNCATE TABLE ' . $this->nomeTabella);
    }

    /**
     * Funzione per la cancellazione di tutto il contenuto della tabella
     *
     * @return  boolean
     */
    public final function deleteTable()
    {
        return $this->conn->exec('DELETE FROM  ' . $this->nomeTabella);
    }

    /**
     * Funzione per la truncate della tabella
     *
     * @param PDO $conn
     *
     * @return  boolean
     */
    public final static function truncateTableStatic($conn)
    {
        return $conn->exec('TRUNCATE TABLE ' . self::getNomeTabella());
    }

    /**
     * Funzione per la cancellazione di tutto il contenuto della tabella
     *
     * @param PDO $conn
     *
     * @return  boolean
     */
    public final static function deleteTableStatic($conn)
    {
        return $conn->exec('DELETE FROM  ' . self::getNomeTabella());
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
     * @param bool $boolKey ser settato a true forza il salvataggio del nuovo recor anche se è presnete la chiave
     *                       primaria
     *
     * @return null|string
     */
    public function saveKeyArray($arrayValori = null, $boolKey = false)
        {
            if (!$arrayValori) $arrayValori = $this->createKeyArray();


            if ($boolKey) {
                $this->logger->debug('INSERT FORZATO');
                $this->id = $this->conn->insertKeyArray($this->nomeTabella, $arrayValori);
              /*  $id = $this->issetPk($this->id);
                if ($id) {
                    $this->id = $id;
                }
                else {
                    $this->id = $this->conn->insertKeyArray($this->nomeTabella, $arrayValori);
                }*/
            }
            else if ($this->checkPk()) {
                $this->logger->debug('UPDATE');
                if($this->flagMultiKey){
                    $this->conn->updateKeyArray($this->nomeTabella, $arrayValori, $this->getArrayPk(), $this->wherePK());
                }else{
                    $this->conn->updateKeyArray($this->nomeTabella, $arrayValori, $this->id);
                }
            }
            else {
                $this->logger->debug('INSERT');
                $this->id = $this->conn->insertKeyArray($this->nomeTabella, $arrayValori);
            }


            return $this->id;
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

            $this->logger->error("createResultValue: $e->getMessage() ");
            return null;
        }
    }

    /**
     * @param string $query
     * @param null $parametri
     * @param int $tipoRisultato
     * @param int $encodeType
     *
     * @return array|string
     */
    protected function createResultArray($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ, $encodeType = self::STR_DEFAULT)
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
                        $arrayObj[$valori[0]] = $this->utf8EncodeString($valori[1]);
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

            $this->logger->error("createResultArray: $e->getMessage() ");
            return null;
        }
    }

    /**
     * @param $query
     * @param null $parametri
     * @param int $tipoRisultato
     * @param int $encodeType
     *
     * @return array|null|string
     */
    protected function createResult($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ, $encodeType = self::STR_DEFAULT)
    {
        try {
            if ($tipoRisultato == self::FETCH_NUMARRAY) {
                $valori = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_NUM);
            } else {
                $valori = $this->conn->execQuery($query, $parametri, DrakkarDbConnector::FETCH_SINGLE);

            }

            if (!$valori) {
                return;
            }

            switch ($tipoRisultato) {
                case self::FETCH_OBJ:
                    foreach ($valori as $chiave => $valore) {
                        $variabile = $this->creaNomeVariabile($chiave);
                        $this->$variabile = $this->encodeString($valore, $encodeType);
                    }
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

            $this->logger->error("createResult: $e->getMessage() ");
            return null;
        }
    }

    /**
     * Dato un oggetto Json istanzia la classe e la popola con i valori
     *
     * @param $json
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

    /**
     * @param $input
     * @param $typeEncode
     *
     * @return string
     */
    public function encodeString($input, $typeEncode)
    {
        if (is_string($input))
            switch ($typeEncode) {
                case self::STR_UTF8:
                    return utf8_encode($input);
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
     * @return string
     */
    public function decodeString($input, $typeEncode)
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
    public function encodeArray($input, $typeEncode)
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
    public function decodeArray($input, $typeEncode)
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
     * @param $input
     * @param $typeEncode
     *
     * @return string|int
     */
    public function dencodeString($input, $typeEncode)
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
     * @param string $string search string
     * @param int $likeMatching pattern for like matching
     */
    public function prepareLikeMatching($string, $likeMatching)
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


    public function createLimitQuery($limit = -1, $offset = -1)
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

//------------------------------
// Getter & Setter
//------------------------------

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->conn;
    }

    /**
     * @param PDO $conn
     */
    public function setPdo($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return mixed
     */
    public function getWhereBase()
    {
        return $this->whereBase;
    }

    /**
     * @param mixed $whereBase
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
     */
    public function getNomeTabella()
    {
        return $this->nomeTabella;
    }

    /**
     * @param string $nomeTabella
     */
    public function setNomeTabella($nomeTabella)
    {
        $this->nomeTabella = $nomeTabella;
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
// public function setOffsetBase(Integer $offsetBase)
    public function setOffsetBase($offsetBase)
    {
        $this->offsetBase = $offsetBase;
    }

//------------------------------
// Abstract
//------------------------------
    /**
     * Transforms the object into a key array
     *
     * @return array
     */
    public abstract function createKeyArray();

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
    private function issetPk($id)
    {
        return $this->createResultValue("SELECT ID FROM $this->tableName WHERE ID = ?", array($id));
    }

    public function checkPk()
    {
        return ($this->id) ? true : false;
    }

    public function wherePk(){
        return '';
    }

    protected $flagMultiKey = false;

    //------------------------------
    // Deprecation
    //------------------------------

    /**
     * Dead on Drakkar 1.0.0
     *
     * @param $keyArray
     *
     * @since      0.0.2
     * @deprecated 0.0.3
     */
    private function findKeyByPk($id)
    {
        return $this->issetPk($id);
    }


    /**
     * Dead on Drakkar 1.0.0
     *
     * @param $keyArray
     *
     * @since      0.0.2
     * @deprecated 0.0.3
     */
    public function creaObjKeyArray($keyArray)
    {
        $this->createObjKeyArray($keyArray);
    }

    /**
     * Dead on Drakkar 1.0.0
     *
     * @since      0.0.2
     * @deprecated 0.0.3
     */
    public function getElencoCampi()
    {
        $this->getListColumns();
    }


    /**
     * Dead on Drakkar 1.0.0
     *
     * @param $query
     * @param null $parametri
     * @param int $tipoRisultato
     *
     * @return array|object|string
     *
     * @since      0.0.2
     * @deprecated 0.0.3
     */
    public function creaRisultato($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ)
    {
        return $this->createResult($query, $parametri, $tipoRisultato);
    }

    /**
     * Dead on Drakkar 1.0.0
     * funzione per la creazione del keyArray dall'oggetto
     *
     * @return array
     *
     * @since      0.0.2
     * @deprecated 0.0.3
     */
    public function creaKeyArray()
    {
        return $this->createKeyArray();
    }

    /**
     *Dead on Drakkar 1.0.0
     *
     * @param $query
     * @param array|null $parametri
     * @param int $tipoRisultato
     *
     * @return array|string|object
     *
     * @since      0.0.2
     * @deprecated 0.0.3
     */
    protected function creaRisultatoArray($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ)
    {
        return $this->createResultArray($query, $parametri, $tipoRisultato);
    }

    /**
     * Dead on Drakkar 1.0.0
     *
     * @param $arrayPosizionale
     *
     * @return mixed
     *
     * @since      0.0.2
     * @deprecated 0.0.3
     */
    public function creaKeyArrayDaPosizionale($arrayPosizionale)
    {
        return $this->createKeyArrayFromPositional($arrayPosizionale);
    }

}