<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'extendurl' => '"com/".$_REQUEST["com"]',
		'permisname' => 'duty_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" rel="__MODULE__add" target="dialog"  height="350" width="700"  mask="true" ><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'extendurl' => '"com/".$_REQUEST["com"]',
		'permisname' => 'duty_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}/#extendurl#" rel="__MODULE__edit" target="dialog"  height="350" width="700"  mask="true"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	//删除
	'js-delete' => array(
			'ifcheck' => '1',
			'permisname' => 'duty_delete',
			'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__/rel/MisSystemCompanyB" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),		
	//导入按钮
	'js-import' => array(
			'ifcheck' => '1',
			'extendurl' => '"com/".$_REQUEST["com"]',
			'permisname' => 'duty_import',
			'html' => '<a class="add js-import tml-btn tml_look_btn tml_mp" href="__URL__/import/#extendurl#" rel="__MODULE__import" target="navTab" tabid="__MODULE__import" title="导入"><span><span class="icon icon-circle-arrow-down icon_lrp"></span>导入</span></a>',
			'shows' => '1',
			'sortnum' => '4',
	),
);