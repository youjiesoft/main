<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'misdynamicformmanage_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="navTab"  rel="__MODULE__add" title="动态表管理_新增" ><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//删除
	/*'js-delete' => array(
			'ifcheck' => '1',
			'permisname' => 'misdynamicformmanage_delete',
			'extendurl' => '"id/".$_REQUEST["id"]',
			'html' => '<a class="delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/#extendurl#/navTabId/__MODULE__" target="ajaxTodo" callback="navTabAjaxDone" title="你确定要删除吗？" warn="请选择用户"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),*/
	'js-edit' => array(
			'ifcheck' => '1',
			'permisname' => 'misdynamicformmanage_edit',
			'extendurl' => '"id/".$_REQUEST["id"]',
			'html' => '<a class="edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/#extendurl#" target="navTab" rel="__MODULE__add"  title="动态表管理_编辑"><span><span class="icon icon-edit icon_lrp"></span>编辑</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),
);