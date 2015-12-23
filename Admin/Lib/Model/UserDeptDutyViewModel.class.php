<?php
//Version 1.0
// 用户模型
class UserDeptDutyViewModel extends ViewModel {
	//用户和员工视图
	public $viewFields = array(
			'user_dept_duty'=>array('_as'=>'user_dept_duty' ,'id','typeid','status','_type'=>'LEFT'),
			'mis_system_department'=>array('_as'=>'mis_system_department','id'=>'deptid','name'=>'deptname','status'=>'deptstatus','_on'=>'user_dept_duty.deptid=mis_system_department.id','_type'=>'LEFT'),
			"Duty"=>array('_as'=>'duty','id'=>'dutyid','name'=>'dutyname','_on'=>'duty.id=user_dept_duty.dutyid','_type'=>'LEFT'),
		);		
	
	
}
?>