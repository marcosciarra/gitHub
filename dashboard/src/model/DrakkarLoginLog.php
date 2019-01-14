<?php
	/**
	 * Created by PhpStorm.
	 * User: claudio
	 * Date: 02/05/18
	 * Time: 20.54
	 */
	
	namespace Drakkar\Log;
	
	
	class DrakkarLoginLog
	{
		private $idLogin;
		
		public function __construct($idLogin)
		{
			$this->idLogin = $idLogin;
			$this->writeFileLog();
		}
		
		/**
		 * @param $idLogin
		 */
		function writeLog($idLogin)
		{
			$this->writeFileLog();
		}
		
		/**
		 *
		 */
		private function writeFileLog()
		{
			//$fileName = $_SERVER['DOCUMENT_ROOT'] . '/log/access.' . $this->now('Ymd') . '.log';
			$fileName = dirname(dirname(dirname(__FILE__))) . '/log/access.' . $this->now('Ymd') . '.log';
			//$fExist = file_exists($fileName);
			$log = fopen($fileName, "a");
			
			if (!$log)
				throw new \Exception('File open failed.');
			
//			if ($fExist)
//				fwrite($log, "User\tDate\tTime\tTrace\n");
			
			
			fwrite($log,
				   $this->idLogin . "\t" .
				   $this->now("d-m-Y") . "\t" .
				   $this->now("H:i") . "\n"
			);
			fclose($log);
			
		}
		
		private function now($format = "Y-m-d H:i:s")
		{
			return date($format);
		}
	}