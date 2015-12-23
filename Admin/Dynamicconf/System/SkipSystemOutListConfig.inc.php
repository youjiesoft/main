<?php
/* 本配置文件用于设置:从系统内部面板跳转到外部html 页面 . */
$SkipSystemOutListConfig = array(
		'0'=>array(
				'titile'=>"问题列表",//要显示的名称
				'url' =>"/MisSystemNoticeMethod/experquestionlist",//要跳转的url:__APP__/  之后的部分
				'target'=>'_blank',
				'model'=>'MisExpertquestionList'
				),
	);	
return $SkipSystemOutListConfig;