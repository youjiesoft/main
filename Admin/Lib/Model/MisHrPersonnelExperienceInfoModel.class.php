<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(工作经历模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:47:06 
 * @version V1.0
 */
class MisHrPersonnelExperienceInfoModel extends CommonModel {
	/*
	 * 表名
	*/
	protected  $trueTableName = 'mis_hr_personnel_experience_info';

	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('startdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('finishdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>
