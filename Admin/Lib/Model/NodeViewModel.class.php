<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(用一句话描述该文件做什么) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-6-27 下午3:29:26 
 * @version V1.0
 */
class NodeViewModel extends ViewModel {
	public $viewFields = array(
		'node'=>array('_as'=>'node','id','name','icon','level','title','group_id','sort','status','remark','pid','showmenu','toolshow','category','type','_type'=>'LEFT'),
		'`group`'=>array('_table'=>'`group`','name'=>'group_name', '_on'=>'node.group_id=`group`.id','_type'=>'LEFT'),
 	);
}
?>