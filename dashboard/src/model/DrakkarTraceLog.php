<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 02/05/18
 * Time: 20.54
 */

namespace Drakkar\Log;


class DrakkarTraceLog
{
    private $idLogin;
    private $traceJump;

    public function __construct($idLogin, $traceJump = 2)
    {
        $this->idLogin = $idLogin;
        $this->traceJump = $traceJump;
        $this->writeFileLog();
    }

    /**
     * @param $idLogin
     */
    private function writeLog($idLogin)
    {
        $this->writeFileLog();
    }

    /**
     *
     */
    private function writeFileLog()
    {
        $fileName = dirname(dirname(dirname(__FILE__))) . '/log/operation.' . $this->now('Ymd') . '.log';
        $fExist = file_exists($fileName);
        $log = fopen($fileName, "a");

        if (!$log)
            throw new Exception('File open failed.');

        if (!$fExist)
            fwrite($log, "User\tDate\tTime\tCaller\tTrace\n");

        //$debugTrace = debug_backtrace();
        //debug_print_backtrace();

        fwrite($log,
            $this->idLogin . "\t" .
            $this->now("d-m-Y") . "\t" .
            $this->now("H:i") . "\t" .
            $this->getCaller(debug_backtrace()) . "\t" .
            json_encode($this->cleanTrace(debug_backtrace())) . "\n"
        );
        fclose($log);

    }

    private function now($format = "Y-m-d H:i:s")
    {
        return date($format);
    }

    private function getCaller($trace)
    {
        $app = explode('/', $trace[$this->traceJump]['file']);
        return $app[count($app) - 1];
    }

    private function cleanTrace($trace)
    {
        $result = [];

        for ($i = $this->traceJump; $i < count($trace); $i++) {
            if (isset($trace[$i]['type'])) {
                unset($trace[$i]['type']);
            }
            if (isset($trace[$i]['object'])) {
                if (gettype($trace[$i]['object']) == 'string')
                    if (trim($trace[$i]['object']) == '')
                        unset($trace[$i]['object']);
            }

            $result[] = $trace[$i];
        }
        return $result;
    }
}