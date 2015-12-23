<?php
//Version 1.0

class MisSystemUserViewModel extends ViewModel {

 public $viewFields = array(
		'mis_system_user'=>array('id','manname','sex','workdate','dutyid','remark','createtime','createid','dutyid','_as'=>'mis_system_user'),
		'mis_system_duty'=>array('name'=>'dtName', '_as'=>'mis_system_duty','_on'=>'mis_system_user.dutyid=mis_system_duty.id','_type'=>'LEFTS'),
 	);
}
?>