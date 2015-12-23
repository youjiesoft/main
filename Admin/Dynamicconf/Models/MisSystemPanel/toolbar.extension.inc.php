<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'extendurl' => '"modeid/".$_REQUEST["group_id"]',
		'permisname' => 'missystempanel_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" target="dialog"  title="新增" rel="__MODULE__add" mask="true" width="700" height="500"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//修改
	'js-edit' => array(
			'ifcheck' => '1',
			'permisname' => 'missystempanel_edit',
			'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" target="dialog"  title="修改面板" rel="__MODULE__edit" mask="true" width="700" height="500"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
			'shows' => '1',
			'sortnum' => '1',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'permisname' => 'missystempanel_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/rel/missystempanellookupindex" rel="id" postType="string" class="delete" target="selectedTodo" title="你确定要删除吗？" warn="请选择一条组申请"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
		'sortnum' => '7',
	),
);