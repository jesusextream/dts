<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/model/Producto.php';


if(isset($_POST['dat']) && strcmp($_POST['dat'], 'pqbo') == 0 ){
   $fileName = 'productsQBO.xlsx';
} else {
   $fileName = 'products.xlsx';
}

$XLFileType = PHPExcel_IOFactory::identify('files/' .$fileName);  
$objReader = PHPExcel_IOFactory::createReader($XLFileType);  
//$objReader->setLoadSheetsOnly('Sheet1');  
$objPHPExcel = $objReader->load('files/' .$fileName);  



$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);  
//$objWorksheet = $objPHPExcel->setActiveSheetIndexByName('Hoja1');


for ($i=1; $i < $objPHPExcel->getActiveSheet()->getHighestRow(); $i++) {
//var_dump($objPHPExcel->getActiveSheet()->getCell('A'.($i+1))->getFormattedValue());
   if ($objPHPExcel->getActiveSheet()->getCell('A'.($i+1))->getFormattedValue() != ''){
   $producto = new Producto();
   $producto->setCantidad($objPHPExcel->getActiveSheet()->getCell('A'.($i+1))->getFormattedValue());
   $producto->setNumPart($objPHPExcel->getActiveSheet()->getCell('B'.($i+1))->getFormattedValue());
   $producto->setDescripcion($objPHPExcel->getActiveSheet()->getCell('C'.($i+1))->getFormattedValue());
   $producto->setPrecio($objPHPExcel->getActiveSheet()->getCell('D'.($i+1))->getFormattedValue());
   $producto->setTotal(((int) preg_replace('/[A-Za-z-$]/','',$producto->getCantidad()))*((float) preg_replace('/[^0-9.]/','',$producto->getPrecio())));
   $productos[] = $producto->getArrayVars();
}
   //var_dump((float) preg_replace('/[^0-9.]/','',$producto->getPrecio()));
   
}

if (isset($productos))
	echo json_encode($productos);
else
	echo json_encode(FALSE);





