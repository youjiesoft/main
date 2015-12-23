<?php 
/**
 * @Title: MisHrBecomeEmployeeModel
 * @Package package_name
 * @Description: todo(人事转正管理)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-26 下午2:17:02
 * @version V1.0
 */
class MisHrBecomeEmployeeModel extends CommonModel{
	protected  $trueTableName = 'mis_hr_regular_employee';
	
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('positivedate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('indate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>