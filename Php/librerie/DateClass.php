<?php
/**
 * Created by Marco Sciarra
 */

class DateClass
{

    /**
     * DateClass constructor.
     */
    public function __construct()
    {
    }

    /**
     * Funziona che calcola la data dal giorno, mese e anni inserito
     *
     * @param $anni
     * @param $mese
     * @param $giorni
     * @param string $formato
     * @param string $delimitatore
     * @return false|string
     */
    public function creaData($anni, $mese, $giorni, $ora = 0, $minuti = 0, $secondi = 0, $formato = 'Y-m-d')
    {
        return date($formato, mktime($ora, $minuti, $secondi, $mese, $giorni, $anni));
    }


    /**
     * Somma / Sottrae ANNI, MESI, GIORNI alla data inserita
     *
     * @param $data
     * @param $anni
     * @param $mese
     * @param $giorni
     * @param string $formato
     * @param string $delimitatore
     * @return false|string
     */
    public function calcolaData($data, $anni, $mese, $giorni, $formato = 'Y-m-d', $delimitatore = '-')
    {
        $data = explode($delimitatore, $data);
        return date($formato, mktime(0, 0, 0, $data[1] + $mese, $data[2] + $giorni, $data[0] + $anni));
    }

    /**
     * Restituisce il primo giorno dell'anno della data inserita
     *
     * @param $data
     * @param string $formato
     * @param string $delimitatore
     * @return false|string
     */
    function getInizioAnno($data, $formato = 'Y-m-d', $delimitatore = '-')
    {
        $data = explode($delimitatore, $data);
        return date($formato, mktime(0, 0, 0, 01, 01, $data[0]));
    }


    /**
     * Restituisce l'ultimo giorno dell'anno della data inserita
     *
     * @param $data
     * @param string $formato
     * @param string $delimitatore
     * @return false|string
     */
    function getFineAnno($data, $formato = 'Y-m-d', $delimitatore = '-')
    {
        $data = explode($delimitatore, $data);
        return date($formato, mktime(0, 0, 0, 12, 31, $data[0]));
        $giorni = dateDiff($rate->getPeriodoInizio(), $rate->getPeriodoFine(), '%a');
    }


    /**
     * Restituisce il giorno della data inserita
     *
     * @param $data
     * @return mixed
     */
    function restituisciGiorno($data)
    {
        $data = explode('-', $data);
        return $data[2];
    }


    /**
     * Restituisce il mese della data inserita
     *
     * @param $data
     * @return mixed
     */
    function restituisciMese($data)
    {
        $data = explode('-', $data);
        return $data[1];
    }


    /**
     * Restituisce l'anno della data inserita
     *
     * @param $data
     * @return mixed
     */
    function restituisciAnno($data)
    {
        $data = explode('-', $data);
        return $data[0];
    }


    /**
     * Trasforma la stringa MySql in formato data
     *
     * @param $input
     * @return false|string
     */
    function stringMySqlToDate($input)
    {
        return date("Y-m-d", strtotime($input));
    }


    /**
     * Formatta data nel formato che indico
     *
     * @param $data
     * @param string $formato
     * @param string $delimitatore
     * @return false|string|void
     */
    function formattaDate($data, $formato = 'd-m-Y', $delimitatore = '-')
    {
        if ($data == null)
            return false;
        $data = explode($delimitatore, $data);
        return date($formato, mktime(0, 0, 0, $data[1], $data[2], $data[0]));
    }

    /**
     * Restituisce la differenza in giorni tra due date
     *
     * @param $date1 --> formato stringa 'YYYY-MM-DD'
     * @param $date2 --> formato stringa 'YYYY-MM-DD'
     * @param $format --> %a restituisce i giorni
     * @return string
     */
    function dateDiff($date1, $date2, $format)
    {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        return $interval->format($format);
    }

}