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

$mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
switch ($opc){
    case 'searchOrder':
        $where = "OrdNo = '".$OrdNo."'  GROUP BY od.OrdID";
        $resul = $obj_bdmysql->select(
            "orders o 
            JOIN `customers bill` cb ON o.CustID = cb.CustID
            JOIN `orders detail` od ON o.OrdID = od.OrdID",
            "o.OrdID, cb.BillName, group_CONCAT(DISTINCT(od.QtyReserve)) AS QtyReserve ",
            $where,
            "",
            "",
            $mysqli);
        //var_dump($resul);
        if(!is_array($resul)){ 
            $salida[mss] = 'NO SE ENCONTRO LA ORDEN'; 
        }else {
            $salida[mss] = '1';
            $salida[OrdNo] = $OrdNo;
            $salida[OrdID] = $resul[0]['OrdID'];
            $salida[BillName] = $resul[0]['BillName'];
            $salida[QtyReserve] = $resul[0]['QtyReserve'];
            if ($resul[0]['QtyReserve'] == '0'){
                $salida[option] = 'start';
            }else{
                $salida[option] = 'edit';
            }
        }
        echo json_encode($salida);
    break;
    case 'saveOrderDetail':
        //update($tabla,$campo,$where,$mysqli
        $campo = "QtyReserve = '".$QtyReserve."', QtyDts = '".$QtyDts."'";
        $where = "LineID = '".$LineID."'";
        $saveOrderDetail = $obj_bdmysql->update(
            "`orders detail`",
            $campo,
            $where,
            $mysqli);
        //var_dump($saveOrderDetail);
        if($saveOrderDetail  == '1'){
            $totalOrdersDetail = $obj_bdmysql->select("`orders detail`", 
                "COUNT(`LineID`) count, SUM(`Ord`) countItems, SUM(`QtyReserve`) totalQtyReserve, SUM(`QtyDts`) totalQtyDts", 
                "OrdID=" . $OrdID, 
                "", 
                "",
                $mysqli);
            $salida[count] = $totalOrdersDetail[0]['count'];
            $salida[countItems] = $totalOrdersDetail[0]['countItems'];
            $salida[totalQtyReserve] = $totalOrdersDetail[0]['totalQtyReserve'];
            $salida[totalQtyDts] = $totalOrdersDetail[0]['totalQtyDts'];

            $totalColumn = $obj_bdmysql->select("`orders detail`", 
                "COUNT(`LineID`) totalQtyProducts", 
                "OrdID=" . $OrdID . " AND (QtyReserve >0 OR QtyDts > 0) ", 
                "", 
                "",
                $mysqli);

            $salida[totalQtyProducts] = $totalColumn[0]['totalQtyProducts'];

            $salida[mss] = '1';
            $salida[LineID] = $LineID;
            $salida[QtyReserve] = $QtyReserve;
            $salida[QtyDts] = $QtyDts;
        }else {
            $salida[mss] = 'NO SE GUARDO CORRECTAMENTE'; 
        }
        echo json_encode($salida);
    break;
    default :
        echo json_encode(array('mss' => utf8_encode('NO SE IDENTIFICO LA SOLICITUD. '.$opc.'.'), 'salida' => utf8_encode('')));
    break;
}
