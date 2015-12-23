<?php 
/**
 * @Title: 文件系统视图
 * @Description: todo(文件管理列表) 
 * @author qchlian 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2013-9-13
 * @version V1.0
 */
class MisFileManagerViewModel extends ViewModel {
	protected $autoCheckFields = false;
	public $viewFields = array(
		"MisFileManager"=>array('_as'=>'mis_file_manager','*','_type'=>'LEFT'),
		"mis_file_manager_access"=>array('_as'=>'mis_file_manager_access','userid'=>'access_userid','id'=>'access_id','_on'=>'mis_file_manager_access.fid=mis_file_manager.id','_type'=>'LEFT'),
	);
}
?>