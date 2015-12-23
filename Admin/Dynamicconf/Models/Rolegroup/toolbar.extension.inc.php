<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'rolegroup_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__APP__/Rolegroup/add" height="450" width="720" mask="true" target="dialog" rel="__MODULE__add"  title="权限组_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'rolegroup_edit',
		'rules' => '#operateid#==0',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" height="450" width="720"  href="__APP__/Rolegroup/edit/id/{sid_node}" mask="true" rel="__MODULE__edit" target="dialog" title="权限组_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'rolegroup_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" height="450" width="720" mask="true" href="__APP__/Rolegroup/view/id/{sid_node}" target="dialog"  title="权限组_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'rolegroup_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__APP__/Rolegroup/delete/id/{sid_node}" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
);

?>