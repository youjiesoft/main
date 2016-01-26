<?php 
return array(
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrpersonneladdressbook_edit',
		'html' => '<a class="js-edit edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid}" target="dialog" width="640" height="480" title="员工通讯录_修改" mask="true" rel="__MODULE__edit"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrpersonneladdressbook_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid}" target="dialog" width="640" height="480" title="员工通讯录_查看" mask="true" rel="__MODULE__view"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
);