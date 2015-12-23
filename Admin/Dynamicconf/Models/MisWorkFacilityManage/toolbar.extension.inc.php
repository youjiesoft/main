<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'misworkfacilitymanage_add',
		'extendurl' => '"equipmenttype/".$_REQUEST["equipmenttype"]',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" rel="__MODULE__add" target="navTab"  title="设备管理_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'misworkfacilitymanage_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="navTab" warn="请选择节点!" title="设备管理_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'misworkfacilitymanage_edit',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab" title="设备管理_查看" rel="__MODULE__view"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-addremind' => array(
			'ifcheck' => '1',
			'permisname' => 'misworkfacilitymanage_remind',
			'extendurl' => '"equipmenttype/".$_REQUEST["equipmenttype"]',
			'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" mask="true" href="__APP__/Public/lookupaddremind/MisWorkFacilityManage" rel="__MODULE__addremind" target="dialog" width="527" height="227"  title="加入提醒"><span><span class="icon icon-time icon_lrp"></span>加入提醒</span></a>',
			'shows' => '1',
			'sortnum' => '1',
	),
);

