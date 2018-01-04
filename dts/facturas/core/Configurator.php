<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Constants.php';
//require_once 'DBMagnament.php';

        const __NAME__ = 'name';
        const __TIMEZONE__ = 'timezone';
        const __PRODUCTION__ = 'production';
        const __DATABASE__ = 'database';
        const __DEBUG__ = 'debug';
        const __LOG_EXCEPTIONS__ = 'log_exceptions';
        const __CHARSET__ = 'charset';
        const __CACHE_DRIVER__ = 'cache_driver';
        const __METADATA_LIFETIME__ = 'metadata_lifetime';
        const __NAMESPACE_AUTH__ = 'namespace_auth';
/* * ********************************************* */

        const __HOST__ = 'host';
        const __USERNAME__ = 'username';
        const __PASSWORD__ = 'password';
// const __NAME__ = 'name';
        const __TYPE__ = 'type';

//const __CHARSET__ = 'charset';

class Configurator implements Constants {

    private static $instance;
    private $name;
    private $timezone;
    private $production;
    private $database;
    private $debug;
    private $log_exceptions;
    private $charset;
    private $cache_driver;
    private $metada_lifetime;
    private $namespace_auth;

    private function __construct() {
        $this->loadConfig();
    }

    public static function getInstance() {

        if (isset(self::$instance)) {
            return self::$instance;
        } else {
            $clase = __CLASS__;
            self::$instance = new $clase;
            return self::$instance;
        }
    }

    public function loadConfig() {
        $configData = parse_ini_file("../config/config.ini", true);
        foreach ($configData['application'] as $key => $value) {
            // echo "asada";

            (strcmp($key, __NAME__) === 0) ? $this->name = $value : '';
            (strcmp($key, __TIMEZONE__) === 0) ? $this->timezone = $value : '';
            (strcmp($key, __PRODUCTION__) === 0) ? $this->production = $value : '';
            (strcmp($key, __DATABASE__) === 0) ? $this->database = $value : '';
            (strcmp($key, __DEBUG__) === 0) ? $this->debug = $value : '';
            //(strcmp($key, __NAME__) === 0) ? $this->name = $value : '';
            //(strcmp($key, __NAME__) === 0) ? $this->name = $value : '';
        }

        $this->loadDBConfig();
    }

    public function loadDBConfig($dataBaseConfig = NULL) {
        $configData = parse_ini_file("../config/databases.ini", true);
        
        if (isset($dataBaseConfig)){
            $searchDB = $dataBaseConfig;
        } else {
            $searchDB = $this->getDatabase();
        }
        
        if (NULL != $this->getDatabase()) {
            // var_dump($configData);
            foreach ($configData as $key => $value) {
                if (strcmp($searchDB, $key) === 0) {

                    foreach ($value as $key => $val) {


                        (strcmp($key, __HOST__) === 0) ? DBManagement::getInstance()->setHost($val) : '';
                        (strcmp($key, __USERNAME__) === 0) ? DBManagement::getInstance()->setUser($val) : '';
                        (strcmp($key, __NAME__) === 0) ? DBManagement::getInstance()->setDbName($val) : '';
                        (strcmp($key, __TYPE__) === 0) ? DBManagement::getInstance()->setType($val) : '';
                        (strcmp($key, __PASSWORD__) === 0) ? DBManagement::getInstance()->setPass($val) : '';
                        (strcmp($key, __CHARSET__) === 0) ? DBManagement::getInstance()->setCharset($val) : '';
                    }
                }
            }
        }
    }

    function getName() {
        return $this->name;
    }

    function getTimezone() {
        return $this->timezone;
    }

    function getProduction() {
        return $this->production;
    }

    function getDatabase() {
        return $this->database;
    }

    function getDebug() {
        return $this->debug;
    }

    function getLog_exceptions() {
        return $this->log_exceptions;
    }

    function getCharset() {
        return $this->charset;
    }

    function getCache_driver() {
        return $this->cache_driver;
    }

    function getMetada_lifetime() {
        return $this->metada_lifetime;
    }

    function getNamespace_auth() {
        return $this->namespace_auth;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setTimezone($timezone) {
        $this->timezone = $timezone;
    }

    function setProduction($production) {
        $this->production = $production;
    }

    function setDatabase($database) {
        $this->database = $database;
    }

    function setDebug($debug) {
        $this->debug = $debug;
    }

    function setLog_exceptions($log_exceptions) {
        $this->log_exceptions = $log_exceptions;
    }

    function setCharset($charset) {
        $this->charset = $charset;
    }

    function setCache_driver($cache_driver) {
        $this->cache_driver = $cache_driver;
    }

    function setMetada_lifetime($metada_lifetime) {
        $this->metada_lifetime = $metada_lifetime;
    }

    function setNamespace_auth($namespace_auth) {
        $this->namespace_auth = $namespace_auth;
    }

}
