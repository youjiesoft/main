<?php 
return array(
	//办理入职按钮
	'js-add' => array(
			'ifcheck' => '1',
			'permisname' => 'mishrbasicemployee_add',
			'extendurl' => '"companyid/".$_REQUEST["companyid"]."/deptid/".$_REQUEST["deptid"]."/ptId/".$_REQUEST["ptId"]',
			'html' => '<a class="tbadduser js-addhr tml-btn tml_look_btn tml_mp" href="__URL__/add/#extendurl#" rel="__MODULE__add" target="navTab"  title="办理入职"><span><span class="icon icon-smile icon_lrp"></span>办理入职</span></a>',
			'shows' => '1',
			'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'permisname' => 'mishrbasicemployee_edit',
		'extendurl' => '"companyid/".$_REQUEST["companyid"]',
		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp" href="__URL__/editgeneral/rel/mishrprobationemployee/id/{sid_node}/#extendurl#" rel="__MODULE__editgeneral" target="navTab"  warn="请选择节点" title="员工档案"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
// 		//延长试用期按钮
// 		'js-setprobation' => array(
// 				'ifcheck' => '1',
// 				'permisname' => 'mishrbasicemployee_setprobation',
// 				'html' => '<a class="edit tbclock js-setprobation tml-btn tml_look_btn tml_mp" href="__URL__/setprobation/rel/mishrbasicemployee/id/{sid_node}" rel="__MODULE__setprobation" mask="true" warn="请选择节点" title="延长试用期" onclick="basicleave()" target="dialog" height="150" width="500"><span><span class="icon icon-level-up icon_lrp"></span>试用期</span></a>',
// 				'shows' => '1',
// 				'sortnum' => '3',
// 		),
// 		//延长试用期按钮
// 		'js-addbecome' => array(
// 				'ifcheck' => '1',
// 				'permisname' => 'mishrbasicemployee_addbecome',
// 				'html' => '<a class="edit addBecome tbuserarrow js-addbecome tml-btn tml_look_btn tml_mp" href="__URL__/addBecome/id/{sid_node}" onclick="Becomeclick(\'__URL__/addBecome/id/\',\'addBecome\')"  rel="__MODULE__addBecome" mask="true" warn="请选择节点" title="代办转正"    target="dialog"  height="550" width="681"><span><span class="icon icon-thumbs-up icon_lrp"></span>转正</span></a>',
// 				'shows' => '1',
// 				'sortnum' => '4',
// 		),
// 	//离职按钮
// 	'js-addleave' => array(
// 			'ifcheck' => '1',
// 			'permisname' => 'mishrbasicemployee_addleave',
// 			'html' => '<a class="add tbremoveuser js-addleave tml-btn tml_look_btn tml_mp" href="__URL__/addleave/id/{sid_node}" rel="__MODULE__addleave" mask="true" warn="请选择节点" title="代办离职"  onclick="basicleave();Becomeclick(\'__URL__/addleave/id/\',\'tbremoveuser\');"  target="dialog" height="500" width="660"> <span><span class="icon icon-frown icon_lrp"></span>离职</span></a>',
// 			'shows' => '1',
// 			'sortnum' => '5',
// 	),
// 	//请假按钮
// 	'js-addforleave' => array(
// 			'ifcheck' => '1',
// 			'permisname' => 'mishrbasicemployee_addforleave',
// 			'html' => '<a class="add tbuserexclamation js-addforleave tml-btn tml_look_btn tml_mp"  href="__URL__/addforleave/id/{sid_node}" rel="__MODULE__addforleave" mask="true" warn="请选择节点" title="代办请假" onclick="basicleave()" target="dialog" height="650" width="680"> <span><span class="icon icon-play icon_lrp"></span>请假</span></a>',
// 			'shows' => '1',
// 			'sortnum' => '6',
// 	),
// 	//异动按钮
// 	'js-addtransfer' => array(
// 			'ifcheck' => '1',
// 			'permisname' => 'mishrbasicemployee_addtransfer',
// 			'html' => '<a class="add tbuserarrow js-addtransfer tml-btn tml_look_btn tml_mp"   href="__URL__/addtransfer/id/{sid_node}" rel="__MODULE__addtransfer" mask="true" warn="请选择节点" title="代办调职" onclick="basicleave()" target="dialog" height="510" width="670"> <span><span class="icon icon-undo icon_lrp"></span>异动</span></a>',
// 			'shows' => '1',
// 			'sortnum' => '7',
// 	),
 
);