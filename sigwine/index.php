<?php
//ini_set('extension php_pdo.dll');
//ini_set('extension php_pdo_mysql.dll');
 //$path = php_ini_loaded_file();
  //  echo 'The loaded file path is :' . $path;
//echo "in index.php";
error_reporting(E_ALL);
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';
 
// remove the following lines when in production mode
//defined('YII_DEBUG') or define('YII_DEBUG',false);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
