<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(员工基本信息视图表) 
 * @author lcx 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:43:49 
 * @version V1.0
 */
class MisHrBasicEmployeeViewModel extends ViewModel{
	public $viewFields = array(
		"MisHrBasicEmployee"=>array('_as'=>'mis_hr_personnel_person_info','id','accounttype','working','employeeheight','worktype','orderno','name','sex','indate','nativeaddress','dutyname','transferdate','email','chinaid','phone','workstatus','address','status','_type'=>'LEFT'),
		"Duty"=>array('_as'=>'duty','id'=>'dutylevelid','name'=>'dutyname','_on'=>'duty.id=mis_hr_personnel_person_info.dutylevelid','_type'=>'LEFT'),
		"MisSystemDepartment"=>array('_as'=>'mis_system_department','id'=>'deptid','name'=>'deptname','parentid','_on'=>'mis_system_department.id=mis_hr_personnel_person_info.deptid'),
	);
}
?>