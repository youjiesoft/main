<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'misoameetingmanage_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="navTab"  title="会议发布_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'rules' => '#stepType#!=1',
		'permisname' => 'misoameetingmanage_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="navTab" warn="请选择节点!" title="会议发布_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-view' => array(
		'ifcheck' => '0',
		'permisname' => 'misoameetingmanage_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab" title="会议发布_查看" rel="__MODULE__view"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-icon' => array(
			'ifcheck' => '1',
			'rules' => '#stepType#==1',
			'permisname' => 'misoameetingmanage_add',
			'html' => '<a class="add js-icon tml-btn tml_look_btn tml_mp" href="__APP__/MisOaMeetingSummary/add/meetid/{sid_node}" rel="MisOaMeetingSummaryadd" target="navTab"  title="会议纪要_新增"><span><span class="icon icon-pencil icon_lrp"></span>会议纪要</span></a>',
			'shows' => '1',
			'sortnum' => '5',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'rules' => '#stepType#!=1',
		'permisname' => 'misoameetingmanage_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '7',
	),
		
		
);

