<?php 
return array(
	//办理入职按钮
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrpersonnelleave_add',
		'html' => '<a class="tbadduser js-add tml-btn tml_look_btn tml_mp" href="__URL__/add" rel="__MODULE__add" target="navTab"  title="办理离职"><span><span class="icon icon-hand-right icon_lrp"></span>办理离职</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'rules' => "#ptmptid#==0",
		'permisname' => 'mishrpersonnelleave_edit',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/editgeneral/rel/mishrleaveemployee/id/{sid_node}" rel="__MODULE__edit" target="navTab" warn="请选择节点" title="办理离职_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	'js-view' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrpersonnelleave_edit',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}/" rel="__MODULE__view" target="dialog" mask="true" width="792" height="480" title="人事离职_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '5',
	),
);