<?php
/**
 * @Title: MisWorkFacilityManageModel 
 * @Package package_name
 * @Description: todo(办公设备管理MODEL) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-22 下午2:21:45 
 * @version V1.0
 */
class MisWorkFacilityManageModel extends CommonModel{
	protected  $trueTableName = 'mis_work_facility_manage';
	public $_auto	=array(
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('qty','numberToReplace',self::MODEL_BOTH,'callback'),
			array('kyqty','numberToReplace',self::MODEL_BOTH,'callback'),
			array('unitprice','numberToReplace',self::MODEL_BOTH,'callback'),
			array('amount','numberToReplace',self::MODEL_BOTH,'callback'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	);
}
?>