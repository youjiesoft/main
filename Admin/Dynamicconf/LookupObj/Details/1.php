<?php 
return array(
	'title' => '测试修改',
	'fields' => 'id,orderno,deptid,dutylevelid,sumpeople,deptpeople',
	'fields_china' => array(
		'id' => 'ID',
		'orderno' => '申请编号',
		'deptid' => '申请部门',
		'dutylevelid' => '申请职级',
		'sumpeople' => '共增补人数',
		'deptpeople' => '部门现有人数',
	),
	'checkforfields' => '&#39;id&#39;=>&#39;ID&#39;,&#39;orderno&#39;=>&#39;申请编号&#39;,&#39;deptid&#39;=>&#39;申请部门&#39;,&#39;dutylevelid&#39;=>&#39;申请职级&#39;,&#39;sumpeople&#39;=>&#39;共增补人数&#39;,&#39;deptpeople&#39;=>&#39;部门现有人数&#39;,&#39;id&#39;=>&#39;编号&#39;',
	'fieldcom' => NULL,
	'listshowfields' => NULL,
	'listshowfields_china' => array(
	),
	'funccheck' => NULL,
	'funccheck_china' => array(
	),
	'funcinfo' => false,
	'url' => 'lookupGeneral',
	'mode' => 'MisHrPersonnelApplicationInfo',
	'checkformodel' => 'MisHrPersonnelApplicationInfo',
	'filed' => 'sumpeople',
	'filed1' => 'sumpeople',
	'val' => 'id',
	'showrules' => '(申请部门 包含 &#39;测试,测试2&#39;) 并且 (申请职级 包含 &#39;队长,班长,办事员&#39;)',
	'rulesinfo' => 'YToyOntzOjY6ImRlcHRpZCI7YToxOntpOjA7YTo2OntzOjQ6Im5hbWUiO3M6NjoiZGVwdGlkIjtzOjY6InN5bWJvbCI7czoxOiIzIjtzOjc6InNob3d2YWwiO3M6MTQ6Iua1i+ivlSzmtYvor5UyIjtzOjM6InZhbCI7YToyOntpOjA7czoyOiIyOSI7aToxO3M6MjoiMzAiO31zOjc6ImNvbnRyb2wiO3M6Njoic2VsZWN0IjtzOjY6IndpZGdldCI7czoxMzoicm9sZXRleHRpbnNldCI7fX1zOjExOiJkdXR5bGV2ZWxpZCI7YToxOntpOjA7YTo2OntzOjQ6Im5hbWUiO3M6MTE6ImR1dHlsZXZlbGlkIjtzOjY6InN5bWJvbCI7czoxOiIzIjtzOjc6InNob3d2YWwiO3M6MjM6IumYn+mVvyznj63plb8s5Yqe5LqL5ZGYIjtzOjM6InZhbCI7YTozOntpOjA7czoxOiI4IjtpOjE7czoxOiI5IjtpOjI7czoyOiIxMCI7fXM6NzoiY29udHJvbCI7czo2OiJzZWxlY3QiO3M6Njoid2lkZ2V0IjtzOjEzOiJyb2xldGV4dGluc2V0Ijt9fX0=',
	'rules' => 'deptid in(&#39;29,30&#39;)  and dutylevelid in(&#39;8,9,10&#39;)',
	'condition' => 'deptid in(&#39;29,30&#39;)  and dutylevelid in(&#39;8,9,10&#39;)',
	'status' => '1',
	'level' => '15',
);

?>