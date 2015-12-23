<?php
/**
 * @Title: MisMessageInterEmailInboxModel 
 * @Package package_name
 * @Description: todo(mis_message_inter_email_inbox的model) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-4 下午3:54:09 
 * @version V1.0
 */
class MisMessageInterEmailInboxModel extends CommonModel {
	protected $trueTableName = 'mis_message_inter_email_inbox';

	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
	
}
?>