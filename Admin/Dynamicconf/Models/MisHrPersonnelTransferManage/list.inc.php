<?php 
return array(
	'0' => array(
		'name' => 'id',
		'showname' => 'ID',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'id',
		'sortnum' => '1',
		'issearch' => '',
		'isexport' => '1',
		'searchField' => '',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => '',
		'isallsearch' => '',
		'searchsortnum' => '',
	),
	'1' => array(
		'name' => 'orderno',
		'showname' => '调动编号',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'orderno',
		'sortnum' => '2',
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.orderno',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '1',
		'searchsortnum' => '2',
	),
	'2' => array(
		'name' => 'personid',
		'showname' => '员工编号',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'personid',
		'sortnum' => '3',
		'issearch' => '',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.personid',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '',
		'searchsortnum' => '3',
	),
	'3' => array(
		'name' => 'name',
		'showname' => '员工姓名',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'name',
		'sortnum' => '4',
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.name',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '1',
		'searchsortnum' => '4',
	),
	'4' => array(
		'name' => 'deptid',
		'showname' => '调动前部门',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'deptid',
		'sortnum' => '5',
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
					'3' => 'mis_system_department',
				),
			),
		),
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.deptid',
		'table' => 'mis_system_department',
		'field' => 'id',
		'conditions' => '',
		'type' => 'db',
		'isallsearch' => '1',
		'searchsortnum' => '5',
	),
	'5' => array(
		'name' => 'newdeptid',
		'showname' => '调动后部门',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'newdeptid',
		'sortnum' => '6',
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
					'3' => 'mis_system_department',
				),
			),
		),
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.newdeptid',
		'table' => 'mis_system_department',
		'field' => 'id',
		'conditions' => '',
		'type' => 'db',
		'isallsearch' => '1',
		'searchsortnum' => '6',
	),
	'6' => array(
		'name' => 'dutyname',
		'showname' => '调动前职位',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'dutyname',
		'sortnum' => '7',
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.dutyname',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '1',
		'searchsortnum' => '7',
	),
	'7' => array(
		'name' => 'newdutyname',
		'showname' => '调动后职位',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'newdutyname',
		'sortnum' => '8',
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.newdutyname',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '1',
		'searchsortnum' => '8',
	),
	'8' => array(
		'name' => 'indate',
		'showname' => '入职日期',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'indate',
		'sortnum' => '9',
		'func' => array(
			'0' => array(
				'0' => 'transTime',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.indate',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'time',
		'isallsearch' => '',
		'searchsortnum' => '9',
	),
	'9' => array(
		'name' => 'transferdate',
		'showname' => '生效时间',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'transferdate',
		'sortnum' => '10',
		'func' => array(
			'0' => array(
				'0' => 'transTime',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.transferdate',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'time',
		'isallsearch' => '',
		'searchsortnum' => '10',
	),
	'10' => array(
		'name' => 'transfer',
		'showname' => '调动原因',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'transfer',
		'sortnum' => '11',
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.transfer',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '',
		'searchsortnum' => '11',
	),
	'11' => array(
		'name' => 'remark',
		'showname' => '备注说明',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'remark',
		'sortnum' => '12',
		'issearch' => '',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.remark',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '',
		'searchsortnum' => '12',
	),
	'12' => array(
		'name' => 'createid',
		'showname' => '创建人',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'createid',
		'sortnum' => '13',
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
		'issearch' => '1',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.createid',
		'table' => 'user',
		'field' => 'id',
		'conditions' => '',
		'type' => 'db',
		'isallsearch' => '',
		'searchsortnum' => '13',
	),
	'13' => array(
		'name' => 'createtime',
		'showname' => '创建时间',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'createtime',
		'sortnum' => '14',
		'func' => array(
			'0' => array(
				'0' => 'transTime',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'issearch' => '',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.createtime',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '',
		'searchsortnum' => '14',
	),
	'14' => array(
		'name' => 'updateid',
		'showname' => '修改人',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'updateid',
		'sortnum' => '15',
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
		'issearch' => '',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.updateid',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '',
		'searchsortnum' => '15',
	),
	'15' => array(
		'name' => 'updatetime',
		'showname' => '修改时间',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'updatetime',
		'sortnum' => '16',
		'func' => array(
			'0' => array(
				'0' => 'transTime',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'issearch' => '',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.updatetime',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '',
		'searchsortnum' => '16',
	),
	'16' => array(
		'name' => 'auditState',
		'showname' => '审核状态',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'auditState',
		'func' => array(
			'0' => array(
				'0' => 'getAuditState',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => '#id#',
					'2' => '#ptmptid#',
				),
			),
		),
		'sortnum' => '17',
		'issearch' => '',
		'isexport' => '1',
		'searchField' => 'mis_hr_personnel_train_info.auditState',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '',
		'searchsortnum' => '17',
	),
	'17' => array(
		'name' => 'action',
		'showname' => '操作',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'status',
		'sortnum' => '18',
		'issearch' => '',
		'searchField' => '',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => '',
		'isallsearch' => '',
		'searchsortnum' => '',
		'status' => '1',
		'extention_html_end' => array(
			'0' => ' ',
		),
		'func' => array(
			'0' => array(
				'0' => 'getOperateStatus',
			),
			'1' => array(
				'0' => 'getStatus',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '#createid#',
					'1' => '#auditState#',
					'2' => 'id/#id#',
					'3' => '',
					'4' => '',
					'5' => 'dialog',
					'6' => '员工调职',
					'7' => '660',
					'8' => '520',
				),
			),
			'1' => array(
				'0' => array(
					'0' => '#status#',
				),
			),
		),
	),
);

?>