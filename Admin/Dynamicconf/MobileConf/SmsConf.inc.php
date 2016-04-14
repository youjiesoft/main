<?php 
// 短信接口配置文件
// key为$channel,短信通道类型
// SmsType,class为扩展包,url为外部链接的rust请求
// SmsAccount为Sms账户
// SmsPassword为Sms密码
// SmsPswdModel为Sms密码加密模式
// SmsClass为接口包类名
// SmsPath为包路径
// SmsUrl为请求路径
// SmsParam为请求参数
return array(
	'dhst' => array(
		'SmsType' => 'class',
		'SmsClass' => 'SmsHttp',			
		'SmsAccount' => 'dh56712',		
		'SmsPassword' => 'F2Ha$2qJ',
		'SmsPswdModel'=>'MD5',		
		'SmsPath' => '@.ORG.Sms.SmsHttp',
		'SmsFlowControl' => '120',
		'SmsUrl' => '',
		'SmsParam'=>''
			
	),
	'skyzero' => array(
		'SmsType' => 'class',
		'SmsClass' => 'SmsHttp',
		'SmsAccount' => '',
		'SmsPassword' => '',
		'SmsPswdModel'=>'',
		'SmsPath' => '@.ORG.Sms.SkyZero',
		'SmsFlowControl' => '120',
		'SmsUrl' => 'http://utf8.sms.webchinese.cn/?Uid=zerosky&Key=8e0c3a6817a34384c165',
		'SmsParam' => array(
				'smsMob' => '',//电话号码
				'smsText' => '',//发送的文本
		),
	)
);

?>			