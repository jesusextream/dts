<?php


require_once '../core/DBManagement.php';
require_once '../core/DBInspector.php';
require_once '../core/Configurator.php';
	
	Configurator::getInstance();

if (isset($_GET['e'])){
    
    $sql = "select logo, type_logo from empresa where rif='".$_GET['e']."'";
    
   DBManagement::getInstance()->consultar($sql);
   $r = DBManagement::getInstance()->getResultSet();
   
   if (is_array($r) && count($r) > 0){
       
      // header("Content-type: ".$r[0]['type_logo']);
//echo $r[0]['type_logo'];
     // echo $r[0]['logo'];
$result = imagecreatefromstring(base64_decode($r[0]['type_logo']));
imagejpeg($r[0]['logo'], 'test.jpg');


       
       
   } else {
       $r = FALSE;
       echo "";
   }
            
    }

?>