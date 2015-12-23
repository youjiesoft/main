<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'extendurl' => '"modelname/".$_REQUEST["modelname"]',
		'permisname' => 'typeinfo_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" rel="__MODULE__add" target="dialog"   width="700" height="500" mask="true"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'typeinfo_edit',
		'extendurl' => '"modelname/".$_REQUEST["modelname"]',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}/#extendurl#" rel="__MODULE__edit" target="dialog" mask="true" width="700" height="500"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	//删除
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'typeinfo_delete',
		'extendurl' => '"modelname/".$_REQUEST["modelname"]',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/#extendurl#/rel/ToolBarConfigBox" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	//按钮在页面显示
	'js-button' => array(
			'ifcheck' => '1',
			'extendurl' => '"modelname/".$_REQUEST["modelname"]',
			'permisname' => 'typeinfo_add',
			'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/buttonToPage/#extendurl#" rel="__MODULE__buttonToPage" target="dialog"   width="700" height="500" mask="true"><span><span class="icon icon-plus icon_lrp"></span>按钮页面显示</span></a>',
			'shows' => '1',
			'sortnum' => '4',
	),
);