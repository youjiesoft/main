<?php class MisHrRegularEmployeeViewModel extends ViewModel{
    //put your code here
    /**
     * 人事转正模型
     * @var unknown_type
     */
	public $viewFields = array(
			'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info','id','name','orderno','sex','education','chinaid','profession','picture','national','birthday','school','phone','urgentphone','email','qq','weibo','ismarry','nativeaddress','address','politicsstatus','deptid','dutylevelid','dutyname','worktype','indate','probationCycle','transferdate','worktype','_as'=>'mis_hr_personnel_person_info','_type'=>'LEFT'),
			'mis_hr_regular_employee'=>array('_as'=>'mis_hr_regular_employee','itemid','grade','remark' ,'_as'=>'mis_hr_regular_employee','_on'=>'mis_hr_personnel_person_info.id=mis_hr_regular_employee.employeeid'),
	);
}
?>