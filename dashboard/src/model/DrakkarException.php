<?php
/**
 * TODO Description
 *
 * User: Claudio COLOMBO - P.D.A. Srl
 * Creation: 12/11/17
 */

namespace Drakkar\Exception;


class DrakkarException extends \Exception
{

    protected $title;
    protected $query;
    /** @var string */
    protected $code;
    protected $queryParameters;
    protected $obj;

    public function __construct ($code = '', $previous = null, $obj = '', $query = '', $queryParameters = array()) {
        $c = explode('-', $code);
        $c[2] += 0;
        $error_array = parse_ini_file("error.ini", true);
        $mex = explode('|', $error_array[$c[1]]['code'][$c[2]]);
        parent::__construct($mex[1], 0, $previous);
        $this->title = (isset($mex[0])) ? $mex[0] : '';
        $this->query = $query;
        $this->code = $code;
        $this->queryParameters = $queryParameters;
        $this->obj = $obj;
    }

    public function getTitle () {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getQuery () {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getQueryParameters ($toString = false) {
        if ($toString)
            return implode('||', $this->getQueryParameters());
        else
            return $this->queryParameters;
    }


}