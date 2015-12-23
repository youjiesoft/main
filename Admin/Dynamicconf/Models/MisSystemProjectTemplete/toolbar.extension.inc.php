<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemprojecttemplete_add',
		'extendurl' => '"pid/".$_REQUEST["pid"]."/tid/".$_REQUEST["tid"]',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="navTab" rel="__MODULE__add"  title="新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemprojecttemplete_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit"  target="navTab" title="修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemprojecttemplete_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab" title="查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemprojecttemplete_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/rel/MisSystemProjectTemplete_left" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-label' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemprojecttemplete_label',
			'html' => '<a class="js-label tml-btn tml_look_btn tml_mp" href="__URL__/label/id/{sid_node}" target="navTab" ><span><span class="icon icon-plus icon_lrp"></span>标签管理</span></a>',
			'shows' => '1',
			'sortnum' => '5',
	),
);

?>