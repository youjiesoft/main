<?php 
return array(
	'title' => 'rewg34g',
	'fields' => 'id,orderno,deptid,dutylevelid,sumpeople,deptpeople',
	'fields_china' => array(
		'id' => 'ID',
		'orderno' => '申请编号',
		'deptid' => '申请部门',
		'dutylevelid' => '申请职级',
		'sumpeople' => '共增补人数',
		'deptpeople' => '部门现有人数',
	),
	'checkforfields' => '&#39;id&#39;=>&#39;ID&#39;,&#39;orderno&#39;=>&#39;申请编号&#39;,&#39;deptid&#39;=>&#39;申请部门&#39;,&#39;dutylevelid&#39;=>&#39;申请职级&#39;,&#39;sumpeople&#39;=>&#39;共增补人数&#39;,&#39;deptpeople&#39;=>&#39;部门现有人数&#39;',
	'fieldcom' => NULL,
	'listshowfields' => 'orderno,deptid,dutylevelid,sumpeople,deptpeople',
	'listshowfields_china' => array(
		'orderno' => '申请编号',
		'deptid' => '申请部门',
		'dutylevelid' => '申请职级',
		'sumpeople' => '共增补人数',
		'deptpeople' => '部门现有人数',
	),
	'funccheck' => 'deptid',
	'funccheck_china' => array(
		'deptid' => '申请部门',
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