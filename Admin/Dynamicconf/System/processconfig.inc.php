<?php 
return array(
	'3' => array(
		'modulename' => 'MisHrBecomeEmployee',
		'title' => '人事转正审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '23',
				'typeid' => '1',
				'rules' => '',
				'crosslevel' => '0',
			),
		),
	),
	'4' => array(
		'modulename' => 'MisHrEmployeeLeaveManagement',
		'title' => '人事请假审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '25',
				'typeid' => '1',
				'rules' => '',
				'crosslevel' => '0',
			),
		),
	),
	'5' => array(
		'modulename' => 'MisHrPersonnelTrainInfo',
		'title' => '人事调职审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '21',
				'typeid' => '1',
				'rules' => '#level# &lt;=8',
				'crosslevel' => '0',
			),
			'1' => array(
				'pid' => '57',
				'typeid' => '1',
				'rules' => '#level# ==9',
				'crosslevel' => '0',
			),
		),
	),
	'6' => array(
		'modulename' => 'MisHrLeaveEmployee',
		'title' => '人事离职审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '22',
				'typeid' => '1',
				'rules' => '#level# &lt;=8',
				'crosslevel' => '0',
			),
			'1' => array(
				'pid' => '56',
				'typeid' => '1',
				'rules' => '#level# ==9',
				'crosslevel' => '0',
			),
		),
	),
	'13' => array(
		'modulename' => 'MisEquipmentApplication',
		'title' => '装备领用申请审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '49',
				'typeid' => '11',
				'rules' => '',
				'crosslevel' => '0',
			),
		),
	),
	'14' => array(
		'modulename' => 'MisEquipmentReturn',
		'title' => '装备退还审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '50',
				'typeid' => '11',
				'rules' => '',
				'crosslevel' => '0',
			),
		),
	),
	'15' => array(
		'modulename' => 'MisHrPersonnelApplicationInfo',
		'title' => '特殊人员申请审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '51',
				'typeid' => '1',
				'rules' => '',
				'crosslevel' => '0',
			),
		),
	),
	'16' => array(
		'modulename' => 'MisHrInvitereSpecialForm',
		'title' => '特殊人员招聘审批',
		'process_rule' => array(
			'0' => array(
				'pid' => '52',
				'typeid' => '1',
				'rules' => '',
				'crosslevel' => '0',
			),
		),
	),
	'17' => array(
		'modulename' => 'MisRequestCar',
		'title' => '派车申请',
		'process_rule' => array(
			'0' => array(
				'pid' => '8',
				'typeid' => '5',
				'rules' => '',
				'crosslevel' => '0',
			),
		),
	),
);

?>