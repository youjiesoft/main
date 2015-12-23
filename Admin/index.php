<?php
//定义项目名称和路径
define('APP_NAME', 'Admin');
define('APP_PATH', './');
// 开启调试模式

define('APP_DEBUG',false);
// define('APP_DEBUG',true);

//第三方模块目录
define('M_D', dirname(__FILE__)."/Module/");
define('ROOT', dirname(__FILE__));
//上传路径配置
define("UPLOAD_PATH", dirname(dirname(__FILE__))."/Public/Uploads/");
define("PUBLIC_PATH", dirname(dirname(__FILE__))."/Public/");
define("UPLOAD_Sample", dirname(dirname(__FILE__))."/Public/SampleExecl/");
define("UPLOAD_SampleWord", dirname(dirname(__FILE__))."/Public/SampleWord/");
define("UPLOAD_PATH_TEMP", dirname(dirname(__FILE__))."/Public/Uploadstemp/");
define("UPLOAD_SampleArchives", "SampleArchives");//档案管理处，目录模版下载
//配置文件柜地址
define("File_PATH_TEMP", dirname(dirname(__FILE__))."/Public/FileManager");
//签名路径配置
define("SIGNATURE_PATH", dirname(dirname(__FILE__))."/Public/Images/signature");
//自定义动态配置文件目录
define("DConfig_PATH", str_replace('\\', '/',dirname(dirname(__FILE__)).'/').APP_NAME."/Dynamicconf");


// 加载框架入口文件
require( "../ThinkPHP/ThinkPHP.php");

/*

// 定义ThinkPHP框架路径
define('THINK_PATH', '../ThinkPHP');
//定义项目名称和路径
define('APP_NAME', 'Admin');
define('APP_PATH', '.');
define('STRIP_RUNTIME_SPACE', false);
//第三方模块目录
define('M_D', dirname(__FILE__)."/Module/");
define('ROOT', dirname(__FILE__));
//上传路径配置
define("UPLOAD_PATH", dirname(dirname(__FILE__))."/Public/Uploads/");
define("UPLOAD_PATH_TEMP", dirname(dirname(__FILE__))."/Public/Uploadstemp/");
//签名路径配置
define("SIGNATURE_PATH", dirname(dirname(__FILE__))."/Public/Images/signature");
//自定义动态配置文件目录
define("DConfig_PATH", str_replace('\\', '/',dirname(dirname(__FILE__)).'/').APP_NAME."/Dynamicconf");
// 加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");
Load('extend');
//实例化一个网站应用实例
App::run();*/