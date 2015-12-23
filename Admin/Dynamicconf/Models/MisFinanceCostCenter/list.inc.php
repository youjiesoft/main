<?php
return array(
	'0' => array(
		'name' => 'code',
		'showname' => '成本中心编码',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'code',
		'sortnum' => '1',
	),
	'1' => array(
		'name' => 'name',
		'showname' => '成本中心名称',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'name',
		'func' => array(
			'0' => array(
				'0' => 'getnextUrl',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => '#id#',
					'2' => 'parentid',
					'3' => 'index',
					'4' => '###',
				),
			),
		),
		'sortnum' => '2',
	),
	'2' => array(
		'name' => 'name',
		'showname' => '成本中心搜索名称',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'name',
		'func' => array(
			'0' => array(
				'0' => 'getnextUrl',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => '#id#',
					'2' => 'parentid',
					'3' => 'index',
					'4' => '###',
				),
			),
		),
		'sortnum' => '3',
	),
	'3' => array(
		'name' => 'remark',
		'showname' => '备注',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'remark',
		'sortnum' => '4',
	),
	'4' => array(
		'name' => 'status',
		'showname' => '状态',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'status',
		'func' => array(
			'0' => array(
				'0' => 'getStatus',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'sortnum' => '5',
	),
	'5' => array(
		'name' => 'action',
		'showname' => '操作',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
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
		'sortname' => 'status',
		'sortnum' => '6',
	),
);

?>