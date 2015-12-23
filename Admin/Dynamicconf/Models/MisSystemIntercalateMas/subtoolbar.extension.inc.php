<?php 
return array(	
		//新增
		'js-add' => array(
				'ifcheck' => '1',
				'permisname' => 'node_add',
				'extendurl' => '"masid/".$_REQUEST[masid]',
				'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="dialog" mask="true" width="700" height="450" title="新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
				'shows' => '1',
				'sortnum' => '1',
		),
		//edit按钮
		'js-edit' => array(
				'ifcheck' => '1',
				'permisname' => 'node_edit',
				'extendurl' => '"masid/".$_REQUEST[masid]',
				'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}/#extendurl#"  target="dialog" mask="true" width="700" height="450" title="修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
				'shows' => '1',
				'sortnum' => '2',
		),		
// 		'js-view' => array(
// 				'ifcheck' => '0',
// 				'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}/" rel="__MODULE__view" target="dialog" mask="true" width="700" height="450" title="节点_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
// 				'shows' => '1',
// 				'sortnum' => '3',
// 		),			
		//删除
		'js-delete' => array(
				'ifcheck' => '1',
				'permisname' => 'node_delete',
				'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/type/1/id/{sid_node}/rel/MisSystemIntercalateMasBox" title="确实要删除这些记录吗?" target="ajaxTodo" rel="id" postType="string"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
				'shows' => '1',
				'sortnum' => '5',
		),
);

