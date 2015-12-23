<?php 
return array(
	//办理入职按钮
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrpersonnelmanagement_add',
		'extendurl' => '"companyid/".$_REQUEST["companyid"]."/deptid/".$_REQUEST["deptid"]."/ptId/".$_REQUEST["ptId"]',
		'html' => '<a class="tbadduser js-addhr tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" rel="__MODULE__add" target="navTab"  title="办理入职"><span><span class="icon icon-smile icon_lrp"></span>办理入职</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrpersonnelmanagement_edit',
		'extendurl' => '"companyid/".$_REQUEST["companyid"]',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/editgeneral/rel/mishrpersonnelmanagement/id/{sid_node}/#extendurl#" rel="__MODULE__editgeneral" target="navTab"  warn="请选择节点" title="员工档案"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
// 	//离职按钮
// 	'js-addleave' => array(
// 			'ifcheck' => '1',
// 			'permisname' => 'mishrpersonnelmanagement_addleave',
// 			'html' => '<a class="add tbremoveuser js-addleave tml-btn tml_look_btn tml_mp" href="__URL__/addleave/id/{sid_node}" rel="__MODULE__addleave" mask="true" warn="请选择节点" title="代办离职" onclick="inleave()" target="dialog" height="500" width="660"> <span><span class="icon icon-frown icon_lrp"></span>离职</span></a>',
// 			'shows' => '1',
// 			'sortnum' => '3',
// 	),
// 	//请假按钮
// 	'js-addforleave' => array(
// 			'ifcheck' => '1',
// 			'permisname' => 'mishrpersonnelmanagement_addforleave',
// 			'html' => '<a class="add tbuserexclamation js-addforleave tml-btn tml_look_btn tml_mp"  href="__URL__/addforleave/id/{sid_node}" rel="__MODULE__addforleave" mask="true" warn="请选择节点" title="代办请假" onclick="inleave()" target="dialog" height="650" width="680"> <span><span class="icon icon-play icon_lrp"></span>请假</span></a>',
// 			'shows' => '1',
// 			'sortnum' => '4',
// 	),
// 	//异动按钮
// 	'js-addtransfer' => array(
// 			'ifcheck' => '1',
// 			'permisname' => 'mishrpersonnelmanagement_addtransfer',
// 			'html' => '<a class="add tbuserarrow js-addtransfer tml-btn tml_look_btn tml_mp"   href="__URL__/addtransfer/id/{sid_node}" rel="__MODULE__addtransfer" mask="true" warn="请选择节点" title="代办调职" onclick="inleave()" target="dialog" height="520" width="670"> <span><span class="icon icon-undo icon_lrp"></span>异动</span></a>',
// 			'shows' => '1',
// 			'sortnum' => '5',
// 	),
 
);