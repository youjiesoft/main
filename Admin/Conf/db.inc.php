<?php
return array(
	'DB_TYPE'=>'mysql',
    'DB_HOST'=>'192.168.1.3',
    'DB_NAME'=>'advprodect',
    'DB_USER'=>'tmlsoft',
    'DB_PWD'=>'tml1000',//ryAEXy6JjrhvuKZZ
// 	'DB_HOST'=>'localhost',
// 	'DB_NAME'=>'system2',
// 	'DB_USER'=>'root',
// 	'DB_PWD'=>'',//ryAEXy6JjrhvuKZZ    
    'DB_PORT'=>'3306',
    'DB_PREFIX'=>'',
		
    //生产环境使用，开发时需要启用debug，或者清除缓存下的Data目录下面的_field文件夹，否则会导致开发人员变更数据库后curd无法正常对新增字段做操作
	'DB_FIELDS_CACHE'=>true,
    'DB_FIELDTYPE_CHECK'=>true,  // 开启字段类型验证
);
?>

