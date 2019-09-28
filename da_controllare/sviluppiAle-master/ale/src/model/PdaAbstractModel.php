<?php
//require_once 'PdaInterfaceModel.php';
/**
 * User: Claudio COLOMBO - P.D.A. Srl
 * Date: 02/08/16
 * Version 1.0.4 C
 */



class PdaAbstractModel //implements PdaInterfaceModel
{

    // Costanti
    const FETCH_JSON     = 2;
    const FETCH_OBJ      = 1;
    const FETCH_KEYARRAY = 3;
    const FETCH_NUMARRAY = 4;
    const FETCH_XML      = 5;

    /** @var  PDO */
    protected $pdo;

    protected $nomeTabella;

    /** Condizioni Where aggiuntive per query base */
    protected $whereBase = "";
    protected $orderBase = "";
    /**
     *se settato esegue la limit con gli attributi passati<br/>
     *                           es: - Per ottenere i primi 10 elementi della query basterà passare '10'<br/>
     *                               - Per ottenere 10 elementi partendo dal terzo si passerà '3,10'
     *
     * @var string
     */
    protected $limitBase = "";

    /**
     * PdaAbstractModel constructor.
     *
     * @param PDO $pdo
     */
    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
        }
        else {
            return $this->saveKeyArray();
        }
    }

    /**
     * Funzione per la truncate della tabella
     *
     * @return  boolean
     */
    public function truncateTable()
    {
        return $this->pdo->exec('TRUNCATE TABLE ' . $this->nomeTabella);
    }

    /**
     * Funzione per la cancellazione di tutto il contenuto della tabella
     *
     * @return  boolean
     */
    public function deleteTable()
    {
        return $this->pdo->exec('DELETE FROM  ' . $this->nomeTabella);
    }

    /**
     * Funzione per la truncate della tabella
     *
     * @param PDO $pdo
     *
     * @return  boolean
     */
    public static function truncateTableStatic(PDO $pdo)
    {
        return $pdo->exec('TRUNCATE TABLE ' . self::getNomeTabella());
    }

    /**
     * Funzione per la cancellazione di tutto il contenuto della tabella
     *
     * @param PDO $pdo
     *
     * @return  boolean
     */
    public static function deleteTableStatic(PDO $pdo)
    {
        return $pdo->exec('DELETE FROM  ' . self::getNomeTabella());
    }

    /**
     * Ritorna il nome Java Stile di una variabile
     *
     * @param $variabile
     *
     * @return string
     */
    protected function creaNomeVariabile($variabile)
    {
        $variabile = ucwords(strtolower(str_replace("_", " ", $variabile)));
        $variabile = strtolower(substr($variabile, 0, 1)) . substr($variabile, 1, strlen($variabile));

        return str_replace(" ", "", $variabile);
    }


    /**
     * Salvataggio dell'oggetto da un array posizionale
     *
     * @param bool $forzaInsert se true salva l'oggetto settando anche la chiave primaria
     *
     * @return int
     */
    public function saveOrUpdatePosizionale($arrayPosizionale)
    {
        $arrayValori = $this->creaKeyArrayDaPosizionale($arrayPosizionale);

        return $this->saveKeyArray($arrayValori);
    }

    //TODO: Risolere problema update chiavi multiple
    /**
     * @param array $arrayValori
     * @param bool  $boolKey ser settato a true forza il salvataggio del nuovo recor anche se è presnete la chiave
     *                       primaria
     */
    public function saveKeyArray($arrayValori = null, $boolKey = false)
    {
        if (!$arrayValori) $arrayValori = $this->creaKeyArray();
        if ($boolKey) {
            $id = $this->findKeyByPk($this->id);
            if ($id) {
                $this->id = $id;
            }
            else {
                $this->id = insertKeyArrayPdo($this->pdo, $this->nomeTabella, $arrayValori);
            }
        }
        else if ($this->id) {
            updateKeyArrayPdo($this->pdo, $this->nomeTabella, $arrayValori, $this->id);
        }
        else {
            $this->id = insertKeyArrayPdo($this->pdo, $this->nomeTabella, $arrayValori);
        }

        return $this->id;
    }


    protected function creaRisultatoArray($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ)
    {
        $nomeClasse = get_class($this);
        $valoriPdo = queryPreparedPdo($this->pdo->prepare($query), $parametri, "p");
        $arrayObj = array();
        switch ($tipoRisultato) {
            case self::FETCH_OBJ:
                while ($valori = $valoriPdo->fetch(PDO::FETCH_ASSOC)) {
                    $app = new $nomeClasse($this->pdo);
                    $app->creaObjKeyArray($valori);
                    $arrayObj[] = $app;
                }
                $valoriPdo->closeCursor();

                return $arrayObj;
                break;
            case self::FETCH_JSON:
                while ($valori = $valoriPdo->fetch(PDO::FETCH_ASSOC)) {
                    $arrayObj[] = $valori;
                }
                $valoriPdo->closeCursor();

                return json_encode($arrayObj);
                break;
            case self::FETCH_KEYARRAY:
                while ($valori = $valoriPdo->fetch(PDO::FETCH_ASSOC)) {
                    $arrayObj[] = $valori;
                }
                $valoriPdo->closeCursor();

                return $arrayObj;
                break;
            case self::FETCH_NUMARRAY:
                while ($valori = $valoriPdo->fetch(PDO::FETCH_NUM)) {
                    $arrayObj[] = $valori;
                }
                $valoriPdo->closeCursor();

                return $arrayObj;
                break;
            case self::FETCH_XML:
                $xml = '<obj>';
                while ($valori = $valoriPdo->fetch(PDO::FETCH_ASSOC)) {
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


    }

    protected function creaRisultato($query, $parametri = null, $tipoRisultato = self::FETCH_OBJ)
    {
        $valori = queryPreparedPdo($this->pdo->prepare($query), $parametri, "f");

        if (!$valori) {
            return;
        }

        switch ($tipoRisultato) {
            case self::FETCH_OBJ:
                foreach ($valori as $chiave => $valore) {
                    $variabile = $this->creaNomeVariabile($chiave);
                    $this->$variabile = $valore;
                }
                break;
            case self::FETCH_JSON:
                return json_encode($valori);
                break;
            case self::FETCH_KEYARRAY:
                return $valori;
                break;
            case self::FETCH_NUMARRAY:
                //TODO: Da istemare
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
    }
    

    public function creaObjJson($json, $flgObjJson = false)
    {
        if ($flgObjJson) {
            $json = json_encode($json);
        }
        $json = json_decode($json, true);
        $this->creaObjKeyArray($json);
    }

    public function getEmptyJson()
    {
        return json_encode(get_object_vars($this));
    }

    public function getEmptyKeyArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param PDO $pdo
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
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
     * @return string
     */
    public function getLimitBase()
    {
        return $this->limitBase;
    }

    /**
     * @param string $limitBase
     */
    public function setLimitBase($limitBase)
    {
        $this->limitBase = $limitBase;
    }
    
    //------------------------------------------------------------------ABSTRACT
    
    /**
     * @param string $query
     * @param array|null parameters
     * @return int|string
     */
    protected function createResultValue($query, $parameters = null){
        
        return queryPreparedPdo($this->pdo,$this->pdo->prepare($query), $parameters, "v");
    }
    /**
     * @param $query
     * @param null $parametri
     */
    protected function createResult($query, $parametri = null){
        
        $valori = queryPreparedPdo($this->pdo,$this->pdo->prepare($query), $parametri, "f");
        if (!$valori) {
            return;
        }
        return $this->utf8EncodeArray($valori);
    }
    
     /**
     * @param string $query
     * @param null $parametri
     */
    protected function createResultArray($query, $parametri = null){

        $valoriPdo = queryPreparedPdo($this->pdo,$this->pdo->prepare($query), $parametri, "p");
        $arrayObj = array();
        while ($valori = $valoriPdo->fetch(PDO::FETCH_ASSOC)) {
            $arrayObj[] = $this->utf8EncodeArray($valori);
        }
        $valoriPdo->closeCursor();

        return $arrayObj;   
    }

    
    //------------------------------------------------------------------ENCODING
    
    public function utf8EncodeString($input) {
        if (is_string($input)){
           return utf8_encode($input);
        }else{
            return $input;
        }
    }

    public function utf8EncodeArray($input){
        if (is_array($input)) {
            $app = array();
            foreach ($input as $key=>$value) {
               $app[$key]=$this->utf8EncodeString($value);
            }
            return $app;
        }else{
            return $input;
        }
    }	

}