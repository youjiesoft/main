<?php 
/**
 * @Title: Config
 * @Package package_name
 * @Description: todo(动态表单_配置文件-list.inc 配置文件)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-08 20:12:56
 * @version V1.0
*/
return array(
	'0' => array(
		'name' => 'orderno',
		'showname' => '编号',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'orderno',
		'sortnum' => '1',
		'fieldtype' => '',
		'fieldcategory' => 'text',
		'searchField' => 'mis_system_unit_exchange.orderno',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
	'1' => array(
		'name' => 'zhuanhuanmingchen',
		'showname' => '转换名称',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'zhuanhuanmingchen',
		'sortnum' => '2',
		'fieldtype' => 'varchar',
		'fieldcategory' => 'text',
		'searchField' => 'mis_system_unit_exchange.zhuanhuanmingchen',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '1',
		'isallsearch' => '1',
		'searchsortnum' => '1',
	),
	'2' => array(
		'name' => 'baseunitid',
		'showname' => '主单位',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'baseunitid',
		'sortnum' => '3',
		'fieldtype' => 'int',
		'fieldcategory' => 'select',
		'searchField' => 'mis_system_unit_exchange.baseunitid',
		'conditions' => '',
		'type' => 'select',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '2',
		'func' => array(
			'0' => array(
				'0' => 'getFieldBy',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'id',
					'2' => 'danweimingchen',
					'3' => 'mis_system_unit',
				),
			),
		),
		'table' => 'mis_system_unit',
		'field' => 'danweimingchen',
	),
	'3' => array(
		'name' => 'exchange',
		'showname' => '转换系数',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'exchange',
		'sortnum' => '4',
		'fieldtype' => 'decimal',
		'fieldcategory' => 'text',
		'searchField' => 'mis_system_unit_exchange.exchange',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '3',
	),
	'4' => array(
		'name' => 'subunitid',
		'showname' => '转换单位',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'subunitid',
		'sortnum' => '5',
		'fieldtype' => 'int',
		'fieldcategory' => 'select',
		'searchField' => 'mis_system_unit_exchange.subunitid',
		'conditions' => '',
		'type' => 'select',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '4',
		'func' => array(
			'0' => array(
				'0' => 'getFieldBy',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'id',
					'2' => 'danweimingchen',
					'3' => 'mis_system_unit',
				),
			),
		),
		'table' => 'mis_system_unit',
		'field' => 'danweimingchen',
	),
	'5' => array(
		'name' => 'beizhu',
		'showname' => '备注',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'beizhu',
		'sortnum' => '6',
		'fieldtype' => 'varchar',
		'fieldcategory' => 'textarea',
		'searchField' => 'mis_system_unit_exchange.beizhu',
		'conditions' => '',
		'type' => 'textarea',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '5',
		'ischosice' => '1',
	),
	'6' => array(
		'name' => 'action',
		'showname' => '操作',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'status',
		'func' => array(
			'0' => array(
				'0' => 'showStatus',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '#status#',
					'1' => '#id#',
				),
			),
		),
		'sortnum' => '7',
	),
);

?>