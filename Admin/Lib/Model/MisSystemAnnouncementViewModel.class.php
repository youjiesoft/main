<?php
 class MisSystemAnnouncementViewModel extends ViewModel{

	public $viewFields = array(
		'mis_system_announcement'=>array('_as'=>'mis_system_announcement','id','title','createid','createtime','type','content','starttime','endtime','toptime',top,'status','_as'=>'mis_system_announcement','_type'=>'LEFT'),
		//'mis_expert_question_type'=>array('_as'=>'mis_expert_question_type','name'=>'typename','_on'=>'mis_expert_question_type.id=mis_expert_question_list.categoryid','_type'=>'LEFT'),
		'user' =>array('_as'=>'user','name','_on'=>'user.id=mis_system_announcement.createid','_type'=>'LEFT'),
		//'mis_hr_personnel_person_info'=>array('_as'=>'mis_hr_personnel_person_info','picture','_on'=>'user.employeid=mis_hr_personnel_person_info.id','_type'=>'LEFT'),
	);
}
?>