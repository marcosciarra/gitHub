<?php


namespace Matrix;


class MatrixDbPdo
{
    protected $conn;

    protected $host;
    protected $port;
    protected $user;
    protected $skema;
    protected $charset;


    /**
     * MatrixDbPdo constructor.
     *
     * @param $conn
     *
     */
    public function __construct($conn = null)
    {
        try {
            $this->connect($conn);
        } catch (DrakkarConnectionException $e) {
            throw $e;
        }
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
    public function connect($numberConnection = 0)
    {
        if ($numberConnection) {
            $this->host = constant("HOST" . $numberConnection);
            $this->port = constant("PORT" . $numberConnection);
            $this->user = constant("USER" . $numberConnection);
            $password = constant("PWD" . $numberConnection);
            $this->skema = constant("SCHEMA" . $numberConnection);
            $this->charset = constant("CHARSET" . $numberConnection);
        } else {
            $this->host = constant("HOST");
            $this->port = constant("PORT" );
            $this->user = constant("USER");
            $password = constant("PWD");
            $this->skema = constant("SCHEMA");
            $this->charset = constant("CHARSET");
        }

        try {
            //todo da gestire se pdo verso altro tipo di db non mysql/mariadb
            $attribute = array(\PDO::ATTR_EMULATE_PREPARES => false, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
            $this->conn =
                new \PDO('mysql:host=' . $this->host
                    . ';dbname=' . $this->skema
                    .';port='.$this->port
                    . ';charset=' . $this->charset, $this->user, $password, $attribute);
        } catch (\PDOException $e) {
            if (in_array($e->getCode(), array(1042, 1044, 1045, 1046, 1049, 1053, 2002, 2003))) {
                throw new DrakkarConnectionException('DKR-CON-001', $e);
            } else {
                throw new DrakkarException('DKR-CON-000', $e);
            }
        }

        return $this->conn;
    }

    /**
     * connet
     *
     * @return MatrixDbPdo
     * @throws DrakkarException
     * @throws DrakkarConnectionException
     */
    public static function connectStatic()
    {
        try {
            $conn = new self();
        } catch (DrakkarConnectionException $e) {
            throw $e;
        }

        return $conn;
    }


    /**
     * @param null|object $e
     * @param string $query
     * @param array $arrayKey
     *
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    protected function gestioneEccezioni($e = null, $query = '', $arrayKey = [])
    {
        if (is_null($e)) {
            throw new DrakkarException('DKR-QRY-000', null, '', $query, $arrayKey);
        }

        switch ($e->getCode()) {
            case 1046:
                throw new DrakkarConnectionException('DKR-QRY-001', $e);
                break;
            case 1062:
                throw new DrakkarDuplicatePrimaryException('DKR-QRY-004', $e, '', $query, $arrayKey);
                break;
            case 23000:
                throw new DrakkarIntegrityConstrainException('DKR-QRY-003', $e, '', $query, $arrayKey);
                break;
            default:
                throw new DrakkarException('DKR-QRY-000', $e, '', $query, $arrayKey);

        }
    }

    /**
     *
     * @param      $query
     * @param null $queryParameters
     * @param int $typeReturn
     *
     * @return array|mixed|null
     */
    public function execQuery($query, $queryParameters = null, $typeReturn = self::FETCH_ASSOC)
    {
        /** @var $queryHandler \PDOStatement */
        $queryHandler =
            ($query instanceof \PDOStatement) ? $query : $this->conn->prepare($query);

        if (!is_null($queryParameters) and !is_array($queryParameters)) {
            $queryParameters = array($queryParameters);
        }

        $queryHandler->execute($queryParameters);

        $result = null;
        switch ($typeReturn) {
            case self::FETCH_ASSOC:
                $result = array();
                while ($row = $queryHandler->fetch(\PDO::FETCH_ASSOC)) {
                    $result[] = $row;
                }
                break;
            case self::FETCH_SINGLE:
                $result = $queryHandler->fetch(\PDO::FETCH_ASSOC);
                break;
            case self::FETCH_NUM:
                $result = array();
                while ($row = $queryHandler->fetch(\PDO::FETCH_NUM)) {
                    $result[] = $row;
                }
                break;
            case self::FETCH_VALUE:
            case self::FETCH_COLUMN:
                $result = $queryHandler->fetch(\PDO::FETCH_COLUMN);
                break;
        }
        $queryHandler->closeCursor();

        return $result;

    }

    /**
     * Create Sql Query insert
     *
     * @param $strTabella
     * @param $arrayKey
     *
     * @return string
     */
    private function createQueryInsert($strTabella, $arrayKey)
    {
        $arrayCampi = array();
        $arrayPosizioni = array();
        $query = '';
        foreach ($arrayKey as $key => $value) {
            //            if (null === $value or !$value) {
            //                unset($arrayKey[$key]);
            //                continue;
            //            }
            $arrayCampi[] = $key;
            $arrayPosizioni[] = ':' . $key;
        }
        $query = 'INSERT INTO ' .
            $strTabella .
            ' (' .
            implode(',', $arrayCampi) .
            ') VALUES (' .
            implode(',', $arrayPosizioni) .
            ')';

        return $query;
    }

    /**
     * Insert data into table
     *
     * @param $strTabella
     * @param $arrayKey $idValue
     *
     * @return int last insert id
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    public function insertKeyArray($strTabella, $arrayKey)
    {
        return $this->execSaveOrUpdate($this->createQueryInsert($strTabella, $arrayKey), $arrayKey, 1);
    }

    /**
     * Create SQL Query Update
     *
     * @param string $table
     * @param array $values
     * @param string|array $idValue
     * @param string|int|array $strIdColumn
     * @param bool $noWhere
     *
     * @return string
     */
    private function createQueryUpdate($table, $values, $idValue, $strIdColumn, $forcedUpdate = false)
    {
        $query = '';
        $bindValues = [];

        //var_dump($values);

        foreach ($values as $key => $value) {
            if ($forcedUpdate)
                $bindValues[] = $key . '=:' . $key . '_DKRUPDATE';
            else
                $bindValues[] = $key . '=:' . $key;

        }

        if ($forcedUpdate)
            return 'UPDATE ' . implode(',', $bindValues);
        else
            $query = 'UPDATE ' . $table . ' SET ' . implode(',', $bindValues);

        //aggiungo all'associativo il valore della chiave di where
        if (is_array($idValue)) {
            for ($i = 0; $i < count($idValue); $i++) {
                $k = 'ID__COLUMN' . $i;
                $values[$k] = $idValue[$i];
            }
            $query .= ' WHERE ' . $strIdColumn;
        } else {
            $values['ID__COLUMN'] = $idValue;
            //preparo la query
            $query .= ' WHERE ' . $strIdColumn . '=:ID__COLUMN';

        }

        return $query;
    }

    /**
     * Update data
     *
     * @param string $table
     * @param array $values
     * @param int|array $idValue
     * @param string $strIdColumn
     *
     * @return int update row count
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     *
     * @deprecated
     */
    public function updateKeyArrayPdo($table, $values, $idValue, $strIdColumn = 'ID')
    {
        return $this->updateKeyArray($table, $values, $idValue, $strIdColumn);
    }

    /**
     * Update data
     *
     * @param string $table
     * @param array $values
     * @param int|array $idValue
     * @param string $strIdColumn
     *
     * @return int update row count
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    public function updateKeyArray($table, $values, $idValue, $strIdColumn = 'id')
    {
        $values2 = array_merge($values, ['ID__COLUMN' => $idValue]);
        return $this->execSaveOrUpdate($this->createQueryUpdate($table, $values, $idValue, $strIdColumn), $values2, 2);
    }

    /**
     * Insert/Update data into table
     *
     * @param $strTabella
     * @param $arrayKey
     *
     * @return int last insert id
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    public function insertForcedKeyArray($strTabella, $arrayKey)
    {

        $arrayQuery = $arrayKey;
        $arrayUpdate = [];
        foreach ($arrayKey as $key => $value) {
            $arrayQuery[$key . '_DKRUPDATE'] = $value;
        }

        $query = $this->createQueryInsert($strTabella, $arrayKey)
            . ' ON DUPLICATE KEY '
            . $this->createQueryUpdate($strTabella, $arrayKey, '', 0, true);

        return $this->execSaveOrUpdate($query, $arrayQuery, 3);
    }

    protected function logArray($array)
    {
        $log = '';
        foreach ($array as $key => $value) {
            $log .= $key . '=>' . $value . '||';
        }
        error_log($log, 0);
    }

    /**
     *
     * @param string $query
     * @param array $values
     * @param int $type
     *
     * @return bool|string
     * @throws DrakkarConnectionException
     * @throws DrakkarException
     */
    private function execSaveOrUpdate($query, $values, $type)
    {

        // error_log($query, 0);
        //$this->logArray($values);

        try {
            $update = $this->conn->prepare($query);
            if ($update->execute($values)) {
                $id = $this->conn->lastInsertId();
                //error_log("ID => " . $id, 0);
                return $id;
                //                if ($type == 1) {
                //                    return $this->conn->lastInsertId();
                //                } else {
                //                    return 'OK';
                //                }
            } else {
                throw new DrakkarException('DKR-QRY-00' . $type, null, '', $query, $values);
            }
        } catch (\PDOException $e) {
            $this->gestioneEccezioni($e, $query, $values);
        } catch (\Exception $e) {
            throw new DrakkarException('DKR-PHP-000', $e);
        }
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        $this->conn->commit();
    }

    /**
     * Roll-back transaction
     */
    public function rollBack()
    {
        $this->conn->rollBack();
    }

    /**
     * @return \PDO
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @param \PDO $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getSkema()
    {
        return $this->skema;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }


}