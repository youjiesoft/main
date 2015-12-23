<?php
/**
 * 带审核的Action列表集合
 * key 和 unit 是针对手机批复内容的设置
 */
return array(
		array(
				"model" => "MisKnowledgeManage",
				"name" => "文章管理",
				"session" => "misknowledgemanage",
				"level" => "-16"
		),
		array(
				"model" => "MisRequestCar",
				"name" => "派车申请",
				"session" => "misrequestcar",
				"level" => "-16"
		),
		array(
				"model" => "MisHrPersonnelApplicationInfo",
				"name" => "人员申请管理",
				"session" => "mishrpersonnelapplicationinfo",
				"level" => "-8"
		),
		array(
				"model" => "MisHrPersonnelTransferManage",
				"name" => "员工调职管理",
				"session" => "mishrpersonneltransfermanage",
				"level" => "-8"
		),
		array(
				"model" => "MisHrPersonnelQuitInfo",
				"name" => "员工离职管理",
				"session" => "mishrpersonnelquitinfo",
				"level" => "-8"
		),
		array(
				"model" => "MisHrPersonnelLeaveInfo",
				"name" => "员工请假管理",
				"session" => "mishrpersonnelleaveinfo",
				"level" => "-8",
				"key"=>array(
						'name'=>'员工请假:',
						'orderno'=>'请假编号:',
						'remark'=>'请假原因:',
						'hours'=>'请假时间:'
				),
				"unit"=>array(
						'name'=>'',
						'orderno'=>'',
						'remark'=>'',
						'hours'=>'',
				),
		),
);
?>