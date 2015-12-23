<?php 
return array(
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'misworkreadstatement_edit',
		'rules' => '#iscomment#==0',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" target="navTab" title="工作报告查阅_点评" rel="__MODULE__edit"><span><span class="icon icon-comment icon_lrp"></span>点评</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-view' => array(
		'ifcheck' => '0',
		'permisname' => 'misworkreadstatement_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab" title="工作报告查阅_查看" rel="__MODULE__view"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
);