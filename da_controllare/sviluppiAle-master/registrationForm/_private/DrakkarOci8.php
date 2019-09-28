<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 27/07/17
 * Time: 23.09
 */

namespace Drakkar;

class DrakkarOci8
{
    private $logger;

    protected $conn;

    /*CONSTANT*/
    const FETCH_ASSOC  = 1;
    const FETCH_NUM    = 2;
    const FETCH_VALUE  = 3;
    const FETCH_COLUMN  = 3;
    const FETCH_SINGLE = 4;

    /**
     * DrakkarOci8 constructor.
     *
     * @param $conn
     */
    public function __construct ($conn = null) {
        if (is_null($conn)) $this->connect();
        elseif ($conn instanceof DrakkarOci8) $this->conn = $conn->getConn();
        else
            $this->conn = $conn;

        //logger
        \Logger::configure('/var/www/html/conf/log.properties');
        $this->logger = \Logger::getLogger('DrakkarOci8');

    }

    public function connect () {

        /* PRODUZIONE */
        $this->conn = oci_connect(USER, PWD, HOST . ':' . PORT . '/' . ORACLE_SID);

        if (!$this->conn) {
            $e = oci_error();
            $this->logger->error('ERRORE CONNESSIONE');
            $this->logger->error($e['message']);

            return htmlentities($e['message'], ENT_QUOTES);
        }

        return $this->conn;
    }

    public static function connectStatic () {
        $app = new self();

        return $app;
    }

    /**
     * Prepare and binding query
     *
     * @param            $query
     * @param array|null $elements
     *
     * @return resource a statement handle on success, or <b>FALSE</b> on error
     *
     */
    public function prepareAndBindingQuery ($query, $elements = null) {
        $ociParse = oci_parse($this->conn, $query);

        if ($elements == null) return $ociParse;

        if (is_array($elements)) {

            //Verify if array is associative
            if (array_keys($elements) === range(0, count($elements) - 1)) {
                $elementsApp = array();
                //If isn't associative prepare the array
                for ($i = 0; $i < count($elements); $i++) {
                    $k = 'P' . $i;
                    $elementsApp[$k] = $elements[$i];
                }
                $elements = $elementsApp;
            }
            //Query binding
            foreach ($elements as $key => $val) {
                $this->logger->debug("$key => $val");
                // oci_bind_by_name($stid, $key, $val) does not work
                // because it binds each placeholder to the same location: $val
                // instead use the actual location of the data: $ba[$key]
                oci_bind_by_name($ociParse, ':' . $key, $elements[$key]);
            }

        }
        else
            //Binding only one element
            oci_bind_by_name($ociParse, ":P0", $elements);

        return $ociParse;
    }

    /**
     * Exec the query
     *
     * @param            $query
     * @param array|null $elements
     * @param int        $typeReturn
     *
     * @return array|null
     */
    public function execQuery ($query, $elements = null, $typeReturn = self::FETCH_ASSOC) {
        $this->logger->info("ExecQuery: $query");
        $statement = $this->prepareAndBindingQuery($query, $elements);
        $ck = oci_execute($statement);

        if (!$ck) return null;

        $arrayApp = Array();
        //OCI_NUM
        switch ($typeReturn) {
            case self::FETCH_ASSOC:
                while ($row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    $arrayApp[] = $row;
                }

                return $arrayApp;
                break;
            case self::FETCH_SINGLE:
                return oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_NULLS);
                break;
            case self::FETCH_NUM:
                while ($row = oci_fetch_array($statement, OCI_NUM + OCI_RETURN_NULLS)) {
                    $arrayApp[] = $row;
                }

                return $arrayApp;
                break;
            case self::FETCH_VALUE:
                $row = oci_fetch_array($statement, OCI_NUM + OCI_RETURN_NULLS);

                return $row[0];
                break;
            default:
                return null;
        }
    }


    public function insertKeyArray ($table, $values) {
        //creo array associativo per insert
        foreach ($values as $key => $value) {
            $keys[] = $key;
            $bindValues[] = ':' . $key;
        }

        //eseguo la query
        $query = 'INSERT INTO ' . $table . ' (' . implode(',', $keys) . ') VALUES (' . implode(',', $bindValues) . ')';
        $this->logger->info("Query di insert: $query");
        $statement = $this->prepareAndBindingQuery($query, $values);
        oci_execute($statement);
    }

    public function updateKeyArray ($table, $values, $idValue, $strIdColumn = 'ID') {
        //creo array associativo per update
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
        $statement = $this->prepareAndBindingQuery($query, $values);

        //eseguo la query
        oci_execute($statement);
    }


    public function beginTransaction () {
    }

    public function commit () {
    }

    public function rollBack () {
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