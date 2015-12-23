<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(合约签订模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:46:18 
 * @version V1.0
 */
class MisHrPersonnelPersonContractModel extends CommonModel {
	
	protected  $trueTableName = 'mis_hr_personnel_person_contract';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('signdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('enddate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>
