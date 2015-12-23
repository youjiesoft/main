<?php
/**
 * @Title: MisAutoGktAction
 * @Package package_name
 * @Description: todo(动态表单_自动生成-流程节点管理)
 * @author 管理员
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-10-20 15:52:25
 * @version V1.0
*/
class MisAutoGktAction extends CommonAction {
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)
			$map['status']=array("gt",-1);
	}
	/**
	 * @Title: edit
	 * @Description: todo(重写父类编辑函数)
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws 
	*/
	function edit(){
		$mainTab = 'mis_auto_hcfwc';
		//获取当前控制器名称
		$name=$this->getActionName();
		$model = D("MisAutoGktView");
		//获取当前主键
		$map[$mainTab.'.id']=$_REQUEST['id'];
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		//读取动态配制
		$this->getSystemConfigDetail($name);
		//扩展工具栏操作
		$scdmodel = D('SystemConfigDetail');
		// 上一条数据ID
		$map['id'] = array("lt",$id);
		$updataid = $model->where($map)->order('id desc')->getField('id');
		$this->assign("updataid",$updataid);
		// 下一条数据ID
		$map['id'] = array("gt",$id);
		$downdataid = $model->where($map)->getField('id');
		$this->assign("downdataid",$downdataid);
		//lookup带参数查询
		$module=A($name);
		if (method_exists($module,"_after_edit")) {
			call_user_func(array(&$module,"_after_edit"),&$vo);
		}
		$this->assign( 'vo', $vo );
		$this->display ();
	}
	/**
	 * @Title: _before_edit
	 * @Description: todo(前置编辑函数)
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws 
	*/
	function _before_edit(){
	}
	/**
	 * @Title: _before_insert
	 * @Description: todo(前置添加函数)
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws 
	*/
	function _before_insert(){
	}
	/**
	 * @Title: _before_update
	 * @Description: todo(前置修改函数)  
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws
	*/
	function _before_update(){
	}
	/**
	 * @Title: _after_edit
	 * @Description: todo(后置编辑函数)
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws 
	*/
	function _after_edit($vo){
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistsummary=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistsummary[]=$temp;
		}
		$this->assign("selectlistsummary",$selectlistsummary);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistreadyonly=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistreadyonly[]=$temp;
		}
		$this->assign("selectlistreadyonly",$selectlistreadyonly);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistconstrainttype=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistconstrainttype[]=$temp;
		}
		$this->assign("selectlistconstrainttype",$selectlistconstrainttype);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistcritical=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistcritical[]=$temp;
		}
		$this->assign("selectlistcritical",$selectlistcritical);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistformtype=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistformtype[]=$temp;
		}
		$this->assign("selectlistformtype",$selectlistformtype);
	}
	/**
	 * @Title: _after_insert
	 * @Description: todo(后置insert函数)  
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws
	*/
	function _after_insert($id){
	}
	/**
	 * @Title: _before_add
	 * @Description: todo(前置add函数)  
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws
	*/
	function _before_add(){
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistsummary=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistsummary[]=$temp;
		}
		$this->assign("selectlistsummary",$selectlistsummary);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistreadyonly=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistreadyonly[]=$temp;
		}
		$this->assign("selectlistreadyonly",$selectlistreadyonly);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistconstrainttype=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistconstrainttype[]=$temp;
		}
		$this->assign("selectlistconstrainttype",$selectlistconstrainttype);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistcritical=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistcritical[]=$temp;
		}
		$this->assign("selectlistcritical",$selectlistcritical);
		$model=D('Selectlist');
		$selectlis = $model->GetRules('debit');
		$selectlistformtype=array();
		foreach($selectlis['debit'] as $k=>$v){
			$temp['key']=$k;
			$temp['val']=$v;
			$selectlistformtype[]=$temp;
		}
		$this->assign("selectlistformtype",$selectlistformtype);
	}
	/**
	 * @Title: _after_update
	 * @Description: todo(后置update函数)  
	 * @author 管理员
	 * @date 2014-10-20 15:52:25
	 * @throws
	*/
	function _after_update(){
	}
}
?>