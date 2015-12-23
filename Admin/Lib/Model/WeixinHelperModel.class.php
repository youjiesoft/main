<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: 微信模型
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-9-25 上午10:08:17 
 * @version V1.0
 */
class WeixinHelperModel extends Model{
	
	/**
	 * @Title: getMessages 
	 * @Description:获取$userid 用户的邮件信息
	 * @param int 当前传入的后台用户ID $userid
	 * @param int 获取邮件条数 $number 获取邮件的条数。默认为10条
	 * @param int 获取邮件状态 $isRead 已读或未读邮件：0 表示未读，1 表示已读
	 * @param 返回值的类型 $returnType  
	 * @author liminggang 
	 * @date 2014-9-25 上午10:09:58 
	 * @throws
	 */
	public function getMessages($userid,$number=10,$isRead=0,$returnType='json'){
		//file_put_contents('a.log',var_export($_REQUEST,1),FILE_APPEND);
		//$returnData = A("MisMessageInbox")->getMessages(10,0,$this->userid);
		//返回了mis_message_user表的id(邮件指向)，createid发件人,createtime发件时间，messageType(邮件类型),mis_message表的title（主题）
		
		$map=array();//初始化查询条件空数组
		//根据情况配置发件人查询条件
		if($userid!==0){
			$map['recipient'] = $userid;
		}else{
			if ($_SESSION[C('USER_AUTH_KEY')]){
				$map['recipient'] = $_SESSION[C('USER_AUTH_KEY')];
			}else {
				$this->error ( '没有获取到当前登陆人！' );
			}
		}
		$map['readedStatus'] = $isRead;//表示未读
		$map['mis_message.status'] = 1;
		$map['mis_message_user.status'] = 1;
		$map['mis_message_user.returnmessage'] = 1;
		//实例化mis_message_user表
		$model = D('MisMessageInboxView');
		//定义其他where条件
		$returnData = $model->where( $map )->order('id desc')->field('id,title,sendid,messageType,username,createtime')->limit($number)->select();
		//现在重新拼装带URL的返回值数据
		foreach($returnData as $key => $val){
			$messageType = "inboxself";
			if($val['messageType'] == 1){
				$messageType = "inboxsystem";
			}
			$returnData[$key]['urldata']="MisMessage,index,id,".$returnData[$key]['id'].",messageType,".$messageType.";MisMessage;MisMessage";
		}
		
		return $this->getReturnData($returnData,$returnType);			
	}
	//此方法用来处理返回数据类型，为JSON  还是  array
	public  function getReturnData($returnData,$returnType){
		if($returnType=='json'){
			if($returnData){
				//echo '[{"error":"没有找到数据"}]';
				echo json_encode($returnData);
			}else{
				echo '[{"error":"没有找到数据"}]';
			}
		}else if($returnType=='arr'){
			if($returnData){
				if(is_array($returnData)){
					$returnData=$returnData;
				}else{
					$returnData=(array)$returnData;
				}
				return $returnData;
			}
		}
	}
}