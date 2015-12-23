<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'selectlist_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="dialog" width="700" height="350" title="类型_新增" mask="true" rel="__MODULE__add"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'selectlist_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" target="dialog" width="700" height="350" mask="true" title="类型_修改" rel="__MODULE__edit"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-view' => array(
		'ifcheck' => '0',
		'permisname' => '',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="dialog" width="700" height="350" mask="true" title="类型_查看" rel="__MODULE__view"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	'js-init' => array(
			'ifcheck' => '0',
			'permisname' => '',
			'html' => '<a class="tml-btn tml_look_btn tml_mp" href="__URL__/initSelectList" target="ajax" rel="__MODULE__" title="重置配置文件"><span><span class="icon icon-retweet icon_lrp"></span>配置文件初始化</span></a>',
			'shows' => '1',
			'sortnum' => '5',
	),
);