<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrbasicemployeecontract_add',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="navTab" mask="true" title="合同管理_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrbasicemployeecontract_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" rel="__MODULE__edit" target="navTab"   title="合同管理_编辑"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	//续签
	'js-addc' => array(
			'ifcheck' => '1',
			'permisname' => 'mishrbasicemployeecontract_add',
			'html' => '<a class="add js-addc tml-btn tml_look_btn tml_mp" href="__URL__/add/id/{sid_node}" rel="__MODULE__add" target="navTab"   title="合同管理_续签"><span><span class="icon icon-link icon_lrp"></span>续签</span></a>',
			'shows' => '1',
			'sortnum' => '3',
	),
	//终止
	'js-stop' => array(
			'ifcheck' => '1',
			'rules' => '#contractstatus#==0',
			'permisname' => 'mishrbasicemployeecontract_stop',
			'html' => '<a class="tbnouse  js-stop tml-btn tml_look_btn tml_mp" href="__URL__/stop/id/{sid_node}" rel="__MODULE__stop" target="dialog"  width="450" height="180"  title="合同管理_终止"><span><span class="icon icon-stop icon_lrp"></span>终止</span></a>',
			'shows' => '1',
			'sortnum' => '4',
	),
	//导入按钮
	'js-import' => array(
			'ifcheck' => '1',
			'permisname' => 'mishrbasicemployeecontract_add',
			'html' => '<a class="add js-import tml-btn tml_look_btn tml_mp" href="__URL__/lookupimport" rel="__MODULE__lookupimport" target="dialog" height="250" width="500" tabid="__MODULE__lookupimport" title="导入"><span><span class="icon icon-plus icon_lrp"></span>导入</span></a>',
			'shows' => '1',
			'sortnum' => '5',
	),
);