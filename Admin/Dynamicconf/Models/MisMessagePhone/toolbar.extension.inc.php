<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'mismessagephone_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="dialog" mask="true" width="650" height="360" title="发送短信息" rel="__MODULE__add"><span><span class="icon icon-envelope icon_lrp"></span>发送短信息</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'rules' => '#flag#==0',
		'permisname' => 'mismessagephone_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" target="dialog" mask="true" width="650" height="360" title="编辑短信息" rel="__MODULE__edit"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-view' => array(
		'ifcheck' => '0',
		'permisname' => 'mismessagephone_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/showMessage/id/{sid_node}" mask="true" width="650" height="360" rel="__MODULE__view" target="dialog"   title="短信息_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'rules' => '#flag#==0',
		'permisname' => 'mismessagephone_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '7',
	),
);