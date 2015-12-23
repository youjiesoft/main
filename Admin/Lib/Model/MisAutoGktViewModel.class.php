<?php
/**
 * @Title: MisAutoGktModelView
 * @Package package_name
 * @Description: todo(动态表单_自动生成-流程节点管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-10-20 15:52:25
 * @version V1.0
*/
class MisAutoGktViewModel extends ViewModel {
	public $viewFields = array(
	'mis_auto_hcfwc'=>array('id','name','supcategory','category','modelname','parentid','summary','days','begintime','endtime','outlinenumber','outlinelevel','readyonly','percentcomplete','notes','constrainttype','hyperlink','hyperlinkurl','classname','critical','custtypeid','hyid','cylid','formtype','yzid','_type'=>'LEFT'),
);
}
?>