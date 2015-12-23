<?php
/**
 * 
 * @Title: MisWorkFacilityDistributeModel 
 * @Package package_name
 * @Description: todo(设备分布表) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-23 下午4:32:33 
 * @version V1.0
 */
class MisWorkFacilityDistributeModel extends CommonModel{
	protected  $trueTableName = 'mis_work_facility_distribute';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('appqty','numberToReplace',self::MODEL_BOTH,'callback'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
}
?>