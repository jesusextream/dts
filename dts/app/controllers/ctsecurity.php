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

$mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
foreach ($_POST as $i_dato => $dato_){
    $$i_dato = $obj_function->evalua_array($_POST,$i_dato);
}

switch ($method){
    case 'editBrad':
        if($BrandId == ''){
            //INSERTA BRAND
            $campo = "Name, Description";
            $valor = "'".$Name."', '".$Description."'";
            $Brand = $obj_bdmysql->insert("brand",$campo,$valor,$mysqli);
            //var_dump($Brand);
            $mss = '1';
            $salida = 'Add Successfully..';
        }else{
            //MODIFICAR BRAND
            $campo = "Name='".$Name."', Description='".$Description."'";
            $where = "BrandId = $BrandId";
            $Brand_update = $obj_bdmysql->update("brand",$campo,$where,$mysqli);
            $mss = '1';
            $salida = 'Update Successfully.';
        }
        $resp = array('mss' => $mss, 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    //REMOVE CONTACTS
    case 'deleteBrad':
        $where = "BrandId = $BrandId";
        $brand_delete = $obj_bdmysql->delete("brand",$where,$mysqli);
        $mss = '1';
        $salida = 'Delete Successfully.';
        $resp = array('mss' => utf8_encode($mss), 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    case 'por_listado':
        $sql = "SELECT BrandId, Name, Description FROM brand";
        $result = $mysqli->query($sql);
        while($obj = mysqli_fetch_object($result)) {
            $var[] = $obj;
        }
        $resp = array('mss' => $mss, 'salida' => $var);
        echo json_encode($resp);
    break;
    case 'editModule':
        if($idsecuritymodule == ''){
            //INSERTA 
            $campo = "name, description, keywords";
            $valor = "'".$name."', '".$description."', '".$keywords."'";
            $securityModuled = $obj_bdmysql->insert("securitymodule",$campo,$valor,$mysqli);
            $id = mysqli_insert_id($mysqli);
            $sql = "SELECT MAX(roleId) AS r FROM role";
            $result = $mysqli->query($sql);
            $obj = mysqli_fetch_object($result);
            foreach($obj as $key => $value)
            {
              $roleId = $value;
            }
            for ($i=1; $i <= $roleId; $i++) { 
            $campo = "role_id, idsecuritymodule";
            $valor = "'".$i."', '".$id."'";
            $sql = $obj_bdmysql->insert("securityrolemodule",$campo,$valor,$mysqli);
            }
            $mss = '1';
            $salida = 'Add Successfully..';
        }else{
            //MODIFICAR securitymodule
            $campo = "name='".$name."', description='".$description."', keywords='".$keywords."'";
            $where = "idsecuritymodule = $idsecuritymodule";
            $securitymodule_update = $obj_bdmysql->update("securitymodule",$campo,$where,$mysqli);
            $mss = '1';
            $salida = 'Update Successfully.';
        }
        $resp = array('mss' => $mss, 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    //REMOVE 
    case 'deleteModule':
            //MODIFICAR securitymodule
            $campo = "status='0'";
            $where = "idsecuritymodule = $idsecuritymodule";
            $securitymodule_update = $obj_bdmysql->update("securitymodule",$campo,$where,$mysqli);
            $mss = '1';
        $salida = 'Delete Successfully.';
        $resp = array('mss' => utf8_encode($mss), 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    case 'por_listado_Module':
        $sql = "SELECT idsecuritymodule, name, description, keywords FROM securitymodule WHERE status = 1";
        $result = $mysqli->query($sql);
        while($obj = mysqli_fetch_object($result)) {
            $var[] = $obj;
        }
        $resp = array('mss' => $mss, 'salida' => $var);
        echo json_encode($resp);
    break;
    case 'editUser':
        if($User_ID ==''){
            //INSERTA USER
            $campo = "name, UserName, UserPass ,role_id";
            $valor = "'".$name."','".$UserName."','".md5($UserPass)."', '".$role_id."'";
            $user = $obj_bdmysql->insert("securitykey",$campo,$valor,$mysqli);
            $mss = '1';
            $salida = 'Add Successfully.';
        }else{
            //MODIFICAR USER
            $campo = "name='".$name."', UserName='".$UserName."', role_id='".$role_id."'";
            $where = "User_ID = $User_ID";
            $user_update = $obj_bdmysql->update("securitykey",$campo,$where,$mysqli);
            $mss = '1';
            $salida = 'Update Successfully.';
        }
        $resp = array('mss' => $mss, 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    //REMOVE USER
    case 'deleteUser':
        $where = "User_ID = $User_ID";
        $user_delete = $obj_bdmysql->delete("securitykey",$where,$mysqli);
        $mss = '1';
        $salida = 'Delete Successfully.';
        $resp = array('mss' => utf8_encode($mss), 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    case 'ListUSER':
        $sql = "SELECT User_ID, name, UserName, rolname, roleId FROM securitykey s 
                INNER JOIN role r ON s.role_id = r.roleId "; 
        $result = $mysqli->query($sql);
        while($obj = mysqli_fetch_object($result)) {
            $var[] = $obj;
        }
        $resp = array('mss' => $mss, 'salida' => $var);
        echo json_encode($resp);
    break;
    case 'editRole':
        if($roleId ==''){
            //INSERTA ROLE
            $campo = "rolname, status";
            $valor = "'".$rolname."','".$status."'";
            $user = $obj_bdmysql->insert("role",$campo,$valor,$mysqli);
            $mss = '1';
            $salida = 'Add Successfully.';
        }else{
            //MODIFICAR ROLE
            $campo = "rolname='".$rolname."', status='".$status."'";
            $where = "roleId = $roleId";
            $Rol_update = $obj_bdmysql->update("role",$campo,$where,$mysqli);
            $mss = '1';
            $salida = 'Update Successfully.';
        }
        $resp = array('mss' => $mss, 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    //REMOVE ROLE
    case 'deleteRole':
        $where = "roleId = $roleId";
        $user_delete = $obj_bdmysql->delete("role",$where,$mysqli);
        $mss = '1';
        $salida = 'Delete Successfully.';
        $resp = array('mss'=> utf8_encode($mss), 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    case 'ListRole':
        $sql = "SELECT roleId, rolname,status ,(CASE WHEN status='0' THEN 'Inactivo' ELSE 'Activo' END) AS statusname FROM role"; 
        $result = $mysqli->query($sql);
        while($obj = mysqli_fetch_object($result)) {
            $var[] = $obj;
        }
        $resp = array('mss' => $mss, 'salida' => $var);
        echo json_encode($resp);
    break;
    case 'editOMS':
        if($id ==''){
            //INSERTA Title
            $campo = "openmatter_id, student_id";
            $valor = "'".$openmatter_id."','".$student_id."'";
            $OMS = $obj_bdmysql->insert("openmatterstudent",$campo,$valor,$mysqli);
            $mss = '1';
            $salida = 'Add Successfully.';
        }else{
            //MODIFICAR Title
            $campo = "openmatter_id='".$openmatter_id."', student_id='".$student_id."'";
            $where = "id = $id";
            $OMS_update = $obj_bdmysql->update("openmatterstudent",$campo,$where,$mysqli);
            $mss = '1';
            $salida = 'Update Successfully.';
        }
        $resp = array('mss' => $mss, 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    //REMOVE Title
    case 'deleteOMS':
        $where = "id = $id ";
        $OMS_delete = $obj_bdmysql->delete("openmatterstudent",$where,$mysqli);
        $mss = '1';
        $salida = 'Delete Successfully.';
        $resp = array('mss' => utf8_encode($mss), 'salida' => utf8_encode($salida));
        echo json_encode($resp);
    break;
    case 'ListB':
        $sql = "SELECT b.id AS id, b.name_binloc AS name, b.id_co_art
        FROM binloc b
        WHERE id_co_art = $id_co_art "; 
        $result = $mysqli->query($sql);
        while($obj = mysqli_fetch_object($result)) {
            $var[] = $obj;
        }

            //var_dump($sql);
        $resp = array('mss' => $mss, 'salida' => $var);
        echo json_encode($resp);
    break;
}
?>