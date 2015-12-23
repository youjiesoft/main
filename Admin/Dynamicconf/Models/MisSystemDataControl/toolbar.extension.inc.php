<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemdataroammas_add',
		'extendurl' => '"companyid/".$_REQUEST["parentid"]',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="navTab" width="580" height="500"  mask="true" title="漫游新增" rel="__MODULE__add"><span><span class="icon icon-plus icon_lrp"></span>漫游新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemdataroammas_edit',
			'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" width="580" height="500" rel="__MODULE__edit" target="navTab"  mask="true" title="漫游编辑"><span><span class="icon icon-edit icon_lrp"></span>漫游编辑</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),				
);