<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../core/DBManagement.php';
require_once '../core/DBInspector.php';
require_once '../core/Configurator.php';

	//$obj_function = new coFunction();
	Configurator::getInstance();
        //$mysqli = DBManagement::getInstance();

function putPedido($pedido) {
  //  global $mysqli;
    //$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
 
    
					
                                                   //var_dump($r);
						//$r = $resul->fetch_array(MYSQLI_ASSOC);
						
							$campo = "num_invoice,num_orden,fecha_emision,cliente_rif,empresa_rif, sub_total, sale_tax, discount, freight, handling, restocking, total_sale";
							$valor = "'" . $pedido['num_invoice'] . "','" . $pedido['num_orden']. "',CURRENT_TIMESTAMP, '".$pedido['cliente_rif']."', '".$pedido['empresa_rif']."', '".preg_replace('/[^0-9.]/','',$pedido['sub_total'])."', '".preg_replace('/[^0-9.]/','',$pedido['sale_tax'])."', '".preg_replace('/[^0-9.]/','',$pedido['discount'])."', '".$pedido['freight']."', '".$pedido['handling']."', '".$pedido['restocking']."', '".preg_replace('/[^0-9.]/','',$pedido['total_sale'])."'";					
                                                        $sql = "INSERT INTO pedidos (".$campo.") VALUES ($valor)";
                                                        //var_dump($sql);
							DBManagement::getInstance()->insertar($sql);
							if(DBManagement::getInstance()->getCountRows() == 1){
                                                                $idPedido = DBManagement::getInstance()->getLastId();
								$campo = "num_invoice,qty, num_part, descripcion,  precio_unit,  sub_total, id_pedido";						
                                                                
                                                                foreach ($pedido['detalle'] as $detalle) {
                                                                    
                                                                
								$valor = "'" . $detalle['num_invoice'] . "','" . $detalle['qty']. "', '" . $detalle['numPart']. "',  '".$detalle['descripcion']."', '".preg_replace('/[^0-9.]/','',$detalle['precio_unit'])."', '".preg_replace('/[^0-9.]/','',$detalle['sub_total'])."', '" . $idPedido . "'";
                                                                $sql = "insert into detalle_pedido (".$campo.") values (".$valor.")";
                                                               // var_dump($sql);
								DBManagement::getInstance()->insertar($sql);
								if(DBManagement::getInstance()->getCountRows() == 1){							
									$aResult['result'] = "PEDIDO CREADO;".$idPedido;					
								}else{
									$aResult['error'] = "PEDIDO CREADO CON EXITO PERO, EL DETALLE NO FUE AGREGADO";
								}
                                                                }
							}else{
								$aResult['error'] = "CREACION DE PEDIDO FALLIDA!";
								$aResult['error'] = "CREACION DE PEDIDO FALLIDA!" .$sql;
							}
					
							
					//else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
    
                                        return json_encode($aResult);
}





if (isset($_POST)){
$method = $_POST['method'];
if (isset($_POST['parameters'])){
	$parameters = $_POST['parameters'];
	foreach ($parameters as $key => $value) {
		$p[] = $value;
	}

	echo call_user_func_array($method, $p);

} else {
	echo ($method());
}
}