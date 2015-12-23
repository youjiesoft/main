<?php
/**
 * 
 * @Title: MisDynamicFormProperyViewModel 
 * @Package package_name
 * @Description: todo(组件及模板视图) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年12月23日 下午3:39:48 
 * @version V1.0
 */
class MisDynamicFormProperyViewModel extends ViewModel {
	public $viewFields = array(
			"mis_dynamic_form_propery"=>array('_as'=>'mis_dynamic_form_propery','id','title','fieldname','category','ids','formid','tplid','status','_type'=>'LEFT'),
			"mis_dynamic_form_template"=>array('_as'=>'mis_dynamic_form_template','tplname','formid'=>'tformid','_on'=>'mis_dynamic_form_propery.tplid=mis_dynamic_form_template.id'),
			);
}
?>