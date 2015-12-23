<?php 
return array(	
		//新增
		'js-add' => array(
				'ifcheck' => '1',
				'permisname' => 'desingmas_add',
				'extendurl' => '"group_id/".$_REQUEST["group_id"]."/pid/".$_REQUEST[pid]',
				'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="dialog" mask="true" width="700" height="450" title="新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
				'shows' => '1',
				'sortnum' => '1',
		),
		//edit按钮
		'js-edit' => array(
				'ifcheck' => '1',
				'permisname' => 'desingmas_edit',
				'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}"  target="dialog" mask="true" width="700" height="450" title="修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
				'shows' => '1',
				'sortnum' => '2',
		),		
// 		'js-view' => array(
// 				'ifcheck' => '0',
// 				'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}/" rel="__MODULE__view" target="navtab" mask="true" width="700" height="450" title="节点_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
// 				'shows' => '1',
// 				'sortnum' => '3',
// 		),			
		//删除
		'js-delete' => array(
				'ifcheck' => '1',
				'permisname' => 'desingmas_delete',
				'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="警告！删除此记录将同时删除生成的Action文件！" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
				'shows' => '1',
				'sortnum' => '5',
		),
		//edit按钮
		'js-role' => array(
				'ifcheck' => '1',
				'permisname' => 'desingmas_role',
				'html' => '<a class="role js-role tml-btn tml_look_btn tml_mp" href="__URL__/role"  target="navTab" rel="MisSystemPanelDesingMasRole" mask="true"  title="面板禁权"><span><span class="icon icon-edit icon_lrp"></span>面板禁权</span></a>',
				'shows' => '1',
				'sortnum' => '6',
		),
		'js-initp' => array(
				'ifcheck' => '1',
				'permisname' => 'desingmas_initp',
				'html' => '<a class="initp js-initp tml-btn tml_look_btn tml_mp" href="__URL__/initPanleActionFile"  target="navTab" rel="MisSystemPanelDesingMasRole" mask="true"  title="初始化面板Action文件"><span><span class="icon icon-edit icon_lrp"></span>初始化面板Action文件</span></a>',
				'shows' => '1',
				'sortnum' => '7',
		),
);

