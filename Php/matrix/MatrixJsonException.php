<?php
/**
 * TODO Description
 *
 * User: Claudio COLOMBO - P.D.A. Srl
 * Creation: 12/11/17
 */

namespace Drakkar\Exception;
class MatrixJsonException extends MatrixException
{
    public function __construct ($code = 0) {
    	$code = $this->codeGenerator($code);
        parent::__construct($code, null, '', '', null);
    }
    
    
    
    private function codeGenerator($code){
		switch ($code) {
			case JSON_ERROR_DEPTH:
				return 'DKR-JSN-001';
				break;
			case JSON_ERROR_STATE_MISMATCH:
				return 'DKR-JSN-002';
				break;
			case JSON_ERROR_CTRL_CHAR:
				return 'DKR-JSN-003';
				break;
			case JSON_ERROR_SYNTAX:
				return 'DKR-JSN-004';
				break;
			case JSON_ERROR_UTF8:
				return 'DKR-JSN-005';
				break;
			case JSON_ERROR_RECURSION:
				return 'DKR-JSN-006';
				break;
			case JSON_ERROR_INF_OR_NAN:
				return 'DKR-JSN-007';
				break;
			case JSON_ERROR_UNSUPPORTED_TYPE:
				return 'DKR-JSN-008';
				break;
			default:
				return 'DKR-JSN-000';
				break;
		}
	}


}