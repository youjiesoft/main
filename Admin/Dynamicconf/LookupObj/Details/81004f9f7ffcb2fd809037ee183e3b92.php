<?php 
return array(
	'title' => 'sadg',
	'fields' => 'id,orderno,deptid,dutylevelid,sumpeople,applicationdate',
	'fields_china' => array(
		'id' => 'ID',
		'orderno' => '申请编号',
		'deptid' => '申请部门',
		'dutylevelid' => '申请职级',
		'sumpeople' => '共增补人数',
		'applicationdate' => '申请日期',
	),
	'checkforfields' => '&#39;id&#39;=>&#39;ID&#39;,&#39;orderno&#39;=>&#39;申请编号&#39;,&#39;deptid&#39;=>&#39;申请部门&#39;,&#39;dutylevelid&#39;=>&#39;申请职级&#39;,&#39;sumpeople&#39;=>&#39;共增补人数&#39;,&#39;applicationdate&#39;=>&#39;申请日期&#39;,&#39;id&#39;=>&#39;编号&#39;',
	'fieldcom' => NULL,
	'listshowfields' => 'orderno,deptid,dutylevelid',
	'listshowfields_china' => array(
		'orderno' => '申请编号',
		'deptid' => '申请部门',
		'dutylevelid' => '申请职级',
	),
	'funccheck' => 'deptid,applicationdate',
	'funccheck_china' => array(
		'deptid' => '申请部门',
		'applicationdate' => '申请日期',
	),
	'funcinfo' => array(
		'\'deptid\'' => array(
			'field' => '\'deptid\'',
			'funcname' => 'getFieldBy',
			'funcdata' => array(
				'0' => '###',
				'1' => 'orderno',
				'2' => 'name',
				'3' => 'mis_system_department',
			),
		),
		'\'applicationdate\'' => array(
			'field' => '\'applicationdate\'',
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
	'showrules' => '',
	'rulesinfo' => '',
	'rules' => '',
	'condition' => '',
	'status' => '1',
	'level' => '15',
);

?>