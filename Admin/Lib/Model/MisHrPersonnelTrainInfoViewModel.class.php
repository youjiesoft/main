<?php
class MisHrPersonnelTrainInfoViewModel extends ViewModel{
	/**
	 * 人事调职模型
	 * @var unknown_type
	 */
	public $viewFields = array(
			'mis_hr_personnel_train_info'=>array('_as'=>'mis_hr_personnel_train_info','id'=>'trainid','personid'=>'personid','trantype'=>'trantype','remark','_as'=>'mis_hr_personnel_train_info','_type'=>'LEFT'),
			'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info','id','workstatus','name','orderno','dutyname','deptid','sex','age','indate','education','chinaid','_as'=>'mis_hr_personnel_person_info','_on'=>'mis_hr_personnel_person_info.id=mis_hr_personnel_train_info.personid'),
	);
}
?>