<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10/01/19
 * Time: 21.15
 */

namespace Click\Affitti\Viste;

class Rilascio extends PdaAbstractModel
{

    /** @var PDO  */
    protected $con;

    /** @var array */
    protected $clienti;

    /**
     * Rilascio constructor.
     */
    public function __construct()
    {
        $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        $this->con = new PDO(
            'mysql:host=' . HOST . ':' . PORT . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
            USER,
            PWD,
            $attribute
        );

        $query = 'SELECT ambiente FROM clienti';
        $this->clienti = $this->con->query($query)->fetchAll();

    }


    public function copia($origine, $destinazione)
    {
        foreach (scandir($origine) as $file) {
            if (!is_readable($origine . '/' . $file)) continue;
            if (is_dir($file) && ($file != '.') && ($file != '..')) {
                mkdir($destinazione . '/' . $file);
                copia($origine . '/' . $file, $destinazione . '/' . $file);
            } else copy($origine . '/' . $file, $destinazione . '/' . $file);
        }
    }

    /**
     * @return array
     */
    public function getClienti()
    {
        return $this->clienti;
    }

    /**
     * @param array $clienti
     */
    public function setClienti($clienti)
    {
        $this->clienti = $clienti;
    }

}