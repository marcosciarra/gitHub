<?php
/**
 * TODO Description
 *
 * User: Claudio COLOMBO - P.D.A. Srl
 * Creation: 12/11/17
 */

namespace Drakkar\Exception;
class MatrixDuplicatePrimaryException extends MatrixException
{
    public function __construct ($code = 0, $previous = null,$obj='',$query='',$queryParameters=array()) {
        parent::__construct($code, $previous, '', $query, $queryParameters);
    }


}