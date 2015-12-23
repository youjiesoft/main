<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'extendurl' => '"pid/".$_REQUEST["pid"]',
		'permisname' => 'misworkfacilitytype_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/mdtype/MisWorkFacilityType/#extendurl#" target="dialog"  rel="__MODULE__add" width="700" height="300" mask="true" title="类型管理_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'misworkfacilitytype_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}/mdtype//MisWorkFacilityType" target="dialog"  rel="__MODULE__edit" width="700" height="300" mask="true" warn="请选择节点!" title="类型管理_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'extendurl' => '"table/".$_REQUEST["md"]',
		'permisname' => 'misworkfacilitytype_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/rel/misworkfacilitytype/#extendurl#" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '7',
	),
		
		
);

