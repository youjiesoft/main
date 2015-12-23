<?php 
return array(
		'0' => array(
				'modulename' => 'MisMessageUser',
				'relmodule'=>'MisMessage/index/messageType/inboxself',
				'authmodule'=>'authId',
				'title' => '邮件',
				'span'=>'icon-envelope',
				'sortnum'=>'1',
				'color'=>'tml-bg-cyan',
				'countcolor'=>'tml-c-blue',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'收件箱',
								'rtitle'=>'个人邮件',
								'rotitle'=>'条',
								'map'=>'status=1 and commit=1 and  readedStatus=0 and  returnmessage=1 and recipient=$uid and messageType=0',
		
						),
						'1'=>array(
								'reltitle'=>'系统邮件',
								'rtitle'=>'系统邮件',
								'rotitle'=>'条',
								'map'=>'status=1 and commit=1 and  readedStatus=0 and  returnmessage=1 and recipient=$uid and messageType=1',
						),
				),
		),
		'1' => array(
				'modulename' => 'MisWorkReadStatementView',
				'relmodule'=>'MisWorkReadStatement/index',
				'authmodule'=>'authId',
				'title' => '报告',
				'span'=>'icon-volume-up',
				'color'=>'tml-bg-green',
				'countcolor'=>'tml-c-green',
				'sortnum'=>'2',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'待阅工作报告',
								'rtitle'=>'待阅报告',
								'rotitle'=>'条',
								'map'=>'mis_work_read_statement.userid=$uid and  mis_work_read_statement.status=1   and mis_work_read_statement.readstatus =1 ',
		
						),
				),
		),
		'2' => array(
				'modulename' => 'MisSystemFunctionalBox',
				'relmodule'=>'MisSystemFunctionalBox/index',
				'authmodule'=>'authId',
				'title' => '助手',
				'span'=>'icon-tag',
				'color'=>'tml-bg-blue',
				'countcolor'=>'tml-c-blue',
				'sortnum'=>'3',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'功能盒子',
								'rtitle'=>'功能应用',
								'rotitle'=>'条',
								'map'=>'mis_system_functional_box.status=1  ',
		
						),
				),
		),
		'3' => array(
				'modulename' => 'MisUserEvents',
				'relmodule'=>'MisUserEvents/index',
				'authmodule'=>'authId',
				'title' => '日程',
				'span'=>'icon-calendar',
				'sortnum'=>'4',
				'color'=>'tml-bg-orange',
				'countcolor'=>'tml-c-orange',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'我的日程',
								'rtitle'=>'个人日程',
								'rotitle'=>'条',
								'map'=>'status=1 and userid=$uid  and  enddate>$time  and startdate<$time+86400 ',
		
						),
						'1'=>array(
								'reltitle'=>'协同日程',
								'rtitle'=>'协同日程',
								'rotitle'=>'条',
								'map'=>'FIND_IN_SET($uid, personid) and  status=1 and enddate>$time and  startdate<$time+86400 ',
		
						),
				),
		),
		'4' => array(
				'modulename' => 'MisOaMeetingManage',
				'relmodule'=>'MisOaMeetingPerson/index',
				'authmodule'=>'authId',
				'title' => '会议',
				'span'=>'icon-comments',
				'color'=>'tml-bg-purple',
				'countcolor'=>'tml-c-purple',
				'sortnum'=>'5',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'会议参与查看',
								'rtitle'=>'未查阅',
								'rotitle'=>'条',
								'map'=>'status=1 and find_in_set( $uid,notread ) and starttime>$nowtime and stepType=1',
		
						),
				),
		),
		'5' => array(
				'modulename' => 'MisHrBasicEmployee',
				'relmodule'=>'MisHrRemindBecomeEmployee/index',
				'authmodule'=>'mishrbecomeemployee_index',
				'title' => '转正',
				'span'=>'icon-comment-alt',
				'color'=>'tml-bg-green',
				'countcolor'=>'tml-c-green',
				'sortnum'=>'6',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'待办转正人员',
								'rtitle'=>'待办转正',
								'rotitle'=>'位',
								'map'=>'status=1 and  workstatus=2 and  transferdate<=$time+2592000',
						),
				),
		),
		'6' => array(
				'modulename' => 'MisHrBasicEmployeeContract',
				'relmodule'=>'MisHrRemindEmployeeContract/index',
				'authmodule'=>'mishrbasicemployeecontract_index',
				'title' => '合同',
				'span'=>'icon-paste',
				'color'=>'tml-bg-orange',
				'countcolor'=>'tml-c-orange',
				'sortnum'=>'7',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'待续签合同人员',
								'rtitle'=>'即将到期',
								'rotitle'=>'份合同',
								'map'=>'status=1 and  endtime<=$time+2592000   and endtime<>0   and contractstatus=0',

						),
				),
		),
		'7' => array(
				'modulename' => 'MisHrEmployeeLeaveManagement',
				'relmodule'=>'MisHrEmployeeLeaveManagement/index',
				'authmodule'=>'mishremployeeleavemanagement_index',
				'title' => '请假',
				'span'=>'icon-comments',
				'color'=>'tml-bg-blue',
				'countcolor'=>'tml-c-blue',
				'sortnum'=>'8',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'员工请假申请',
								'rtitle'=>'假期即将结束',
								'rotitle'=>'条',
								'map'=>'status=1 and endleavedate>$time and   endleavedate<=$time+86400*3   and auditState=3 ',
		
						),
				),
		),
		'8' => array(
				'modulename' => 'MisCarInsurance',
				'relmodule'=>'MisCarInsurance/index',
				'authmodule'=>'miscarinsurance_index',
				'title' => '保险',
				'span'=>'icon-comments',
				'color'=>'tml-bg-blue',
				'countcolor'=>'tml-c-blue',
				'sortnum'=>'9',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'车辆保险',
								'rtitle'=>'即将到期',
								'rotitle'=>'份',
								'map'=>'status=1 and  expire_time<=$time+2592000   and expire_time<>0',
		
						),
				),
		),
		'9' => array(
				'modulename' => 'MisWorkPlan',
				'relmodule'=>'MisWorkPlanContent/index',
				'authmodule'=>'authId',
				'title' => '计划查阅',
				'span'=>'icon-volume-up',
				'color'=>'tml-bg-green',
				'countcolor'=>'tml-c-green',
				'sortnum'=>'10',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'工作计划查阅',
								'rtitle'=>'工作计划查阅',
								'rotitle'=>'条',
								'map'=>'mis_work_plan.stepType=1 and FIND_IN_SET($uid, lookpeople)',
		
						),
				),
		),
		'10' => array(
				'modulename' => 'MisOaMeetingSummary',
				'relmodule'=>'MisOaMeetingSummaryRead/index',
				'authmodule'=>'authId',
				'title' => '会议纪要',
				'span'=>'icon-comments',
				'color'=>'tml-bg-purple',
				'countcolor'=>'tml-c-purple',
				'sortnum'=>'11',
				'list'=>array(
						'0'=>array(
								'reltitle'=>'会议纪要查阅',
								'rtitle'=>'未查阅',
								'rotitle'=>'条',
								'map'=>'status=1 and find_in_set( $uid,notread ) and stepType=1',
						),
				),
		),
);

?>