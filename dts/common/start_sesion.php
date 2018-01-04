<?php
    include 'general.php';

      //var_dump($_POST);
      $obj_bdmysql = new coBdmysql();
//    $navegador = ObtenerNavegador($_SERVER['HTTP_USER_AGENT']);
//    if($navegador == 'Mozilla' || $navegador == 'Mozilla Firefox'){
        session_start();
        $_SESSION["auten"]=0;
        $usuario = $_POST['Username'];
        $clave = $_POST['Password'];
        $pin = $_POST['pin'];
        $mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
        if (!$mysqli->connect_error){
          if ($pin == ''){
            $r = $obj_bdmysql->select( 
                "autodatasystem.securitykey sk
                JOIN autodatasystem.role r ON sk.role_id = r.roleId
                JOIN autodatasystem.securityrolemodule srm ON r.roleId = srm.role_id
                JOIN autodatasystem.securitymodule sm ON srm.idsecuritymodule = sm.idsecuritymodule"
                , "sk.*, group_CONCAT(DISTINCT(sm.keywords)) AS keywords"
                ,"UserName = '".$usuario."' AND UserPass = '".$clave."' group by srm.role_id"
                , ""
                , ""
                ,$mysqli);
          }else{
            $r = $obj_bdmysql->select( 
                "autodatasystem.securitykey sk
                JOIN autodatasystem.securityrole sr ON sk.idsecurityrole = sr.idsecurityrole
                JOIN autodatasystem.securityrolemodule srm ON sr.idsecurityrole = srm.idsecurityrole
                JOIN autodatasystem.securitymodule sm ON srm.idsecuritymodule = sm.idsecuritymodule"
                , "sk.*, group_CONCAT(DISTINCT(sm.keywords)) AS keywords"
                ,"pin = '".$pin."' group by srm.idsecurityrole"
                , ""
                , ""
                ,$mysqli);
          }

          if (is_array($r)){
              if ($r[0]['status'] == '1'){
                  $_SESSION['valida_sesion'] = '1';
                  $_SESSION["user"]=$r[0]['name'];
                  $_SESSION["cod_usuario"]=$r[0]['User_ID'];
                  $_SESSION["keywords"]=explode(',',$r[0]['keywords']);
//                    $_SESSION["perfil"]=$r['perfil'];
                  header("location:../app/view/init.php");
              }else{ 
                header("location:../index.php?salida=inactivo");
              }
          }else{ 
            header("location:../index.php?salida=fallida");
          }
        }else{ 
          header("location:../index.php?salida=interno");
        }
//    }else{ header("location:navegadores.php"); }
        
    /*
    function ObtenerNavegador($user_agent) {
         $navegadores = array(
              'Opera' => 'Opera',
              'Mozilla Firefox'=> '(Firebird)|(Firefox)',
              'Galeon' => 'Galeon',
              'Mozilla'=>'Gecko',
              'MyIE'=>'MyIE',
              'Lynx' => 'Lynx',
              'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
              'Konqueror'=>'Konqueror',
              'Internet Explorer 9' => '(MSIE 9\.[0-9]+)',
              'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
              'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
              'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
              'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
    );

    foreach($navegadores as $navegador=>$pattern){
           if (preg_match($pattern, $user_agent))
           return $navegador;
        }
    return 'Desconocido';
    }
     * 
     */
?>