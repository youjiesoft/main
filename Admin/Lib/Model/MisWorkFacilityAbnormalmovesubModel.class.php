<?php
/**
 * 
 * @Title: MisWorkFacilityAbnormalmovesubModel 
 * @Package package_name
 * @Description: todo(设备异动sub) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-7-24 下午3:02:55 
 * @version V1.0
 */
class MisWorkFacilityAbnormalmovesubModel extends CommonModel{
	protected  $trueTableName = 'mis_work_facility_abnormalmovesub';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('oldqty','numberToReplace',self::MODEL_BOTH,'callback'),
			array('qty','numberToReplace',self::MODEL_BOTH,'callback'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
}