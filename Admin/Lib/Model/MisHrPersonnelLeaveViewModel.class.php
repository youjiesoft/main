<?php 
class MisHrPersonnelLeaveViewModel extends ViewModel{
    /**
     * 人事离职模型
     * @var unknown_type
     */
	public $viewFields = array(
			'mis_hr_leave_employee'=>array('_as'=>'mis_hr_leave_employee','id','forleavetype','employeeid'=>'employeeid','orderno'=>'leaveorderno','leavedate','leavereason','leavetype','remark','status','auditState','ptmptid','ostatus','alreadyAuditUser','auditUser','createid','createtime','_type'=>'LEFT'),
			'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info','accounttype','agreetypeid','phone','id'=>'personid','name','orderno','dutyname','itemid','deptid','dutylevelid','sex','indate','education','chinaid','_on'=>'mis_hr_personnel_person_info.id=mis_hr_leave_employee.employeeid'),
	);
}
?>