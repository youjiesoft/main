<?php
/** 
 * @Title: MisHrBasicEmployeeContractModel 
 * @Package package_name
 * @Description: todo(员工合同模型) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-16 上午10:24:50 
 * @version V1.0 
*/ 
class MisHrBasicEmployeeContractModel extends CommonModel {
	protected $trueTableName = 'mis_hr_basic_employee_contract';
	public $_auto	=array(
		array('createid','getMemberId',self::MODEL_INSERT,'callback'),
		array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_UPDATE,'function'),
		array('starttime','dateToTimeString',self::MODEL_BOTH,'callback'),
		array('endtime','dateToTimeString',self::MODEL_BOTH,'callback'),
		array('signdate','dateToTimeString',self::MODEL_BOTH,'callback'),
		array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
		array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
		array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>