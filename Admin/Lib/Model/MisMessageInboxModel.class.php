<?php
// 用户模型
class MisMessageInboxModel extends CommonModel {
	protected $trueTableName = 'mis_message_user';

	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	
	public function getMessages($number=1,$isRead=0,$userid=0,$map){
		//$map=array();//初始化查询条件空数组
		//根据情况配置发件人查询条件
		if($userid!==0){
			$map['recipient'] = $userid;
		}else{
			$map['recipient'] = $_SESSION[C('USER_AUTH_KEY')];
		}
		$map['readedStatus'] = $isRead;//表示未读
		$map['mis_message.status'] = 1;
		$map['mis_message_user.status'] = 1;
		$map['mis_message_user.returnmessage'] = 1;		
		//user.status=1条件被注销，防止创建邮件的用户被删除，或者创建人被注销
		//$map['user.status'] = 1;
		//实例化mis_message_user表
		$model = D('MisMessageInboxView');	
		//定义一个分页条数
		$numPerPage=C('PAGE_LISTROWS');
		//获取分页条码数
		$pageNum = $number;
		$firstNum = ($pageNum-1)*$numPerPage;
		$nextNum = $pageNum*$numPerPage;
		if($number){
			//定义其他where条件
			$list = $model->where( $map )->order('id desc')->field('id,title,createid,sendid,content,messageType,username,createtime')->limit($firstNum . ',' . $nextNum)->select();
		}else{
			//定义其他where条件
			$list = $model->where( $map )->order('id desc')->field('id,title,createid,sendid,content,messageType,username,createtime')->select();
		}
		return $list;
	}
	
	/**
	 * @Title: getMessagesDetail
	 * @Description: todo(查指定邮件详细内容)
	 * @param int    $id 读取指定邮件：mis_message_user的序号
	 * @author yangxi
	 * @date 2014-4-6
	 * @throws
	 */
	public function getMessagesDetail($id=0){
		if(!$id){
			return array();
		}else{
			$map=array();//初始化查询条件空数组
			$map['mis_message_user.id'] = $id;//表示自己当前读取的mis_message_user id
			$map['mis_message.status'] = 1;
			$map['mis_message_user.status'] = 1;
			$map['mis_message_user.returnmessage'] = 1;
			//user.status=1条件被注销，防止创建邮件的用户被删除，或者创建人被注销
			//$map['user.status'] = 1;
			//实例化mis_message_user表
			$model = D('MisMessageInboxView');
			//定义其他where条件
			$returnData = $model->where( $map )->order('id desc')->field('id,title,sendid,createid,recipientname,messageType,content,username,createtime')->find();
			if($returnData){
				//声明相关附件表
				$modelMAR = D('MisAttachedRecord');
				//现在重新拼装带URL的返回值数据
				$messageType = "inboxself";
				if($val['messageType'] == 1){
					$messageType = "inboxsystem";
				}
				$returnData['urldata']="MisMessage,index,id,".$returnData[$key]['id'].",messageType,".$messageType.";MisMessage;MisMessage";
                //获取附件信息
                $returnData['attach']=$modelMAR->getAttachedFields('MisMessage',$returnData['sendid']);
			}
			return $returnData;
		}
	}
}
?>