<?php

/** *******************************
 * 通用 属性
 * 
 ******************************** */
/*
 * 
 * 1.组件通用属性
 * 		$NBM_PROPERTY		内里所有属性不可修改
 * 2.默认系统字段配置
 * 		$NBM_SYSTEM_FIELDS		可调整
 * 3.表单类型配置
 * 		$NBM_TEMPLATE_TYPE
 * 
 * 
 */
 
/**
 * 通用组件属性
 * 属性说明：
 * 		title： 属性编辑操作界面中文抬头
 * 		type：操作标签类型
 * 		id：标签ID值
 * 		name：标签name值
 * 		dbfield：组件属性在数据库中的字段名称
 * 		isforeignoprate：是事允许复用
 * 		displayright：是否允许显示在属性操作界面中
 * 		desc：属性在界面中的描述
 */
$NBM_PROPERTY = array(
		'id'	=>	array( // 不可删除属性
				'title'	=>	'属性ID',
				'type'	=>	'select',
				'id'	=>	'id',
				'name'	=>	'id',
				'dbfield'=>'id',
				'isforeignoprate' => 1,
				'displayright'=>0,
		),
		'isforeignfield'	=>	array( // 不可删除属性
				'title'	=>	'是否为引用字段',// 复用
				'type'	=>	'select',
				'id'	=>	'isforeignfield',
				'name'	=>	'isforeignfield',
				'dbfield'=>'isforeignfield',
				'isforeignoprate' => 1,
				'displayright'=>0,
		),
		'category'	=>	array( // 不可删除属性
				'title'	=>	'组件类型',
				'type'	=>	'select',
				'id'	=>	'category',
				'name'	=>	'category',
				'dbfield'=>'category',
				'displayright'=>1,
				'dataAdd'=>'tagcategorycontroll',
				'checkvaluetovisable'=>'ids',
				'changeTagCategory'=>'changeTagCategory',
		),
		'fields'	=>	array( // 不可删除属性
				'title'	=>	'字段名',
				'type'	=>	'text',
				'id'	=>	'fields',
				'name'	=>	'fields',
				'dbfield'=>'fields',
				'identity'	=> true,
				'displayright'=>1,
				'checkvaluetovisable'=>'ids',
		)
		,'sort'	=>	array(	// 不可删除属性
				'title'	=>	'排序',
				'type'	=>	'text',
				'id'	=>	'sort',
				'name'	=>	'sort',
				'dbfield'=>'sort',
				'displayright'=>0,
		)
		,'ids'	=>	array( // 不可删除属性
				'title'	=>	'当前项ID',
				'type'	=>	'text',
				'id'	=>	'ids',
				'name'	=>	'ids',
				'dbfield'=>'ids',
				'isforeignoprate' => 1,
				'displayright'=>'0',
		)
		,'title'	=>	array( // 不可删除属性
				'title'	=>	'标题',
				'type'	=>	'text',
				'default'=>'',
				'id'	=>	'title',
				'name'	=>	'title',
				'dbfield'=>'title',
				'isforeignoprate' => 1,
				'displayright'=>1,

		),'searchlist'	=>	array(
				'title'	=>	'是否检索',
				'type'	=>	'checkbox',
				'default'=>'0',
				'id'	=>	'searchlist',
				'name'	=>	'searchlist',
				'displayright'=>1,
				'dbfield'=>'isformsearch',

		),'allsearchlist'	=>	array(
				'title'	=>	'是否全局检索',
				'type'	=>	'checkbox',
				'default'=>'0',
				'id'	=>	'allsearchlist',
				'name'	=>	'allsearchlist',
				'displayright'=>1,
				'dbfield'=>'islistsearch',

		),'tabletype'	=>	array(
				'title'	=>	'字段数据类型',
				'type'	=>	'select',
				// 'default'=>'VARCHAR',
				'id'	=>	'tabletype',
				'name'	=>	'tabletype',
				'dbfield'=>'fieldtype',
				'data'	=>	'varchar|字符串(varchar)|30|text#int|整型(int)|10|text#text|文本型(text)|255|textarea#longtext|长文本型(longtext)|255|textarea#decimal|浮点型(decimal)|18,6|text#smallint|短整型(smallint)|8|text#tinyint|布尔型(tinyint)|1|text#date|日期型(date)|11|date',
				'default'=>':0',
				'displayright'=>1,
				'dbfield'=>'fieldtype',
				'beforefunc'=>'setdefault',
				'checkvaluetovisable'=>'ids',
		)
	 ,'tablename'	=>	array(
	 		'title'	=>	'插入到的表',
	 		'type'	=>	'select',
	 		'id'	=>	'tablename',
	 		'name'	=>	'tablename',
	 		'data'	=>	'',
	 		'dataAdd'=>'checkavailabletablecontroll',
	 		'dbfield'=>'dbname',
	 		'default'=>':0',
	 		'beforefunc'=>'setdefault',
	 		'isforeignoprate' => 1,
	 		'displayright'=>1,
	 		'checkvaluetovisable'=>'ids',
	 )
	 /*,'defaultval'	=>	array(
	  'title'	=>	'默认值',
	 		'type'	=>	'text',
	 		'id'	=>	'defaultval',
	 		'name'	=>	'defaultval',
	 		'dbfield'=>'fieldcheck',
	 		'displayright'=>1,

	 )*/,'length'	=>	array(
	 		'title'	=>	'字段长度',
	 		'type'	=>	'text',
	 		'default'=>'10',
	 		'id'	=>	'length',
	 		'name'	=>	'length',
	 		'dbfield'=>'tablelength',
	 		'displayright'=>1,
	 		'checkvaluetovisable'=>'ids',
	 		//'dbfield'=>'dblength',
	 ),'isshow'	=>	array( //不可删除属性
	 		'title'	=>	'是否显示在页面',
	 		'type'	=>	'checkbox',
	 		'default'=>1,
	 		'id'	=>	'isshow',
	 		'name'	=>	'isshow',
	 		'displayright'=>1,
	 		'isforeignoprate' => 1,
	 		'dbfield'=>'isshow',
	 		'function'=>'changetagsortanddisplay', // 后
	 ),'models'	=>	array( //不可删除属性
	 		'title'	=>	'关联模板',
	 		'type'	=>	'text',
	 		'default'=>'',
	 		'id'	=>	'models',
	 		'name'	=>	'models',
	 		'displayright'=>1,
	 		'dbfield'=>'models',
	 ),
		'methods'	=>	array( //不可删除属性
				'title'	=>	'关联函数',
				'type'	=>	'text',
				'default'=>'view',
				'id'	=>	'methods',
				'name'	=>	'methods',
				'displayright'=>1,
				'dbfield'=>'methods',
		),
		'relation'	=>	array( //不可删除属性
				'title'	=>	'关联关系',
				'type'	=>	'text',
				'default'=>'orderno',
				'id'	=>	'relation',
				'name'	=>	'relation',
				'displayright'=>1,
				'dbfield'=>'relation',
		),
);