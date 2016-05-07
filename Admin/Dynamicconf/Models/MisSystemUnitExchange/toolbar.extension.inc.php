<?php 
/**
 * @Title: Config
 * @Package package_name
 * @Description: todo(动态表单_配置文件-操作权限配置文件)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-01-08 20:12:56
 * @version V1.0
*/

$original = array(
		'js-add' => array(
				'ifcheck' => '1',
				'permisname' => 'missystemunitexchange_add',
				'html' => '<a class="js-add add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="dialog" width="720" height="500"  mask="true"  rel="__MODULE__add"  title="单位设置_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
				'shows' => '1',
				'sortnum' => '1',
		),
		'js-edit' => array(
				'ifcheck' => '1',
				'permisname' => 'missystemunitexchange_edit',
				'html' => '<a class="js-edit edit tml-btn tml_look_btn tml_mp"  href="__URL__/edit/id/{sid_node}" target="dialog" width="720" height="500"  mask="true"  title="单位设置_修改"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
				'shows' => '1',
				'sortnum' => '3',
		),
		'js-view' => array(
				'ifcheck' => '0',
				'permisname' => 'missystemunitexchange_view',
				'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}/eid/{sid_node}" target="dialog" width="720" height="500"  mask="true"   title="单位设置_查看"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
				'shows' => '1',
				'sortnum' => '4',
		),
		'js-delete' => array(
				'ifcheck' => '1',
				'permisname' => 'missystemunitexchange_delete',
				'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/navTabId/__MODULE__" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
				'shows' => '1',
				'sortnum' => '2',
		),

);
$extedsTool = require 'toolbar.extensionExtend.inc.php';
return array_merge($original , $extedsTool);
?>