<?php
//Version 1.0
// 用户模型
class UserViewModel extends ViewModel {
	//用户和员工视图
	public $viewFields = array(
			'user' => array('_as'=>'user','account','shangji','anquandengji','attachrole','role_id','id','name','employeid','password','qq','isonline','mobile','email','leavetime','status','_type'=>'LEFT'),
			'mis_system_department'=>array('_as'=>'mis_system_department','id'=>'did','name'=>'deptname','parentid','status'=>'deptstatus','_on'=>'user.dept_id=mis_system_department.id','_type'=>'LEFT'),
			"Duty"=>array('_as'=>'duty','id'=>'dutyid','name'=>'dutyname','_on'=>'duty.id=user.duty_id','_type'=>'LEFT'),
		);		
}
?>