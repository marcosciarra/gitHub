<?php

namespace Matrix;

use Matrix\Exception\MatrixConnectionException;
use Matrix\Exception\MatrixException;

interface MatrixDbConnectorInterface
{


    /*CONSTANT*/
    const FETCH_ASSOC  = 1;
    const FETCH_NUM    = 2;
    const FETCH_VALUE  = 3;
    const FETCH_COLUMN = 3;
    const FETCH_SINGLE = 4;


    /**
     * MatrixDbConnector constructor.
     *
     * @param  $conn
     *
     * @throws MatrixException
     * @throws MatrixConnectionException
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
     * @throws MatrixException
     * @throws MatrixConnectionException
     */
    public static function connectStatic ();

    /**
     * Insert data into table
     *
     * @param $strTabella
     * @param $arrayKey
     *
     * @return int last insert id
     * @throws MatrixConnectionException
     * @throws MatrixException
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
     * @throws MatrixConnectionException
     * @throws MatrixException
     *
     * @deprecated
     */
    public function updateKeyArrayPdo ($table, $values, $idValue, $strIdColumn = 'ID');


    /**
     * Exec Insert data on DB and set id
     *
     * @param $arrayValori
     *
     * @throws \Matrix\Exception\MatrixConnectionException
     * @throws \Matrix\Exception\MatrixException
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
     * @throws MatrixConnectionException
     * @throws MatrixException
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