<?php 
return array(
	'title' => '多表带回测试',
	'fields' => 'xingming,dizhi,gongsi',
	'fields_china' => array(
		'xingming' => '姓名',
		'dizhi' => '地址',
		'gongsi' => '公司',
	),
	'checkforfields' => '&#39;xingming&#39;=>&#39;姓名&#39;,&#39;dizhi&#39;=>&#39;地址&#39;,&#39;gongsi&#39;=>&#39;公司&#39;,&#39;id&#39;=>&#39;编号&#39;',
	'fieldcom' => 'xingming',
	'listshowfields' => NULL,
	'listshowfields_china' => array(
	),
	'funccheck' => NULL,
	'funccheck_china' => array(
	),
	'funcinfo' => false,
	'url' => 'lookupGeneral',
	'mode' => 'MisAutoVtu',
	'checkformodel' => 'MisAutoVtu',
	'filed' => 'xingming',
	'filed1' => NULL,
	'val' => 'id',
	'showrules' => '',
	'rulesinfo' => '',
	'rules' => '',
	'condition' => '',
	'status' => '1',
	'level' => '15',
	'proid' => '377',
	'fieldback' => '6950,6951',
	'dt' => array(
		'mis_auto_ddskf_sub_datatable4' => array(
			'title' => '内嵌数据表格_4',
			'datename' => 'mis_auto_ddskf_sub_datatable4',
			'list' => array(
				'xueli' => array(
					'title' => '学历',
					'category' => 'text',
					'length' => '30',
				),
				'congyejingyan' => array(
					'title' => '从业经验',
					'category' => 'text',
					'length' => '10',
				),
			),
		),
	),
);

?>