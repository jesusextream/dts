<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../core/DBManagement.php';
require_once '../core/DBInspector.php';
require_once '../core/Configurator.php';

Configurator::getInstance()->loadDBConfig("SQLSERVER");

function getSalesReceipt($id = NULL, $from = NULL, $to = NULL) {
    
    
    
    if (isset($id) && $id != "") {

        $sql = "SELECT tot_neto, fact_num, descrip, cast(fec_emis as date) as fec_emis, nombre FROM factura WHERE fact_num = '$id'";
        $existsWhere = TRUE;
    } else {

        $sql = "SELECT tot_neto, fact_num, descrip, cast(fec_emis as date) as fec_emis, nombre FROM factura";
    }

    if (isset($from) && trim($from) != "" && isset($to) && trim($to) != "") {
        if (isset($existsWhere) && $existsWhere == TRUE) {
            $where = " AND fec_reg >= '" . $from . "' and fec_reg <= '" . $to . "'";
        } else {
            $where = " WHERE cast(fec_emis as date) >= cast('" . $from . "' as date) and cast(fec_emis as date) <= cast('" . $to . "' as date)";
        }
    } else
        $where = '';

    $sql = $sql . $where;

    //echo $sql;
    //$mysqli = new mysqli("50.196.74.121", "querysys", "text5740", "AFM_A");

    $serverName = DBManagement::getInstance()->getHost(); //serverName\instanceName
    $connectionInfo = array( "Database"=>DBManagement::getInstance()->getDbName(), "UID"=>DBManagement::getInstance()->getUser(), "PWD"=>DBManagement::getInstance()->getPass(), "CharacterSet" => "UTF-8");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    
        if( $conn === false ) {
         die( print_r( sqlsrv_errors(), true));
    }
    
    
    $stmt = sqlsrv_query( $conn, $sql);
    if( $stmt === false ) {
         die( print_r( sqlsrv_errors(), true));
    } 
    
    while ($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
        
        $r[] = $row;
        
    }
    
    sqlsrv_free_stmt( $stmt);
    sqlsrv_close($conn);
    

    

    if (is_array($r) && count($r) > 0) {

        $r;
    } else {
        $r = FALSE;
    }

    /* $data[] = array(

      // 'num' => 1,
      'razon' => 'Nodnet',
      'dir' => 'boleita',
      'tlf' => '55555555',
      'rif' => 'J-123456789',
      'id' => '1'
      ); */

    return json_encode($r);
}



function getDetail($id = NULL, $from = NULL, $to = NULL) {
    if (isset($id) && $id != "") {

        $sql = "select b.co_art as part_no, b.total_art as cant_qty, 
            b.prec_vta as precio_uni, a.art_des as descripcion
            from art as a inner join reng_fac as b 
            on a.co_art = b.co_art
            where b.fact_num = '".$id."'";
        
        //echo $sql;
        $existsWhere = TRUE;
    } else {

        $sql = "SELECT * FROM factura";
    }

    if (isset($from) && trim($from) != "" && isset($to) && trim($to) != "") {
        if (isset($existsWhere) && $existsWhere == TRUE) {
            $where = " AND fec_reg >= '" . $from . "' and fec_reg <= '" . $to . "'";
        } else {
            $where = " WHERE fec_reg >= '" . $from . "' and fec_reg <= '" . $to . "'";
        }
    } else
        $where = '';

    $sql = $sql . $where;

    //echo $sql;
    //$mysqli = new mysqli("50.196.74.121", "querysys", "text5740", "AFM_A");

    $serverName = DBManagement::getInstance()->getHost(); //serverName\instanceName
    $connectionInfo = array( "Database"=>DBManagement::getInstance()->getDbName(), "UID"=>DBManagement::getInstance()->getUser(), "PWD"=>DBManagement::getInstance()->getPass(), "CharacterSet" => "UTF-8");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);

        if( $conn === false ) {
         die( print_r( sqlsrv_errors(), true));
    }
    
    
    $stmt = sqlsrv_query( $conn, $sql);
    if( $stmt === false ) {
         die( print_r( sqlsrv_errors(), true));
    } 
    
    while ($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
        
        $r[] = $row;
        
    }
    
    sqlsrv_free_stmt( $stmt);
    sqlsrv_close($conn);
    

    

    if (is_array($r) && count($r) > 0) {

        $r;
    } else {
        $r = FALSE;
    }

    /* $data[] = array(

      // 'num' => 1,
      'razon' => 'Nodnet',
      'dir' => 'boleita',
      'tlf' => '55555555',
      'rif' => 'J-123456789',
      'id' => '1'
      ); */
    //var_dump($r);
     $json = json_encode($r, JSON_UNESCAPED_UNICODE);
     //echo json_last_error_msg();
     return $json;
}

//var_dump($_POST);


if (isset($_POST)) {
    $method = $_POST['method'];
    //echo $method;
    if (isset($_POST['parameters'])) {
        $parameters = $_POST['parameters'];
        //var_dump($parameters);
        foreach ($parameters as $key => $value) {
            $p[] = $value;
        }

        echo call_user_func_array($method, $p);
    } else {
        echo ($method());
    }
}