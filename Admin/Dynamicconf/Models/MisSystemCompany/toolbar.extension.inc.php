<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemcompany_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="dialog" width="580" height="500"  mask="true" title="新增公司信息" rel="__MODULE__add"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemcompany_edit',
			'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" width="580" height="500" rel="__MODULE__edit" target="dialog"  mask="true" title="编辑公司信息"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),
	//删除
	'js-delete' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemcompany_delete',
			'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/MisSystemCompany" target="ajaxTodo" title="你确定要删除吗？" warn="删除此数据可能会引起数据不完整,请谨慎操作!"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
			'sortnum' => '4',
	),
	//公司登陆LOGO
	'js-updateLogoPic' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemcompany_updateLogoPic',
			'html' => '<a class="edit js-updateLogoPic tml-btn tml_look_btn tml_mp" href="__URL__/updateLogoPic" width="584" height="283" rel="__MODULE__updateLogoPic" target="dialog"  mask="true" title="修改公司登陆LOGO"><span><span class="icon icon-edit icon_lrp"></span>修改公司登陆LOGO</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),
	//view按钮
	'js-adddept' => array(
			'ifcheck' => '1',
			'html' => '<a class="js-adddept icon tml-btn tml_look_btn tml_mp" href="__APP__/MisSystemDepartment/index" rel="MisSystemDepartment" target="navTab"  title="部门管理"><span><span class="icon icon-eye-open icon_lrp"></span>创建部门</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),
	//view按钮
	'js-view' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemcompany_view',
			'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" width="580" height="500" rel="__MODULE__view" target="dialog" mask="true" title="查看公司信息"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),	
);