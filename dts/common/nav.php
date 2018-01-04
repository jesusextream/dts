<?php

/* 
 * OPCIONES DEL MENU.
 */
session_start();
global $opc_nav;
$opc_nav = array(
    'index' => array('Inicio','init.php','fa fa-home')
);								
if (in_array('catalog', $_SESSION['keywords'])){
	$opc_nav['Catalogos'] = array('Catalogos','catalogoIndex.php','fa fa-file-text-o','');
}
if (in_array('flayer', $_SESSION['keywords'])){
	$opc_nav['flayers'] = array('Flayers','flyer.php','fa fa-book','');
}
if (in_array('order', $_SESSION['keywords'])){
	$opc_nav['Orders'] = array('Orders','submenu-orders','fa fa-check-square-o', 'orderPick.php', 'Order Pick');
}
if (in_array('report', $_SESSION['keywords'])){
	$opc_nav['Reportes'] = array('Reportes','reports.php','fa fa-clipboard','');
}
//var_dump($opc_nav);die();
//    function opc_nav(){
//        $opc_nav = array(
//             'index' => array('Inicio','init.php','fa fa-home')
//            ,'catalogos' => array('Catalogos','catalogoIndex.php','fa fa-book')
//        );
//        return $opc_nav;
//    }	