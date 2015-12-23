<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(协同回复模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-1 下午12:02:26 
 * @version V1.0
 */
class MisOaItemsReplyModel extends CommonModel {
	protected $trueTableName = 'mis_oa_items_reply';
	/*
	 * 自动填充
	*/
	public $_auto =	array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('senduserid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>