<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(待阅读报告视图) 
 * @author liminggang 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-7-2 下午5:16:22 
 * @version V1.0
 */
class MisWorkReadStatementViewModel extends ViewModel {

	//待阅读报告视图
	public $viewFields = array(
		'MisWorkReadStatement' => array('_as'=>'mis_work_read_statement','id','userid','copystep','iscomment','readstatus','comment','commentuserid','commenttime','status','_type'=>'LEFT'),
		'MisWorkStatement'=>array('_as'=>'mis_work_statement','summary','title','typeid','content','createid','createtime','_on'=>'mis_work_read_statement.workid=mis_work_statement.id'),
	);
}
?>