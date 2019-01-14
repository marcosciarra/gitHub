<?php

namespace Click\Flussi\Utility;
/**
 * funzini per la gestione delle stringhe
 *
 * @author  COLOMBO Claudio
 * @version 1.4 febbraio 2017
 */
class StringUtility
{

    /**
     *
     * funzione per preparare le stringhe per i file di flussi
     *
     * @param string $stringa
     * @param        int        ˙ $lunghezza
     * @param        $direzione "S" sinistra - "L" destra
     *
     * @return string
     */
    public static function preparaPerFlussi($stringa, $lunghezza = 0, $direzione = "S")
    {

        $s = "";
        if (strlen($stringa) < $lunghezza) {
            switch (strtoupper($direzione)) {
                case 'L':
                case 'D':
                case ((is_numeric($direzione) and ($direzione == STR_PAD_LEFT))):
                    $direzione = STR_PAD_LEFT;
                    break;
                case 'S':
                case 'R':
                case ((is_numeric($direzione) and ($direzione == STR_PAD_RIGHT))):
                    $direzione = STR_PAD_RIGHT;
                    break;
                case ((is_numeric($direzione) and ($direzione == STR_PAD_BOTH))):
                    break;
                default:
                    $direzione = STR_PAD_RIGHT;
                    break;
            }
            $s = str_pad(trim($stringa), $lunghezza, ' ', $direzione);
        } else {
            $s = substr($stringa, 0, $lunghezza);
        }

        return $s;
    }

    public static function preparaPerFlussiUpper($stringa, $lunghezza = 0, $direzione = "S")
    {
        $stringa = strtoupper($stringa);

        return StringUtility::preparaPerFlussi($stringa, $lunghezza, $direzione);
    }

    /**
     *
     * funzione per preparare i numeri per i file di flussi compensando con 0
     *
     * @param string $stringa
     * @param        int ˙ $lunghezza
     *
     * @return string
     */
    public static function preparaImportiPerFlussi($stringa, $lunghezza = 0, $tornaSpazi = false)
    {
        $importo = explode('.', $stringa);
        if ($importo[0] == '') {

            if ($tornaSpazi) {
                return str_pad('', $lunghezza, ' ', STR_PAD_LEFT);
            }
            return str_pad(0, $lunghezza, '0', STR_PAD_LEFT);
        } else
            if ($tornaSpazi && $importo[0] == 0) {
                return str_pad('', $lunghezza, ' ', STR_PAD_LEFT);
            }
        return str_pad(trim($importo[0]), $lunghezza, '0', STR_PAD_LEFT);
    }


    /**
     *
     * funzione per preparare i numeri per i file di flussi compensando con 0
     *
     * @param string $stringa
     * @param        int ˙ $lunghezza
     *
     * @return string
     */
    public static function preparaPerFlussiCon0($stringa, $lunghezza = 0)
    {
        if (strlen($stringa) < $lunghezza) {
            return str_pad(trim($stringa), $lunghezza, '0', STR_PAD_LEFT);
        } else {
            return substr($stringa, 0, $lunghezza);
        }
    }

    /**
     *
     * Prepara i double per essere inseriti nei flussi.<br/>
     * Il valore numerico viene riportato in centesimi e trasformato in stringa
     * di lunghezza riempita di 0
     *
     * @param double $valore
     * @param int $lunghezza
     *
     * @return string stringa fornattata
     */
    public static function doubleToStringFlussi($valore, $lunghezza = 0)
    {
        if ($valore == '') $valore = 0;

        //trasformo in centesimi
        $valore *= 100;
        $num = round($valore);
        //trasformo in stringa
        $s = str_pad($num, $lunghezza, '0', STR_PAD_LEFT);

        return $s;
    }

    /**
     * Verifoca se un flag è 0: se lo ' restituisce 0, altrimenti 1
     *
     * @param $flg
     *
     * @return string
     */
    public static function ckFlag0($flg)
    {
        return ($flg == "0") ? "0" : "1";
    }

    /**
     *
     *
     * @param $flg
     *
     * @return string
     */
    public static function ckFlagTernario($flg)
    {
        if ($flg == '' || $flg == ' ' || $flg == null) return ' ';
        return ($flg == "0") ? "0" : "1";
    }

    /**
     * @param int $lunghezza
     *
     * @return string
     */
    public static function creaFiller($lunghezza = 1)
    {
        $stringa = "";
        for ($i = 0; $i < $lunghezza; $i++) {
            $stringa = $stringa . " ";
        }

        return $stringa;
    }

    /**
     * @param int $lunghezza
     *
     * @return string
     */
    public static function creaFiller0($lunghezza = 1)
    {
        $stringa = "";
        for ($i = 0; $i < $lunghezza; $i++) {
            $stringa = $stringa . "0";
        }

        return $stringa;
    }

    /**
     * @param string $stringa
     * @param int $lunghezza
     *
     * @return string
     */
    public static function creaStringaConFiller($stringa = '', $lunghezza = 1)
    {
        for ($i = 0; $i < $lunghezza; $i++) {
            $stringa = $stringa . " ";
        }

        return $stringa;
    }

    public static function creaCampoCB($flag = true)
    {
        return ($flag) ? 1 : 0;
    }


    public static function sistemaCaratteriXml($testo)
    {
        $testo = utf8_encode($testo);

        return str_replace(array('&', '<', '>', '"', "'", '°', '∞', 'à', 'è', 'é', 'ì', 'ò', 'ù'),
            array('e', '&lt;', '&gt;', '&quot;', '&apos;', '', '', "a'", "e'", "e'", "i'", "o'", "u'"),
            $testo);
    }

    public static function preparaPerFlussiNumTelefono($telefono, $dimensione)
    {
        $telefono = str_replace(array('/', '-', '.'), '', $telefono);

        return self::preparaPerFlussi($telefono, $dimensione);
    }

}

?>
