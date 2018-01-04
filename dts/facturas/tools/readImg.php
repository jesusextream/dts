<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../core/DBManagement.php';
require_once '../core/DBInspector.php';
require_once '../core/Configurator.php';
	
	Configurator::getInstance();

if (isset($_GET['e'])){
    
    $sql = "select logo, type_logo from empresa where rif='".$_GET['e']."'";
    
   DBManagement::getInstance()->consultar($sql);
   $r = DBManagement::getInstance()->getResultSet();
   //var_dump($r[0]['logo']);//die();
   if (is_array($r) && count($r) > 0){
       
       header("Content-type: ".$r[0]['type_logo']);
//echo $r[0]['type_logo'];
      echo $r[0]['logo'];
       
       
   } else {
       $r = FALSE;
       echo "";
   }
            
    
    
    
    
    
    
}