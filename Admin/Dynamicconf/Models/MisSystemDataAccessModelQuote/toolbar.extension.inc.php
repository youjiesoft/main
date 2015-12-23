<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemdataaccess_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/aname/"  target="dialog" width="650" height="700" rel="__MODULE__add"  mask="true" title="添加" rel="__MODULE__add"><span><span class="icon icon-plus icon_lrp"></span>添加</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//删除
	'js-delete' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemdataaccess_delete',
			'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/navTabId/__MODULE__/id/" target="ajaxTodo" rel="MisSystemDataAccessModelQuoteBOX" title="你确定要删除吗？" warn="删除此数据可能会引起数据不完整,请谨慎操作!"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),
	
);