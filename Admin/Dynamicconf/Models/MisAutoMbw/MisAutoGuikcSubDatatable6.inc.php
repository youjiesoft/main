<?php 
/**
 * @Title: Config
 * @Package package_name
 * @Description: todo(动态表单_配置文件-MisAutoGuikcSubDatatable6.inc 配置文件)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-10-09 16:27:20
 * @version V1.0
*/
return array(
	'zhuanshoumoxing' => array(
		'name' => 'zhuanshoumoxing',
		'showname' => '转授模型',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => '转授模型',
		'sortnum' => '1',
		'iscount' => '0',
		'fieldtype' => 'VARCHAR',
		'fieldcategory' => 'lookup',
		'type' => 'lookup',
		'required' => '1',
		'validate' => '',
		'unique' => '0',
		'transform' => '1',
		'func' => array(
			'0' => array(
				'0' => 'getFieldBy',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'name',
					'2' => 'title',
					'3' => 'Node',
				),
			),
		),
		'table' => 'Node',
		'field' => 'name',
		'unfunc' => array(
			'0' => array(
				'0' => 'ungetFieldBy',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'name',
					'2' => 'title',
					'3' => 'Node',
				),
			),
		),
	),
	'id' => array(
		'name' => 'id',
		'showname' => 'ID',
		'shows' => '1',
		'widths' => '',
		'sorts' => '1',
		'models' => '',
		'status' => '1',
		'sortname' => 'id',
		'sortnum' => '0',
		'searchField' => 'mis_auto_guikc_sub_datatable6.id',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '1',
		'isallsearch' => '1',
		'searchsortnum' => '0',
	),
);
?>