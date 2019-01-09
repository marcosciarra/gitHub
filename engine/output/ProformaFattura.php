<?php
/**
Created by Sciarra Marco
**/

namespace Click\Affitti\TblBase;
require_once 'ProformaFatturaModel.php';

use Click\Affitti\TblBase\ProformaFatturaModel;

class ProformaFattura extends ProformaFatturaModel
{


function __construct($pdo){parent::__construct($pdo);}
}