<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'extendurl' => '"pid/".$_REQUEST["pid"]',
		'permisname' => 'misexperttype_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#"  rel="__MODULE__add" title="专家分类-新增"  target="dialog" mask="true" width="350" height="250"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//删除
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'misexperttype_delete',
		'html' => '<a class="delete js-delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid}/rel/misexperttype" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	//修改
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'misexecutiontype_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid}" rel="__MODULE__edit" target="dialog" mask="true"  width="350" height="250" title="专家分类_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	//查看
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'misexecutiontype_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid}/" rel="__MODULE__view" target="dialog" mask="true" width="350" height="250" title="专家分类_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
);