<?php
/**
 * @Title: Action
 * @Package package_name
 * @Description: todo(动态表单_扩展类。本类为用户代码注入入口，系统一旦生成将不再重复生成。 * 						但当用户选为组合表单方案后会更新该文件，请做好备份)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2015-03-06 21:44:06
 * @version V1.0
*/
class ExtendAction extends CommonAction {
	public function _extend_filter(&$map){
	}
	/**
	 * @Title: _extend_before_index
	 * @Description: todo(扩展前置index函数)
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws 
	*/
	function _extend_before_index() {
	}
	/**
	 * @Title: _extend_before_edit
	 * @Description: todo(扩展的前置编辑函数)
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws 
	*/
	function _extend_before_edit(){
	$name=$this->getActionName();
	if(!$_REQUEST['eid']){
		$eid=$_REQUEST['bindid']?$_REQUEST['bindid']:$_REQUEST['id'];
		$this->getCileType();
		$this->assign("eid",$eid);
		$this->assign("ename",$name);
		$this->display("editbasic");exit;
	}
	$this->getCileType();
	}
	/**
	* 获取当前Action下的关联子action
	*/
	private function getCileType(){
		$name = $this ->getActionName();
		$MisAutoBindModel = M("mis_auto_bind");
		$_REQUEST['bindid']=$_REQUEST['bindid']?$_REQUEST['bindid']:$_REQUEST['id'];
		 // 查询符合条件的表单
		$MisAutoBindList=$MisAutoBindModel->where("status=1 and bindaname='{$name}'  and typeid=1")->select();
		$map = array();
		$map['status'] = 1;
		$map['bindid'] = $_REQUEST['bindid'];
		foreach($MisAutoBindList as $key =>$val) {
			if ($val['bindtype'] == 0) {
				$model = D($val['inbindaname']);
				$bindList = $model->where($map)->find();
				$MisAutoBindList[$key]['id'] = $bindList['id'];
				if(!$MisAutoBindList[$key]['id']){
					$date=array();
					$date['bindid']=$_REQUEST['bindid'];
					$reuslt=$model->add($date);
					$model->commit();
					$MisAutoBindList[$key]['id'] =$reuslt;
					$reuslt="";
				}
			}
		}
		$this->assign("bindid", $_REQUEST['id']);
		$this->assign('MisSaleClientSTypeList', $MisAutoBindList);
	}
	/**
	 * @Title: _extend_before_insert
	 * @Description: todo(扩展的前置添加函数)
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws 
	*/
	function _extend_before_insert(){
	}
	/**
	 * @Title: _extend_before_update
	 * @Description: todo(扩展前置修改函数)  
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws
	*/
	function _extend_before_update(){
	}
	/**
	 * @Title: _extend_after_edit
	 * @Description: todo(扩展后置编辑函数)
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws 
	*/
	function _extend_after_edit($vo){
	}
	/**
	 * @Title: _extend_after_list
	 * @Description: todo(扩展前置List)
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws 
	*/
	function _extend_after_list(){
	}
	/**
	 * @Title: _extend_after_insert
	 * @Description: todo(扩展后置insert函数)  
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws
	*/
	function _extend_after_insert($id){
		// 默认往关联表中插入空数据
		// 获取传送的id及主体id
		$name=$this->getActionName();
		$MisAutoBindModel =M ( "mis_auto_bind" );
		$bindMap['status']=1;
		$bindMap['bindaname']=$name;
		$bindMap['bindtype']=0;
		$MisAutoBindList=$MisAutoBindModel->where($bindMap)->getField("id,inbindaname");
		if($MisAutoBindList){
		$date = array ();
		//获取数据漫游date
		$MisSystemDataRoamingModel=D('MisSystemDataRoaming');
		$bindid = getFieldBy($id, "id", "orderno", $name);
		$bindList=array();
			foreach ($MisAutoBindList as $key=>$val){
				if(in_array($val, array_keys($bindList))){
				}else{
				$bindList[$val]=1;
				$model = D ( $val);
				$data=$MisSystemDataRoamingModel->dataRoamOrderno($name,$id,$val);
				foreach($data as $k=>$v){
				$date[key($v)] = reset($v);
				}
				$date['bindid']=$bindid;
				$result = $model->add ( $date );
				if (! $result) {
					$this->error ( "数据插入失败,请联系管理员！" );
				}
			}
		}
	}
	$this->success ( L ( '_SUCCESS_' ), '', array (
			'bindid' => $id,
	));
	}
	/**
	 * @Title: _extend_before_add
	 * @Description: todo(扩展前置add函数)  
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws
	*/
	function _extend_before_add(){
		$name=$this->getActionName();
		$MisAutoBindModel =M ( "mis_auto_bind" );
		$bindMap['status']=1;
		$bindMap['bindaname']=$name;
		// 获取到当前action的名称
		$misDynamicFormManageObj = M('mis_dynamic_form_manage');
		$title = $misDynamicFormManageObj->where("`actionname`='{$name}'")->field('actiontitle')->find();
		$this->assign('actiontitle' , $title['actiontitle']);
		$MisAutoBindList=$MisAutoBindModel->where($bindMap)->field("id,inbindaname")->select();
		$this->assign('MisAutoBindList',$MisAutoBindList);
		$this->getFormIndexLoad();
	}
	/**
	 * @Title: _extend_after_update
	 * @Description: todo(扩展后置update函数)  
	 * @author 管理员
	 * @date 2015-03-06 21:44:06
	 * @throws
	*/
	function _extend_after_update(){
	}
}
?>