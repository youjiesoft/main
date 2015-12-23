<?php 
/**
 * @Title: Config
 * @Package package_name
 * @Description: todo(动态表单_配置文件-data)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-05-08 17:50:24
 * @version V1.0
*/
return array(
	'id' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'id',
		'type' => 'int(10)',
		'nullable' => 'NO',
		'default' => NULL,
		'primary' => 'PRI',
		'autoinc' => 'auto_increment',
		'comment' => 'ID',
	),
	'title' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'title',
		'type' => 'varchar(100)',
		'nullable' => 'NO',
		'default' => NULL,
		'primary' => '',
		'autoinc' => '',
		'comment' => '标题',
	),
	'actionname' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'actionname',
		'type' => 'varchar(50)',
		'nullable' => 'YES',
		'default' => NULL,
		'primary' => '',
		'autoinc' => '',
		'comment' => '对象名称',
	),
	'status' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'status',
		'type' => 'tinyint(1)',
		'nullable' => 'YES',
		'default' => NULL,
		'primary' => '',
		'autoinc' => '',
		'comment' => '状态',
	),
	'createid' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'createid',
		'type' => 'int(10)',
		'nullable' => 'YES',
		'default' => NULL,
		'primary' => '',
		'autoinc' => '',
		'comment' => '创建人ID',
	),
	'createtime' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'createtime',
		'type' => 'int(11)',
		'nullable' => 'YES',
		'default' => NULL,
		'primary' => '',
		'autoinc' => '',
		'comment' => '创建时间',
	),
	'updateid' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'updateid',
		'type' => 'int(10)',
		'nullable' => 'YES',
		'default' => NULL,
		'primary' => '',
		'autoinc' => '',
		'comment' => '修改人ID',
	),
	'updatetime' => array(
		'tablename' => 'mis_system_design_form',
		'name' => 'updatetime',
		'type' => 'int(11)',
		'nullable' => 'YES',
		'default' => NULL,
		'primary' => '',
		'autoinc' => '',
		'comment' => '修改时间',
	),
);

?>