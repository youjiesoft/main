<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'extendurl' => '"deptid/".$_REQUEST["deptid"]."/com/".$_REQUEST["com"]',
		'permisname' => 'duty_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" mask="true" title="岗位_新增" width="700" height="400" href="__APP__/MisRoleGroup/add" target="dialog"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'extendurl' => '"com/".$_REQUEST["com"]',
		'permisname' => 'mishrjobinfo_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__APP__/MisRoleGroup/edit/id/{sid_node}/#extendurl" rel="__MODULE__edit" target="dialog" mask="true" width="700" height="400" title="岗位_编辑"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	//view按钮
	'js-adddept' => array(
			'ifcheck' => '1',
			'html' => '<a class="js-adddept icon tml-btn tml_look_btn tml_mp" href="__APP__/MisHrJobInfo/MisSystemDepartment/index" rel="MisSystemDepartment" target="navTab"  title="部门管理"><span><span class="icon icon-eye-open icon_lrp"></span>返回部门</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),
	//删除
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrjobinfo_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__APP__/MisRoleGroup/delete/id/{sid_node}/rel/MisHrJobInfoBox" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
);