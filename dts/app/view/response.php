<?php
	//include connection file 
$serverName2 = "50.196.74.121"; //serverName\instanceName
$connectionInfo2 = array( "Database"=>"AFM_A", "UID"=>"querysys", "PWD"=>"tex5740");
$conn = sqlsrv_connect( $serverName2, $connectionInfo2);

	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 =>'co_art',
		1 =>'art_des', 
		2 => 'modelo',
		3 => 'ref'
	);
	$where = $sqlTot = $sqlRec = "";

	// check search value exist
	if( !empty($params['search']['value']) ) {   
		$where .=" WHERE ";
		$where .=" ( co_art LIKE '".$params['search']['value']."%' ";    
		$where .=" OR art_des LIKE '".$params['search']['value']."%' ";

		$where .=" OR modelo LIKE '".$params['search']['value']."%' )";
	}

	// getting total number records without any search

	$sql = "SELECT * FROM art";
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}
 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

	$queryTot = sqlsrv_query($conn, $sqlTot);

	$totalRecords = sqlsrv_fetch_array($queryTot, SQLSRV_FETCH_ASSOC);

	$queryRecords = sqlsrv_query($conn, $sqlRec);
//var_dump($totalRecords);
//var_dump($queryTot);
	//iterate on results row and create new index array of data
	while( $row = sqlsrv_fetch_array($queryRecords, SQLSRV_FETCH_ASSOC) ) { 
		$data[] = $row;
	}	

	$json_data = array(   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>