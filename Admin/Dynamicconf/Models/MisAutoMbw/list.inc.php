<?php 
/**
 * @Title: Config
 * @Package package_name
 * @Description: todo(动态表单_配置文件-list.inc 配置文件)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-10-09 16:27:20
 * @version V1.0
*/
$original = array(
	'id' => array(
		'name' => 'id',
		'showname' => 'ID',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'status' => '1',
		'sortname' => 'id',
		'sortnum' => '0',
		'searchField' => 'mis_auto_guikc.id',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '1',
		'message' => '1',
		'isallsearch' => '1',
		'searchsortnum' => '0',
	),
	'orderno' => array(
		'name' => 'orderno',
		'showname' => '编号',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'orderno',
		'sortnum' => '0',
		'shows' => '1',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => '',
		'relation' => '',
		'fieldtype' => '',
		'fieldcategory' => 'text',
		'searchField' => 'mis_auto_guikc.orderno',
		'conditions' => '',
		'type' => 'text',
		'validate' => '',
		'unique' => '1',
		'required' => '0',
		'transform' => '0',
		'issearch' => '1',
		'isallsearch' => '1',
		'searchsortnum' => '0',
	),
	'zhuanshouren' => array(
		'name' => 'zhuanshouren',
		'showname' => '转授人',
		'widths' => '',
		'sorts' => '0',
		'models' => 'User',
		'sortname' => 'zhuanshouren',
		'sortnum' => '1',
		'shows' => '1',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => 'view',
		'relation' => 'id',
		'fieldtype' => 'int',
		'fieldcategory' => 'lookup',
		'searchField' => 'mis_auto_guikc.zhuanshouren',
		'conditions' => '',
		'type' => 'db|name',
		'validate' => '',
		'unique' => '0',
		'required' => '1',
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
					'1' => 'id',
					'2' => 'name',
					'3' => 'user',
				),
			),
		),
		'table' => 'user',
		'field' => 'id',
		'issearch' => '1',
		'isallsearch' => '1',
		'searchsortnum' => '1',
		'unfunc' => array(
			'0' => array(
				'0' => 'ungetFieldBy',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'id',
					'2' => 'name',
					'3' => 'user',
				),
			),
		),
	),
	'zhuanshougei' => array(
		'name' => 'zhuanshougei',
		'showname' => '转授给',
		'widths' => '',
		'sorts' => '0',
		'models' => 'User',
		'sortname' => 'zhuanshougei',
		'sortnum' => '2',
		'shows' => '1',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => 'view',
		'relation' => 'id',
		'fieldtype' => 'int',
		'fieldcategory' => 'lookup',
		'searchField' => 'mis_auto_guikc.zhuanshougei',
		'conditions' => '',
		'type' => 'db|name',
		'validate' => '',
		'unique' => '0',
		'required' => '1',
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
					'1' => 'id',
					'2' => 'name',
					'3' => 'user',
				),
			),
		),
		'table' => 'user',
		'field' => 'id',
		'issearch' => '1',
		'isallsearch' => '1',
		'searchsortnum' => '2',
		'unfunc' => array(
			'0' => array(
				'0' => 'ungetFieldBy',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'id',
					'2' => 'name',
					'3' => 'user',
				),
			),
		),
	),
	'zhuanshoufanwei' => array(
		'name' => 'zhuanshoufanwei',
		'showname' => '转授范围',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'zhuanshoufanwei',
		'sortnum' => '3',
		'shows' => '1',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => '',
		'relation' => '',
		'fieldtype' => 'varchar',
		'fieldcategory' => 'select',
		'searchField' => 'mis_auto_guikc.zhuanshoufanwei',
		'conditions' => '',
		'type' => 'select|ndzsfw01',
		'validate' => NULL,
		'unique' => '0',
		'required' => '0',
		'transform' => '1',
		'func' => array(
			'0' => array(
				'0' => 'getSelectlistValue',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'ndzsfw01',
				),
			),
		),
		'unfunc' => array(
			'0' => array(
				'0' => 'ungetSelectlistValue',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'ndzsfw01',
				),
			),
		),
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '3',
	),
	'shengxiaoriqi' => array(
		'name' => 'shengxiaoriqi',
		'showname' => '生效日期',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'shengxiaoriqi',
		'sortnum' => '4',
		'shows' => '1',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => '',
		'relation' => '',
		'fieldtype' => 'int',
		'fieldcategory' => 'date',
		'searchField' => 'mis_auto_guikc.shengxiaoriqi',
		'conditions' => '',
		'type' => 'time',
		'validate' => '',
		'unique' => '0',
		'required' => '1',
		'transform' => '1',
		'func' => array(
			'0' => array(
				'0' => 'transTime',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'Y-m-d H:i',
				),
			),
		),
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '4',
		'unfunc' => array(
			'0' => array(
				'0' => 'untransTime',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'Y-m-d H:i',
				),
			),
		),
	),
	'shixiaoriqi' => array(
		'name' => 'shixiaoriqi',
		'showname' => '失效日期',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'shixiaoriqi',
		'sortnum' => '5',
		'shows' => '1',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => '',
		'relation' => '',
		'fieldtype' => 'int',
		'fieldcategory' => 'date',
		'searchField' => 'mis_auto_guikc.shixiaoriqi',
		'conditions' => '',
		'type' => 'time',
		'validate' => '',
		'unique' => '0',
		'required' => '1',
		'transform' => '1',
		'func' => array(
			'0' => array(
				'0' => 'transTime',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'Y-m-d H:i',
				),
			),
		),
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '5',
		'unfunc' => array(
			'0' => array(
				'0' => 'untransTime',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'Y-m-d H:i',
				),
			),
		),
	),
	'miaoshu' => array(
		'name' => 'miaoshu',
		'showname' => '描述',
		'widths' => '350',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'miaoshu',
		'sortnum' => '6',
		'shows' => '1',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => '',
		'relation' => '',
		'fieldtype' => 'text',
		'fieldcategory' => 'textarea',
		'searchField' => 'mis_auto_guikc.miaoshu',
		'conditions' => '',
		'type' => 'textarea',
		'validate' => '',
		'unique' => '0',
		'required' => '0',
		'transform' => '0',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '6',
	),
	'datatable6' => array(
		'name' => 'datatable6',
		'showname' => '转授范围',
		'widths' => NULL,
		'sorts' => '0',
		'models' => '',
		'sortname' => 'datatable6',
		'sortnum' => '7',
		'shows' => '0',
		'status' => '1',
		'rules' => '1',
		'message' => '1',
		'isexport' => '1',
		'methods' => 'view',
		'relation' => 'orderno',
		'fieldtype' => 'varchar',
		'fieldcategory' => 'datatable',
		'searchField' => 'mis_auto_guikc.datatable6',
		'conditions' => '',
		'type' => 'datatable',
		'validate' => '',
		'unique' => '0',
		'required' => '0',
		'transform' => '0',
		'ischosice' => '1',
	),
	'action' => array(
		'name' => 'action',
		'showname' => '操作',
		'shows' => '0',
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
		'sortnum' => '10',
		'transform' => '0',
		'unique' => '0',
		'validate' => '0',
		'required' => '0',
	),
	'operateid' => array(
		'name' => 'operateid',
		'showname' => '是否确认',
		'rules' => '1',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'func' => array(
			'0' => array(
				'0' => 'getSelectByName',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => 'operateidVal',
					'1' => '###',
				),
			),
		),
		'sortname' => 'operateid',
		'sortnum' => '11',
		'searchField' => 'mis_auto_guikc.operateid',
		'transform' => '0',
		'unique' => '0',
		'validate' => '0',
		'required' => '0',
	),
	'companyid' => array(
		'name' => 'companyid',
		'showname' => '公司',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => 'MisSystemCompany',
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
					'2' => 'name',
					'3' => 'MisSystemCompany',
				),
			),
		),
		'sortname' => 'companyid',
		'sortnum' => '12',
		'issearch' => '1',
		'table' => 'mis_system_company',
		'searchField' => 'mis_auto_guikc.companyid',
		'transform' => '1',
		'unique' => '0',
		'validate' => '0',
		'required' => '0',
		'unfunc' => array(
			'0' => array(
				'0' => 'ungetFieldBy',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'id',
					'2' => 'name',
					'3' => 'MisSystemCompany',
				),
			),
		),
	),
	'projectid' => array(
		'name' => 'projectid',
		'showname' => '项目ID',
		'rules' => '0',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'projectid',
		'sortnum' => '13',
		'searchField' => 'mis_auto_guikc.projectid',
		'transform' => '0',
		'unique' => '0',
		'validate' => '0',
		'required' => '0',
	),
	'createid' => array(
		'name' => 'createid',
		'rules' => '1',
		'showname' => '创建人',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => 'User',
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
					'2' => 'name',
					'3' => 'User',
				),
			),
		),
		'sortname' => 'createid',
		'issearch' => '1',
		'table' => 'user',
		'sortnum' => '14',
		'searchField' => 'mis_auto_guikc.createid',
		'transform' => '1',
		'unique' => '0',
		'validate' => '0',
		'required' => '0',
		'unfunc' => array(
			'0' => array(
				'0' => 'ungetFieldBy',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'id',
					'2' => 'name',
					'3' => 'User',
				),
			),
		),
	),
	'createtime' => array(
		'name' => 'createtime',
		'showname' => '创建时间',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'createtime',
		'sortnum' => '15',
		'fieldtype' => 'date',
		'fieldcategory' => 'date',
		'searchField' => 'mis_auto_guikc.createtime',
		'conditions' => '',
		'type' => 'time',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '1',
		'func' => array(
			'0' => array(
				'0' => 'transtime',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'helpvalue' => '',
		'transform' => '1',
		'unique' => '0',
		'validate' => '0',
		'required' => '0',
		'unfunc' => array(
			'0' => array(
				'0' => 'untranstime',
			),
		),
		'unfuncdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
	),
);

$extedsList = require 'listExtend.inc.php';
return array_merge($original , $extedsList);
?>