<?php
/**
 * @Title: MisAutoEweAction
 * @Package package_name
 * @Description: todo(动态表单_自动生成-单据提醒)
 * @author 管理员
 * @company Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @copyright 本文件归属于Aqo5Re65bSr5zG755m45t92YuQnZvNHbtRnL3d3d
 * @date 2015-07-20 14:55:22
 * @version V1.0
*/
class MisAutoEweAction extends MisAutoEweExtendAction {
	public function _filter(&$map){
		
		$this->_extend_filter($map);
	}
	/**
	 * @Title: _before_index
	 * @Description: todo(前置index函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _before_index() {
		
		$this->_extend_before_index();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _before_edit(){
		if($_REQUEST['main'])
			$this->assign("main",$_REQUEST['main']);
		$this->_extend_before_edit();
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(前置添加函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _before_insert(){
		$this->checkifexistcodeororder('mis_auto_oyljp','orderno',$this->getActionName());
		$this->_extend_before_insert();
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(前置修改函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _before_update(){
		$this->checkifexistcodeororder('mis_auto_oyljp','orderno',$this->getActionName(),1);
		$this->_extend_before_update();
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws 
	*/
	function _after_edit(&$vo){		
		$this->_extend_after_edit(&$vo);
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _after_insert($id){
		$this->_extend_after_insert($id);
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(前置add函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _before_add(){
		if($_REQUEST['main']) $this->assign("main",$_REQUEST['main']);	
		$this->_extend_before_add($vo);
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)  
	 * @author 管理员
	 * @date 2015-07-20 14:55:22
	 * @throws
	*/
	function _after_update(){
		$this->_extend_after_update();
	}
// 	public function resultrole(){
// 		$model = getFieldBy($_POST['modelid'],'id','name','node');
// 		$tables = $this->lookuptablelist($model);
// 		$tablearr = array_keys($tables);
		
// 		echo W('ShowSqlResult', array('inputname'=>"tixingtiaojian",'model'=>$model,'table'=>array($model=>array($tablearr))) );
		
// 	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>