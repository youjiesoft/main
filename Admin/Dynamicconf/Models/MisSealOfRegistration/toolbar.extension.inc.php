<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'extendurl' => '"typeid/".$_REQUEST["typeid"]',
		'permisname' => 'missealofregistration_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="dialog" mask="true" title="盖章登记类型_新增" rel="__MODULE__add" mask="true" width="700" height="250"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'missealofregistration_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid}/rel/missealofregistration" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组申请"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'missealofregistration_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid}" mask="true" target="dialog" title="盖章登记类型_修改" rel="__MODULE__edit" mask="true" width="700" height="250"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-view' => array(
		'ifcheck' => '0',
		'permisname' => 'missealofregistration_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid}" mask="true" target="dialog" title="盖章登记类型_查看" rel="__MODULE__view" mask="true" width="700" height="250"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
);