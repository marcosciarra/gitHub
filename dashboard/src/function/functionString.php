<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 19/10/18
 * Time: 14.51
 */

function stringaRidotta($input, $numCaratteri, $stringaFinale = '')
{
    $output=$input;
    if (strlen($input) > $numCaratteri) {
        $output = substr($input, 0, $numCaratteri);
        $output = $output . $stringaFinale;
    }
    return $output;
}