<?php
session_start();

if(isset($_GET['export_report_csv'])) {

	header("Content-Type: text/csv");
	header("Content-Disposition: attachment; filename=\"".$_SESSION["csv_filename"].".csv\"");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	
	// export header and body of report ran
	echo($_SESSION["csv_header"].$_SESSION["csv_body"]);
	
	die();

}
?>