<?php
/**
 * @Title: MisWorkFacilityAbnormalmoveViewModel 
 * @Package package_name
 * @Description: todo(办公设备异动view) 
 * @author xiafengqin 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-22 上午11:14:47 
 * @version V1.0
 */
class MisWorkFacilityAbnormalmoveViewModel extends ViewModel{
	public $_auto	=array(
			array('createid','getMemberId',self::MODEL_INSERT,'callback'),
			array('updateid','getMemberId',self::MODEL_UPDATE,'callback'),
			array('createtime','time',self::MODEL_INSERT,'function'),
			array('updatetime','time',self::MODEL_UPDATE,'function'),
		   array("companyid","getCompanyID",self::MODEL_INSERT,"callback"),
			array("departmentid","getDeptID",self::MODEL_INSERT,"callback"),
			array('sysdutyid','getDutyID',self::MODEL_INSERT,'callback'),

	); 
	public $viewFields = array(
			"mis_work_facility_manage"=>array('_as'=>'mis_work_facility_manage','id','orderno','equipmenttype','departmentid','unit','equipmentname','version','qty','status','_type'=>'LEFT'),
			"mis_work_facility_type"=>array('_as'=>'mis_work_facility_type','name'=>'equipmenttypename','_on'=>'mis_work_facility_manage.equipmenttype=mis_work_facility_type.id','_type'=>'LEFT'),
			"mis_product_unit"=>array('_as'=>'mis_product_unit','name'=>'unitname','_on'=>'mis_work_facility_manage.unit=mis_product_unit.id','_type'=>'LEFT'),
			"mis_system_department"=>array('_as'=>'mis_system_department','name'=>'departmentname','_on'=>'mis_work_facility_manage.departmentid=mis_system_department.id','_type'=>'LEFT'),
	);
	
}
?>