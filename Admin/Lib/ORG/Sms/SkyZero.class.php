<?php
class SmsHttp{
private  $account  = "";	 //账号
private  $password = "";   //密码
private  $smsid    = "";			 //查状态报告需要带msgid,可为空，为空是查询多条状态报告
	  public function __construct($account,$password){
	  	 $this->account =$account;
	  	 $this->password=$password;
	  }

	  /*
	   * 提供短信
	  * $phone 电话号码，多个以,号分隔
	  * $content 发送内容
	  * $httpType 请求类型  1为发送短信
	  */
	  public function getMessage($phone,$content,$httpType=1,$test){		
		  	switch($httpType){
		  		case 1://发送短信
		  			$url="http://utf8.sms.webchinese.cn/?Uid=zerosky&Key=8e0c3a6817a34384c165&smsMob=$phone&smsText=$content"; 
		  			//1.初始化，创建一个新cURL资源
		  			$curl = curl_init();
		  			//2.设置URL和相应的选项
				    curl_setopt($curl,CURLOPT_URL,$url);                //请求地址  
				    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);    //屏蔽返回结果 
		  			//3.抓取URL并把它传递给浏览器
		  			$return=curl_exec($curl);
		  			//4.关闭cURL资源，并且释放系统资源
		  			curl_close($curl);
		  			break;
		  		case 2://返回上行短信
		  			$return=false;
		  			break;
		  		case 3://返回报表
		  			$return=false;
		  			break;
		  		case 4://返回余额
		  			$return=false;
		  			break;  					  				
		  }
		  return   $return;  
	 }
}
?>