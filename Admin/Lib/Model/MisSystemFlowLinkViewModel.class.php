<?php
class MisSystemFlowLinkViewModel extends ViewModel {
	
	public $viewFields = array(
	     'mis_system_flow_form'=>array('_as'=>'mis_system_flow_form','id'=>'id','name'=>'name','critical'=>'critical'),
	     'mis_system_flow_link'=>array('_as'=>'mis_system_flow_link','predecessorid','successorid','type','_on'=>'mis_system_flow_form.id=mis_system_flow_link.id'),
	   );
}
?>