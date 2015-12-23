<?php
/**
 * 
 * @Title: MisDynamicFormProperyAndSubViewModel 
 * @Package package_name
 * @Description: todo(组件及模板视图) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年12月23日 下午3:39:48 
 * @version V1.0
 */
class MisDynamicFormProperyAndSubViewModel extends ViewModel {
	public $viewFields = array(
			"mis_dynamic_form_propery"=>array('_as'=>'mis_dynamic_form_propery','id','showoption','subimporttableobj','subimporttablefieldobj','subimporttablefield2obj','subimporttableobjcondition','treedtable','treeshowfield','format','title','filedname','category','filedname','ids','formid','tplid','status','_type'=>'LEFT'),
			"mis_dynamic_database_sub"=>array('_as'=>'mis_dynamic_database_sub','field','formid'=>'subformid','_on'=>'mis_dynamic_form_propery.ids=mis_dynamic_database_sub.id'),
			);
}
?>