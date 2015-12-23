<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrinvitereform_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add"  rel="__MODULE__add" title="招聘信息_新增"  target="dialog" mask="true" width="700" height="500"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrinvitereform_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid}" rel="__MODULE__edit" target="dialog" mask="true" width="700" height="500" title="招聘信息_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	//办理入职按钮
	'js-addhr' => array(
		'ifcheck' => '1',
		'rules' => '#isinjob#==0',
		'permisname' => 'mishrbasicemployee_add',
		'html' => '<a class="add js-addhr tml-btn tml_look_btn tml_mp" href="__APP__/MisHrBasicEmployee/add/id/{sid}" rel="__MODULE__edit" target="navTab" mask="true" width="700" height="500" title="办理入职"><span><span class="icon icon-smile icon_lrp"></span>办理入职</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	//删除
	'js-delete' => array(
		'ifcheck' => '1',
		'rules' => '#auditState#==0',
		'permisname' => 'mishrinvitereform_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
);