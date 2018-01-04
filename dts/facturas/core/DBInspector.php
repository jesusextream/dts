<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DBInspector {

    private $fields;
    private $table;
    private $describe;

    const __FIELD__ = 'FIELD';
    const __TYPE__ = 'TYPE';
    
    
    public function __construct($table) {
        $this->table = $table;
    }
    

    public function getDescribeTable() {

        $sql = "DESCRIBE $this->table";

        DBManagement::getInstance()->consultar($sql);

        $this->setDescribe(DBManagement::getInstance()->getResultSet());
    }

    function getFields() {

        if ($this->getDescribe() != NULL) {
        foreach ($this->getDescribe() as $key => $value) {

            //$field;
            $fields[$value['Field']] = array(
                'type' => $value['Type'],
                'null' => $value['Null'],
                'default' => $value['Default'],
                'extra' => $value['Extra'],
                'key' => $value['Key']
            );

            $this->setFields($fields);
        }
        }

        return $this->fields;
    }

    function getTable() {
        return $this->table;
    }

    function getDescribe() {
        return $this->describe;
    }

    function setFields($fields) {
        $this->fields = $fields;
    }

    function setTable($table) {
        $this->table = $table;
    }

    function setDescribe($describe) {
        $this->describe = $describe;
    }

}
