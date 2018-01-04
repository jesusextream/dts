<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GeneratorResponse {
    
    
    private static $instancia;
    private $data;
    private $response;
    private $status;
    private $contentType;
    private $statusMsg;
    
    
    private function __construct() {
            
    $this->estado = array(  
       200 => 'OK',  
       201 => 'Created',  
       202 => 'Accepted',  
       204 => 'No Content',  
       301 => 'Moved Permanently',  
       302 => 'Found',  
       303 => 'See Other',  
       304 => 'Not Modified',  
       400 => 'Bad Request',  
       401 => 'Unauthorized',  
       403 => 'Forbidden',  
       404 => 'Not Found',  
       405 => 'Method Not Allowed',  
       500 => 'Internal Server Error');  
    }
    
    public static function getInstancia() {
        
        return (isset(self::$instancia)) ? self::$instancia : self::$instancia = new GeneratorResponse();
        
    }
    
    
    public function cleanDataPost($data){
        
        $entrada = array();  
     if (is_array($data)) {  
       foreach ($data as $key => $value) {  
         $entrada[$key] = $this->cleanDataPost($value);  
       }  
     } else {  
       if (get_magic_quotes_gpc()) {  
         //Quitamos las barras de un string con comillas escapadas  
         //Aunque actualmente se desaconseja su uso, muchos servidores tienen activada la extensión magic_quotes_gpc.   
         //Cuando esta extensión está activada, PHP añade automáticamente caracteres de escape (\) delante de las comillas que se escriban en un campo de formulario.   
         $data = trim(stripslashes($data));  
       }  
       //eliminamos etiquetas html y php  
       $data = strip_tags($data);  
       //Conviertimos todos los caracteres aplicables a entidades HTML  
       $data = htmlentities($data);  
       $entrada = trim($data);  
     }  
     return $entrada; 
        
        
    }
    
    
    
    
    public function makeResponse(){
       // var_dump(json_encode($this->getData(), JSON_UNESCAPED_UNICODE));
       // var_dump(($this->getData()));
 
        $this->setResponse(utf8_encode(json_encode(array ('status' => $this->statusMsg, 'data' => $this->getData()), JSON_NUMERIC_CHECK )));
        //echo $this->getResponse();

        }
        
        
        public function getErrorJson(){
            
                    switch(json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - Sin errores';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Excedido tamaño máximo de la pila';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Desbordamiento de buffer o los modos no coinciden';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Encontrado carácter de control no esperado';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Error de sintaxis, JSON mal formado';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
        break;
        default:
            echo ' - Error desconocido';
        break;
    }
            
            
        }
        
    
    function getData() {
        return $this->data;
    }

    function getResponse() {
        return $this->response;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setResponse($response) {
        $this->response = $response;
    }

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    
    function getContentType() {
        return $this->contentType;
    }

    function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    function getEstado() {
        return $this->estado;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function getStatusMsg() {
        return $this->statusMsg;
    }

    function setStatusMsg($statusMsg) {
        $this->statusMsg = $statusMsg;
    }


}