<?php
/**
 * @Title: MisSystemFlowViewModel
 * @Package 项目模板-项目菜单生成视图
 * @Description: TODO(项目节点与任务)
 * @author yangxi
 * @company 重庆特米洛科技有限公司˾
 * @copyright 重庆特米洛科技有限公司˾
 * @date 2014-10-18 19:18:54
 * @version V1.0
 */
class MisSystemFlowViewModel extends ViewModel {
	
	public $viewFields = array(
	     'mis_system_flow_type'=>array('_as'=>'mis_system_flow_type','id','name'),
	     'mis_system_flow_form'=>array('_as'=>'mis_system_flow_form','id'=>'form_id','name'=>'form_name', '_on'=>'mis_system_flow_form.category=mis_system_flow_type.id'),
	     'mis_system_flow_resource'=>array('_as'=>'mis_system_flow_resource','managerid','userid','_on'=>'mis_system_flow_resource.id=mis_system_flow_form.id'),
	   );
}
?>