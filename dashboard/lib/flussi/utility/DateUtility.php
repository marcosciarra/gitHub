<?php

/**
 * funzini per la gestione delle date
 *
 * @author  COLOMBO Claudio
 * @version 1.0 gennaio 2012
 */
class DateUtility
{
    /**
     * @param string $data
     * @param int    $tipoConversione   0(default): YYYYMMGG ->GG/MM/YYYY<br/>
     *                                  1         : YYYYMMGG ->GG/MM/YYYY<br/>
     *                                  2         : YYYY-MM-GG ->GG/MM/YYYY<br/>
     *                                  3         : YYYY-MM-GG ->GGMMYYYY<br/>
     *                                  4         : YYYY-MM-GG ->GGMMYY<br/>
     *                                  5         : YYYY-MM-GG ->MMYYYY<br/>
     *                                  6         : YYYY-MM-GG(0000-00-00) ->GGMMYYYY('')<br/>
     */
    public static function convertiData($data, $tipoConversione = 0)
    {
        $dtf = false;
        switch ($tipoConversione) {
            case 0:
                $dtf = DateUtility::formatta0($data);
                break;
            case 1:
                $dtf = DateUtility::formatta1($data);
                break;
            case 2:
                $dtf = DateUtility::formatta2($data);
                break;
            case 3:
                $dtf = DateUtility::formatta3($data);
                break;
            case 4:
                $dtf = DateUtility::formatta4($data);
                break;
            case 5:
                $dtf = DateUtility::formatta5($data);
                break;
            case 6:
                $dtf = DateUtility::formatta6($data);
                break;
        }

        return $dtf;
    }

    private static function formatta0($dt)
    {
        $a = substr($dt, 0, 4);
        $m = substr($dt, 5, 2);
        $d = substr($dt, 8, 2);

        return $d . $m . $a;
    }

    private static function formatta1($dt)
    {
        $a = substr($dt, 0, 4);
        $m = substr($dt, 5, 2);
        $d = substr($dt, 8, 2);

        return $d . $m . $a;
    }

    private static function formatta2($dt)
    {
        $a = substr($dt, 0, 4);
        $m = substr($dt, 5, 2);
        $d = substr($dt, 8, 2);

        return $d . '/' . $m . '/' . $a;
    }

    private static function formatta3($dt)
    {
        $a = substr($dt, 0, 4);
        $m = substr($dt, 5, 2);
        $d = substr($dt, 8, 2);

        return $d . $m . $a;
    }

    private static function formatta4($dt)
    {
        $a = substr($dt, 2, 2);
        $m = substr($dt, 5, 2);
        $d = substr($dt, 8, 2);

        return $d . $m . $a;
    }

    private static function formatta5($dt)
    {
        $a = substr($dt, 0, 4);
        $m = substr($dt, 5, 2);

        return $m . $a;
    }

    private static function formatta6($dt)
    {
        if($dt == '0000-00-00')
            return '';
        else
            return self::formatta3($dt);
    }
}