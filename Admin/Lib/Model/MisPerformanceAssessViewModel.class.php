<?php
/** 
 * @Title: MisPerformanceAssessViewModel 
 * @Package package_name
 * @Description: todo(绩效评估视图模型) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-8-15 下午5:07:15 
 * @version V1.0 
*/ 
class MisPerformanceAssessViewModel extends ViewModel {
	public $viewFields = array(
		'mis_performance_assess' => array('_as'=>'mis_performance_assess','id','totalscore','amendscore','isamend','amendremark','comments','conclusions','_type'=>'LEFT'),
		'mis_performance_plan' => array('_as'=>'mis_performance_plan','id'=>'planid','orderno'=>'planorderno','name'=>'planname','setdate','levelid','_on'=>'mis_performance_plan.id=mis_performance_assess.planid','_type'=>'LEFT'),
		'mis_hr_personnel_person_info' => array('_as'=>'mis_hr_personnel_person_info','name','orderno','deptid','_on'=>'mis_hr_personnel_person_info.id=mis_performance_assess.byuser','_type'=>'LEFT'),
		"duty" => array('_as'=>'duty','name'=>'dutyname','_on'=>'duty.id=mis_hr_personnel_person_info.dutylevelid','_type'=>'LEFT'),
		"mis_system_department" => array('_as'=>'mis_system_department','name'=>'deptname','_on'=>'mis_system_department.id=mis_hr_personnel_person_info.deptid','_type'=>'LEFT'),
	);
}
?>