<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../common/general.php';
$obj_function = new coFunction();
$obj_bdmysql = new coBdmysql();
$cod_usuario = $_SESSION["cod_usuario"];

$serverName = "iskro.no-ip.org"; //serverName\instanceName
$connectionInfo = array( "Database"=>"ISKRO_A", "UID"=>"roy", "PWD"=>"ry2016");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
//VARIABLES DE LA VISTA.
//foreach ($_GET as $i_dato => $dato_){ $$i_dato = $obj_function->evalua_array($_GET,$i_dato); }
foreach ($_POST as $i_dato => $dato_){ 
    $$i_dato = $obj_function->evalua_array($_POST,$i_dato);
}

switch ($opc){
    case 'carga_subcategoria':        
        if ($conn) {
            //$salida = '<option value="">Seleccione...</option>';
            $salida = '';$j = 0;

            $qwery = "SELECT co_subl,subl_des FROM sub_lin WHERE co_lin = '".$co_lin."'";
            $resultSubCategoryList = sqlsrv_query($conn, $qwery); 

            while( $row = sqlsrv_fetch_array($resultSubCategoryList) ) {
              $salida.='<input type="checkbox" id="catalogo_ch_'.trim($row['co_subl']).'" value="'.trim($row['co_subl']).'"> <label for="catalogo_ch_'.trim($row['co_subl']).'">'.trim($row['subl_des']).'</label> <br>';
              $j++;
            }

            $CountCategory = $j;
            sqlsrv_free_stmt($resultSubCategoryList);

            if($CountCategory == 0){
                $mss = 'NO SE ENCONTRARON DATOS';
            }else{
                $mss = '1';
            }
        }else{ $mss = 'ERROR EN CONEXION CON LA BD.'; }
        $resp = array('mss' => utf8_encode($mss), 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    //BUSCA ARTICULOS
    case 'catalogoArtBusca':
        $mss = '';
        $salida = '';    

        if($conn){
            $where = " WHERE 1=1";        
            if($catalogo_categoria != ''){
                $where.= " AND ( co_lin = '".$catalogo_categoria."' ) "; 
            }
            //var_dump($catalogo_subcategoria);
            if($catalogo_subcategoria != ''){ 
                $where.= " AND ( co_subl IN (".$catalogo_subcategoria.") ) "; }
            if ($catalogo_ventaFrom != ''){
                $where .= " AND (fa.fec_emis BETWEEN  convert(datetime, '".$catalogo_ventaFrom." 00:00:00', 120)
                    AND  convert(datetime, '".$catalogo_ventaTo." 23:59:59', 120))";
            }

            $qwery = "SELECT count(*) AS QtySales, COUNT(DISTINCT fa.co_cli) AS QtyCustumers, 
                COUNT(DISTINCT rf.fact_num) AS QtyInvoice, rf.co_art, AVG(rf.prec_vta2) AS prec_vta2, art.art_des, art.stock_act 
                FROM [factura] fa 
                JOIN reng_fac rf ON fa.fact_num = rf.fact_num 
                JOIN art ON rf.co_art = art.co_art $where GROUP BY rf.co_art, art.art_des, art.stock_act";
            $result = sqlsrv_query( $conn, $qwery);
            //echo  $qwery;

            $j = 0;
            while ($row = sqlsrv_fetch_array($result)) {
                $mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
                if(!$mysqli->connect_error){
                  //inventory dts skuNo Qty
                  $ordersDetail = $obj_bdmysql->select(
                      "`inventory dts` id 
                      JOIN Inventory i ON id.SkuNo = i.SkuNo", 
                      "id.SkuNo, MAX(Qty) AS QtyAviDTS, (`OnHand` - `InPick`) AS QtyAviTex", 
                      "id.PartNo_DTS = '".$row['co_art']."'", 
                      "",
                      "",
                      $mysqli);

                  if (is_array($ordersDetail)){
                    if (is_null($ordersDetail[0]['SkuNo'])){
                      $row['QtyAviDTS'] = 0;
                      $row['QtyAviTex'] = 0;
                    }else{
                      $row['QtyAviDTS'] = intval($ordersDetail[0]['QtyAviDTS']);
                      $row['QtyAviTex'] = intval($ordersDetail[0]['QtyAviTex']);
                    }
                  }
                  $row['art_des'] = trim($row['art_des']);
                }
                $out[$j] = $row;
                $j++;
            }
            $salida = $out;
            if($j ==0){
                $mss = 'NO SE ENCONTRARON ARTICULOS. ';
            }else{
                $mss = 1;
            }
            // Limpieza
            sqlsrv_free_stmt($result);
        }else{ $mss = 'ERROR EN CONEXION CON LA BD.'; }

        $resp = array('mss' => utf8_encode($mss), 'salida' => ($salida));
        echo (json_encode($resp));
    break;
    default :
        echo json_encode(array('mss' => utf8_encode('NO SE IDENTIFICO LA SOLICITUD. '.$opc.'.'), 'salida' => utf8_encode('')));
    break;
}
