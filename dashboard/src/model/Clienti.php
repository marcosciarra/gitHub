<?php
/**
Created by Sciarra Marco
**/

namespace Click\Affitti\TblBase;
require_once 'ClientiModel.php';

use Click\Affitti\TblBase\ClientiModel;

class Clienti extends ClientiModel
{


function __construct($pdo){parent::__construct($pdo);}
}