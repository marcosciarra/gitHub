<?php

/**
* Created by Drakkar vers. 0.1.3(Hjortspring)
* User: P.D.A. Srl
* Date: 2018-07-23
* Time: 13:25:12.598434
*/


namespace Click\Affitti\TblBase;
		interface PdaInterfaceModel
	{

		/*CONST*/

		// Fetch Constant
		/**
		 * Returns a new instance, or an array of instances, of the requested class.<br/>
		 * If the result of the query is Null, it will return an empty instance or an empty array.
		 */
		const FETCH_OBJ = 1;

		/**
		 * Returns a Json object of the requested class.<br/>
		 * If the result of the query is Null, it will return Null.
		 */
		const FETCH_JSON = 2;

		/**
		 * Returns an array indexed by column name as returned in your result set.<br/>
		 * It is the same as FETCH_ASSOC
		 */
		const FETCH_KEYARRAY = 3;

		/**
		 * Returns an array indexed by column name as returned in your result set.<br/>
		 * It is the same as FETCH_KEYARRAY
		 */
		const FETCH_ASSOC = 3;

		/**
		 * Returns an array indexed by column number as returned in your result set, starting at column 0<br/>
		 * It is the same as FETCH_NUM
		 */
		const FETCH_NUMARRAY = 4;

		/**
		 * Returns an array indexed by column number as returned in your result set, starting at column 0<br/>
		 * It is the same as FETCH_NUMARRAY
		 */
		const FETCH_NUM = 4;

		/**
		 * DON'T USE!!!!!<br/><br/>
		 * Returns a Xml String of the requested class.<br/>
		 * If the result of the query is Null, it will return empty string.
		 */
		const FETCH_XML = 5;

		/**
		 * Returns an array indexed by value of column 0 and as a value the value of column 1.<br/>
		 * Example: result {"column0" => "column1"}
		 */
		const FETCH_KEYVALUEARRAY = 6;

		const FETCH_VALUEARRAY = 7;

		// Like String Constant
		const LIKE_MATCHING_LEFT = 0;
		const LIKE_MATCHING_RIGHT = 1;
		const LIKE_MATCHING_BOTH = 2;
		const LIKE_MATCHING_PATTERN = 3;

		// Encode/Decode String
		const STR_NORMAL = 0;
		const STR_UTF8 = 1;
		const STR_CP1252 = 2;
		const STR_WINDOWS_1252 = 2;
		//const STR_ASCII = 3;
		//const STR_ISO8859_1 = 4;
		//const STR_ISO8859_15 = 5;
		//const STR_ISO8859_6 = 6;
		//const STR_CP1256 = 7;

        // NULL VAL
        const NULL_VALUE = '';


		/*METHOD*/

		/**
		 * Save the object into database
		 *
		 * @param bool $forcedInsert if true, save the object using the primary key that has been enhanced
		 *
		 * @return int|null|string
		 */
		public function saveOrUpdate($forcedInsert);

		/**
		 * Save the object into database and save the operation log
		 *
		 * @param int $idUser
		 * @param bool $forcedInsert if true, save the object using the primary key that has been enhanced
		 *
		 * @return int|null|string
		 */
		public function saveOrUpdateAndLog($idUser, $forcedInsert);

		/**
		 * Truncate table's class
		 *
		 * @return  boolean
		 */
		public function truncateTable();

		/**
		 * Truncate table's class
		 *
		 * @param DrakkarDbConnector $conn
		 *
		 * @return  boolean
		 */
		public static function truncateTableStatic($conn);

		/**
		 * Delete all data
		 *
		 * @return  boolean
		 */
		public function deleteTable();
		/**
		 * Delete all data
		 * @param $idUser
		 * @return  boolean
		 */
		public function deleteTableAndLog($idUser);

		/**
		 * Funzione per la cancellazione di tutto il contenuto della tabella
		 *
		 * @param DrakkarDbConnector $conn
		 *
		 * @return  boolean
		 */
		public static function deleteTableStatic($conn);

		/**
		 * @return bool
		 */
		public function checkPk();

		/**
		 * Exec Insert data on DB and set id
		 *
		 * @param $arrayValori
		 *
		 * @throws \Drakkar\Exception\DrakkarConnectionException
		 * @throws \Drakkar\Exception\DrakkarException
		 */
		public function insertDb($arrayValori);

		/**
		 * Dato un oggetto Json istanzia la classe e la popola con i valori
		 *
		 * @param      $json
		 * @param bool $flgObjJson
		 */
		public function creaObjJson($json, $flgObjJson = false);

		/**
		 * Restituisce la rappresentazione della classe in formato Json
		 *
		 * @return string
		 */
		public function getEmptyObjJson();

		public function getEmptyDbJson();

		/**
		 * Restituisce la rappresentazione della classe in formato array
		 *
		 * @return array
		 */
		public function getEmptyObjKeyArray();

		public function getJsonValue($metodo, $indice = null, $key = null);

		/**
		 * @param $input
		 *
		 * @return string|int
		 */
		public function encodeObj($input);

		/*GETTER & SETTER*/
		/**
		 * @return DrakkarDbConnector
		 */
		public function getConn();

		/**
		 * @param DrakkarDbConnector $conn
		 */
		public function setConn($conn);


		/**
		 * @return string
		 */
		public function getWhereBase();

		/**
		 * @param string $whereBase
		 */
		public function setWhereBase($whereBase);

		/**
		 * @return string
		 */
		public function getOrderBase();

		/**
		 * @param string $orderBase
		 */
		public function setOrderBase($orderBase);

		/**
		 * @return string
		 */
		public function getTableName();

		/**
		 * @param string $tableName
		 */
		public function setTableName($tableName);

		/**
		 * @return integer
		 */
		public function getLimitBase();

		/**
		 * @param integer $limitBase
		 */
		// public function setLimitBase(Integer $limitBase)
		public function setLimitBase($limitBase);

		/**
		 * @return integer
		 */
		public function getOffsetBase();

		/**
		 * @param integer $offsetBase
		 */
		public function setOffsetBase($offsetBase);

		/**
		 * @return bool
		 */
		public function isFlagObjectDataValorized();

		/**
		 * @param $string
		 *
		 * @return bool
		 */
		public function isJson($string);

		/**
		 * @param $j
		 *
		 * @return string
		 */
		public function jsonEncode($string);

		/**
		 * @param $string
		 *
		 * @return string
		 * @throws DrakkarJsonException
		 */
		public function jsonDecode($string);
	}