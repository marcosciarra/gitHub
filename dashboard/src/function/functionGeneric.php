<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27/06/18
 * Time: 14.51
 */


function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
{
    $sort_col = array();
    foreach ($arr as $key => $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}


function formattaNumero($value, $decimali = 2, $separatoreDecimali = '.', $separatoreMigliaia = '.')
{
    return number_format($value, $decimali, $separatoreDecimali, '');
}