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
	),
	'1' => array(
		'name' => 'code',
		'showname' => '银行账号',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'code',
		'sortnum' => '2',
	),
	'2' => array(
		'name' => 'name',
		'showname' => '账号全称',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'name',
		'sortnum' => '3',
	),
	'3' => array(
		'name' => 'sname',
		'showname' => '账号简称',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'sname',
		'sortnum' => '4',
	),
	'4' => array(
		'name' => 'catalog',
		'showname' => '类别',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'catalog',
		'sortnum' => '5',
	),
	'5' => array(
		'name' => 'amount',
		'showname' => '余额',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'amount',
		'sortnum' => '6',
	),
	'6' => array(
		'name' => 'bankid',
		'showname' => '账号银行关联',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => 'MisFinanceBank',
		'sortname' => 'bankid',
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
					'3' => 'MisFinanceBank',
				),
			),
		),
		'sortnum' => '7',
	),
	'7' => array(
		'name' => 'sname',
		'showname' => '持卡人',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'sname',
		'sortnum' => '8',
	),
	'8' => array(
		'name' => 'remark',
		'showname' => '描述',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'remark',
		'sortnum' => '9',
	),
	'9' => array(
		'name' => 'action',
		'showname' => '操作',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'status',
		'sortnum' => '10',
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
					'6' => '银行账户',
					'7' => '520',
					'8' => '350',
				),
			),
			'1' => array(
				'0' => array(
					'0' => '#status#',
				),
			),
		),
	),
	/* '10' => array(
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
		'sortnum' => '11',
	), */
);

?>