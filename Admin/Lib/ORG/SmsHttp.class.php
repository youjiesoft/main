<?php
class SmsHttp{
private  $account  = "dh56712";	 //账号
private  $password = "F2Ha$2qJ";   //密码
private  $smsid    = "";			 //查状态报告需要带msgid,可为空，为空是查询多条状态报告
  public function __construct(){
  	 $account ="dh56712";	   //账号 tml  dh56241
  	 $password="F2Ha$2qJ";       //密码       oKd%4Jpt
  	 $this->account =$account;
  	 $this->password=md5($password);
  }  
  /*
   * 提供短信
   * $phone 电话号码，多个以,号分隔
   * $content 发送内容
   * $httpType 请求类型  1为发送短信
   */
  public function getMessage($phone,$content,$httpType=1){
  	$key==$_REQUEST['key']?$_REQUEST['key']:0;
  	switch($httpType){
  		case 1://发送短信
			$sendSmsAddress = "http://3tong.net/http/sms/Submit";
			$message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
							."<message>"
							. "<account>"
							. $this->account
							. "</account><password>"
							. $this->password
							. "</password>"
							. "<msgid></msgid><phones>"
							. $phone
							. "</phones><content>"
							. $content
							. "</content><subcode>"
							."</subcode>"
							."<sendtime></sendtime>"
							."</message>";
  			break;
  			
  		case 2://获取上行短信  			
  			if($key=='yangxi'){
  			$DeliverAddress = "http://3tong.net/http/sms/Deliver";
  			 $message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
  			."<message>"
  			. "<account>"
  			. $this->account
  			. "</account><password>"
  			. $this->password
  			. "</password>"
  			."</message>";
  			}else{
  				$expSign=true;
  			}
  			 break;
  		case 3://获取报告
  			if($key=='yangxi'){
  			$ReportAddress = "http://3tong.net/http/sms/Report";
  			 $message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
			  			."<message>"
			  			. "<account>"
			  			. $this->account
			  			. "</account><password>"
			  			. $this->password
			  			. "</password>"
			  			. "<msgid>"
			  			. $this->smsid
			  			. "</msgid><phone>"
			  			. $phone
			  			. "</phone>"
			  			."</message>";
  			}else{
  				$expSign=true;
  			}
  			 break;
  		case 4://查询余额
  			if($key=='yangxi'){
  			  $BalanceAddress = "http://3tong.net/http/sms/Balance";
  			 $message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
  			 ."<message>"
  			 . "<account>"
  			 . $this->account
  			 . "</account><password>"
  			 . $this->password
  			 . "</password>"
  			 ."</message>";
  			}else{
  				$expSign=true;
  			}
  			 break;
  	}
  	//验证密钥标示不对，直接返回null,不执行短信查询
  	if($expSign===true) return;
	  $params = array(
	  		'message' => $message);
	  $data = http_build_query($params);
	  $context = array('http' => array(
	  		'method' => 'POST',
	  		'header'  => 'Content-Type: application/x-www-form-urlencoded',
	  		'content' => $data,
	  ));
	  $contents = file_get_contents($sendSmsAddress, false, stream_context_create($context));
	  //echo $contents;
	  return $contents;
  }
}

/*
 * 提供报告
* $phone 电话号码，多个以,号分隔
* $content 发送内容
* $httpType 请求类型  1为发送短信
*/
/**
 * 8517
*/
/*
$account = "dh8373";	   //账号
$password = md5("dhtest"); //密码
$phone = "18621803633";   //多个号码用逗号隔开
$content = "祝福王振江";
$smsid  =  "";							//查状态报告需要带msgid,可为空，为空是查询多条状态报告
//发送短信


$sendSmsAddress = "http://3tong.net/http/sms/Submit";
$message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
				."<message>"
				. "<account>"
				. $account
				. "</account><password>"
				. $password
				. "</password>"
				. "<msgid></msgid><phones>"
				. $phone
				. "</phones><content>"
				. $content
				. "</content><subcode>"
				."</subcode>"
				."<sendtime></sendtime>"
				."</message>";
		
				

//获取上行短信
/*$DeliverAddress = "http://3tong.net/http/sms/Deliver";
$message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
				."<message>"
				. "<account>"
				. $account
				. "</account><password>"
				. $password
				. "</password>"
				."</message>";
			
				*/
//获取状态报告
/*$ReportAddress = "http://3tong.net/http/sms/Report";
$message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
				."<message>"
				. "<account>"
				. $account
				. "</account><password>"
				. $password
				. "</password>"
				. "<msgid>"
				. $smsid
				. "</msgid><phone>"
				. $phone
				. "</phone>"
				."</message>";
*/
//查询余额
/*
$BalanceAddress = "http://3tong.net/http/sms/Balance";
$message ="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
				."<message>"
				. "<account>"
				. $account
				. "</account><password>"
				. $password
				. "</password>"
				."</message>"; 
				*/
/*
$params = array(
	'message' => $message);
	
$data = http_build_query($params);
$context = array('http' => array(
	'method' => 'POST',
	'header'  => 'Content-Type: application/x-www-form-urlencoded',
	'content' => $data,
));
$contents = file_get_contents($sendSmsAddress, false, stream_context_create($context));
echo$contents;
*/
?>