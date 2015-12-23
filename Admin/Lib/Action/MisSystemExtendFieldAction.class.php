<?php
//自定义表单模块
class MisSystemExtendFieldAction extends CommonAction {
	private $firstDetail = array();
	public function index(){
		$model = D('SystemConfigDetail');
		if($_REQUEST['jump']){
			$modelName = $_REQUEST['model'];
			$name = $model->getTitleName($modelName);
		}else{		
			//$this -> getRoleTree();
			$name = $model->getTitleName($modelName);
			$models = D('SystemConfigNumber');
			$returnarr = $models->getRoleTree('missystemextendfieldBox');
			$this->assign('returnarr',$returnarr);
			$modelName = $models->firstDetail['name'];
			$name = $models->firstDetail['title'];
			$this -> assign('check', $models->firstDetail['check']);
		}
		$map['modelname'] = $modelName;
		$this->_search();
		$this->_list($this->getActionName(),$map);
		
		if($map['_complex']){
			// 做全检索
			$searchField = array_keys($map['_complex']);
			array_pop($searchField);
			$searchWord = $_POST['qkeysword'];
		}else{
			// 做指定字段的检索
			$searchField = array_keys($map);
			$keyname = 'quick'.end(explode('.',$searchField[0]));
			$searchWord = $_POST["$keyname"];
		}
		foreach($searchField as $k=>$v){
			$searchField[$k] = end(explode('.',$v));
		}
		$arr=array();
		if($searchField){
			foreach($newselectlist as $key=>$val){
				foreach($searchField as $k=>$v){
					if(strpos($val[$v] , $searchWord) !== false ){
						$arr[][$key]=$selectlist[$key];
					}
				}
			}
		}else{
			foreach($newselectlist as $key=>$val){
				$arr[][$key] = $val;
			}
		}
		//dump($arr);
		$this->assign("detailList",$detailList);
		$this->assign('model', $modelName);
		$this->assign('name', $name);
		if ($_GET['type'] == "ajaxcall") {
			$this->display ("ajax_index");exit;
		}
		if($_REQUEST['jump']){
			$this->display('indexview'); 
		}else{
			$this->display();
		}
	 
	}
	public function _before_add() {
		$model	=M("mis_typeform_field_type");
		$map['status']=1;
		$typelist	=$model->where($map)->select();
		$modelname=$_REQUEST['model']?$_REQUEST['model']:$this->error("请选择一个最底层节点");
		$this->assign('model',$modelname);
		$this->assign('typelist',$typelist);
		$this->assign('catalogid',$_GET['catalogid']);
		$this->assign('linkid',$_GET['linkid']);
	  
	}
	
	public function _before_insert(){
		$_POST['ismust']= $_POST['ismust'] ? 1:0;
		if(is_array($_POST['options'])){
			$_POST['options'] = implode(';',$_POST['options']);
		}
		$arr=array();
		foreach ( $_POST['checkreg'] as $k=>$v ){
			if($v) $arr[$k]=$v;
		}
		$_POST['checkreg']= $arr ? implode(";",$arr):'';
	}
	
	public function _before_update(){
		if(is_array($_POST['options'])){
			$_POST['options'] = implode(';',$_POST['options']);
		}
		$_POST['ismust']= $_POST['ismust'] ? 1:0;
		$arr=array();
		foreach ( $_POST['checkreg'] as $k=>$v ){
			if($v) $arr[$k]=$v;
		}
		$_POST['checkreg']= $arr ? implode(";",$arr):'';
	}
	
	public function _before_edit() {
		$model	=M("mis_typeform_field_type");
		$map['status']=1;
		$typelist	=$model->where($map)->select();
		$this->assign('typelist',$typelist);
	}
	public function _after_edit($vo){
		//找出需要改变样式的选项类型 用其id组成数组与$vo里的filetypeid比较 看选项用什么样式
		$fieldtype = array('单选框','复选框','下拉选择框');
		$fieldtypenew = array();
		foreach($fieldtype as $v){
			$fieldtypenew[] = getFieldBy($v, 'name', 'id', 'mis_typeform_field_type');
		}		
		$this->assign('fieldtypenew',$fieldtypenew);
		if(in_array($vo['fieldtypeid'],$fieldtypenew)){
			$vo['options']=explode(';',$vo['options']);
		}
	}

}
?>