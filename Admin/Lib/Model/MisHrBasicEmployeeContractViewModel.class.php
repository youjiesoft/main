<?php
/** 
 * @Title: MisHrBasicEmployeeContractViewModel 
 * @Package package_name
 * @Description: todo(员工合同视图) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-10-16 上午10:25:56 
 * @version V1.0 
*/ 
class MisHrBasicEmployeeContractViewModel extends ViewModel {
	public $viewFields = array(
		'mis_hr_basic_employee_contract'=>array('_as'=>'mis_hr_personnel_person_info_contract','id','employeeid','orderno','name','sex','tel','cardid','familyaddress','newsleaddress','accounttype','contracttype','limittype','starttime','endtime','fixed','contractstatus','status','createid','updateid','createtime','updatetime','_type'=>'LEFT'),
		'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info','working','_on'=>'mis_hr_personnel_person_info.id=mis_hr_personnel_person_info_contract.employeeid','_type'=>'LEFT'),
	);
}
?>