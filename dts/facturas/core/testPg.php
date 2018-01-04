<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './DBManagement.php';


$db = DBManagement::getInstance();




$db->setHost('localhost');
$db->setUser('postgres');
$db->setPass('0000');
$db->setType('pgsql');
$db->setDbname('postgres');
$db->setPort('5432');
//$db->setCharset('utf8');


$sql = "select schema_name
from information_schema.schemata";

$db->consultar($sql);


var_dump($db->getResultset());



