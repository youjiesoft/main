<?php
//Version 1.0
// 用户模型
class MisHrPersonnelPersonInfoViewModel extends ViewModel {
	//用户和员工视图
	public $viewFields = array(
		'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info' ,'sex','phone','shortNumber','officeNumber', 'id'=>'hrid','orderno'=>'orderno','deptid'=>'deptid','dutylevelid'=>'dutylevelid','indate'=>'indate','picture','status'=>'hrstatus','transferdate'=>'transferdate','_type'=>'LEFT'),
		'mis_system_department'=>array('_as'=>'mis_system_department','id'=>'did','name'=>'deptname','parentid','status'=>'deptstatus','_on'=>'mis_hr_personnel_person_info.deptid=mis_system_department.id','_type'=>'LEFT'),
		'user' => array('_as'=>'user','id','name','employeid','qq','isonline','mobile','email','leavetime','status','_on'=>'user.employeid=mis_hr_personnel_person_info.id','_type'=>'LEFT'),
		"Duty"=>array('_as'=>'duty','id'=>'dutyid','name'=>'dutyname','_on'=>'duty.id=mis_hr_personnel_person_info.dutylevelid','_type'=>'LEFT'),
	);		
}
?>