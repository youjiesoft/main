<?php
//Version 1.0
/**
 *	公司大事件
 */
class CompanyNewsAction extends CommonAction{
	public function _filter(&$map){
	    if(empty($_REQUEST['status'])) {
		 if ($_SESSION["a"] != 1)
		 	$map['status']=array("gt",-1);
		 	$map['type'] = array("eq","company");
		}
	}
	public function _before_add(){
		$model=D('User');
	    $list=$model->where('status = 1')->getField("id,name");
	    $this->assign("ulist", $list);
	}
	public function _before_edit(){
	    $model=D('User');
	    $list=$model->where('status = 1')->getField("id,name");
	    $this->assign("ulist", $list);
	}
	public function chakan(){
		$id=$_GET['id'];
		$model=D("CompanyNews");
		$list=$model->where("id=".$id)->find();
		$this->assign("vo",$list);
		$this->display("chakan");
	}
}
?>