<?php
//Version 1.0
/**
+----------------------------------------------------------
* 站内信视图
+----------------------------------------------------------
* @author lcx
* @date:2013-07-27
*/
class MisMessageInboxViewModel extends ViewModel{
	public $viewFields = array(
			'mis_message_user' => array('_as'=>'mis_message_user','id','recipient','sendid','createid','createtime','commit','_type'=>'LEFT'),
			'mis_message' => array('_as'=>'mis_message','id'=>'messageid','title','recipientname','content','commit','_on'=>'mis_message.id=mis_message_user.sendid','_type'=>'LEFT'),
			
	);
	
}
?>