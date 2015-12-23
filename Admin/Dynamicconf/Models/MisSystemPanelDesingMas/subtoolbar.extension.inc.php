<?php 
return array(	
		//新增
		'js-add' => array(
				'ifcheck' => '1',
				'permisname' => 'desingmas_add',
				'extendurl' => '"id/".$_REQUEST[id]',
				'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="navTab" mask="true"  title="新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
				'shows' => '1',
				'sortnum' => '1',
		),
		//edit按钮
		'js-edit' => array(
				'ifcheck' => '1',
				'permisname' => 'desingmas_edit',
				'extendurl' => '"pid/".$_REQUEST[id]',
				'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}/#extendurl#"  target="navTab" mask="true"   title="修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
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
				'permisname' => 'desingmas_delete',
				'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/deletesub/id/{sid_node}/rel/MisSystemPanelDesingMasBox" target="ajaxTodo" title="确实要删除这些记录吗?" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
				'shows' => '1',
				'sortnum' => '5',
		),
);

