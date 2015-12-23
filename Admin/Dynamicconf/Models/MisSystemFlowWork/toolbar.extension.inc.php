<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemflowwork_add',
		'extendurl' => '"supcategory/".$_REQUEST["supcategory"]."/category/".$_REQUEST["category"]."/pid/".$_REQUEST["pid"]',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="navTab" rel="__MODULE__add"  title="节点任务_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemflowwork_edit',
		'rules' => '#operateid#==0',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}" target="navTab" title="节点任务_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemflowwork_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/rel/MisSystemFlowWork_left" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-abc'=>array(
		'ifcheck'=>0,
		'permisname' => 'missystemflowwork_abc',
		'html'=>'<li><a class="tml_look_btn  tml_mp js-abc" href="__URL__/lookupCopy/id/{sid_node}" mask="true" rel="__MODULE__addremind" target="dialog" width="640" height="370"><span class="icon-bell icon_lrp"></span><span>任务复制</span></a></li>',
		'shows' => '1',
		'sortnum' => '5',
	),	
);

?>