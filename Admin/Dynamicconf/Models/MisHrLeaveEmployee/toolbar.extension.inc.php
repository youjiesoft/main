<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrleaveemployee_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="dialog" mask="true" width="700" height="480"  title="人事离职_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//代办离职
	'js-addleave' => array(
			'ifcheck' => '1',
			'permisname' => 'mishrleaveemployee_addleave',
			'html' => '<a class="add js-addleave tml-btn tml_look_btn tml_mp" href="__URL__/addleave" rel="__MODULE__add" target="dialog" mask="true" width="700" height="480"  title="代办离职_新增"><span><span class="icon icon-hand-right icon_lrp"></span>代办离职</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'rules' => '#auditState#==0||#auditState#==-1',
		'permisname' => 'mishrleaveemployee_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="dialog" mask="true" width="700" height="480" title="人事离职_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrleaveemployee_edit',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/auditView/id/{sid_node}/" rel="__MODULE__view" target="dialog" mask="true" width="700" height="480" title="人事离职_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '5',
	),
	//单据撤回
	'js-iconBack' => array(
		'ifcheck' => '1',
		'rules' => '#auditState#==1',
		'permisname' => 'mishrleaveemployee_add',
		'html' => '<a class="js-iconBack tbundo tml-btn tml_look_btn tml_mp" href="__URL__/lookupGetBackprocess/id/{sid_node}/navTabId/__MODULE__" warn="请选择节点" target="ajaxTodo" title="您确定要撤回单据吗?"><span><span class="icon icon-external-link icon_lrp"></span>单据撤回</span></a>',
		'shows' => '1',
		'sortnum' => '6',
	),
	//删除
	'js-delete' => array(
			'ifcheck' => '1',
			'rules' => '#auditState#==0||#auditState#==-1',
			'permisname' => 'miscomputerfixlog_delete',
			'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),
);