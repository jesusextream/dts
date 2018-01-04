<?php	
	header('Content-Type: application/json');
include '../common/general.php';
require_once '../core/DBManagement.php';
require_once '../core/DBInspector.php';
require_once '../core/Configurator.php';
Configurator::getInstance();
$obj_function = new coFunction();
$obj_bdmysql = new coBdmysql();
#$cod_usuario = $_SESSION["cod_usuario"];
if( !isset($_POST['functionname']) ) { $aResult['error'] = 'NO SE ENVIO UNA FUNCION!'; }
if( !isset($_POST['arguments']) ) { $aResult['error'] = 'NO SE ENVIO NINGUN ARGUMENTO DE LA FUNCION!'; }
if( !isset($aResult['error']) ) {
switch($_POST['functionname']) {
case 'agregar_cliente': /* 0=>RIF, 1=>RAZON SOCIAL, 2=> DIRECCI&#65533;N, 3=>TELEFONO */
if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
$aResult['error'] = 'ARGUMENTOS INCOMPLETOS!';
}else{
$mysqli = new mysqli(DBManagement::getInstance()->getHost(), DBManagement::getInstance()->getUser(), DBManagement::getInstance()->getPass(), DBManagement::getInstance()->getDbName());
//$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
if (!$mysqli->connect_error){
$where = "rif= '" . $_POST['arguments']['rif'] . "'";
$SQL = "SELECT COUNT(rif) FROM cliente WHERE " . $where;
// var_dump($SQL);
$resul = $mysqli->query($SQL);
$r = $resul->fetch_array(MYSQLI_ASSOC);
if($r['COUNT(rif)'] == 0){
$campo = "rif,razon_social,direccion";
$valor = "'" . $_POST['arguments']['rif'] . "','" . $_POST['arguments']['razon_social']. "','" . $_POST['arguments']['direccion'] . "'";
$cliente_insert = $obj_bdmysql->insert("cliente", $campo, $valor, $mysqli);
if($cliente_insert == 1){
$campo = "rif,telefono";
$valor = "'" . $_POST['arguments']['rif'] . "','" . $_POST['arguments']['telefono'] . "'";
$cliente_insert = $obj_bdmysql->insert("cliente_telefono", $campo, $valor, $mysqli);
if($cliente_insert == 1){
$aResult['result'] = "CLIENTE CREADO CON EXITO: " . $_POST['arguments']['razon_social'] . " " . $_POST['arguments']['rif'];
}else{
$aResult['error'] = "CLIENTE CREADO CON EXITO PERO, EL TELEFONO NO FUE AGREGADO";
}
}else{
$aResult['error'] = "CREACIÓN DE CLIENTE FALLIDA!";
}
}else{
$aResult['error'] = "CLIENTE EXISTENTE!";
}
}else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
}
break;
case 'buscar_cliente':
if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 0) ) {
$aResult['error'] = 'ARGUMENTOS INCOMPLETOS!'; /* 0=>Rif */
}else{
$mysqli = new mysqli(DBManagement::getInstance()->getHost(), DBManagement::getInstance()->getUser(), DBManagement::getInstance()->getPass(), DBManagement::getInstance()->getDbName());
//$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
if (!$mysqli->connect_error){
if (isset($_POST['arguments']['rif']) && $_POST['arguments']['rif'] != ""){
$where = "where CL.rif= '" . $_POST['arguments']['rif'] . "'";
} else {
$where = "";
}
$SQL = "SELECT * FROM cliente CL INNER JOIN cliente_telefono TEL ON (TEL.rif=CL.rif) " . $where;
// var_dump($SQL);
//exit;
$resul = $mysqli->query($SQL); #select("CLIENTE","*",$where,$order,$limit,$mysqli,FALSE);
$r=null;
while($data = $resul->fetch_array(MYSQLI_ASSOC)){
$r[] = $data;
}
if(!is_array($r)){
$aResult['error'] = "BUSQUEDA DE CLIENTE FALLIDA " . $where;
}else{
$aResult['result'] = $r;
}
}else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
}
break;
case 'cliente_existe':
if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
$aResult['error'] = 'ARGUMENTOS INCOMPLETOS!'; /* 0=>Rif */
}else{
$mysqli = new mysqli(DBManagement::getInstance()->getHost(), DBManagement::getInstance()->getUser(), DBManagement::getInstance()->getPass(), DBManagement::getInstance()->getDbName());
//$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
if (!$mysqli->connect_error){
$where = "rif= '" . $_POST['arguments']['rif'] . "'";
$SQL = "SELECT COUNT(rif) FROM cliente WHERE " . $where;
$resul = $mysqli->query($SQL);
$r = $resul->fetch_array(MYSQLI_ASSOC);
if(!is_array($r)){
$aResult['error'] = "BUSQUEDA DE CLIENTE FALLIDA " . $where;
}else{
$aResult['result'] = $r['COUNT(rif)'];
}
}else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
}
break;
case 'buscar_rif_todos':
if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 0) ) {
$aResult['error'] = 'ARGUMENTOS INCOMPLETOS!'; /* 0=>Rif */
}else{
$mysqli = new mysqli(DBManagement::getInstance()->getHost(), DBManagement::getInstance()->getUser(), DBManagement::getInstance()->getPass(), DBManagement::getInstance()->getDbName());
//$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
if (!$mysqli->connect_error){
$resul = $mysqli->query("SELECT rif FROM cliente");
$r = $resul->fetch_array(MYSQLI_ASSOC);
if(!is_array($r)){
$aResult['error'] = "BUSQUEDA DE CLIENTE FALLIDA";
}else{
$aResult['result'] =$r;
}
}else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
}
break;
case 'buscar_todos':
if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 0) ) {
$aResult['error'] = 'ARGUMENTOS INCOMPLETOS!'; /* 0=>Rif */
}else{
$mysqli = new mysqli(DBManagement::getInstance()->getHost(), DBManagement::getInstance()->getUser(), DBManagement::getInstance()->getPass(), DBManagement::getInstance()->getDbName());
//$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
if (!$mysqli->connect_error){
$resul = $mysqli->query("SELECT * FROM cliente CL INNER JOIN cliente_telefono TEL ON (TEL.rif=CL.rif)");
$r = $resul->fetch_array(MYSQLI_ASSOC);
if(!is_array($r)){
$aResult['error'] = "BUSQUEDA DE CLIENTE FALLIDA";
}else{
$aResult['result'] =$r;
}
}else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
}
break;
case 'editar_cliente':
if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
$aResult['error'] = "ARGUMENTOS INCOMPLETOS!";
}else{
$mysqli = new mysqli(DBManagement::getInstance()->getHost(), DBManagement::getInstance()->getUser(), DBManagement::getInstance()->getPass(), DBManagement::getInstance()->getDbName());
//$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
if (!$mysqli->connect_error){
$where = " WHERE rif= '" . $_POST['arguments']['rif'] . "'";
$campos ="";
$cnt = 0;
$arg = 0;
$resul = $mysqli->query("SELECT RIF, RAZON_SOCIAL, DIRECCION FROM cliente " . $where);
$r = $resul->fetch_array(MYSQLI_ASSOC);
foreach($_POST['arguments'] as $key=>$value){
if($key != 'telefono'){
if($r[strtoupper($key)] != $value){
if($cnt < count($_POST['arguments']) && $arg !=0 && $key != 'telefono'){$campos .= ", ";}
$campos .= $key . "= '" . $value . "'";
$arg++;
}
} else {
$camposTlf = "telefono='".$value."'";
$arg++;
}
$cnt++;
}
if($camposTlf != ""){
$SQL = "UPDATE cliente_telefono SET " . $camposTlf . " " . $where;
if($mysqli->query($SQL)){
$tlf = true;
}else{
$tlf = false;
}
}
if($campos != ""){
$SQL = "UPDATE cliente SET " . $campos . " " . $where;
if($mysqli->query($SQL)){
$aResult['result'] = "DATOS ACTUALIZADOS SATISFACTORIAMENTE " ;
$aResult['query'] = $SQL;
}else{
$aResult['error'] = "NO SE PUDO ACTUALIZAR EL CLIENTE";
$aResult['query'] = $SQL;
}
}else{
if ($camposTlf == ""){
$aResult['error'] = "NINGUN CAMPO CAMBIADO!";
} else {
if ($tlf){
$aResult['result'] = "DATOS ACTUALIZADOS SATISFACTORIAMENTE " ;
} else {
$aResult['error'] = "NO SE PUDO ACTUALIZAR EL CLIENTE";
}
}
}
#$resul = select("CLIENTE","*",$where,$order,$limit,$mysqli,$print = FALSE);
}else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
}
break;
case 'eliminar_cliente':
if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
$aResult['error'] = "ARGUMENTOS INCOMPLETOS!";
}else{
$mysqli = new mysqli(DBManagement::getInstance()->getHost(), DBManagement::getInstance()->getUser(), DBManagement::getInstance()->getPass(), DBManagement::getInstance()->getDbName());
//$mysqli = new mysqli(DBHOST3, DBUSER3, DBPASS3, DBNOM3);
if (!$mysqli->connect_error){
$sql = "delete from cliente where rif='".$_POST['arguments']['rif']."'";
// var_dump($sql);
if(!$mysqli->query($sql)){
$aResult['error'] = "ELIMINACIÓN DE CLIENTE FALLIDA";
}else{
$sql = "delete from cliente_telefono where rif='".$_POST['arguments']['rif']."'";
if(!$mysqli->query($sql)){
$aResult['result'] = "CLIENTE ELIMINADO, PERO NO SE ELIMINO EL TLF";
} else {
$aResult['result'] = "CLIENTE ELIMINADO";
}
}
}else{ $aResult['error'] = "NO SE PUDO CONECTAR A LA BASE DE DATOS!"; }
}
break;
default:
$aResult['error'] = 'FUNCION '.$_POST['functionname'].' NO ENCONTRADA!';
break;
}
}
echo json_encode($aResult);

?>