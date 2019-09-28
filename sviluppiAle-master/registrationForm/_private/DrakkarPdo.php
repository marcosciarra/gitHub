<?php
/**
 * TODO Description
 *
 * User: Claudio COLOMBO - P.D.A. Srl
 * Creation: 03/11/17
 */

namespace Drakkar;


class _DrakkarPdo
{
    private $logger;

    /** @var \PDO */
    protected $conn;

    /*CONSTANT*/
    const FETCH_ASSOC  = 1;
    const FETCH_NUM    = 2;
    const FETCH_VALUE  = 3;
    const FETCH_SINGLE = 4;


    /**
     * DrakkarPdo constructor.
     *
     * @param $conn
     */
    public function __construct ($conn = null) {
        if (is_null($conn)) $this->connect();
        elseif ($conn instanceof DrakkarPdo) $this->conn = $conn->getConn();
        else
            $this->conn = $conn;

        //logger
        \Logger::configure('conf/log.properties');
        $this->logger = \Logger::getLogger('DrakkarPdo');

    }


    public function connect () {
        try {
            //todo da gestire se pdo verso altro tipo di db non mysql/mariadb
            $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
            $this->conn =
                new PDO('mysql:host=' . HOST . ';dbname=' . DB_NAME . ';charset=' . CHARSET, USER, PWD, $attribute);
        }
        catch (PDOException $e) {
            $this->logger->error('ERRORE CONNESSIONE');
            $this->logger->error($e->getMessage());

            return false; //gestione errore connessione
        }

        return $this->conn;
    }

    public static function connectStatic () {
        $app = new self();

        return $app;
    }


    public function insertKeyArray ($strTabella, $arrayKey) {
        $arrayCampi = array();
        $arrayPosizioni = array();
        foreach ($arrayKey as $key => $value) {
            if (null === $value or !$value) {
                unset($arrayKey[$key]);
                continue;
            }
            $arrayCampi[] = $key;
            $arrayPosizioni[] = ':' . $key;
        }
        $insert =
            $this->conn->prepare('INSERT INTO ' .
                                 $strTabella .
                                 ' (' .
                                 implode(',', $arrayCampi) .
                                 ') VALUES (' .
                                 implode(',', $arrayPosizioni) .
                                 ')');
        $insert->execute($arrayKey);

        return $this->conn->lastInsertId();
    }


    public function updateKeyArrayPdo ($table, $values, $idValue, $strIdColumn = 'ID') {
        foreach ($values as $key => $value) {
            $bindValues[] = $key . '=:' . $key;
        }
        //aggiungo all'associativo il valore della chiave di where
        if (is_array($idValue)) {

            for ($i = 0; $i < count($idValue); $i++) {
                $k = 'ID__COLUMN' . $i;
                $values[$k] = $idValue[$i];
            }
            //preparo la query
            $query = 'UPDATE ' . $table . ' SET ' . implode(',', $bindValues) . ' WHERE ' . $strIdColumn;

        }
        else {
            $values['ID__COLUMN'] = $idValue;
            //preparo la query
            $query =
                'UPDATE ' . $table . ' SET ' . implode(',', $bindValues) . ' WHERE ' . $strIdColumn . '=:ID__COLUMN';

        }

        $this->logger->info("Query di update: $query");
        $update=$this->conn->prepare($query);
        $update->execute($values);

        return $update->rowCount();

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
        return $this->conn;
    }

    /**
     * @param null $conn
     */
    public function setConn ($conn) {
        $this->conn = $conn;
    }


}