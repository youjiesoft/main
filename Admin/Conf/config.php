<?php
//Version 1.0
$siteconfig = require './siteconfig.inc.php';
$dbconfig = require 'db.inc.php';
$config	= array(
	'COPYRIGHT'=>"重庆市农业担保有限公司",//版权信息
    'SHOW_PAGE_TRACE' =>false, // 显示页面Trace信息
	'SERVER_ADDRESS'=>'localhost/',//外网IP地址或URL地址加单斜杠
    'APP_AUTOLOAD_PATH'=>'ORG.Util',
    'LANG_SWITCH_ON' => true,
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'LANG_AUTO_DETECT' => true, // 自动侦测语言
    'DEFAULT_THEME'=>'default',
    
    'Version'=>"1.0",
    'pcode'=>'1',
    'software'=>'software',
    'URL_MODEL'=>1, // 如果你的环境不支持PATHINFO 请设置为3 (与jquery.autocomplete.js文件有关)
    'TMPL_CACHE_ON' => false,// 清除缓存
	'LOG_RECORD'=>true,//开启了日志记录
	'LOG_RECORD_LEVEL'=>array('EMERG','ALERT','ERROR'),
    
	//手机端应用参数
    'PHONE' => "TRUE",// 手机端
    //验证是否重复登录
    'SAME_TIME_LOADING'=>true,
				
    //Redis 配置
    'DATA_CACHE_TYPE'	=> 'File',  // 数据缓存类型,支持:Redis|File|Db|Apc|Memcache|Shmop|Sqlite| Xcache|Apachenote|Eaccelerator
    //'REDIS_HOST' => '127.0.0.1', //默认为127.0.0.1
    //'REDIS_PORT' => 6379, //默认为6379
    'DATA_CACHE_TIME' => 600,
    'DATA_CACHE_COMPRESS'   => true,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'	=> false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     => '',     // 缓存前缀
    'DATA_CACHE_PATH'       => TEMP_PATH,// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'	=> true,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)(仅对File方式缓存有效)
    'DATA_PATH_LEVEL'       => 3,        // 子目录缓存级别(仅对File方式缓存有效)
    
    //静态缓存,貌似只能是File缓存，Html目录默认在项目下
    'HTML_CACHE_ON'=>false, // 开启静态缓存
    'HTML_FILE_SUFFIX'  =>  '.html', // 设置静态缓存后缀为.shtml
    'HTML_CACHE_TIME' => 600,//貌似改为0不是永久缓存，而是实时更新
    'HTML_CACHE_RULES'=> array(
    '*' => array('{:module}_{:action}'),
    ),
    
    //SQL解析缓存
    'DB_SQL_BUILD_CACHE' => true,
    //'DB_SQL_BUILD_QUEUE' => 'apc',
    'DB_SQL_BUILD_LENGTH' => 100, // SQL缓存的队列长度
    
    //自定义配置
    'chat_port' => 8888, //聊天室Node.js监听端口
    
    'DYNAMIC_PATH' => './Dynamicconf',  //动态配置文件 以Admin为基准
    'LIST_CONF_FILE' => 'list.inc.php', //列表配置文件
    'RIGHT_ON'=>true,
    'TOKEN_ON'=>true,
    'TOKEN_NAME'=>'__hash__',
    'TOKEN_TYPE'=>'md5',
    
    //RBAC 配置
    'USER_AUTH_ON'       =>true,
    'USER_AUTH_TYPE'     =>1,		// 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY'      =>'authId',	// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'     =>'administrator',
    'USER_AUTH_MODEL'    =>'User',	// 默认验证数据表模型
    'AUTH_PWD_ENCODER'   =>'md5',	// 用户认证密码加密方式
    'USER_AUTH_GATEWAY'  =>'/Public/login',	// 默认认证网关
	'AREA_TYPE'  =>'1',	// 系统的地址组件版本  1按模板取值  2按表取值
    //无需认证模块  已移植至notauth.php
 	//'NOT_AUTH_MODULE'	 =>'Public,MisOaItems,MisOaItemsWaitFor,UserInfo,ReportCharts,CheckFor,Search,MisChat,SendMsg,MisMessage,MisMessageInbox,MisMessageOutbox,MisMessageDrafts,MisMessageRecycle,ReportExcel,OAHelper,MisMessagePhone,MisHrRemindBecomeEmployee,MisHrRemindEmployeeContract,MisUserEvents,MisSystemFunctionalBox,WXHelper',		// 默认无需认证模块
    'REQUIRE_AUTH_MODULE'=>'',		// 默认需要认证模块  
	//无需认证方法  已移植至notauth.php
    //'NOT_AUTH_ACTION'	 =>'additemview,acquire,mybusiness,getFormFlow,showMessage,myCoursesList,roleGroupAuthorizeC,exportBysearchHtml,baseInfo,detail,clear_cache,workplatform,audit,insert,update,view,waitAudit,alreadyAudit,auditProcess,backprocess,auditEdit,startprocess,auditView,seeProcessDetail,seeAuditUser,playSWF,sureProcess,getAllScheduleList,setDbhaSmsgType,organization,misimportexceladd,misimportexcelinsert,tmlbbs,misFileManageDownload',	// 默认无需认证操作
    'REQUIRE_AUTH_ACTION'=>'',		// 默认需要认证操作
    'GUEST_AUTH_ON'      => false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'      => 0,     // 游客的用户ID
    
    'DB_LIKE_FIELDS'     => 'title|remark',
    'RBAC_ROLE_TABLE'    => 'role',
    'RBAC_USER_TABLE'	 =>	'role_user',
    'RBAC_ACCESS_TABLE'  =>	'access',
    'RBAC_NODE_TABLE'	 => 'node',
    
    /* 分页设置 */
    'VAR_PAGE'           => 'pageNum',
    'PAGE_ROLLPAGE'      => 5,      // 分页显示页数
    'PAGE_LISTROWS'      => 20,     // 分页每页显示记录数
    'PAGE_DWZLISTROWS'   => 500,     // 分页每页显示记录数
    
    /*文件上传配置*/
   'allexts'=>array('jpg', 'gif', 'png', 'jpeg','doc','xls','csv','zip','docx','xlsx','pdf','rar'),//允许上传文件类型
    'savePath'=> UPLOAD_PATH,//上传路径设置
    'maxSize'=> 3145728,//允许最大上传文件大小 默认3MB
    'UPLOAD_FILE_RULE'=> 'time',
    
    /*文件上传目录配置*/
    'subType'   => 'date',//子目录格式
    'dateFormat' => 'Ym',//子目录命名
    'autoSub'=>true,//是否启用子目录
    
    /*数据库备份路径配置*/
    'BACKUP_PATH' => APP_PATH.'/Backup',  //数据库备份目录 以Admin为基准
    'BACKUP_TABLE' => 'resume', //备份记录表，还原时不对其操作
    'BACKUP_RECORD_FILE' => 'record.txt', //记录备份的表的文件
    'BACKUP_LOG_FILE' => 'my.log', //备份记录文件
    /*数据库还原配置*/
    'RESUME_MODEL' => 'Database', //还原模式为Database表示数据库级回滚，Table表示表级回滚
    
    /*邮件发送服务配置*/
    'SMTP_HOST' => 'smtp.163.com',  //SMTP服务器
    'SMTP_PORT' => 25,  //SMTP服务器端口
    'EMAIL_USERNAME' => 'brianl_yang@163.com', //邮件发送帐号用户名
    'EMAIL_SERVERADDRESS' => 'brianl_yang@163.com', //被显示的邮件发送帐号用户名
    'EMAIL_SERVERNAME' => '系统服务邮箱', //被显示的邮件发送帐号用户名
    'EMAIL_PASSWORD' => '77683385', //邮件发送帐号密码
    
    //系统升级相关参数
    'System_update'=>'http://yun.tmlsoft.com/myapp/index.php/SerialNumber/index',
    'UPGRADE_PATH' => '../Upgrade',  //升级文件存档目录 以Admin为基准
    'UPGRADE_TABLE' =>'upgrade',  //升级文件存档目录 以Admin为基准
    'PAGE_LIFE_TIME'=>'3000',       //页面运行最大时间
    
    'COOKIE_EXPIRE'=>7200, // Cookie有效期
    'COOKIE_DOMAIN'=>"",// Cookie有效域名
    'COOKIE_PATH'=>'/',                        // Cookie路径
    'COOKIE_PREFIX'=>'tml_',
    'SendPhoneMsg'=>array("CorpID"=>"HTEK00738","Pwd"=>703005),
    /*权限组*/
    //基础权限组启用开关，开On  关Off
    'SWITCH_BASIC_ROLE_GROUP'=>'off',
    //高级权限组启用开关，开On  关Off
    'SWITCH_SENIOR_ROLE_GROUP'=>'On',
    'USER_SENIOR_ROLE_GROUP'=>array(1),
    'USER_DEFAULT_PASSWORD'=>'123456',
    //后台用户手机验证是否必填配置
    'MOBILEREQUIRED'=>true,
    //登录时是否需要验证码
    'VERIFICATION_CODE' => false, 

      //操作系统类型
    'OS_TYPE'=>'WINDOWS',//windows系统为WINDOWS，LINUX系统为LINUX
 
	//文档配置
	'FILE_RENAME'=> false,//文档是否启用重命名,有些服务器不支持中文名打开，需要关闭此项
	'FILE_OFFICEONLINE'=> true,//文档是否启用在线查看，有些服务器未安装及开启相关服务，需要关闭此项
	'FILE_OFFICEONLINE_DEMO'=> '',//演示效果路径
    //文件转换swf的类型
    'TRANSFORM_SWF' => array('doc','docx','xls','xlsx','ppt','pptx','txt'),
    //在线查看允许图片文件
    'IMG_file' => array('jpg', 'gif', 'png', 'jpeg'),
	'OPENOFFICE_PATH_WINDOWS'=>'D:/Program Files/OpenOffice 4',//OPENOFFICE服务启动路径
	'JODCONVERTER_PATH_WINDOWS'=>'D:/jodconverter3/lib/jodconverter-core-3.0-beta-4.jar',//jodconverter安装路径
	'SWFTOOL_PATH_WINDOWS'=>'D:/Program Files/SWFTools/pdf2swf.exe', //swftool安装目录,生成swf的插件
	//文档在线查看
	'SWFTOOL_PATH_LINUX'=>'/usr/local/bin/pdf2swf', //swftool安装路径/usr/local/wenku/swftools/bin/pdf2swf
	'JODCONVERTER_PATH_LINUX'=>'java -Doffice.home=/opt/openoffice4/ -jar /usr/share/jodconverter3/lib/jodconverter-core-3.0-beta-4.jar',
	//知识库显示条数配置
	'ANNOUNCEMENT_TYPE_NUM'=>5,//类型内显示条数
	'ANNOUNCEMENT_LIST_NUM'=>30,//页面显示多少条数
	//项目人员判断，1:一个人员只能在一个项目里面，2:一个人员在多个项目里面。
	'ONLY_ONE_PROJECT'=>'2',
    'GANTT_TYPE' => false, //甘特图免费版、60天试用版切换 true:免费版; false:60试用版
    'AUDIT_TIMEOUT'=>'48', //设置审批超期时间。(小时)
	'AUDIT_TIMEOUT_REMIND'=>'8', //设置即将超期提前多少小时提醒
	'SYSTEM_SORT'=>'desc', //系统默认排序
	'TMPL_PARSE_STRING' => array (//路径配置
	
			//Timi文件路径还原
			'--PUBLIC--' => '__PUBLIC__',
			'--APP--' => '__APP__',
			'--URL--' => '__URL__',
			'--ACTION--' => '__ACTION__',
			'--SELF--' => '__SELF__',
			'--INFO--' => '__INFO__',
			'--EXT--' => '__EXT__',
			'--MODULE--'=>'__MODULE__'
	),
);

return array_merge($config, $siteconfig,$dbconfig);
?>