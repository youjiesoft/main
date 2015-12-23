<?php 
return array(
		//转授权
		'js-add' => array(
			'ifcheck' => '1',
			'permisname' => 'missystemclientchangerole_add',
			'extendurl' => '"userid/".$_REQUEST["id"]',
			'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#/id/"  rel="__MODULE__add" title="转授权编辑"  target="navTab"><span><span class="icon icon-plus icon_lrp"></span>转授权编辑</span></a>',
			'shows' => '1',
			'sortnum' => '1',
		),	
		//删除
		'js-delete' => array(
				'ifcheck' => '1',
				'permisname' => 'missystemclientchangerole_delete',
				'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/rel/MisSystemClientChangeRoleBox" target="ajaxTodo" title="你确定要删除吗？" warn="删除此数据可能会引起数据不完整,请谨慎操作!"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
				'shows' => '1',
				'sortnum' => '4',
		),
);

