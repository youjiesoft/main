<?php
/**
 * @Title: MisWorkFacilityAbnormalmoveModel 
 * @Package package_name
 * @Description: todo(办公设备异动的MODEL) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-22 下午5:45:10 
 * @version V1.0
 */
class MisWorkFacilityAbnormalmoveModel extends CommonModel{
	protected  $trueTableName = 'mis_work_facility_abnormalmove';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('movedate','dateToTimeString',self::MODEL_BOTH,'callback'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
}