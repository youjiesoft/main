<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'misautogkt_add',
		'html' => '<a class="js-add add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="navTab" rel="__MODULE__add"  title="流程节点管理_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'misautogkt_edit',
		'html' => '<a class="js-edit edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}"  target="navTab" title="流程节点管理_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-view' => array(
		'ifcheck' => '0',
		'permisname' => 'misautogkt_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab"  title="流程节点管理_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'misautogkt_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-addremind' => array(
		'ifcheck' => '0',
		'permisname' => 'misautogkt_remind',
		'html' => '<a class="tml_look_btn  tml_mp js-addremind" mask="true" href="__APP__/MisSystemRemind/lookupaddremind/md/MisAutoGkt" rel="__MODULE__addremind" target="dialog" width="640" height="227"  title="加入提醒"><span class="icon-bell icon_lrp"></span><span>加入提醒</span></a>',
		'shows' => '1',
		'sortnum' => '5',
	),
);

?>