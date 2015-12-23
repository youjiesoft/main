<?php
/**
 * @Title: MisHrBasicEmployeeforContractViewModel 
 * @Package package_name
 * @Description: todo(员工合同做排除) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-12-11 下午6:33:35 
 * @version V1.0
 */
class MisHrBasicEmployeeforContractViewModel extends ViewModel {
	public $viewFields = array(
		'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info','working','_type'=>'LEFT'),
		'mis_hr_personnel_person_info_contract'=>array('_as'=>'mis_hr_personnel_person_info_contract','id','employeeid','orderno','name','sex','tel','cardid','familyaddress','newsleaddress','accounttype','contracttype','limittype','starttime','endtime','fixed','contractstatus','status','createid','updateid','createtime','updatetime','_on'=>'mis_hr_personnel_person_info.id=mis_hr_personnel_person_info_contract.employeeid'),
	);
}
?>