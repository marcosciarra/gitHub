<?php


namespace Matrix;

require_once 'MatrixDbPdo.php';

class MatrixDbConnector
{
    /** @var MatrixDbPdo  */
    private $conn;

    /**
     * DrakkarDbConnector constructor.
     *
     * @param DrakkarDbConnector|null|int  $conn  Accetta in ingresso una connessione di tipo DrakkarDbConnector<br/>
     *                                            Nel caso si passi 0 oppure null si effettuerà la connessione sui parametri di default</br>
     *                                            Nel caso si passi un numero superiore a 1 si effettuerà la connessione sui parametri della connessione richiesta
     *
     * @throws DrakkarException
     * @throws DrakkarConnectionException
     */
    public function __construct ($conn = null) {
        if ($conn instanceof DrakkarDbConnector)
            $this->conn = $conn;
        else
            $this->conn = new MatrixDbPdo($conn);
    }

    /**
     * connet
     *
     * @param $numberConnection
     *
     * @return \PDO
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    public function connect ($numberConnection) {
        return $this->conn->connect($numberConnection);
    }

    /**
     * connet
     *
     * @return DrakkarDbConnector
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    public static function connectStatic () {
        try {
            $conn = new self();
        }
        catch (DrakkarConnectionException $e) {
            throw $e;
        }

        return $conn;
    }

    /**
     * Insert data into table
     *
     * @param $strTabella
     * @param $arrayKey
     *
     * @return int last insert id
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    public function insertKeyArray ($strTabella, $arrayKey) {
        return $this->conn->insertKeyArray($strTabella, $arrayKey);
    }

    /**
     * exec query on DB
     *
     * @param            $query
     * @param null|array $queryParameters
     * @param int        $typeReturn
     *
     * @return array|mixed|null
     */
    public function execQuery ($query, $queryParameters = null, $typeReturn = self::FETCH_ASSOC) {
        return $this->conn->execQuery($query, $queryParameters, $typeReturn);
    }

    /**
     * Update data
     *
     * @param string    $table
     * @param array     $values
     * @param int|array $idValue
     * @param string    $strIdColumn
     *
     * @return int update row count
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     *
     * @deprecated
     */
    public function updateKeyArrayPdo ($table, $values, $idValue, $strIdColumn = 'ID') {
        return $this->conn->updateKeyArray($table, $values, $idValue, $strIdColumn);

    }

    /**
     * Update data
     *
     * @param string    $table
     * @param array     $values
     * @param int|array $idValue
     * @param string    $strIdColumn
     *
     * @return int update row count
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    public function updateKeyArray ($table, $values, $idValue, $strIdColumn = 'ID') {
        return $this->conn->updateKeyArray($table, $values, $idValue, $strIdColumn);
    }

    public function beginTransaction () {
        $this->conn->beginTransaction();
    }

    public function commit () {
        $this->conn->commit();
    }

    public function rollBack () {
        $this->conn->rollBack();
    }

    /**
     * @return null
     */
    public function getConn () {
        return $this->conn->getConn();
    }

    /**
     * @param null $conn
     */
    public function setConn ($conn) {
        $this->conn->setConn($conn);
    }

    /**
     * Exec Insert data on DB and set id
     *
     * @param $arrayValori
     *
     * @throws \Drakkar\Exception\DrakkarConnectionException
     * @throws \Drakkar\Exception\DrakkarException
     */
    public function insertForcedKeyArray ($strTabella, $arrayKey) {
        return $this->conn->insertForcedKeyArray($strTabella, $arrayKey);
    }


    /**
     * @return string
     */
    public function getHost () {
        return $this->conn->getHost();
    }

    /**
     * @return string
     */
    public function getPort () {
        return $this->conn->getPort();
    }

    /**
     * @return string
     */
    public function getUser () {
        return $this->conn->getUser();
    }

    /**
     * @return string
     */
    public function getSkema () {
        return $this->conn->getSkema();
    }

    /**
     * @return string
     */
    public function getCharset () {
        return $this->conn->getCharset();
    }




}