<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'emailtmptype_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp"  href="__URL__/add" target="dialog"  rel="__MODULE__add" mask="true" width="700" title="邮件模板分类_新增" ><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//删除
	'js-delete' => array(
			'ifcheck' => '1',
			'permisname' => 'emailtmptype_delete',
			'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_role}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？"  warn="请选择一条记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
			'shows' => '1',
			'sortnum' => '2',
	),
	'js-edit' => array(
			'ifcheck' => '1',
			'permisname' => 'emailtmptype_edit',
			'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_role}"  target="dialog"  width="700" mask="true" warn="请选择一条记录" title="邮件模板分类_编辑"><span><span class="icon icon-edit icon_lrp"></span>编辑</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	), 
);