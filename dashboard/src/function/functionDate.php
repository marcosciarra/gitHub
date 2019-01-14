<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 30/01/18
 * Time: 18.25
 */


function calcolaData($data, $anni, $mese, $giorni, $formato = 'Y-m-d', $delimitatore = '-')
{
    $data = explode($delimitatore, $data);
    return date($formato, mktime(0, 0, 0, $data[1] + $mese, $data[2] + $giorni, $data[0] + $anni));
}

function getInizioAnno($data, $formato = 'Y-m-d', $delimitatore = '-')
{
    $data = explode($delimitatore, $data);
    return date($formato, mktime(0, 0, 0, 01, 01, $data[0]));
}

function getFineAnno($data, $formato = 'Y-m-d', $delimitatore = '-')
{
    $data = explode($delimitatore, $data);
    return date($formato, mktime(0, 0, 0, 12, 31, $data[0]));
}

function restituisciGiorno($data)
{
    $data = explode('-', $data);
    return $data[2];
}

function restituisciMese($data)
{
    $data = explode('-', $data);
    return $data[1];
}

function restituisciAnno($data)
{
    $data = explode('-', $data);
    return $data[0];
}


function stringMySqlToDate($input)
{
    return date("Y-m-d", strtotime($input));
}

function formattaDate($data, $formato = 'd-m-Y', $delimitatore = '-')
{
    if ($data == null) return;
    $data = explode($delimitatore, $data);
    return date($formato, mktime(0, 0, 0, $data[1], $data[2], $data[0]));
}

/**
 * @param $date1        --> formato stringa 'YYYY-MM-DD'
 * @param $date2        --> formato stringa 'YYYY-MM-DD'
 * @param $format       --> %a restituisce i giorni
 * @return string
 */
function dateDiff($date1,$date2,$format)
{
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->format($format);
}
