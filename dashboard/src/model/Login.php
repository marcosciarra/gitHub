<?php
/**
 * Created by Sciarra Marco
 **/

namespace Click\Affitti\TblBase;
require_once 'LoginModel.php';

use Click\Affitti\TblBase\LoginModel;

class Login extends LoginModel
{


    function __construct($pdo)
    {
        parent::__construct($pdo);
    }

    public function findByUtentePassword($username, $password, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE username=? AND password=? AND bloccato=0";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResult($query, array($username, $password), $typeResult);
    }

    public function getPasswordByIdUtente($idUtente)
    {
        $query = "SELECT password FROM $this->tableName WHERE id=?";
        return $this->createResultValue($query, array($idUtente));
    }

    public static function getPasswordByIdUtenteStatic($pdo, $idUtente)
    {
        $login = new self($pdo);
        return $login->getPasswordByIdUtente($idUtente);
    }

    /**
     * @param string $password Password
     */
    public function setPassword($password, $encodeType = self::STR_DEFAULT)
    {
        $this->password = self::encryptPassword($password);
    }

    public static function encryptPassword($password)
    {
        return md5(SALT . $password);
    }


    public function findPerSelect($typeResult = self::FETCH_KEYARRAY)
    {
        $query = "SELECT id,username as descrizione FROM $this->tableName ";
        if ($this->whereBase) $query .= " WHERE $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, null, $typeResult);
    }

}