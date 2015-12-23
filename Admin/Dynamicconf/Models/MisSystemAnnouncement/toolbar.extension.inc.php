<?php 
return array(
	//新增
	'js-add' => array(
		'ifcheck' => '1',
		//'extendurl' => '"type/".$_REQUEST["type"]',
		'permisname' => 'missystemannouncement_add',
		'html' => '<a class="js-add add tml-btn tml_look_btn tml_mp" href="__URL__/add" target="navTab" title="系统公告_新增" rel="__MODULE__add"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	),
	//edit按钮
	'js-edit' => array(
		'ifcheck' => '1',
		'rules' => '#commit#!=1&&#commit#!=2',
		'permisname' => 'missystemannouncement_edit',
		'html' => '<a class="js-edit edit tml-btn tml_look_btn tml_mp" href="__URL__/edit/id/{sid_node}" target="navTab" title="系统公告_修改" rel="__MODULE__edit"><span><span class="icon icon-edit icon_lrp"></span>修改</span></a>',
		'shows' => '1',
		'sortnum' => '3',
	),
	'js-view' => array(
		'ifcheck' => '0',
		'permisname' => 'missystemannouncement_view',
		'html' => '<a class="js-view icon tml-btn tml_look_btn tml_mp" href="__URL__/view/id/{sid_node}" target="navTab" title="系统公告_查看" rel="__MODULE__view"><span><span class="icon icon-eye-open icon_lrp"></span>查看</span></a>',
		'shows' => '1',
		'sortnum' => '4',
	),
	//单据删除
	'js-delete' => array(
		'ifcheck' => '1',
		'rules' => '#createid#==$_SESSION[C("USER_AUTH_KEY")]||$_SESSION[C("USER_AUTH_KEY")]==1',
		'permisname' => 'missystemannouncement_delete',
		'html' => '<a class="js-delete delete tml-btn tml_look_btn tml_mp" href="__URL__/delete/id/{sid_node}/rel/missystemannouncementbox" target="ajaxTodo" title="你确定要删除吗？" warn="请选择一条组记录"><span><span class="icon icon-trash icon_lrp"></span>删除</span></a>',
		'shows' => '1',
		'sortnum' => '2',
	),
	//取消置顶
	'js-dd' => array(
		'ifcheck' => '1',
		'rules' => '(#createid#==$_SESSION[C("USER_AUTH_KEY")]||$_SESSION[C("USER_AUTH_KEY")]==1)&&#commit#!=4&&#top#!=2',
		'permisname' => 'missystemannouncement_edit',
		'html' => '<a class="js-dd edit tml-btn tml_look_btn tml_mp" href="__URL__/totop/id/{sid_node}/top/2/rel/missystemannouncementbox" title="确定要取消置顶吗?" target="ajaxTodo"><span><span class="icon icon-minus-sign-alt icon_lrp"></span>取消置顶</span></a>',
		'shows' => '1',
		'sortnum' => '8',
	),
	//立即置顶
	'js-zz' => array(
		'ifcheck' => '1',
		'rules' => '(#createid#==$_SESSION[C("USER_AUTH_KEY")]||$_SESSION[C("USER_AUTH_KEY")]==1)&&#commit#!=2&&#top#==2',
		'permisname' => 'missystemannouncement_edit',
		'html' => '<a class="js-zz edit tml-btn tml_look_btn tml_mp" href="__URL__/totop/id/{sid_node}/top/1/rel/missystemannouncementbox" title="确定要置顶吗? 默认置顶3天" target="ajaxTodo"><span><span class="icon icon-level-up icon_lrp"></span>立即置顶</span></a>',
		'shows' => '1',
		'sortnum' => '9',
	),
	//单据撤回
	'js-iconBack' => array(
		'ifcheck' => '1',
		'rules' => '(#createid#==$_SESSION[C("USER_AUTH_KEY")]||$_SESSION[C("USER_AUTH_KEY")]==1)&&#commit#!=5',
		'permisname' => 'missystemannouncement_add',
		'html' => '<a class="js-iconBack tbundo tml-btn tml_look_btn tml_mp" href="__URL__/announcementGetBack/id/{sid_node}/commit/5/rel/missystemannouncementbox" warn="请选择节点" target="ajaxTodo" title="您确定要撤回该条公告吗?"><span><span class="icon icon icon-external-link icon_lrp"></span>撤回</span></a>',
		'shows' => '1',
		'sortnum' => '10',
	),
);