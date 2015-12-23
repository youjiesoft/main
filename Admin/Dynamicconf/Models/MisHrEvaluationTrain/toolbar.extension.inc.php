<?php 
return array(
		//新增按钮
		'js-add' => array(
				'ifcheck' => '1',
				'extendurl' => '"classsource/".$_REQUEST["classsource"]',
				'permisname' => 'mishrevaluationtrain_add',
				'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" rel="__MODULE__add" target="navTab"    title="新增培训课程"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
				'shows' => '1',
				'sortnum' => '1',
		),
		//edit按钮
		'js-edit' => array(
			'ifcheck' => '1',
			'permisname' => 'mishrevaluationtrain_edit',
			'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid}" rel="__MODULE__edit" target="navTab"  warn="请选择节点" title="编辑培训课程"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
			'shows' => '1',
			'sortnum' => '2',
		),
		//设置人员
		'js-accredit' => array(
				'ifcheck' => '1',
				'rules' => '#status#==1',
				'permisname' => 'mishrevaluationtrain_accredit',
				'html' => '<a class="edit js-accredit tml-btn tml_look_btn tml_mp" href="__URL__/accredit/id/{sid}" rel="__MODULE__accredit" mask="true" title="添加培训人员" warn="请选择课程" target="dialog" height="465" width="755"><span><span class="icon icon-undo icon_lrp"></span>设置人员</span></a>',
				'shows' => '1',
				'sortnum' => '3',
		),
		//删除按钮
		'js-del' => array(
				'ifcheck' => '1',
				'permisname' => 'mishrevaluationtrain_delete',
				'html' => '<a class="delete  js-del tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择课程"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
				'shows' => '1',
				'sortnum' => '4',
		),
);