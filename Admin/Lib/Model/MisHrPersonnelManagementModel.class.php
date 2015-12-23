<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(正式员工入职) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-6-6 上午11:48:55 
 * @version V1.0
 */
class MisHrPersonnelManagementModel extends CommonModel{
	protected  $trueTableName = 'mis_hr_personnel_person_info';
	
	public $_auto	=array(
				
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('agreetime','dateToTimeString',self::MODEL_BOTH,'callback'),//合约时间
			array('indate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('birthday','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('transferdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}

?>