<?php
/**
 * Created by PhpStorm.
 * User: clickale
 * Date: 06/04/17
 * Time: 15.37
 */

require_once '../lib/functions.php';

require_once '../PHPExcel/Classes/PHPExcel.php';



function stampaExcel($name){


    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Alessandro Pericolo")
        ->setLastModifiedBy("Ale Peril")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $name)
        ->setCellValue('B2', $name)
        ->setCellValue('C1', $name)
        ->setCellValue('D2', $name);

    // Miscellaneous glyphs, UTF-8
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A4', 'Miscellaneous glyphs')
        ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');


    $objPHPExcel->getActiveSheet()->setTitle('Foglio 1');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="TestDownload.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

    exit;
}

stampaExcel($_GET['name']);

// create/read session
ob_start();
session_start();

//$postdata = file_get_contents("php://input");
//$request = json_decode($postdata);
//$function = $request->function;
//$r = $function($request);
//echo $r;