<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require_once '../core/DBManagement.php';
require_once '../core/DBInspector.php';
require_once '../core/Configurator.php';

/* para quickboks */
require_once('../quickbooks/config.php');
require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
require_once(PATH_SDK_ROOT . 'DataService/DataService.php');
require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');

	
	Configurator::getInstance();

function getCustomersQBO($id=NULL){
    if (isset($id) && $id != ""){
      
        $sql = "SELECT * FROM Customer";

    } else {

        $sql = "SELECT * FROM Customer";
    
    }


    if (isset($_SESSION['token'])){

  //Specify QBO or QBD
$serviceType = IntuitServicesType::QBO;

// Get App Config
$realmId = ConfigurationManager::AppSettings('RealmID');
if (!$realmId)
  exit("Please add realm to App.Config before running this sample.\n");

$token = unserialize($_SESSION['token']);
//echo $token['oauth_token'];
//exit;
// Prep Service Context
$requestValidator = new OAuthRequestValidator($token['oauth_token'],
                                              $token['oauth_token_secret'],
                                              ConfigurationManager::AppSettings('ConsumerKey'),
                                              ConfigurationManager::AppSettings('ConsumerSecret'));
$serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
if (!$serviceContext)
  exit("Problem while initializing ServiceContext.\n");

//var_dump ($serviceContext);

// Prep Data Services
$dataService = new DataService($serviceContext);
if (!$dataService)
  exit("Problem while initializing DataService.\n");

//var_dump ($dataService);
// Run a query
$entities = $dataService->Query($sql);

// Echo some formatted output
if (is_null($entities)){
  unset($_SESSION['token']);
  //header('location: ?pag=qbo');
}

return json_encode($entities);

} else{
    //header('location: ?pag=qbo');
}
return json_encode(false);
  
}

function getEmployeesQBO($id=NULL){
    if (isset($id) && $id != ""){
      
        $sql = "SELECT * FROM Employee";

    } else {

        $sql = "SELECT * FROM Employee";
    
    }


    if (isset($_SESSION['token'])){

  //Specify QBO or QBD
$serviceType = IntuitServicesType::QBO;

// Get App Config
$realmId = ConfigurationManager::AppSettings('RealmID');
if (!$realmId)
  exit("Please add realm to App.Config before running this sample.\n");

$token = unserialize($_SESSION['token']);
//echo $token['oauth_token'];
// Prep Service Context
$requestValidator = new OAuthRequestValidator($token['oauth_token'],
                                              $token['oauth_token_secret'],
                                              ConfigurationManager::AppSettings('ConsumerKey'),
                                              ConfigurationManager::AppSettings('ConsumerSecret'));
$serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
if (!$serviceContext)
  exit("Problem while initializing ServiceContext.\n");

//var_dump ($serviceContext);

// Prep Data Services
$dataService = new DataService($serviceContext);
if (!$dataService)
  exit("Problem while initializing DataService.\n");

//var_dump ($dataService);
// Run a query
$entities = $dataService->Query($sql);

// Echo some formatted output
if (is_null($entities)){
  unset($_SESSION['token']);
  //header('location: ?pag=qbo');
}

return json_encode($entities);

} else{
    //header('location: ?pag=qbo');
}
return json_encode(false);
  
}


function getAccountsQBO($id=NULL){
    if (isset($id) && $id != ""){
      
        $sql = "SELECT * FROM Account";

    } else {

        $sql = "SELECT * FROM Account";
    
    }


    if (isset($_SESSION['token'])){

  //Specify QBO or QBD
$serviceType = IntuitServicesType::QBO;

// Get App Config
$realmId = ConfigurationManager::AppSettings('RealmID');
if (!$realmId)
  exit("Please add realm to App.Config before running this sample.\n");

$token = unserialize($_SESSION['token']);
//echo $token['oauth_token'];
// Prep Service Context
$requestValidator = new OAuthRequestValidator($token['oauth_token'],
                                              $token['oauth_token_secret'],
                                              ConfigurationManager::AppSettings('ConsumerKey'),
                                              ConfigurationManager::AppSettings('ConsumerSecret'));
$serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
if (!$serviceContext)
  exit("Problem while initializing ServiceContext.\n");

//var_dump ($serviceContext);

// Prep Data Services
$dataService = new DataService($serviceContext);
if (!$dataService)
  exit("Problem while initializing DataService.\n");

//var_dump ($dataService);
// Run a query
$entities = $dataService->Query($sql);

// Echo some formatted output
if (is_null($entities)){
  unset($_SESSION['token']);
  //header('location: ?pag=qbo');
}

return json_encode($entities);

} else{
    //header('location: ?pag=qbo');
}
return json_encode(false);
  
}


function getSalesReceiptQBO($id=NULL, $from = NULL, $to = NULL){
    if (isset($id) && $id != ""){
      
        $sql = "SELECT * FROM SalesReceipt WHERE Id = '$id'";
        $existsWhere = TRUE;

    } else {

        $sql = "SELECT * FROM SalesReceipt";
    
    }

    if (isset($from) && trim($from) != "" && isset($to) && trim($to) != ""){
      if (isset($existsWhere) && $existsWhere == TRUE){
        $where = " AND MetaData.CreateTime >= '".$from."T00:00:00' and MetaData.CreateTime <= '".$to."T23:59:59'";
      } else {
        $where = " WHERE MetaData.CreateTime >= '".$from."T00:00:00' and MetaData.CreateTime <= '".$to."T23:59:59'";
      }
    } else $where = '';

    $sql = $sql . $where;

    //echo $sql;


    if (isset($_SESSION['token'])){

  //Specify QBO or QBD
$serviceType = IntuitServicesType::QBO;

// Get App Config
$realmId = ConfigurationManager::AppSettings('RealmID');
if (!$realmId)
  exit("Please add realm to App.Config before running this sample.\n");

$token = unserialize($_SESSION['token']);
//echo $token['oauth_token'];
// Prep Service Context
$requestValidator = new OAuthRequestValidator($token['oauth_token'],
                                              $token['oauth_token_secret'],
                                              ConfigurationManager::AppSettings('ConsumerKey'),
                                              ConfigurationManager::AppSettings('ConsumerSecret'));
$serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
if (!$serviceContext)
  exit("Problem while initializing ServiceContext.\n");

//var_dump ($serviceContext);

// Prep Data Services
$dataService = new DataService($serviceContext);
if (!$dataService)
  exit("Problem while initializing DataService.\n");

//var_dump ($dataService);
// Run a query
$entities = $dataService->Query($sql);
//return var_dump($serviceContext);
// Echo some formatted output
if (is_null($entities)){
  unset($_SESSION['token']);
  //header('location: ?pag=qbo');
}

return json_encode($entities);

} else{
    //header('location: ?pag=qbo');
}
return json_encode(false);
  
}


function putSalesInFile(){
    
}

//var_dump($_POST);


if (isset($_POST)){
$method = $_POST['method'];
if (isset($_POST['parameters'])){
	$parameters = $_POST['parameters'];
	foreach ($parameters as $key => $value) {
		$p[] = $value;
	}

	echo call_user_func_array($method, $p);

} else {
	echo ($method());
}
}