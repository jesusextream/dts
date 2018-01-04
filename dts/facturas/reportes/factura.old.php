<?php
	include '../tools/class_fpdf.php';
	include '../common/general.php';
        require_once '../core/DBManagement.php';
        require_once '../core/DBInspector.php';
        require_once '../core/Configurator.php';
        
        
       
        
        Configurator::getInstance();
	if (isset($_GET['f']) && isset($_GET['i']) && $_GET['p']){
        $pie = json_decode($_GET['p']);
        $tipo_fact = $_GET['f'];
        $invoice = $_GET['i'];
        $bill = '';
        $ship = '';
        $date ='';
        $page =1;
        $order ='';
        $rifEmp = '';
        $razonEmp='';
        $dirEmp ='';
        $tlfEmp = '';
        $rifCli = '';
        $imgType = '';
        $totalPag=1;
        $piePag = $pie->pie;
        $contactName = $pie->contactName;
        $homePhone = $pie->tlfLocal;
        $workPhone = $pie->tlfWork;
        $emailAdr= $pie->email;
        $subTotal='';
        $salesTax='';
        $discount='';
        $freight='';
        $handling='';
        $restoking='';
        $totalSale='';
        
        //$invoice;
        } else {
            header('location: ../');
            exit;
        }
        
        function getPedido($invoice){

            global $ship, $date, $order, $rifEmp, $subTotal, $salesTax, $totalSale, $restoking, $handling, $freight, $discount;
            $sql = "select b.qty, b.num_part, b.descripcion, b.precio_unit, b.sub_total as sub_totalP, a.num_orden,"
                 . " a.fecha_emision, a.cliente_rif, a.empresa_rif, a.sub_total, a.sale_tax,"
                 . " a.discount, a.freight, a.handling, a.restocking, a.total_sale  from pedidos a, detalle_pedido b where a.num_invoice=b.num_invoice"
                    . " and a.num_invoice = '".$invoice."'";
            
            DBManagement::getInstance()->consultar($sql);
            $r = DBManagement::getInstance()->getResultSet();
            
            
            if (is_array($r) && count($r)>0){
                //var_dump($r);
                
                getShip($r[0]['cliente_rif']);
                $date = $r[0]['fecha_emision'];
                $order = $r[0]['num_orden'];
                $rifEmp = $r[0]['empresa_rif'];
                getBill($rifEmp);
                $rifCli = $r[0]['cliente_rif'];
                $subTotal = $r[0]['sub_total'];
                $salesTax = $r[0]['sale_tax'];
                $discount = $r[0]['discount'];
                $freight = $r[0]['freight'];
                $handling = $r[0]['handling'];
                $restoking = $r[0]['restocking'];
                $totalSale = $r[0]['total_sale'];
                

                
                return $r;
                
            } else {
                 header('location: ../');
                 exit;
            }
            
            
        }
        
        
        function getBill($rif) {
            global $bill, $imgType, $razonEmp, $dirEmp, $tlfEmp;
            $sql = "select a.RAZON_SOCIAL, a.DIRECCION, a.rif, b.TELEFONO, a.type_logo from empresa a, empresa_telefono b where a.rif=b.rif and a.rif='".$rif."' limit 1";
            DBManagement::getInstance()->consultar($sql);
            $r = DBManagement::getInstance()->getResultSet();
            
            if (is_array($r) && count($r)>0){

                $bill = $r[0]['RAZON_SOCIAL'] . ", " . $r[0]['rif']. "\n"; 
                $bill .= $r[0]['DIRECCION'] . "\n" . $r[0]['TELEFONO']. "\n";
                
                $razonEmp = $r[0]['RAZON_SOCIAL'];
                //echo $r[0]['type_logo'];
                
                $dirEmp =$r[0]['DIRECCION'];
                $tlfEmp = $r[0]['TELEFONO'];
                
                $imgType = strtoupper(explode('/', $r[0]['type_logo'])[1]);
                 //echo $imgType;
                
            } else {
                 header('location: ../');
                 exit;
            }
            
            
        }
        
        function getShip($rif) {
            global $ship;
            $sql = "select a.RAZON_SOCIAL, a.DIRECCION, a.rif, b.TELEFONO from cliente a, cliente_telefono b where a.rif=b.rif and a.rif='".$rif."' limit 1";
            //echo $sql;
            DBManagement::getInstance()->consultar($sql);
            $r = DBManagement::getInstance()->getResultSet();
            
            if (is_array($r) && count($r)>0){
                //var_dump($r);
                
                $ship = ''. $r[0]['RAZON_SOCIAL'] . ", " . $r[0]['rif']. "\t\t"; 
                $ship .= $r[0]['DIRECCION'] . "\t" . $r[0]['TELEFONO']. "";
                
            } else {
                 header('location: ../');
                 exit;
            }
            
        }
        
        
        function getImg($e) {
            
            $sql = "select logo, type_logo from empresa where rif='".$e."'";
    
            DBManagement::getInstance()->consultar($sql);
            $r = DBManagement::getInstance()->getResultSet();

            if (is_array($r) && count($r) > 0){

                //header("Content-type: ".$r[0]['type_logo']);
                echo $r[0]['logo'];


            } else {
                $r = FALSE;
                echo "";
            }

         }
	
	function put_header($pdf,$tipo_fact){
            global $ship, $date, $invoice, $page, $order, $rifEmp, $imgType, $totalPag, $razonEmp, $dirEmp, $tlfEmp;
           
		switch($tipo_fact){
			case "A":
				$pdf->Image('http://localhost/facturasdinamicas/tools/readImg.php?e='.$rifEmp,90,5,45,35,$imgType);	
                                //$pdf->MemImage('../tools/readImg.php?e='.$invoice, 50, 30);
				$pdf->SetXY(15,10);
				$pdf->Multicell(70,5,utf8_decode($razonEmp),'','',FALSE);
				$pdf->SetXY(15,15);
				$pdf->SetFont('Arial','',10);
				$pdf->Multicell(70,5,utf8_decode($dirEmp),'','',FALSE);
				$pdf->SetXY(15,20);
				$pdf->SetFont('Arial','',8);
				$pdf->Multicell(70,5,utf8_decode($tlfEmp),'','',FALSE);
				break;
			case "B":
				$pdf->Image('http://localhost/facturasdinamicas/tools/readImg.php?e='.$rifEmp,155,5,45,35,$imgType);
				$pdf->SetFont('Arial','B',12);
				$pdf->SetXY(15,10);
				$pdf->Multicell(70,5,utf8_decode($razonEmp),'','',FALSE);
				$pdf->SetXY(15,15);
				$pdf->SetFont('Arial','',10);
				$pdf->Multicell(70,5,utf8_decode($dirEmp),'','',FALSE);
				$pdf->SetXY(15,20);
				$pdf->SetFont('Arial','',8);
				$pdf->Multicell(70,5,utf8_decode($tlfEmp),'','',FALSE);
				break;
			default:
				$pdf->Image('http://localhost/facturasdinamicas/tools/readImg.php?e='.$rifEmp,15,5,45,35,$imgType);	
				$pdf->SetXY(60,10);
				$pdf->SetFont('Arial','B',12);
				$pdf->Multicell(70,5,utf8_decode($razonEmp),'','',FALSE);
				$pdf->SetXY(60,15);
				$pdf->SetFont('Arial','',10);
				$pdf->Multicell(70,5,utf8_decode($dirEmp),'','',FALSE);
				$pdf->SetXY(60,20);
				$pdf->SetFont('Arial','',8);
				$pdf->Multicell(70,5,utf8_decode($tlfEmp),'','',FALSE);
				break;
		}
		
		
		
		$pdf->SetXY(15,35);
		$pdf->SetLineWidth(0.8);
		$pdf->Multicell(70,5,utf8_decode(''),'B','',FALSE);

		$pdf->SetLineWidth(0.1);
		$pdf->SetXY(85,35);
		$pdf->Multicell(30,5,utf8_decode('DATE'),1,'C',FALSE);
		$pdf->SetXY(115,35);
		$pdf->Multicell(30,5,utf8_decode('PAGE'),1,'C',FALSE);
		$pdf->SetXY(145,35);
		$pdf->Multicell(30,5,utf8_decode('ORDER #'),1,'C',FALSE);
		$pdf->SetXY(175,35);
		$pdf->Multicell(30,5,utf8_decode('INVOICE #'),1,'C',FALSE);
		
		$pdf->SetXY(85,40);
		$pdf->Multicell(30,5,utf8_decode($date),1,'C',FALSE);
		$pdf->SetXY(115,40);
		$pdf->Multicell(30,5,utf8_decode('Page '.$pdf->PageNo().''),1,'C',FALSE);
		$pdf->SetXY(145,40);
		$pdf->Multicell(30,5,utf8_decode($order),1,'C',FALSE);
		$pdf->SetXY(175,40);
		$pdf->Multicell(30,5,utf8_decode($invoice),1,'C',FALSE);
		
		$pdf->SetXY(15,50);
                $pdf->SetFont('Arial','B',8);
                $pdf->Multicell(85,4,utf8_decode('BILL TO:'),0,'',FALSE);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY(15,54);
		$pdf->Multicell(85,4,utf8_decode('' . $ship),0,'',FALSE);
		$pdf->SetXY(120,50);
                $pdf->SetFont('Arial','B',8);
		$pdf->Multicell(85,4,utf8_decode('SHIP TO:'),0,'',FALSE);
                $pdf->SetFont('Arial','',8);
                $pdf->SetXY(120,54);
                $pdf->Multicell(85,4,utf8_decode('' . $ship),0,'',FALSE);
	}
	
	function put_t_header($pdf){
		$pdf->SetXY(15,80);
		$pdf->Multicell(10,5,utf8_decode('QTY'),1,'',FALSE);
		$pdf->SetXY(25,80);
		$pdf->Multicell(140,5,utf8_decode('Description'),1,'C',FALSE);
		$pdf->SetXY(165,80);
		$pdf->Multicell(20,5,utf8_decode('UnitPrice'),1,'C',FALSE);
		$pdf->SetXY(185,80);
		$pdf->Multicell(20,5,utf8_decode('Total'),1,'C',FALSE);
	}
        
        function put_t_footer($pdf){
		$pdf->SetXY(15,80);
		$pdf->Multicell(10,5,utf8_decode('QTY'),1,'',FALSE);
		$pdf->SetXY(25,80);
		$pdf->Multicell(140,5,utf8_decode('Description'),1,'C',FALSE);
		$pdf->SetXY(165,80);
		$pdf->Multicell(20,5,utf8_decode('UnitPrice'),1,'C',FALSE);
		$pdf->SetXY(185,80);
		$pdf->Multicell(20,5,utf8_decode('Total'),1,'C',FALSE);
	}
	
        
        $result = getPedido($invoice);
        
       // getShip($rifCli);
        // echo $imgType;
        
	$pdf = new PDF('P','mm','Letter');	
	$pdf->SetMargins(0,0,0,0);
	$pdf->SetAutoPageBreak(false, 0.0);
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);
	
	put_header($pdf,$tipo_fact);
	put_t_header($pdf);
	
	$x_pos = 15;
	$y_pos = 85;	
	
	$t_items = 100;
	$c_item = 0;
	for($i=0;$i<count($result);$i++){
		
		if($c_item== 35){
                        $page++;
			$pdf->AddPage();
			put_header($pdf, $tipo_fact);
			put_t_header($pdf);			
			$y_pos = 85;
			$c_item=0;
		}
		
		$pdf->SetXY($x_pos,$y_pos);
		$pdf->Multicell(10,5,utf8_decode($result[$i]['qty']),1,'',FALSE);
		
		$pdf->SetXY($x_pos + 10,$y_pos);
		$pdf->Multicell(140,5,utf8_decode($result[$i]['num_part'] . ' - ' . $result[$i]['descripcion']),1,'C',FALSE);
		
		$pdf->SetXY($x_pos + 150,$y_pos);		
		$pdf->Multicell(20,5,utf8_decode($result[$i]['precio_unit']),1,'C',FALSE);
		
		$pdf->SetXY($x_pos + 170,$y_pos);
		$pdf->Multicell(20,5,utf8_decode($result[$i]['sub_totalP']),1,'C',FALSE);
		$y_pos += 5;
		$c_item++;
	}
	
                
                $y_pos += 5;
                $pdf->SetXY(15,$y_pos);
                $yOrg = $y_pos;
                $y_pos += 5;
                $pdf->Multicell(150,5,utf8_decode('Notas'),1,'C',FALSE);
                $pdf->SetXY(15,$y_pos);
                $y_pos += 20;
		$pdf->Multicell(150,20,utf8_decode($piePag),1,'',FALSE);
		$pdf->SetXY(15,$y_pos);
                $y_pos += 5;
		$pdf->Multicell(40,5,utf8_decode('Nombre de Contacto'),1,'C',FALSE);
		$pdf->SetXY(15,$y_pos);
                
		$pdf->Multicell(40,5,utf8_decode($contactName),1,'',FALSE);
                
                $pdf->SetXY(55,$y_pos-5);
                $pdf->Multicell(30,5,utf8_decode('Tlf Local'),1,'C',FALSE);
                $pdf->SetXY(55,$y_pos);
                $pdf->Multicell(30,5,utf8_decode($homePhone),1,'',FALSE);
                
                $pdf->SetXY(85,$y_pos-5);
                $pdf->Multicell(30,5,utf8_decode('Tlf de Trabajo'),1,'C',FALSE);
                $pdf->SetXY(85,$y_pos);
                $pdf->Multicell(30,5,utf8_decode($workPhone),1,'',FALSE);
                
                $pdf->SetXY(115,$y_pos-5);
                $pdf->Multicell(50,5,utf8_decode('Email'),1,'C',FALSE);
                $pdf->SetXY(115,$y_pos);
                $pdf->Multicell(50,5,utf8_decode($emailAdr),1,'',FALSE);
                
		$pdf->SetXY(165,$yOrg);
                $pdf->Multicell(20,5,utf8_decode('Sub-Total'),1,'R',FALSE);
                $pdf->SetXY(185,$yOrg);
                $pdf->Multicell(20,5,utf8_decode($subTotal),1,'R',FALSE);
                $yOrg += 5;
                $pdf->SetXY(165,$yOrg);
                $pdf->Multicell(20,5,utf8_decode('Impuesto'),1,'R',FALSE);
                $pdf->SetXY(185,$yOrg);
                $pdf->Multicell(20,5,utf8_decode($salesTax),1,'R',FALSE);
                $yOrg += 5;
                $pdf->SetXY(165,$yOrg);
                $pdf->Multicell(20,5,utf8_decode('Descuento'),1,'R',FALSE);
                $pdf->SetXY(185,$yOrg);
                $pdf->Multicell(20,5,utf8_decode($discount),1,'R',FALSE);
                $yOrg += 5;
                $pdf->SetXY(165,$yOrg);
                $pdf->Multicell(20,5,utf8_decode('Carga'),1,'R',FALSE);
                $pdf->SetXY(185,$yOrg);
                $pdf->Multicell(20,5,utf8_decode($discount),1,'R',FALSE);
                $yOrg += 5;
                $pdf->SetXY(165,$yOrg);
                $pdf->Multicell(20,5,utf8_decode('Entrega'),1,'R',FALSE);
                $pdf->SetXY(185,$yOrg);
                $pdf->Multicell(20,5,utf8_decode($handling),1,'R',FALSE);
                $yOrg += 5;
                $pdf->SetXY(165,$yOrg);
                $pdf->Multicell(20,5,utf8_decode('Reposición'),1,'R',FALSE);
                $pdf->SetXY(185,$yOrg);
                $pdf->Multicell(20,5,utf8_decode($restoking),1,'R',FALSE);
                
                $yOrg += 5;
                $pdf->SetXY(165,$yOrg);
                $pdf->Multicell(20,5,utf8_decode('Total'),1,'R',FALSE);
                $pdf->SetXY(185,$yOrg);
                $pdf->Multicell(20,5,utf8_decode($totalSale),1,'R',FALSE);
		
                

	
	$pdf->Output();
?>