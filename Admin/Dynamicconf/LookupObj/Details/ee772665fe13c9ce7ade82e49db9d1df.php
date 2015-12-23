<?php 
return array(
	'title' => '测试转换函数5',
	'fields' => 'id,orderno,deptid,dutylevelid,sumpeople,deptpeople,formationpeople,applicationdate',
	'fields_china' => array(
		'id' => 'ID',
		'orderno' => '申请编号',
		'deptid' => '申请部门',
		'dutylevelid' => '申请职级',
		'sumpeople' => '共增补人数',
		'deptpeople' => '部门现有人数',
		'formationpeople' => '部门编制人数',
		'applicationdate' => '申请日期',
	),
	'checkforfields' => '&#39;id&#39;=>&#39;ID&#39;,&#39;orderno&#39;=>&#39;申请编号&#39;,&#39;deptid&#39;=>&#39;申请部门&#39;,&#39;dutylevelid&#39;=>&#39;申请职级&#39;,&#39;sumpeople&#39;=>&#39;共增补人数&#39;,&#39;deptpeople&#39;=>&#39;部门现有人数&#39;,&#39;formationpeople&#39;=>&#39;部门编制人数&#39;,&#39;applicationdate&#39;=>&#39;申请日期&#39;',
	'fieldcom' => NULL,
	'listshowfields' => 'deptid,deptpeople,formationpeople,applicationdate',
	'listshowfields_china' => array(
		'deptid' => '申请部门',
		'deptpeople' => '部门现有人数',
		'formationpeople' => '部门编制人数',
		'applicationdate' => '申请日期',
	),
	'funccheck' => NULL,
	'funccheck_china' => array(
	),
	'funcinfo' => array(
		'deptid' => array(
			'field' => 'deptid',
			'funcname' => 'getFieldBy',
			'funcdata' => array(
				'0' => '###',
				'1' => 'id',
				'2' => 'name',
				'3' => 'mis_system_department',
			),
		),
		'applicationdate' => array(
			'field' => 'applicationdate',
			'funcname' => 'transTime',
			'funcdata' => array(
				'0' => '',
			),
		),
	),
	'url' => 'lookupGeneral',
	'mode' => 'MisHrPersonnelApplicationInfo',
	'checkformodel' => 'MisHrPersonnelApplicationInfo',
	'filed' => 'deptid',
	'filed1' => NULL,
	'val' => 'id',
	'showrules' => '(申请编号 不等于 &#39;1&#39;)',
	'rulesinfo' => 'YToyOntzOjc6Im9yZGVybm8iO2E6MTp7aTowO2E6MTA6e3M6NDoibmFtZSI7czo3OiJvcmRlcm5vIjtzOjU6InRpdGxlIjtzOjEyOiLnlLPor7fnvJblj7ciO3M6Njoic3ltYm9sIjtzOjE6IjIiO3M6MzoidmFsIjtzOjE6IjEiO3M6NzoiY29udHJvbCI7czo0OiJ0ZXh0IjtzOjY6IndpZGdldCI7czoxMzoicm9sZXRleHRpbnNldCI7czo3OiJsZWZ0aXB0IjtzOjA6IiI7czo4OiJyaWdodGlwdCI7czowOiIiO3M6NDoic29ydCI7aTowO3M6OToiY2VudGVydGlwIjtzOjA6IiI7fX1zOjY6Im1hcHNxbCI7YToxOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6Mzoic3FsIjtzOjM6InNxbCI7czowOiIiO319fQ==',
	'rules' => 'orderno!=(&#39;1&#39;)',
	'condition' => 'orderno!=(&#39;1&#39;)',
	'status' => '1',
	'level' => '15',
);

?>