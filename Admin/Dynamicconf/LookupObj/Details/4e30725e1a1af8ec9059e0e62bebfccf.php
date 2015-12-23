<?php 
return array(
	'title' => '最新内切表带回',
	'fields' => 'orderno,zhiyouguanshen,action',
	'fields_china' => array(
	),
	'checkforfields' => '&#39;orderno&#39;=>&#39;编号&#39;,&#39;zhiyouguanshen&#39;=>&#39;只有关神&#39;,&#39;action&#39;=>&#39;操作&#39;,&#39;id&#39;=>&#39;编号&#39;',
	'fieldcom' => NULL,
	'listshowfields' => NULL,
	'listshowfields_china' => array(
	),
	'funccheck' => NULL,
	'funccheck_china' => array(
	),
	'funcinfo' => false,
	'url' => 'lookupGeneral',
	'mode' => 'MisAutoTba',
	'checkformodel' => 'MisAutoTba',
	'filed' => 'orderno',
	'filed1' => NULL,
	'val' => 'orderno',
	'showrules' => '',
	'rulesinfo' => '',
	'rules' => '',
	'condition' => '',
	'status' => '1',
	'level' => '15',
	'proid' => '520',
	'fieldback' => '6976,6977,6978,6979',
	'dt' => array(
		'mis_auto_oalgz_sub_datatable2' => array(
			'title' => '内嵌数据表格_2',
			'datename' => 'mis_auto_oalgz_sub_datatable2',
			'list' => array(
				'jin' => array(
					'title' => '金',
					'category' => 'select',
					'length' => '30',
				),
				'yu' => array(
					'title' => '玉',
					'category' => 'date',
					'length' => '11',
				),
				'man' => array(
					'title' => '满',
					'category' => 'lookup',
					'length' => '30',
				),
				'tang' => array(
					'title' => '堂',
					'category' => 'text',
					'length' => '30',
				),
			),
		),
	),
);

?>