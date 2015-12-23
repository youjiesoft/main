<?php 
return array(	
		//新增
		'js-add' => array(
				'ifcheck' => '1',
				'permisname' => 'node_add',
				'extendurl' => '"group_id/".$_REQUEST["group_id"]."/pid/".$_REQUEST[pid]',
				'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="dialog" mask="true" width="700" height="450" title="节点_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
				'shows' => '1',
				'sortnum' => '1',
		),
		//edit按钮
		'js-edit' => array(
				'ifcheck' => '1',
				'permisname' => 'node_edit',
				'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}"  target="dialog" mask="true" width="700" height="450" title="节点_新增"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
				'shows' => '1',
				'sortnum' => '2',
		),		
		'js-view' => array(
				'ifcheck' => '0',
				'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}/" rel="__MODULE__view" target="dialog" mask="true" width="700" height="450" title="节点_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
				'shows' => '1',
				'sortnum' => '3',
		),	
		'js-pack' => array(
				'ifcheck' => '0',
				'html' => '<a class="js-pack add tml-btn tml_look_btn tml_mp" href="__URL__/upgradePackage" target="navTabIds" rel="id" tabid="NodeUpgradePachage" warn="请选择打包节点"><span><span class="icon icon-briefcase icon_lrp"></span>打包</span></a>',
				'shows' => '1',
				'sortnum' => '4',
		),		
// 		//删除
// 		'js-delete' => array(
// 				'ifcheck' => '1',
// 				'permisname' => 'node_delete',
// 				'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/rel/jbsxNodeBox" title="确实要删除这些记录吗?" target="selectedTodo" rel="id" postType="string"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
// 				'shows' => '1',
// 				'sortnum' => '5',
// 		),
		'js-orderno'=>array(
				'ifcheck'=>'0',
				'rules' => '#level#==3',
				'permisname'=>'',
				'html'=>'<li><a class="js-orderno tml-btn tml_look_btn tml_mp" href="__URL__/lookupcoding/id/{sid_node}"  target="dialog" mask="true" width="700" height="450" title="编码方案"><span><span class="icon icon-gbp icon_lrp"></span>编码方案</span></a></li>',
				'shows'=>'1',
				'sortnum'=>'6'
		),
);

