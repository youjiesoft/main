<?php
//自定义表单模块
class MisTypeformFieldAction extends CommonAction {
	public function _before_add() {
		$model	=M("mis_typeform_field_type");
		$map['status']=1;
		$typelist	=$model->where($map)->select();
		$this->assign('typelist',$typelist);
		$this->assign('catalogid',$_GET['catalogid']);
		 $this->assign('linkid',$_GET['linkid']);
	  
	}
	
	public function _before_insert(){
		$_POST['ismust']= $_POST['ismust'] ? 1:0;
		$arr=array();
		foreach ( $_POST['checkreg'] as $k=>$v ){
			if($v) $arr[$k]=$v;
		}
		$_POST['checkreg']= $arr ? implode(";",$arr):'';
	}
	
	public function _before_update(){
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
}
?>