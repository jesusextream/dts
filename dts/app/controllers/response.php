
<?php
	//include connection file 
	 
include '../../common/general.php';
	// initilize all variable
$connectionInfo2 = array( "Database"=>"AFM_A", "UID"=>"querysys", "PWD"=>"tex5740");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
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
	$sql = "SELECT * FROM `art` ";
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}


 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

	$queryTot = mysqli_query($conn, $sqlTot) or die("database error:". mysqli_error($conn));


	$totalRecords = mysqli_num_rows($queryTot);

	$queryRecords = mysqli_query($conn, $sqlRec) or die("error to fetch employees data");

	//iterate on results row and create new index array of data
	while( $row = mysqli_fetch_row($queryRecords) ) { 
		$data[] = $row;
	}	

	$json_data = array(   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	