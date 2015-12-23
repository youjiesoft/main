<?php
class MisOaItemsWaitForViewModel extends ViewModel{
	public $viewFields = array(
			"mis_oa_flows_instance"=>array('_as'=>'mis_oa_flows_instance','id'=>'instanceid','flowskey','flowsuser','flowsusername','dostatus','doinfo','_type'=>'LEFT'),
			"mis_oa_items"=>array('id','title','secretlevel','urgentlevel','updatetime','relevanceitems','content','dealstatus','createid','description','status'=>'status','createtime','_as'=>'mis_oa_items','_on'=>'mis_oa_items.id=mis_oa_flows_instance.itemsid'),
	);
}