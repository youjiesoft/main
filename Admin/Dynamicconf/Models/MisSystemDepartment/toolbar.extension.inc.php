<?php 
return array(
	'js-add' => array(
		'ifcheck' => '1',
		'permisname' => 'missystemdepartment_add',
		'extendurl' => '"jump/jump/companyid/".$_REQUEST["parentid"]',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/index/add/1/#extendurl#" target="ajax" rel="MisSystemDepartmentBox"  title="部门新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
// 	'js-edit' => array(
// 		'ifcheck' => '1',
// 		'permisname' => 'missystemdepartment_edit',
// 		'html' => '<a class="edit js-edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}"  target="dialog" mask="true" width="580" height="500"   title="部门修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
// 		'shows' => '1',
// 		'sortnum' => '3',
// 	),
// 	'js-view' => array(
// 		'ifcheck' => '1',
// 		'permisname' => 'missystemdepartment_view',
// 		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" mask="true"  target="dialog" mask="true" width="580" height="500"    title="部门查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
// 		'shows' => '1',
// 		'sortnum' => '4',
// 	),
		//view按钮
// 		'js-adddept' => array(
// 				'ifcheck' => '1',
// 				'html' => '<a class="js-adddept icon tml-btn tml_look_btn tml_mp" href="__APP__/MisHrJobInfo/index" rel="MisHrJobInfo" target="navTab"  title="岗位管理"><span><span class="icon icon-eye-open icon_lrp"></span>创建岗位</span></a>',
// 				'shows' => '1',
// 				'sortnum' => '6',
// 		),
// 		//view按钮
// 		'js-resyscom' => array(
// 				'ifcheck' => '1',
// 				'html' => '<a class="js-resyscom icon tml-btn tml_look_btn tml_mp" href="__APP__/MisSystemCompany/index" rel="MisSystemCompany" target="navTab"  title="建立公司"><span><span class="icon icon-eye-open icon_lrp"></span>返回公司</span></a>',
// 				'shows' => '1',
// 				'sortnum' => '5',
// 		),
);

?>