<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'misorganizationalset_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__APP__/MisOrganizationalSet/add" rel="__MODULE__add" target="dialog" mask="true" width="640" height="400"  title="部门角色_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'misorganizationalset_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__APP__/MisOrganizationalSet/edit/id/{sid_node}" rel="__MODULE__edit" target="dialog" mask="true" width="640" height="400" title="部门角色_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'misorganizationalset_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__APP__/MisOrganizationalSet/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组申请"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
);