<?php
/**
 * @Title: file_name 
 * @Package package_name 
 * @Description: todo(部门角色维护) 
 * @author libo 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-4-29 上午11:13:59 
 * @version V1.0
 */
class MisOrganizationalSetAction extends CommonAction{
	/**
	 * @Title: _filter 
	 * @Description: todo(这里用一句话描述这个方法的作用) 
	 * @param unknown_type $map  
	 * @author libo 
	 * @date 2014-4-29 上午11:25:57 
	 * @throws
	 */
	public function _filter(&$map){
		$map['catgory'] = 5; //部门角色
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	
	public function _after_insert($id){
		$this->getPinyin('rolegroup', 'name');
		//创建成功，则给管理员发信息，负责对该项目人员类别进行权限分配
		//构造内部邮件title 和content
		//$this->pushMessage();
		//$this->pushMessage($recipientListID);
	}
	public function _before_add(){
		//公司信息新增
		if($_REQUEST['com']){
			$this->assign("com",$_REQUEST['com']);
		}
	}
}

?>