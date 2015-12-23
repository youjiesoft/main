<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(所在项目部模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-4-11 上午12:53:38 
 * @version V1.0
 */
class MisHrPersonnelProjectRecordsModel extends CommonModel{

	protected $trueTableName = 'mis_hr_personnel_project_records';

	public $_validate	=	array(
			array('enddate','dataCompare',"离开日期晚于开始日期",self::VALUE_VAILIDATE,'callback',self::MODEL_BOTH,
					array('$_POST[begindate]')
			),
	);

	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('begindate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('enddate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);

}
?>