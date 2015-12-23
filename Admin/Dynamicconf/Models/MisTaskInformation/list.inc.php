<?php 
return array(
	'1' => array(
		'name' => 'taskid',
		'showname' => '任务名称',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'taskid',
		'sortnum' => '1',
		'func' => array(
			'0' => array(
				'0' => 'getFieldBy',
                '1' => 'missubstr',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
					'1' => 'id',
					'2' => 'title',
					'3' => 'mis_task',
				),
                '1' => array(
					'0' => '###',
					'1' => '30',
				),
			),
		),
		'issearch' => '1',
		'isearch' => '0',
		'searchField' => 'mis_task.title',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '1',
		'searchsortnum' => '0',
	),
	'2' => array(
		'name' => 'pid',
		'showname' => '父任务名称',
		'shows' => '0',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'pid',
		'sortnum' => '2',
		'issearch' => '0',
		'isearch' => '0',
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
					'2' => 'title',
					'3' => 'mis_task',
				),
			),
		),
		'searchField' => 'mis_task_information.pid',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
    '3' => array(
		'name' => 'remark',
		'showname' => '描述',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'remark',
		'sortnum' => '3',
        'func' => array(
			'0' => array(
                '0' => 'missubstr',
			),
		),
		'funcdata' => array(
			'0' => array(
                '0' => array(
					'0' => '###',
					'1' => '30',
				),
			),
		),
		'searchField' => 'mis_task_information.remark',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
    '4' => array(
		'name' => 'trackuser',
		'showname' => '跟踪人员',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'trackuser',
		'sortnum' => '4',
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
    ),
	'5' => array(
		'name' => 'executeuser',
		'showname' => '负责人员',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'executeuser',
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
					'3' => 'user',
				),
			),
		),
		'issearch' => '1',
		'isearch' => '0',
		'searchField' => 'mis_task_information.executeuser',
		'table' => 'user',
		'field' => 'id',
		'conditions' => '',
		'type' => 'db',
		'isallsearch' => '1',
		'searchsortnum' => '0',
	),
	'6' => array(
		'name' => 'begindate',
		'showname' => '计划开始时间',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'begindate',
		'sortnum' => '6',
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
		'searchField' => 'mis_task_information.begindate',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'time',
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
	'7' => array(
		'name' => 'enddate',
		'showname' => '计划完成时间',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'enddate',
		'sortnum' => '7',
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
		'searchField' => 'mis_task_information.enddate',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'time',
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
	'8' => array(
		'name' => 'executingstatus',
		'showname' => '执行状态',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'executingstatus',
		'sortnum' => '8',
		'func' => array(
			'0' => array(
				'0' => 'getTaskInformationExecutingStatus',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'searchField' => 'mis_task_information.executingstatus',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'select|MisTaskInformation_executingstatus',
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
    '9' => array(
		'name' => 'realityknowdate',
		'showname' => '查看时间',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'realityknowdate',
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
                    '1' => 'Y-m-d H:i',
				),
			),
		),
		'searchField' => 'mis_task_information.realityknowdate',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'time',
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
	'10' => array(
		'name' => 'realitybegindate',
		'showname' => '实际开始时间',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'realitybegindate',
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
                    '1' => 'Y-m-d H:i',
				),
			),
		),
		'searchField' => 'mis_task_information.realitybegindate',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'time',
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
    '11' => array(
		'name' => 'realityenddate',
		'showname' => '实际完成时间',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'realityenddate',
		'sortnum' => '11',
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
		'searchField' => 'mis_task_information.realityenddate',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'time',
		'issearch' => '1',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
    '12' => array(
		'name' => 'urgentstatus',
		'showname' => '紧急状况',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'urgentstatus',
		'sortnum' => '12',
        'func' => array(
			'0' => array(
				'0' => 'getTaskInformationUrgentStatus',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'searchField' => 'mis_task_information.urgentstatus',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
    '13' => array(
		'name' => 'difficulty',
		'showname' => '困难程度',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'difficulty',
		'sortnum' => '13',
        'func' => array(
			'0' => array(
				'0' => 'getTaskInformationDifficult',
			),
		),
		'funcdata' => array(
			'0' => array(
				'0' => array(
					'0' => '###',
				),
			),
		),
		'searchField' => 'mis_task_information.difficulty',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	),
    '14' => array(
		'name' => 'chedule',
		'showname' => '完成进度',
		'shows' => '1',
		'widths' => '',
		'sorts' => '0',
		'models' => '',
		'sortname' => 'chedule',
		'sortnum' => '14',
		'searchField' => 'mis_task_information.chedule',
		'table' => '',
		'field' => '',
		'conditions' => '',
		'type' => 'text',
		'issearch' => '0',
		'isallsearch' => '0',
		'searchsortnum' => '0',
	)
);

?>