<?php
/**
 * TODO Description
 *
 * User: Claudio COLOMBO - P.D.A. Srl
 * Creation: 10/11/17
 */

namespace Drakkar;


use Drakkar\Exception\DrakkarConnectionException;
use Drakkar\Exception\DrakkarException;

interface DrakkarDbConnectorInterface
{


    /*CONSTANT*/
    const FETCH_ASSOC  = 1;
    const FETCH_NUM    = 2;
    const FETCH_VALUE  = 3;
    const FETCH_COLUMN = 3;
    const FETCH_SINGLE = 4;


    /**
     * DrakkarDbConnector constructor.
     *
     * @param  $conn
     *
     * @throws DrakkarException
     * @throws DrakkarConnectionException
     */
    public function __construct ($conn = null);

    /**
     * connet
     *
     * @param $numberConnection
     *
     * @return \PDO
     */
    public function connect ($numberConnection);

    /**
     * connet
     *
     * @return \PDO
     * @throws DrakkarException
     * @throws DrakkarConnectionException
     */
    public static function connectStatic ();

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
    public function insertKeyArray ($strTabella, $arrayKey);

    /**
     * exec query on DB
     *
     * @param            $query
     * @param null|array $queryParameters
     * @param int        $typeReturn
     *
     * @return array|mixed|null
     */
    public function execQuery ($query, $queryParameters = null, $typeReturn = self::FETCH_ASSOC);

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
    public function updateKeyArrayPdo ($table, $values, $idValue, $strIdColumn = 'ID');


    /**
     * Exec Insert data on DB and set id
     *
     * @param $arrayValori
     *
     * @throws \Drakkar\Exception\DrakkarConnectionException
     * @throws \Drakkar\Exception\DrakkarException
     */
    public function insertForcedKeyArray ($strTabella, $arrayKey);

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
    public function updateKeyArray ($table, $values, $idValue, $strIdColumn = 'ID');

    public function beginTransaction ();

    public function commit ();

    public function rollBack ();

    //function gestioneEccezioni($e);

    /**
     * @return null
     */
    public function getConn ();

    /**
     * @param null $conn
     */
    public function setConn ($conn);


}