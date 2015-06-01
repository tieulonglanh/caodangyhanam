<?php
error_reporting(E_ERROR); 
ini_set('display_errors','on');
//Enable error report and disalble E_DEPRECATED
//ini_set('display_errors',1); 
//$time = microtime(true);
date_default_timezone_set("Asia/Saigon");

//Set header charset
header("Content-Type: text/html; charset=UTF-8");

// Define đường dẫn tới thư mục application
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__)));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', ROOT_PATH . '/Application');

set_include_path(ROOT_PATH . PATH_SEPARATOR . "Library");

require_once 'Library/XPHP/Loader.php';
XPHP_Loader::registerAutoload();

//Khởi tạo runtime
//XPHP_Runtime::init("Runtime", "xweb_xphp_ems");
//$runtime = XPHP_Runtime::getInstance();

//Khởi tạo XPHP_Config
XPHP_Config_Adapter::load("config.xml");

//Khởi tạo DB Adapter kết nối tới CSDL
$adapter = new XPHP_Db_Adapter();
$adapter->loadConfig("database");

$adapter_xf = new XPHP_Db_Adapter();
$adapter_xf->loadConfig("database_xf");

/*
$session = XPHP_Session::getInstance();
$resource = XPHP_Resource::loadConfig("resource");
//Lấy ra ngôn ngữ. Nếu chưa có trong session lấy mặc định nếu có thì lấy trong session
if(isset($session->clang))
{
    $resource->setLanguage($session->clang); 
}
$_SESSION['language'] = $resource->language;
require_once 'Application/Models/Language.php';
$modelLang = new Models_Language();
$_SESSION['lang_id'] = $modelLang->getIdLang($_SESSION['language']);
*/

//Dispatch application
$controller = new XPHP_Controller_Front();
//$controller->detect();
$controller->dispatch();

//$extime = microtime(true) - $time;
//echo "Thời gian : " . ($extime * 10) . " (ms)";
//echo "<br />";
//echo "Ram sử dụng : " . (memory_get_usage() / 1024 / 1024) . " (Mb)";
//$runtime->save();
