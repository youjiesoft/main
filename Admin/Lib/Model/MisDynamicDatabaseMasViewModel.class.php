<?php
/**
 * 
 * @Title: MisDynamicDatabaseMasViewModel 
 * @Package package_name
 * @Description: todo(表单记录) 
 * @author renling 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014年12月11日 下午4:01:00 
 * @version V1.0
 */
class MisDynamicDatabaseMasViewModel extends ViewModel {
	public $viewFields = array(
			"mis_dynamic_database_mas"=>array('_as'=>'mis_dynamic_database_mas','id','tablename','tabletitle','isprimary','ischoise','formid','status','_type'=>'LEFT'),
			"mis_dynamic_database_sub"=>array('_as'=>'mis_dynamic_database_sub','id'=>'subid','masid','field','title','type','length','category','weight','sort','isdatasouce','status'=>'substatus','_on'=>'mis_dynamic_database_mas.id=mis_dynamic_database_sub.masid'),
			);
}
?>