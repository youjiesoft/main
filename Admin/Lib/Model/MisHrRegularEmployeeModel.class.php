<?php 
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo正式员工)
 * @author liminggang
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-4-11 下午5:34:03
 * @version V1.0
 */
class MisHrRegularEmployeeModel extends CommonModel{
	/**
	 * 员工培训关联表
	 */
	protected  $trueTableName = 'mis_hr_regular_employee';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>