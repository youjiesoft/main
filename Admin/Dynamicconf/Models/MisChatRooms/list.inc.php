<?php
return array(
	'0' => array(
		'name' => 'id',
		'showname' => '编号',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'id',
		'sortnum' => '1',
	),
	'2' => array(
		'name' => 'name',
		'showname' => '房间名称',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'name',
		'sortnum' => '3',
	),
	'3' => array(
		'name' => 'channel',
		'showname' => '房间频道',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'channel',
		'sortnum' => '4',
	),
	'4' => array(
		'name' => 'totalnum',
		'showname' => '房间容纳人数（0为不限）',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'totalnum',
		'sortnum' => '5',
	),
	'5' => array(
		'name' => 'pass',
		'showname' => '房间密码',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'pass',
		'sortnum' => '6',
	),
	'6' => array(
		'name' => 'sort',
		'showname' => '房间排序',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'sort',
		'sortnum' => '7',
	),
	'12' => array(
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
		'sortnum' => '13',
	),
	'13' => array(
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
			'1' => array(
				'0' => array(
					'0' => '#id#',
					'1' => '#name#',
				),
			),
		),
		'sortnum' => '14',
	),
);

?>