<?php

require_once 'PdaAbstractModel.php';
require_once '../../lib/pdo.php';

class Login extends PdaAbstractModel{

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function findPasswordByUsername($username){

        $query = 'SELECT password FROM login WHERE username = ?';
        
        $login = new Login($this->pdo);
        return $login->createResultValue($query, array($username));
    }
    
    public function findAllByUsername($username){

        $query = 'SELECT * FROM login WHERE username = ?';
        
        $result = array();
        $login = new Login($this->pdo);
        $result = $login->createResult($query, array($username));
        
        return json_encode($result);
    }

        
    public function findAll(){

        $query = 'SELECT * FROM login';
        
        $result = array();
        $login = new Login($this->pdo);
        $result = $login->createResultArray($query, null);
        
        return json_encode($result);
    }
    

}