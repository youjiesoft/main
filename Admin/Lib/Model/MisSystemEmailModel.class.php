<?php
class MisSystemEmailModel extends CommonModel {
	protected $trueTableName = 'mis_system_email';
	public $_validate=array(
		array('email','require','电子邮件地址必填！'),
		array('pop3','require','接收邮件'),
		array('smtp','require','发送邮件'),
	);
	/*
	 * 自动填充
	 */
	public $_auto		=	array(
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