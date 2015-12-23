<?php
class MisTaskInformationViewModel extends ViewModel {
    
    public $viewFields = array(
	'mis_task_information'=>array('_as'=>'mis_task_information','id','remark','begindate','enddate','realityknowdate','realitybegindate','realityenddate','status','trackuser','assignstatus','executeuser','executingstatus','difficulty','urgentstatus','chedule','_type'=>'LEFT'),

	'mis_task'=>array('_as'=>'mis_task','id'=>'taskid','title','pid','createid','createtime','updateid','updatetime','_on'=>'mis_task.id=mis_task_information.taskid'),
    );
}
?>