<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(客服信息 经营状况) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-17 下午2:01:56 
 * @version V1.0
 */
class MisManageStatusModel extends CommonModel{
	protected $trueTableName = 'mis_manage_status';
	
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('loandate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('sellprice','numberToReplace',self::MODEL_BOTH,'callback'),
			array('ratepaying','numberToReplace',self::MODEL_BOTH,'callback'),
			array('profit','numberToReplace',self::MODEL_BOTH,'callback'),
			array('qyproperty','numberToReplace',self::MODEL_BOTH,'callback'),
			array('inventory','numberToReplace',self::MODEL_BOTH,'callback'),
			array('facilityprice','numberToReplace',self::MODEL_BOTH,'callback'),
			array('balance','numberToReplace',self::MODEL_BOTH,'callback'),
			array('loan','numberToReplace',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
			
		);
}

?>