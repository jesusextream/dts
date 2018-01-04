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
//VARIABLES DE LA VISTA.
//foreach ($_GET as $i_dato => $dato_){ $$i_dato = $obj_function->evalua_array($_GET,$i_dato); }
foreach ($_POST as $i_dato => $dato_){ 
    $$i_dato = $obj_function->evalua_array($_POST,$i_dato);
}

$DB = $catalogo_db;

$serverName = "iskro.no-ip.org"; //serverName\instanceName
$connectionInfo = array( "Databases"=>$DB, "UID"=>"roy", "PWD"=>"ry2016");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

$serverName2 = "50.196.74.121"; //serverName\instanceName
$connectionInfo2 = array( "Database"=>"AFM_A", "UID"=>"querysys", "PWD"=>"tex5740");
$conn2 = sqlsrv_connect( $serverName2, $connectionInfo2);
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
    case 'ArtBusca':
        $mss = '';
        $salida = ''; 
        if($conn2){
            $where = " WHERE 1=1";
            //var_dump($catalogo_subcategoria);
            if($co_art != ''){
                $where .= " AND ( co_art LIKE '".$co_art."%') ";
            }
            if ($catalogo_ventaFrom != ''){
                $whereSub= " AND (fec_emis BETWEEN  convert(datetime, '".$catalogo_ventaFrom." 00:00:00', 120)
                    AND  convert(datetime, '".$catalogo_ventaTo." 23:59:59', 120))";
            }

            $qwerydb3 = "SELECT prov.prov_des AS Nombre_p, descrip AS descripcion,o.fact_num as PO_Num,('') as Fact_Num,(CASE WHEN o.status='0' THEN 'Abierta' WHEN o.status='1' THEN 'Procesando' ELSE'Facturada' END) AS statusname,
                 fec_emis AS F_orden,r_ord.co_art,Cantidad,Precio FROM ordenes o 
                JOIN prov  ON prov.co_prov = o.co_cli
                 JOIN
                (SELECT  RTRIM(r_ord.co_art) AS co_art,total_art AS Cantidad, prec_vta AS Precio,
                fact_num FROM reng_ord
                 r_ord WHERE 1=1 ) AS r_ord ON o.fact_num = r_ord.fact_num  $where $whereSub";
                
            $result = sqlsrv_query( $conn2, $qwerydb3);
            //var_dump($qwerydb3);
            //echo $qwerydb3;
            while ($row = sqlsrv_fetch_array($result)) { 
                
                if (!in_array($row['co_art'], $arrayco_art)) {
                        $rows['co_art'] = $row['co_art'];
                        $rows['Nombre_p'] = utf8_encode($row['Nombre_p']);
                        $rows['descripcion'] = utf8_encode($row['descripcion']);
                        $rows['PO_Num'] = utf8_encode($row['PO_Num']);
                        $rows['statusname'] = utf8_encode($row['statusname']);
                        $rows['F_orden'] = $row['F_orden']->format('Y-m-d');
                        $rows['Cantidad'] = $row['Cantidad'];
                        $rows['Precio'] = $row['Precio'];
                        $out[$j] = $rows;
                    
                    //echo '<----------->';var_dump($co_art);
                    $j++;
                }
                else{
                  $idFind = array_search($row['co_art'], array_column($out  , 'co_art'));
                }
            }

            $salida = $out;
            if($j ==0){
                $mss = 'NO SE ENCONTRARON ARTICULOS. ';
            }else{
                $mss = 1;
            }
            // Limpieza
            sqlsrv_free_stmt($result);
        }else{ $mss = 'ERROR EN CONEXION CON LA BD1.'; }

        $resp = array('mss' => utf8_encode($mss), 'salida' => ($salida));
        echo (json_encode($resp));

    break;
    case 'catalogoArtBusca':
        $mss = '';
        $salida = '';    
        

        //if($conn2){echo 'ERROR EN CONEXION CON LA BD2.';}
        if($conn){
            $whereSub = "";
            $join = "LEFT";
            $where = " WHERE 1=1";
            $whereSta = " AND co_art LIKE '".$co_art."%' AND co_alma LIKE '01%'";
            //var_dump($catalogo_subcategoria);
            if($catalogo_subcategoria != ''){ 
                $where.= " AND co_lin IN (".$catalogo_subcategoria.")"; 
            }
            if($co_art != '' && $catalogo_ventaFrom == ''){
                $where.= " AND art.co_art LIKE '".$co_art."%'";
                $qwery = "SELECT RTRIM(art.co_art) AS co_art, art.art_des, sta.stock_act, 
                (RTRIM(art.modelo) + ' - ' + RTRIM(art.ref)) AS ModRef, 0 AS pedidos, art.co_art,
                0 AS QtySales, 0 AS QtyCustumers, 0 AS QtyInvoice, 0 AS prec_vta2
                FROM art
                LEFT JOIN (SELECT stock_act, RTRIM(co_art) AS co_art  FROM st_almac 
                        WHERE 1=1 $whereSta
                    ) AS sta ON art.co_art = sta.co_art
                $where";
                
            }
            if ($catalogo_ventaFrom != ''){
                if($co_art != ''){
                    $where.= " AND art.co_art LIKE '".$co_art."%'";
                    $whereSub.= " AND rf.co_art LIKE '".$co_art."%'";
                }

                $qwery = "SELECT RTRIM(art.co_art) AS co_art, art.art_des, sta.stock_act, 
                (RTRIM(art.modelo) + ' - ' + RTRIM(art.ref)) AS ModRef, 0 AS pedidos, art.co_art,
                SUB.QtySales, SUB.QtyCustumers, SUB.QtyInvoice, SUB.prec_vta2
                FROM art
                LEFT JOIN (SELECT stock_act, RTRIM(co_art) AS co_art  FROM st_almac 
                        WHERE 1=1 $whereSta
                    ) AS sta ON art.co_art = sta.co_art
                LEFT JOIN (
                    SELECT count(fa.fact_num) AS QtySales, COUNT(DISTINCT fa.co_cli) AS QtyCustumers, 
                    COUNT(DISTINCT rf.fact_num) AS QtyInvoice, AVG(rf.prec_vta2) AS prec_vta2, RTRIM(rf.co_art) AS co_art
                    FROM [factura] fa 
                    JOIN reng_fac rf ON fa.fact_num = rf.fact_num 
                    WHERE 1=1  AND (fa.fec_emis BETWEEN  convert(datetime, '".$catalogo_ventaFrom." 00:00:00', 120)
                    AND  convert(datetime, '".$catalogo_ventaTo." 23:59:59', 120)) $whereSub
                    GROUP BY co_art
                ) AS SUB ON art.co_art = SUB.co_art $where";

            }

            $result = sqlsrv_query( $conn, $qwery);
            //echo ($qwery);
            //echo DBHOST2.", ".DBUSER2.", ".DBPASS2.", ".DBNOM2;
            //var_dump($DB);
            //var_dump($conn);
            $j = 0;
            $arrayco_art = array();
            $mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
            while ($row = sqlsrv_fetch_array($result)) { 
                $qwerySub = "SELECT SUM(rp.total_art) AS pedidos
                    FROM [factura] fa 
                    JOIN reng_fac rf ON fa.fact_num = rf.fact_num 
                    LEFT JOIN reng_ped rp ON rf.reng_doc = rp.reng_doc
                    AND rf.num_doc = rp.num_doc
                    AND rf.co_art = rp.co_art $where 
                    GROUP BY rf.co_art, art.art_des, sta.stock_act";
                //$resultSub = sqlsrv_query( $conn, $qwerySub);
                //echo "$qwerySub<br>";



                if(!$mysqli->connect_error){
                  //inventory dts skuNo Qty
                  //consulta sqlserver
                  $qwerydb4 = "SELECT RTRIM(art.co_art) AS co_art, art.art_des, art.stock_act, co_art_ord 
                    FROM art 
                    LEFT JOIN (SELECT  RTRIM(co_art) AS co_art_ord  FROM reng_ord 
                            JOIN ordenes ON ordenes.fact_num = reng_ord.fact_num
                            WHERE 1=1 
                            AND status=0
                            OR status=1
                            AND co_art LIKE '".$row['co_art']."%'
                        ) AS r_ord ON RTRIM(art.co_art) = r_ord.co_art_ord
                    WHERE 1=1
                    AND art.co_art LIKE '".$row['co_art']."%'";
                    unset($co_art_ord);
                    $resultOsub = sqlsrv_query( $conn2, $qwerydb4);
                    //var_dump($resultOsub);
                    while ($rowSub = sqlsrv_fetch_array($resultOsub)) { 
                      $co_art_ord = $rowSub['co_art_ord'];
                      //echo "<<>>".$rowSub['co_art_ord']."<<>>";
                    }
                    sqlsrv_free_stmt($resultOsub);
                  //var_dump($qwerydb2);
                  //inventory dts skuNo Qty
                  $ordersDetail = $obj_bdmysql->select(
                      "`inventory dts` id 
                      JOIN Inventory i ON id.SkuNo = i.SkuNo", 
                      "id.SkuNo, MAX(Qty) AS QtyAviDTS", 
                      "id.PartNo_DTS = '".$row['co_art']."'", 
                      "",
                      "",
                      $mysqli);
                      //echo "<<>>".$row['co_art']."<<>>";

                  if (is_array($ordersDetail)){
                    if (is_null($ordersDetail[0]['SkuNo'])){
                      $row['QtyAviDTS'] = 0;
                    }else{
                      $row['QtyAviDTS'] = intval($ordersDetail[0]['QtyAviDTS']);
                    }
                  }
                  $row['art_des'] = trim($row['art_des']);
                }
                $rows['QtySales'] = $row['QtySales']==null?0:$row['QtySales'];
                $rows['QtyCustumers'] = $row['QtyCustumers']==null?0:$row['QtyCustumers'];
                $rows['QtyInvoice'] = $row['QtyInvoice']==null?0:$row['QtyInvoice'];
                $rows['co_art'] = utf8_encode(trim($row['co_art']));
                $arrayco_art[] = trim($rows['co_art']);
                $rows['prec_vta2'] = $row['prec_vta2']==null?0:$row['prec_vta2'];
                $rows['art_des'] = utf8_encode(trim($row['art_des']));
                $rows['stock_act'] = $row['stock_act'];
                $rows['ModRef'] = utf8_encode(trim($row['ModRef']));
                $rows['QtyAviDTS'] = $row['QtyAviDTS'];
                $rows['pedidos'] = $row['pedidos'];
                $rows['db'] = 'ISKRO';
                $rows['co_art_ord'] = $co_art_ord;
                $out[$j] = $rows;
                //echo '<----------->';var_dump($rows);
                $j++;

            }

            $qwerydb2 = "SELECT RTRIM(art.co_art) AS co_art, art.art_des, art.stock_act
                FROM art
                $join JOIN (
                    SELECT count(fa.fact_num) AS QtySales, COUNT(DISTINCT fa.co_cli) AS QtyCustumers, 
                    COUNT(DISTINCT rf.fact_num) AS QtyInvoice, AVG(rf.prec_vta2) AS prec_vta2, RTRIM(rf.co_art) AS co_art
                    FROM [factura] fa 
                    JOIN reng_fac rf ON fa.fact_num = rf.fact_num 
                    WHERE 1=1 $whereSub
                    GROUP BY co_art
                ) AS SUB ON art.co_art = SUB.co_art
                WHERE 1=1
                AND art.co_art LIKE '".$co_art."%'";
            //echo ($qwerydb2);

            $result = sqlsrv_query( $conn2, $qwerydb2);
            //echo ($qwery);
            //var_dump($DB);
            //var_dump($arrayco_art);
            while ($row = sqlsrv_fetch_array($result)) {

                if (!in_array(trim($row['co_art']), $arrayco_art)) {

                    if(!$mysqli->connect_error){
                      //inventory dts skuNo Qty
                  $qwerydb5 = "SELECT RTRIM(art.co_art) AS co_art, art.art_des, art.stock_act, co_art_ord 
                    FROM art 
                    LEFT JOIN (SELECT  RTRIM(co_art) AS co_art_ord  FROM reng_ord 
                            JOIN ordenes ON ordenes.fact_num = reng_ord.fact_num
                            WHERE 1=1 
                            AND status=0
                            OR status=1
                            AND co_art LIKE '".$row['co_art']."%'
                        ) AS r_ord ON RTRIM(art.co_art) = r_ord.co_art_ord
                    WHERE 1=1
                    AND art.co_art LIKE '".$row['co_art']."%'";
                    unset($co_art_ord);
                    $resultOsub = sqlsrv_query( $conn2, $qwerydb5);
                    //var_dump($resultOsub);
                    while ($rowSub = sqlsrv_fetch_array($resultOsub)) { 
                      $co_art_ord = $rowSub['co_art_ord'];
                      //echo "<<>>".$rowSub['co_art_ord']."<<>>";
                    }
                    sqlsrv_free_stmt($resultOsub);
                      $ordersDetail = $obj_bdmysql->select(
                          "`inventory dts` id 
                          JOIN Inventory i ON id.SkuNo = i.SkuNo", 
                          "id.SkuNo, MAX(Qty) AS QtyAviDTS", 
                          "id.PartNo_DTS = '".$row['co_art']."'", 
                          "",
                          "",
                          $mysqli);
                          //echo "<<>>".$row['co_art']."<<>>";

                      if (is_array($ordersDetail)){
                        if (is_null($ordersDetail[0]['SkuNo'])){
                          $row['QtyAviDTS'] = 0;
                        }else{
                          $row['QtyAviDTS'] = intval($ordersDetail[0]['QtyAviDTS']);
                        }
                      }
                      $row['art_des'] = trim($row['art_des']);
                    }
                    
                        $rows['QtySales'] = 0;
                        $rows['QtyCustumers'] = 0;
                        $rows['QtyInvoice'] = 0;
                        $rows['co_art'] = $row['co_art'];
                        $rows['prec_vta2'] =0;
                        $rows['art_des'] = utf8_encode($row['art_des']);
                        $rows['stock_act'] = $row['stock_act'];
                        $rows['ModRef'] = '';
                        $rows['QtyAviDTS'] = $row['QtyAviDTS'];
                        $rows['pedidos'] = 0;
                        $rows['db'] = 'DTS';
                        $rows['co_art_ord'] = $co_art_ord;
                        $out[$j] = $rows;
                    
                          //echo "<<>>".$row['co_art_ord']."<<>>";
                    //echo '<----------->';var_dump($co_art);
                    $j++;
                }
                else{
                  $idFind = array_search($row['co_art'], array_column($out  , 'co_art'));
                  $out[$idFind]['QtyAviDTS'] = round($row['stock_act']);
                }
            }

            $salida = $out;
            if($j ==0){
                $mss = 'NO SE ENCONTRARON ARTICULOS. ';
            }else{
                $mss = 1;
            }
            // Limpieza
            sqlsrv_free_stmt($result);
        }else{ $mss = 'ERROR EN CONEXION CON LA BD1.'; }

        $resp = array('mss' => utf8_encode($mss), 'salida' => ($salida));
        echo (json_encode($resp));
    break;
    default :
        echo json_encode(array('mss' => utf8_encode('NO SE IDENTIFICO LA SOLICITUD. '.$opc.'.'), 'salida' => utf8_encode('')));
    break;
}
sqlsrv_close($conn);
sqlsrv_close($conn2);