<?php
$general = array(
	'ADD'=>array(
		'insert' => array(
			'name'=>'保存',
			'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_blue">保存</button></li>',
		),
		'confirmcmit' => array(
			'name'=>'提交',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="ConfirmCommit(this.form,\'__MODULE__\');">提交</button></li>',
		),
		'startprocess' => array(
			'name'=>'发起流程',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess(this.form,\'__MODULE__\');">发起流程</button></li>',
		),
	),
	'EDIT'=>array(
		'update' => array(
			'name'=>'保存',
			'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_blue">保存</button></li>',
		),
		'confirmcmit' => array(
			'name'=>'提交',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="ConfirmCommit(this.form,\'__MODULE__\');">提交</button></li>',
		),
		'startprocess' => array(
			'name'=>'发起流程',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess(this.form,\'__MODULE__\');">发起流程</button></li>',
		),
		'delete' => array(
			'name'=>'删除',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_red" onclick="deleteRecord(this,\'1\',\'__MODULE__\');">删除</button></li>',
		),
	),
	'CHANGEEDIT'=>array(
		'lookupUpdateProcess' => array(
			'name'=>'变更',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="changeRecord(this.form,\'__MODULE__\');">变更</button></li>',
		),
	),
	'AUDITEDIT'=>array(
		'lookupAuditTuiProcess' => array(
			'name'=>'节点任务',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="auditTuiProcess(this,\'__MODULE__\');">节点任务</button></li>',
		),
		'auditProcess' => array(
			'name'=>'提交',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue js-auditProcess">提交</button></li>',
		),
		'backprocess' => array(
			'name'=>'退回',
			'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_red js-backProcess" m="__MODULE__">退回</button></li>',
		),
	),
);
$special = array(
	"MisRequestCar"=>array(
		'ADD'=>array(
			'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess_dialog(this.form,\'__MODULE__\');">发起流程</button></li>',),
		),
		'EDIT'=>array(
			'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess_dialog(this.form,\'__MODULE__\');">发起流程</button></li>',),
		),
	),
	"MisLogisticsFixLog"=>array(
			'ADD'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess(this.form,\'__MODULE__\');">发起流程</button></li>',),
			),
			'EDIT'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess(this.form,\'__MODULE__\');">发起流程</button></li>',),
			),
	),
	"MisFinanceMarkLog"=>array(
			'ADD'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess_dialog(this.form,\'__MODULE__\');">发起流程</button></li>',),
			),
			'EDIT'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="StartProcess_dialog(this.form,\'__MODULE__\');">发起流程</button></li>',),
			),
	),
	"MisWorkStatement"=>array(
		'ADD'=>array(
			'insert' => array(
				'more' => 1,
				'list' => array(
					0 => array(
						'name'=>'保存',
						'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">保存</button></li>',
					),
					1 => array(
						'name'=>'发送',
						'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_green" onclick="sendWorkStateMent(this.form);">发送</button></li>',
					),
				)
			),
		),
		'EDIT'=>array(
			'update' => array(
				'more' => 1,
				'list' => array(
					0 => array(
						'name'=>'保存',
						'rules'=>'#sendStatus#!=1',
						'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">保存</button></li>',
					),
					1 => array(
						'name'=>'发送',
						'rules'=>'#sendStatus#!=1',
						'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_green" onclick="sendWorkStateMent(this.form);">发送</button></li>',
					),
					2 => array(
						'name'=>'撤回',
						'rules'=>'#readstatus#!=1&&#sendStatus#==1',
						'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_green" onclick="recallworkstastement(this.form);">撤回</button></li>',
					),
				),
			),
			'delete' => array(
					'name'=>'删除',
					'rules'=>'#readstatus#!=1&&#sendStatus#!=1',
					'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_red" onclick="deleteRecord(this,\'1\',\'__MODULE__\');">删除</button></li>',
			),
		),
	),
	'MisHrPersonnelManagement'=>array(
		'ADD' => array('startprocess'=>null),
		'ADDFORLEAVE'=>array(
		  	'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrEmployeeLeaveManagement\');">发起流程</button></li>',),
		),
		'ADDLEAVE'=>array(
				'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrLeaveEmployee\');">发起流程</button></li>',),
		),
		'ADDTRANSFER'=>array(
				'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrPersonnelTrainInfo\');">发起流程</button></li>',),
		),
			'EDITGENERAL'=>array(
					'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
			),
	),
	'MisHrBasicEmployee'=>array(
			'ADD' => array('startprocess'=>null),
			'ADDFORLEAVE'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrEmployeeLeaveManagement\');">发起流程</button></li>',),
			),
			'ADDLEAVE'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrLeaveEmployee\');">发起流程</button></li>',),
			),
			'ADDTRANSFER'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrPersonnelTrainInfo\');">发起流程</button></li>',),
			),
			'ADDBECOME'=>array(
					'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrBecomeEmployee\');">发起流程</button></li>',),
			),
			'SETPROBATION'=>array(
					'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
			),
			'EDITGENERAL'=>array(
					'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
			),
			
	),
		'MisHrRemindBecomeEmployee'=>array(
				'SETPROBATION'=>array(
						'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
				),
		),
		'MisHrPersonnelLeave'=>array(
			'ADD' => array('startprocess'=>null),
			'EDITGENERAL'=>array(
					'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
			),
		),
		"MisSystemAnnouncement"=>array(
			'ADD'=>array(
				'insert' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="missystemannouncementsave(this.form,5);">保存</button></li>',
						),
						1 => array(
							'name'=>'发布',
							//'rules'=>'$_REQUEST["typeid"]!=1',
							'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="missystemannouncementsave(this.form,1);">发布</button></li>',
						),
					)
				),
			),
			'EDIT'=>array(
				'update' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="missystemannouncementsaveEdit(this.form,5);">保存</button></li>',
						),
						1 => array(
							'name'=>'发布',
							//'rules'=>'$_REQUEST["typeid"]!=1',
							'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_blue" onclick="missystemannouncementsaveEdit(this.form,1);">发布</button></li>',
						),
					)
				),
			),
			'AUDIT'=>array(
				'announcementAuditUpdate' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'审核',
							'html'=>'<li><button class="tml-btn tml-btn-primary" type="submit">审核</button></li>',
						),
						1 => array(
							'name'=>'打回',
							'html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="missystemannouncementaudit(this.form,3)">打回</button></li>',
						),
					),
				),
			),
		),
		"MisMessagePhone"=>array(
			'ADD'=>array(
				'insert' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'发送短信息',
							'html'=>'<li><button class="tml-btn tml-btn-primary" type="button" onclick="commitviewAddWriteForm(this.form);">发送短信息</button></li>',
						),
						1 => array(
							'name'=>'保存草稿',
							'html'=>'<li><button class="tml-btn tml-btn-primary" type="submit">保存草稿</button></li>',
						),
					)
				),
			),
			'EDIT'=>array(
				'update' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'发送短信息',
							'html'=>'<li><button class="tml-btn tml-btn-primary" type="button" onclick="commitviewAddWriteForm(this.form);">发送短信息</button></li>',
						),
						1 => array(
							'name'=>'保存草稿',
							'html'=>'<li><button class="tml-btn tml-btn-primary" type="submit">保存草稿</button></li>',
						),
					)
				),
			),
		),
	"MisWorkExecuting"=>array(
		'EDIT'=>array(
			'update' => array(
				'more' => 1,
				'list' => array(
					0 => array(
						'name'=>'接收',
						'rules'=>'$_REQUEST["isshow"]==1&&$_REQUEST["dg"]==1',
						'html'=>'<li><button class="tml-btn tml-btn-primary" type="button" onclick="mislogisticsfixhandlesave(this.form,2,1)">接收</button></li>',
					),
					1 => array(
						'name'=>'完成',
						'rules'=>'$_REQUEST["dg"]==1',
						'html'=>'<li><button class="tml-btn tml-btn-blue" type="button" onclick="mislogisticsfixhandlesave(this.form,1,1)">完成</button></li>',
					),
					2 => array(
						'name'=>'接收',
						'rules'=>'$_REQUEST["isshow"]==1&&$_REQUEST["dg"]!=1',
						'html'=>'<li><button class="tml-btn tml-btn-primary" type="button" onclick="mislogisticsfixhandlesave(this.form,2,2)">接收</button></li>',
					),
					3 => array(
						'name'=>'完成',
						'rules'=>'$_REQUEST["dg"]!=1',
						'html'=>'<li><button class="tml-btn tml-btn-primary" type="button" onclick="mislogisticsfixhandlesave(this.form,1,2)">完成</button></li>',
					),
						
					
				)
			),
			'delete' => array(
			),
		),
	),
		
		'MisHrLeaveEmployee'=>array(
				'ADD' => array('insert'=>null),
				'ADDLEAVE'=>array(
						'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrLeaveEmployee\');">发起流程</button></li>'),
				),
		),
		'MisHrBecomeEmployee'=>array(
				'ADDBECOME'=>array(
						'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrBecomeEmployee\');">发起流程</button></li>'),
				),
		),
		'MisHrPersonnelTrainInfo'=>array(
				'ADDTRANSFER'=>array(
						'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrPersonnelTrainInfo\');">发起流程</button></li>'),
				),
		),
		'MisHrPersonnelTrainInfo'=>array(
				'ADDTRANSFER'=>array(
						'startprocess' => array('name'=>'发起流程','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'MisHrPersonnelTrainInfo\');">发起流程</button></li>'),
				),
		),
		'MisHrImportRoster'=>array(
					'INDEX'=>array(
					'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
			),
		),
		'MisHrImportLeave'=>array(
				'INDEX'=>array(
						'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
				),
		),
		"MisOaMeetingManage"=>array(
			'COMPLETE'=>array(
				'update' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
					)
				),
			),
		),
		'MisHrEvaluationTrain'=>array(
				'ACCREDIT'=>array(
						'insert' => array('name'=>'保存','html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>'),
				),
		),
		'MisHrEmployeeLeaveManagement'=>array(
				'ADD' => array('insert'=>null),
				),
		"MisOaMeetingPerson"=>array(
				'EDIT'=>array(
						'update' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'参与',
												'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">参与</button></li>',
										),
										1 => array(
												'name'=>'不参与',
												'html'=>'<li><button type="button" onclick="openNotAttendDialog();" class="tml-btn tml-btn-primary">不参与</button></li>',
										),
								)
						),
				),
		),
		"MisSystemAnnouncementSet"=>array(
				'EDIT'=>array(
					'delete' => array(),
				),
			),
		"MisPurchaseApplymas"=>array(
			'ADD'=>array(
				'startprocess' => array('name'=>'提交','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'__MODULE__\');">提交</button></li>',),
			),
			'EDIT'=>array(
				'startprocess' => array('name'=>'提交','html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="StartProcess_dialog(this.form,\'__MODULE__\');">提交</button></li>',),
			),
		),
		"MisWorkPlan"=>array(
			'ADD'=>array(
				'insert' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
						1 => array(
							'name'=>'发送',
							'html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="sendMisWorkPlan(this.form);">发送</button></li>',
						),
					)
				),
			),
			'EDIT'=>array(
				'update' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
						1 => array(
							'name'=>'发送',
							'html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="sendMisWorkPlan(this.form);">发送</button></li>',
						),
					),
				),
			),
			'VIEW'=>array(
				'update' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
					),
				),
			),
		),
		"MisWorkPlanContent"=>array(
			'EDIT'=>array(
				'delete' => array(
				),
			),
		),
		"MisOaMeetingManage"=>array(
			'ADD'=>array(
				'insert' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
						1 => array(
							'name'=>'发送',
							'html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="sendMisOaMeetingManage(this.form);">发送</button></li>',
						),
					)
				),
			),
			'EDIT'=>array(
				'update' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
						1 => array(
							'name'=>'发送',
							'html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="sendMisOaMeetingManage(this.form);">发送</button></li>',
						),
					),
				),
			),
		),
		"MisOaMeetingPerson"=>array(
			'EDIT'=>array(
				'update' => array(
				),
				'delete' => array(
				),
			),
		),
		"MisOaMeetingSummary"=>array(
			'ADD'=>array(
				'insert' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
						1 => array(
							'name'=>'发送',
							'html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="sendMisOaMeetingSummary(this.form);">发送</button></li>',
						),
					)
				),
			),
			'EDIT'=>array(
				'update' => array(
					'more' => 1,
					'list' => array(
						0 => array(
							'name'=>'保存',
							'html'=>'<li><button type="submit" class="tml-btn tml-btn-blue">保存</button></li>',
						),
						1 => array(
							'name'=>'发送',
							'html'=>'<li><button type="button" class="tml-btn tml-btn-primary" onclick="sendMisOaMeetingSummary(this.form);">发送</button></li>',
						),
					),
				),
			),
		),
		"MisOaMeetingSummaryRead"=>array(
			'EDIT'=>array(
				'update' => array(
				),
				'delete' => array(
				),
			),
		),
		"Selectlist"=>array(
			'EDIT'=>array(
				'delete' => array(
				),
			),
		),
		"MisAutoHxr"=>array(
				'ADD'=>array(
						'insert' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								)
						),
				),
				'EDIT'=>array(
						'update' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								),
						),
						'delete' => array(
								'name'=>'删除',
								'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_red" onclick="deleteRecord(this,\'1\',\'__MODULE__\');">删除</button></li>',
						),
				),
		),
		"MisAutoAux"=>array(
				'ADD'=>array(
						'insert' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								)
						),
				),
				'EDIT'=>array(
						'update' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								),
						),
						'delete' => array(
								'name'=>'删除',
								'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_red" onclick="deleteRecord(this,\'1\',\'__MODULE__\');">删除</button></li>',
						),
				),
		),
		"MisAutoMrt"=>array(
				'ADD'=>array(
						'insert' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								)
						),
				),
				'EDIT'=>array(
						'update' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								),
						),
						'delete' => array(
								'name'=>'删除',
								'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_red" onclick="deleteRecord(this,\'1\',\'__MODULE__\');">删除</button></li>',
						),
				),
		),
		"MisAutoTyl"=>array(
				'ADD'=>array(
						'insert' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								)
						),
				),
				'EDIT'=>array(
						'update' => array(
								'more' => 1,
								'list' => array(
										0 => array(
												'name'=>'提交',
												'html'=>'<li><button type="submit" class="tml_formBar_btn tml_formBar_btn_green">提交</button></li>',
										),
								),
						),
						'delete' => array(
								'name'=>'删除',
								'html'=>'<li><button type="button" class="tml_formBar_btn tml_formBar_btn_red" onclick="deleteRecord(this,\'1\',\'__MODULE__\');">删除</button></li>',
						),
				),
		),
);
?>