<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(正式员工年总考评视图模型) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-3-27 上午11:43:49 
 * @version V1.0
 */
class MisHrPersonnelAppraisalInfoViewModel extends ViewModel{
	public $viewFields = array(
			"MisHrPersonnelPersonInfo"=>array('_as'=>'mis_hr_personnel_person_info','id','orderno','name','sex','indate','dutyname','dutylevelid','transferdate','email','_type'=>'LEFT'),
			"Duty"=>array('_as'=>'duty','id'=>'dutyid','name'=>'dutylevelname','_on'=>'duty.id=mis_hr_personnel_person_info.dutylevelid','_type'=>'LEFT'),
			"MisSystemDepartment"=>array('_as'=>'mis_system_department','id'=>'deptid','name'=>'deptname','_on'=>'mis_system_department.id=mis_hr_personnel_person_info.deptid','_type'=>'LEFT'),
			"User"=>array('_as'=>'user','id'=>'userid','_on'=>'user.employeid=mis_hr_personnel_person_info.id','_type'=>'LEFT'),
	);
}
?>