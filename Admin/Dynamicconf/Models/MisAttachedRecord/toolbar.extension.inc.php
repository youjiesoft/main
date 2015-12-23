<?php 
return array(
	 'js-add' => array(
		'ifcheck' => '1',
	 	//'rules' => '#'.$_REQUEST["projectworkid"].'#!=null',
		'permisname' => 'misattachedrecord_add',
	 	'extendurl' => '"projectid/".$_REQUEST["id"]."/projectworkid/".$_REQUEST["projectworkid"]',
		'html' => '<a class="add js-add tml-btn tml_look_btn tml_mp" href="__URL__/lookupfileadd/#extendurl#" target="dialog"  whith="500" height="400"  mask="true"   rel="__MODULE__add"  title="资料_新增"><span><span class="icon icon-plus icon_lrp"></span>新增</span></a>',
		'shows' => '1',
		'sortnum' => '1',
	), 
);

?>