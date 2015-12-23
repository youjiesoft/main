<?php
/** 
 * @Title: MisOaFlowsAction 
 * @Package package_name
 * @Description: todo(协同流程管理) 
 * @author 杨东 
 * @company 重庆特米洛科技有限公司
 * @copyright 本文件归属于重庆特米洛科技有限公司
 * @date 2014-4-2 上午11:03:55 
 * @version V1.0 
*/ 
class MisOaFlowsAction extends CommonFlowsAction {
	
	public function _filter(&$map){
		if ($_SESSION["a"] != 1)$map['status']=array("gt",-1);
	}
	public function add(){
		$this->getSystemConfigDetail();
		$data = array();
		$data[] = array("id"=>0,"name"=>"开始","key"=>0,"level"=>1);
		unset($_SESSION["flowsdata"]);
		$_SESSION["flowsdata"] = $data;
		$jsondata = $this->setDataJson($data,0);
		$this->assign("data",$jsondata);
		$this->display();
	}
	function edit() {
		$name=$this->getActionName();
		$model = D ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$map['id']=$id;
		if ($_SESSION["a"] != 1) $map['status'] = 1;
		$vo = $model->where($map)->find();
		if(empty($vo)){
			$this->display ("Public:404");
			exit;
		}
		$this->getSystemConfigDetail($name);
		$data = unserialize($vo['flowtrack']);
// 		print_r($data);
// 		foreach ($data as $k => $v) {
// 			if($v["name"] == "管理员") $data[$k]['to'] = array(3);
// 		}
		unset($_SESSION["flowsdata"]);
		$_SESSION["flowsdata"] = $data;
		$jsondata = $this->setDataJson($data,$vo['id']);
		$this->assign("data",$jsondata);
		$this->assign( 'vo', $vo );
		$this->display ();
	}
	
	public function _before_insert(){
		if(count($_SESSION["flowsdata"]) <=1 ){
			$this->error ("请先添加流程节点再进行流程保存！");
			exit;
		}
		$_POST["flowtrack"] = serialize($_SESSION["flowsdata"]);
		unset($_SESSION["flowsdata"]);
	}
	public function _before_update(){
		if(count($_SESSION["flowsdata"]) <=1 ){
			$this->error ("请先添加流程节点再进行流程保存！");
			exit;
		}
		$_POST["flowtrack"] = serialize($_SESSION["flowsdata"]);
		unset($_SESSION["flowsdata"]);
	}
}
?>