
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 
include '../../common/general.php';
$obj_function = new coFunction();
$obj_bdmysql = new coBdmysql();
$id_user_crm = $_SESSION["id_user_crm"];
//var_dump($obj_bdmysql);

$conn = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNOM) or die("Connection failed: " . mysqli_connect_error());/* Database connection end */

$serverName2 = "50.196.74.121"; //serverName\instanceName
$connectionInfo2 = array( "Database"=>"AFM_A", "UID"=>"querysys", "PWD"=>"tex5740");
$conn = sqlsrv_connect( $serverName2, $connectionInfo2);

foreach ($_POST as $i_dato => $dato_){
    //var_dump("$i_dato => $dato_");
    $$i_dato = $obj_function->evalua_array($_POST,$i_dato);
}

switch ($method){    
    case 'campaignTableList':

        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        $columns = array( 
        // datatable column index  => database column name
            1 => 'ca.name',
            2 => 'ct.name'
        );
        // getting total number records without any search
        $sql = "SELECT co_art, art_des, modelo, ref  FROM art a";
        if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
            $sql.=" AND ( a.co_art LIKE '".$requestData['search']['value']."%' ";
        }
        //var_dump($sql);
        $query = sqlsrv_query($conn, $sql) or die($sql);
        $totalData = sqlsrv_num_rows($query);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT ca.id, ca.name, ca.get_data, ct.name AS name_trigger";
        $sql.=" FROM campaign ca
            LEFT JOIN campaign_trigger ct ON ca.id = ct.id_campaign
            WHERE ca.id_user_crm = $id_user_crm AND ca.status = 1";
        if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
            $sql.=" AND ( ca.name LIKE '".$requestData['search']['value']."%' ";
            $sql.=" OR ct.name LIKE '".$requestData['search']['value']."%' )";
        }
        //var_dump($sql);
        $query=sqlsrv_query($conn, $sql) or die($sql);

        $totalFiltered = sqlsrv_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */    
        $query=sqlsrv_query($conn, $sql) or die($sql);

        $data = array();
        while($row=mysqli_fetch_array($query) ) {  // preparing an array

            $nestedData=array(); 
            $nestedData[] = '';
            
            $nestedData[] = '<a href="campaign_edit.php?idCampaign='.
                            $row["id"].'">'.$row["name"].'</a>';
            $nestedData[] = $row["name_trigger"];
            $nestedData[] = '<a href="campaign_edit.php?idCampaign='.$row["id"].'"><i class="md-icon material-icons">&#xE254;</i></a>';
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );
        //var_dump($data);
        echo json_encode($json_data);  // send data as json format
        break;
    //BUSCA campaign_node_email
    case 'findEmailbyIdCampaignNode':
        $sql = "SELECT  cne.* FROM campaign_node cn 
            JOIN campaign_node_email cne ON cn.id = cne.id_campaign_node 
            WHERE cn.id_campaign = " . $id_campaign;
        $query = sqlsrv_query($conn, $sql) or die($sql);
        $data = sqlsrv_fetch_array($query);

        $json_data = array(
            "data"            => $data   // total data array
        );

        echo json_encode($json_data);  // send data as json format
        break;
    case 'editCampaign':
        $arrayNode = json_decode($arrayNode, true);
        $arrayNodeCheckTags = json_decode($arrayNodeCheckTags, true);
        $arrayNodeCondition = json_decode($arrayNodeCondition, true);
        $arrayNodeSendMail = json_decode($arrayNodeSendMail, true);
        $arrayNodeSendSms = json_decode($arrayNodeSendSms, true);
        $arrayNodeTags = json_decode($arrayNodeTags, true);
        $arrayNodeWait = json_decode($arrayNodeWait, true);
        $arrayNodeSetProperty = json_decode($arrayNodeSetProperty, true);

        if($id == 0){
            //INSERTA CAMPAIGN
            $campo = "name, status, get_data, id_user_crm, description, start_date, end_date, source, objective";
            $valor = "'".$name."', 1, '".$get_data."',$id_user_crm, '".$description."', '".$start_date."', '".$end_date."', ".$source.", ".$objective;

            $id = $obj_bdmysql->insert("campaign",$campo,$valor,$conn);
            $mss = '1';
            $salida = 'CAMPAIGN INSERT.';

        }else{
            //MODIFICAR CAMPAIGN
            $campo = "name = '".$name."', status = 1, get_data = '".$get_data."', description = '".$description."', start_date = '".$start_date."', end_date = '".$end_date."', source = ".$source.", objective = ".$objective;
            $where = "id= $id";
            $contacs_update = $obj_bdmysql->update("campaign",$campo,$where,$conn);
            $mss = '1';
            $salida = 'CAMPAIGN UPDATE.';
        }

        if ($source == '1'){
            //INSERTA LANDING PAGES
            $name_landing_pages_array = explode(",", $input_name_landing_pages);
            $company_array = explode(",", $input_company);
            $page_title_array = explode(",", $input_page_title);
            $terms_title_array = explode(",", $input_terms_title);
            $terms_type_array = explode(",", $select_terms_type);
            $terms_description_array = explode(",", $input_terms_description);
            $terms_url_array = explode(",", $input_terms_url);
            $image_type_array = explode(",", $select_image_type);
            $images_preview_array = explode("$%", $images_preview);
            $images_new_array = explode("$%", $images_new);
            $url_video_array = explode(",", $input_url_video);
            $button_title_array = explode(",", $input_button_title);
            $license_type_array = explode(",", $select_license_type);
            $license_description_array = explode(",", $input_license_description);
            $license_url_array = explode(",", $input_license_url);
            $pages_array = explode(",", $pages);
            $checked_names_array = explode(",", $checkbox_checked_names);
            $checked_last_names_array = explode(",", $checkbox_checked_last_names);
            $checked_email_array = explode(",", $checkbox_checked_email);
            $checked_phone_array = explode(",", $checkbox_checked_phone);
            $checked_address_array = explode(",", $checkbox_checked_address);
            $checked_country_array = explode(",", $checkbox_checked_country);
            $checked_state_array = explode(",", $checkbox_checked_state);
            $i=0;
            foreach ($name_landing_pages_array as $name_landing_pages) {
                if($name_landing_pages != ""){

                    //var_dump($images_preview_array);
                    //var_dump($images_new_array);

                    //imagen nueva o vieja
                    if ($images_preview_array[$i] != ""){
                        $image = $images_preview_array[$i];
                    }else{
                        if ($images_new_array[$i] == '../../assets/img/no-image-icon.png'){
                            $image = '../../demo/assets/img/no-image-icon.png';
                        }else{
                            $image = $images_new_array[$i];
                        }
                    }


                    $campo = "id_campaign, name_landing_pages, page, page_title, company, terms_title, terms_type, terms_description, terms_url, image_type, image, url_video, checked_names, checked_last_names, checked_email, checked_phone, checked_address, checked_country, checked_state, button_title, license_type, license_description, license_url, status_landing_pages";
                    $valor = $id.",'".$name_landing_pages_array[$i]."', '".$pages_array[$i]."', '".$page_title_array[$i]."', '".$company_array[$i]."', '".$terms_title_array[$i]."', ".$terms_type_array[$i].", '".$terms_description_array[$i]."', '".$terms_url_array[$i]."', ".$image_type_array[$i].", '".$image."', '".$url_video_array[$i]."', ".$checked_names_array[$i].", ".$checked_last_names_array[$i].", ".$checked_email_array[$i].", ".$checked_phone_array[$i].", ".$checked_address_array[$i].", ".$checked_country_array[$i].", ".$checked_state_array[$i].", '".$button_title_array[$i]."', ".$license_type_array[$i].", '".$license_description_array[$i]."', '".$license_url_array[$i]."', 1";

                    //echo $campo."<<<<<>>>>>".$valor;

                    $campaign_landing_pages_insert = $obj_bdmysql->insert("campaign_landing_pages",$campo,$valor,$conn);
                }
                $i++;
            }
            
        }

        
        
        $campaign_node = array();
        //INACTIVER TODOS LOS NODOS ANTERIORES
        $campo = "status = 2";
        $where = "id_campaign = $id";
        $obj_bdmysql->update("campaign_node",$campo,$where,$conn);
        foreach ($arrayNode as $rowNode){
            $node = array();
            $campo = "id_campaign, operatorId, id_type_node, next_node_yes, next_node_no";
            $valor = "$id, '".$rowNode['operatorId']."', '".$rowNode['id_type_node']."', 0, 0";
            $campaign_node_id = $obj_bdmysql->insert("campaign_node",$campo,$valor,$conn);

            $node['operatorId'] = $rowNode['operatorId'];
            $node['id_type_node'] = $rowNode['id_type_node'];
            $node['campaign_node_id'] = $campaign_node_id;
            $campaign_node[] = $node;
        }

        $get_data = json_decode($get_data, true);
        foreach ($get_data['links'] as $links) {

            if ($links['fromOperator'] != 'begin') {
                $fromNode = $links['fromOperator'];
                $toNode = $links['toOperator'];
                $fromConnector = preg_replace("/[^0-9]/","",$links['fromConnector']);

                foreach ($campaign_node as $key => $value) {
                    if($value['operatorId'] == $toNode){                        
                        $next_node = $campaign_node[$key]['campaign_node_id'];
                    }
                }

                foreach ($campaign_node as $key => $value) {
                    if($value['operatorId'] == $fromNode){
                        if ($fromConnector == 1){
                            $campaign_node[$key]['yes'] = $toNode;
                            $campaign_node[$key]['next_node_yes'] = $next_node;
                        }else{ 
                            $campaign_node[$key]['no'] = $toNode;
                            $campaign_node[$key]['next_node_no'] = $next_node;
                        }
                    }
                }

            }
        }

        foreach ($campaign_node as $cn) {
            $next_node_yes = is_null($cn['next_node_yes']) ? 0 : $cn['next_node_yes'];
            $next_node_no = is_null($cn['next_node_no']) ? 0 : $cn['next_node_no'];
            $campo = "next_node_yes = $next_node_yes, next_node_no = $next_node_no";
            $where = "id = ".$cn['campaign_node_id'];
            $contacs_update = $obj_bdmysql->update("campaign_node",$campo,$where,$conn);

            //ADD CHECK TAGS
            if($cn['id_type_node'] == 1){
                //FIND  arrayNodeCheckTags
                foreach ($arrayNodeCheckTags as $key => $value) {
                    if($value['operatorId'] == $cn['operatorId']){
                        $where = "operatorId = '".$value['operatorId']."'";
                        $r = $obj_bdmysql->select('campaign_node_check_tags','id',$where,'','',$conn);
                        if (is_array($r)){
                            $campo = "id_campaign_node = ".$cn['campaign_node_id'].", tags = '".$value['tags']."'";
                            $obj_bdmysql->update("campaign_node_check_tags",$campo,$where,$conn);
                        }else{
                            $campo = "id_campaign_node, operatorId, tags";
                            $valor = $cn['campaign_node_id'].",'".$value['operatorId']."', '".$value['tags']."'";
                            $obj_bdmysql->insert("campaign_node_check_tags",$campo,$valor,$conn);
                        }
                        break;
                    }
                }
            }

            //ADD CONDITION
            if($cn['id_type_node'] == 2){
                //FIND  arrayNodeCondition
                foreach ($arrayNodeCondition as $key => $value) {
                    if($value['operatorId'] == $cn['operatorId']){
                        $where = "operatorId = '".$value['operatorId']."'";
                        $r = $obj_bdmysql->select('campaign_node_condition','id',$where,'','',$conn);
                        if (is_array($r)){
                            $campo = "id_campaign_node, type_condition, type_variable, merge_field, compare_this, with_value";
                            $obj_bdmysql->update("campaign_node_condition",$campo,$where,$conn);
                        }else{
                            $campo = "id_campaign_node, operatorId, type_condition, type_variable, merge_field, compare_this, with_value";
                            $valor = $cn['campaign_node_id'];
                            $valor .= ",'".$value['operatorId']."'";
                            $valor .= ",'".$value['type_condition']."'";
                            $valor .= ",".$value['type_variable'];
                            $valor .= ",'".$value['merge_field']."'";
                            $valor .= ",'".$value['compare_this']."'";
                            $valor .= ",'".$value['with_value']."'";
                            $obj_bdmysql->insert("campaign_node_condition",$campo,$valor,$conn);
                        }
                        break;
                    }
                }
            }

            //ADD SEND MAIL
            if($cn['id_type_node'] == 3){
                //FIND  arrayNodeSendMail
                foreach ($arrayNodeSendMail as $key => $value) {
                    if($value['operatorId'] == $cn['operatorId']){
                        $where = "operatorId = '".$value['operatorId']."'";
                        $r = $obj_bdmysql->select('campaign_node_email','id',$where,'','',$conn);
                        if (is_array($r)){
                            $campo = "id_campaign_node= ".$cn['campaign_node_id'].", from_names='".$value['from_names']."' , from_email= '".$value['from_email']."', to_email= '".$value['to_email']."', cc_email='".$value['cc_email']."' , bcc_email= '".$value['bcc_email']."', subbject= '".$value['subbject']."', reply_to= '".$value['reply_to']."', text= '".$value['text']."', html= '".trim($value['html'])."', track_clicks= '".$value['track_clicks']."', id_weekday= ".$value['id_weekday'].", id_time= ".$value['id_time'].", id_time_zone= ".$value['id_time_zone'];
                            $obj_bdmysql->update("campaign_node_email",$campo,$where,$conn);
                        }else{
                            $campo = "id_campaign_node, operatorId, from_names, from_email, to_email, cc_email, bcc_email, subbject, reply_to, text, html, track_clicks, id_weekday, id_time, id_time_zone";
                            $valor = $cn['campaign_node_id'];
                            $valor .= ",'".$value['operatorId']."'";
                            $valor .= ",'".$value['from_names']."'";
                            $valor .= ",'".$value['from_email']."'";
                            $valor .= ",'".$value['to_email']."'";
                            $valor .= ",'".$value['cc_email']."'";
                            $valor .= ",'".$value['bcc_email']."'";
                            $valor .= ",'".$value['subbject']."'";
                            $valor .= ",'".$value['reply_to']."'";
                            $valor .= ",'".$value['text']."'";
                            $valor .= ",'".trim($value['html'])."'";
                            $valor .= ",'".$value['track_clicks']."'";
                            $valor .= ",".$value['id_weekday'];
                            $valor .= ",".$value['id_time'];
                            $valor .= ",".$value['id_time_zone'];
                            $obj_bdmysql->insert("campaign_node_email",$campo,$valor,$conn);
                        }
                        break;
                    }
                }
            }

            //ADD SEND SMS
            if($cn['id_type_node'] == 4){
                //FIND  arrayNodeSendSms
                foreach ($arrayNodeSendSms as $key => $value) {
                    if($value['operatorId'] == $cn['operatorId']){
                        $where = "operatorId = '".$value['operatorId']."'";
                        $r = $obj_bdmysql->select('campaign_node_sms','id',$where,'','',$conn);
                        if (is_array($r)){
                            $campo = "id_campaign_node = ".$cn['campaign_node_id'].", id_provides_number = ".$value['id_provides_number'].", to = '".$value['to']."', message = '".$value['message']."'";
                            $obj_bdmysql->update("campaign_node_sms",$campo,$where,$conn);
                        }else{
                            $campo = "id_campaign_node, operatorId, id_provides_number, to, message";
                            $valor = $cn['campaign_node_id'];
                            $valor .= ",'".$value['operatorId']."'";
                            $valor .= ",".$value['id_provides_number'];
                            $valor .= ",'".$value['to']."'";
                            $valor .= ",'".$value['message']."'";
                            $obj_bdmysql->insert("campaign_node_sms",$campo,$valor,$conn);
                        }
                        break;
                    }
                }
            }

            //ADD CHECK TAGS
            if($cn['id_type_node'] == 5){
                //FIND  arrayNodeTags
                foreach ($arrayNodeTags as $key => $value) {
                    if($value['operatorId'] == $cn['operatorId']){
                        $where = "operatorId = '".$value['operatorId']."'";
                        $r = $obj_bdmysql->select('campaign_node_tags','id',$where,'','',$conn);
                        if (is_array($r)){
                            $campo = "id_campaign_node = ".$cn['campaign_node_id'].", type_tags = '".$value['type_tags']."', tags = '".$value['tags']."'";
                            $obj_bdmysql->update("campaign_node_tags",$campo,$where,$conn);
                        }else{
                            $campo = "id_campaign_node, operatorId, type_tags, tags";
                            $valor = $cn['campaign_node_id'];
                            $valor .= ",'".$value['operatorId']."'";
                            $valor .= ",'".$value['type_tags']."'";
                            $valor .= ",'".$value['tags']."'";
                            $obj_bdmysql->insert("campaign_node_tags",$campo,$valor,$conn);
                        }
                        break;
                    }
                }
            }

            //ADD WAIT
            if($cn['id_type_node'] == 6){
                //FIND  arrayNodeWait
                foreach ($arrayNodeWait as $key => $value) {
                    //var_dump($value['operatorId']." == ".$cn['operatorId']);
                    if($value['operatorId'] == $cn['operatorId']){
                        $where = "operatorId = '".$value['operatorId']."'";
                        $r = $obj_bdmysql->select('campaign_node_wait','id',$where,'','',$conn);
                        if (is_array($r)){
                            $campo = "id_campaign_node = ".$cn['campaign_node_id'].", value_duration = ".$value['value_duration'].", type_duration = '".$value['type_duration']."', result_wait = ".$value['result_wait'].", id_time = ".$value['id_time'].", id_time_zone = ".$value['id_time_zone']."";
                            $obj_bdmysql->update("campaign_node_wait",$campo,$where,$conn);
                        }else{
                            $campo = "id_campaign_node, operatorId, value_duration, type_duration, result_wait, id_time, id_time_zone";
                            $valor = $cn['campaign_node_id'];
                            $valor .= ",'".$value['operatorId']."'";
                            $valor .= ",".$value['value_duration'];
                            $valor .= ",'".$value['type_duration']."'";
                            $valor .= ",".$value['result_wait'];
                            $valor .= ",".$value['id_time'];
                            $valor .= ",".$value['id_time_zone'];
                            $obj_bdmysql->insert("campaign_node_wait",$campo,$valor,$conn);
                        }
                        break;
                    }
                }
            }

            //ADD SET PROPERTY
            if($cn['id_type_node'] == 7){
                //FIND  arrayNodeSetProperty
                foreach ($arrayNodeSetProperty as $key => $value) {
                    //var_dump($value['operatorId']." == ".$cn['operatorId']);
                    if($value['operatorId'] == $cn['operatorId']){
                        $where = "operatorId = '".$value['operatorId']."'";
                        $r = $obj_bdmysql->select('campaign_node_set_property','id',$where,'','',$conn);
                        if (is_array($r)){
                            $campo = "id_campaign_node = ".$cn['campaign_node_id'].", merge_field = '".$value['merge_field']."', type_set = '".$value['type_set']."', value = '".$value['value']."'";
                            $obj_bdmysql->update("campaign_node_set_property",$campo,$where,$conn);
                        }else{
                            $campo = "id_campaign_node, operatorId, merge_field, type_set, value";
                            $valor = $cn['campaign_node_id'];
                            $valor .= ",'".$value['operatorId']."'";
                            $valor .= ",'".$value['merge_field']."'";
                            $valor .= ",'".$value['type_set']."'";
                            $valor .= ",'".$value['value']."'";
                            $obj_bdmysql->insert("campaign_node_set_property",$campo,$valor,$conn);
                        }
                        break;
                    }
                }
            }

        }

        $resp = array('mss' => $mss, 'salida' => utf8_encode($salida),'id' => $id);
        echo json_encode($resp);
    break;

}

?>