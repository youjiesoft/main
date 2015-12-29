<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'misoaarchivesmanagemas_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="navTab"  title="档案管理_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'misoaarchivesmanagemas_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="navTab" warn="请选择节点!" title="档案管理_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'misoaarchivesmanagemas_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab" title="档案管理_查看" rel="__MODULE__view"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'misoaarchivesmanagemas_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/misoaarchivesmanagesub" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '7',
	),
	
);

