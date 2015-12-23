<?php
class MisHrEgressRegisterAction extends CommonAuditAction {
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)
			$map['status']=array("gt",-1);
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws
	 */
	function _before_add(){
		$this->assign('now',time());
		$this->assign("uid",$_SESSION[C('USER_AUTH_KEY')]);
		$this->assign("deptid",$_SESSION['user_dep_id']);
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws 
	*/
	function _before_edit(){
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(前置添加函数)
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws 
	*/
	function _before_insert(){
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(前置修改函数)  
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws
	*/
	function _before_update(){
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws 
	*/
	function _after_edit($vo){
	}
	/**
	 * @Title: _after_list
	 * @Description: todo(前置List)
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws 
	*/
	function _after_list(){
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws
	*/
	function _after_insert($id){
	}
	/**
	 * @Title: _after_add
	 * @Description: todo(后置add函数)  
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws
	*/
	function _after_add(){
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)  
	 * @author 屈强
	 * @date 2014-08-02 10:05:06
	 * @throws
	*/
	function _after_update(){
	}
}
?>