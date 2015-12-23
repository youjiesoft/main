<?php 
return array(		
		'js-view' => array(
				'ifcheck' => '0',
				'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}/" rel="__MODULE__view" target="dialog" mask="true" width="700" height="450" title="节点_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
				'shows' => '1',
				'sortnum' => '3',
		),		
		//删除
		'js-delete' => array(
				'ifcheck' => '1',
				'permisname' => 'node_delete',
				'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/rel/jbsxNodeBox" title="确实要删除这些记录吗?" target="AjaxTodo" rel="id" postType="string"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
				'shows' => '1',
				'sortnum' => '5',
		),
);

