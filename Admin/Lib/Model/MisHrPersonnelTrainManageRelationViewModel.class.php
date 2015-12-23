<?php 

/** 
 * @Title: MisHrPersonnelTrainManageRelationViewModel 
 * @Package package_name
 * @Description: todo(员工与课程关联视图) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-19 下午2:31:55 
 * @version V1.0 
*/ 
class MisHrPersonnelTrainManageRelationViewModel extends ViewModel {
	public $viewFields = array(
		'mis_hr_personnel_train_manage_relation'=>array('_as'=>'mis_hr_personnel_train_manage_relation','id','manageid','trainid','personid','classhour','status','_type'=>'LEFT'),
		'mis_hr_evaluation_train'=>array('_as'=>'mis_hr_evaluation_train','orderno','name','type','classsource','departmentid','lecturer','level','traintime','site','classhour'=>'tclasshour','trainform','assessment','traincost','curriculumvalue','_on'=>'mis_hr_personnel_train_manage_relation.trainid=mis_hr_evaluation_train.id'),
	);
}
?>