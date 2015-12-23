<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(人事管理：人事申请模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:42:54 
 * @version V1.0
 */
class MisHrPersonnelApplicationInfoModel extends CommonModel {
	protected $trueTableName = 'mis_hr_personnel_applicationinfo';
	
	public $_validate=array(
		array('orderno','','编号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_BOTH),
		array('orderno','require','编号必须'),
			
	);
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('applicationdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array('toworkdate','dateToTimeString',self::MODEL_BOTH,'callback'),
			array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),
	);
}
?>