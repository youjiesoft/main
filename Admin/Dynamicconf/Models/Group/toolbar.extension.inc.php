<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'group_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="dialog" width="720" height="300"  mask="true"  title="菜单分组_新增" ><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//删除
	'js-delete' => array(
			'ifcheck' => '1',
			'permisname' => 'group_delete',
			'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" rel="id" postType="string" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="确实要删除这条记录吗?" warn="请选择一条记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),
	'js-edit' => array(
			'ifcheck' => '1',
			'permisname' => 'group_edit',
			'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit"  target="dialog" width="720" height="300"  mask="true"  warn="请选择节点" title="菜单分组_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),
);