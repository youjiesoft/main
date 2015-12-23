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
		'name' => 'title',
		'showname' => '标题',
		'shows' => '1',
		'widths' => '100',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'title',
		'sortnum' => '2',
	),
	'2' => array(
		'name' => 'content',
		'showname' => '内容',
		'shows' => '1',
		'widths' => '100',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'content',
		'sortnum' => '3',
	),
	'3' => array(
		'name' => 'url',
		'showname' => '链接URL',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'url',
		'sortnum' => '4',
	),
	'4' => array(
		'name' => 'userid',
		'showname' => '操作员',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'userid',
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
		'sortnum' => '5',
	),
	'5' => array(
		'name' => 'remark',
		'showname' => '备注',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'remark',
		'sortnum' => '6',
	),
	'6' => array(
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
		'sortnum' => '7',
	),
	'7' => array(
		'name' => 'action',
		'showname' => '操作',
		'shows' => '1',
		'widths' => '200',
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
		'sortnum' => '8',
	),
);

?>